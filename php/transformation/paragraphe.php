<?php

include_once('element.php'); 
include_once('transforme.php');  

class Paragraphe extends Element {
	function accept(Transforme $visiteur){
		//<paragraphe> devient <p>
		$visiteur->transforme();
	}	
}

?>