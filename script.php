<?php

include("telegram.php");
include("teletekst.php");

// Hier zeggen we wat terug
$reply = utf8_encode($reply);
$reply = urlencode($reply); // omdat we via een API terugpraten moeten de strings urlencoded zijn



$request = $url.$token."/sendMessage?text=".$reply."&chat_id=".$chat_id; // Hier maken we het bericht, in de goeie chat

//$request .= "&reply_markup={\"keyboard\":[[\"vorige\",\"101\",\"volgende\"]],\"one_time_keyboard\":false,\"resize_keyboard\":true}";

$request .= "&reply_markup={\"hide_keyboard\":true}";

fopen($request, "r"); // open het URL en doe verder niets

//file_put_contents('log.txt', $request);

?>
Dit is een Telegram-bot! telegram.me/teletekstbot info? schellevis.net
