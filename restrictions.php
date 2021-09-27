<?php
$whiteIP = [
    '37.140.192.80',
    '95.105.113.135',
    '45.84.225.246',
    '5.44.169.244'
];

if (!in_array($_SERVER['REMOTE_ADDR'], $whiteIP)){
    die(403);
}