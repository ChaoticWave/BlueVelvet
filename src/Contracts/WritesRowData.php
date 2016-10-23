<?php namespace ChaoticWave\BlueVelvet\Contracts;

/**
 * Something that writes data
 */
interface WritesRowData
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param array|\Traversable $data
     *
     * @return mixed
     */
    public function writeRow($data = []);
}
