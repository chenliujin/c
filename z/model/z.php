<?php
namespace z;

class z extends \Model
{
	static protected $dbo;

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2017-01-01
	 */
	static public function getDbHandle($host='localhost', $port=3306, $dbname='', $username='', $password='')
	{
		if (!self::$dbo) {
			self::$dbo = self::connect(DB_SERVER, 3306, DB_DATABASE, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
		}

		return self::$dbo;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2017-01-01
	 */
	static public function getInstance()
	{
		static $instance;

		if (!$instance) {
			$instance = new self;
		}

		return $instance;
	}
}
