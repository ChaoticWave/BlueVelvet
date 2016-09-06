<?php namespace ChaoticWave\BlueVelvet\Utility;

use ChaoticWave\BlueVelvet\Traits\IncludeExclude;

/**
 * ListChecker
 */
class ListChecker
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use IncludeExclude;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Constructor
     *
     * @param array  $include  The include values
     * @param array  $exclude  The exclude values
     * @param string $wildcard The character to use for wildcards. Defaults to '*'
     */
    public function __construct($include = null, $exclude = null, $wildcard = null)
    {
        $this->setupIncludeExclude($include, $exclude, $wildcard);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function excluded($value)
    {
        return $this->isValueExcluded($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function included($value)
    {
        return $this->isValueIncluded($value);
    }

    /**
     * @return array
     */
    public function getIncludes()
    {
        return $this->getIncludedValues();
    }

    /**
     * @return array
     */
    public function getExcludes()
    {
        return $this->getExcludedValues();
    }

    /**
     * @param array    $data
     * @param bool     $included If true, filter against includes, otherwise excludes
     * @param \Closure $callback
     *
     * @return array
     */
    public function filtered($data = [], $included = true, $callback = null)
    {
        return $this->filteredValues($data, $included, $callback);
    }
}
