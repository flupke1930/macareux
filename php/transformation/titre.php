<?php


include_once('element.php'); 
include_once('transforme.php');  

class Titre extends Element {
	function accept(Transforme $visiteur){
		//<titre> devient <h2>
		$visiteur->transforme();
	}	
}

?>