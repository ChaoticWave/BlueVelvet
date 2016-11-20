<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * A bit-wise utility belt
 */
class Bits
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Test if $value has $mask bits set
     *
     * @param int $value The value to check
     * @param int $mask  The bitmask to use
     *
     * @return bool
     */
    public static function test($value, $mask = 0)
    {
        return $mask === ($value & $mask);
    }

    /**
     * Test if the value of CONSTANT has $mask bits set
     *
     * @param int $constant The value to check
     * @param int $mask     The bitmask to use
     *
     * @return bool FALSE if test fails or invalid constant, TRUE otherwise
     */
    public static function testConstant($constant, $mask = 0)
    {
        try {
            if (!is_numeric($constant)) {
                if (null === ($constant = constant($constant))) {
                    return false;
                }
            }

            return static::test($constant, $mask);
        } catch (\Exception $_ex) {
        }

        return false;
    }
}
