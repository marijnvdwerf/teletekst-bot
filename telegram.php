<?php

// Telegrambot

// Config
$token = ""; //bot-token
$url = "https://api.telegram.org/bot"; //url van de Telegram-API

// Ophalen post-info
$post = file_get_contents('php://input');
$incoming = json_decode($post);

// Chat_id, nodig voor binnenkomende berichten
$chat_id = $incoming->message->chat->id;

// Dit is de tekst van het binnenkomende bericht
$text = $incoming->message->text;



?>
