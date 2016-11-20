<?php namespace ChaoticWave\BlueVelvet\Services;

use ChaoticWave\BlueVelvet\Contracts\Bootable;
use ChaoticWave\BlueVelvet\Contracts\ServiceLike;
use ChaoticWave\BlueVelvet\Traits\HasApplication;
use ChaoticWave\BlueVelvet\Traits\HasAppLogger;

/**
 * A base class for all services
 * Holds the main $app and sets up logging
 */
class BaseService implements ServiceLike
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use HasApplication, HasAppLogger;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Constructor
     *
     * @param \Laravel\Lumen\Application|\Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->setApplication($app);

        //  If this is a bootable service, call its boot() method
        if ($this instanceof Bootable) {
            $this->boot();
        }
    }
}
