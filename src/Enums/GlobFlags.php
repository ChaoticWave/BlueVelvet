<?php namespace ChaoticWave\BlueVelvet\Enums;

/**
 * GlobFlags
 * Ya know, for globbing...
 */
class GlobFlags extends BaseEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @type int
     */
    const GLOB_NODIR = 0x0100;
    /**
     * @type int
     */
    const GLOB_PATH = 0x0200;
    /**
     * @type int
     */
    const GLOB_NODOTS = 0x0400;
    /**
     * @type int
     */
    const GLOB_RECURSE = 0x0800;
    /**
     * @type int
     */
    const NODIR = 0x0100;
    /**
     * @type int
     */
    const PATH = 0x0200;
    /**
     * @type int
     */
    const NODOTS = 0x0400;
    /**
     * @type int
     */
    const RECURSE = 0x0800;
}
