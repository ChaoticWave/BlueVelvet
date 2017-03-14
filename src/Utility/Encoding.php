<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * Down and dirty character encoding utility
 */
class Encoding
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string|array $string
     *
     * @return string|array
     */
    public static function toUtf8($string)
    {
        return static::encode($string, 'utf-8');
    }

    /**
     * @param string|string[] $string
     *
     * @return array|string
     */
    public static function toWin1252($string)
    {
        return static::encode($string, 'iso-8859-1');
    }

    /**
     * @param string|string[] $string
     *
     * @return array|string
     */
    public static function toLatin1($string)
    {
        return static::toWin1252($string);
    }

    /**
     * Returns a normalized charset name
     *
     * @param string $label
     *
     * @return string
     */
    public static function normalizeEncoding($label)
    {
        $_types = array(
            'ISO88591'    => 'ISO-8859-1',
            'ISO8859'     => 'ISO-8859-1',
            'ISO'         => 'ISO-8859-1',
            'LATIN1'      => 'ISO-8859-1',
            'LATIN'       => 'ISO-8859-1',
            'UTF8'        => 'UTF-8',
            'UTF'         => 'UTF-8',
            'WIN1252'     => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1',
        );

        $_encoding = preg_replace('/[^a-zA-Z0-9\s]/', '', strtoupper(trim($label)));

        return empty($_types[$_encoding]) ? 'UTF-8' : $_types[$_encoding];
    }

    /**
     * Generic character decoder
     *
     * @param string|array $string
     *
     * @return array|string
     */
    public static function decode($string)
    {
        if (is_array($string)) {
            foreach ($string as $_key => $_value) {
                $string[$_key] = static::decode($_value);
            }

            return $string;
        }

        return utf8_decode(static::toUtf8($string));
    }

    /**
     * Generic character encoder
     *
     * @param string|array $string
     * @param string       $to A valid character encoding name (i.e. UTF-8, UTF-16, Windows-1252, etc.)
     *
     * @return array|string
     */
    public static function encode($string, $to = 'UTF-8')
    {
        $to = static::normalizeEncoding($to);

        if (is_array($string)) {
            foreach ($string as $_key => $_value) {
                $string[$_key] = static::encode($_value, $to);
            }

            return $string;
        }

        if (false === ($_encoding = mb_detect_encoding($string))) {
            return $string;
        }

        return mb_convert_encoding($string, $to);
    }
}
