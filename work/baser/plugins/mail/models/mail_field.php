<?php
/* SVN FILE: $Id$ */
/**
 * メールフィールドモデル
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
 * @package			baser.plugins.mail.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * メールフィールドモデル
 *
 * @package baser.plugins.mail.models
 *
 */
class MailField extends MailAppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'MailField';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Cache');
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "項目名を入力してください。"),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '項目名は255文字以内で入力してください。')
		),
		'field_name' => array(
			array(	'rule'		=> array('halfText'),
					'message'	=> 'フィールド名は半角のみで入力してください。',
					'allowEmpty'=> false),
			array(	'rule'		=> 'duplicateMailField',
					'message'	=> '入力されたフィールド名は既に登録されています。'),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'フィールド名は255文字以内で入力してください。')
		),
		'type' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "タイプを入力してください。")
		),
		'head' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '項目見出しは255文字以内で入力してください。')
		),
		'attention' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '注意書きは255文字以内で入力してください。')
		),
		'before_attachment' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '前見出しは255文字以内で入力してください。')
		),
		'after_attachment' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '後見出しは255文字以内で入力してください。')
		),
		'source' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '選択リストは255文字以内で入力してください。')
		),
		'options' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'オプションは255文字以内で入力してください。')
		),
		'class' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'クラス名は255文字以内で入力してください。')
		),
		'separator' => array(
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> '区切り文字は20文字以内で入力してください。')
		),
		'default_value' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '初期値は255文字以内で入力してください。')
		),
		'description' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '説明文は255文字以内で入力してください。')
		),
		'group_field' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'グループフィールドは255文字以内で入力してください。')
		),
		'group_valid' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'グループ入力チェックは255文字以内で入力してください。')
		)
	);
/**
 * コントロールソースを取得する
 *
 * @param string $field
 * @return array source
 * @access public
 */
	function getControlSource($field = null) {

		$source['type'] = array(
			'text'				=> 'テキスト',
			'textarea'			=> 'テキストエリア',
			'radio'				=> 'ラジオボタン',
			'select'			=> 'セレクトボックス',
			'email'				=> 'Eメール',
			'multi_check'		=> 'マルチチェックボックス',
			'autozip'			=> '自動補完郵便番号',
			'pref'				=> '都道府県リスト',
			'date_time_wareki'	=> '和暦日付',
			'date_time_calender'=> 'カレンダー',
			'hidden'			=> '隠しフィールド'
		);

		$source['valid'] = array(
			'VALID_NOT_EMPTY'	=> '入力必須',
			'VALID_EMAIL'		=> 'Eメールチェック',
			'/^(|[0-9]+)$/'		=> '数値チェック',
			'/^([0-9]+)$/'		=> '数値チェック（入力必須）'
		);

		$source['valid_ex'] = array(
			'VALID_EMAIL_CONFIRM'	=> 'Eメール比較チェック',
			'VALID_GROUP_COMPLATE'	=> 'グループチェック',
			'VALID_NOT_UNCHECKED'	=> 'チェックなしチェック',
			'VALID_DATETIME'		=> '日付チェック'
		);
		
		$source['auto_convert'] = array('CONVERT_HANKAKU'=>'半角変換');

		if($field) {
			return $source[$field];
		}else {
			return $source;
		}
		
	}
/**
 * 同じ名称のフィールド名がないかチェックする
 * 同じメールコンテンツが条件
 * 
 * @param array $check
 * @return boolean
 * @access public
 */
	function duplicateMailField($check) {

		$conditions = array('MailField.'.key($check)=>$check[key($check)],
				'MailField.mail_content_id' => $this->data['MailField']['mail_content_id']);
		if($this->exists()) {
			$conditions['NOT'] = array('MailField.id'=>$this->id);
		}
		$ret = $this->find($conditions);
		if($ret) {
			return false;
		}else {
			return true;
		}

	}
	
}
?>