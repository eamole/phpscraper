<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 02:23
 */

function scraper_cruise_list($html) {

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