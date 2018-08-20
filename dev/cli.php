<?php

require_once '../vendor/autoload.php';

$pipe = new \anvein\pipedrive_sdk\Pipedrive('566efd2ed84fca09d4359948cfb2b89b3701549f', null, null, false);

$res = $pipe->person->update(
    1,
    'пест'
    [
        'aaa'
    ]

);

echo '<pre>';
print_r($res);
echo '</pre>';
die();