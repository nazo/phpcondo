<?php
/* SVN FILE: $Id$ */
/**
 * CKEditorヘルパー
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
 * @package			baser.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class CkeditorHelper extends AppHelper {
/**
 * ヘルパー
 * @var array
 * @access public
 */
	var $helpers = array('Javascript', 'Form');
/**
 * スクリプト
 * 既にjavascriptが読み込まれている場合はfalse
 * 
 * @var boolean
 * @access public
 */
	var $_script = false;
/**
 * 初期化状態
 * 複数のCKEditorを設置する場合、一つ目を設置した時点で true となる
 *
 * @var boolean
 * @access public
 */
	var $inited = false;
/**
 * 初期設定スタイル
 * StyleSet 名 basercms
 *
 * @var array
 * @access public
 */
	var $style = array(
					array(	'name' => '青見出し(h3)',
							'element' => 'h3',
							'styles' => array('color'=>'Blue')),
					array(	'name' => '赤見出し(h3)',
							'element' => 'h3',
							'styles' => array('color' => 'Red')),
					array(	'name' => '黄マーカー(span)',
							'element' => 'span',
							'styles' => array('background-color' => 'Yellow')),
					array(	'name' => '緑マーカー(span)',
							'element' => 'span',
							'styles' => array('background-color' => 'Lime')),
					array(	'name' => '大文字(big)',
							'element' => 'big'),
					array(	'name' => '小文字(small)',
							'element' => 'small'),
					array( 	'name' => 'コード(code)',
							'element' => 'code'),
					array( 	'name' => '削除文(del)',
							'element' => 'del'),
					array( 	'name' => '挿入文(ins)',
							'element' => 'ins'),
					array(	'name' => '引用(cite)',
							'element' => 'cite'),
					array( 	'name' => 'インライン(q)',
							'element' => 'q')
			);
	function __construct() {
		parent::__construct();

	}
