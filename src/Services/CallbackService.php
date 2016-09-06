<?php namespace ChaoticWave\BlueVelvet\Services;

use ChaoticWave\BlueVelvet\Contracts\HandlesCallbacks;

/**
 * A simple callback registrar
 */
class CallbackService extends BaseService implements HandlesCallbacks
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var array Registered callbacks
     */
    protected $callbacks;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function register($id, $callback)
    {
        if (!($callback instanceof \Closure) && !is_callable($callback)) {
            throw new \InvalidArgumentException('The $callback argument is not callable.');
        }

        $_closure = function() use ($callback) {
            $_args = func_get_args();

            return call_user_func_array($callback, $_args);
        };

        array_set($this->callbacks, $id, $_closure);
    }

    /** @inheritdoc */
    public function fire($id, $arguments = [])
    {
        $_results = [];

        /** @var callable[] $_closures */
        $_closures = array_get($this->callbacks, $id, []);

        foreach ($_closures as $_callback) {
            try {
                $_results[] = $this->apply($_callback, $arguments);
            } catch (\Exception $_ex) {
                $_results[] = ['id' => $id, 'arguments' => $arguments, 'callback' => is_string($_callback) ? $_callback : '<closure>', 'exception' => $_ex];
            }
        }

        return $_results;
    }

    /**
     * @param callable $callable
     * @param array    $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    protected function apply($callable, array $arguments = [])
    {
        try {
            return call_user_func_array($callable, $arguments);
        } catch (\Exception $_ex) {
            $this->logError($_ex);

            throw $_ex;
        }
    }
}
