<?php namespace ChaoticWave\BlueVelvet\Contracts;

/**
 * Something that can be booted
 */
interface Bootable
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Boot the thing
     *
     * @return mixed
     */
    public function boot();
}
