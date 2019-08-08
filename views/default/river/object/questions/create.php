<?php

$object = $vars['item']->getObjectEntity();
$excerpt = elgg_get_excerpt($object->description);

echo elgg_view('river/elements/layout', [
	'item' => $vars['item'],
	'message' => $excerpt,
]);
