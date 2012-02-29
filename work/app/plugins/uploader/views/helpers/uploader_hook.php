<?php
/* SVN FILE: $Id$ */
/**
 * Updaterフックヘルパー
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
 * @package			updater.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class UploaderHookHelper extends AppHelper {
/**
 * フック登録
 * @var		array
 * @access	public
 */
	var $registerHooks = array('afterLayout');
/**
 * コンストラクタ
 * @access	public
 */
	function __construct() {

		// TODO プラグインフックの仕組みとしてヘルパが自動初期化されないので明示的に初期化
		$this->Javascript = new JavascriptHelper();
		$this->HtmlEx = new HtmlExHelper();

	}
/**
 * afterLayout
 *
 * @return	void
 * @access	pubic
 */
	function afterLayout() {

		$view =& ClassRegistry::getObject('view');

		if($view) {

			if(isset($view->loaded['ckeditor'])) {

				if(preg_match_all("/var\s*?(editor_[a-z0-9_]*?)\s*?=\s*?CKEDITOR\.replace/s",$view->output,$matches)) {

					/* ckeditor_uploader.js を読み込む */
					$jscode = $this->Javascript->codeBlock("var baseUrl ='".$this->base."/';");
					$jscode .= $this->Javascript->link('/uploader/js/ckeditor_uploader');
					$view->output = str_replace('</head>',$jscode.'</head>',$view->output);

					/* CSSを読み込む */
					// 適用の優先順位の問題があるので、bodyタグの直後に読み込む
					$css = $this->HtmlEx->css('/uploader/css/uploader');
					$view->output = str_replace('</body>',$css.'</body>',$view->output);

					/* VIEWのCKEDITOR読込部分のコードを書き換える */
					foreach($matches[1] as $key => $match) {
						$jscode = $this->__getCkeditorUploaderScript($match);
						$pattern = "/<script type=\"text\/javascript\">(.*?var\s*?".$match."\s*?=\s*?CKEDITOR.replace.*?)\/\/\]\]>\n*?<\/script>/s";
						$output = preg_replace($pattern,$this->Javascript->codeBlock("$1".$jscode),$view->output);
						if(!is_null($output)) {
							$view->output = $output;
						}
					}

					/* 通常の画像貼り付けダイアログを画像アップローダーダイアログに変換する */
					$pattern = "/(CKEDITOR\.replace.*?\"toolbar\".*?)\"Image\"(.*?);/is";
					$view->output = preg_replace($pattern,"$1".'"BaserUploader"'."$2;",$view->output);

				}

			}
			if (!empty($view->params['prefix']) && $view->params['prefix'] == 'mobile') {

				/* モバイル画像に差し替える */
				$aMatch = "/<a([^>]*?)href=\"([^>]*?)\"([^>]*?)><img([^>]*?)\/><\/a>/is";
				$imgMatch = "/<img([^>]*?)src=\"([^>]*?)\"([^>]*?)\/>/is";
				$view->output = preg_replace_callback($aMatch,array($this,"__mobileImageAnchorReplace"),$view->output);
				$view->output = preg_replace_callback($imgMatch,array($this,"__mobileImageReplace"),$view->output);

			}

		}

	}
/**
 * CKEditorのアップローダーを組み込む為のJavascriptを返す
 *
 * 「baserUploader」というコマンドを登録し、そのコマンドが割り当てられてボタンをツールバーに追加する
 * {EDITOR_NAME}.addCommand	// コマンドを追加
 * {EDITOR_NAME}.addButton	// ツールバーにボタンを追加
 * ※ {EDITOR_NAME} は、コントロールのIDに変換する前提
 *
 * @return	string
 * @access	private
 */
	function __getCkeditorUploaderScript($id) {

		return <<< DOC_END
				{$id}.on( 'pluginsLoaded', function( ev ) {
				{$id}.addCommand( 'baserUploader', new CKEDITOR.dialogCommand( 'baserUploaderDialog' ));
				{$id}.ui.addButton( 'BaserUploader', { label : 'アップローダー', command : 'baserUploader' });
});
DOC_END;

	}
/**
 * 画像タグをモバイル用に置き換える
 *
 * @param	array	$matches
 * @return	string
 * @access	private
 */
	function __mobileImageReplace($matches) {

		$url = $matches[2];
		$pathinfo = pathinfo($url);

		if(!isset($pathinfo['extension'])) {
			return $matches[0];
		}

		$url = str_replace('__small','',$url);
		$url = str_replace('__midium','',$url);
		$url = str_replace('__large','',$url);
		$basename = basename($url,'.'.$pathinfo['extension']);
		$_url = 'files'.DS.'uploads'.DS.$basename.'__mobile_small.'.$pathinfo['extension'];
		// TODO uploads固定となってしまっているのでmodelから取得するようにする
		$path = WWW_ROOT.$_url;

		if(file_exists($path)) {
			return '<img'.$matches[1].'src="'.$this->webroot($_url).'"'.$matches[3].'/>';
		}else {
			return $matches[0];
		}

	}
/**
 * アンカータグのリンク先が画像のものをモバイル用に置き換える
 *
 * @param	array	$matches
 * @return	string
 * @access	private
 */
	function __mobileImageAnchorReplace($matches) {

		$url = $matches[2];
		$pathinfo = pathinfo($url);

		if(!isset($pathinfo['extension'])) {
			return $matches[0];
		}

		$url = str_replace('__small','',$url);
		$url = str_replace('__midium','',$url);
		$url = str_replace('__large','',$url);
		$basename = basename($url,'.'.$pathinfo['extension']);
		$_url = 'files'.DS.'uploads'.DS.$basename.'__mobile_large.'.$pathinfo['extension'];
		// TODO uploads固定となってしまっているのでmodelから取得するようにする
		$path = WWW_ROOT.$_url;

		if(file_exists($path)) {
			return '<a'.$matches[1].'href="'.$this->webroot($_url).'"'.$matches[3].'><img'.$matches[4].'/></a>';
		}else {
			return $matches[0];
		}

	}

}
?>