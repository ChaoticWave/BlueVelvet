<?php namespace ChaoticWave\BlueVelvet\Enums;

use Monolog\Logger;

/**
 * PHP error constant strings
 */
class PhpErrors extends BaseEnum
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Given a PHP error_reporting value, spit back the first matching error constant.
     *
     * @param int $type
     *
     * @return string
     */
    public static function getName($type)
    {
        switch ($type) {
            case E_ERROR:
                return 'E_ERROR';

            case E_WARNING:
                return 'E_WARNING';

            case E_PARSE:
                return 'E_PARSE';

            case E_NOTICE:
                return 'E_NOTICE';

            case E_CORE_ERROR:
                return 'E_CORE_ERROR';

            case E_CORE_WARNING:
                return 'E_CORE_WARNING';

            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';

            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';

            case E_USER_ERROR:
                return 'E_USER_ERROR';

            case E_USER_WARNING:
                return 'E_USER_WARNING';

            case E_USER_NOTICE:
                return 'E_USER_NOTICE';

            case E_STRICT:
                return 'E_STRICT';

            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';

            case E_DEPRECATED:
                return 'E_DEPRECATED';

            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';

            case E_ALL:
                return 'E_ALL';
        }

        return null;
    }

    /**
     * Given a PHP error_reporting value, spit back all matching error constants.
     *
     * @param int $type
     *
     * @return array|null
     */
    public static function getNames($type)
    {
        $_types = null;

        for ($_i = 0; $_i < 15; $_i++) {
            if (null !== ($_name = static::getName($type & pow(2, $_i)))) {
                $_types[] = $_name;
            }
        }

        return $_types;
    }

    /**
     * @param int  $error  The PHP error value
     * @param bool $string If true, returns the generalized level of the error. If false, the Monolog integer logging level is returned.
     *
     * @return int|string
     */
    public static function toLevel($error, $string = false)
    {
        $_name = static::getName($error);

        if (in_array($_name, [E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR, E_PARSE])) {
            return $string ? 'error' : Logger::ERROR;
        }

        if (in_array(static::getName($error), [E_WARNING, E_USER_WARNING, E_CORE_WARNING, E_COMPILE_WARNING])) {
            return $string ? 'warning' : Logger::WARNING;
        }

        if (in_array(static::getName($error), [E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED])) {
            return $string ? 'notice' : Logger::NOTICE;
        }

        //  Return an error because it's not one of the above?
        return $string ? $_name : Logger::ERROR;
    }
}