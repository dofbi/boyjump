<?php

include_spip ('base/spip-listes');

//nombre de processus d'envoi simultanes
@define('_SPIP_LISTE_SEND_THREADS',1);

function spiplistes_log($texte){
	spip_log($texte,'spiplistes');
}

//Balises Spip-listes

function balise_MELEUSE_CRON($p) {
   $p->code = "''";
   $p->statut = 'php';
   return $p;
}


function calcul_DATE_MODIF_SITE() {
   $date_art=spip_query("SELECT date,titre FROM spip_articles WHERE statut='publie' ORDER BY date DESC LIMIT 0,1");
   $date_art=spip_fetch_array($date_art);
   $date_art= $date_art['date'];
   
   $date_bre=spip_query("SELECT date_heure,titre FROM spip_breves WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_bre=spip_fetch_array($date_bre);
   $date_bre= $date_bre['date_heure'];
   
   $date_modif= ($date_bre>$date_art)? $date_bre : $date_art ;   
   return  $date_modif;
}

function balise_DATE_MODIF_SITE($p) {
   $p->code = "calcul_DATE_MODIF_SITE()";
   $p->statut = 'php';
   return $p;
}


function calcul_DATE_MODIF_FORUM() {
   $date_f=spip_query("SELECT date_heure,titre FROM spip_forum WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_f=spip_fetch_array($date_f);
   $date_f= $date_f['date_heure'];
   
   return  $date_f;
}

function balise_DATE_MODIF_FORUM($p) {
   $p->code = "calcul_DATE_MODIF_FORUM()";
   $p->statut = 'php';
   return $p;
}

//utiliser le cron pour envoyer les messages en attente
function spiplistes_taches_generales_cron($taches_generales){
	$taches_generales['spiplistes_cron'] = 10 ;
	return $taches_generales;
}

$spiplistes_v = $GLOBALS['meta']['spiplistes_version'] ;

//afficher la version de spip_listes dans le pied de page
if($spiplistes_v == 1.91)
$GLOBALS['spiplistes_version'] = "SPIP-listes 1.9.1";
if($spiplistes_v >= 1.92)
$GLOBALS['spiplistes_version'] = "SPIP-listes $spiplistes_v";


include_spip('inc/options_spip_listes');
?>