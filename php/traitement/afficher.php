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
function fabricationUL($html,$ul,$lesEntreesXml) {
	global $glb_chemin_relatif ;
	/*
	 * Remplacement des éléments HTML par le contenu XML.
	 */
	if ($ul!=null){

		//Supprime les noeuds enfants.
		noeudSupprLesEnfants($ul);
		// Entrées du menu
		foreach ($lesEntreesXml as $ligne) {
			$li =$html->createElement("li");
			$a =$html->createElement("a");
			$span=$html->createElement("span");
				
			$span->appendChild($html->createTextNode($ligne->firstChild->nodeValue));
				
				
			//Récupération des attributs de style dans le xml
			$lesAttributs=recuperationAttributs($ligne);
				
				
			//Attributs
			foreach ($lesAttributs as $attribut) {
				if ($attribut->name=="idRef") {
					$page= new DOMDocument();
					$page->load($glb_chemin_relatif."/xml/page/page".$attribut->value.".xml");
					//Récupération des entree;
					$xpath = new DOMXPath($page);
					$type = $xpath->query("/page/@type")->item(0)->value;
					$a->setAttribute("href","afficher.php?idPage=".$attribut->value."&cheminModele=".$type);
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

/*
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
 */
function fabricationMenu($html,$idref,$idMenu) {
	global $glb_chemin_relatif ;
	try {
		//
		$menu= new DOMDocument();
		$menu->load($glb_chemin_relatif."xml/menu/menu".substr($idref,0,4).".xml");
		// Localisation du menu (div) dans le modèle HTML.
		$html_xpath = new DOMXPath($html);
		$ul=$html_xpath->query("//div[@id='".$idMenu."']/ul")->item(0);
		// Récupération des entree;
		$xpath = new DOMXPath($menu);
		$lesEntreesXml = $xpath->query("/menu/entree");
		$ul=fabricationUL($html,$ul,$lesEntreesXml);

		


	} catch (Exception $e) {
		echo $e;
	}
	return $html;
}


/**
 * Transfert les information contenu dans le XML dans le HTML.
 * @param Noeud $div
 * @param Noeud $lesLignesXml
 * @param Tableau $atrbs attributs du style css
 * @param Tableau $ele
 */
function fabricationDIV($html,$corpsHtml,$lesLignesXml,$atrbs,$ele,$idPageAppel) {

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
					//	echo "<br>---------------------------------------------------------";
					//	echo "<br>i=".$i++;
					//	echo "<br>";

					debugXML($groupeXml);
					$div=$html->createElement("div");
					$div=initAttributs($div, $divAttribus);


					/*
					 * Boucle Traitement des lignes Groupe
					 * Récupération du contenu des articles.
					 * contenu (titre,resume,vignette)
					 * Pour chaque ligne du sommaire XML il faut récupérér les informations
					 * de l'article (titre, image, résumé).
					 */

					$h3 =$html->createElement("h3");
					$span =$html->createElement("span");
					$span->appendChild($html->createTextNode($groupeXml->getAttribute("titre")));
					$h3->appendChild($span);
					$div->appendChild($h3);
					//
					// boucle sur les articles
					//
					$lesPagesXml=$groupeXml->getElementsByTagName("page");




					foreach ($lesPagesXml as $pageXml)
					{
						// Fabrication d'une ligne du sommaire.
						$articleXml=$pageXml->getElementsByTagName("article")->item(0);
						// Récupération de la ligne depuis le fichier article.
						$listeContenu=recuperationContenuLigne($idPageAppel, $articleXml->getAttribute("idref"));

						$div3 =$html->createElement("div");

						$img = $html->createElement("img");
						$img->setAttribute("src",$listeContenu["image"]["src"]);
						$img->setAttribute("width",$listeContenu["image"]["largeur"]);
						$img->setAttribute("length",$listeContenu["image"]["hauteur"]);


						$h4 =$html->createElement("h4");
						$h4->appendChild($html->createTextNode($listeContenu["titre"]));

						$p =$html->createElement("p");
						$span=$html->createElement("span");
						$span->appendChild($html->createTextNode( $listeContenu["resume"] ));
						$p->appendChild($span);
							
						$a =$html->createElement("a");
						$a->setAttribute("href",$glb_chemin_relatif.$groupeXml->getAttribute("uri").$glb_fichier_extension);
						$span=$html->createElement("span");
						$span->appendChild($html->createTextNode("plus"));
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
						if ($atrbs["a"] ){
							foreach ($atrbs["a"] as $attr) {
								$a->setAttribute($attr->name,$attr->value);
							}
						}

						$div3->appendChild($img);
						$div3->appendChild($h4);
						$p->appendChild($a);
						$div3->appendChild($p);
						$div->appendChild($div3);
						debugXML($div);
						//debug_print_backtrace();
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
 * @param DOMElement $html
 * @param DOMDocument $xml
 */
function fabricationSommaire($html,$xml,$idPage) {
	global $glb_chemin_relatif ;

	/*
	 * Récupération du sommaire dans le fichier XML.
	 */
	$xpath_xml = new DOMXPath($xml);
	$lesLignesXml=$xpath_xml->query("/page/liste[@id='sommaire']");

	//$xPath = new XPathQueryLength($doc);
	//print $xPath->queryLength("/page/liste[@id='sommaire']/groupe/page");

	// "=== nb de lignes:".$lesLignesXml->length."===";

	/*
	 * Récupération du sommaire HTML dans le modèle.
	 */
	$xpath_html = new DOMXPath($html);
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
		$div=fabricationDIV($html,$div,$lesLignesXml,$atrbs,$ele,$idPage);
	}
	return $html;
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
 *  @param $html code html.
 *  @param $idref reférence.
 */
function fabricationEncadre($html,$idref) {
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
	$html_xpath = new DOMXPath($html);
	$div=$html_xpath->query("//div[@id='encadre']")->item(0);

	/* Titre
	 * */
	$h3 =$html->createElement("h3");
	$h3->appendChild($html->createTextNode($titre->item(0)->firstChild->nodeValue));
	$div->appendChild($h3);


	/*
	 * On peut pas afficher un bout de XML.
	 * */
	//print_r($encadre->saveXML($encadre));

	/* Liens
	 * */
	foreach ($lesLiens as $lien) {

		$a = $html->createElement("a");
		$a->appendChild($html->createTextNode($lien->firstChild->nodeValue));
		$a->setAttribute("href",$lien->getAttribute("url"));
		$div->appendChild($a);
	}
	//parcours récursif pour faire la substitution des noeuds

	return $html;
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
	$page= new DOMDocument();
	$page->load($glb_chemin_relatif."/xml/page/page".$idPage.".xml");
	$titre=$page->getElementsByTagName("titre")->item(0);
	$modele=$page->getElementsByTagName("modele")->item(0);

	/*
	 * Modele (html).
	 * */
	$html= new DOMDocument();
	$html->load($glb_chemin_relatif."/html/".$cheminModele."/modele".$modele->getAttribute("idref").".html");


	// On met le bon titre dans la page HTML à partir du titre XML.
	$title=$html->getElementsByTagName("title")->item(0);
	$t2 = $html->createTextNode($titre->firstChild->nodeValue);
	if ($title->firstChild) {
		$title->removeChild($title->firstChild);
	}
	$title->appendChild($t2);

	/*
	 * Fabrication du menu.
	 */
	$listeMenu=$page->getElementsByTagName("menu");
	
	if ($listeMenu!=null) {
		foreach ($listeMenu as $menu){
		$html=fabricationMenu($html,$menu->getAttribute("idref"),$menu->getAttribute("id"));
	}}

	/*
	 * Fabrication encadre.
	 */
	$encadre=$page->getElementsByTagName("encadre")->item(0);
	if ($encadre!=null){
		$html=fabricationEncadre($html,$encadre->getAttribute("idref"));
	}

	/*
	 * Fabrication de la liste de sommaire.
	 */
	$sommaire=$page->getElementsByTagName("liste")->item(0);
	if ($sommaire!=null) {
		$html=fabricationSommaire($html, $page,$idPage);
	}

	/*
	 * Affichage du html.
	 */
	echo $html->saveXML();
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
	//$idPage="0503";
}

if (isset($_GET["cheminModele"]))
{
	$cheminModele=$_GET["cheminModele"];//type de modèle.
} else
{
	$cheminModele="page";
	//$cheminModele="article";
	//$cheminModele="sommaire";
}

fabricationPage($idPage,$cheminModele);


?>