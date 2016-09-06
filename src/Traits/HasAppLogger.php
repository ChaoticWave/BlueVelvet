<?php namespace ChaoticWave\BlueVelvet\Traits;

if (!class_exists('\AppLog', false)) {
    class_alias('Illuminate\Support\Facades\Log', '\AppLog');
}

/**
 * Provides logging functions via laravel/lumen
 */
trait HasAppLogger
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    protected function logDebug($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        return \AppLog::debug($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    protected function logInfo($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        return \AppLog::info($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    protected function logNotice($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        return \AppLog::notice($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    protected function logWarning($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        return \AppLog::warning($message, $context);
    }

    /**
     * @param string|\Exception $message
     * @param array             $context
     *
     * @return bool Returns FALSE always
     */
    protected function logError($message, $context = [])
    {
        if ($message instanceof \Exception) {
            $message = 'exception (' . $message->getCode() . '): ' . $message->getMessage();
        }

        /** @noinspection PhpUndefinedClassInspection */
        \AppLog::error($message, $context);

        return false;
    }

    /**
     * @param string $message
     * @param array  $context
     *
     *
     * @return bool Returns FALSE always
     */
    protected function logCritical($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        \AppLog::critical($message, $context);

        return false;
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return bool Returns FALSE always
     */
    protected function logAlert($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        \AppLog::alert($message, $context);

        return false;
    }

    /**
     * @param string $message
     * @param array  $context
     *
     * @return bool Returns FALSE always
     */
    protected function logEmergency($message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        \AppLog::emergency($message, $context);

        return false;
    }

    /**
     * @param string $message
     * @param int    $level
     * @param array  $context
     *
     * @return bool
     */
    protected function logRecord($level, $message, $context = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        return \AppLog::addRecord($level, $message, $context);
    }
}
