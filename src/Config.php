<?php

namespace Harp\Harp;

use Harp\Validate\AssertsTrait;
use Harp\Serializer\SerializersTrait;
use Harp\EventListeners\EventListenersTrait;
// use Harp\Harp\Rel\RelConfigTrait;
use Harp\Harp\Model;
// use Harp\Harp\Rel\AbstractRel;
// use Harp\Harp\Repo\ReflectionModel;
use ReflectionClass;
// use Harp\Harp\Repo\Container;
use InvalidArgumentException;

/**
 * A Repo represents a storage and configuration medium for models. Each model has a corresponding "repo".
 * Repos are also singleton classes. You can get the repo object with the "get" static method
 *
 * This class is the core implementation of a repo and contins all the logic for the "configuration" part.
 *
 * The abstract method "initialize" which is implemented in your own repos is called only once. It is
 * distinct from the __construct, becase it can create a lot of overhead. Since relations require "repo"
 * requesting a single "repo" could trigger the constructors of all the other repos, associated with it,
 * and their related repo's too. Thats why we need "initialize" method, which will lazy load all the relations.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Config
{
    const PRIMARY_KEY = 'id';
    const INHERITED_KEY = 'class';
    const SOFT_DELETE_KEY = 'deletedAt';

    // use RelConfigTrait;
    use AssertsTrait;
    use SerializersTrait;
    use EventListenersTrait;
    use RelsTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $nameKey = 'name';

    /**
     * @var string
     */
    private $table;

    /**
     * @var ReflectionModel
     */
    private $reflection;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var boolean
     */
    private $softDelete = false;

    /**
     * @var boolean
     */
    private $inherited = false;

    /**
     * @var Config
     */
    private $rootConfig;

    public function __construct($class)
    {
        $this->reflection = new ReflectionModel($class);
        $this->name = $this->table = $this->reflection->getShortName();
        $this->fields = $this->reflection->getPublicPropertyNames();

        if ($this->reflection->hasMethod('initialize')) {
            $this->reflection->getMethod('initialize')->invoke(null, $this);
        }

        $this->inherited = $this->reflection->hasInheritedTrait();
        $this->softDelete = $this->reflection->hasSoftDeleteTrait();
        $this->sessionInstanceId = $session->getInstanceId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->reflection->getName();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = (string) $table;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $items
     * @return Config
     */
    public function setFields(array $items)
    {
        $this->fields = $items;

        return $this;
    }

    /**
     * @return ReflectionModel
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * @return Config
     */
    public function getRootConfig()
    {
        return $this->rootConfig;
    }

    /**
     * @return Config
     */
    public function isInherited()
    {
        return $this->inherited;
    }

    /**
     * @return boolean
     */
    public function isRoot()
    {
        return $this->getRootConfig() === $this;
    }

    /**
     * @return boolean
     */
    public function isSoftDelete()
    {
        return $this->softDelete;
    }

    // /**
    //  * @return boolean
    //  */
    // public function getInherited()
    // {
    //     return $this->inherited;
    // }

    // /**
    //  * Enables Repo "inheritance" allowing multiple repos to share one storage table
    //  * You will need to call setRootRepo on all the child repos.
    //  *
    //  * @param  boolean      $inherited
    //  * @return Config $this
    //  */
    // public function setInherited($inherited)
    // {
    //     $this->inherited = (bool) $inherited;

    //     if ($inherited) {
    //         if (! $this->reflection->isRoot()) {
    //             $rootRepo = Container::get($this->reflection->getRoot()->getName());
    //             $this->rootConfig = $rootRepo->getConfig();
    //         }

    //         $this->table = $this->rootConfig->getTable();
    //     }

    //     return $this;
    // }

    /**
     * @return string
     */
    public function getNameKey()
    {
        return $this->nameKey;
    }

    /**
     * @param string
     * @return Config $this
     */
    public function setNameKey($nameKey)
    {
        $this->nameKey = $nameKey;

        return $this;
    }
}
