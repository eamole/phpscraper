<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 02:24
 */
//phpinfo();

error_reporting (E_ALL );
include "src/SplClassLoader.php";

$loader = new SplClassLoader( null, array('src/'));
$loader->register();

include "src/scrapers.php";
include "src/phpquery/phpQuery-onefile.php";

$html = file_get_contents("src/upcoming_cruises.html");
if(!$html) {
    Core\Base::error("file does not exists "."src/upcoming_cruises.html");
}
echo "Hello";
echo scraper_cruise_list($html);
