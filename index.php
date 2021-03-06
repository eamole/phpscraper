<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 02:24
 */
//phpinfo();
/*
class A {

	public static function go() {
		return "Hello guys ".static::class;
	}
}
class B extends A {


}

echo A::go();

exit ("done");
*/

error_reporting (E_ALL );
include "src/SplClassLoader.php";

include "src/phpquery/phpQuery-onefile.php";

$loader = new SplClassLoader( null, array('src/'));
$loader->register();

App\AppScraper::init();

//include "src/app/scrapers.php";

//$html = file_get_contents("src/upcoming_cruises.html");
//if(!$html) {
//    Core\Base::error("file does not exists "."src/upcoming_cruises.html");
//}
//echo "Hello";


/*
 * most important - need to come in at the top to force
 * an init - the lower Core/Db doesn't know how to connect
 * getting ::init() error - can't seem to call "up" the inheritance tree
 */
App\Db::init();

/*
 * Entities have a probem to do with gtheir static nature and cross coupling
 * with Models
 * Therefore before we contruct a new entoty we need to init the class!
 * What a bloody mess
 *
 * The entity requires the model to already be created to provide field names
 * however, the model is also calling the Entity for the entity name
 * circular!! Therefore change them to deferred methods
 *
 */

use App\ORM\Models;
use App\ORM\Entities;
//\Entity\CruiseLine::init();
//\Entity\Ship::init();

$ships=App\ORM\Models\Ships::getModel();
$cruiselines = Models\Cruiselines::getModel();

$cruiseline = Entities\Cruiseline::findUniqueBy("name","test4");

$ship=new Entities\Ship();
$ship->cruiseline =$cruiseline;
$ship->save();