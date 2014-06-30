<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;

require_once(dirname(__FILE__) . '/CreditCardTypes.php');
require_once(dirname(__FILE__) . '/CreditCardIPS.php');
require_once(dirname(__FILE__) . '/BankAccount.php');

class CreditCard
{
    /**
     * Card name
     * @var string
     */
    public $name;
    /**
     * Card number (masked)
     * @var string
     */
    public $number;
    /**
     * Integrated Processing solutions (Visa / MC)?
     * @var CreditCardIPS
     */
    public $ips;
    /**
     * Bank account linked to credit card
     * @var string
     */
    public $account;
    /**
     * Card type
     * @var CreditCardType
     */
    public $type;
    /**
     * Internal Alfa-bank status ID, not know what it is
     * @var string
     */
    public $statusId;
    /**
     * Status description
     * @var string
     */
    public $statusName;
}