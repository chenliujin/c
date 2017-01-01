<?php

namespace z;

include_once('z/requires.php');

class products_to_categories extends z 
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-09
	 */
	static public function GetTableName()
	{
		return 'products_to_categories';
	}


	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-09
	 */
	static public function GetPrimaryKey()
	{
		return ['products_id', 'categories_id'];
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
