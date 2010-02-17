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

function exec_abonne_edit(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;
	
	$id_auteur = _request('id_auteur');
	$confirm = _request('confirm');
	$suppr_auteur = _request('suppr_auteur');
	$id_liste = _request('id_liste');
	$effacer_definitif = _request('effacer_definitif');
	$nom = _request('nom');
	$email = _request('email');
	
	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 
		
	// Admin SPIP-Listes
	echo debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
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
	
	// MODE ABONNE: gestion d'un abonne---------------------------------------------
	
	if($confirm == 'oui'){
		$type_abo = _request('suppl_abo');
		if($type_abo=='non') spiplistes_desabonner($id_auteur);
		
		 spip_query("UPDATE `spip_auteurs_elargis` SET `spip_listes_format`="._q($type_abo)." WHERE `id_auteur` ="._q($id_auteur));	
	}
	
	$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur="._q($id_auteur));
	
	if ($row = spip_fetch_array($result)) {
		$id_auteur=$row['id_auteur'];
		$nom=$row['nom'];
		$bio=$row['bio'];
		$email=$row['email'];
		$nom_site_auteur=$row['nom_site'];
		$url_site=$row['url_site'];
		$login=$row['login'];
		$pass=$row['pass'];
		$statut=$row['statut'];
		$pgp=$row["pgp"];
		$messagerie=$row["messagerie"];
		$imessage=$row["imessage"];
		$low_sec = $row["low_sec"];
		
		echo "<div align='center'>";
		gros_titre($nom);
		echo "</div>";
		
		if ($statut == "0minirezo")
			$logo = "redacteurs-admin-24.gif";
		elseif ($statut == "5poubelle")
			$logo = "redacteurs-poubelle-24.gif";
		else
			$logo = "redacteurs-24.gif";
		
		if (strlen($email) > 2 OR strlen($bio) > 0 OR strlen($nom_site_auteur) > 0 OR ($champs_extra AND $extra)) {
			echo debut_cadre_relief("$logo");
			echo "<font face='Verdana,Arial,Sans,sans-serif'>";
			if (strlen($email) > 2) 
				echo _T('email_2')." <b><a href='mailto:$email'>$email</a></b><br /> ";
			if (strlen($nom_site_auteur) > 2)
				echo _T('info_site_2')." <b><a href='$url_site'>$nom_site_auteur</a></b>";
			echo "<p>".propre($bio)."</p>";
			echo "</font>";
			echo fin_cadre_relief();
			
				echo debut_cadre_relief("$logo");
				
				echo"<form action='?exec=abonne_edit' method='post'>";
				
				$abo = spip_fetch_array(spip_query("SELECT `spip_listes_format` FROM `spip_auteurs_elargis` WHERE `id_auteur`=$id_auteur")) ;		
		//var_dump($abo);die("coucou");
		$abo = $abo["spip_listes_format"];
				$c1 = ( $abo == 'html')? 'checked=checked)': '';
				$c2 = ( $abo == 'texte')? 'checked=checked': '';
				$c3 = ( $abo == 'non')? 'checked=checked': '';
				echo'<div style="text-align: left;">';
				echo'<strong>Format :</strong><br>';
				echo'<input name="suppl_abo" value="html" '.$c1.'  type="radio">'._T('spiplistes:html').'<br>';
				echo'<input name="suppl_abo" value="texte" '.$c2.' type="radio">'._T('spiplistes:texte').'<br>';
				echo'<input name="suppl_abo" '.$c3.' value="non" type="radio">'._T('spiplistes:desabonnement').'<br>';
				echo'</div>';
				echo"<p>";
				echo"<input type='submit' name='Valider' value='"._T('spiplistes:modifier')."'>";
				echo"<input type='hidden' name='id_auteur'  value=$id_auteur >";
				echo"<input type='hidden' name='confirm'  value='oui' >";
				echo"</p>";
				echo"</form>";
				echo fin_cadre_relief();
			
		}
		
		
		echo "<p>";
		if ($connect_statut == "0minirezo") $aff_art = "'prepa','prop','publie','refuse'";
		else if ($connect_id_auteur == $id_auteur) $aff_art = "'prepa','prop','publie'";
		else $aff_art = "'prop','publie'";
	}
	
	echo spiplistes_afficher_en_liste(_T('spiplistes:abonne_listes'), _DIR_PLUGIN_SPIPLISTES.'img_pack/stock_mail.gif', 'abonnements', '', '', 'position') ;

	if ($statut == '6forum'){
		$retour = generer_url_ecrire('abonnes_tous');
		$action = generer_action_auteur('spiplistes_supprimer_abonne',$id_auteur,$retour);
		echo debut_cadre_relief("$logo");
		echo "<h3>"._T('spiplistes:supprime_contact')."</h3>";
		echo "<form action='$action' method='post'>";
		echo form_hidden($action);
		echo "<p align='center'>";
		echo "<input type='submit' name='Valider' value='"._T('spiplistes:supprime_contact_base')."'>";
		echo "</p>";
		echo "</form>";
		echo fin_cadre_relief();
	}
	
	
	//MODE ABONNE FIN abonne -------------------------------------------------------
	
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