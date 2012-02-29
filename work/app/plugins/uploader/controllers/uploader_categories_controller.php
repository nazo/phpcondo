<?php
/* SVN FILE: $Id$ */
/**
 * ファイルカテゴリコントローラー
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
 * @package			uploader.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Controller', 'Plugins');
/**
 * ファイルカテゴリコントローラー
 *
 * @package			uploader.controllers
 */
class UploaderCategoriesController extends PluginsController {
/**
 * クラス名
 *
 * @var		string
 * @access	public
 */
	var $name = 'UploaderCategories';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	var $uses = array('Plugin', 'Uploader.UploaderCategory');
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * サブメニュー
 *
 * @var		array
 * @access	public
 */
	var $subMenuElements = array('uploader');
/**
 * ファイルカテゴリ一覧
 *
 * @return	void
 * @access	public
 */
	function admin_index() {

		$this->pageTitle = 'カテゴリ一覧';
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('UploaderCategory', array('default' => $default));
		$this->paginate = array(
				'order'=>'UploaderCategory.id',
				'limit'=>$this->passedArgs['num']
		);
		$this->set('datas', $this->paginate('UploaderCategory'));

	}
/**
 * 新規登録
 *
 * @return	void
 * @access	public
 */
	function admin_add() {

		if($this->data) {
			$this->UploaderCategory->set($this->data);
			if($this->UploaderCategory->save()) {
				$message = 'アップロードファイルカテゴリ「'.$this->data['UploaderCategory']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->UploaderCategory->saveDbLog($message);
				$this->redirect(array('action'=>'edit', $this->UploaderCategory->getInsertID()));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
		}
		$this->pageTitle = 'カテゴリ新規登録';
		$this->render('form');
		
	}
/**
 * 編集
 *
 * @return	void
 * @access	public
 */
	function admin_edit($id = null) {


		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->UploaderCategory->read(null, $id);
		}else {

			$this->UploaderCategory->set($this->data);
			if($this->UploaderCategory->save()) {
				$message = 'アップロードファイルカテゴリ「'.$this->data['UploaderCategory']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->UploaderCategory->saveDbLog($message);
				$this->redirect(array('action'=>'edit', $id));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		$this->pageTitle = 'カテゴリ編集';
		$this->render('form');
		
	}
/**
 * 削除
 *
 * @param	int		$id
 * @return	void
 * @access	public
 */
	function admin_delete($id = null) {

		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'index'));
		}

		// メッセージ用にデータを取得
		$name = $this->UploaderCategory->field('name', array('UploaderCategory.id' => $id));

		if($this->UploaderCategory->del($id)) {
			$message = 'アップロードファイルカテゴリ「'.$name.'」を削除しました。';
			$this->Session->setFlash($message);
			$this->UploaderCategory->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect('index');
		
	}

}
?>
