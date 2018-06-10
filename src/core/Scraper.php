<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 13:08
 */

namespace Core;

use Core;

/*
 * model each scraped website
 */

class Scraper extends Base
{
	use  _List;

	public $name;
	public $domain;
	public $urls = [];
	// public $routes=[];
	public $html;
	public $data = [];    // hold all captured values/objects

	public function __construct($name, $domain)
	{
		parent::_construct();

		$this->name = $name;
		$this->domain = $domain;
//        self::add($this);

	}

	/*
	 * you can name routes
	 */
	public function addUrl($name, $url, $scraper)
	{
		if (isset($this->urls[$name])) {
			self::warning("overwriting exists URL [$name][$url]");
		}
		$this->urls[$name] = ["url" => $url, "scraper" => $scraper];
		return $this;
	}

	/*
	 * can fill apoarm urls with named args
	 * url args in for {arg}
	 */
	public function getUrl($name, $args = [])
	{
		if (!isset($this->urls[$name])) {
			self::warning("cannot find URL [$name]");
		}
		$url = $this->urls[$name]['url'];
		$scraper = $this->urls[$name]['scraper'];

		$this->html = Http::_get($url);
		$this->dom = \phpQuery::loadHtml($this->html);

		return $this;
	}

	/*
	 * use a selector
	 */
	public function map($sel, $callback)
	{

		$nodes = pq($sel);
		$this->data = [];
		foreach ($nodes as $node) {
			// node is a domelement
			$node = pq($node);
			$datum = $this->$callback($node);
			$this->data[] = $datum;
		}
//        $this->data = $data;

		return $this;
	}


}