<?php namespace CL\Luna\Repo;

use CL\Luna\Util\Storage;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Model\Model;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkOne extends AbstractLink
{
    protected $model;
    protected $original;

    public function __construct(AbstractRel $rel, Model $model)
    {
        parent::__construct($rel);

        $this->model = $model;
        $this->original = $model;
    }

    public function set(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function get()
    {
        return $this->model;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function getAll()
    {
        $all = new SplObjectStorage();
        $all->attach($this->model);
        $all->attach($this->original);

        return $all;
    }
}
