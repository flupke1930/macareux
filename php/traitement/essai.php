<?php

$doc = new DOMDocument('1.0',"UTF-8");

$root = $doc->createElement('book');
$root = $doc->appendChild($root);

$title = $doc->createElement('title');
$title = $root->appendChild($title);

$text = $doc->createTextNode('Ceci est le titre');
$text = $title->appendChild($text);

echo "Récupération de tout le document :\n";
echo $doc->saveXML() . "\n";

echo "Récupération du titre, uniquement :\n";
echo $doc->saveXML($title);

