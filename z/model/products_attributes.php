<?php
namespace z;

include_once('z/model/z.php');
include_once('z/model/products_options.php');
include_once('z/model/products_options_values.php');

class products_attributes extends \Model
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-06
	 */
	static public function GetTableName()
	{
		return 'products_attributes';
	}


	/**
	 * @authro chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-09
	 */
	public function getPrimaryKey()
	{
		return ['products_attributes_id'];
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


	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-06
	 */
	static public function GetAttributes($products_id, $parent_id, $language_id)
	{
		$sql = '
			SELECT
		   		products_attributes.products_id, 
		   		products_attributes.attributes_image, 
				products_options.products_options_name,
				products_options_values.products_options_values_id,
				products_options_values.products_options_values_name
			FROM ' . self::GetTableName() . '  
			LEFT JOIN ' . \z\products_options::GetTableName() . ' ON options_id = products_options_id 
			LEFT JOIN ' . \z\products_options_values::GetTableName() . ' ON options_values_id = products_options_values_id AND products_options.language_id = products_options_values.language_id
			WHERE parent_id = ? AND products_options.language_id = ? 
			ORDER BY products_attributes.products_options_sort_order';

		$params = [$parent_id, $language_id];

		$products_attributes 		= new self;
		$products_attributes_list 	= $products_attributes->query($sql, $params);

		foreach ($products_attributes_list as $product_attribute) {
			$product[$product_attribute->products_id][$product_attribute->products_options_name] = $product_attribute->products_options_values_name;



			$data[$product_attribute->products_options_name][$product_attribute->products_options_values_name][] = $product_attribute;

			if ($product_attribute->products_id == $products_id) {
				$current[$product_attribute->products_options_values_name] = $product_attribute->products_options_values_id;
			}
		}

		foreach ($data as $option => $arr1) {
			foreach ($arr1 as $option_value => $arr2) {
				foreach ($arr2 as $i => $product_attribute) {
					foreach ($product[$products_id] as $key => $value) {
						if ($key != $option) {
							if ($product[$product_attribute->products_id][$key] != $product[$products_id][$key]) {
								unset($data[$option][$option_value][$i]);
							}
						}
					}
				}
			}
		}

		return $data;
/*
$attributes = [
  'Color' => [
      'red' => [
            'products_id' => '1',
            'attributes_name'  => 'red',
            'attributes_image' => 'images/attributes/color_red.gif',
          ],
      'yellow' => [
            'products_id' => '',
            'attributes_image' => 'images/attributes/color_yellow.gif',
          ],
      'blue' => [
            'products_id' => '',
            'attributes_image' => 'images/attributes/a_bugs_life_blue.gif',
          ],
  ],
  'Size'  => [
        [
              'attributes_name' => 'M',
              'products_id' => '',
        ],
        [
              'attributes_name' => 'L',
              'products_id' => '1',
        ],
        [
              'attributes_name' => 'XL',
              'products_id' => '',
        ],  
  ],
];
*/

	}

}
