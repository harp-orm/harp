<?php namespace CL\Luna\Config;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class DocBlock
{
	protected $text;
	protected $lines;

	public function getText()
	{
		return $this->text;
	}

	function __construct($text)
	{
		$this->text = $text;
	}

	public function getLines()
	{
		if ($this->lines === NULL)
		{
			$text = trim($this->getText(), "/*\n\t /");

			$lines = explode("\n", $text);

			$lines = array_map(function($line){
				return trim($line, "*\t ");
			}, $lines);

			$this->lines = array_filter($lines);
		}

		return $this->lines;
	}

	public function getLinesFiltered($filter)
	{
		return array_filter($this->getLines(), function($line) use ($filter) {
			return self::isLineNamed($line, $filter);
		});
	}

	public function getTagValue($name)
	{
		$lines = $this->getTagValues($name);
		return end($lines);
	}

	public function getTagValues($name)
	{
		$lines = $this->getLinesFiltered($name);

		return array_map(function($line) use ($name) {
			return substr($line, strlen($name) + 2);
		}, $lines);
	}

	public function getObjects($name, $default_namespace = NULL)
	{
		return array_map(function($line) use ($default_namespace) {
			return self::convertLineToObject($line, $default_namespace);
		}, $this->getLinesFiltered($name));
	}

	public function getObject($name, $default_namespace = NULL)
	{
		foreach ($this->getLines() as $line)
		{
			if (self::isLineNamed($line, $name))
			{
				return self::convertLineToObject($line, $default_namespace);
			}
		}
	}

	public static function isLineNamed($line, $name)
	{
		return strpos($line, '@'.$name) === 0;
	}

	public static function convertLineToObject($line, $default_namespace = NULL)
	{
		list($tag, $class, $params) = array_pad(explode(' ', $line, 3), 3, array());

		if (strpos($class, '\\') === FALSE AND $default_namespace)
		{
			$class = $default_namespace.$class;
		}

		if ($params)
		{
			$params = json_decode($params);
		}

		return new $class($params);
	}
}
