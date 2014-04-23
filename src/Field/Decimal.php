<?php namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Decimal extends AbstractField
{
    public $precision = 2;
    public $mode = PHP_ROUND_HALF_UP;

    public function __construct($name, $precision = 2, $mode = PHP_ROUND_HALF_UP)
    {
        parent::__construct($name);

        if (! in_array($mode, [PHP_ROUND_HALF_UP, PHP_ROUND_HALF_DOWN, PHP_ROUND_HALF_EVEN, PHP_ROUND_HALF_ODD])) {
            throw new InvalidArgumentException('Inalid rounding mode');
        }

        $this->precision = (int) $precision;
        $this->mode = $mode;
    }

    public function load($value)
    {
        return is_numeric($value) ? (int) $value : null;
    }

    public function store($value)
    {
        return round($value, $this->precision, $this->mode);
    }
}
