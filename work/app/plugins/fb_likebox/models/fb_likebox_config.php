<?php
/**
 * Facebook LikeBoxプラグイン設定モデル
 *
 * @copyright		Copyright 2011, materializing.
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			fb_likebox.models
 * @since			Baser v 1.6.11.2
 * @version			1.1.0
 * @license			GPL
 */
class FbLikeboxConfig extends AppModel {
/**
 * モデル名
 * @var string
 * @access public
 */
	var $name = 'FbLikeboxConfig';
/**
 * プラグイン名
 * @var string
 * @access public
 */
	var $plugin = 'FbLikebox';
/**
 * DB設定
 * @var string
 * @access public
 */
	var $useDbConfig = 'plugin';
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
			'page_url'	=> array(
				array(
					'rule'		=>	'notEmpty',
					'message'	=>	'入力必須です。'
				),
				array(
					'rule'		=>	'url',
					'message'	=>	'URL形式で入力して下さい。'
				)
			),
			'width'		=> array(
				'rule'			=>	'numeric',
				'message'		=>	'半角数字で入力して下さい。',
				'allowEmpty'	=> true
			),
			'border_color'	=> array(
				'rule'			=>	'halfText',
				'message'		=>	'半角英字で入力して下さい。'
			)
	);
/**
 * 表示設定値
 *
 * @var array
 * @access public
 */
	var $color_scheme = array(
		'1'		=>	'light',
		'2'		=>	'dark'
	);
	var $show_faces = array(
		'0'		=>	'false',
		'1'		=>	'true'
	);
	var $stream = array(
		'0'		=>	'false',
		'1'		=>	'true'
	);
	var $header = array(
		'0'		=>	'false',
		'1'		=>	'true'
	);
	var $language = array(
		'1'		=>	'ja_JP',
		'2'		=>	'en_US'
	);
/**
 * チェックして、空文字、FALSE、NULL なら0を返す
 *
 * @param int $param
 * @return int
 */
	function checkEmpty($param) {
		if(!$param) {
			$param = 0;
		}
		return $param;
	}
}
?>