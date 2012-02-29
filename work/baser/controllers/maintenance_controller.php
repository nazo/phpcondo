<?php
/* SVN FILE: $Id$ */
/**
 * メンテナンスコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * メンテナンスコントローラー
 *
 * @package baser.controllers
 */
class MaintenanceController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Maintenance';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = null;
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array();
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * メンテナンス中ページを表示する
 *
 * @return void
 * @access	public
 */
	function index() {

		$this->pageTitle = 'メンテナンス中';

	}
/**
 * [モバイル] メンテナンス中ページを表示する
 *
 * @return void
 * @access	public
 */
	function mobile_index() {
		
		$this->pageTitle = 'メンテナンス中';
		
	}
	
}
?>