<?php
abstract class Element {
	
	private	$html;
	
	abstract function accept(Transforme $visiteur);	
}

?>