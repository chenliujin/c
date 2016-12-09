<?php
namespace z;

include_once('z/model/z.php');

/**
 * TODO
 * products_description.products_url: 添加字段生成产品的链接，考虑 SEO
 */
class products extends \Model
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-29
	 */
	static public function getTableName()
	{
		return 'products';
	}

	/**
	 * @authro chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-07
	 */
	public function getPrimaryKey()
	{
		return ['products_id'];
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
	 * @since 2016-10-07
	 */
	static public function GetImage($filename, $size)
	{
		$part = explode('.', $filename);

		return $part[0] . '_' . $size . '.' . $part[1];
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-08
	 */
	static public function Image($filename, $alt='', $width='', $height='')
	{
		$part = explode('.', $filename);

		$src = $part[0] . '_' . $width . '.' . $part[1];

		return '<img src="' . $src . '" alt="' . $alt . '" title="' . $alt . '" width="' . $width . '" height="' . $height . '" />';
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-29
	 * TODO clear $db, $currencies
	 */
	static public function ShowPriceList( $products_id )
	{
		global $db, $currencies;

		$price = self::GetPriceList( $products_id );

		switch (TRUE) {
			case !empty($price->sale_price): 
				$str = ' 
					<tr>
						<td class="size-base text-right nowrap">Price:</td>
						<td class="price-del">' . $currencies->format($price->normal_price) . '</td>
					</tr>
					<tr class="price">
						<td class="size-base text-right nowrap">' . PRODUCT_PRICE_SALE .'</td>
						<td class="size-medium">' . $currencies->format($price->sale_price) . '</td>
					</tr>
					<tr>
						<td class="size-base text-right nowrap">' . PRODUCT_PRICE_DISCOUNT_PREFIX . '</td>
						<td class="price">' . $price->sale_discount . '</td>
					</tr>
					';
				break;

			default:
				$str = ' 
					<tr>
						<td class="size-base text-right">Price:</td>
						<td class="size-medium price">' . $currencies->format($price->normal_price) . '</td>
					</tr>
					';
				break;
		}

		echo $str;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-29
	 * TODO clear $db, $currencies
	 */
	static public function GetPriceList( $products_id )
	{
		global $db, $currencies;

		$price = new \stdClass;

		$product_check = $db->Execute("
			select 
				products_tax_class_id, 
				products_price, 
				products_priced_by_attribute, 
				product_is_free, 
				product_is_call, 
				products_type 
			from " . TABLE_PRODUCTS . " 
			where products_id = '" . (int)$products_id . "'" . " limit 1");

		// no prices on Document General
		if ($product_check->fields['products_type'] == 3) {
			return '';
		}

		$price->normal_price 	= zen_get_products_base_price($products_id);
		$price->special_price 	= zen_get_products_special_price($products_id, true);
		$price->sale_price 		= zen_get_products_special_price($products_id, false);

		if ($price->sale_price) {
			$price->sale_discount  = $currencies->format($price->normal_price - $price->sale_price);
			if ($price->normal_price) {
				$price->sale_discount .= '&nbsp;'; 
				$price->sale_discount .= '(' . number_format(100 - (($price->sale_price / $price->normal_price) * 100), SHOW_SALE_DISCOUNT_DECIMALS) . '%)';
			}
		} else {
			$price->sale_discount  = $currencies->format($price->normal_price - $price->special_price);

			if ($price->normal_price) {
				$price->sale_discount .= '&nbsp;';
				$price->sale_discount .= '(' . number_format(100 - (($price->special_price / $price->normal_price) * 100), SHOW_SALE_DISCOUNT_DECIMALS) . '%)';
			}
		}

		return $price;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 */
	static public function GetAllProducts()
	{
		$sql = "
			SELECT 
				p.products_type, 
				p.products_id, 
				pd.products_name, 
				p.products_image, 
				p.products_price, 
				p.products_tax_class_id, 
				p.products_date_added, 
				m.manufacturers_name, 
				p.products_model, 
				p.products_quantity, 
				p.products_weight, 
				p.product_is_call, 
				p.product_is_always_free_shipping, 
				p.products_qty_box_status, 
				p.master_categories_id 
			FROM " . self::GetTableName() . " p LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd 
			WHERE 
				p.products_status = 1 AND 
				p.products_id = pd.products_id AND 
				pd.language_id = ? 
			"; 

		$params = [
			$_SESSION['languages_id']
		];

		$page = new \Page($sql, $params);
		$page->per_page_rows(12);
		$page->order_by('ORDER BY products_id DESC');

		return $page;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-28
	 */
	static public function GetCategoriesProduct($sql)
	{
		$params = [];

		$page = new \Page($sql, $params);
		$page->per_page_rows(12);
		//$page->order_by('ORDER BY products_id DESC');

		return $page;
	
	}


	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-12-08
	 */
	static public function UploadProductImage($products_id)
	{
		if (!$products_id) return;

		$www_root = '/data/www/z/';

		$path = str_pad(substr($products_id, 0, 4), 4, '0', STR_PAD_LEFT);
		$path = substr($path, 0, 2) . '/' . substr($path, 2, 2) . '/' . $products_id;
		$path = 'II/' . $path . '/';

		@mkdir($www_root . $path, 0777, TRUE);

		foreach ($_FILES['product_image']['error'] as $i => $error) {
			if ($error != UPLOAD_ERR_OK) continue; 

			$file = $path . $_FILES['product_image']['name'][$i];
			$images[] = $file;

			$file = $www_root . $file;
			$path_info = pathinfo($file);

			$rs = move_uploaded_file($_FILES['product_image']['tmp_name'][$i], $file);

			if ($rs) {
				$imagick = new \Imagick($file);

				$imagick->resizeImage(450, 450, \Imagick::FILTER_LANCZOS, TRUE);
				$imagick->writeImage($www_root . $path . $path_info['filename'] . '_450.' . $path_info['extension']);

				if ($i == 0) {
					$imagick->resizeImage(220, 220, \Imagick::FILTER_LANCZOS, TRUE);
					$imagick->writeImage($www_root . $path . $path_info['filename'] . '_220.' . $path_info['extension']);

					$imagick->resizeImage(100, 100, \Imagick::FILTER_LANCZOS, TRUE);
					$imagick->writeImage($www_root . $path . $path_info['filename'] . '_100.' . $path_info['extension']);
				}

				$imagick->resizeImage(40,  40,  \Imagick::FILTER_LANCZOS, TRUE);
				$imagick->writeImage($www_root . $path . $path_info['filename'] . '_40.' . $path_info['extension']);
			}
		}

		if (empty($images)) return;

		$products = new self;
		$products = $products->get($products_id);
		$products->products_image = json_encode($images);
		$products->update();
	}
}
