<?php

namespace CL\Luna;

use CL\LunaCore\Model\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractDbModel extends AbstractModel {

    public function __construct(array $fields = null, $state = null)
    {
        $state = $state ?: $this->getDefaultState();

        if ($state === self::PERSISTED) {
            $fields = $fields ?: $this->getFieldValues();
            $fields = $this->getRepo()->getFields()->callLoadData($fields);
        }

        parent::__construct($fields, $state);

        if ($this->getRepo()->getPolymorphic()) {
            $this->polymorphicClass = get_called_class();
        }
    }

    public function getDefaultState()
    {
        return $this->getId() ? self::PERSISTED : self::PENDING;
    }

    public function getFieldValues()
    {
        $fields = [];

        foreach ($this->getRepo()->getFieldNames() as $name) {
            $fields[$name] = $this->{$name};
        }

        return $fields;
    }
}
