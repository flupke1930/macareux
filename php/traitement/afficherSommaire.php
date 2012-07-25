<?php
include_once 'constantes.php';
include_once 'afficherArticleMode.php';

/**
 * * TODO: doc le cartouche
 * Permet la récupération des données XML du sommaire à partir des infos
 * contenu dans les articles (titre, résumé, etc.) et la façon de
 * les présenter (mode affichage).
 */



/**
 * * TODO: doc le cartouche
 * Transfert les information contenu dans le XML dans le HTML.
 * @param Noeud $div
 * @param Noeud $lesLignesXml
 * @param Tableau $atrbs attributs du style css
 * @param Tableau $ele
 */
function fabricationSommaireDIV($pageHtml,$corpsHtml,$lesLignesXml,$atrbs,$ele,$idPageAppel) {

	global $glb_chemin_relatif;

	/*
	 * Remplacement des éléments HTML par le contenu XML.
	 */

	try {
		if ($corpsHtml!=null){
			$div=$corpsHtml->getElementsByTagName("div")->item(0);
			$corpsHtml->removeChild($div);

			if ($div!=null){
				// On enlève le div qui correspond à un bloque de sommaire html.
				$div2=$div->getElementsByTagName("div")->item(0);
				$div->removeChild($div2);
				// On enlève le titre HTML statique du modèle.
				$div->removeChild($ele["h3"]);
				$lesGroupesXml=$lesLignesXml->item(0)->getElementsByTagName("groupe");
				// Récupération des attributs du DIV Html.
				$divAttribus=recuperationAttributs($div);
					
				//
				// Traitement du Groupe XML.
				//
				$i=0;
				foreach ($lesGroupesXml as $groupeXml) {
					// TODO : nettoyage
					//	echo "<br>---------------------------------------------------------";
					//	echo "<br>i=".$i++;
					//	echo "<br>";

					//debugXML($groupeXml);
					$div=$pageHtml->createElement("div");
					$div=initAttributs($div, $divAttribus);


					/*
					 * Boucle Traitement des lignes Groupe
					 * Récupération du contenu des articles.
					 * contenu (titre,resume,vignette)
					 * Pour chaque ligne du sommaire XML il faut récupérér les informations
					 * de l'article (titre, image, résumé).
					 */

					$h3 =$pageHtml->createElement("h3");
					$span =$pageHtml->createElement("span");
					$span->appendChild($pageHtml->createTextNode($groupeXml->getAttribute("titre")));
					$h3->appendChild($span);
					$div->appendChild($h3);
					//
					// boucle sur les articles
					//
					$lesPagesXml=$groupeXml->getElementsByTagName("page");

					foreach ($lesPagesXml as $pageXmlXml)
					{
						// Fabrication d'une ligne du sommaire.
						$articleXml=$pageXmlXml->getElementsByTagName("article")->item(0);

						//-------------------------
						// Récupération du contenu
						// de la ligne depuis le fichier article.
						//-------------------------
						$listeContenu=recuperationContenuLigne($idPageAppel, $articleXml->getAttribute("idref"));

						$div3 =$pageHtml->createElement("div");

						$img = $pageHtml->createElement("img");
						$img->setAttribute("src",$listeContenu["image"]["src"]);
						$img->setAttribute("width",$listeContenu["image"]["largeur"]);
						$img->setAttribute("length",$listeContenu["image"]["hauteur"]);


						$h4 = $pageHtml->createElement("h4");
						$h4->appendChild($pageHtml->createTextNode($listeContenu["titre"]));

						$p = $pageHtml->createElement("p");
						$span=$pageHtml->createElement("span");
						$span->appendChild($pageHtml->createTextNode( $listeContenu["resume"] ));
						$p->appendChild($span);
							


						//---------------------------
						// Creation du lien vers l'article.
						//---------------------------
						$a = $pageHtml->createElement("a");
						if ($atrbs["a"] ){
							foreach ($atrbs["a"] as $attr) {
								$a->setAttribute($attr->name,$attr->value);
							}
						}
						$pageXml= new DOMDocument();
						$pageXml->load($glb_chemin_relatif."/xml/page/page".$pageXmlXml->getAttribute("uri").".xml");
						$xpath = new DOMXPath($pageXml);
						$type = $xpath->query("/page/@type")->item(0)->value;
						$url=fabricationAffichageURL($pageXmlXml->getAttribute("uri"), $type);
						$a->setAttribute("href",$url);


						$span=$pageHtml->createElement("span");
						$span->appendChild($pageHtml->createTextNode("plus"));
						$a->appendChild($span);

						//
						//Ajout des attributs HTML (liés au modèle de présentation).
						//
						if ($atrbs["img"]){
							foreach ($atrbs["img"] as $attr) {
								$img->setAttribute($attr->name,$attr->value);
							}
						}

						if ($atrbs["h4"]){
							foreach ($atrbs["h4"] as $attr) {
								$h4->setAttribute($attr->name,$attr->value);
							}
						}

						if ($atrbs["p"] ){
							foreach ($atrbs["p"] as $attr) {
								$p->setAttribute($attr->name,$attr->value);
							}
						}

						$div3->appendChild($img);
						$div3->appendChild($h4);
						$p->appendChild($a);
						$div3->appendChild($p);
						$div->appendChild($div3);
				//		debugXML($div);
					}// boucle article

					$corpsHtml->appendChild($div);

				}//boucle grp


			}//if
		}//if

	} catch (Exception $e) {

		echo "<pre>";
		echo $e;
		echo $e->getMessage();
		print_r( $e->getTrace());
		echo "</pre>";
	}

	return $corpsHtml;

}


