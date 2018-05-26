<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 13:08
 */

namespace App;
use Core;
/*
 * model each scraped website
 */
class Scraper {
    use Core\_Base;

    public $name;
    public $domain;
    public $urls=[];
    // public $routes=[];

    public function __construct($name,$domain) {
        $this->name=$name;
        $this->domain=$domain;
        self::add($this);
    }
    /*
     * you can name routes
     */
    public function addUrl($name,$url) {
        if(isset($this->urls[$name])) {
            self::warning("overwriting exists URL [$name][$url]");
        }
        $this->urls[$name]=$url;
    }
    /*
     * can fill apoarm urls with named args
     * url args in for {arg}
     */
    public function getUrl($url,$args) {

    }

}