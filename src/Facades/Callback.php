<?php namespace ChaoticWave\BlueVelvet\Utility\Facades;

use ChaoticWave\BlueVelvet\Utility\Providers\CallbackServiceProvider;

/**
 * @see \ChaoticWave\BlueVelvet\Utility\Services\CallbackService
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
