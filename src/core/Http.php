<?php
/**
 * Created by PhpStorm.
 * User: eamol
 * Date: 26/05/2018
 * Time: 16:13
 */

namespace Core;
use Core;

class Http extends Base{

    public static $pathToCache = "/cache/http/";

    public $options;
    public $html;   // the most recently retrieved HTML
    public $url;
    public $path;

    public $context;    // a context to use with get

    // uni
    public static $http;

    public static function init() {
        if(!isset(self::$http)) {
            self::$http = new Http();
        }
        self::$pathToCache =  Core\App::$storage . self::$pathToCache ;
        mkdir(self::$pathToCache, 0755, true);
        self::debug("Path to cache ".self::$pathToCache);
    }

    public function __construct()
    {
    	parent::__construct();
		 /*u
		  * for use with file_get_contents - otherwise 404
		  */
        $options = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                    "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            )
        );

        $this->context = stream_context_create($options);

    }
    /*
     * static version
     */
    public static function _get($url,$args=[]) {
        self::init();
        return self::$http->get($url,$args);
    }

    public function get($url,$args=[]) {

        foreach ($args as $key=>$value) {
            $url = str_replace("{$key}" , $value , $url );
        }

        $this->url=$url;

        // convert it to a path
        $path = str_replace("/" , "_" , $this->url);
        $path = str_replace(":" , "_" , $path);
        $path = str_replace("\\" , "_" , $path);
        $path = str_replace("?" , "_" , $path);
        $path = str_replace("=" , "_" , $path);
        $path = str_replace("&" , "_" , $path);

        // add the cache
        $path = self::$pathToCache.$path;
        // save the path
        $this->path = $path;

        if(file_exists( $path)) {
            self::debug("cache hit [$path]");
            $html = file_get_contents($path);
        } else {
            self::debug("cache miss [$path]. Fetching fresh [$url]");

            $html = file_get_contents($this->url,false,$this->context );

            file_put_contents($path , $html );
        }
        // save $html
        $this->html = $html;

        return $html;
    }
}