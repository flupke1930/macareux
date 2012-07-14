<?php


include_once('element.php'); 
include_once('transforme.php');  

class Lien extends Element {
	function accept(Transforme $visiteur){
		//<lien> devient <a>
		$visiteur->transforme();
	}	
}

?>