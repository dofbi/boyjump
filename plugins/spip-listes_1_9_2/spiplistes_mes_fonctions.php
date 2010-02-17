<?php

// Boucles SPIP-listes
global $tables_principales,$exceptions_des_tables,$table_date;


//
// <BOUCLE(LISTES)>
//
function boucle_LISTES($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_listes";  
        return calculer_boucle($id_boucle, $boucles);
}



//
// <BOUCLE(COURRIERS)>
//
function boucle_COURRIERS($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[] =  "spip_courriers AS $id_table";
        //$boucle->where[] = "type='nl'";
        $boucle->where[] = array("'='","'type'","'\"nl\"'"); 
        return calculer_boucle($id_boucle, $boucles);
}

// Filtres SPIP-listes
function supprimer_destinataires($texte) {
 return eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
}


function date_depuis($date) {
	    
	    if (!$date) return;
 	    $decal = date("U") - date("U", strtotime($date));
 	    
	    if ($decal < 0) {
 	        $il_y_a = "date_dans";
 	        $decal = -1 * $decal;
	    } else {
 	        $il_y_a = "spiplistes:date_depuis";
	    }
	    
	    if ($decal < 3600) {
 	        $minutes = ceil($decal / 60);
	        $retour = _T($il_y_a, array("delai"=>"$minutes "._T("date_minutes")));
	    }
	    else if ($decal < (3600 * 24) ) {
	        $heures = ceil ($decal / 3600);
 	        $retour = _T($il_y_a, array("delai"=>"$heures "._T("date_heures")));
 	    }
    else if ($decal < (3600 * 24 * 7)) {
 	        $jours = ceil ($decal / (3600 * 24));
 	        $retour = _T($il_y_a, array("delai"=>"$jours "._T("date_jours")));
	    }
	    else if ($decal < (3600 * 24 * 7 * 4)) {
	        $semaines = ceil ($decal / (3600 * 24 * 7));
 	        $retour = _T($il_y_a, array("delai"=>"$semaines "._T("date_semaines")));
	    }
	    else if ($decal < (3600 * 24 * 30 * 6)) {
 	        $mois = ceil ($decal / (3600 * 24 * 30));
 	        $retour = _T($il_y_a, array("delai"=>"$mois "._T("date_mois")));
 	    }
	    else {
 	        $retour = _T($il_y_a, array("delai"=>" ")).affdate_court($date);
 	    }
 	
 	
 	
 	    return $retour;
}

// http://doc.spip.org/@inc_editer_auteurs_dist
function inc_editer_auteurs($type, $id, $flag, $cherche_auteur, $ids, $titre_boite = NULL, $script_edit_objet = NULL) {
	global $options;
	$arg_ajax = "&id_{$type}=$id";
	//ligne rajout� au fork
	$arg_ajax .= "&type=".$type;
	//fin du fork du fichier
	
	if ($script_edit_objet===NULL)
	 $script_edit_objet = $type.'s';

	if ($titre_boite===NULL)
		$titre_boite = _T('texte_auteurs'). aide("artauteurs");
	else
		$arg_ajax.= "&titre=".urlencode($titre_boite);

	$cond_les_auteurs = "";
	$aff_les_auteurs = afficher_auteurs_objet($type, $id, $flag, $cond_les_auteurs, $script_edit_objet, $arg_ajax);

	if ($flag AND $options == 'avancees') {
		$futurs = ajouter_auteurs_objet($type, $id, $cond_les_auteurs,$script_edit_objet, $arg_ajax);
	} else $futurs = '';

	$ldap = isset($GLOBALS['meta']['ldap_statut_import']) ?
	  $GLOBALS['meta']['ldap_statut_import'] : '';

	return editer_auteurs_objet($type, $id, $flag, $cherche_auteur, $ids, $aff_les_auteurs, $futurs, $ldap,$titre_boite,$script_edit_objet, $arg_ajax);
}

?>