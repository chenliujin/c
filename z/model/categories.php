<?php
namespace z;

include_once('z/requires.php');
include_once('z/model/categories_description.php');

class categories extends z 
{
	static public $root;
	static public $data;

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-21
	 */
	static public function getTableName()
	{
		return 'categories';
	}


	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-21
	 */
	public function get_category_tree()
	{
		$language_id = $_SESSION['languages_id'];

		$sql = "
			SELECT categories.categories_id, categories_name, parent_id 
			FROM " . self::getTableName() . ", " . categories_description::getTableName() . " 
			WHERE categories.categories_id = categories_description.categories_id
		   		AND language_id = " . (int)$language_id . "
				AND categories_status = 1
			ORDER BY parent_id, sort_order, categories_name
			";

		$_this = new self;
		$rs = $_this->query($sql);

		foreach ($rs as $category) {
			$category->path = !$category->parent_id ? 'cPath=' . $category->categories_id : self::$data[$category->parent_id]->path . '_' . $category->categories_id;

			self::$data[$category->categories_id] = $category;

			if (!$category->parent_id) {
				self::$root[$category->categories_id] = $category;
			} else {
				self::$data[$category->parent_id]->children[] = $category->categories_id;
			}
		}
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-22
	 */
	static public function root()
	{
		if (!self::$root) {
			self::get_category_tree();
		}

		return self::$root;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-23
	 */
	static public function get_category($categories_id)
	{
		if (!self::$data) {
			self::get_category_tree();
		}

		return self::$data[$categories_id];
	}

}
