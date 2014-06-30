<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;
require_once(dirname(__FILE__) . '/Currency.php');

class BankAccount
{
    /**
     * Bank account name
     * @var string
     */
    public $name;
    /**
     * Bank account number
     * @var string
     */
    public $number;
    /**
     * Bank account balance
     * @var float
     */
    public $balance;
    /**
     * Bank account currency
     * @var Currency
     */
    public $currency;

    public function __construct($name, $number, $balance, Currency $currency)
    {
        $this->name = $name;
        $this->number = $number;
        $this->balance = $balance;
        $this->currency = $currency;
    }
}