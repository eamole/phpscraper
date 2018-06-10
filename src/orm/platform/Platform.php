<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 29/05/2018
 * Time: 12:07
 */


/*
 * this should probable be Core\Language not ORM!! or ORM should extend Core version of this
 */
namespace ORM\Platform;

/*
 * This is a type factory
 * is uses a language selector
 * Php SQL etc to
 * within the language/platform we then
 * select a type supported by that platform
 * we need to introduce x-platform type mappings
 * I need to create one factory ? per language
 * requests here are delegated to the handler!!
 * This prob should be alnguage handler, with datatypes being just one aspect
 */
use Core;
/*
 * this should probably be Language as a language/platform selector
 * this should be stored (singleton) in App as language/platform controller
 * WP might be an extension of this class?
 */
class Platform extends  Core\Base
{
	public static $langs = [];

	public $lang;		// the lang id
	public $ns;			// the name space of the handler class - based on the lang
	public $datatypeHandler;	// the data type handler

	public function __construct($lang,$ns=null){

		$ns = __NAMESPACE__ . $ns ?? $ns = ucfirst($lang);

		$this->lang = $lang;
		$this->ns = $ns;

		$class = $ns.$lang . "\\TypeFactory";
		// create a new datatypes handlere
		$this->datatypeHandler = new $class($lang);


	}
	public function init() {
		/*
		 * the languages should probably be plugins
		 */
		self::addLang("php");
		// we might add some common translations to support field naming from sql to model, and model to entity
		self::addLang("model");
		self::addLang("sql");

	}
	/*
	 * this creates a new DataType object (self)
	 */
	public static function addLang($lang,$ns=null) {
		$ns = __NAMESPACE__ . $ns ?? $ns = ucfirst($lang);
		self::$langs[$lang]=new Platform($lang,$ns);

	}
}