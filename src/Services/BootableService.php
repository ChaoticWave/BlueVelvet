<?php namespace ChaoticWave\BlueVelvet\Services;

use ChaoticWave\BlueVelvet\Contracts\Bootable;
use ChaoticWave\BlueVelvet\Traits\FiresEvents;

/**
 * A base class for bootable services
 * They're $app-aware and have an event system and a logger
 */
abstract class BootableService extends BaseService implements Bootable
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use FiresEvents;
}
