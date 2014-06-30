<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;

class Movement
{
    /**
     * Payment ID
     * @var string
     */
    public $id;
    /**
     * Date of payment in unixtime
     * @var string
     */
    public $date;
    /**
     * Payment amount
     * @var float
     */
    public $amount;
    /**
     * Payment currency
     * @var Currency
     */
    public $currency;
    /**
     * Payment description
     * @var string
     */
    public $description;
    /**
     * Payment operation key
     * @var string
     */
    public $operationKey;
    /**
     * Payment reference
     * @var string
     */
    public $reference;
    /**
     * Is payment holded
     * @var boolean
     */
    public $hold;
    /**
     * Is payment available for marking
     * @var boolean
     */
    public $isAvailableForMarking;



    /**
     * Next params may not exist.
     * They are used only if Alfa-Bank could determine it.
     */

    /**
     * Credit card number which was used in payment
     * @var string
     */
    public $cardNumber;

    /**
     * Purchase date in unixtime
     * @var integer
     */
    public $purchaseDate;

    /**
     * Category ID of payment
     * @var string
     */
    public $bankCategoryId;

    /**
     * Category name of payment
     * @var string
     */
    public $bankCategoryName;

    /**
     * ID of shop where payment was made
     * @var string
     */
    public $pointOfSaleId;

    /**
     * Name of shop where payment was made
     * @var string
     */
    public $pointOfSaleName;
}