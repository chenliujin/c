<?php
namespace z;

include_once('z/requires.php');

class customers extends z 
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-19
	 */
	static public function getTableName()
	{
		return 'customers';
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-19
	 */
	public function get_customer_nickname()
	{
		if (!empty($_SESSION['customer_first_name'])) {
			return $_SESSION['customer_first_name'];
		}
	}
}

