<?php namespace ChaoticWave\BlueVelvet\Traits;

use ChaoticWave\BlueVelvet\Utility\Curl;

/**
 * Add savory CURL abilities to any class with Curly! The best stooge ever!
 */
trait Curly
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $url     The url to call
     * @param array  $query   Any query string parameters in key/value format
     * @param array  $options Any CURL options for the request
     *
     * @return bool|mixed|\stdClass
     */
    protected function httpOptions($url, $query = [], $options = [])
    {
        return Curl::options($url, $query, $options);
    }

    /**
     * @param string $url     The url to call
     * @param array  $query   Any query string parameters in key/value format
     * @param array  $options Any CURL options for the request
     *
     * @return bool|mixed|\stdClass
     */
    protected function httpHead($url, $query = [], $options = [])
    {
        return Curl::head($url, $query, $options);
    }

    /**
     * @param string $url     The url to call
     * @param array  $query   Any query string parameters in key/value format
     * @param array  $options Any CURL options for the request
     *
     * @return \stdClass|string
     */
    protected function httpGet($url, $query = [], $options = [])
    {
        return Curl::get($url, $query, $options);
    }

    /**
     * @param string $url     The url to call
     * @param array  $payload The call's payload
     * @param array  $options Any CURL options for the request
     *
     * @return bool|mixed|\stdClass
     */
    protected function httpPost($url, $payload = [], $options = [])
    {
        return Curl::post($url, $payload, $options);
    }

    /**
     * @param string $url     The url to call
     * @param array  $payload The call's payload
     * @param array  $options Any CURL options for the request
     *
     * @return bool|mixed|\stdClass
     */
    protected function httpPut($url, $payload = [], $options = [])
    {
        return Curl::put($url, $payload, $options);
    }

    /**
     * @param string $url     The url to call
     * @param array  $payload The call's payload
     * @param array  $options Any CURL options for the request
     *
     * @return bool|mixed|\stdClass
     */
    protected function httpDelete($url, $payload = [], $options = [])
    {
        return Curl::delete($url, $payload, $options);
    }

    /**
     * @param string $url     The url to call
     * @param array  $payload The call's payload
     * @param array  $options Any CURL options for the request
     *
     * @return bool|mixed|\stdClass
     */
    protected function httpPatch($url, $payload = [], $options = [])
    {
        return Curl::patch($url, $payload, $options);
    }

    /**
     * @param string $method  The method
     * @param string $url     The url to call
     * @param array  $payload The call's payload
     * @param array  $options Any CURL options for the request
     *
     * @return mixed
     */
    protected function httpAny($method, $url, $payload = [], $options = [])
    {
        return Curl::request($method, $url, $payload, $options);
    }
}
