<?php

/**
 * Methods associated with getting data from HTTP requests.
 */
class Request
{
    private function __construct() {}

    /**
     * Get values from the POST superglobal. If no value is set
     * return a default value.
     *
     * @return value in the matching POST superglobal
     */
    public static function post($str, $default = 0) {
        return isset($_POST[$str]) ? $_POST[$str] : $default;
    }

    /**
     * Get values from the GET superglobal. If no value is set
     * return a default value.
     *
     * @return value in the matching GET superglobal
     */
    public static function get($str, $default = 0) {
        return isset($_GET[$str]) ? $_GET[$str] : $default;
    }
}
