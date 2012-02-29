<?php
/* SVN FILE: $Id$ */
/**
 * フィード詳細コントローラー
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
 * @package			baser.plugins.feed.controllers
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
 * フィード詳細コントローラー
 *
 * @package baser.plugins.feed.controllers
 */
class FeedDetailsController extends FeedAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'FeedDetails';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Feed.FeedDetail','Feed.FeedConfig', 'Feed.RssEx');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('FormEx');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array('フィード管理'=>'/admin/feed/feed_configs/index');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		parent::beforeFilter();
		$feedConfig = $this->FeedConfig->read(null,$this->params['pass'][0]);
		$this->navis = am($this->navis,array('フィード設定情報： '.$feedConfig['FeedConfig']['name']=>'/admin/feed/feed_configs/edit/'.$this->params['pass'][0]));
		if($this->params['prefix']=='admin') {
			$this->subMenuElements = array('feed_details');
		}
		
	}
/**
 * [ADMIN] 登録
 *
 * @param int feed_config_id
 * @return void
 * @access public
 */
	function admin_add($feedConfigId) {

		/* 除外処理 */
		if(!$feedConfigId) {
			$this->Session->setFlash('無効なIDです');
			$this->redirect(array('controller'=>'feed_configs','action'=>'index'));
		}

		if(empty($this->data)) {

			$this->data = $this->FeedDetail->getDefaultValue($feedConfigId);

		}else {

			if(!preg_match('/^http/is', $this->data['FeedDetail']['url']) && !preg_match('/^\//is', $this->data['FeedDetail']['url'])){
				$this->data['FeedDetail']['url'] = '/'.$this->data['FeedDetail']['url'];
			}
			$this->FeedDetail->create($this->data);

			// データを保存
			if($this->FeedDetail->save()) {
				$id = $this->FeedDetail->getLastInsertId();
				$this->Session->setFlash('フィード「'.$this->data['FeedDetail']['name'].'」を追加しました。');
				$this->FeedDetail->saveDbLog('フィード「'.$this->data['FeedDetail']['name'].'」を追加しました。');
				$this->redirect(array('controller'=>'feed_configs','action'=>'admin_edit', $feedConfigId, $id.'#headFeedDetail'));

			}else {

				$this->Session->setFlash('入力エラーです。内容を修正してください。');

			}

		}

		// 表示設定
		$this->pageTitle = '新規フィード情報登録';
		$this->render('form');

	}
/**
 * [ADMIN] 編集
 *
 * @param int $feedConfigId
 * @param int $id
 * @return	void
 * @access	public
 */
	function admin_edit($feedConfigId,$id) {

		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('controller'=>'feed_configs','action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->FeedDetail->read(null, $id);
		}else {
			if(!preg_match('/^http/is', $this->data['FeedDetail']['url']) && !preg_match('/^\//is', $this->data['FeedDetail']['url'])){
				$this->data['FeedDetail']['url'] = '/'.$this->data['FeedDetail']['url'];
			}
			$this->FeedDetail->set($this->data);
			// データを保存
			if($this->FeedDetail->save()) {
				$this->requestAction('/admin/feed/feed_configs/clear_cache', array('pass'=>array($this->data['FeedDetail']['feed_config_id'], $this->data['FeedDetail']['url'])));
				$this->Session->setFlash('フィード詳細「'.$this->data['FeedDetail']['name'].'」を更新しました。');
				$this->FeedDetail->saveDbLog('フィード詳細「'.$this->data['FeedDetail']['name'].'」を更新しました。');
				$this->redirect(array('controller'=>'feed_configs','action'=>'admin_edit', $feedConfigId, $id.'#headFeedDetail'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		// 表示設定
		$this->pageTitle = 'フィード情報編集';
		$this->render('form');

	}
/**
 * フィードのキャッシュを削除する
 *
 * @param string $feedConfigId
 * @param string $url
 * @return	void
 * @access	protected
 */
	function _clearViewCatch($feedConfigId, $url) {
		
		clearViewCache('/feed/index/'.$feedConfigId);
		clearViewCache('/feed/ajax/'.$feedConfigId);
		clearViewCache('/feed/cachetime/'.$feedConfigId);
		if(strpos($url,'http')===false) {
			// 実際のキャッシュではSSLを利用しているかどうかわからないので、両方削除する
			clearCache($this->RssEx->__createCacheHash('', 'http://'.$_SERVER['HTTP_HOST'].$this->base.$url), 'views', '.rss');
			clearCache($this->RssEx->__createCacheHash('', 'https://'.$_SERVER['HTTP_HOST'].$this->base.$url), 'views', '.rss');
		}else {
			clearCache($this->RssEx->__createCacheHash('', $url),'views','.rss');
		}
		
	}
/**
 * [ADMIN] 削除
 *
 * @param int $feedConfigId
 * @param	int $id
 * @return void
 * @access	public
 */
	function admin_delete($feedConfigId, $id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('controller'=>'feed_configs','action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$FeedDetail = $this->FeedDetail->read(null, $id);

		// 削除実行
		if($this->FeedDetail->del($id)) {
			$this->Session->setFlash($FeedDetail['FeedDetail']['name'].' を削除しました。');
			$this->FeedDetail->saveDbLog('フィード「'.$FeedDetail['FeedDetail']['name'].'」を削除しました。');
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('controller'=>'feed_configs','action'=>'admin_edit', $feedConfigId, $id.'#headFeedDetail'));

	}
	
}

?>