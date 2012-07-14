<?PHP
include("configuration/universel.inc.php");
include("configuration/environnement.inc.php");
include("xlst.php");

/*+++++++++++++++++++++++++++++++++++++++++++

++++++++++++++++++++++++++++++++++++++++++++*/

$xmlRecupFichier.=stripslashes($_POST["flux"]);

$XML->loadXML( $xmlRecupFichier );
$XSL->load( "../../xsl/page.xslt", LIBXML_NOCDATA); 
$xslt->importStylesheet( $XSL ); 
$result = $xslt->transformToXML( $XML ); 


/*+++++++++++++++++++++++++++++++++++++++++++

++++++++++++++++++++++++++++++++++++++++++++*/
/*
$XSL->load( "../../xsl/repertoire/repModele_front02.xsl", LIBXML_NOCDATA); 
$XML->load("../../xml/article/modeleFrontListe.xml");
$xslt->setParameter('', 'modeleId',  trim($result));
$xslt->importStylesheet( $XSL ); 
$result = $xslt->transformToXML( $XML ); 
//*/
/*+++++++++++++++++++++++++++++++++++++++++++

récupération du fichier XHTML modèle

++++++++++++++++++++++++++++++++++++++++++++*/
/*
$fichier="../../../../contenuPro/modele/g0commun/".trim($result);
$xml2="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml2.="<data>\n";
$xml2.= implode("",(file($fichier)));
//*/
/*+++++++++++++++++++++++++++++++++++++++++++
	
	Le modèle XHTML et les donnees XML 
	sont mis bout a bout et 
	traites par la XSL
	
	le but est de remplacer  	
		<div tpl="zone1"></div> 
	par
		<div>
		<h3>ceci est une zone variable</h3>
		<div>

++++++++++++++++++++++++++++++++++++++++++++*/
/*
$xml2.="<dyn>\n";
$xml2.=stripslashes($_POST["flux"]);
$xml2.="</dyn>\n";
$xml2.="</data>\n";

//print("###".$xml2."###");


$XSL->load("../../xsl/article/article_front_test.xsl", LIBXML_NOCDATA); 
$XML->loadXML($xml2);
$xslt->importStylesheet( $XSL ); 
$result = $xslt->transformToXML( $XML ); 

//*/
/*+++++++++++++++++++++++++++++++++++++++++++
Affichage du flux HTML 
++++++++++++++++++++++++++++++++++++++++++++*/
?>         