<?php
require $_SERVER["DOCUMENT_ROOT"] . "/restrictions.php";
require $_SERVER["DOCUMENT_ROOT"] . "/functions.php";


$wallet = $_GET['user'];

$debug = $_GET['debug'] ?? false;
if ($_SERVER['REMOTE_ADDR'] != '95.105.113.135') {
    $debug = false;
}

$transaction_id = $_GET['id'];

$rowResult = execWithInfo(["cleos -u https://wax.greymass.com/ get transaction $transaction_id"], false, true);


echo json_encode(array_shift($rowResult)['response']);


