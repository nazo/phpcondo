<?php
/* SVN FILE: $Id$ */
/* Pages schema generated on: 2010-11-24 19:11:45 : 1290596025*/
class PagesSchema extends CakeSchema {
	var $name = 'Pages';

	var $path = '/Users/ryuring/Documents/Projects/basercms/app/tmp/schemas/baser/';

	var $file = 'pages.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $pages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'contents' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'page_category_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'url' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'draft' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'author_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>