<?php namespace ChaoticWave\BlueVelvet\Enums;

/**
 * Ways characters can be escaped
 */
class Escapes extends BaseEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var int No escape!
     */
    const NONE = 0;
    /**
     * @var int Escaped with a double character (i.e. '')
     */
    const DOUBLED = 1;
    /**
     * @var int Escaped with a backslash (i.e. \')
     */
    const SLASHED = 2;
}
