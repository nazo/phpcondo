<?php 
/* SVN FILE: $Id$ */
/* UploaderCategories schema generated on: 2011-04-18 04:04:01 : 1303067461*/
class UploaderCategoriesSchema extends CakeSchema {
	var $name = 'UploaderCategories';

	var $file = 'uploader_categories.php';

	var $connection = 'plugin';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $uploader_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>