/**
 * CKEditor のスクリプトを構築する
 * 【ボタン一覧】
 * Source			- ソース
 * Save				- 保存
 * NewPage			- 新しいページ
 * Preview			- プレビュー
 * Templates		- テンプレート
 * Cut				- 切り取り
 * Copy				- コピー
 * Paste			- 貼り付け
 * PasteText		- プレーンテキスト貼り付け
 * PasteFromWord	- ワードから貼り付け
 * Print			- 印刷
 * SpellChecker		- スペルチェック
 * Scayt			- スペルチェック設定
 * Undo				- 元に戻す
 * Redo				- やり直し
 * Find				- 検索
 * Replace			- 置き換え
 * SelectAll		- すべて選択
 * RemoveFormat		- フォーマット削除
 * Form				- フォーム
 * Checkbox			- チェックボックス
 * Radio			- ラジオボタン
 * TextField		- 1行テキスト
 * Textarea			- テキストエリア
 * Select			- 選択フィールド
 * Button			- ボタン
 * ImageButton		- 画像ボタン
 * HiddenField		- 不可視フィールド
 * Bold				- 太字
 * Italic			- 斜体
 * Underline		- 下線
 * Strike			- 打ち消し線
 * Subscript		- 添え字
 * Superscript		- 上付き文字
 * NumberedList		- 段落番号
 * BulletedList		- 箇条書き
 * Outdent			- インデント解除
 * Indent			- インデント
 * Blockquote		- ブロック引用
 * JustifyLeft		- 左揃え
 * JustifyCenter	- 中央揃え
 * JustifyRight		- 右揃え
 * JustifyBlock		- 両端揃え
 * Link				- リンク挿入／編集
 * Unlink			- リンク解除
 * Anchor			- アンカー挿入／編集
 * Image			- イメージ
 * Flash			- FLASH
 * Table			- テーブル
 * HorizontalRule	- 横罫線
 * Smiley			- 絵文字
 * SpecialChar		- 特殊文字
 * PageBreak		- 改ページ挿入
 * Styles			- スタイル
 * Format			- フォーマット
 * Font				- フォント
 * FontSize			- フォントサイズ
 * TextColor		- テキスト色
 * BGColor			- 背景色
 * Maximize			- 最大化
 * ShowBlocks		- ブロック表示
 * About			- CKEditorバージョン情報
 * Publish			- 本稿に切り替え
 * Draft			- 草稿に切り替え
 * CopyPublish		- 本稿を草稿にコピー
 * CopyDraft		- 草稿を本稿にコピー
 *
 * @param string $fieldName
 * @param array $ckoptions
 * @return string
 * @access protected
 */
	function _build($fieldName, $ckoptions = array(), $styles = array()) {

		if(isset($ckoptions['stylesSet'])) {
			$stylesSet = $ckoptions['stylesSet'];
			unset($ckoptions['stylesSet']);
		} else {
			$stylesSet = 'basercms';
		}
		if(isset($ckoptions['useDraft'])) {
			$useDraft = $ckoptions['useDraft'];
			unset($ckoptions['useDraft']);
		} else {
			$useDraft = false;
		}
		if(isset($ckoptions['draftField'])) {
			$draftField = $ckoptions['draftField'];
			unset($ckoptions['draftField']);
		} else {
			$draftField = false;
		}
		if(isset($ckoptions['disablePublish'])) {
			$disablePublish = $ckoptions['disablePublish'];
			unset($ckoptions['disablePublish']);
		} else {
			$disablePublish = false;
		}
		if(isset($ckoptions['disableDraft'])) {
			$disableDraft = $ckoptions['disableDraft'];
			unset($ckoptions['disableDraft']);
		} else {
			$disableDraft = true;
		}
		if(isset($ckoptions['disableCopyDraft'])) {
			$disableCopyDraft = $ckoptions['disableCopyDraft'];
			unset($ckoptions['disableCopyDraft']);
		} else {
			$disableCopyDraft = false;
		}
		if(isset($ckoptions['disableCopyPublish'])) {
			$disableCopyPublish = $ckoptions['disableCopyPublish'];
			unset($ckoptions['disableCopyPublish']);
		} else {
			$disableCopyPublish = false;
		}
		if(isset($ckoptions['readOnlyPublish'])) {
			$readOnlyPublish = $ckoptions['readOnlyPublish'];
			unset($ckoptions['readOnlyPublish']);
		} else {
			$readOnlyPublish = false;
		}

		$jscode = '';
		if(strpos($fieldName,'.')) {
			list($model,$field) = explode('.',$fieldName);
		}else {
			$field = $fieldName;
		}
		if($useDraft) {
			$srcField = $field;
			$field .= '_tmp';
			$srcFieldName = $fieldName;
			$fieldName .= '_tmp';
		}

		if($useDraft) {
			$publishAreaId = Inflector::camelize($model.'_'.$srcField);
			$draftAreaId = Inflector::camelize($model.'_'.$draftField);
		}

		if (!$this->_script) {
			$this->_script = true;
			$this->Javascript->link('/js/ckeditor/ckeditor.js', false);
		}
		
		$toolbar = array(
			'simple' => array(
				array(	'Bold', 'Underline', '-',
						'NumberedList', 'BulletedList', '-', 
						'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
						'Format', 'FontSize', 'TextColor', 'BGColor', 'Link', 'Image'),
				array(	'Maximize', 'ShowBlocks', 'Source')
			),
			'normal' => array(
				array(	'Cut', 'Copy', 'Paste', '-','Undo', 'Redo', '-', 'Bold', 'Italic', 'Underline', 'Strike', '-',
						'NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', '-', 
						'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-',
						'Smiley', 'Table', 'HorizontalRule', '-'),
				array(	'Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor', '-', 'Link', 'Unlink', '-', 'Image'),
				array(	'Maximize', 'ShowBlocks', 'Source')
			)
		);

		$_ckoptions = array('language' => 'ja',
			'type'	=> 'normal',
			'skin' => 'kama',
			'width' => '600px',
			'height' => '300px',
			'collapser' => false,
			'baseFloatZIndex' => 900,
		);
		
		$ckoptions = array_merge($_ckoptions,$ckoptions);

		if(empty($ckoptions['toolbar'])) {
			$ckoptions['toolbar'] = $toolbar[$ckoptions['type']];
		}

		if($useDraft) {			
			$lastBar = $ckoptions['toolbar'][count($ckoptions['toolbar'])-1];
			$lastBar = am($lastBar , array( '-', 'Publish', '-', 'Draft'));
			if(!$disableCopyDraft) {
				$lastBar = am($lastBar , array('-', 'CopyDraft'));
			}
			if(!$disableCopyPublish) {
				$lastBar = am($lastBar , array('-', 'CopyPublish'));
			}
			$ckoptions['toolbar'][count($ckoptions['toolbar'])-1] = $lastBar;
		}
		
		if(!$this->inited) {
			$jscode = "CKEDITOR.addStylesSet('basercms',".$this->Javascript->object($this->style).");";
			$this->inited = true;
		} else {
			$jscode = '';
		}
		if($styles) {
			foreach($styles as $key => $style) {
				$jscode .= "CKEDITOR.addStylesSet('".$key."',".$this->Javascript->object($style).");";
			}
		}
		$jscode .= "CKEDITOR.config.extraPlugins = 'draft,readonly';";
		$jscode .= "CKEDITOR.config.stylesCombo_stylesSet = '".$stylesSet."';";
		$jscode .= "var editor_" . $field ." = CKEDITOR.replace('" . $this->__name($fieldName) ."',". $this->Javascript->object($ckoptions) .");";
		$jscode .= "CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );";
		$jscode .= "editor_{$field}.on('pluginsLoaded', function(event) {";
		if($useDraft) {
			if($draftAreaId) {
				$jscode .= "editor_{$field}.draftDraftAreaId = '{$draftAreaId}';";
			}
			if($publishAreaId) {
				$jscode .= "editor_{$field}.draftPublishAreaId = '{$publishAreaId}';";
			}
			if($readOnlyPublish) {
				$jscode .= "editor_{$field}.draftReadOnlyPublish = true;";
			}
		}
		$jscode .= " });";
		if($useDraft) {
			$jscode .= "editor_{$field}.on('instanceReady', function(event) {";
			if($disableDraft) {
				$jscode .= "editor_{$field}.execCommand('disableDraft');";
			}
			if($disablePublish) {
				$jscode .= "editor_{$field}.execCommand('disablePublish');";
			}
			$jscode .= " });";
		}
		return $this->Javascript->codeBlock($jscode);
		
	}
