<?php namespace ChaoticWave\BlueVelvet\Contracts;

interface Inspectable
{
    /**
     * Return an array of inspection data
     *
     * @param string|int|null $filter
     *
     * @return array
     */
    public function inspect($filter = null);
}
