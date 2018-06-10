<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 02:07
 */

namespace App\ORM\Entities;

use App\ORM\Models;

use Core;
use ORM;
use ORM\EntityManager;


class Cruise extends ORM\Entity
{


	/*
	 * this duplicating the fields below!!
	 */
	/*
	public $cruise_id;
	public $title;
	public $ship_id;
	public $shipName;
	public $cruiseline_id;
	public $cruiseLine;
	public $cruiseLineLogo;
	public $itinerary = [];
	public $itineraryUrl;
	public $ports = [];
	public $images = [];

	*/

	/*

	 [
				  ['name' => 'title', 'type' => 'string', 'len' => 255,
							 'args' => ['index' => true, 'scraper' => ['sel' => '.fac-result__title', 'ord' => 0, 'callback' => null]]],
				  ['name' => 'cruiseline_id', 'type' => 'int', 'len' => null],
				  ['name' => 'cruiseLine', 'type' => 'string', 'len' => 255,
							 'args' => ['index' => true, 'scraper' => ['sel' => '.fac-result__cruise_line', 'ord' => 0, 'callback' => null]]],
				  ['name' => 'ship_id', 'type' => 'string', 'len' => null],
				  ['name' => 'shipName', 'type' => 'string', 'len' => 255,
							 'args' => ['index' => true, 'scraper' => ['sel' => '.fac-result__ship-name', 'ord' => 0, 'callback' => null]]],
				  ['name' => 'cruiseLineLogo', 'type' => 'string', 'len' => 255,
							 'args' => ['scraper' => ['sel' => '.fac-result__ship-logo img', 'ord' => 0, 'callback' => 'cb_cruiseLineLogo']]],
				  ['name' => 'itineraryUrl', 'string' => 'array', 'len' => 255,
							 'args' => ['scraper' => ['sel' => '.itinerary-map__data__view-more a', 'ord' => null, 'callback' => 'cb_itineraryUrl']]],
				  ['name' => 'itinerary', 'type' => 'array', 'len' => null,
							 'args' => ['scraper' => ['sel' => '.itinerary-map__data li', 'ord' => null, 'callback' => 'cb_itinerary']]],
				  ['name' => 'images', 'type' => 'array', 'len' => null,
							 'args' => ['scraper' => ['sel' => '', 'ord' => 0, 'callback' => null]]],
				  ['name' => 'ports', 'type' => 'array', 'len' => null,
							 'args' => ['scraper' => ['sel' => '', 'ord' => 0, 'callback' => null]]],
		]

	*/

/*
	public function __construct()
	{
		parent::__construct(self::class , Models\Cruises::class);
//		self::init();   // can't call this auto from below!! Must be called in this scope
	}*/

	/*
	 * this is called once by the EM
	 * here I can set the scraper params
	 */
	public static function init(EntityManager $em) {

		$em->addProps("cruiseLineLogo,cruiseLineName,shipName,itinerary");

//		['sel' => '.fac-result__title', 'ord' => 0, 'callback' => null]
//		['sel' => '.fac-result__cruise_line', 'ord' => 0, 'callback' => null
//		['sel' => '.fac-result__ship-name', 'ord' => 0, 'callback' => null]
//		['sel' => '.fac-result__ship-logo img', 'ord' => 0, 'callback' => 'cb_cruiseLineLogo']
//		['sel' => '.itinerary-map__data__view-more a', 'ord' => null, 'callback' => 'cb_itineraryUrl']
//		['sel' => '.itinerary-map__data li', 'ord' => null, 'callback' => 'cb_itinerary']

		self::setScraperArgs($em,"title" , '.fac-result__title' , 0 );
		self::setScraperArgs($em,"cruiseLineName" , '.fac-result__cruise_line' ,0 );
		self::setScraperArgs($em,"shipName" , '.fac-result__ship-name' , 0 );
		self::setScraperArgs($em,"cruiseLineLogo" , '.fac-result__ship-logo img' , 0,'cb_cruiseLineLogo' );
		self::setScraperArgs($em,"itinerary_url" , '.itinerary-map__data__view-more a' , null , 'cb_itineraryUrl' );
		self::setScraperArgs($em,"itinerary" , '.itinerary-map__data li' , null,  'cb_itinerary' );


	}

	/**
	 * @param EntityManager $em
	 * @param $propName
	 * @param $sel
	 * @param null $ord	- applied to sel when sel returns more than one element
	 * @param null $callback
	 */
	public static function setScraperArgs(EntityManager $em, $propName, $sel, $ord=null , $callback=null)
	{
		$prop=$em->isProperty($propName);
		$prop->sel=$sel;
		$prop->ord=$ord;
		$prop->callback = $callback;

	}

	public function fromHtml($dom)
	{
		/*        pq($dom);
				  $node = pq(".fac-result__title hidden-sm-up heading__bold--lg");
				  $this->title = $node->text();*/

		/*        $this->set($dom,"title",".fac-result__title",0);
				  $this->set($dom,"cruiseLine",".fac-result__cruise_line",0);
				  $this->set($dom,"ship",".fac-result__ship-name",0);*/

		$this->get($dom, "title");
		$this->get($dom, "cruiseLineName");
		$this->get($dom, "shipName");
		$this->get($dom, "cruiseLineLogo");
		$this->get($dom, "itinerary");
		$this->get($dom, "itinerary_url");

	}

