<?php
/* SVN FILE: $Id$ */
/**
 * ファイルアップローダー設定モデル
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
 * ファイルアップローダー設定モデル
 *
 * @package			baser.plugins.uploader.models
 */
class UploaderConfig extends AppModel {
/**
 * モデル名
 * @var     string
 * @access  public
 */
	var $name = 'UploaderConfig';
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
 * バリデート
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'large_width' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（大）[幅] を入力してください。')),
		'large_height' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（大）[高さ] を入力してください。')),
		'midium_width' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（中）[幅] を入力してください。')),
		'midium_height' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（中）[高さ] を入力してください。')),
		'small_width' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（小）[幅] を入力してください。')),
		'small_height' => array(array(	'rule'		=> array('notEmpty'),
										'message'	=> 'PCサイズ（小）[高さ] を入力してください。')),
		'mobile_large_width' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（大）[幅] を入力してください。')),
		'mobile_large_height' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（大）[高さ] を入力してください。')),
		'mobile_small_width' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（小）[幅] を入力してください。')),
		'mobile_small_height' => array(array('rule'		=> array('notEmpty'),
											'message'	=> '携帯サイズ（小）[幅] を入力してください。'))
	);
}