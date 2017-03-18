<?php namespace ChaoticWave\BlueVelvet\Traits;

/**
 * Include/exclude objects that are on/not on a list
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
     * @param array|null  $includes
     * @param array|null  $excludes
     * @param string|null $wildcard
     *
     * @return \ChaoticWave\BlueVelvet\Traits\IncludeExclude
     */
    protected function setupIncludeExclude($includes = null, $excludes = null, $wildcard = null)
    {
        $this->_ieWildCharacter = $wildcard ?: '*';

        //  Load 'em up!
        $this->_ieInclude = $includes;
        $this->_ieExclude = $excludes;

        return $this;
    }

    /**
     * Parses conditionals and returns an array of closure handlers
     *
     * @param array $array
     *
     * @return array
     */
    protected static function parseConditions(&$array = [])
    {
        $_conditions = [];

        foreach ($array as $_key => $_needle) {
            if (!is_string($_key) && is_numeric($_key)) {
                continue;
            }

            switch ($_key) {
                case 'starts-with':
                case 'ends-with':
                case 'contains':
                    $_conditions[$_key][] = $_needle;
                    break;

                // default:
                //     throw new \RuntimeException('Invalid condition "' . $_key . '" found');
            }
        }

        return $_conditions;
    }

    /**
     * @param string $value
     * @param bool   $include TRUE = includes, FALSE = excludes
     *
     * @return bool Returns true if $value matches one or more conditions, FALSE otherwise
     */
    protected function isValueConditional($value, $include = true)
    {
        //  JIT/Late-bound initialization
        if (null === $this->_ieIncludeConditions || null === $this->_ieExcludeConditions) {
            $this->_ieIncludeConditions = static::parseConditions($this->_ieInclude);
            $this->_ieExcludeConditions = static::parseConditions($this->_ieExclude);
        }

        //  No conditions, we're done.
        if (null === ($_conditions = ($include ? $this->_ieIncludeConditions : $this->_ieExcludeConditions)) || empty($_conditions)) {
            return false;
        }

        //  Call each of the closures registered. They return true if matched
        foreach ($_conditions as $_type => $_needles) {
            switch ($_type) {
                case 'starts-with':
                    if (starts_with($value, $_needles)) {
                        return true;
                    }
                    break;

                case 'ends-with':
                    if (ends_with($value, $_needles)) {
                        return true;
                    }
                    break;

                case 'contains':
                    if (str_contains($value, $_needles)) {
                        return true;
                    }
                    break;
            }
        }

        return false;
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
        static $cache = [];
        $_cacheKey = md5(json_encode(['contains' => [$haystack, $needles]]));

        return $cache[$_cacheKey] = array_get($cache, $_cacheKey) ?: str_contains($haystack, $needles);
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
        static $cache = [];
        $_cacheKey = md5(json_encode(['starts-with' => [$haystack, $needles]]));

        return $cache[$_cacheKey] = array_get($cache, $_cacheKey) ?: starts_with($haystack, $needles);
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
        static $cache = [];
        $_cacheKey = md5(json_encode(['ends-with' => [$haystack, $needles]]));

        return $cache[$_cacheKey] = array_get($cache, $_cacheKey) ?: ends_with($haystack, $needles);
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
}
