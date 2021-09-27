<?php

function execWithInfo($commands, $debug = false, $merger = false): array
{
    $result = [];
    foreach ($commands as $command) {
        $status = null;
        $response = null;
        $commandResult = '';
        exec($command.' 2>&1', $response, $status);
        if (count($response) > 1) {
            foreach ($response as $line) {
                $commandResult .= $line;
            }
            $commandResult = json_decode($commandResult);
        } else {
            $commandResult = $response[0];
        }
        $result[$command] = [
            'command' => $command,
            'status' => $status,
            'response' => ($merger) ? $commandResult : $response,
        ];
    }
    if ($debug) {
        echo '<pre>';
        var_dump($result);
        echo '</pre>';
    }
    return $result;
}

function getBalance($user): string
{
    $url = "https://staking.goldencage.io/api/get_balance/".$user;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($resp);
    if ($response->status == 'success') {
        return $response->balance;
    } else {
        return 'Error';
    }
}

function getCPU($response): array
{
    $cpu_staked = $response[15];
    $cpu_staked = str_replace('(total stake delegated from account to self)', '', $cpu_staked);
    $cpu_staked = str_replace('staked:', '', $cpu_staked);
    $cpu_staked = str_replace('WAX', '', $cpu_staked);
    $cpu_staked = str_replace(' ', '', $cpu_staked);
    $cpu_delegated = $response[16];
    $cpu_delegated = str_replace('(total staked delegated to account from others)', '', $cpu_delegated);
    $cpu_delegated = str_replace('delegated:', '', $cpu_delegated);
    $cpu_delegated = str_replace('WAX', '', $cpu_delegated);
    $cpu_delegated = str_replace(' ', '', $cpu_delegated);

    return [
        'staked' => $cpu_staked,
        'delegated' => $cpu_delegated,
    ];
}

function getWalletBalance($response)
{
    $balance = $response[22];
    $balance = str_replace(' ', '', $balance);
    $balance = str_replace('liquid:', '', $balance);
    $balance = str_replace('WAX', '', $balance);
    return $balance;
}