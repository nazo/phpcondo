<?php 
/* SVN FILE: $Id$ */
/* UploaderFiles schema generated on: 2011-04-18 04:04:01 : 1303067461*/
class UploaderFilesSchema extends CakeSchema {
	var $name = 'UploaderFiles';

	var $file = 'uploader_files.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $uploader_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'alt' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'uploader_category_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>