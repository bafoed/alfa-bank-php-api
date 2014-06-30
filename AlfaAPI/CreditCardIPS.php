<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;
require_once(dirname(__FILE__) . '/Enum.php');

class CreditCardIPS extends Enum
{
    public static $VISA = 'visa';
    public static $MASTERCARD = 'mastercard';
    public static $UNKNOWN = 'unknown';

    /**
     * Detect card IPS from response
     * @param  string $cardIPS Card IPS from response
     * @return CreditCardIPS
     */
    public static function detectCardIPS($cardIPS)
    {
        if ($cardIPS === 'MC') {
            return CreditCardIPS::$MASTERCARD;
        }

        if ($cardIPS === 'VISA') {
            return CreditCardIPS::$VISA;
        }

        return CreditCardIPS::$UNKNOWN;
    }
}

CreditCardIPS::enumerate();
