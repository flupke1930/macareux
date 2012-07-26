<?php
include_once 'constantes.php';
include_once 'afficherArticleMode.php';

/**
 * * TODO: doc le cartouche
 * Permet la récupération des données XML article à partir des infos
 * contenu dans les articles (titre, résumé, etc.) et la façon de
 * les présenter (mode affichage).
 */

/**
 * TODO: doc le cartouche
 * Récupération
 * @param  $idPageAppel
 * @param  $idArticle
 */
function recuperationArticleContenu($idPageAppel,$idArticle) {
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
	$affichageType=recupAffichageMode($xml_xpath, $idPageAppel);
	// Récupération des type de contenu à afficher (titre/resumé/etc.).
	$listeContenu=recupContenuType($xml_xpath,$affichageType);


//	echo "#####################listeContenu LIGNE $idPageAppel,$idArticle #############################<pre>";
//	var_dump($listeContenu);
//	echo "</pre>";

	return $listeContenu;
}



/**
 * Fabrication d'un article.
 * @param String $pageHtml
 * @param String $pageXml
 * @param int $idRef
 * @param int $idPage
 */
function fabricationArticle($pageHtml, $pageXml,$idRef,$idPage) {
	GLOBAL $glb_chemin_relatif;

	//---------------------------
	//
	// Récupération article HTML dans le modèle.
	//
	//---------------------------
	$xpath_html = new DOMXPath($pageHtml);
	$div=$xpath_html->query("//div[@id='actualite-detail']")->item(0);

	/*
	 * Si article existe dans le modèle.
	 */
	if ($div!=null){
		/*
		 * Récupération des éléments enfant de l'élément DIV du HTML.
		 * */
		$ele['h3']=$xpath_html->query("//div[@id='actualite-detail']/h3")->item(0);
		$ele['img']=$xpath_html->query("//div[@id='actualite-detail']/img")->item(0);
		//$ele['h4']=$xpath_html->query("//div[@id='actualite-detail']/h4")->item(0);
		$ele['p']=$xpath_html->query("//div[@id='actualite-detail']/p")->item(0);

		/*
		 * Recopie des attributs HTML de chaque élément.
		 */
		$atrbs["h3"]=recuperationAttributs($ele['h3']);
	//	$atrbs["h4"]=recuperationAttributs($ele['h4']);
		$atrbs["img"]=recuperationAttributs($ele['img']);
		$atrbs["p"]=recuperationAttributs($ele['p']);
		$atrbs["a"]=recuperationAttributs($ele['a']);

		$div=fabricationArticleDIV($pageHtml,$div,$pageXml,$atrbs,$ele,$idPage,$idRef);
	}
	//
	// Remplacement
	//

	return $pageHtml;
}


/**
 *
 * Fabrication du DIV contenant l'article.
 * @param String $pageHtml
 * @param String $div
 * @param String $pageXml
 * @param Table $atrbs
 * @param Table $ele
 * @param int $idPage
 * @param int $idRef
 */

function fabricationArticleDIV($pageHtml,$divActu,$pageXml,$atrbs,$ele,$idPage,$idRef)
{
	global $glb_chemin_relatif;
	$listeContenu=recuperationArticleContenu($idPage, $idRef);

	//Nettoyage du modèle HTML.
	noeudSupprLesEnfants($ele['h3']);
	$divActu->removeChild($ele['p']);
	
	
	// Attention : Positionnement de la date dans le code HTML en dure dans le code PHP
	$titre=$pageHtml->createTextNode($listeContenu["titre"]." [".$listeContenu["date"]["type"]["creation"]."]");
	$span=$pageHtml->createElement("span");
	$span->appendChild($titre);
	$ele['h3']->appendChild($span);
	$paragraphes=$listeContenu["corps"]["paragraphe"];
	
	foreach ($paragraphes as $para) {
		$p =$pageHtml->createElement("p");
		$p = initAttributs($p, $atrbs["p"]);
		$p->appendChild($pageHtml->createTextNode($para));
		$divActu->appendChild($p);
		
		
//	$ele['p']->appendChild($pageHtml->createTextNode($para));
	}
//TODO: reprendre les attributs
//TODO: faire une boucle sur les paragraphes
//TODO: image	
	
}



?>