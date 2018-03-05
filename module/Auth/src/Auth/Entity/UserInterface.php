<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth model */
namespace Auth\Entity;

use Core\Entity\IdentifiableEntityInterface;
use Organizations\Entity\OrganizationReferenceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Defines an user model interface
 *
 * Interface UserInterface
 * @package Auth\Entity
 */
interface UserInterface extends IdentifiableEntityInterface, RoleInterface
{

    /**
     * defines the role of a recruiter
     */
    const ROLE_RECRUITER = 'recruiter';
    /*
     * defines the role of an authenticated user
     */
    const ROLE_USER = 'user';
    /*
     * defines the role of an admin user.
     */
    const ROLE_ADMIN = 'admin';

    /**
     * Sets the users login name
     *
     * @param string $login
     */
    public function setLogin($login);
    
    /**
     * Gets the users login name
     *
     * @return string
     */
    public function getLogin();
    
    /**
     * Sets the role of the users. Currently "user" or "recruiter"
     *
     * @param String $role
     */
    public function setRole($role);

    /**
     * Gets the role of the user
     *
     * @return String
     */
    public function getRole();
    
    /**
     * Set contact data, user image etc. of a user.
     *
     * @param InfoInterface $info
     */
    public function setInfo(InfoInterface $info);
    
    /**
     * Get contact data, user image etc. of a user.
     *
     * @return InfoInterface
     */
    public function getInfo();
    
    /**
     * Set the API password of the user.
     *
     * @param String $password
     */
    public function setPassword($password);
    
    /**
     * get the Web frontend password of the user
     *
     * @param String $credential
     */
    public function setCredential($credential);
    
    /**
     * get the web frontend password of the user
     */
    public function getCredential();
    
    /**
     * Sets the profile info from HybridAuth
     *
     * @param array $profile
     */
    public function setProfile(array $profile);
    
    /**
     * Gets the profile info from HybridAuth
     *
     * @return array
     */
    public function getProfile();
    
    /**
     * get user settings of a certain Module.
     *
     * @param String $module
     */
    public function getSettings($module);

    /**
     * get groups of the user
     *
     * @return \Core\Entity\Collection\ArrayCollection
     */
    public function getGroups();

    /**
     * get tokens of the user
     *
     * @return \Core\Entity\Collection\ArrayCollection
     */
    public function getTokens();

    /**
     * Sets the organization reference of the user.
     *
     * @param OrganizationReferenceInterface $organization
     *
     * @return self
     * @since 0.18
     */
    public function setOrganization(OrganizationReferenceInterface $organization);

    /**
     * returns true, if the user is associated to an organization.
     *
     * @return boolean
     * @since 0.18
     */
    public function hasOrganization();

    /**
     * Gets the organization reference of the the user.
     *
     * @return null|OrganizationReferenceInterface
     * @since 0.18
     */
    public function getOrganization();

    /**
     * Returns true, if a user is created as a draft.
     *
     * @return bool
     * @since 0.19
     */
    public function isDraft();

    /**
     * marks a users as draft.
     *
     * @param bool
     * @return self
     * @since 0.19
     */
    public function setIsDraft($draft);
    
    /**
     * Return true, if user is active
     *
     * @return boolean
     * @since 0.25
     */
    public function isActive();
}
