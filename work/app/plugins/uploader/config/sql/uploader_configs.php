<?php 
/* SVN FILE: $Id$ */
/* UploaderConfigs schema generated on: 2011-04-16 13:04:05 : 1302929345*/
class UploaderConfigsSchema extends CakeSchema {
	var $name = 'UploaderConfigs';

	var $file = 'uploader_configs.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $uploader_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false),
		'value' => array('type' => 'text', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>