<?php
namespace z;

include_once('z/model/z.php');

class products_options_values extends \Model
{
	/**
	 * @authr chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-06
	 */
	static public function GetTableName()
	{
		return 'products_options_values';
	}
}
