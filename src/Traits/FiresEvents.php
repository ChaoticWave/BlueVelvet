<?php namespace ChaoticWave\BlueVelvet\Traits;

/**
 * Adds an event firing method
 */
trait FiresEvents
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var string Optional event name prefix
     */
    private $__fePrefix;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Generate an application event. $name is prefixed with module event prefix
     *
     * @param string $name    The event name
     * @param array  $payload The payload
     * @param bool   $halt    Halt event propagation after first handler and return response.
     *
     * @return array|null|boolean
     */
    protected function fireEvent($name, $payload = [], $halt = false)
    {
        try {
            $_service = app('events');

            if (!empty($this->__fePrefix)) {
                $name = trim($this->__fePrefix, ' .') . '.' . ltrim($name, ' .');
            }

            return $_service->fire($name, $payload, $halt);
        } catch (\Exception $_ex) {
            return false;
        }
    }

    /**
     * @param string $prefix The prefix for event names (i.e. "blue-velvet", "my-app", etc.)
     *
     * @return $this
     */
    protected function setEventPrefix($prefix)
    {
        $this->__fePrefix = $prefix;

        return $this;
    }
}
