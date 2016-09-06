<?php namespace ChaoticWave\BlueVelvet\Packets;

use Illuminate\Http\Response;

class BasePacket
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The version of this packet
     */
    const PACKET_VERSION = '1.0';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Builds a response container
     *
     * @param array             $data The contents
     * @param int               $httpCode
     * @param string|\Exception $errorMessage
     *
     * @return array
     */
    public static function make($data = null, $httpCode = Response::HTTP_OK, $errorMessage = null)
    {
        //  All packets have this
        return static::makePacket($data, $httpCode, $errorMessage);
    }

    /**
     * @param int|null               $httpCode The HTTP status to return
     * @param mixed|array|null       $data     The payload to return
     * @param string|\Exception|null $errorMessage
     *
     * @return array
     */
    protected static function makePacket($data = null, $httpCode = Response::HTTP_OK, $errorMessage = null)
    {
        $_ex = null;

        if ($data instanceof \Exception) {
            $_ex = $data;
            $data = null;
        } elseif ($errorMessage instanceof \Exception) {
            $_ex = $errorMessage;
            $errorMessage = null;
        } elseif ($httpCode instanceof \Exception) {
            $_ex = $httpCode;
        }

        //  Build the packet
        $_packet = [
            //  Basically any 2xx code is considered successful
            'success' => $_success = ($httpCode >= Response::HTTP_OK && $httpCode < Response::HTTP_MULTIPLE_CHOICES),
            'data'    => $data,
        ];

        //  Get the status code
        $_packet = array_merge($_packet, $_ex ? static::parseException($_ex) : ['code' => $httpCode]);

        return static::signPacket($_packet);
    }

    /**
     * Generates a signature for a packet request response
     *
     * @param array $packet
     *
     * @return array
     *
     */
    protected static function signPacket(array $packet = [])
    {
        $_startTime = array_get($_SERVER, 'REQUEST_TIME_FLOAT', array_get($_SERVER, 'REQUEST_TIME', $_timestamp = microtime(true)));
        $_elapsed = $_timestamp - $_startTime;

        $_id = sha1($_startTime . array_get($_SERVER, 'HTTP_HOST') . array_get($_SERVER, 'REMOTE_ADDR'));

        //  All packets have this
        $packet['request'] = [
            'id'          => $_id,
            'version'     => static::PACKET_VERSION,
            'signature'   => base64_encode(hash_hmac('sha256', $_id, $_id, true)),
            'verb'        => array_get($_SERVER, 'REQUEST_METHOD'),
            'request-uri' => array_get($_SERVER, 'REQUEST_URI'),
            'start'       => date('c', $_startTime),
            'elapsed'     => (float)number_format($_elapsed, 4),
        ];

        return $packet;
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    protected function parseException($exception)
    {
        $_parsed = [
            'success' => false,
            'error'   => $exception->getMessage(),
        ];

        if (!empty($_code = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : $exception->getCode())) {
            $_parsed['code'] = $_code;
        }

        return $_parsed;
    }
}
