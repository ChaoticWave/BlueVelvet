<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * Stuff dealing with words
 */
class Words
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $string
     * @param int    $minimum   The minimum length
     * @param bool   $lowercase If true, convert to lower case. If false, convert to uppercase. Null = do nothing
     *
     * @return array
     */
    public static function extract($string, $minimum = 2, $lowercase = true)
    {
        $_string = stripslashes($string);
        $_search = [];

        if (preg_match_all('/"[^"]*"/', $_string, $_search)) {
            $_string = preg_replace('/"[^"]*"/', '', $_string);
        }

        $_wordList = preg_split("/[; :\t]/", $_string, -1, PREG_SPLIT_NO_EMPTY);

        if (!empty($_search[0])) {
            foreach ($_search[0] as $_key => $_value) {
                $_wordList[] = trim($_value, ' "');
            }
        }

        $_wordList = array_unique($_wordList);

        foreach ($_wordList as $_key => $_word) {
            if (!is_numeric($_word) && strlen($_word) < $minimum) {
                array_forget($_wordList, $_key);
                continue;
            }

            $_word = utf8_encode($_word);
            $_wordList[$_key] = $lowercase ? mb_strtolower($_word) : mb_strtoupper($_word);
        }

        return $_wordList;
    }
}
