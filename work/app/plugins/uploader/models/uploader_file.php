<?php
/* SVN FILE: $Id$ */
/**
 * ファイルアップローダーモデル
 *
 * PHP versions 4 and 5
 *
 * Baser :  Basic Creating Support Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			uploader.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ファイルアップローダーモデル
 *
 * @package		uploader.models
 */
class UploaderFile extends AppModel {
/**
 * モデル名
 * 
 * @var     string
 * @access  public
 */
	var $name = 'UploaderFile';
/**
 * データソース
 *
 * @var		string
 * @access 	public
 */
	var $useDbConfig = 'plugin';
/**
 * プラグイン名
 *
 * @var		string
 * @access 	public
 */
	var $plugin = 'Uploader';
/**
 * behaviors
 *
 * @var 	array
 * @access 	public
 */
	var $actsAs = array('Upload' => array(
		'saveDir'	=> "uploads",
		'fields'	=> array(
				'name'	=> array('type'	=> 'all')
	)));
/**
 * コンストラクタ
 *
 * @param	int		$id
 * @param	string	$table
 * @param	string	$ds
 */
	function  __construct($id = false, $table = null, $ds = null) {
		
		parent::__construct($id, $table, $ds);
		$sizes = array('large', 'midium', 'small', 'mobile_large', 'mobile_small');
		$UploaderConfig = ClassRegistry::init('Uploader.UploaderConfig');
		$uploaderConfigs = $UploaderConfig->findExpanded();
		$imagecopy = array();
		
		foreach($sizes as $size) {
			if(!isset($uploaderConfigs[$size.'_width']) || !isset($uploaderConfigs[$size.'_height'])) {
				continue;
			}
			$imagecopy[$size] = array('suffix'	=> '__'.$size);
			$imagecopy[$size]['width'] = $uploaderConfigs[$size.'_width'];
			$imagecopy[$size]['height'] = $uploaderConfigs[$size.'_height'];
			if(isset($uploaderConfigs[$size.'_thumb'])) {
				$imagecopy[$size]['thumb'] = $uploaderConfigs[$size.'_thumb'];
			}
		}
		
		$settings = $this->actsAs['Upload'];
		$settings['fields']['name']['imagecopy'] = $imagecopy;
		$this->Behaviors->attach('Upload', $settings);

	}
/**
 * ファイルの存在チェックを行う
 *
 * @param	string	$fileName
 * @return	void
 * @access	boolean
 */
	function fileExists($fileName) {

		$savePath = WWW_ROOT . 'files' . DS . $this->actsAs['Upload']['saveDir'] . DS . $fileName;
		return file_exists($savePath);

	}
/**
 * 複数のファイルの存在チェックを行う
 * 
 * @param	string	$fileName
 * @return	array
 * @access	void
 */
	function filesExists($fileName) {

		$pathinfo = pathinfo($fileName);
		$ext = $pathinfo['extension'];
		$basename = basename($fileName,'.'.$ext);
		$files['small'] = $this->fileExists($basename.'__small'.'.'.$ext);
		$files['midium'] = $this->fileExists($basename.'__midium'.'.'.$ext);
		$files['large'] = $this->fileExists($basename.'__large'.'.'.$ext);
		return $files;

	}
/**
 * コントロールソースを取得する
 *
 * @param	string	$field			フィールド名
 * @param	array	$options
 * @return	mixed	$controlSource	コントロールソース
 * @access	public
 */
	function getControlSource($field = null, $options = array()) {

		switch ($field) {
			case 'user_id':
				$User = ClassRegistry::getObject('User');
				return $User->getUserList($options);
			case 'uploader_category_id':
				$UploaderCategory = ClassRegistry::init('Uploader.UploaderCategory');
				return $UploaderCategory->find('list', array('order' => 'UploaderCategory.id'));
		}
		return false;

	}

}
?>