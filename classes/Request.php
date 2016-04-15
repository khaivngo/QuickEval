<?php

class Request
{
    private function __construct() {}

    /**
     * Get values from the POST superglobal.
     *
     * @return value in the matching POST superglobal,
     * if it's not set return default value
     */
    public static function post($str, $default = 0) {
        return isset($_POST[$str]) ? $_POST[$str] : $default;
    }

    /**
     * Get values from the GET superglobal.
     *
     * @return value in the matching GET superglobal,
     * if it's not set return default value
     */
    public static function get($str, $default = 0) {
        return isset($_GET[$str]) ? $_GET[$str] : $default;
    }
}
