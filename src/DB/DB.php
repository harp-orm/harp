<?php namespace CL\Luna\DB;

use PDO;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class DB extends \CL\Atlas\DB {

	public static $defaults = array(
		'dsn' => 'mysql:dbname=test;host=127.0.0.1',
		'username' => '',
		'password' => '',
		'driver_options' => array(
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_STATEMENT_CLASS => 'CL\Luna\DB\PDOStatement',
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		),
	);
}
