<?php

echo elgg_view('page/elements/comments_block', [
	'subtypes' => 'questions',
	'owner_guid' => elgg_get_page_owner_guid(),
]);

echo elgg_view('page/elements/tagcloud_block', [
	'subtypes' => 'questions',
	'owner_guid' => elgg_get_page_owner_guid(),
]);
