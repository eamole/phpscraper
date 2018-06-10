<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 02:23
 */

use App\Scrapers;
/**
 * @param $html
 * @return string|void
 */
function scraper_cruise_list($html) {

	App\AppScraper::init();

    // Core\Db::connectToWP("src/");
//      echo php_ini_loaded_file();
//    print_r(get_loaded_extensions());
//    phpinfo();
    echo var_dump($_SERVER['DOCUMENT_ROOT']); //['DOCUMENT_ROOT'];
    echo Core\App::$root;

    $cruiseScraper = new Scrapers\CruiseCritic();
    $cruises = $cruiseScraper->getUpcoming();
    // $cruises->save();
//    App\CruiseScrape::dump($cruises);

    /*
        now we'd like to save it!!
    */

//    echo "\n<br/>Path : " . realpath(".");
    return;





    // $dom = new DOMDocument($html);
//    $dom = new phpQuery();
//    phpQuery::$debug=true;
    $dom = phpQuery::loadHtml($html);
    // really could do with a CSS type selectors!!

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
    }
    App\Cruise::dump($cruises);
    $new_html="";
    return $new_html;

}
class CssToXpath {



}