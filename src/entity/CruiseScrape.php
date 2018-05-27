<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 02:07
 */

namespace Entity;

use Core;

class CruiseScrape extends Core\Entity
{

    use Core\_Base;

    /*
     * this duplicating the fields below!!
     */
    public $cruise_id;
    public $title;
    public $ship_id;
    public $shipName;
    public $cruiseline_id;
    public $cruiseLine;
    public $cruiseLineLogo;
    public $itinerary=[];
    public $itineraryUrl;
    public $ports=[];
    public $images=[];

    /*
     * this Entity isn't saved - its used to collect data which will be saved to their respective entities
     */
    public static function init()
    {
        // plural for table
        parent::init(self::class, [
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
        ],[
            'entity' => 'cruise' ,
            'table' => 'cruises'
        ]);

    }

    public function __construct()
    {
        parent::__construct("Cruise");
        self::init();   // can't call this auto from below!! Must be called in this scope
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
        $this->get($dom, "cruiseLine");
        $this->get($dom, "shipName");
        $this->get($dom, "cruiseLineLogo");
        $this->get($dom, "itinerary");
        $this->get($dom, "itineraryUrl");

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

    public function get($dom, $fieldName)
    {
        if (!isset(self::$fields[$fieldName])) {
            self::error("cannot retrieve field def for [$fieldName]");
            return null;
        }
        $field = self::$fields[$fieldName];
        $sel = $field['args']['scraper']['sel'];
        $ord = $field['args']['scraper']['ord'];
        $cb = $field['args']['scraper']['callback'];

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
        $this->$fieldName = $text;

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
        $itinerary=[];
        self::debug("extractng itinerary");
        foreach($node->elements as $day) {
            $stop = [];
            $stop['day'] = trim($day->getElementsByTagName("div")->item(0)->nodeValue);

            // cruising days are not a link
            $port = $day->getElementsByTagName("a")->item(0);
            if($port) {
                //$stop['port']= trim($port->getElementsByTagName("span")->item(0)->nodeValue);
                $stop['port'] = trim($port->nodeValue);
                $stop['url'] = $port->getAttribute("href");
            } else {
                // cruising
                $stop['at sea']=true;
            }
            $itinerary[$stop['day']] = $stop;
        }
        $url = $node->elements[0]->getAttribute("src");
        return $itinerary;

    }
    public function cb_itineraryUrl($node) {
        // have to get to the dom element
        $url = $node->elements[0]->getAttribute("href");
        return $url;
    }
    public static function saveAll($data=null) {
        if($data) {
            self::$data=$data;
        }
        foreach ($data as $datum) {
            $datum->save();
        }
    }
    /*
     * save it to constituent entitiesd
     */
    public function save() {
        // start with the cruiseline
        // should create a CruiseLine object and map it via Model to DB!!
        $row = Core\Db::findAddKey("cruiselines" , "cruiseline_id", 'name'  ,
            [
                'name' => $this->cruiseLine ,   // need a mapping
                'logo' => $this->cruiseLineLogo // need a mapping
            ] );

        $this->cruiseline_id = $row['cruiseline_id'];

        // next tackle the ship
        $row = Core\Db::findAddKey("ships" , "ship_id", 'name'  ,
            [
                'name' => $this->shipName ,  // need a mapping
                'cruiseline_id' => $this->cruiseline_id
            ] );
        $this->ship_id = $row['ship_id'];

        // next tackle the ports

        foreach ($this->itinerary as $item) {
            if(!isset($item['at sea'])) { // in port
                $row = Core\Db::findAddKey("ports" , "port_id", 'name'  ,
                    [
                        'name' => $item['port'],  // need a mapping
                        'url' => $item['url']
                    ] );
                $item['port_id'] = $row['port_id'];
            }

        }

    }
    /*
     * load it from entities given a key
     */


}