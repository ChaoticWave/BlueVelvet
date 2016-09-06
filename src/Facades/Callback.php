<?php namespace ChaoticWave\BlueVelvet\Facades;

use ChaoticWave\BlueVelvet\Providers\CallbackServiceProvider;

/**
 * @see \ChaoticWave\BlueVelvet\Services\CallbackService
 *
 * @method static void register($id, $callback)
 * @method static array fire($id, $key = null, array $arguments = []);
 */
class Callback extends BaseFacade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CallbackServiceProvider::ALIAS;
    }
}
