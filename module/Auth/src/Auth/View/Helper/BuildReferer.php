<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth view helper */
namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Auth\Filter\StripQueryParams as StripQueryParamsFilter;

/**
 * View helper to access authentication service and the
 * authenticated user (and its properties).
 *
 */
class BuildReferer extends AbstractHelper
{
    
    protected $filter;
    
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }
    
    public function getFilter()
    {
        if (!$this->filter) {
            $this->setFilter(new StripQueryParamsFilter());
        }
        return $this->filter;
    }
    
    /**
     * Entry point.
     *
     */
    public function __invoke($uri = null, array $stripParams = null)
    {
        if (is_array($uri)) {
            $stripParams = $uri;
            $uri = null;
        }
        
        if (null === $uri && isset($_SERVER['REQUEST_URI'])) {
            $uri = preg_replace('~^' . $this->getView()->basePath() . '~', '', $_SERVER['REQUEST_URI']);
        }
        
        if (null === $stripParams) {
            return $this->getFilter()->filter($uri);
        }
        return $this->getFilter()->filter($uri, $stripParams);
    }
}
