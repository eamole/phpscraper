<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 14:53
 */

namespace Scraper;
use Core;
use Entity;

class CruiseCritic extends Core\Scraper {
    use Core\_Base;
    /*
     * may need subdomains for images!! - not the same url for all
     */
    public function __construct() {
        parent::__construct("CruiseCritic" , "https://cruisecritic.com");
//        add($this);

        $this->addUrl("upcoming","https://www.cruisecritic.co.uk/cruiseto/cruiseitineraries.cfm?startDate=2018-06" , "upcoming");

    }

    public function getUpcoming() {

        $this->html = $this->getUrl("upcoming");

        $this->map("article","upcoming_cruise");

        // padd the captured data across
//        Entity\CruiseScrape::$data=;
        Entity\CruiseScrape::saveAll($this->data);

        if(function_exists("yaml_emit")) {
            $str = yaml_emit( $this->data );
            self::dump($str);

        } else {
            $str = json_encode($this->data,JSON_PRETTY_PRINT);
            self::dump($str);
            Core\App::writeFile("data/" . "data.json" , $str );
        }

        return $this->data;

        /*        //
                $dom = phpQuery::loadHtml($html);
                // really could do with a CSS type selectors!!

                // find all <articles> and map to cruises
                $articles = pq('article');    // '->getElementsByTagName("article");
                $cruises=[];
                foreach ($articles as $article) {
                    $article=pq($article);
                    phpQuery::debug("hello");
                    echo "New Cruise Article";
                    $cruise = new App\Cruise();
                    $cruise->title="Test";
                    $cruise->fromHtml($article);
                    $cruises[]=$cruise;
                }*/


    }

    public function upcoming_cruise($node) {

        $cruise = new Entity\CruiseScrape();
        $cruise->fromHtml($node);
        return $cruise;
    }
}