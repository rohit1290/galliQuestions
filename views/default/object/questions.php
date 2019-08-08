<?php

$full = elgg_extract('full_view', $vars, false);
$questions = elgg_extract('entity', $vars, false);

if (!$questions) {
	return;
}

$owner = $questions->getOwnerEntity();
$owner_icon = elgg_view_entity_icon($owner, 'tiny');

$link = elgg_view('output/url', ['href' => $questions->address]);
$description = elgg_view('output/longtext', ['value' => $questions->description, 'class' => 'pbl']);

$owner_link = elgg_view('output/url', [
	'href' => $owner->getURL(),
	'text' => $owner->name,
	'is_trusted' => true,
]);
$author_text = elgg_echo('byline', [$owner_link]);

$date = elgg_view_friendly_time($questions->time_created);

$comments_count = $questions->countComments();
$text = elgg_echo("questions:answers") . " ($comments_count)";
$comments_link = elgg_view('output/url', [
	'href' => $questions->getURL() . '#comments',
	'text' => $text,
	'is_trusted' => true,
]);

$metadata = elgg_view_menu('entity', [
	'entity' => elgg_extract('entity', $vars),
	'handler' => elgg_extract('handler', $vars),
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);

$subtitle = "$author_text $date $comments_link";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full && !elgg_in_context('gallery')) {
	$params = [
		'entity' => $questions,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
	];
	$params = $params + $vars;
	$summary = elgg_view('object/elements/summary', $params);

	$body = <<<HTML
<div class="questions elgg-content mts">
	$description
</div>
HTML;

	$answer = elgg_get_entities(['type' => 'object', 'relationship' => 'is_answer_of', 'relationship_guid' => $questions->guid, 'inverse_relationship' => true]);
	if ($answer) {
		$answer_entity = elgg_view_entity($answer[0]);
		$header = elgg_echo('questions:acceptedanswer');
		$body .= "	<div class='questions elgg-content mts selected-answer'>
						<h3>$header</h3>
						$answer_entity
					</div>";
	}

	echo elgg_view('object/elements/full', [
		'entity' => $questions,
		'icon' => $owner_icon,
		'summary' => $summary,
		'body' => $body,
	]);
} elseif (elgg_in_context('gallery')) {
	echo <<<HTML
<div class="questions-gallery-item">
	<h3>$questions->title</h3>
	<p class='subtitle'>$owner_link $date</p>
</div>
HTML;
} else {
	// brief view
	$excerpt = elgg_get_excerpt($questions->description);

	$params = [
		'entity' => $questions,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
	];
	$params = $params + $vars;
	$body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $body);
}
