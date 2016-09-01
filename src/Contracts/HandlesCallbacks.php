<?php namespace ChaoticWave\BlueVelvet\Utility\Contracts;

/**
 * Something that handles callbacks
 */
interface HandlesCallbacks extends HandlerLike
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string   $id       The id of the callbacks to add
     * @param callable $callback The callback
     *
     * @return $this
     */
    public function register($id, $callback);

    /**
     * Fires all callbacks registered to $id
     *
     * @param string $id        The id of the callback to fore
     * @param array  $arguments Arguments to send to the callback
     *
     * @return array
     */
    public function fire($id, $arguments = []);
}