/**
 ** TODO: doc le cartouche
 * Fabrication du sommaire.
 * @param DOMElement $pageHtml
 * @param DOMDocument $xml
 */
function fabricationSommaire($pageHtml,$xml,$idPage) {
	global $glb_chemin_relatif ;

	/*
	 * Récupération du sommaire dans le fichier XML.
	 */
	$xpath_xml = new DOMXPath($xml);
	$lesLignesXml=$xpath_xml->query("/page/liste[@id='sommaire']");

	/*
	 * Récupération du sommaire HTML dans le modèle.
	 */
	$xpath_html = new DOMXPath($pageHtml);
	$div=$xpath_html->query("//div[@id='corps-de-page']")->item(0);

	/*
	 * Si le sommaire existe dans le modèle.
	 */
	if ($div!=null){
		/*
		 * Récupération des éléments enfant de l'élément DIV du HTML.
		 * */
		$ele['h3']=$xpath_html->query("//div[@id='sommaire']/h3")->item(0);
		$ele['img']=$xpath_html->query("//div[@id='sommaire']/div/img")->item(0);
		$ele['h4']=$xpath_html->query("//div[@id='sommaire']/div/h4")->item(0);
		$ele['p']=$xpath_html->query("//div[@id='sommaire']/div/p")->item(0);
		$ele['a']=$xpath_html->query("//div[@id='sommaire']/div/p/a")->item(0);

		/*
		 * Recopie des attributs HTML de chaque élément.
		 */
		$atrbs["img"]=recuperationAttributs($ele['img']);
		$atrbs["h4"]=recuperationAttributs($ele['h4']);
		$atrbs["p"]=recuperationAttributs($ele['p']);
		$atrbs["a"]=recuperationAttributs($ele['a']);


		$div=fabricationSommaireDIV($pageHtml,$div,$lesLignesXml,$atrbs,$ele,$idPage);
	}
	return $pageHtml;
}


/**
 ** TODO: doc le cartouche
 * Fabrication d'une liste HTML UL,LI,etc.
 * @param unknown_type $ul
 * @param unknown_type $lesLignes
 * @param unknown_type $attrs
 */
function fabricationUL($pageHtml,$ul,$lesEntreesXml) {
	global $glb_chemin_relatif ;
	/*
	 * Remplacement des éléments HTML par le contenu XML.
	 */
	if ($ul!=null){

		//Supprime les noeuds enfants.
		noeudSupprLesEnfants($ul);
		// Entrées du menu
		foreach ($lesEntreesXml as $ligne) {
			$li =$pageHtml->createElement("li");
			$a =$pageHtml->createElement("a");
			$span=$pageHtml->createElement("span");
			$span->appendChild($pageHtml->createTextNode($ligne->firstChild->nodeValue));

			//Récupération des attributs de style dans le xml
			$lesAttributs=recuperationAttributs($ligne);

			// Boucle sur les attributs
			foreach ($lesAttributs as $attribut) {
				if ($attribut->name=="idRef") {
					$pageXml= new DOMDocument();
					$pageXml->load($glb_chemin_relatif."/xml/page/page".$attribut->value.".xml");
					//Récupération des entree;
					$xpath = new DOMXPath($pageXml);
					$type = $xpath->query("/page/@type")->item(0)->value;
					$url=fabricationAffichageURL($attribut->value, $type);
					$a->setAttribute("href",$url);
				} else {
					$a->setAttribute($attribut->name,$attribut->value);
				}
			}
			$a->appendChild($span);
			$li->appendChild($a);
			$ul->appendChild($li);
		}
	}

	return $ul;

}


/**
 * * TODO: doc le cartouche
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
	$affichageType=recupAffichageMode($xml_xpath, $idPageAppel);
	// Récupération des type de contenu à afficher (titre/resumé/etc.).
	$listeContenu=recupContenuType($xml_xpath,$affichageType);


	//	echo "#####################listeContenu LIGNE $idPageAppel,$idArticle #############################<pre>";
	//	var_dump($listeContenu);
	//	echo "</pre>";

	return $listeContenu;
}
?>