<?php

$max = (int) $vars['entity']->num_display;

$options = [
	'type' => 'object',
	'subtype' => 'questions',
	'container_guid' => $vars['entity']->owner_guid,
	'limit' => $max,
	'full_view' => false,
	'pagination' => false,
];
$content = elgg_list_entities($options);

echo $content;

if ($content) {
	$url = "questions/owner/" . elgg_get_page_owner_entity()->username;
	$more_link = elgg_view('output/url', [
		'href' => $url,
		'text' => elgg_echo('questions:more'),
		'is_trusted' => true,
	]);
	echo "<span class=\"elgg-widget-more\">$more_link</span>";
} else {
	echo elgg_echo('questions:none');
}
