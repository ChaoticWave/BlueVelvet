<?php namespace ChaoticWave\BlueVelvet\Utility\Services;

use ChaoticWave\BlueVelvet\Utility\Contracts\ServiceLike;
use ChaoticWave\BlueVelvet\Utility\Traits\HasApplication;
use ChaoticWave\BlueVelvet\Utility\Traits\HasAppLogger;

/**
 * A base class for all services
 *
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
    }
}
