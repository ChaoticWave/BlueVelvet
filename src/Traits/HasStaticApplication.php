<?php namespace ChaoticWave\BlueVelvet\Utility\Traits;

/**
 * Provides access to the parent application. Also fulfills the ServiceLike contract
 */
trait HasStaticApplication
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var \ChaoticWave\BlueVelvet\Utility\Containers\BaseModule|\Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected static $app;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return \ChaoticWave\BlueVelvet\Utility\Containers\BaseModule|\Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    public static function getApplication()
    {
        return static::$app;
    }

    /**
     * @param \ChaoticWave\BlueVelvet\Utility\Containers\BaseModule|\Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application $app
     */
    protected static function setApplication($app)
    {
        static::$app = $app;
    }
}
