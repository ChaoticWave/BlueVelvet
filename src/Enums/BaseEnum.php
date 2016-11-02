<?php namespace ChaoticWave\BlueVelvet\Enums;

use ChaoticWave\BlueVelvet\Utility\Inflector;

/**
 * This is an enum utility class upon which to base your enum classes.
 */
abstract class BaseEnum
{
    //*************************************************************************
    //* Members
    //*************************************************************************

    /**
     * @var array The cache for quick lookups
     */
    protected static $constants = null;
    /**
     * @type array Optional aliases for constants
     */
    protected static $tags = null;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string      $class
     * @param bool        $overwrite
     * @param string|null $tag Additional cache tag
     *
     * @return string
     */
    public static function introspect($class = null, $overwrite = true, $tag = null)
    {
        $_key = static::_cacheKey($class) . ($tag ? '.' . $tag : null);

        if (true === $overwrite || !isset(static::$constants[$_key])) {
            $_mirror = new \ReflectionClass($class ?: \get_called_class());
            static::$constants[$_key] = $_mirror->getConstants();
            unset($_mirror);
        }

        return $_key;
    }

    /**
     * Gets a hashed cache key
     *
     * @param string $class
     *
     * @return string
     */
    protected static function _cacheKey($class = null)
    {
        static $_key = null;

        return $_key ?: sha1($class ?: get_called_class());
    }

    /**
     * Returns a hash of the called class's constants ( CONSTANT_NAME => value ). Caches for speed
     * (class cache hash, say that ten times fast!).
     *
     * @param bool   $flipped  If true, the array is flipped before return ( value => CONSTANT_NAME )
     * @param string $class    Used internally to cache constants
     * @param bool   $listData If true, the constant names themselves are cleaned up for display purposes. Ignores $flipped value.
     *
     * @return array
     */
    public static function getDefinedConstants($flipped = false, $class = null, $listData = false)
    {
        $_key = static::introspect($class, false, $flipped ? 'flipped' : null);

        if (false === $listData) {
            return false === $flipped ? static::$constants[$_key] : array_flip(static::$constants[$_key]);
        }

        $_temp = [];

        foreach (static::$constants[$_key] as $_constant => $_value) {
            $_temp[$_value] = Inflector::display(Inflector::neutralize($_constant));
            unset($_value, $_constant);
        }

        return $_temp;
    }

    /**
     * Alias of static::getDefinedConstants()
     *
     * @param bool   $flipped  If true, the array is flipped before return ( value => CONSTANT_NAME )
     * @param string $class    Used internally to cache constants
     * @param bool   $listData If true, the constant names themselves are cleaned up for display purposes.
     *
     * @return array
     */
    public static function all($flipped = false, $class = null, $listData = false)
    {
        return static::getDefinedConstants($flipped, $class, $listData);
    }

    /**
     * Given a VALUE, return the associated CONSTANT
     *
     * @param string $value
     *
     * @return string
     */
    public static function toConstant($value)
    {
        if (false !== ($_index = array_search($value, static::getDefinedConstants()))) {
            return $_index;
        }

        throw new \InvalidArgumentException('The value "' . $value . '" has no associated constant.');
    }

    /**
     * Given a CONSTANT, return the associated VALUE
     *
     * @param mixed $constant
     *
     * @return string
     */
    public static function toValue($constant)
    {
        if (false !== ($_index = array_search($constant, static::getDefinedConstants(true)))) {
            return $_index;
        }

        throw new \InvalidArgumentException('The constant "' . $constant . '" has no associated value.');
    }

    /**
     * Given a CONSTANT or VALUE, return the VALUE
     *
     * @param string|int $item
     *
     * @return mixed
     */
    public static function resolve($item)
    {
        try {
            return static::toConstant($item);
        } catch (\Exception $_ex) {
            //  Ignored...
        }

        try {
            return static::toValue($item);
        } catch (\Exception $_ex) {
            //  Ignored...
        }

        //  Sorry charlie...
        throw new \InvalidArgumentException('The item "' . $item . '" can not be resolved.');
    }

    /**
     * Returns constant name or true/false if class contains a specific constant value.
     *
     * Use for validity checks:
     *
     *    if ( false === VeryCoolShit::contains( $evenCoolerShit ) ) {
     *        throw new \InvalidArgumentException( 'Sorry, your selection of "' . $evenCoolerShit . '" is invalid.' );
     *    }
     *
     * @param mixed $value
     * @param bool  $returnConstant
     *
     * @return bool|mixed
     */
    public static function contains($value, $returnConstant = false)
    {
        try {
            $_key = static::toConstant($value);

            return $returnConstant ? $_key : true;
        } catch (\Exception $_ex) {
            return false;
        }
    }

