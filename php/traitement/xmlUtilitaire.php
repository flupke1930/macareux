<?php
/**
 * * TODO: doc le cartouche
 * Fonctions XML diverses.
 *
 */

/**
 * * TODO: doc le cartouche
 * Supprime tous les noeuds enfants.
 * Enter description here ...
 * @param unknown_type $node
 */
function noeudSupprLesEnfants(&$neud) {
  while ($neud->firstChild) {
    while ($neud->firstChild->firstChild) {
      noeudSupprLesEnfants($neud->firstChild);
    }
    $neud->removeChild($neud->firstChild);
  }
}


/**
 ** TODO: doc le cartouche
 * Récurpération des attributs d'une éléments
 * @param unknown_type $ele
 */
function recuperationAttributs($ele) {

	if ($ele !=null )
	if ($ele->hasAttributes())
	{
		$atrb = $ele->attributes;
	}

	return $atrb;
}

/**
 * * TODO: doc le cartouche
 * Init des attribut d'un élément
 *
 */
function initAttributs($ele,$lesAttributs){

	if ($ele !=null )
	if ($lesAttributs!=null && count($lesAttributs)>0)
	{
		foreach ($lesAttributs as $attribut){
			$ele->setAttribute($attribut->name,$attribut->value);
		}
	}

	return $ele;
}


/**
 * * TODO: doc le cartouche
 * Affichage en debug du XML
 * */
function debugXML($obj ){
	echo "<pre style='border:dotted 2px;background:olive;margin:10px;padding:10px'>---debugXML()---<br>";
	var_dump($obj);

	$doc = new DOMDocument('1.0',"UTF-8");
	if (get_class($obj)=="DOMNodeList"){
echo "<br>--".$obj->length;
		$i=0;
		foreach ($obj as $l)
		{
			echo "<br>--".$i++;
			$n=$doc->importNode($l,true);
			$doc->appendChild($n);
		}
	}
	if (get_class($obj)=="DOMElement"){
		foreach ($obj->childNodes as $l)
		{
			$n=$doc->importNode($l,true);
			$doc->appendChild($n);
		}

	}

		echo "".htmlentities($doc->saveXML());
		echo "</pre>";
}