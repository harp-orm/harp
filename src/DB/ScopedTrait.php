<?php namespace CL\Luna\DB;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait ScopedTrait {

	public function scope($scope)
	{
		call_user_func(array($this->getFetchCLass(), 'scope'.ucfirst($scope)), $this);

		return $this;
	}
}
