<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * Contains helpers to include/require PHP files
 */
class Includer
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Convenience access method for "include()"
     *
     * @param array|string $file
     * @param bool         $once
     * @param array|bool   $extract
     *
     * @return mixed
     */
    public static function includeFile($file, $once = true, $extract = false)
    {
        return static::includeIfExists($file, false, $once, $extract);
    }

    /**
     * Convenience access method for "require()"
     *
     * @param array|string $file    An absolute file name or an array of parts to assemble into one
     * @param bool         $once    If true, include|require_once() will be used instead of include|require()
     * @param array|bool   $extract If true, an array of variables defined in $file will be returned
     *
     * @return mixed
     */
    public static function requireFile($file, $once = true, $extract = false)
    {
        return static::includeIfExists($file, true, $once, $extract);
    }

    /**
     * Requires a file only if it exists
     *
     * @param array|string $file    An absolute file name or an array of parts to assemble into one
     * @param bool         $require use "require" instead of "include"
     * @param bool         $once    use "include_once" or "require_once" if $require is true
     * @param bool         $extract If true, any declarations in the included file are returned
     *
     * @return bool|mixed
     */
    public static function includeIfExists($file, $require = false, $once = false, $extract = false)
    {
        return static::includeFileExtract($file, $require, $once, $extract);
    }

    /**
     * Looks for file in the
     *
     * @param array|string $filename An absolute OR relative (to app.base_path) file name or an array of parts to assemble into one
     * @param bool         $require  require vs. include
     * @param bool         $once     require_once vs. include_once
     * @param bool         $extract  If true, any declarations in the included file are returned
     *
     * @return array|bool|mixed The extracted variables from the include file. Include/require return otherwise
     */
    public static function includeFileExtract($filename, $require = false, $once = false, $extract = true)
    {
        $_result = null;

        if (!is_readable($_file = Disk::path($filename))) {
            if (!function_exists('base_path')) {
                return false;
            }

            //  See if the file exists in the root or in config/
            $_file = base_path($filename);

            if (!is_readable($_file)) {
                if (!is_readable($_file = base_path('config/' . $filename))) {
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
