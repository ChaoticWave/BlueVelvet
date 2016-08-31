<?php namespace ChaoticWave\BlueVelvet\Utility;

class Math
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|number $value
     * @param mixed         $default      The default value to return
     * @param int|null      $precision    The number of decimals
     * @param string|null   $separator    The thousands separator
     * @param string|null   $decimalPoint The decimal point character
     *
     * @return float|int
     */
    public static function toFloat($value, $default = 0, $precision = null, $separator = null, $decimalPoint = null)
    {
        if (empty($precision . $separator . $decimalPoint)) {
            $_result = str_replace([',', ' '], ['.', null], $value);

            if (!is_numeric($_result) || !is_finite($_result = floatval($_result))) {
                $_result = $default;
            }

            return $_result;
        }
        //   From the start, followed by an optional signing character
        $_pattern = '/^[\-\+]{0,1}';

        //  Find period separator
        if (null !== $separator) {
            $_pattern .= '[0-9]{1,3}(' . (('.' === $separator) ? '[\.]' : $separator) . '[0-9]{3})*';
        } else {
            $_pattern .= '[0-9]+';
        }

        //  What's the decimal separator
        $_dot = '[\.]';

        if (null !== $decimalPoint) {
            $_dot = ('.' == $decimalPoint) ? '[\.]' : $decimalPoint;
        }

        //  Fixed number of decimals expected:
        if (null !== $precision && is_int($precision)) {
            //  If 0, then we do no expect ANY decimal
            if (!empty($precision)) {
                $_pattern .= $_dot . '[0-9]{' . $precision . '}';
            }
        } else {
            //  Decimals are optional, just check the format: separator followed by some digits...
            $_pattern .= '(' . $_dot . '[0-9]+){0,1}';
        }

        // End the string!
        $_pattern .= '$/';

        preg_match($_pattern, $value, $_matches);

        if (empty($_matches[0])) {
            return $default;
        }

        //  Removes the +sign if any:
        $_result = str_replace('+', null, $_matches[0]);

        //  Removes the thousand separator: useless for parsing!
        if (isset($separator)) {
            $_result = str_replace($separator, null, $_result);
        }
        // Forces default decimal separator:
        if (isset($decimalPoint)) {
            $_result = str_replace($decimalPoint, '.', $_result);
        }

        return floatval($_result);
    }
}