/**
 * CKEditorのテキストエリアを出力する（textarea）
 *
 * @param string $fieldName
 * @param array $options
 * @param array $options
 * @return string
 */
	function textarea($fieldName, $options = array(), $editorOptions = array(), $styles = array(), $form = null) {
		
		if(!$form){
			$form = $this->Form;
		}
		if(!empty($editorOptions['useDraft']) && !empty($editorOptions['draftField']) && strpos($fieldName,'.')){
			list($model,$field) = explode('.',$fieldName);
			$inputFieldName = $fieldName.'_tmp';
			$hidden = $form->hidden($fieldName).$form->hidden($model.'.'.$editorOptions['draftField']);
		} else {
			$inputFieldName = $fieldName;
			$hidden = '';
		}
		return $form->textarea($inputFieldName, $options) . $hidden . $this->_build($fieldName, $editorOptions, $styles);
		
	}
/**
 * CKEditorのテキストエリアを出力する（input）
 *
 * @param string $fieldName
 * @param array $options
 * @param array $tinyoptions
 * @return string
 */
	function input($fieldName, $options = array(), $editorOptions = array(), $styles = array(), $form = null) {
		
		if(!$form){
			$form = $this->Form;
		}
		if(!empty($editorOptions['useDraft']) && !empty($editorOptions['draftField']) && strpos($fieldName,'.')){
			list($model,$field) = explode('.',$fieldName);
			$inputFieldName = $fieldName.'_tmp';
			$hidden = $form->hidden($fieldName).$form->hidden($model.'.'.$editorOptions['draftField']);
		} else {
			$inputFieldName = $fieldName;
			$hidden = '';
		}
		$options['type'] = 'textarea';
		return $form->input($inputFieldName, $options) . $hidden . $this->_build($fieldName, $editorOptions, $styles, $form);
		
	}
	
}
?>