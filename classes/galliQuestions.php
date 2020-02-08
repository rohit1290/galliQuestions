<?php
use Elgg\DefaultPluginBootstrap;

class galliQuestions extends DefaultPluginBootstrap {

  public function init() {
  	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'questions_owner_block_menu');

  	elgg_extend_view('css/elgg', 'questions/css');
  	elgg_extend_view('js/elgg', 'questions/js');

  	elgg_register_widget_type([
  		'id' => 'questions',
  		'context' => ['profile', 'dashboard'],
  	]);

  	// Register for notifications
  	elgg_register_notification_event('object', 'questions', ['create']);
  	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:questions', 'questions_prepare_notification');

  	elgg_register_plugin_hook_handler('entity:url', 'questions', 'questions_url');

  	elgg_register_entity_type('object', 'questions');

  	elgg()->group_tools->register('questions', [
  			'label' => elgg_echo('questions:enablequestions'),
  	]);

  	elgg_register_plugin_hook_handler('register', 'menu:entity', 'questions_answers_entity_menu_setup');
  }
}