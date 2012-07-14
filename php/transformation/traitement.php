<?php

include_once("lien.php"); 
include_once("transforme_a.php");
include_once("transforme_p.php");
include_once("transforme_h2.php");

$l=new Lien();
$a=new TransformeA();
$p=new TransformeP();
$h2=new TransformeH2();

//$a->traitement($l);
//$p->traitement($l);



/*

*/
$idref="0102";
$encadre= new DOMDocument();
$encadre->load("../../xml/encadre/encadre".substr($idref,0,4).".xml");
$xpath = new DOMXPath($encadre);
$lesNoeuds = $xpath->query("//lien");
echo $lesNoeuds->length;
foreach ($lesNoeuds as $noeud)
{
	echo "-".$noeud->nodeName;
}
?>