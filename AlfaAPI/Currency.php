<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;
require_once(dirname(__FILE__) . '/Enum.php');

class Currency extends Enum
{
    public static $RUR = 'rur';
    public static $EUR = 'eur';
    public static $USD = 'usd';
    public static $UNKNOWN = 'unknown';

    /**
     * Detect currency from response
     * @param  string $currency Currency from response
     * @return Currency
     */
    public static function detectCurrency($currency)
    {
        if ($currency === 'RUR') {
            return Currency::$RUR;
        }

        if ($currency === 'EUR') {
            return Currency::$EUR;
        }

        if ($currency === 'USD') {
            return Currency::$USD;
        }

        return Currency::$UNKNOWN;
    }
}

Currency::enumerate();