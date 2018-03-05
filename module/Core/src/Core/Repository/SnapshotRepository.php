<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Repository;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\SnapshotAttributesProviderInterface;
use Core\Entity\SnapshotInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Zend\Hydrator\HydratorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class SnapshotRepository extends DocumentRepository
{
    /**
     *
     *
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     *
     *
     * @var HydratorInterface
     */
    protected $sourceHydrator;

    /**
     *
     *
     * @var array
     */
    protected $snapshotAttributes = [];

    /**
     * @param \Zend\Hydrator\HydratorInterface $hydrator
     *
     * @return self
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new EntityHydrator());
        }
        return $this->hydrator;
    }

    /**
     * @param \Zend\Hydrator\HydratorInterface $sourceHydrator
     *
     * @return self
     */
    public function setSourceHydrator($sourceHydrator)
    {
        $this->sourceHydrator = $sourceHydrator;

        return $this;
    }

    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getSourceHydrator()
    {
        if (!$this->sourceHydrator) {
            return $this->getHydrator();
        }

        return $this->sourceHydrator;
    }

    /**
     * @param array $snapshotAttributes
     *
     * @return self
     */
    public function setSnapshotAttributes($snapshotAttributes)
    {
        $this->snapshotAttributes = $snapshotAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getSnapshotAttributes()
    {
        return $this->snapshotAttributes;
    }

    public function create(EntityInterface $source, $persist = true)
    {

        $snapshot = $this->getDocumentName();
        $snapshot = new $snapshot($source);

        $this->copy($source, $snapshot);

        if ($persist) {
            $this->store($snapshot);
        }

        return $snapshot;
    }

    protected function copy($source, $target, $inverse = false)
    {
        if ($inverse) {
            $attributes = $this->getCopyAttributes($target, $source);
            $sourceHydrator = $this->getHydrator();
            $targetHydrator = $this->getSourceHydrator();
        } else {
            $attributes = $this->getCopyAttributes($source, $target);
            $sourceHydrator = $this->getSourceHydrator();
            $targetHydrator = $this->getHydrator();
            $source = clone $source;
        }


        $data = $sourceHydrator->extract($source);
        $data = array_intersect_key($data, array_flip($attributes));
        $targetHydrator->hydrate($data, $target);
    }

    protected function getCopyAttributes($source, $target)
    {
        if ($source instanceOf SnapshotAttributesProviderInterface) {
            return $source->getSnapshotAttributes();
        }

        if ($target instanceOf SnapshotAttributesProviderInterface) {
            return $target->getSnapshotAttributes();
        }

        return $this->getSnapshotAttributes();
    }

    public function merge(SnapshotInterface $snapshot, $snapshotDraftStatus = false)
    {
        $this->checkEntityType($snapshot);

        $meta       = $snapshot->getSnapshotMeta();
        $entity     = $snapshot->getOriginalEntity();

        $meta->setIsDraft((bool) $snapshotDraftStatus);

        $this->copy($snapshot, $entity, true);

        return $entity;
    }

    public function diff(SnapshotInterface $snapshot)
    {
        $entity = $snapshot->getEntity();
        $attributes = $this->getCopyAttributes($entity, $snapshot);


    }

    public function findLatest($sourceId, $isDraft = false)
    {
        return $this->createQueryBuilder()
          ->field('snapshotEntity')->equals(new \MongoId($sourceId))
          ->field('snapshotMeta.isDraft')->equals($isDraft)
          ->sort('snapshotMeta.dateCreated.date', 'desc')
          ->limit(1)
          ->getQuery()
          ->getSingleResult();

    }

    public function findBySourceId($sourceId, $includeDrafts = false)
    {
        $criteria = ['snapshotEntity' => $sourceId];

        if (!$includeDrafts) {
            $criteria['snapshotMeta.isDraft'] = false;
        }

        return $this->findBy($criteria);
    }

    /**
     * @param $entity
     * @throws \InvalidArgumentException
     * @return self
     */
    public function store($entity)
    {
        $this->checkEntityType($entity);
        $this->dm->persist($entity);
        $this->dm->flush($entity);

        return $this;
    }

    public function remove($entity)
    {
        $this->checkEntityType($entity);
        $this->dm->remove($entity);

        return $this;
    }

    public function removeAll($sourceId)
    {

    }

    protected function checkEntityType($entity)
    {
        if ( !is_a($entity,  $this->getDocumentName()) ) {
            throw new \InvalidArgumentException(sprintf(
                'Entity must be of type %s but recieved %s instead',
                $this->getDocumentName(),
                get_class($entity)
            ));
        }

    }

    protected function extract($source, array $attributes = [])
    {
        $hydrator = $this->getSourceHydrator();
        $data     = $hydrator->extract($source);
        $hydrate  = [];

        if (empty($attributes)) {
            $attributes = array_keys($data);
        }

        foreach ($attributes as $key => $spec) {
            if (is_numeric($key)) {
                $key = $spec;
                $spec = null;
            }

            if ($data[$key] instanceOf EntityInterface) {
                $hydrate[$key] = clone $data[$key];

            } else if ($data[$key] instanceOf Collection) {
                $collection = new ArrayCollection();
                foreach ($data[$key] as $item) {
                    $collection->add(clone $item);
                }
                $hydrate[$key] = $collection;

            } else {
                $hydrate[$key] = $data[$key];
            }
        }

        return $hydrate;
    }
}
