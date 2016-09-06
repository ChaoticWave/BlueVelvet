<?php namespace ChaoticWave\BlueVelvet\Facades;

use ChaoticWave\BlueVelvet\Services\BaseService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Facade;

class BaseFacade extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Returns the/an instance of the service this facade covers
     *
     * @param \Illuminate\Contracts\Foundation\Application|null $app
     *
     * @return mixed|BaseService
     */
    public static function service(Application $app = null)
    {
        return $app ? $app->make(static::getFacadeAccessor()) : app(static::getFacadeAccessor());
    }
}
