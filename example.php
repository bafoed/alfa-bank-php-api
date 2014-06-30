<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');
require_once(dirname(__FILE__) . '/AlfaAPI/AlfaAPI.php');
$alfa = new \AlfaAPI\AlfaAPI('7663717', '******');
if (!$alfa->auth()) {
    die('Cannot auth.');
}

$bankAccounts = $alfa->getBankAccounts();
foreach ($alfa->getMovements($bankAccounts[0]) as $payment) {
    echo sprintf('[%s]: %s (%s)', date('d.m.Y', $payment->date), $payment->amount, $payment->description) . PHP_EOL;
}
