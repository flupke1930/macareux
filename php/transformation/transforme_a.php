<?php

include_once('element.php'); 
include_once('transforme.php');  

class TransformeA extends Transforme {
	function traitement(Element $element) {
		//<lien> devient <a>
		echo "a";
		
		return $html;
	}	
}

?>