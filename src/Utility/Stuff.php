<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * Things that can't be categorized
 */
class Stuff
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Strips a class name down for use in logs
     *
     * @param string      $class
     * @param bool        $basename
     * @param string|null $remove
     * @param bool        $lowercase
     *
     * @return mixed|string
     */
    public static function trimClassForLogging($class, $basename = true, $remove = null, $lowercase = true)
    {
        if ($basename) {
            $class = array_last(explode('\\', $class));
        }

        if ($remove) {
            $class = str_ireplace($remove, null, $class);
        }

        if ($lowercase) {
            $class = strtolower($class);
        }

        return $class;
    }
}
