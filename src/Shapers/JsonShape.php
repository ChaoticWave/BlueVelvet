<?php namespace ChaoticWave\BlueVelvet\Utility\Shapers;

use ChaoticWave\BlueVelvet\Utility\Contracts\ShapesData;
use ChaoticWave\BlueVelvet\Utility\Json;

class JsonShape implements ShapesData
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Transforms an array of data into a new shape
     *
     * @param array $source  The source data
     * @param array $options Defaults are ['options' => 0, 'depth' => 512]
     *
     * @return mixed
     */
    public static function transform(array $source, $options = [])
    {
        return Json::encode($source, array_get($options, 'options', 0), array_get($options, 'depth', 512));
    }
}
