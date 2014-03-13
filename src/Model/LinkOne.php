<?php namespace CL\Luna\Model;

use CL\Luna\Util\ObjectStorage;
use CL\Luna\Schema\Schema;
use CL\Luna\Repo\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkOne implements LinkInterface
{
	protected $model;
	protected $original;

	public function __construct(Model $model)
	{
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
		$all = new ObjectStorage();
		$all->attach($this->model);
		$all->attach($this->original);

		return $all;
	}
}
