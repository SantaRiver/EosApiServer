<?php

require $_SERVER["DOCUMENT_ROOT"]."/restrictions.php";
require $_SERVER["DOCUMENT_ROOT"]."/functions.php";

$command = 'cleos -u https://wax.greymass.com/ get account gcmoneystake -j';
$response = execWithInfo([$command], false, true)[$command]['response'];


$resource = $_GET['resource'];
if ($response->account_name = 'gcmoneystake') {
    switch ($resource) {
        case 'balance':
            echo json_encode(
                [
                    'status' => 'success',
                    'balance' => str_replace(' WAX', '', $response->core_liquid_balance)
                ]
            );
            break;
        case 'cpu':
            echo json_encode(
                [
                    'status' => 'success',
                    'cpu' => str_replace(' WAX', '', $response->self_delegated_bandwidth->cpu_weight)
                ]
            );
            break;
        case 'net':
            echo json_encode(
                [
                    'status' => 'success',
                    'net' => str_replace(' WAX', '', $response->self_delegated_bandwidth->net_weight)
                ]
            );
            break;
        case 'ram':
            echo json_encode(
                [
                    'status' => 'success',
                    'ram' => $response->total_resources->ram_bytes
                ]
            );
            break;
        default :
            $result = [
                'balance' => str_replace(' WAX', '', $response->core_liquid_balance),
                'cpu' => str_replace(' WAX', '', $response->self_delegated_bandwidth->cpu_weight),
                'net' => str_replace(' WAX', '', $response->self_delegated_bandwidth->net_weight),
                'ram' => $response->total_resources->ram_bytes,
            ];
            echo json_encode(
                [
                    'status' => 'success',
                    'resources' => $result
                ]
            );

            break;
    }
} else {
    echo json_encode(['status' => 'error']);
}