	public function set($dom, $field, $sel, $ord = null)
	{
		$a = 1;
		if (!is_null($ord)) {
			$node = $dom->find($sel)->eq($ord);
		} else {
			$node = $dom->find($sel);
		}
		$text = trim($node->text());
		$this->$field = $text;

	}

	public function get($dom, $propName)
	{

		if(!$prop = $this->isProperty($propName))
		{
			self::error("cannot retrieve property def for [$propName]");
			return null;
		}
		$sel = $prop->sel;
		$ord = $prop->ord;
		$cb = $prop->callback;

		if (!is_null($ord)) {
			$node = $dom->find($sel)->eq($ord);
		} else {
			$node = $dom->find($sel);
		}
		if ($cb) {
			$text = $this->$cb($node);
		} else {
			$text = trim($node->text());
		}
		$this->$propName = $text;

	}

	public function cb_cruiseLineLogo($node)
	{
//        self::debug("extractng cruiseline logo");
		$url = $node->elements[0]->getAttribute("src");
		return $url;
	}

	// class li
	public function cb_itinerary($node)
	{
		$itinerary = [];
		self::debug("extractng itinerary");
		foreach ($node->elements as $day) {
			$stop = [];
			// this dangerous - might be mre than one stop per day
			$stop['day'] = trim($day->getElementsByTagName("div")->item(0)->nodeValue);

			// cruising days are not a link
			$port = $day->getElementsByTagName("a")->item(0);
			if ($port) {
				//$stop['port']= trim($port->getElementsByTagName("span")->item(0)->nodeValue);
				$stop['name'] = trim($port->nodeValue);
				$stop['url'] = $port->getAttribute("href");
			} else {
				// cruising
				$stop['at_sea'] = true;
			}
			$itinerary[] = (object) $stop;
		}
		$url = $node->elements[0]->getAttribute("src");
		return $itinerary;

	}

	public function cb_itineraryUrl($node)
	{
		// have to get to the dom element
		$url = $node->elements[0]->getAttribute("href");
		return $url;
	}

	public static function saveAll($data = null)
	{
/*		if ($data) {
			self::$data = $data;
		}*/
		foreach ($data as $datum) {
			$datum->save();
		}
	}

	public function save()
	{
		/*
		 * what really should happen here is that these objects should already exist and be embedded in
		 * cruise object -
		 * I need a way of
		 */
		$cruiseline = new Cruiseline();
		$cruiseline->name = $this->cruiseLineName;
		$cruiseline->logo = $this->cruiseLineLogo;
		$cruiseline->saveOrAddKey("name");
		$this->cruiseline_id=$cruiseline->cruiseline_id;

		$ship=new Ship();
		$ship->name=$this->shipName;
		$ship->saveOrAddKey("name");
		$this->ship_id = $ship->ship_id;

		$this->saveOrAddKey("title");

		/*
		 * now we have a few lists to attach to
		 * start with reference data
		 * always have problems when we take items from list and tghen change them!! Need to add them back
		 */

		// this is a GOTCHA - cause of magic methods, can't update the array live!! think references etc
		$itinerary = $this->itinerary;

		foreach ($itinerary as $key => $node) {
			if(isset($node->name)) {
				$port = new Port();
				$port->name = $node->name;
				$port->url = $node->url;

				$port->saveOrAddKey("name");
				$itinerary[$key]=$port;
			} else {
				// assume at sea

			}
		}
		$this->itinerary = $itinerary;

		// now go iver the list again, and save the actual itenary
		foreach ($this->itinerary as $key => $node) {


		}

	}

	/*
	 * save it to constituent entitiesd
	 */
	public function save_old()
	{
		// start with the cruiseline
		// should create a CruiseLine object and map it via Model to DB!!
		$row = Core\Db::findKeyOrAdd("cruiselines", "cruiseline_id", 'name',
				  [
							 'name' => $this->cruiseLine,   // need a mapping
							 'logo' => $this->cruiseLineLogo // need a mapping
				  ]);

		$this->cruiseline_id = $row['cruiseline_id'];

		// next tackle the ship
		$row = Core\Db::findKeyOrAdd("ships", "ship_id", 'name',
				  [
							 'name' => $this->shipName,  // need a mapping
							 'cruiseline_id' => $this->cruiseline_id
				  ]);
		$this->ship_id = $row['ship_id'];

		// next tackle the ports

		foreach ($this->itinerary as $item) {
			if (!isset($item['at sea'])) { // in port
				$row = Core\Db::findKeyOrAdd("ports", "port_id", 'name',
						  [
									 'name' => $item['port'],  // need a mapping
									 'url' => $item['url']
						  ]);
				$item['port_id'] = $row['port_id'];
			}

		}

	}
	/*
	 * load it from entities given a key
	 */


}