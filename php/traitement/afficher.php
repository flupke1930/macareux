<?PHP
include_once 'constantes.php';
include_once 'xmlUtilitaire.php';
include_once 'afficherArticleSommaire.php';

/**
 * Permet la transformation d'un fichier de contenu en XML en page xHTML (utilisation d'un modèle).
 * Actuellement on a encore du code en dure par rapport au id xhtml.
 */


/**
 *
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
 *
 * fabrication d'une URL pour l'affichage d'une page HTML.
 * @param unknown_type $idPage
 * @param unknown_type $type
 */
function fabricationAffichageURL($idPage,$type){
	$url="afficher.php?idPage=".$idPage."&cheminModele=".$type;
	return $url;
}

/**
 * Affichage en debug du XML
 * */
function debugXML($obj ){




	//echo "---debugXML()---<br>";
	//var_dump($obj);

	$doc = new DOMDocument('1.0',"UTF-8");
	if (get_class($obj)=="DOMNodeList"){

		$i=0;
		foreach ($obj as $l)
		{
			//	echo "--".$i++;
			$n=$doc->importNode($l,true);
			$doc->appendChild($n);
		}
	}
	if (get_class($obj)=="DOMElement"){
		foreach ($obj->childNodes as $l)
		{
			$n=$doc->importNode($l,true);
			$doc->appendChild($n);
		}

	}

	//	echo "<br>------<pre>".htmlentities($doc->saveXML());
	//	echo "</pre>------";
}

/**
 * Fabrication du menu
 * TODO: Vérifier la présence des attributs.
 */
function fabricationMenu($pageHtml,$idref,$idMenu) {
	global $glb_chemin_relatif ;
	try {
		//
		$menu= new DOMDocument();
		$menu->load($glb_chemin_relatif."xml/menu/menu".substr($idref,0,4).".xml");
		// Localisation du menu (div) dans le modèle HTML.
		$pageHtml_xpath = new DOMXPath($pageHtml);
		$ul=$pageHtml_xpath->query("//div[@id='".$idMenu."']/ul")->item(0);
		// Récupération des entree;
		$xpath = new DOMXPath($menu);
		$lesEntreesXml = $xpath->query("/menu/entree");
		$ul=fabricationUL($pageHtml,$ul,$lesEntreesXml);




	} catch (Exception $e) {
		echo $e;
	}
	return $pageHtml;
}


/**
 * Transfert les information contenu dans le XML dans le HTML.
 * @param Noeud $div
 * @param Noeud $lesLignesXml
 * @param Tableau $atrbs attributs du style css
 * @param Tableau $ele
 */
function fabricationDIV($pageHtml,$corpsHtml,$lesLignesXml,$atrbs,$ele,$idPageAppel) {

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

					debugXML($groupeXml);
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
						// Récupération de la ligne depuis le fichier article.
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
							
						$a = $pageHtml->createElement("a");
						//*
						if ($atrbs["a"] ){
							foreach ($atrbs["a"] as $attr) {
								$a->setAttribute($attr->name,$attr->value);
							}
						}
						//*/


						// Il faut passer par l'article.

						$pageXml= new DOMDocument();
						$pageXml->load($glb_chemin_relatif."/xml/page/page".$pageXmlXml->getAttribute("uri").".xml");
						$xpath = new DOMXPath($pageXml);
						$type = $xpath->query("/page/@type")->item(0)->value;
						$url=fabricationAffichageURL($pageXmlXml->getAttribute("uri"), $type);
						$a->setAttribute("href",$url);


						$span=$pageHtml->createElement("span");
						$span->appendChild($pageHtml->createTextNode("plus"));
						$a->appendChild($span);
						//Ajout des attributs HTML (liés au modèle de présentation).
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
						debugXML($div);
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
 *
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


		//TODO: pb lien vers article ou bien vers la page.
		$div=fabricationDIV($pageHtml,$div,$lesLignesXml,$atrbs,$ele,$idPage);
	}
	return $pageHtml;
}

/**
 *
 * Récurpération des attributs d'une éléments
 * @param unknown_type $ele
 */
function recuperationAttributs($ele) {

	if ($ele !=null )
	if ($ele->hasAttributes())
	{
		$atrb = $ele->attributes;
	}

	return $atrb;
}

/**
 * Init des attribut d'un élément
 *
 */
function initAttributs($ele,$lesAttributs){

	if ($ele !=null )
	if ($lesAttributs!=null && count($lesAttributs)>0)
	{
		foreach ($lesAttributs as $attribut){
			$ele->setAttribute($attribut->name,$attribut->value);
		}
	}

	return $ele;
}


/**
 *  Fabrication des encradrés.
 *  @param $pageHtml code html.
 *  @param $idref reférence.
 */
