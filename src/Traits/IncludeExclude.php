<?php namespace ChaoticWave\BlueVelvet\Traits;

/**
 * Include/exclude objects that are on/not on a list
 * New conditions may be added by defining a new conditionType method and adding it to $_ieConditionals
 */
trait IncludeExclude
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @var array The included list
     */
    private $_ieInclude;
    /**
     * @var array The excluded list
     */
    private $_ieExclude;
    /**
     * @var array Conditional inclusion rules
     */
    private $_ieIncludeConditions;
    /**
     * @var array Conditional exclusion rules
     */
    private $_ieExcludeConditions;
    /**
     * @var string The wildcard character. Defaults to '*'
     */
    private $_ieWildCharacter = '*';
    /**
     * @var bool If true, comparisons are case-sensitive. This is the default
     */
    private $_ieCaseSensitive = true;
    /**
     * @var string The reason for the last exclusion
     */
    private $_ieExcludeReason;
    /**
     * @var array The supported conditionals
     */
    private $_ieConditionals = ['starts-with', 'ends-with', 'contains'];
    /**
     * @var array A cache of handled conditions
     */
    private $_ieConditionCache;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Test if a value is included or wildcarded, but not excluded
     *
     * @param string $value The value to check
     *
     * @return bool
     */
    protected function isValueIncluded($value)
    {
        return !$this->isValueExcluded($value) && $this->_ieCheckValue($value);
    }

    /**
     * @param string $value The value to check
     *
     * @return bool
     */
    protected function isValueExcluded($value)
    {
        //  Explicitly excluded?
        return $this->_ieCheckValue($value, false);
    }

    /**
     * @param string $value
     * @param bool   $include TRUE = includes, FALSE = excludes
     *
     * @return bool Returns true if $value matches one or more conditions, FALSE otherwise
     */
    protected function isValueConditional($value, $include = true)
    {
        //  Only handle scalar in here
        if (!is_scalar($value)) {
            return false;
        }

        //  Get conditions, if none, no match
        if (false !== ($_conditions = $this->getIncludeExcludeConditions($include))) {
            foreach ($_conditions as $_type => $_needles) {
                if (method_exists($this, $_method = 'condition' . studly_case($_type))) {
                    if (call_user_func([$this, $_method], $value, $_needles)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Test if a wildcard is defined in the in/exclusions
     *
     * @param bool $included TRUE = includes, FALSE = excludes
     *
     * @return bool
     */
    protected function isWildcardDefined($included = true)
    {
        $_check = $included ? $this->_ieInclude : $this->_ieExclude;

        return $this->_ieWildCharacter === $_check || static::inArray($this->_ieWildCharacter, $_check, $this->_ieCaseSensitive);
    }

    /**
     * @param array|null  $includes
     * @param array|null  $excludes
     * @param string|null $wildcard
     *
     * @return \ChaoticWave\BlueVelvet\Traits\IncludeExclude
     */
    protected function setupIncludeExclude($includes = null, $excludes = null, $wildcard = null)
    {
        $this->_ieConditionCache = [];
        $this->_ieWildCharacter = $wildcard ?: '*';

        //  Load 'em up!
        $this->_ieInclude = $includes;
        $this->_ieExclude = $excludes;

        return $this;
    }

    /**
     * Filters an associative array using the key against the include/exclude list
     *
     * @param array    $data     The data to filter
     * @param bool     $included TRUE = includes, FALSE = excludes
     * @param \Closure $callback
     *
     * @return array
     */
    protected function filteredValues($data = [], $included = true, $callback = null)
    {
        return array_where($this->deepFilter($data, $this->getExcludedValues()),
            function($key, $value) use ($included, $callback) {
                $_result = $included ? $this->isValueIncluded($key) : $this->isValueExcluded($key);

                if (!$_result || !$callback) {
                    return $_result;
                }

                return call_user_func($callback, $key, $value, $included);
            });
    }

    /**
     * Deeply filter an array removing keys from the array $keys
     *
     * @param array $array
     * @param array $keys
     * @param bool  $caseSensitive
     *
     * @return array The filtered array
     */
    public static function deepFilter($array = [], $keys = [], $caseSensitive = true)
    {
        $_filtered = [];

        foreach ($array as $_key => $_value) {
            if (!static::inArray($_key, $keys, $caseSensitive)) {
                continue;
            }

            $_filtered[$_key] = is_scalar($_value) ? $_value : static::deepFilter($_value, $keys, $caseSensitive);
        }

        return $_filtered;
    }

    /**
     * Checks if $value is in $array values. Case-sensitive if need be
     *
     * @param string $value
     * @param array  $array
     * @param bool   $caseSensitive
     *
     * @return bool
     */
    public static function inArray($value, $array, $caseSensitive = true)
    {
        if ($caseSensitive) {
            return in_array($value, $array);
        }

        foreach ($array as $_value) {
            if (0 === strcasecmp($value, $_value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getIncludedValues()
    {
        return $this->_ieInclude;
    }

    /**
     * @return array
     */
    protected function getIncludedConditions()
    {
        return $this->_ieIncludeConditions;
    }

    /**
     * @return array
     */
    protected function getExcludedValues()
    {
        return $this->_ieExclude;
    }

    /**
     * @return array
     */
    protected function getExcludedConditions()
    {
        return $this->_ieExcludeConditions;
    }

    /**
     * @return string
     */
    protected function getWildCharacter()
    {
        return $this->_ieWildCharacter;
    }

    /**
     * @param string $ieWildCharacter
     *
     * @return IncludeExclude
     */
    protected function setWildCharacter($ieWildCharacter)
    {
        $this->_ieWildCharacter = $ieWildCharacter;

        return $this;
    }

    /**
     * @return boolean
     */
    protected function getCaseSensitive()
    {
        return $this->_ieCaseSensitive;
    }

    /**
     * @param boolean $caseSensitive
     *
     * @return IncludeExclude
     */
    protected function setCaseSensitive($caseSensitive)
    {
        $this->_ieCaseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * @return string
     */
    protected function getExcludeReason()
    {
        return $this->_ieExcludeReason;
    }

    /**
     * Check only if $value is included/excluded
     *
     * @param string $value
     * @param bool   $include
     *
     * @return bool
     */
    protected function _ieCheckValue($value, $include = true)
    {
        //  Conditions?
        $_check = $include ? $this->_ieInclude : $this->_ieExclude;

        if (static::inArray($value, $_check, $this->_ieCaseSensitive)) {
            $this->_ieExcludeReason = 'inArray:[' . $value . ']';

            return true;
        }

        //  Conditional
        if ($this->isValueConditional($value, $include)) {
            $this->_ieExcludeReason = 'conditional:[' . $value . ']';

            return true;
        }

        //  Then default to wildcard, if defined
        if ($this->isWildcardDefined($include)) {
            $this->_ieExcludeReason = 'wildcard:[' . $value . ']';

            return true;
        }

        return false;
    }

    /**
     * Returns the include/exclude conditions, initializing them if they haven't been.
     *
     * @param bool $include If true, include conditions are returned, otherwise exclude conditions
     *
     * @return array|bool
     */
    protected function getIncludeExcludeConditions($include = true)
    {
        if (null === $this->_ieIncludeConditions && !empty($this->_ieInclude)) {
            $this->_ieIncludeConditions = $this->parseConditions($this->_ieInclude);
        }

        if (null === $this->_ieExcludeConditions && !empty($this->_ieExclude)) {
            $this->_ieExcludeConditions = $this->parseConditions($this->_ieExclude);
        }

        $_conditions = $include ? $this->_ieIncludeConditions : $this->_ieExcludeConditions;

        return empty($_conditions) ? false : $_conditions;
    }

    //******************************************************************************
    //* Methods for Conditional Handling
    //******************************************************************************

    /**
     * Parses conditionals and returns an array of closure handlers
     *
     * @param array $array
     *
     * @return array
     */
    protected function parseConditions(&$array = [])
    {
        $_conditions = [];

        //  If we have supported conditionals, add them to the conditions
        foreach ($array as $_key => $_needle) {
            if (is_string($_key) && in_array($_key, $this->_ieConditionals)) {
                if (!is_array($_needle)) {
                    $_needle = [$_needle];
                }

                foreach ($_needle as $_thing) {
                    $_conditions[$_key][] = $_thing;
                }
            }
        }

        return $_conditions;
    }

    /**
     * Caching conditional processor: contains
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    protected function conditionContains($haystack, $needles)
    {
        if (!$this->getCachedCondition('contains', [$haystack, $needles])) {
            return $this->putCachedCondition('contains', [$haystack, $needles], str_contains($haystack, $needles));
        }

        return true;
    }

    /**
     * Caching conditional processor: starts-with
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    protected function conditionStartsWith($haystack, $needles)
    {
        if (!$this->getCachedCondition('starts-with', [$haystack, $needles])) {
            return $this->putCachedCondition('starts-with', [$haystack, $needles], starts_with($haystack, $needles));
        }

        return true;
    }

    /**
     * Caching conditional processor: ends-with
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    protected function conditionEndsWith($haystack, $needles)
    {
        if (!$this->getCachedCondition('ends-with', [$haystack, $needles])) {
            return $this->putCachedCondition('ends-with', [$haystack, $needles], ends_with($haystack, $needles));
        }

        return true;
    }

    /**
     * Hashes and stores a conditional result
     *
     * @param string $tag The method or condition name
     * @param array  $key The payload cached
     *
     * @return mixed
     */
    protected function getCachedCondition($tag, $key)
    {
        return array_get($this->_ieConditionCache, md5(json_encode([$tag => $key])), false);
    }

    /**
     * Retrieves a cached conditional result
     *
     * @param string $tag The method or condition name
     * @param array  $key The payload cached
     * @param mixed  $value
     *
     * @return mixed|null Returns $value unmodified for returning
     */
    protected function putCachedCondition($tag, $key, $value = null)
    {
        $this->_ieConditionCache[md5(json_encode([$tag => $key]))] = $value;

        return $value;
    }
}
