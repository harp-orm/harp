<?php

namespace Harp\Harp;

use Harp\Validate\Asserts;
use Harp\Serializer\Serializers;
use Harp\Harp\AbstractModel;
use Harp\Harp\Rel\AbstractRel;
use Harp\Harp\Repo\ReflectionModel;
use Harp\Harp\Repo\EventListeners;
use Harp\Harp\Repo\Container;
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
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $primaryKey = 'id';

    /**
     * @var string
     */
    private $nameKey = 'name';

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $db = 'default';

    /**
     * @var ReflectionModel
     */
    private $reflectionModel;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var boolean
     */
    private $initialized = false;

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

    /**
     * @var EventListeners
     */
    private $eventListeners;

    /**
     * @var AbstractRel[]
     */
    private $rels = [];

    /**
     * @var Asserts
     */
    private $asserts = [];

    /**
     * @var Serializers
     */
    private $serializers = [];

    public function __construct($class)
    {
        $this->reflectionModel = new ReflectionModel($class);
        $this->eventListeners = new EventListeners();
        $this->serializers = new Serializers();
        $this->asserts = new Asserts();
        $this->name = $this->table = $this->reflectionModel->getShortName();
        $this->fields = $this->reflectionModel->getPublicPropertyNames();
        $this->rootConfig = $this;
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
        return $this->reflectionModel->getName();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        $this->initializeOnce();

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
     * @return string
     */
    public function getDb()
    {
        $this->initializeOnce();

        return $this->db;
    }

    /**
     * @param string $db
     * @return Config
     */
    public function setDb($db)
    {
        $this->db = (string) $db;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $this->initializeOnce();

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
    public function getReflectionModel()
    {
        return $this->reflectionModel;
    }

    /**
     * @return Config
     */
    public function getRootConfig()
    {
        $this->initializeOnce();

        return $this->rootConfig;
    }

    /**
     * @return Repo
     */
    public function getRepo()
    {
        return Container::get($this->getModelClass());
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
    public function getSoftDelete()
    {
        $this->initializeOnce();

        return $this->softDelete;
    }

    /**
     * Enables "soft delete" on models of this repo.
     * You will need to add the SoftDeleteTrait to the model class too.
     *
     * @param  boolean      $softDelete
     * @return Config $this
     */
    public function setSoftDelete($softDelete)
    {
        $this->softDelete = (bool) $softDelete;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getInherited()
    {
        $this->initializeOnce();

        return $this->inherited;
    }

    /**
     * Enables Repo "inheritance" allowing multiple repos to share one storage table
     * You will need to call setRootRepo on all the child repos.
     *
     * @param  boolean      $inherited
     * @return Config $this
     */
    public function setInherited($inherited)
    {
        $this->inherited = (bool) $inherited;

        if ($inherited) {
            if (! $this->reflectionModel->isRoot()) {
                $rootRepo = Container::get($this->reflectionModel->getRoot()->getName());
                $this->rootConfig = $rootRepo->getConfig();
            }

            $this->table = $this->rootConfig->getTable();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        $this->initializeOnce();

        return $this->primaryKey;
    }

    /**
     * @param string
     * @return Config $this
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameKey()
    {
        $this->initializeOnce();

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

    /**
     * @param  AbstractRel  $rel
     * @return Config $this
     */
    public function addRel(AbstractRel $rel)
    {
        $this->initializeOnce();

        $this->rels[$rel->getName()] = $rel;

        return $this;
    }

    /**
     * @param AbstractRel[] $rels
     */
    public function addRels(array $rels)
    {
        foreach ($rels as $rel) {
            $this->addRel($rel);
        }

        return $this;
    }

    /**
     * @return AbstractRel[]
     */
    public function getRels()
    {
        $this->initializeOnce();

        return $this->rels;
    }

    /**
     * @param  string           $name
     * @return AbstractRel|null
     */
    public function getRel($name)
    {
        $this->initializeOnce();

        return isset($this->rels[$name]) ? $this->rels[$name] : null;
    }

    /**
     * @param  string                   $name
     * @return AbstractRel
     * @throws InvalidArgumentException If rel does not exist
     */
    public function getRelOrError($name)
    {
        $rel = $this->getRel($name);

        if ($rel === null) {
            throw new InvalidArgumentException(
                sprintf('Rel %s does not exist in %s Repo', $name, $this->getName())
            );
        }

        return $rel;
    }

    /**
     * @return EventListeners
     */
    public function getEventListeners()
    {
        $this->initializeOnce();

        return $this->eventListeners;
    }

    /**
     * @return Asserts
     */
    public function getAsserts()
    {
        $this->initializeOnce();

        return $this->asserts;
    }

    /**
     * @param  \Harp\Validate\Assert\AbstractAssertion[] $asserts
     * @return Config                           $this
     */
    public function addAsserts(array $asserts)
    {
        $this->initializeOnce();

        $this->getAsserts()->set($asserts);

        return $this;
    }

    /**
     * @return Serializers
     */
    public function getSerializers()
    {
        $this->initializeOnce();

        return $this->serializers;
    }

    /**
     * @param  \Harp\Serializer\AbstractSerializer[] $serializers
     * @return Config                          $this
     */
    public function addSerializers(array $serializers)
    {
        $this->initializeOnce();

        $this->getSerializers()->set($serializers);

        return $this;
    }

    /**
     * @param  integer              $event
     * @param  Closure|string|array $callback
     * @return Config         $this
     */
    public function addEventBefore($event, $callback)
    {
        $this->getEventListeners()->addBefore($event, $callback);

        return $this;
    }

    /**
     * @param  integer              $event
     * @param  Closure|string|array $callback
     * @return Config         $this
     */
    public function addEventAfter($event, $callback)
    {
        $this->getEventListeners()->addAfter($event, $callback);

        return $this;
    }

    /**
     * Check if a model belongs to this repo. Child classes are also accepted
     *
     * @param  AbstractModel            $model
     * @throws InvalidArgumentException If model not part of repo
     */
    public function assertModel(AbstractModel $model)
    {
        if (! $this->isModel($model)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Model must be instance of %s, but was %s',
                    $this->getRootConfig()->getModelClass(),
                    get_class($model)
                )
            );
        }
    }

    /**
     * @param  AbstractModel $model
     * @return boolean
     */
    public function isModel(AbstractModel $model)
    {
        return $this->getRootConfig()->getReflectionModel()->isInstance($model);
    }

    /**
     * @return boolean
     */
    public function getInitialized()
    {
        return $this->initialized;
    }

    /**
     * Call "initialize" method only once,
     * this is determined by the "initialized" property.
     */
    public function initializeOnce()
    {
        if (! $this->initialized) {
            $this->initialized = true;

            $this->reflectionModel->initialize($this);
        }
    }
}
