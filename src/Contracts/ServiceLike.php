<?php namespace ChaoticWave\BlueVelvet\Utility\Contracts;

use Illuminate\Contracts\Foundation\Application;

/**
 * Something that acts like an application service
 */
interface ServiceLike
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Returns the current application instance
     *
     * @return Application
     */
    public function getApplication();
}
