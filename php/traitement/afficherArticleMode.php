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
		$listeContenu[$contenuType->nodeValue]=recuperationContenu($xml_xpath, $contenuType);
	}
	return $listeContenu;
}

/**
 * * TODO: doc le cartouche
 * Récupère de l'élément qui doit être afficher.
 */
function recuperationContenu($xml_xpath,$contenuType){

	//
	// En fonction des attributs de
	// l'élément "article/mode/contenu"
	// on récupère les champs.
	//
	$attbr=$contenuType->attributes;
	if ($attbr->length>0){
		$req="//article/".trim($contenuType->nodeValue)."[@".$attbr->item(0)->name."='".$attbr->item(0)->value."']";
		echo "<br/>req XPATH = ".$req;
		$ele=$xml_xpath->query($req)->item(0);
		debugXML($ele);
//*
		if ($ele->hasAttributes()) {
			foreach ($ele->attributes as $a) {
				$contenu[$a->name][$a->value]=$ele->firstChild->nodeValue;
			}
		}
	//*/	
		
		
		echo "<pre>";
		var_dump($contenu);
		echo "</pre>";
	} else {
		// Autres contenus sans attribut.
		//*
		$enfants=$xml_xpath->query("/article/".trim($contenuType->nodeValue)."/paragraphe");
		echo "<br/> champ=".$contenuType->nodeValue." ".count($enfants);
		debugXML($enfants);
		if ($enfants->length>0) {
			$tbl=array();
			foreach ($enfants as $enfant){
				$tbl[]= $enfant->nodeValue;
			}
			$contenu["paragraphe"]=$tbl;
		} else {
			$noeud=$xml_xpath->query("//article/".trim($contenuType->nodeValue))->item(0);
			echo "<br/>+++".$noeud->nodeValue;
			$contenu=$noeud->nodeValue;
		}
	}
	//*/
	return $contenu;
}

?>