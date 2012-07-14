<?php

include_once('element.php'); 
include_once('transforme.php');  

class TransformeH2 extends Transforme {
	function traitement(Element $element){
		//<titre> devient <h2>
		echo "h2";
	}	
}

?>