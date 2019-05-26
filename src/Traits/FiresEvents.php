<?php namespace ChaoticWave\BlueVelvet\Traits;

use ChaoticWave\BlueVelvent\Events\GenericEvent;
use Exception;
use Illuminate\Support\Str;

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
     * @param string|null $name The name to use for the event prefix
     *
     * @return $this
     */
    protected function initializeEvents($name = null)
    {
        $_class = class_basename($_fullClass = get_called_class());

        if (empty($name)) {
            $_constant = $_fullClass . '::NAME';

            if (method_exists($this->app, 'getId')) {
                $name = $this->app->getId();
            } elseif (defined($_constant)) {
                $name = constant($_constant);
            }
        }

		$_module = Str::snake(str_ireplace([$name, 'Service', 'Provider'], null, $_class));

        return $this->setEventPrefix(implode('.', [$name, $_module]));
    }

    /**
     * Generate an application event. $name is prefixed with module event prefix
     *
     * @param string $name    The event name
     * @param array  $payload The payload
     *
     * @return array|null|boolean
     */
	protected function fireEvent($name, $payload = [])
    {
        try {
            if (!empty($this->__fePrefix)) {
                $name = trim($this->__fePrefix, ' .') . '.' . ltrim($name, ' .');
            }

			return event(new GenericEvent($name, $payload));
		} catch (Exception $_ex) {
            //  Return false on this exception to halt propagation
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
