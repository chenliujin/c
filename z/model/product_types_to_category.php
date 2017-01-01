<?php
namespace z;

include_once('z/requires.php');

class product_types_to_category extends z 
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-24
	 */
	static public function getTableName()
	{
		return 'product_types_to_category';
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-24
	 * look up categories product_type
	 */
	public function get_product_type_id($category_id) 
	{
		$options = array(
			'category_id'	=> (int)$category_id
		);

		$rs = self::findOne($options);

		return $rs->product_type_id ? $rs->product_type_id : FALSE;
	}

}


