<?php
namespace z;

include_once('z/model/z.php');

class products_discount_quantity extends \Model
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-20
	 */
	static public function GetTableName()
	{
		return 'products_discount_quantity';
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-20
	 */
	static public function GetInstance()
	{
		static $instance;

		if (!$instance) {
			$instance = new self;
		}

		return $instance;
	}
}
