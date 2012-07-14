<?PHP
//include("traitement.php");
/*
if (isset($_GET["fichier"]))
$fichierNom=$_GET["fichier"];
else
$fichierNom="article";

$fp = fopen("../../../../contenuPro/html/g0commun/".$fichierNom.".html", "w");		
fputs($fp, $result);
fclose($fp); 	
//header("location:edition.php?fichier=".$fichierNom);
*/



/*
1/ ouvrir le fichier page
2/ récupérer la ref. du fichier modèle HTML
3/ récupérer la ref. du fichier menu
4/ dans le fichier HTML 
	- substituer le menu
	- substituer le titre

5/ enregistrer du fichier html
6/ afficher la page dans le navigateur.

//*/

/*

*/
function a(){
	$info1= new DOMDocument();
	$info1->load("../xml/page/page0501.xml");
	$titre=$info1->getElementsByTagName("titre")->item(0);
//	echo $titre->firstChild->nodeValue;

	$info= new DOMDocument();
	$info->load("../html/page/modele0201.html");
	$title=$info->getElementsByTagName("title")->item(0);
	$t2 = $info->createTextNode($titre->firstChild->nodeValue);
//	echo $title->firstChild->nodeValue;
	if ($title->firstChild) {
		
	$title->removeChild($title->firstChild);
	}
	$title->appendChild($t2);	
	echo $info->saveXML();

}

/*
*/
function b(){

	$info= new DOMDocument();
	$info->load("../html/page/modele0201.html");
	$title=$info->getElementsByTagName("title")->item(0);


	$dom = new DomDocument();
	$t1 = $dom->createElement("titre");
	$t2 = $dom->createTextNode($title->firstChild->nodeValue);
	$t1->appendChild($t2);
	$dom->appendChild($t1);
	


	echo $dom->saveXML();
}

a();

/*


$xpath = new DOMXPath($dom);
$noeuds = $xpath->query("modele/@idref");
$i=0;
foreach ($noeuds as $noeud) {
sprintf("<br />modele : %s",$noeud->nodeValue);
}
*/

?>