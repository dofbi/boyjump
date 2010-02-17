<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
*
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*
* Definition des balises #NOTATION_ETOILE{n}
*
**/
include_spip('inc/notation_util');

// Affichage des etoiles cliquables
function notation_etoile_click($nb, $id) { 
	$ret = '';
	if ($nb>0 && $nb<=0.5) {
		$nb=1;
	} else {
		$nb = round($nb);
	}
	// Recherche de l'image
	$img = find_in_path('img_pack/notation-on1.gif');
	$multi = ($img != '');
	if ($multi) { 
		$img0 = str_replace('-on1.gif','',$img);
	} else {
		$img = find_in_path('img_pack/notation-on.gif');
	}
	for ($i=1; $i<=$nb; $i++) {
		if ($multi) {
			$img = $img0.'-on'.$i.'.gif';
		}
		$ret .= "<button type='submit' name='note' value='$i' title='$i/".notation_get_nb_notes()."' onmouseover=\"notation_set_etoile($i,".notation_get_nb_notes().",$id)\" onmouseout=\"notation_set_etoile($nb,".notation_get_nb_notes().",$id)\"><img src='$img' id='star-$id-$i' alt='$i' title='' /></button>";
	}
	$img = str_replace('-on.gif','-off.gif',$img);
	for ($i=$nb+1; $i<=notation_get_nb_notes(); $i++) {
		if ($multi) {
			$img = $img0.'-off'.$i.'.gif';
		}
		$ret .= "<button type='submit' name='note' value='$i' title='$i/".notation_get_nb_notes()."' onmouseover=\"notation_set_etoile($i,".notation_get_nb_notes().",$id)\" onmouseout=\"notation_set_etoile($nb,".notation_get_nb_notes().",$id)\"><img src='$img' id='star-$id-$i' alt='$i' title='' /></button>";
	}
	$ret .= "<script type='text/javascript'>$(document).ready(function() { buttonfix(); });</script >";
	return $ret;
}

// Affichage d'un nombre sous forme d'etoiles
function notation_etoile($nb)
{ if ($nb>0 && $nb<=0.5) $nb=1;
  else $nb = round($nb);
  // Recherche de l'image
  $img = find_in_path('img_pack/notation-on1.gif');
  $multi = ($img != '');
  if ($multi) $img0 = str_replace('-on1.gif','',$img);
  else $img = find_in_path('img_pack/notation-on.gif');
  for ($i=1; $i<=$nb; $i++)
  {  if ($multi) $img = $img0.'-on'.$i.'.gif';
     $ret .= '<img src="'.$img.'" title="'._T('notation:note_'.$nb).'" style="vertical-align:middle" class="notation" />';
  }
  $img = str_replace('-on.gif','-off.gif',$img);
  for ($i=$nb+1; $i<=notation_get_nb_notes(); $i++)
  {  if ($multi) $img = $img0.'-off'.$i.'.gif';
     $ret .= '<img src="'.$img.'" title="'._T('notation:note_'.$nb).'" style="vertical-align:middle" class="notation" />';
  }
  return $ret;
}

// Lles balises

function balise_NOTATION_ETOILE($p){
	$param = interprete_argument_balise(1,$p);
	$p->code = "notation_etoile($param)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_NOTATION_ETOILE_CLICK($p){
	$nb = interprete_argument_balise(1,$p);
	$id = interprete_argument_balise(2,$p);
	$p->code = "notation_etoile_click($nb,$id)";
	$p->interdire_scripts = false;
	return $p;
}
?>