<?php
elgg_register_event_handler('init', 'system', 'questions_init');

function questions_init() {

	require_once __DIR__ ."/lib/questions.php";

	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'questions_owner_block_menu');

	elgg_extend_view('css/elgg', 'questions/css');
	elgg_extend_view('js/elgg', 'questions/js');

	elgg_register_widget_type([
		'id' => 'questions',
		'context' => ['profile', 'dashboard'],
	]);

	// Register for notifications
	elgg_register_notification_event('object', 'questions', array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:questions', 'questions_prepare_notification');

	elgg_register_plugin_hook_handler('entity:url', 'questions', 'questions_url');

	elgg_register_entity_type('object', 'questions');

	elgg()->group_tools->register('questions', [
	        'label' => elgg_echo('questions:enablequestions'),
	]);

	elgg_register_plugin_hook_handler('register', 'menu:entity', 'questions_answers_entity_menu_setup');
}

function questions_url($entity) {
	global $CONFIG;
	$title = $entity->title;
	$title = elgg_get_friendly_title($title);
	return $CONFIG->url . "questions/view/" . $entity->getGUID() . "/" . $title;
}

function questions_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
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

function questions_prepare_notification($hook, $type, $notification, $params) {
	$entity = $params['event']->getObject();
	$owner = $params['event']->getActor();
	$recipient = $params['recipient'];
	$language = $params['language'];
	$method = $params['method'];

	$descr = $entity->description;
	$title = $entity->title;

	$notification->subject = elgg_echo('questions:notify:subject', array($title), $language);
	$notification->body = elgg_echo('questions:notify:body', array(
		$owner->name,
		$title,
		$descr,
		$entity->getURL()
	), $language);
	$notification->summary = elgg_echo('questions:notify:summary', array($entity->title), $language);

	return $notification;
}

function questions_profile_fields(){
	return array(
		array( 'name' => 'title',			'type' => 'text',		'value' => ''),
		array( 'name' => 'description', 	'type' => 'longtext', 	'value' => ''),
	);
}

function questions_answers_entity_menu_setup($hook, $type, $return, $params) {
	$answer = $params['entity'];
	$answer_guid = $answer->guid;

	$question = $answer->getContainerEntity();
	$question_guid = $question->guid;

	// Only for questions
	if($question){
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

	if($question->canEdit()){
		// Already marked this post as the answer?
		if(check_entity_relationship($answer_guid, 'is_answer_of', $question_guid)){
			$text = elgg_echo('questions:unmark');
			$href = "action/questions/mark?todo=unmark&guid=$answer_guid";
		} else {
			$answer = elgg_get_entities(array('type' => 'object', 'relationship' => 'is_answer_of', 'relationship_guid' => $question->guid, 'inverse_relationship' => TRUE));
			if(!$answer){
				$text = elgg_echo('questions:mark');
				$href = "action/questions/mark?todo=mark&guid=$answer_guid";
			}
		}
		if($text & $href){
			$options = array(
				'name' => 'mark-answers',
				'text' => $text,
				'href' => $href,
				'class' => $class,
				'priority' => 150,
				'is_action' => true,
			);
			$return[] = ElggMenuItem::factory($options);
		}
	}

	return $return;
}
