<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーモデル
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
 * ユーザーモデル
 *
 * @package baser.models
 */
class User extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'User';
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
 * 
 * @var array
 * @access public
 */
	var $belongsTo = array('UserGroup' =>   array(  'className'=>'UserGroup',
							'foreignKey'=>'user_group_id'));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name'=>array(
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> 'アカウント名を入力してください。'
			),
			'alphaNumericPlus' => array(
				'rule'		=>	'alphaNumericPlus',
				'message'	=> 'アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。'
			),
			'duplicate' => array(
				'rule'		=>	array('duplicate','name'),
				'message'	=> '既に登録のあるアカウント名です。'
			),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'アカウント名は255文字以内で入力してください。'
			)
		),
		'real_name_1' => array(
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> '名前[姓]を入力してください。'),
			'maxLength' => array(
				'rule'		=> array('maxLength', 50),
				'message'	=> 'アカウント名は50文字以内で入力してください。'
			)
		),
		'real_name_2' => array(
			'maxLength' => array(
				'rule'		=> array('maxLength', 50),
				'message'	=> '名前[名]は50文字以内で入力してください。'
			)
		),
		'password' => array(
			'minLength' => array(
				'rule'		=> array('minLength',6),
				'allowEmpty'=> false,
				'message'	=> 'パスワードは６文字以上で入力してください。'
			),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'パスワードは255文字以内で入力してください。'
			),
			'alphaNumeric' => array(
				'rule'		=> 'alphaNumericPlus',
				'message'	=> 'パスワードは半角英数字とハイフン、アンダースコアのみで入力してください。'
			),
			'confirm' => array(
				'rule'		=> array('confirm', array('password_1', 'password_2')),
				'message'	=> 'パスワードが同じものではありません。'
			)
		),
		'email' => array(
			'email' => array(
				'rule'		=> array('email'),
				'message'	=> 'Eメールの形式が不正です。',
				'allowEmpty'=> true),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'Eメールは255文字以内で入力してください。')
		),
		'user_group_id'=>array(
			'rule'		=> array('notEmpty'),
			'message'	=> 'グループを選択してください。'
		)
	);
/**
 * validates
 *
 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
 * @return boolean True if there are no errors
 * @access public
 */
	function validates($options = array()) {
		
		$result = parent::validates($options);
		if(isset($this->validationErrors['password'])) {
			$this->invalidate('password_1');
			$this->invalidate('password_2');
		}
		return $result;
		
	}
/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	function getControlSource($field) {

		switch($field) {
			
			case 'user_group_id':
				$controlSources['user_group_id'] = $this->UserGroup->find('list');
				break;
		
		}
		
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * ユーザーリストを取得する
 * 条件を指定する場合は引数を指定する
 * 
 * @param array $authUser
 * @return array
 * @access public
 */
	function getUserList($conditions = array()) {

		$users = $this->find("all",array('fields'=>array('id','real_name_1','real_name_2'), 'conditions'=>$conditions));
		$list = array();
		if ($users) {
			// 苗字が同じ場合にわかりにくいので、foreachで生成
			//$this->set('users',Set::combine($users, '{n}.User.id', '{n}.User.real_name_1'));
			foreach($users as $key => $user) {
				if($user[$this->alias]['real_name_2']) {
					$name = $user[$this->alias]['real_name_1']." ".$user[$this->alias]['real_name_2'];
				}else {
					$name = $user[$this->alias]['real_name_1'];
				}
				$list[$user[$this->alias]['id']] = $name;
			}
		}
		return $list;
		
	}
/**
 * フォームの初期値を設定する
 *
 * @return array 初期値データ
 * @access public
 */
	function getDefaultValue() {

		$data['User']['user_group_id'] = 1;
		return $data;

	}
/**
 * afterFind
 *
 * @param array 結果セット
 * @param array $primary
 * @return array 結果セット
 * @access	public
 */
	function afterFind($results, $primary = false) {

		if(isset($results[0]['User'][0])) {
			$results[0]['User'] = $this->convertResults($results[0]['User']);
		}else {
			$results = $this->convertResults($results);
		}
		return parent::afterFind($results,$primary);

	}
/**
 * 取得結果を変換する
 * HABTM対応
 *
 * @param array 結果セット
 * @return array 結果セット
 * @access public
 */
	function convertResults($results) {

		if($results) {
			if(isset($result['User'])||isset($results[0]['User'])) {
				foreach($results as $key => $result) {
					if(isset($result['User'])) {
						if($result['User']) {
							$results[$key]['User'] = $this->convertToView($result['User']);
						}
					}elseif(!empty($result)) {
						$results[$key] = $this->convertToView($result);
					}
				}
			}else {
				$results = $this->convertToView($results);
			}
		}
		return $results;

	}
/**
 * View用のデータを取得する
 *
 * @param array 結果セット
 * @return array 結果セット
 * @access public
 */
	function convertToView($data) {

		return $data;

	}
/**
 * ユーザーが許可されている認証プレフィックスを取得する
 *
 * @param string $userName
 * @return string
 */
	function getAuthPrefix($userName) {

		$user = $this->find('first', array(
			'conditions'	=> array('User.name'=>$userName),
			'recursive'		=> 1
		));

		if(isset($user['UserGroup']['auth_prefix'])) {
			return $user['UserGroup']['auth_prefix'];
		} else {
			return '';
		}

	}

}
?>