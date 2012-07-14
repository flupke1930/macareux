<?php

include_once('element.php'); 
include_once('transforme.php');  

class TransformeP extends Transforme {
	function traitement(Element $element){
		//<paragraphe> devient <p>
		echo "p";
	}	
}

?>