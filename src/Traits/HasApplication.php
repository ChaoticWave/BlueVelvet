<?php namespace ChaoticWave\BlueVelvet\Traits;

/**
 * Provides access to the parent application. Also fulfills the ServiceLike contract
 */
trait HasApplication
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * @param \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application $app
     *
     * @return $this
     */
    protected function setApplication($app)
    {
        $this->app = $app;

        return $this;
    }
}
