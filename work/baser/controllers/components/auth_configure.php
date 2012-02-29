<?php
/* SVN FILE: $Id$ */
/**
 * 認証設定コンポーネント
 * (注）BaserのDB設計に依存している
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
 * @package			baser.controllers.components
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 * /
/**
 * 認証設定コンポーネント
 *
 * @package baser.controllers.components
 */
class  AuthConfigureComponent extends Object {
/**
 * コントローラー
 * 
 * @var Controller
 * @access	public
 */
	var $controller = null;
/**
 * initialize
 *
 * @param object $controller
 * @return void
 * @access public
 */
	function initialize(&$controller) {
		
		$this->controller = $controller;
		
	}
/**
 * 認証設定
 *
 * @param string $config
 * @return boolean
 * @access public
 */
	function setting($config) {

		if(empty($this->controller->AuthEx)) {
			return false;
		}

		$controller =& $this->controller;
		$auth =& $controller->AuthEx;
		$requestedPrefix = '';
		
		if(isset($controller->params['prefix'])) {
			$requestedPrefix = $controller->params['prefix'];
		}
		
		if(!$requestedPrefix) {
			return true;
		}
		
		$_config = array(
			'loginRedirect' => '/'.$requestedPrefix,
			'username'		=> 'name',
			'password'		=> 'password',
			'serial'		=> '',
			'userScope'		=> ''
		);
		$config = array_merge($_config, $config);
		extract($config);

		if(empty($userModel)) {
			$userModel = 'User';
		}
		if(empty($loginAction)) {
			$loginAction = '/'.$requestedPrefix.'/users/login';
		}
		// オートリダイレクトをOFF
		$auth->autoRedirect = false;
		// エラーメッセージ
		$auth->loginError = '入力されたログイン情報を確認できませんでした。もう一度入力してください。';
		// 権限が無いactionを実行した際のエラーメッセージ
		$auth->authError = '指定されたページを開くにはログインする必要があります。';
		//ユーザIDとパスワードのフィールドを指定
		$auth->fields = array('username' => $username, 'password' => $password, 'serial' => $serial);
		$auth->authorize = 'controller';
		// ユーザIDとパスワードがあるmodelを指定('User'がデフォルト)
		$auth->userModel = $userModel;
		// AppController::isAuthorizeでチェックするのでコメントアウト
		/*if($requestedPrefix) {
			//$auth->userScope = array('UserGroup.auth_prefix'=>$requestedPrefix);
		}*/
		if($userScope) {
			$auth->userScope = $userScope;
		}

		// セッション識別
		$auth->sessionKey = 'Auth.'.$userModel;
		// ログインアクション
		$auth->loginAction = $loginAction;

		$redirect = $auth->Session->read('Auth.redirect');
		// 記録された過去のリダイレクト先が対象のプレフィックス以外の場合はリセット
		if($redirect && strpos($redirect, $requestedPrefix)===false) {
			$auth->Session->write('Auth.redirect',null);
		}

		// ログイン後にリダイレクトするURL
		$auth->loginRedirect = $loginRedirect;

		if(!$auth->user()) {
			// クッキーがある場合にはクッキーで認証
			$cookie = $controller->Cookie->read($auth->sessionKey);
			if(!empty($cookie)) {
				$auth->login($cookie);
				return true;
			}
			// インストールモードの場合は無条件に認証なし
			if(Configure::read('debug')==-1) {
				$controller->Session->del('Message.auth');
				$auth->allow();
			}
		}
		
		return true;

	}

}
?>