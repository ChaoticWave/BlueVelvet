<?php namespace ChaoticWave\BlueVelvet\Contracts;

/**
 * Something that acts like a data filter
 */
interface FilterLike
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Apply the filter to $data
     *
     * @param array  $data  The data to filter
     * @param string $table The specific table filter, if any, to apply
     *
     * @return array The filtered data
     */
    public function apply(array $data = [], $table = null);
}
