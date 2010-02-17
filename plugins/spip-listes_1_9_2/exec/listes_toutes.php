<?php

/******************************************************************************************/
/* SPIP-listes est un syst�me de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/affichage');


function exec_listes_toutes(){
	
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;

	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 
	
	// Admin SPIP-Listes
	echo debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	if (!function_exists('spip_listes_onglets')){
		echo(_T('spiplistes:erreur_install'));     
		echo fin_page();
		exit;
	}
	
	if ($connect_statut != "0minirezo" ) {
		echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
		echo fin_page();
		exit;
	}
	
	if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));
	}
	
	debut_gauche();
	
	spip_listes_raccourcis();
	
	creer_colonne_droite();
	
	debut_droite("messagerie");

	
	// MODE LISTES: afficher les listes --------------------------------------------
	
	/*
	debut_cadre_relief('forum-interne-24.gif');
	
	echo _T('spiplistes:nb_abonnes')."$nb_abonnes<p>";
	echo "<p>";
	fin_cadre_relief();
	*/
	
	echo "<p>";
	
	//
	// Afficher tableau de listes
	//
	
	
	//
	// lettres d'infos
	//
	
	
	echo spiplistes_afficher_en_liste(_T('spiplistes:listes_internes'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'listes', 'inact', '', 'position') ;
	echo spiplistes_afficher_en_liste(_T('spiplistes:liste_diff_publiques'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'listes', 'liste', '', 'position') ;
	echo spiplistes_afficher_en_liste(_T('spiplistes:listes_poubelle'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'listes', 'poublist', '', 'position') ;
	
	
	// MODE EDIT LISTES FIN --------------------------------------------------------
	
	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;
	echo fin_gauche(), fin_page();
}

/******************************************************************************************/
/* SPIP-listes est un syst�me de gestion de listes d'abonn�s et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/
?>