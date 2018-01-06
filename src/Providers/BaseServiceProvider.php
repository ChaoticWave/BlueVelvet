<?php namespace ChaoticWave\BlueVelvet\Providers;

use ChaoticWave\BlueVelvet\Services\BaseService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * A base class for laravel 5.2+ service providers
 */
abstract class BaseServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of the service in the IoC
     */
    const ALIAS = false;

    //********************************************************************************
    //* Public Methods
    //********************************************************************************

    /**
     * Called after construction
     */
    public function boot()
    {
        if (is_callable('parent::boot')) {
            parent::boot();
        }
    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string               $abstract
     * @param  \Closure|string|null $concrete
     *
     * @return void
     */
    public function singleton($abstract, $concrete)
    {
        //  Register object into instance container
        $this->app->singleton($abstract ?: static::ALIAS, $concrete);
    }

    /**
     * Register a binding with the container.
     *
     * @param  string|array         $abstract
     * @param  \Closure|string|null $concrete
     * @param  bool                 $shared
     *
     * @return void
     */
    public function bind($abstract, $concrete, $shared = false)
    {
        //  Register object into instance container
        $this->app->bind($abstract ?: static::ALIAS, $concrete, $shared);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return static::ALIAS ? array_merge(parent::provides(), [static::ALIAS,]) : parent::provides();
    }

    /**
     * Returns the service configuration either based on class name or argument name. Override method to provide custom configurations
     *
     * @param string|null $name
     * @param array       $default
     *
     * @return array
     */
    public static function getServiceConfig($name = null, $default = [])
    {
        if (empty($_key = $name)) {
            $_mirror = new \ReflectionClass(get_called_class());
            $_key = snake_case(str_ireplace(['ServiceProvider', 'Provider'], null, $_mirror->getShortName()));
            unset($_mirror);
        }

        return config($_key, $default);
    }

    /**
     * @return string Returns this provider's IoC name
     */
    public function __invoke()
    {
        return static::ALIAS ?: null;
    }

    /**
     * @param Application|null $app
     *
     * @return mixed|BaseService
     */
    public static function make(Application $app = null)
    {
        if (false === static::ALIAS) {
            throw new \RuntimeException('ALIAS not defined. Cannot invoke service.');
        }

        //  Grab the arguments, strip off $app
        $_params = func_get_args();
        array_shift($_params);
        $_params = empty($_params) ? [] : $_params;

        //  Make the service
        return $app ? $app->make(static::ALIAS, $_params) : app(static::ALIAS, $_params);
    }

    /**
     * Register a view file namespace.
     *
     * @param  string $path
     * @param  string $namespace
     */
    protected function loadViewsFrom($path, $namespace)
    {
        //  Make sure we have a resource path when using lumen
        if ($this->app && method_exists($this->app, 'resourcePath')) {
            parent::loadViewsFrom($path, $namespace);
        }
    }
}
