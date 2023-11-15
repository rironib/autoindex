<?php

class pag
{
	var $pages = null;

	function __construct($total, $page, $perpage = 10)
	{
		global $lang;
		$total_pages = ceil($total / $perpage);
		foreach ($_GET as $k => $v)
			if ($k != 'page')
				$query .= "&$k=$v";

		if ($page > 4)
			$this->pages .= "<a class='page-link' href='?$query'>$lang->first</a>";

		if ($page > 1)
			$this->pages .= "<a class='page-link' href='?page=" . ($page - 1) . "$query'>$lang->prev</a>";

		for ($i = max(1, $page - 3); $i <= min($page + 3, $total_pages); $i++) {
			if ($i == $page) {
				$this->pages .= "<span class='page-link text-dark'>$i</span>";
			} else {
				$this->pages .= "<a class='page-link' href='?page=$i$query'>$i</a>";
			}
		}

		if ($page < $total_pages)
			$this->pages .= "<a class='page-link' href='?page=" . ($page + 1) . "$query'>$lang->next</a>";

		if ($page < $total_pages - 3)
			$this->pages .= "<a class='page-link' href='?page=$total_pages$query'> $lang->last </a>";

		return true;
	}
}
