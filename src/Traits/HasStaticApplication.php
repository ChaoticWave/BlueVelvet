<?php namespace ChaoticWave\BlueVelvet\Traits;

/**
 * Provides access to the parent application. Also fulfills the ServiceLike contract
 */
trait HasStaticApplication
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected static $app;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    public static function getApplication()
    {
        return static::$app;
    }

    /**
     * @param \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application $app
     */
    protected static function setApplication($app)
    {
        static::$app = $app;
    }
}
