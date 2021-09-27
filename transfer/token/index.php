<?php

require $_SERVER["DOCUMENT_ROOT"]."/restrictions.php";
require $_SERVER["DOCUMENT_ROOT"]."/functions.php";


$wallet = $_GET['user'];

$debug = $_GET['debug'] ?? false;
if ($_SERVER['REMOTE_ADDR'] != '95.105.113.135') {
    $debug = false;
}

$recipient = $_GET['recipient'];
$quantity = $_GET['quantity'];
if (!isset($recipient)) {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Field `recipient` is empty'
        ]
    );
    die();
}
if (!isset($quantity)) {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Field `quantity` is empty'
        ]
    );
    die();
}


$result = execWithInfo(
    [
        //'cleos wallet create --to-console --name gcstaking',
        'cleos wallet open --name gcstaking',
        'cleos wallet unlock --name gcstaking --password /* ТУТ ПАРОЛЬ ДЛЯ АНЛОНКА*/',
        //'cleos wallet import --name gcstaking --private-key /* ТУТ ПРИВАТНЫЙ АКТИВНЫЙ КЛЮЧ*/',
        'cleos -u https://wax.greymass.com/ transfer gcmoneystake '.$recipient.' "'. $quantity .' WAX" -p gcmoneystake@active'
    ],
    $debug
);

$lastCommand = $result[array_keys($result)[count($result) - 1]];
if ($lastCommand['status'] == 0) {
    if (empty($lastCommand['response'])) {
        echo json_encode(
            [
                'status' => 'error',
                'message' => 'RAM, CPU or NET resources is ran out'
            ]
        );
    } else {
        preg_match('/[a-zA-Z0-9]{64}/', $lastCommand['response'][0], $matches, PREG_OFFSET_CAPTURE);
        echo json_encode(
            [
                'status' => 'success',
                'transaction_id' => array_shift($matches[0]),
                'message' => null,
            ]
        );
    }
} else {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Internal server error'
        ]
    );
}

?>