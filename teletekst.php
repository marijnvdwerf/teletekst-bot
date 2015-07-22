<?php

include("config.php");


//$chat_id ="100";
//$c = $_GET["c"];
$c = $text;
$c = str_replace("/","",$c);
$c = str_replace("@teletekstbot","",$c);

// Start

if($c == "start") {
  $reply = $welkomsttekst;
}

elseif($c == "help") {
  $reply = $welkomsttekst;
}

elseif($c == "about") {
  $reply = $abouttekst;
}

elseif($c == "stats") {
 include("stats.php");
}

elseif(is_numeric($c)) {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
$pag = $c;
}

/*elseif(eregi("-",$c)) {
	$pag = $c;
}*/

/*
elseif(eregi("tt",$c)) {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
$pag = str_replace("/tt ","",$c);
}*/

elseif($c == "volgende") {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
  $pag = $pref["next"];
}

elseif($c == "vorige") {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
  $pag = $pref["prev"];
}


elseif($c == "subvorige") {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
  $pag = $pref["prevsub"];
}


elseif($c == "subvolgende") {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
  $pag = $pref["nextsub"];
}

elseif($c == "herladen") {
  $pref = json_decode(file_get_contents($config_url.$chat_id), true);
  $pag = $pref["current"];
}

else {
  $reply = $welkomsttekst;
}


// Teletekst-parser
if($pag) {
$turl = "http://teletekst-data.nos.nl/json/".$pag;

$json = file_get_contents($turl) or $fail = true;

if(!isset($fail)) {


$data = json_decode($json);

$total = $pref["total"];
$total = $total + 1;

$nav = array(
'current'=>$pag,
'prev'=>$data->{'prevPage'},
'next'=>$data->{'nextPage'},
'prevsub'=>$data->{'prevSubPage'},
'nextsub'=>$data->{'nextSubPage'},
'total'=>$total
);
file_put_contents($config_url.$chat_id, json_encode($nav));


$pagina = strip_tags($data->{'content'});
$pagina = html_entity_decode($pagina);
$pagina = utf8_decode($pagina);
$pagina = str_replace("????","",$pagina);
$pagina = str_replace("???","",$pagina);
$pagina = str_replace("??","",$pagina);
$pagina = str_replace(" volgende  nieuws  weer&verkeer  sport","",$pagina);
$pagina = str_replace("  ","",$pagina);
$pagina = str_replace(".",". ",$pagina);
$pagina = str_replace(":",": ",$pagina);
$pagina = str_replace(",",", ",$pagina);
$pagina = str_replace(";","; ",$pagina);
$pagina = trim($pagina);
$pagina = str_replace( "\n\n", "--XXXNEWLINE--", $pagina);
$pagina = preg_replace( "/\r|\n/", "", $pagina);
$pagina = str_replace( "--XXXNEWLINE--", "\n\n", $pagina);

$pagina = str_replace("OPMERKELIJK","OPMERKELIJK\n\n",$pagina);
$pagina = str_replace("kort nieuws","Kort nieuws\n",$pagina);
$pagina = str_replace( "OETBAL", "\nVOETBAL\n", $pagina);
$pagina = str_replace("\n ","\n",$pagina);
$pagina = str_replace("\n\n\n","\n\n",$pagina);
$pagina = str_replace("volgendenieuwssportopmerkelijk","",$pagina);
$pagina = str_replace("nieuwssporttv gidsweer","",$pagina);
$pagina = str_replace("buitenlandnieuwssportopmerkelijk","",$pagina);
$pagina = str_replace("- www. nos. nl binnenland sport verkeer weer","",$pagina);
$pagina = str_replace("volgende nieuws sport voetbal","",$pagina);
$pagina = str_replace("vooruitzicht nieuws index sport","",$pagina);
$pagina = str_replace("volgendeopmerkelijknieuwssport","",$pagina);


$index = array('100','100-1','100-2','100-3','101','102','103','400','401','402','700','701','800','801','500','501','502','503','600','601','602');

if(in_array($pag,$index)) {


  $pagina = preg_replace('/(?<!\d)(\d{3})(?!\d)/', ' /$1
  ', $pagina);
  $pagina = str_replace("\n\n","\n",$pagina);
  $pagina = str_replace("/102\n","/102",$pagina);
  $pagina = str_replace("
 ","\n",$pagina);
// $pagina = str_replace("NOS Teletekst /","NOS Teletekst ",$pagina);
}

$pagina .= "\n\n /vorige   -   /101   -   /volgende";

if($data->{'prevSubPage'} != "") {
	$pagina .= "
	Vorige subpagina: /subvorige";
}

if($data->{'nextSubPage'} != "") {
	$pagina .= "
	Volgende subpagina: /subvolgende";
}
	


$reply = $pagina;

}

elseif($fail) { $reply = "Die Teletekst-pagina heb ik helaas niet kunnen vinden."; }

}


?>
