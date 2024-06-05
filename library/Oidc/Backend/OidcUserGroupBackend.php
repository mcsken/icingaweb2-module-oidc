<?php
/* Icinga Web 2 | (c) 2013 Icinga Development Team | GPLv2+ */

namespace Icinga\Module\Oidc\Backend;

use Icinga\Application\Config;
use Icinga\Authentication\UserGroup\UserGroupBackendInterface;
use Icinga\Data\Db\DbConnection;
use Icinga\Data\ResourceFactory;
use Icinga\Data\Selectable;
use Icinga\User;

class OidcUserGroupBackend implements UserGroupBackendInterface, Selectable
{

    protected $config = null;
    protected $name = null;
    public function __construct($config)
    {
        $this->config=$config;

    }
    public static function getConfigurationFormClass(){
        return \Icinga\Module\Oidc\Backend\Form\OidcUserGroupBackendForm::class;
    }
    /**
     * Set this repository's name
     *
     * @param   string  $name
     *
     * @return  $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return this repository's name
     *
     * In case no name has been explicitly set yet, the class name is returned.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name ?: __CLASS__;
    }

    public function getDbConnection(){
        $resource = Config::module('oidc')->getSection('backend')->get('resource','oidc');
        $resourceConfig = ResourceFactory::getResourceConfig($resource);
        $db = new DbConnection($resourceConfig);
        return $db;
    }
    public function select()
    {
        $provider_id = Config::app('groups')->getSection($this->getName())->get('provider_id');
        return (new OidcDbUserGroupRepository($this->getDbConnection()))->select()->where('provider_id',$provider_id);
    }

    public function getMemberships(User $user)
    {
        $provider_id = Config::app('groups')->getSection($this->getName())->get('provider_id');
        return (new OidcDbUserGroupRepository($this->getDbConnection()))->getMemberships($user,$provider_id);
    }

    public function getUserBackendName($username)
    {
        $provider_id = Config::app('groups')->getSection($this->getName())->get('provider_id');
        $userBackends = Config::app('authentication')->select()->fetchAll();
        foreach ($userBackends as $name=>$userBackend){
            if($userBackend->provider_id === $provider_id){
                return $name;
            }
        }
        return null;

    }
}