function fabricationEncadre($pageHtml,$idref) {
	global $glb_chemin_relatif ;

	/*
	 * Récupération de l'encadre XML
	 * */
	$encadre= new DOMDocument();
	$encadre->load($glb_chemin_relatif."/xml/encadre/encadre".substr($idref,0,4).".xml");
	$xpath = new DOMXPath($encadre);
	$titre = $xpath->query("/encadre/titre");
	$lesLiens = $xpath->query("/encadre/liens/lien");
	//	$paragraphe=$xpath->query("/encadre/paragraphe");

	/*
	 * Localisation de l'encadre (div) dans le modèle.
	 */
	$pageHtml_xpath = new DOMXPath($pageHtml);
	$div=$pageHtml_xpath->query("//div[@id='encadre']")->item(0);

	/* Titre
	 * */
	$h3 =$pageHtml->createElement("h3");
	$h3->appendChild($pageHtml->createTextNode($titre->item(0)->firstChild->nodeValue));
	$div->appendChild($h3);


	/*
	 * On peut pas afficher un bout de XML.
	 * */
	//print_r($encadre->saveXML($encadre));

	/* Liens
	 * */
	foreach ($lesLiens as $lien) {

		$a = $pageHtml->createElement("a");
		$a->appendChild($pageHtml->createTextNode($lien->firstChild->nodeValue));
		$a->setAttribute("href",$lien->getAttribute("url"));
		$div->appendChild($a);
	}
	//parcours récursif pour faire la substitution des noeuds

	return $pageHtml;
}


/**
 * Fabrication d'un article.
 */

function fabricationArticle($pageHtml, $pageXml,$idRef) {
	GLOBAL $glb_chemin_relatif;
	//
	// Récupération id article XML
	//
	$pageXml= new DOMDocument();
	$pageXml->load($glb_chemin_relatif."/xml/article/article".$idRef.".xml");
	
	//
	// Récupération code XML article
	//

	
	//
	// Remplacement
	//
	
	return $pageHtml;
	
}

/**
 * Fabrication de la page
 * @author pg
 * @since 19/11/2011
 * @package
 */
function fabricationPage($idPage,$cheminModele){

	global $glb_chemin_relatif ;

	/*
	 * Page (xml).
	 * */
	$pageXml= new DOMDocument();
	$pageXml->load($glb_chemin_relatif."/xml/page/page".$idPage.".xml");
	$titre=$pageXml->getElementsByTagName("titre")->item(0);
	$modele=$pageXml->getElementsByTagName("modele")->item(0);

	/*
	 * Modele (html).
	 * */
	$pageHtml= new DOMDocument();
	$pageHtml->load($glb_chemin_relatif."/html/".$cheminModele."/modele".$modele->getAttribute("idref").".html");


	// On met le bon titre dans la page HTML à partir du titre XML.
	$title=$pageHtml->getElementsByTagName("title")->item(0);
	$t2 = $pageHtml->createTextNode($titre->firstChild->nodeValue);
	if ($title->firstChild) {
		$title->removeChild($title->firstChild);
	}
	$title->appendChild($t2);

	/*
	 * Fabrication du menu.
	 */
	$listeMenu=$pageXml->getElementsByTagName("menu");

	if ($listeMenu!=null) {
		foreach ($listeMenu as $menu){
			$pageHtml=fabricationMenu($pageHtml,$menu->getAttribute("idref"),$menu->getAttribute("id"));
		}
	}

	/*
	 * Fabrication encadre.
	 */
	$encadre=$pageXml->getElementsByTagName("encadre")->item(0);
	if ($encadre!=null){
		$pageHtml=fabricationEncadre($pageHtml,$encadre->getAttribute("idref"));
	}

	/*
	 * Fabrication de la liste de sommaire.
	 */
	$sommaire=$pageXml->getElementsByTagName("liste")->item(0);
	if ($sommaire!=null) {
		$pageHtml=fabricationSommaire($pageHtml, $pageXml,$idPage);
	}


	/*
	 * Fabrication d'un article
	 */
	$xpath = new DOMXPath($pageXml);
	$article_idRef= $xpath->query("/page/article/@idref")->item(0)->value;
	echo $article_idRef;
	
	
	if ($article_idRef!=null) {
		$pageHtml=fabricationArticle($pageHtml, $pageXml,$article_idRef);
	}


	/*
	 * Affichage du html.
	 */
	echo $pageHtml->saveXML();
}

/*
 * Fabrication et affichage de la page en HTML.
 * 1- ouvrir le fichier page
 * 2- récupérer la ref. du fichier modèle HTML
 * 3- récupérer la ref. du fichier menu
 * 4- dans le fichier HTML
 *  - substituer le menu
 *  - substituer le titre
 * 5- afficher la page dans le navigateur.
 */

/*Récupération des param
 * */

if (isset($_GET["idPage"]))
{
	$idPage=$_GET["idPage"];
} else
{
	$idPage="0501";
}

if (isset($_GET["cheminModele"]))
{
	$cheminModele=$_GET["cheminModele"];//type de modèle.
} else
{
	$cheminModele="page";
}

fabricationPage($idPage,$cheminModele);


?>