<?php namespace CL\Luna\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait DirtyTrackingTrait
{
	private $originals = [];

	public function setOriginals(array $attributes)
	{
		$this->originals = $attributes;
	}

	public function getOriginals()
	{
		return $this->originals;
	}

	public function getOriginal($name)
	{
		return isset($this->originals[$name]) ? $this->originals[$name] : NULL;
	}

	public function getChange($name)
	{
		if ($this->isAttributeChanged($name))
		{
			return [$this->getOriginal($name), $this->$name];
		}
	}

	public function getChanges()
	{
		$changes = [];

		foreach ($this->originals as $name => $original)
		{
			if ($this->isAttributeChanged($name))
			{
				$changes[$name] = $this->$name;
			}
		}

		return $changes;
	}

	public function isAttributeChanged($name)
	{
		return ($this->$name != $this->getOriginal($name));
	}

	public function isChanged()
	{
		foreach ($this->originals as $name => $orignial)
		{
			if ($this->isAttributeChanged($name))
			{
				return TRUE;
			}
		}
		return FALSE;
	}
}
