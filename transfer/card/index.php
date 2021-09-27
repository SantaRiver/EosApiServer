<?php

require $_SERVER["DOCUMENT_ROOT"]."/restrictions.php";
include $_SERVER["DOCUMENT_ROOT"]."/functions.php";


$wallet = $_GET['user'];

$debug = $_GET['debug'] ?? false;
if ($_SERVER['REMOTE_ADDR'] != '95.105.113.135') {
    $debug = false;
}

$balance = getBalance($wallet);
$recipient = $_GET['recipient'];
$assets_id = $_GET['assets_id'];
if (!isset($recipient)) {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Field `recipient` is empty'
        ]
    );
    die();
}
if (!isset($assets_id)) {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Field `assets_id` is empty'
        ]
    );
    die();
}
$assets_id_query = '[';
foreach (explode(',', $assets_id) as $asset_id) {
    $assets_id_query .= '"'.$asset_id.'", ';
}
$assets_id_query = substr($assets_id_query, 0, -2);
$assets_id_query .= ']';


$result = execWithInfo(
    [
        //'cleos wallet create --to-console --name gcstaking',
        'cleos wallet open --name gcstaking',
        'cleos wallet unlock --name gcstaking --password PW5KURzmtFheRSqsXRiQEyCjSANfdP5DVDvjxH9f6eJni9Myg4Spd',
        //'cleos wallet import --name gcstaking --private-key PVT_K1_2PDKc91LSvb4tFuqeKsEmKzCPDF7qT8XieK6xzJseWhFhzMMBJ',
        //'cleos -u https://wax.greymass.com/ push action eosio.token transfer \'[ "gcmoneystake", "fflro.wam", "1099554680644"]\' -p gcmoneystake@active',
        /*'cleos -u https://wax.greymass.com/ push action eosio.token transfer
                "["fflro.wam", "gcmoneystake", ["1099554680644"], memo: ""}" -p gcmoneystake@active',*/
        'cleos -u https://wax.greymass.com/ push transaction -j \'{
                "actions":[{
                    "account":"atomicassets",
                    "name":"transfer",
                    "data":{
                        "from":"gcmoneystake",
                        "to":"'.$recipient.'",
                        "asset_ids":'.$assets_id_query.',
                        "memo":""},
                    "authorization":[{
                        "actor":"gcmoneystake",
                        "permission":
                        "active"}
                    ]}
                ]
            }\''
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
        preg_match('/[a-zA-Z0-9]{64}/', $lastCommand['response'][1], $matches, PREG_OFFSET_CAPTURE);
        echo json_encode(
            [
                'status' => 'success',
                'transaction_id' => array_shift($matches[0]),
                'message' => null,
            ]
        );
    }
} else {
    $matches = [];
    preg_match(
        "/Sender doesn't own at least one of the provided assets/",
        $lastCommand['response'][2],
        $matches,
        PREG_OFFSET_CAPTURE
    );
    if ($matches[0][0]) {
        echo json_encode(
            [
                'status' => 'error',
                'message' => str_replace("assertion failure with message: ", "", $lastCommand['response'][2])
            ]
        );
    } else {
        echo json_encode(
            [
                'status' => 'error',
                'message' => 'Internal server error'
            ]
        );
    }
}
?>