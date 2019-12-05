<?php
namespace App\Http\Singletons;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequestsHelper
{
    // Hold an instance of the class
    private static $_instance = null;

    // The singleton method
    public static function getInstance()
    {
        if (!is_object(self::$_instance))  //or if( is_null(self::$_instance) ) or if( self::$_instance == null )
            self::$_instance = new RequestsHelper();
        return self::$_instance;
    }

    public function doGetRequest($url)
    {
        $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        $result = curl_exec($curl);
        return $result;
    }

}
