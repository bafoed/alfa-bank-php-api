<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;

require_once(dirname(__FILE__) . '/BankAccount.php');
require_once(dirname(__FILE__) . '/CreditCard.php');
require_once(dirname(__FILE__) . '/Movement.php');
require_once(dirname(__FILE__) . '/StringUtils.php');

class AlfaAPI
{
    /**
     * Alfa-Mobile server URL.
     * @var string
     */
    public static $url = 'https://alfa-mobile.alfabank.ru:8443/ALFAJMB/';
    /**
     * Alfa-Mobile login
     * @var string
     */
    private $_login;
    /**
     * Alfa-Mobile password
     * @var string
     */
    private $_password;
    /**
     * Session ID that will be used for requests. Fills automatically after auth.
     * @var string
     */
    private $_jsessionid;

    public function __construct($login, $password)
    {
        $this->_login = $login;
        $this->_password = $password;
    }

    private function _isLoggedIn()
    {
        return !empty($this->_jsessionid);
    }

    /**
     * Makes request to Alfa-Bank API
     * Alfa-Bank usually uses JSON for his requests/responses, but for some methods we need to sent RAW data of bytes.
     *
     * @param  string $method Method name
     * @param  array|string $data Data to send
     * @param  boolean $raw Send RAW data ($data must be string) or JSON ($data must be array)
     * @param  boolean|string $jmb_protocol_service Custom jmb-protocol-service header (required by some requests)
     * @return array|string if $raw return string of response, otherwise - decoded JSON
     */
    private function _call($method, $data = array(), $raw = false, $jmb_protocol_service = false)
    {
        $url = self::$url . $method;
        if ($this->_isLoggedIn()) {
            $url .= ';jsessionid=' . $this->_jsessionid;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $headers = array('Content-Type: application/octet-stream');
        if ($jmb_protocol_service) {
            $headers[] = 'jmb-protocol-service: ' . $jmb_protocol_service;
            $headers[] = 'jmb-protocol-version: 1.0';
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        if (!$raw) {
            $data = json_encode($data);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $response = StringUtils::fixEncoding($response);
        curl_close($ch);
        return $raw ? $response : json_decode($response, true);
    }

    /**
     * Performs an authorization.
     * @return boolean Is authorization success
     */
    public function auth()
    {
        $authRequest = "\x01\x00\x07" . $this->_login . "\x00\x08" . $this->_password;
        $authRequest .= "\x00\x00\x00\x09" . "iPhone711" . "\x00\x06";
        $authRequest .= "iPhone\x00\x055.5.1\x00\x02ru\x00";
        $response = $this->_call('ControllerServlet', $authRequest, true);
        if (preg_match('/jsessionid=(.*?)$/', $response, $match)) {
            $sessionId = $match[1];
            $this->_jsessionid = $sessionId;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return bank accounts linked to this account.
     * @return BankAccount[]|null
     */
    public function getBankAccounts()
    {
        if (!$this->_isLoggedIn()) {
            return null;
        }

        $bankAccountsRequest = "\x03\x00\x00";
        $response = $this->_call('ControllerServlet', $bankAccountsRequest, true);
        $result = array();
        preg_match_all('/<r(.*?)<\/r>/', $response, $bankAccounts);
        foreach ($bankAccounts[1] as $bankAccount) {
            preg_match_all('/<f>(.*?)<\/f>/', $bankAccount, $fields);
            list($name, $number, $balance) = $fields[1];
            $currency = Currency::detectCurrency(substr($balance, -3));
            $balance = doubleval(StringUtils::removeSpaces($balance));
            $bankAccountInstance = new BankAccount($name, $number, $balance, $currency);
            $result[] = $bankAccountInstance;
        }
        return $result;
    }

    /**
     * Returns list of user's credit card.
     * @return CreditCard[]|null
     */
    public function getCardsList()
    {
        if (!$this->_isLoggedIn()) {
            return null;
        }

        $response = $this->_call('gate', array('operationId' => 'CustomerCards:GetCardsList'), false, 'CustomerCards');
        $cards = array();
        foreach ($response['fields'][0]['value'] as $card) {
            $cardInstance = new CreditCard();
            $cardInstance->name = $card['name'];
            $cardInstance->number = $card['number'];
            $cardInstance->ips = CreditCardIPS::detectCardIPS($card['ips']);
            $bankAccountInstance = new BankAccount(
                $card['account']['description'],
                $card['account']['number'],
                doubleval(StringUtils::removeSpaces($card['account']['amount'])),
                Currency::detectCurrency($card['account']['currency'])
            );
            $cardInstance->account = $bankAccountInstance;
            $cardInstance->type = CreditCardType::detectCardType($card['type']);
            $cardInstance->statusId = $card['statusId'];
            $cardInstance->statusName = $card['statusName'];
            $cards[] = $cardInstance;
        }

        return $cards;
    }

    /**
     * Return history of payments for bank account
     * @param  BankAccount $bankAccount
     * @return Movement[]
     */
    public function getMovements(BankAccount $bankAccount)
    {
        if (!$this->_isLoggedIn()) {
            return null;
        }

        $response = $this->_call('gate', array(
            'operationId' => 'Budget:GetMovements',
            'parameters' => array(
                'part' => 0,
                'accountNumber' => $bankAccount->number
            )
        ), false, 'Budget');

        $movements = array();
        foreach ($response['fields'][0]['value'] as $movement) {
            $movementInstance = new Movement();
            $movementInstance->id = $movement['id'];
            $movementInstance->date = StringUtils::ISO8601ToUnixtime($movement['date']);
            $movementInstance->amount = doubleval(StringUtils::removeSpaces($movement['amount']));
            $movementInstance->currency = Currency::detectCurrency($movement['currency']);
            $movementInstance->description = $movement['description'];
            $movementInstance->operationKey = $movement['operationKey'];
            $movementInstance->reference = $movement['reference'];
            $movementInstance->hold = $movement['hold'];
            $movementInstance->isAvailableForMarking = $movement['isAvailableForMarking'];
            /** Optional fields **/
            if (isset($movement['cardNumber'])) {
                $movementInstance->cardNumber = $movement['cardNumber'];
            }

            if (isset($movement['bankCategoryId'])) {
                $movementInstance->bankCategoryId = $movement['bankCategoryId'];
            }

            if (isset($movement['bankCategoryName'])) {
                $movementInstance->bankCategoryName = $movement['bankCategoryName'];
            }

            if (isset($movement['pointOfSaleId'])) {
                $movementInstance->pointOfSaleId = $movement['pointOfSaleId'];
            }

            if (isset($movement['pointOfSaleName'])) {
                $movementInstance->pointOfSaleName = $movement['pointOfSaleName'];
            }

            if (isset($movement['purchaseDate'])) {
                $movementInstance->purchaseDate = StringUtils::ISO8601ToUnixtime($movement['purchaseDate']);
            }

            $movements[] = $movementInstance;
        }

        return $movements;
    }
}