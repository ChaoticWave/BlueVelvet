<?php namespace ChaoticWave\BlueVelvet\Utility\Packets;

use Illuminate\Http\Response;

class ErrorPacket extends BasePacket
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param array|null|\Exception $contents
     * @param int                   $statusCode
     * @param string|null           $errorMessage
     *
     * @return array
     */
    public static function create($contents = null, $statusCode = Response::HTTP_NOT_FOUND, $errorMessage = null)
    {
        if ($contents instanceof \Exception) {
            $statusCode = $statusCode ?: (method_exists($contents, 'getStatusCode') ? $contents->getStatusCode() : $contents->getCode());
            $errorMessage = $errorMessage ?: $contents->getMessage();
            $contents = null;
        }

        return parent::make($contents, $statusCode, $errorMessage);
    }
}
