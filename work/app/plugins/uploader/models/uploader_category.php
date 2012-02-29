<?php
/* SVN FILE: $Id$ */
/**
 * ファイルカテゴリモデル
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
 * ファイルカテゴリモデル
 *
 * @package			uploader.models
 */
class UploaderCategory extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access	public
 */
	var $name = 'UploaderCategory';
/**
 * DB接続設定
 *
 * @var		string
 * @access	public
 */
	var $useDbConfig = 'plugin';
/**
 * プラグイン名
 *
 * @var		string
 * @access	public
 */
	var $plugin = 'Uploader';
/**
 * バリデート
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'name' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'カテゴリ名を入力してください。')));
}
?>