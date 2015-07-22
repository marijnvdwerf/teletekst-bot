<?php

require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$offset = loadOffset();

$app = new \schellie\teletekstbot\App();

function updateOffset($updateID)
{
    global $offset;
    $offset = $updateID + 1;

    saveOffset($offset);
}

function saveOffset($offset)
{
    file_put_contents(__DIR__ . '/storage/offset.txt', $offset);
}

function loadOffset()
{
    if (!file_exists(__DIR__ . '/storage/offset.txt')) {
        return 0;
    }

    intval(file_get_contents(__DIR__ . '/storage/offset.txt'));
}


$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://api.telegram.org/bot' . getenv('TELEGRAM_TOKEN') . '/',
]);

// Disable webhook
$client->post('setWebhook', ['form_params' => ['url' => '']]);

while (true) {
    $response = $client->get('getUpdates', ['query' => ['offset' => $offset, 'timeout' => 30]]);

    $json = json_decode($response->getBody(), false, 512, JSON_BIGINT_AS_STRING);

    if ($json->ok !== true) {
        throw new Exception('Not OK');
    }

    foreach ($json->result as $update) {
        updateOffset($update->update_id);

        $app->processUpdate($update);
    }
}
