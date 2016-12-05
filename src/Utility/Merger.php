<?php namespace ChaoticWave\BlueVelvet\Utility;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Otherwise unavailable array/object merge functions
 */
class Merger
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Merge values of $source (and any following arrays) down into $target, one at a time.
     *
     * @param array|Arrayable      $target
     * @param array|Arrayable      $source
     * @param array|Arrayable|null $source2 Another array to merge
     *
     * @return array The augmented $target
     */
    public static function mergeDown($target, $source, $source2 = null)
    {
        $_args = func_get_args();

        //  Shift off the target and start merging
        array_shift($_args);

        while (null !== ($_source = array_shift($_args))) {
            if ($_source instanceof Arrayable) {
                $_source = $_source->toArray();
            }

            if (is_array($_source)) {
                foreach (array_merge(array_dot($target), array_dot($_source)) as $_key => $_value) {
                    if ($_value != array_get($target, $_key)) {
                        array_set($target, $_key, $_value);
                    }
                }
            }
        }

        return $target;
    }
}
