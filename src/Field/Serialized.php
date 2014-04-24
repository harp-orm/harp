<?php

namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Serialized extends AbstractField
{
    const NATIVE = 1;
    const JSON = 2;
    const CSV = 3;

    public $mode = self::NATIVE;

    public function __construct($name, $mode = self::NATIVE)
    {
        parent::__construct($name);

        if (! in_array($mode, [self::NATIVE, self::JSON, self::CSV])) {
            throw new InvalidArgumentException('Inalid serialization mode');
        }

        $this->mode = $mode;
    }

    public function save($value)
    {
        return self::serialize($value, $this->mode);
    }

    public function load($value)
    {
        return self::unserialize($value, $this->mode);
    }

    public static function serialize($value, $mode)
    {
        switch ($mode) {

            case self::NATIVE:
                return serialize($value);

            case self::CSV:
                return join(',', $value);

            case self::JSON:
                return json_encode($value);
        }
    }

    public static function unserialize($value, $mode)
    {
        switch ($mode) {

            case self::NATIVE:
                return unserialize($value);

            case self::CSV:
                return explode(',', $value);

            case self::JSON:
                return json_decode($value, true);
        }
}   }
