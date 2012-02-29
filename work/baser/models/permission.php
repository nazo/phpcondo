<?php
/* SVN FILE: $Id$ */
/**
 * パーミッションモデル
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
 * @package			baser.models
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
 * パーミッションモデル
 *
 * @package baser.models
 */
class Permission extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Permission';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Cache');
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * belongsTo
 * @var array
 * @access public
 */
	var $belongsTo = array('UserGroup' =>   array(  'className'=>'UserGroup',
							'foreignKey'=>'user_group_id'));
/**
 * permissionsTmp
 * ログインしているユーザーの拒否URLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @access public
 */
	var $permissionsTmp = -1;
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '設定名を入力してください。'),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '設定名は255文字以内で入力してください。')
		),
		'user_group_id' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'ユーザーグループを選択してください。',
					'required'	=> true)
		),
		'url' => array(	
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '設定URLを入力してください。'),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '設定URLは255文字以内で入力してください。'),
			array(	'rule'		=> array('checkUrl'),
					'message'	=> 'アクセス拒否として設定できるのは認証ページだけです。')
		)
	);
/**
 * 設定をチェックする
 *
 * @param array $check
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 */
	function checkUrl($check) {

		if(!$check[key($check)]) {
			return true;
		}
		$url = $check[key($check)];
		if(preg_match('/^[^\/]/is',$url)) {
			$url = '/'.$url;
		}
		if(preg_match('/^(\/[a-z_]+)\*$/is',$url,$matches)) {
			$url = $matches[1].'/'.'*';
		}
		$params = Router::parse($url);
		if(empty($params['prefix'])) {
			return false;
		}

		return true;

	}
/**
 * 認証プレフィックスを取得する
 *
 * @param int $id
 * @return string
 * @access public
 */
	function getAuthPrefix($id) {

		// CSV の場合、他テーブルの fields を指定するとデータが取得できない
		$data = $this->find('first', array(
			'conditions'=>array('Permission.id'=>$id),
			/*'fields'=>array('UserGroup.auth_prefix'),*/
			'recursive'=>1
		));
		if(isset($data['UserGroup']['auth_prefix'])) {
			return $data['UserGroup']['auth_prefix'];
		} else {
			return '';
		}

	}
/**
 * 初期値を取得する
 * @return array
 * @access public
 */
	function getDefaultValue() {
		
		$data['Permission']['auth'] = 0;
		$data['Permission']['status'] = 1;
		return $data;
		
	}
/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access	public
 */
	function getControlSource($field = null) {

		$controlSources['user_group_id'] = $this->UserGroup->find('list',array('conditions'=>array('UserGroup.id <>'=>1)));
		$controlSources['auth'] = array('0'=>'不可','1'=>'可');
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * beforeSave
 * 
 * @param array $options
 * @return boolean
 * @access public
 */
	function beforeSave($options) {
		
		if(isset($this->data['Permission'])) {
			$data = $this->data['Permission'];
		}else {
			$data = $this->data;
		}
		if(isset($data['url'])) {
			if(preg_match('/^[^\/]/is',$data['url'])) {
				$data['url'] = '/'.$data['url'];
			}
		}
		$this->data['Permission'] = $data;
		return true;
		
	}
/**
 * 権限チェックを行う
 * 
 * @param array $url
 * @param string $userGroupId
 * @param array $params
 * @return boolean
 * @access public
 */
	function check($url, $userGroupId) {

		if($this->permissionsTmp === -1) {
			$conditions = array('Permission.user_group_id' => $userGroupId);
			$permissions = $this->find('all',array('conditions'=>$conditions,'order'=>'sort','recursive'=>-1));
			if($permissions) {
				$this->permissionsTmp = $permissions;
			}else {
				$this->permissionsTmp = array();
				return true;
			}
		}

		$permissions = $this->permissionsTmp;

		if($url!='/') {
			$url = preg_replace('/^\//is', '', $url);
		}
		
		// ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
		$allows = array(
			'admin',
			'admin/',
			'admin/dashboard/index',
			'admin/users/edit/'.$_SESSION['Auth']['User']['id'],
			'admin/users/logout'
		);
		if(in_array($url,$allows)) {
			return true;
		}
		
		$ret = true;
		foreach($permissions as $permission) {
			if(!$permission['Permission']['status']) {
				continue;
			}
			if($permission['Permission']['url']!='/') {
				$pattern = preg_replace('/^\//is', '', $permission['Permission']['url']);
			}else {
				$pattern = $permission['Permission']['url'];
			}
			$pattern = addslashes($pattern);
			$pattern = str_replace('/', '\/', $pattern);
			$pattern = str_replace('*', '.*?', $pattern);
			$pattern = '/^'.str_replace('\/.*?', '(|\/.*?)', $pattern).'$/is';
			//var_dump($pattern);
			if(preg_match($pattern, $url)) {
				$ret = $permission['Permission']['auth'];
			}
		}
		return $ret;

	}
}
?>