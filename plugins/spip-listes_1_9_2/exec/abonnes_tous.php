<?php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');
include_spip('inc/affichage');


function exec_abonnes_tous(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;
	$type = _request('type');
	$new = _request('new');
	$id_auteur = _request('id_auteur');
	
	$options = 'avancees' ;
	
	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 
	
	// Admin SPIP-Listes
	echo debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");
	
	if ($connect_statut != "0minirezo" ) {
		echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
		echo fin_page();
		exit;
	}
	
	if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur))
		spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));
	
	debut_gauche();
	spip_listes_raccourcis();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	//
	// Recherche d'auteur
	//
	
	spiplistes_cherche_auteur();
	
	//Compter tous les abonnes a des listes 
	$result_pile = spip_query(
	  'SELECT listes.statut, COUNT(abonnements.id_auteur)
	   FROM spip_listes AS listes LEFT JOIN spip_auteurs_listes AS abonnements USING (id_liste)
	   GROUP BY listes.statut');
	$nb_abonnes = array();
	while ($row = spip_fetch_array($result_pile, SPIP_NUM)) {
		$nb_abonnes[$row[0]] = intval($row[1]);
	}

	//evaluer les formats de tous les auteurs + compter tous les auteurs
	$result = spip_query('SELECT `spip_listes_format` , COUNT(`spip_listes_format`) FROM `spip_auteurs_elargis` GROUP BY `spip_listes_format`');
	$nb_inscrits = 0;

	//repartition des formats
	$cmpt = array('texte'=>0, 'html'=>0, 'non'=>0);
	
	while ($row = spip_fetch_array($result, SPIP_NUM)) {
		$nb_inscrits += $row[1];
		$abo =$row[0];
		if ($abo) {
			$cmpt[$abo] += $row[1];
		}
	}
	
	//Total des auteurs qui ont un format html ou texte
	$total_abo = $cmpt['html'] + $cmpt['texte'] ;
	
	
	//Total des auteurs qui ne sont pas abonnes a une liste
	$abonnes = spip_query("select a.id_auteur, count(d.id_liste) from spip_auteurs a  
	      left join spip_auteurs_listes d on a.id_auteur =  
	          d.id_auteur group by a.id_auteur having count(d.id_liste) = 0;"); 
	$nb_abonnes_auc = spip_num_rows($abonnes);
	
	echo debut_cadre_relief('forum-interne-24.gif');

	echo"<div>";
	echo"<div style='float:right;width:150px'>";
	echo "<b>"._T('spiplistes:repartition')."</b>  <br /><b>"._T('spiplistes:html')."</b> : {$cmpt['html']} <br /><b>"._T('spiplistes:texte')."</b> : {$cmpt['texte']} <br /><b>"._T('spiplistes:desabonnes')."</b> : {$cmpt['non']}";
	echo"</div>";
	
	$total = $cmpt['html'] + $cmpt['texte'] + $cmpt['non']; // ?
	
	echo _T('spiplistes:nbre_abonnes'). $total_abo .
	  _T('spiplistes:abonnes_liste_pub') . $nb_abonnes['liste'] .
	  _T('spiplistes:abonnes_liste_int') . $nb_abonnes['inact'] .
	 "<br />". _T('spiplistes:abonne_aucune_liste') . " : ". ($nb_abonnes_auc - $cmpt['non']) . "</p>";
	
	echo"</div>";
	
	$result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' LIMIT 2");
	if (spip_num_rows($result) > 1) {
		echo "<form action='?exec=abonnes_tous' METHOD='post'>";
		echo "<div align=center>\n";
		echo "<input type='text' name='cherche_auteur' class='fondl' value='' size='20' />";
		echo " <input type='submit' name='Chercher' value='"._T('bouton_chercher')."' class='fondo' />";
		echo "</div></form>";
	}
	
	echo fin_cadre_relief();
	
	echo "<p>";
	
	// auteur
	
	$retour = generer_url_ecrire("abonnes_tous");
	
	$tri = _request('tri') ? _request('tri') : 'nom';
	$retour = parametre_url($retour,"tri",$tri);
	
	//
	// Construire la requete
	//
	
	$sql_visible="1=1"; 
	$partri = " " . _T('info_par_tri', array('tri' => $tri));
	
	$sql_sel = '';
	
	// tri
	switch ($tri) {
		case 'nombre':
			$sql_order = ' ORDER BY compteur DESC, unom';
			$type_requete = 'nombre';
			$partri = " "._T('info_par_nombre_article');
			break;
		case 'statut':
			$sql_order = ' ORDER BY statut, login = "", unom';
			$type_requete = 'auteur';
			break;
		case 'nom':
			$sql_order = ' ORDER BY unom';
			$type_requete = 'auteur';
			break;
		case 'email':
			$sql_order = ' ORDER BY LOWER(email)';
			$type_requete = 'auteur';
			break;
		case 'multi':
		default:
			$type_requete = 'auteur';
			$sql_sel = ", ".creer_objet_multi ("nom", $spip_lang);
			$sql_order = " ORDER BY multi";
	}

	
	//
	// La requete de base est tres sympa
	//
	
	$query = "SELECT
		aut.id_auteur AS id_auteur,
		aut.statut AS statut,
		aut.login AS login,
		aut.nom AS nom,
		aut.email AS email,
		aut.url_site AS url_site,
		aut.messagerie AS messagerie,
		UPPER(aut.nom) AS unom,
		count(lien.id_liste) as compteur
		$sql_sel
		FROM spip_auteurs as aut
		LEFT JOIN spip_auteurs_listes AS lien ON aut.id_auteur=lien.id_auteur
		LEFT JOIN spip_listes AS art ON (lien.id_liste = art.id_liste)
		WHERE
		$sql_visible
		GROUP BY aut.id_auteur
		$sql_order";

	spiplistes_afficher_auteurs($query, generer_url_ecrire('abonnes_tous'));

	// MODE STATUT FIN -------------------------------------------------------------
	
	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;
	echo fin_gauche(), fin_page();
}

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
?>