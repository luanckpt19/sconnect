<?php

namespace App;

use Exception;

class NetworkUtils {
	
    /*
     * get html content from url
     * */
    public static function fetch_content_from_url($url) {
        try {
            $curlSession = curl_init();
            curl_setopt($curlSession, CURLOPT_URL, $url);
            curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($curlSession);
            curl_close($curlSession);
            
            return $html;
        } catch (Exception $e) {
        }
        return '';
    }
    
}
