<?PHP
include_once 'constantes.php';
include_once 'xmlUtilitaire.php';
include_once 'afficherSommaire.php';
include_once 'afficherArticle.php';

/**
 * * TODO: doc le cartouche
 * @author flupke1930
 * Permet la transformation d'un fichier de contenu en XML en page xHTML (utilisation d'un modèle).
 * Actuellement on a encore du code en dure par rapport au id xhtml.
 */



/**
 ** TODO: doc le cartouche
 * fabrication d'une URL pour l'affichage d'une page HTML.
 * @param unknown_type $idPage
 * @param unknown_type $type
 */
function fabricationAffichageURL($idPage,$type){
	$url="afficher.php?idPage=".$idPage."&cheminModele=".$type;
	return $url;
}



/**
 * * TODO: doc le cartouche
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
 * * TODO: doc le cartouche
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
 * * TODO: doc le cartouche
 * Fabrication de la page
 * @author flupke1930
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


	if ($article_idRef!=null) {
		$pageHtml=fabricationArticle($pageHtml, $pageXml,$article_idRef,$idPage);
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