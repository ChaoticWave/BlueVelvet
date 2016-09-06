<?php namespace ChaoticWave\BlueVelvet\Providers;

use ChaoticWave\BlueVelvet\Services\CallbackService;

class CallbackServiceProvider extends BaseServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const ALIAS = 'callback-service';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $defer = true;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function register()
    {
        $this->singleton(static::ALIAS,
            function($app) {
                return new CallbackService($app);
            });
    }
}
