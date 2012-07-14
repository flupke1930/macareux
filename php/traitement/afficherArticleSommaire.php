<?php
include_once 'constantes.php';

/**
 * Permet la récupération des données XML du sommaire à partir des infos
 * contenu dans les articles (titre, résumé, etc.) et la façon de
 * les présenter (mode affichage).
 */

/**
 * Regarde la page qu'il appelle.
 * Récupère le mode d'affichage.
 */
function recupAffichageType($xml_xpath,$idPageAppel) {
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
 * Récupère les données à afficher.
 */
function recuperationContenu($xml_xpath,$champ,$attrbs){

	// s'il existe un attr il est utilisé pour retrouver le bon contenu.
	if ($attrbs){
		foreach ($attrbs as $attrb){
			$ele=$xml_xpath->query("//article/".trim($champ)."[@taille='".$attrb->nodeValue."']")->item(0);
			if ($ele->hasAttributes()) {
				foreach ($ele->attributes as $a) {
					$contenu[$a->name]=$a->value;
				}
			}
		}
	} else {
		$contenu=$xml_xpath->query("//article/".trim($champ))->item(0)->nodeValue;
	}
	return $contenu;
}

/**
 * Fabrication d'une ligne du sommaire.
 * @param  $idPageAppel
 * @param  $idArticle
 */
function recuperationContenuLigne($idPageAppel,$idArticle) {
	global $glb_chemin_relatif ;
	//Récupération de l'origine de l'appel (id de page)

	/*
	 * Article (xml).
	 * */
	$article= new DOMDocument();
	$article->load($glb_chemin_relatif."/xml/article/article".$idArticle.".xml");
	$xml_xpath = new DOMXPath($article);
	// Regarde la page qu'il appelle.
	// Récupère le mode d'affichage (sommaire/article).
	$affichageType=recupAffichageType($xml_xpath, $idPageAppel);
	// Récupération des type de contenu à afficher (titre/resumé/etc.).
	$listeContenu=recupContenuType($xml_xpath,$affichageType);


	//	echo "#####################listeContenu LIGNE $idPageAppel,$idArticle #############################<pre>";
	//	var_dump($listeContenu);
	//	echo "</pre>";


	return $listeContenu;
}


/**
 * Test unit
 */
function test(){
	$idArticle="0101";
	$idPageAppel="0501";
	$listeChamp=recuperationContenuLigne($idPageAppel, $idArticle);

	//	echo "==========listeChamp===================<br>";
	//	print_r($listeChamp);
}
?>