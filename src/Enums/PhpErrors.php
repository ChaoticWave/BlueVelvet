<?php namespace ChaoticWave\BlueVelvet\Enums;

/**
 * A placeholder for PHP error strings
 */
class PhpErrors extends BaseEnum
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Given a PHP error_reporting value, spit back the error constant.
     *
     * @param int $type
     *
     * @return string
     */
    public function getErrorName($type)
    {
        $_errorType = 'E_UNKNOWN';

        switch ($type) {
            case E_ERROR:
                $_errorType = 'E_ERROR';
                break;

            case E_WARNING:
                $_errorType = 'E_WARNING';
                break;

            case E_PARSE:
                $_errorType = 'E_PARSE';
                break;

            case E_NOTICE:
                $_errorType = 'E_NOTICE';
                break;

            case E_CORE_ERROR:
                $_errorType = 'E_CORE_ERROR';
                break;

            case E_CORE_WARNING:
                $_errorType = 'E_CORE_WARNING';
                break;

            case E_COMPILE_ERROR:
                $_errorType = 'E_COMPILE_ERROR';
                break;

            case E_COMPILE_WARNING:
                $_errorType = 'E_COMPILE_WARNING';
                break;

            case E_USER_ERROR:
                $_errorType = 'E_USER_ERROR';
                break;

            case E_USER_WARNING:
                $_errorType = 'E_USER_WARNING';
                break;

            case E_USER_NOTICE:
                $_errorType = 'E_USER_NOTICE';
                break;

            case E_STRICT:
                $_errorType = 'E_STRICT';
                break;

            case E_RECOVERABLE_ERROR:
                $_errorType = 'E_RECOVERABLE_ERROR';
                break;

            case E_DEPRECATED:
                $_errorType = 'E_DEPRECATED';
                break;

            case E_USER_DEPRECATED:
                $_errorType = 'E_USER_DEPRECATED';
                break;

            case E_ALL:
                $_errorType = 'E_ALL';
                break;
        }

        return $_errorType;
    }
}
