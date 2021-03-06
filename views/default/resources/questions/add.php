<?php
elgg_gatekeeper();
$page_owner = elgg_get_page_owner_entity();

$title = elgg_echo('questions:add');
elgg_push_breadcrumb($title);

$vars = questions_prepare_form_vars();
$content = elgg_view_form('questions/save', [], $vars);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $content,
	'title' => $title,
]);

echo elgg_view_page($title, $body);
