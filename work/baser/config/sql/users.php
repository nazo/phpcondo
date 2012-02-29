<?php 
/* SVN FILE: $Id$ */
/* Users schema generated on: 2010-11-04 18:11:11 : 1288863011*/
class UsersSchema extends CakeSchema {
	var $name = 'Users';

	var $file = 'users.php';

	var $connection = 'baser';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'password' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'real_name_1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'real_name_2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'user_group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>