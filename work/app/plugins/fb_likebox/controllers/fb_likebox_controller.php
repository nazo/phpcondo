<?php
/**
 * Facebook LikeBoxプラグイン表示コントローラー
 *
 * @copyright		Copyright 2011, materializing.
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			fb_likebox.controllers
 * @since			Baser v 1.6.11.2
 * @version			1.1.0
 * @license			GPL
 */
App::import('Controller', 'Plugins');
class FbLikeboxController extends PluginsController {
/**
 * コントローラー名
 * @var string
 * @access public
 */
	var $name = 'FbLikebox';
/**
 * モデル
 * @var array
 * @access public
 */
	var $uses = array('Plugin', 'FbLikebox.FbLikeboxConfig');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array('fb_likebox');
/**
 * Facebook LikeBoxプラグイン表示
 *
 * @return void
 * @access public
 */
	function  admin_index() {

		$data = $this->FbLikeboxConfig->findExpanded();

		// 返ってくる値をチェックして、空文字、FALSE、NULLの場合は0を入れる
		$data['show_faces'] = $this->FbLikeboxConfig->checkEmpty($data['show_faces']);
		$data['stream'] = $this->FbLikeboxConfig->checkEmpty($data['stream']);
		$data['header'] = $this->FbLikeboxConfig->checkEmpty($data['header']);

		// 選択値の設定値を取得
		$this->set('show_faces', $this->FbLikeboxConfig->show_faces);
		$this->set('stream', $this->FbLikeboxConfig->stream);
		$this->set('header', $this->FbLikeboxConfig->header);
		$this->set('color_scheme', $this->FbLikeboxConfig->color_scheme);
		$this->set('language', $this->FbLikeboxConfig->language);

		$this->set('data', $data);

		$this->pageTitle = 'Facebook LikeBoxプラグイン';

	}

}
?>