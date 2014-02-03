<?php namespace CL\Luna\Model;

use CL\Luna\DB\DB;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Model {

	use DirtyTrackingTrait;

	protected $errors;
	protected $rels;
	protected $isLoaded = FALSE;

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

	public static function delete()
	{
		return self::getDbInstance()
			->delete()
			->from(static::getTable());
	}

	public static function update()
	{
		return self::getDbInstance()
			->update()
			->table(static::getTable());
	}

	public static function insert()
	{
		return self::getDbInstance()
			->insert()
			->into(static::getTable());
	}

	public static function getDbInstance()
	{
		static::initializeSchema();

		return DB::instance(static::getDb());
	}

	public function __construct(array $attributes = NULL, $loaded = FALSE)
	{
		if ($loaded === TRUE)
		{
			$attributes = Arr::invokeObjects($this->getAttributes(), static::getFields(), 'load');

			$this->setAttributes($attributes);
			$this->setOriginals($attributes);

			$this->isLoaded = TRUE;
		}
		else
		{
			$this->setOriginals($this->getAttributes());
			$this->setAttributes($attributes);
		}
	}

	public function getId()
	{
		return $this->{static::getPrimaryKey()};
	}

	public function isLoaded()
	{
		return $this->isLoaded;
	}

	public function setAttributes(array $values)
	{
		foreach ($values as $name => $value)
		{
			$this->$name = $value;
		}
	}

	public function save()
	{
		$this->validate();

		if ($this->isChanged())
		{
			$changes = Arr::invokeObjects($this->getChanges(), static::getFields(), 'save');
			$this->insertOrUpdate($changes);
		}
	}

	public function getAttributes()
	{
		$values = array();
		$fieldNames = array_keys(static::getFields());

		foreach ($fieldNames as $name)
		{
			$values[$name] = $this->$name;
		}

		return $values;
	}

	public function insertOrUpdate(array $attributes)
	{
		$query = $this->isLoaded()
			? static::update()->whereKey($this->getId())
			: static::insert();

		$query
			->set($attributes)
			->execute();
	}

	public function getRel($name)
	{
		if ( ! isset($this->rels[$name]))
		{
			$this->rels[$name] = static::getRels()[$name]->load($this);
		}

		return $this->rels[$name];
	}

	public function getErrors()
	{
		if ( ! $this->errors)
		{
			$this->errors = new Errors();
		}

		return $this->errors;
	}

	public function validate()
	{
		$this->getErrors()->validateChanges($this->getChanges(), static::getValidators());

		return $this->isValid();
	}

	public function isValid()
	{
		return $this->getErrors()->isEmpty();
	}
}
