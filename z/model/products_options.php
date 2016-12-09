<?php
namespace z;

include('z/model/z.php');

class products_options extends \Model
{
	/**
	 * @authro chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-06
	 */
	static public function GetTableName()
	{
		return 'products_options';
	}


	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-09
	 */
	public function getPrimaryKey()
	{
		return ['products_options_id', 'language_id'];
	}


	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-09
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
