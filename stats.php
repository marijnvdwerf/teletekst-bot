<?php

/*$json = json_decode(file_get_contents("stats/json"));
$json;
*/

if($chat_id == "12345678") {

$totaal = 0;
$hits = 0;

$files = scandir('/var/www/telegram-config');
foreach($files as $file) {
$totaal = $totaal + 1;

$pref = json_decode(file_get_contents("/var/www/telegram-config/".$file), true);

$hits = $hits + $pref["total"];

}

$eigenpref = json_decode(file_get_contents("/var/www/telegram-config/12345678"), true);
$andertotal = $hits - $eigenpref["total"];

$uptime = shell_exec("uptime");

$gemiddeld = $hits / $totaal;
$gemiddeld = round($gemiddeld,2);

$andergemiddeld = $andertotal / $totaal;
$andergemiddeld = round($andergemiddeld,2);

$reply = "Aantal gesprekken: ".$totaal."
Aantal berichten: ".$hits."
Gemiddeld aantal berichten per user: ".$gemiddeld."

Aantal eigen berichten: ".$eigenpref["total"]."

Aantal hits van anderen: ".$andertotal."
Gemiddelde van anderen: ".$andergemiddeld."

$uptime

Laatste:
";

//$reply .= file_get_contents("log.txt");

}

else { $reply = "Geen toegang"; }

//echo $reply;

?>
