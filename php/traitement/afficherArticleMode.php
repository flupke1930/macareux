<?php

/**
 * * TODO: doc le cartouche
 * Regarde la page qu'il appelle.
 * Récupère le mode d'affichage.
 */
function recupAffichageMode($xml_xpath,$idPageAppel) {
	$page= $xml_xpath->query("//article/page[@idref='".$idPageAppel."']")->item(0);
	$affichage=-1;

	if ($page!=null){
		if ($page->hasAttributes()){
			if ($page->attributes->item(1)!=null){
				$affichage=(int) $page->attributes->item(1)->nodeValue;
			}
		}
	}
	return $affichage;
}

/**
 * * TODO: doc le cartouche
 * Que doit-on afficher comme information pour une ligne de sommaire.
 * Récupération des types de contenu à afficher (titre,résumé,image,etc.)
 */
function recupContenuType($xml_xpath,$type){
	$mode= $xml_xpath->query("//article/affichage/mode[@type='".$type."']/contenu");
	$i=0;
	foreach ($mode as $contenuType) {
		if ($contenuType->hasAttributes()) {
			$attrbs = $contenuType->attributes;
		}
		$listeContenu[$contenuType->nodeValue]=recuperationContenu($xml_xpath, $contenuType->nodeValue,$attrbs);
	}
	return $listeContenu;
}

/**
 * * TODO: doc le cartouche
 * Récupère quel contenu doit être afficher.
 */
function recuperationContenu($xml_xpath,$champ,$attrbs){

	// s'il existe un attr il est utilisé pour retrouver le bon contenu.
	if ($attrbs){
		
		// Images
		foreach ($attrbs as $attrb){
			$ele=$xml_xpath->query("//article/".trim($champ)."[@taille='".$attrb->nodeValue."']")->item(0);
			if ($ele->hasAttributes()) {
				foreach ($ele->attributes as $a) {
					$contenu[$a->name]=$a->value;
				}
			}
		}
	} else {
		
		// Autres contenus.
		$enfants=$xml_xpath->query("/article/".trim($champ)."/paragraphe");
echo "<br/>".$champ." ".count($enfants);
		debugXML($enfants);
		if ($enfants->length>0) {
			$tbl=array();
			
			foreach ($enfants as $enfant){
			 $tbl[]= $enfant->nodeValue;
			}
			$contenu["paragraphe"]=$tbl;

		} else {
		$noeud=$xml_xpath->query("//article/".trim($champ))->item(0);
			echo "+++".$noeud->nodeValue;
			$contenu=$noeud->nodeValue;
		}
	}
	return $contenu;
}

?>