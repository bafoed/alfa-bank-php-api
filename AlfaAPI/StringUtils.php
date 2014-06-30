<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */

namespace AlfaAPI;

class StringUtils
{
    /**
     * Changes encoding from macintosh (used by Alfa-Bank) to UTF-8
     * @param  string $input Macintosh-encoded string
     * @return string        UTF-8 encoded string
     */
    public static function fixEncoding($input)
    {
        // Fallback:
        // return iconv('UTF-8', 'MacCyrillic', $input);
        //
        // Now must be fixed by right file-encoding
        return $input;
    }

    /**
     * Remove spaces from string. Uses when parsing balance
     * @param  string $input
     * @return string
     */
    public static function removeSpaces($input)
    {
        return preg_replace('/\s+/', '', $input);
    }

    /**
     * Convert time from ISO 8601 to unix timestamp.
     * @param string $input ISO 8601 string
     * @return string
     */
    public static function ISO8601ToUnixtime($input)
    {
        return date('U', strtotime($input));
    }

}