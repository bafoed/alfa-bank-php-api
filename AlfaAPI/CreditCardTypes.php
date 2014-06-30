<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;
require_once(dirname(__FILE__) . '/Enum.php');

class CreditCardType extends Enum
{
    public static $DEBIT = 'debit';
    public static $CREDIT = 'credit';
    public static $UNKNOWN = 'unknown';

    /**
     * Detect card type from response
     * @param  string $cardType Type from response
     * @return CreditCardType
     */
    public static function detectCardType($cardType)
    {
        if ($cardType === 'D') {
            return CreditCardType::$DEBIT;
        }

        if ($cardType === 'C') {
            return CreditCardType::$CREDIT;
        }

        return CreditCardType::$UNKNOWN;
    }
}

CreditCardType::enumerate();
