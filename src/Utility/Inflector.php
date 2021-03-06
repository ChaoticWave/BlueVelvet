<?php namespace ChaoticWave\BlueVelvet\Utility;

/**
 * Provides inflection
 */
class Inflector
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Given a string, return it to neutral format (lowercase, period and underscores)
     *
     * @param string $item  The string to neutralize
     * @param string $strip If provided, it's value is removed from item before it's neutralized.
     *                      Example: "REQUEST_URI" would be "URI" with $strip = "REQUEST_"
     *
     * @return string
     */
    public static function neutralize($item, $strip = null)
    {
        if (is_numeric($item)) {
            return $item;
        }

        if (null !== $strip) {
            $item = str_ireplace($strip, null, $item);
        }

        //	Split by forward slash, backslash, period, or space...
        $_parts = preg_split("/[. \/\\\\]+/", $item);

        //  Make it perdee...
        if (!empty($_parts)) {
            foreach ($_parts as $_index => $_part) {
                $_parts[$_index] = static::decamelize($_part);
            }
        }

        return implode('.', $_parts);
    }

    /**
     * Given an object, returns an array containing the variables of the object and their values.
     * The keys for the object have been neutralized for your protection
     *
     * @param object $object
     * @param string $strip If provided, it's value is removed from item before it's neutralized.
     *                      Example: "REQUEST_URI" would be "URI" with $strip = "REQUEST_"
     *
     * @return string
     */
    public static function neutralizeObject($object, $strip = null)
    {
        $_variables = is_array($object) ? $object : get_object_vars($object);

        if (!empty($_variables)) {
            foreach ($_variables as $_key => $_value) {
                $_originalKey = $_key;

                if ($strip) {
                    $_key = str_replace($strip, null, $_key);
                }

                $_variables[static::neutralize(ltrim($_key, '_'))] = $_value;
                unset($_variables[$_originalKey]);
            }
        }

        return $_variables;
    }

    /**
     * Given a neutralized string, return it to suitable for framing
     *
     * @param string $item The string to frame
     *
     * @return string
     */
    public static function display($item)
    {
        return static::camelize(str_replace(['_', '.', '\\', '/'],
            ' ',
            $item),
            '_',
            true,
            false);
    }

    /**
     * Given a string, return it to non-neutral format (delimited camel-case)
     *
     * @param string $item      The string to deneutralize
     * @param bool   $isKey     True if the string is an array/object key/tag
     * @param string $delimiter Will be used to reconstruct the string
     *
     * @return string
     */
    public static function deneutralize($item, $isKey = false, $delimiter = '\\')
    {
        if (is_numeric($item)) {
            return $item;
        }

        return static::camelize(str_replace(['_', '.', $delimiter],
            ' ',
            $item),
            '_',
            false,
            $isKey);
    }

    /**
     * @param string $tag
     * @param string $delimiter
     *
     * @return string
     */
    public static function baseName($tag, $delimiter = '\\')
    {
        return @end(@explode($delimiter, $tag));
    }

    /**
     * Converts a separator delimited string to camel case
     *
     * @param string  $string
     * @param string  $separator
     * @param boolean $preserveWhiteSpace
     * @param bool    $isKey If true, first word is lower-cased
     *
     * @return string
     */
    public static function camelize($string, $separator = null, $preserveWhiteSpace = false, $isKey = false)
    {
        empty($separator) && $separator = ['_', '-'];

        $_newString = ucwords(str_replace($separator, ' ', $string));

        if (false !== $isKey) {
            $_newString = lcfirst($_newString);
        }

        return (false === $preserveWhiteSpace ? str_replace(' ', null, $_newString) : $_newString);
    }

    /**
     * Converts a camel-cased word to a delimited lowercase string
     *
     * @param string $string
     *
     * @return string
     */
    public static function decamelize($string)
    {
        return strtolower(preg_replace("/([a-z])([A-Z])/", "\\1_\\2", $string));
    }

    /**
     * This function is NOT smart. It only looks for an 's' at the end of a word. You have been warned.
     *
     * @param string $word
     * @param bool   $returnSingular If true, the word without the "s" is returned.
     *
     * @return bool|string
     */
    public static function isPlural($word, $returnSingular = false)
    {
        if (empty($word) || !is_string($word) || strlen($word) < 3) {
            return false;
        }

        $_temp = $word[strlen($word) - 1];

        if ('s' == $_temp && $word == static::pluralize(substr($word, 0, -1))) {
            if (false !== $returnSingular) {
                return substr($word, 0, -1);
            }

            return true;
        }

        return false;
    }

    /**
     * Converts a word to its plural form. Totally swiped from Yii
     *
     * @param string $name the word to be pluralized
     *
     * @return string the pluralized word
     */
    public static function pluralize($name)
    {
        /** @noinspection SpellCheckingInspection */
        static $_blacklist = [
            'Amoyese',
            'bison',
            'Borghese',
            'bream',
            'breeches',
            'britches',
            'buffalo',
            'cantus',
            'carp',
            'chassis',
            'clippers',
            'cod',
            'coitus',
            'Congoese',
            'contretemps',
            'corps',
            'debris',
            'deer',
            'diabetes',
            'djinn',
            'eland',
            'elk',
            'equipment',
            'Faroese',
            'flounder',
            'Foochowese',
            'gallows',
            'Genevese',
            'geese',
            'Genoese',
            'Gilbertese',
            'graffiti',
            'headquarters',
            'herpes',
            'hijinks',
            'Hottentotese',
            'information',
            'innings',
            'jackanapes',
            'Kiplingese',
            'Kongoese',
            'Lucchese',
            'mackerel',
            'Maltese',
            '.*?media',
            'metadata',
            'mews',
            'moose',
            'mumps',
            'Nankingese',
            'news',
            'nexus',
            'Niasese',
            'Pekingese',
            'Piedmontese',
            'pincers',
            'Pistoiese',
            'pliers',
            'Portuguese',
            'proceedings',
            'rabies',
            'rice',
            'rhinoceros',
            'salmon',
            'Sarawakese',
            'scissors',
            'sea[- ]bass',
            'series',
            'Shavese',
            'shears',
            'siemens',
            'species',
            'swine',
            'testes',
            'trousers',
            'trout',
            'tuna',
            'Vermontese',
            'Wenchowese',
            'whiting',
            'wildebeest',
            'Yengeese',
        ];
        /** @noinspection SpellCheckingInspection */
        static $_rules = [
            '/(s)tatus$/i'                                                                 => '\1\2tatuses',
            '/(quiz)$/i'                                                                   => '\1zes',
            '/^(ox)$/i'                                                                    => '\1en',
            '/(matr|vert|ind)(ix|ex)$/i'                                                   => '\1ices',
            '/([m|l])ouse$/i'                                                              => '\1ice',
            '/(x|ch|ss|sh|us|as|is|os)$/i'                                                 => '\1es',
            '/(shea|lea|loa|thie)f$/i'                                                     => '\1ves',
            '/(buffal|tomat|potat|ech|her|vet)o$/i'                                        => '\1oes',
            '/([^aeiouy]|qu)ies$/i'                                                        => '\1y',
            '/([^aeiouy]|qu)y$/i'                                                          => '\1ies',
            '/(?:([^f])fe|([lre])f)$/i'                                                    => '\1\2ves',
            '/([ti])um$/i'                                                                 => '\1a',
            '/sis$/i'                                                                      => 'ses',
            '/move$/i'                                                                     => 'moves',
            '/foot$/i'                                                                     => 'feet',
            '/human$/i'                                                                    => 'humans',
            '/tooth$/i'                                                                    => 'teeth',
            '/(bu)s$/i'                                                                    => '\1ses',
            '/(hive)$/i'                                                                   => '\1s',
            '/(p)erson$/i'                                                                 => '\1eople',
            '/(m)an$/i'                                                                    => '\1en',
            '/(c)hild$/i'                                                                  => '\1hildren',
            '/(alumn|bacill|cact|foc|fung|nucle|octop|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
            '/us$/i'                                                                       => 'uses',
            '/(alias)$/i'                                                                  => '\1es',
            '/(ax|cris|test)is$/i'                                                         => '\1es',
            '/s$/'                                                                         => 's',
            '/$/'                                                                          => 's',
        ];

        if (empty($name) || in_array(strtolower($name), $_blacklist)) {
            return $name;
        }

        foreach ($_rules as $_rule => $_replacement) {
            if (preg_match($_rule, $name)) {
                return preg_replace($_rule, $_replacement, $name);
            }
        }

        return $name;
    }

}