    /**
     * Alias of contains()
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function has($value)
    {
        return static::contains($value);
    }

    /**
     * Returns true or false if this class defines a specific constant.
     * Optionally returns the value of the constant, but throws an
     * exception if not found.
     *
     * Use for validity checks:
     *
     *    if ( false === VeryCoolShit::contains( $evenCoolerShit ) ) {
     *        throw new \InvalidArgumentException( 'Sorry, your selection of "' . $evenCoolerShit . '" is invalid.' );
     *    }
     *
     * @param string $constant
     * @param bool   $returnValue If true, returns the value of the constant if found, but throws an exception if not
     *
     * @throws \InvalidArgumentException
     * @return bool|mixed
     */
    public static function defines($constant, $returnValue = false)
    {
        try {
            $_value = static::toValue($constant);

            return $returnValue ? $_value : true;
        } catch (\InvalidArgumentException $_ex) {
            if ($returnValue) {
                throw $_ex;
            }
        }

        return false;
    }

    /**
     * Returns the constant name as a string
     *
     * @param string|int $constant The CONSTANT's value that you want the name of
     * @param bool       $flipped  If false, $constant should be the CONSTANT's name. The CONSTANT's value will be
     *                             returned instead.
     * @param bool       $pretty   If true, returned value is prettified (acme.before_event becomes "Acme Before
     *                             Event")
     *
     * @throws \InvalidArgumentException
     * @return string|int
     */
    public static function nameOf($constant, $flipped = true, $pretty = true)
    {
        try {
            $_name = $flipped ? static::toValue($constant) : static::toConstant($constant);
        } catch (\InvalidArgumentException $_ex) {
            throw new \InvalidArgumentException('A constant with the value of "' . $constant . '" does not exist.');
        }

        return !$flipped && $pretty ? Inflector::display(Inflector::neutralize($_name)) : $_name;
    }

    /**
     * @param mixed $constant
     * @param bool  $flipped
     *
     * @return string
     */
    public static function prettyNameOf($constant, $flipped = true)
    {
        return static::nameOf($constant, $flipped, true);
    }

    /**
     * Returns a list of the constants in a comma-separated display manner
     *
     * @param string|null $quote   An optional quote to enrobe the value (' or " only)
     * @param bool        $tags    If true, $tags will be used instead of the constants themselves
     * @param bool        $numbers If true, the numeric value of the constant will be printed
     * @param bool        $lastOr  If true, the word "or" will be placed before the last item in the list
     *
     * @return string
     */
    public static function prettyList($quote = null, $tags = false, $numbers = false, $lastOr = true)
    {
        $quote != '\'' && $quote != '"' && $quote = null;
        $_values = array_values($tags ? static::$tags : static::getDefinedConstants(true));

        //  Remove unwanted items...
        for ($_i = 0, $_max = count($_values); $_i < $_max; $_i++) {
            if ('_' == $_values[$_i][0]) {
                array_forget($_values, $_i);
                continue;
            }
        }

        //  Find the last item for "or" placement
        end($_values);
        $_last = key($_values);
        $_list = null;

        for ($_i = 0, $_max = count($_values); $_i < $_max; $_i++) {
            //  No comma on first guy...
            $_i != 0 && $_list .= ', ';
            //  Add "or" on the last
            $lastOr && ($_i == $_last) && $_list .= 'or ';

            //  Format the item
            if ($numbers) {
                $_item = $quote . static::toValue($_values[$_i]) . $quote . ' (' . strtolower($_values[$_i]) . ')';
            } else {
                $_item = $quote . strtolower($_values[$_i]) . $quote;
            }

            $_list .= $_item;
        }

        return $_list;
    }

    /**
     * Returns the strict bitwise AND of $constant's value and $mask
     *
     * @param string|int $constant The constant name or numeric value
     * @param int        $mask     The bit mask to check
     *
     * @return bool
     */
    public static function bitSet($constant, $mask = 0)
    {
        if (!is_numeric($constant)) {
            $constant = static::resolve($constant);
        }

        return ($mask == ($constant & $mask));
    }
}
