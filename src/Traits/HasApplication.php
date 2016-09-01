<?php namespace ChaoticWave\BlueVelvet\Utility\Traits;

/**
 * Provides access to the parent application. Also fulfills the ServiceLike contract
 */
trait HasApplication
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var \ChaoticWave\BlueVelvet\Utility\Containers\BaseModule|\Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return \ChaoticWave\BlueVelvet\Utility\Containers\BaseModule|\Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * @param \ChaoticWave\BlueVelvet\Utility\Containers\BaseModule|\Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application $app
     *
     * @return $this
     */
    protected function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }
}
