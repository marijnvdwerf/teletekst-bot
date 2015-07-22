<?php

require '../vendor/autoload.php';

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

$bot = new \schellie\teletekstbot\App();

$app = new \Slim\Slim(['debug' => true]);

$app->get('/', function () {
    echo 'Dit is een Telegram-bot! <a href="https://telegram.me/teleplaatjebot">telegram.me/teleplaatjebot</a>';
});

$app->post('/webhook', function () use ($app, $bot) {
    $cloner = new VarCloner();
    $dumper = new CliDumper(fopen('webhook.txt', 'w'));
    $dumper->dump($cloner->cloneVar($app->request->getBody()));

    $bot->processUpdate(json_decode($app->request->getBody(), false, 512, JSON_BIGINT_AS_STRING));
});

$app->run();
