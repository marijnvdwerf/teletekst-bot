<?php

require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://api.telegram.org/bot' . getenv('TELEGRAM_TOKEN') . '/',
]);

try {
    // set webhook
    //$client->post('setWebhook', ['form_params' => ['url' => 'https://188.166.109.170:80/webhook']]);

    // disable webhook
    //$client->post('setWebhook', ['form_params' => ['url' => '']]);
} catch (GuzzleHttp\Exception\ClientException $e) {
    dump($e);
    dump((string)$e->getResponse()->getBody());
}
