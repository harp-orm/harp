<?php namespace CL\Luna\Model;

use CL\Luna\DB\DB;
use CL\Luna\Model\Config;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model {

	use DirtyTrackingTrait;

	public static function get($id)
	{
		$result = static::all()
			->where(['id' => $id])
			->limit(1)
			->execute();

		return $result->fetch();
	}

	public static function all()
	{
		return self::getDbInstance()
			->select()
			->setFetchClass(get_called_class())
			->from(static::getTable());
	}

	public static function getDbInstance()
	{
		static::initializeSchema();

		return DB::instance(static::getDb());
	}

	public function __construct(array $attributes = NULL, $loaded = FALSE)
	{
		static::initializeSchema();

		if ($loaded)
		{
			$fieldNames = array_keys(static::getFields());

			foreach ($fieldNames as $name)
			{
				$attributes[$name] = $this->$name;
			}

			$this->setOriginals($attributes);
		}

		// $attributes = self::loadFieldData($attributes);

		foreach ($attributes as $name => $value)
		{
			$this->$name = $value;
		}
	}

	// public function validate()
	// {
	// 	self::executeValidators($this);
	// }
}
