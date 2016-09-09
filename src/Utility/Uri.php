<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * HTTP URL/URI helpers
 */
class Uri
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $uri       The uri to parse
     * @param bool   $normalize If true, uri will be normalized to a string
     *
     * @return array|string
     * @todo support query string parameters
     */
    public static function parse($uri, $normalize = false)
    {
        //  Don't parse empty uris
        if (empty($uri)) {
            return true;
        }

        //  Let PHP have a whack at it
        $_parts = parse_url($uri);

        //  Unparsable or missing host or path, we bail
        if (false === $_parts || !(isset($_parts['host']) || isset($_parts['path']))) {
            return false;
        }

        $_scheme = array_get($_parts, 'scheme', Scalar::boolval(array_get($_SERVER, 'HTTPS')) ? 'https' : 'http');

        $_port = array_get($_parts, 'port');
        $_host = array_get($_parts, 'host');
        $_path = array_get($_parts, 'path');

        //  Set ports to defaults for scheme if empty
        if (empty($_port)) {
            $_port = null;

            switch ($_parts['scheme']) {
                case 'http':
                    $_port = 80;
                    break;

                case 'https':
                    $_port = 443;
                    break;
            }
        }

        //  If standard port 80 or 443 and there is no port in uri, clear from parse...
        if (!empty($_port)) {
            if (empty($_host) || (($_port == 80 || $_port == 443) && false === strpos($uri, ':' . $_port))) {
                $_port = null;
            }
        }

        if (!empty($_path) && empty($_host)) {
            //	Special case, handle this generically later
            if ('null' == $_path) {
                return 'null';
            }

            $_host = $_path;
            $_path = null;
        }

        $_uri = [
            'scheme' => $_scheme,
            'host'   => $_host,
            'port'   => $_port,
        ];

        return $normalize ? static::normalize($_uri) : $_uri;
    }

    /**
     * @param array $parts Parts of an uri
     *
     * @return string
     */
    public static function normalize(array $parts)
    {
        $_uri = $parts['scheme'] . '://' . $parts['host'];

        if (!empty($parts['port'])) {
            $_uri .= ':' . $parts['port'];
        }

        return trim($_uri);
    }

    /**
     * @param array $first  uri to compare
     * @param array $second uri to compare
     *
     * @return bool true if they are the same, false otherwise
     */
    public static function compare(array $first, array $second)
    {
        $_diff = array_diff_assoc($first, $second);

        return empty($_diff);
    }

    /**
     * Concatenate $parts into $separator delimited, trimmed, clean string.
     *
     * @param string|array $parts     The part or parts to join
     * @param bool         $leading   If true (default), a leading $separator will be added
     * @param string       $separator The delimiter to use
     *
     * @return null|string
     */
    public static function segment($parts = [], $leading = true, $separator = '/')
    {
        return Disk::segment($parts, $leading, $separator);
    }

    /**
     * Add a query string to ANY url
     *
     * @param string       $url   The URI to adjust
     * @param string|array $key   The query parameter key or an array of key/value pairs
     * @param mixed|null   $value The query parameter value
     *
     * @return string The url with the parameter added to the end
     */
    public static function addUrlParameter($url, $key, $value = null)
    {
        list($_url, $_params) = static::splitUrl($url);

        if (!is_array($key)) {
            $key = [$key => $value];
        }

        $_query = null;

        foreach ($key as $_key => $_value) {
            $_query[] = $_key . '=' . $_value;
        }

        return $_url . ($_query ? '?' . implode('&', $_query) : null);
    }

    /**
     * Splits an url into the address and query string portions
     *
     * @param string $url
     *
     * @return array An array of $url and $pairs [ 0 => 'url', 1 => [ 'key1' => 'value1', ...] ]
     */
    public static function splitUrl($url)
    {
        if (false === ($_pos = strpos($url, '?'))) {
            return [$url, []];
        }

        $_parts = explode('?', $url);

        if (count($_parts) < 2) {
            return [$url, []];
        }

        return [$_parts[0], static::parseQueryString($_parts[1])];
    }

    /**
     * Splits up query string key/value pairs
     *
     * @param string $query
     *
     * @return array
     */
    public static function parseQueryString($query)
    {
        $_result = [];

        if (!empty($_pairs = explode('&', trim($query, ' ?&')))) {
            foreach ($_pairs as $_pair) {
                $_tuple = explode('=', $_pair);
                if (count($_tuple)) {
                    $_result[array_get($_tuple, 0)] = array_get($_tuple, 1);
                }
            }
        }

        return $_result;
    }
}
