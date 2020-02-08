<?php

function questions_url(\Elgg\Hook $hook) {
	$entity = $hook->getEntityParam();

	global $CONFIG;
	$title = $entity->title;
	$title = elgg_get_friendly_title($title);
	return $CONFIG->url . "questions/view/" . $entity->getGUID() . "/" . $title;
}

function questions_owner_block_menu(\Elgg\Hook $hook) {
	$return = $hook->getValue();
	$params = $hook->getParams();

	if ($params['entity'] instanceof ElggUser) {
		$url = "questions/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('questions', elgg_echo('questions'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->questions_enable != 'no') {
			$url = "questions/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('questions', elgg_echo('questions:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

function questions_prepare_notification(\Elgg\Hook $hook) {
	$notification = $hook->getValue();
	$params = $hook->getParams();

	$entity = $params['event']->getObject();
	$owner = $params['event']->getActor();
	$recipient = $params['recipient'];
	$language = $params['language'];
	$method = $params['method'];

	$descr = $entity->description;
	$title = $entity->title;

	$notification->subject = elgg_echo('questions:notify:subject', [$title], $language);
	$notification->body = elgg_echo('questions:notify:body', [
		$owner->name,
		$title,
		$descr,
		$entity->getURL()
	], $language);
	$notification->summary = elgg_echo('questions:notify:summary', [$entity->title], $language);

	return $notification;
}

function questions_profile_fields() {
	return [
		[ 'name' => 'title',			'type' => 'text',		'value' => ''],
		[ 'name' => 'description', 	'type' => 'longtext', 	'value' => ''],
	];
}

function questions_answers_entity_menu_setup(\Elgg\Hook $hook) {
	$return = $hook->getValue();

	$answer = $hook->getEntityParam();
	$answer_guid = $answer->guid;

	$question = $answer->getContainerEntity();
	$question_guid = $question->guid;

	// Only for questions
	if ($question) {
		$subtype = $question->getSubtype();
		if ($subtype != 'questions') {
			return $return;
		}
	} else {
		return $return;
	}

	// Not in widgets
	if (elgg_in_context('widgets')) {
		return $return;
	}

	if ($question->canEdit()) {
		// Already marked this post as the answer?
		if (check_entity_relationship($answer_guid, 'is_answer_of', $question_guid)) {
			$text = elgg_echo('questions:unmark');
			$href = "action/questions/mark?todo=unmark&guid=$answer_guid";
		} else {
			$answer = elgg_get_entities(['type' => 'object', 'relationship' => 'is_answer_of', 'relationship_guid' => $question->guid, 'inverse_relationship' => true]);
			if (!$answer) {
				$text = elgg_echo('questions:mark');
				$href = "action/questions/mark?todo=mark&guid=$answer_guid";
			}
		}
		if ($text & $href) {
			$options = [
				'name' => 'mark-answers',
				'text' => $text,
				'href' => $href,
				'class' => $class,
				'priority' => 150,
				'is_action' => true,
			];
			$return[] = ElggMenuItem::factory($options);
		}
	}

	return $return;
}