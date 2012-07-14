<?php


/**
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