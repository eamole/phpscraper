<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 16:26
 */

namespace App;

use Core;

class AppScraper extends Core\App
{
//	use Core\_Base;

	public static function init()
	{

		parent::init(self::class, "The Scraper App", Db::class );
	}


}