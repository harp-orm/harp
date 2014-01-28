<?php namespace CL\Luna\Config;

use ReflectionProperty;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ReflectionClass extends \ReflectionClass
{
	protected $docBlock;
	protected $propertiesDocBlocks = array();
	protected $methodsDocBlocks = array();

	function __construct($class)
	{
		parent::__construct($class);

		$this->docBlock = new DocBlock($this->getDocComment());

		foreach ($this->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			$this->propertiesDocBlocks[$property->getName()] = new DocBlock($property->getDocComment());
		}

		foreach ($this->getMethods(ReflectionProperty::IS_PUBLIC) as $property)
		{
			$this->methodsDocBlocks[$property->getName()] = new DocBlock($property->getDocComment());
		}
	}

	public function fields()
	{
		return array_filter(array_map(function($property){
			return $property->getObject('field', 'CL\\Luna\\Field\\');
		}, $this->propertiesDocBlocks));
	}

	public function validators()
	{
		return array_filter(array_map(function($property){
			return $property->getObjects('validator', 'CL\\Luna\\Validator\\');
		}, $this->propertiesDocBlocks));
	}

	public function rels()
	{
		return array_filter(array_map(function($method){
			return $method->getObjects('rel', 'CL\\Luna\\Rel\\');
		}, $this->methodsDocBlocks));
	}

	public function events()
	{
		$events = array();

		foreach ($this->methodsDocBlocks as $name => $method)
		{
			foreach ($method->getTagValues('event') as $event)
			{
				$events[$event] []= $name;
			}
		}
		return $events;
	}

	public function configOptions()
	{
		$options = [
			'db' => $this->docBlock->getTagValue('db') ?: 'default',
			'table' => $this->docBlock->getTagValue('table') ?: strtolower($this->getShortName()),
			'fields' => $this->fields(),
			'validators' => $this->validators(),
			'rels' => $this->rels(),
			'events' => $this->events(),
		];

		return $options;
	}
}
