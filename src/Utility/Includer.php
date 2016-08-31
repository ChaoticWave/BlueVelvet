<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * Contains helpers to include/require PHP files
 */
class Includer
{
    /**
     * Requires a file only if it exists
     *
     * @param string $file    the absolute /path/to/file.php
     * @param bool   $require use "require" instead of "include"
     * @param bool   $once    use "include_once" or "require_once" if $require is true
     *
     * @return bool|mixed
     */
    public static function includeIfExists($file, $require = false, $once = false)
    {
        if (file_exists($file) && is_readable($file)) {
            /** @noinspection PhpIncludeInspection */
            return $require
                ? ($once ? require_once($file) : require($file))
                : ($once ? include_once($file)
                    : include($file));
        }

        return false;
    }

    /**
     * Looks for file in the
     *
     * @param string $filename The file name relative to the application root
     * @param bool   $require  require vs. include
     * @param bool   $once     require_once vs. include_once
     * @param bool   $extract  If true, any declarations in the included file are returned
     *
     * @return mixed The extracted variables from the include file. Include/require return otherwise
     */
    public static function includeFileExtract($filename, $require = false, $once = false, $extract = true)
    {
        $_result = null;

        if (!file_exists($_file = $filename)) {
            if (!function_exists('base_path')) {
                return false;
            }

            //  See if the file exists in the root or in config/
            $_file = base_path($filename);

            if (!file_exists($_file)) {
                if (!file_exists($_file = base_path('config/' . $filename))) {
                    return false;
                }
            }
        }

        if ($require) {
            /** @noinspection PhpIncludeInspection */
            $_result = $once ? require_once($_file) : require($_file);
        } else {
            /** @noinspection PhpIncludeInspection */
            $_result = $once ? include_once($_file) : include($_file);
        }

        if (!$extract) {
            return $_result;
        }

        return array_except(get_defined_vars(), ['_result', '_file', 'extract', 'require', 'filename', 'once']);
    }
}
