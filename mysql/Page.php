<?php
/*
$page = new Page($query);
$rs = $page->data();
 */


/**
 * TODO
 * order by
 * group by
 */
class Page extends \Model 
{
	public $query;
	public $query_params;
	public $page_name = 'page';
	public $page_total;
	public $number_of_rows_per_page = 20;
	public $current_page;

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 */
	public function __construct($query, $params = array())
	{
		$this->query 		= $query;
		$this->query		= str_replace(PHP_EOL, ' ', $this->query); //去掉换行符
		$this->query_params = $params;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-13
	 */
	public function order_by($order_by)
	{
		$this->order_by = $order_by;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 */
	public function per_page_rows($num)
	{
		$this->number_of_rows_per_page = intval($num) > 0 ? intval($num) : 20;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 */
	public function setPageName($page_name)
	{
		$this->page_name = $page_name;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 */
	public function total()
	{
		$query = preg_replace('/SELECT.+FROM/i', 'SELECT COUNT(*) AS total FROM', $this->query);
		$result = $this->query($query, $this->query_params);

		return $result[0]->total;
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 */
	public function data()
	{
		$this->current_page = !empty($_REQUEST[$this->page_name]) ? intval($_REQUEST[$this->page_name]) : 1;
		$this->number_of_rows = $this->total();
		$this->page_total = ceil($this->number_of_rows / $this->number_of_rows_per_page);

		if ($this->current_page > $this->page_total) {
			$this->current_page = $this->page_total;
		}

		$offset = ($this->current_page - 1) * $this->number_of_rows_per_page;
		$offset = $offset . ',' . $this->number_of_rows_per_page;

		$this->query .= ' ' . $this->order_by;
		$this->query .= ' LIMIT ' . $offset;

		return $this->query($this->query, $this->query_params);
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-10-09
	 * TODO: SEO
	 */
	public function nav()
	{
		if ($this->page_total <= 1) return;

		$url  = $_SERVER['REQUEST_URI'];
		$url  = preg_replace('/(#.+$|[?&]+' . $this->page_name . '=[0-9]+)/', '', $url);
		$url .= strpos($url, '?') ? '&' : '?';
		$url .= $this->page_name . '=';

		if ($this->current_page <= 1) {
			$html = '<li>&lt;&nbsp;Previous Page</li>';
		} else {
			$html .= '<li><a href="' . $url . ($this->current_page - 1) . '">&lt;&nbsp;Previous Page</a></li>';
		}

		$start 	= $this->current_page-1 <= 1 ? 1 : $this->current_page-1;

		if ($this->current_page+3 >= $this->page_total) {
			$start 	= $this->page_total - 4;
			$end 	= $this->page_total;
		} else {
			$end 	= $this->current_page + 1;
		}

		if ($end < 6) {
			$start = 1;
			$end = $this->page_total > 5 ? 5 : $this->page_total;
		}

		if ($start >= 3) {
			$html .= '<li><a href="' . $url . '1">1</a></li>';
			$html .= '<li>...</li>';
		}

		for ($i=$start; $i<=$end; $i++) {
			if ($i == $this->current_page) {
				$html .= '<li>' . $i . '</li>';
			} else {
				$html .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
			}
		}

		if ($end <= $this->page_total - 1) {
			if ($end < $this->page_total - 1) {
				$html .= '<li>...</li>';
				$html .= '<li>' . $this->page_total . '</li>';
			} else {
				$html .= '<li><a href="">' . $this->page_total . '</a></li>';
			}
		}

		if ($this->current_page + 1 > $this->page_total) {
			$html .= '<li>Next Page&nbsp;&gt;</li>';
		} else {
			$html .= '<li><a href="' . $url . ($this->current_page + 1) . '">Next Page&nbsp;&gt;</a><li>';
		}

		$nav = <<<EOB
<style>
nav {
	height: 36px;
	line-height: 36px;
	background: #eee;
	vertical-align:middle;
}
nav ul {
	margin: 0;
	list-style: none;
	text-align: center;
}
nav li {
	margin-left: 15px;
	display: inline-block;
}
</style>
		<nav>
			<ul>
				$html
			</ul>
		</nav>
EOB;
		echo $nav;
	}
}
