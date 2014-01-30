<?php namespace CL\Luna\Model;

use CL\Luna\DB\DB;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Error{

	protected $identifier;

	protected $attribute;

	protected $parameters;

	public function __construct($identifier, $attribute, array $parameters = NULL)
	{
		$this->identifier = $identifier;
		$this->attribute = $attribute;
		$this->parameters = $parameters;
	}

	public function __toString()
	{
		$text = dgettext('luna', $this->identifier);

		return $this->parameters ? vsprintf($text, $this->parameters) : $text;
	}
}
