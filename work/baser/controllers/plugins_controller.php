<?php
/* SVN FILE: $Id$ */
/**
 * Plugin 拡張クラス
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Plugin 拡張クラス
 * プラグインのコントローラーより継承して利用する
 *
 * @package baser.controllers
 */
class PluginsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Plugins';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('GlobalMenu','Plugin','PluginContent');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	var $helpers = array('Time','FormEx');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form',
			'プラグイン管理'=>'/admin/plugins/index');
/**
 * コンテンツID
 *
 * @var int
 */
	var $contentId = null;
/**
 * beforeFilter
 *
 * @return void
 * @access private
 */
	function beforeFilter() {

		parent::beforeFilter();

		if(!isset($this->Plugin)) {
			$this->cakeError('missingClass', array(array('className' => 'Plugin',
							'notice'=>'プラグインでは、コントローラーで、Pluginモデルを読み込んでおく必要があります。usesプロパティを確認してください。')));
		}

		// 有効でないプラグインを実行させない
		if($this->name != 'Plugins' && !$this->Plugin->find('all',array('conditions'=>array('name'=>$this->params['plugin'], 'status'=>true)))) {
			$this->notFound();
		}

		$this->contentId = $this->getContentId();
		
	}
/**
 * コンテンツIDを取得する
 * 一つのプラグインで複数のコンテンツを実装する際に利用する。
 *
 * @return int $pluginNo
 * @access public
 */
	function getContentId() {

		// 管理画面の場合には取得しない
		if(!empty($this->params['admin'])){
			return null;
		}

		if(!isset($this->PluginContent)) {
			return null;
		}

		if(!isset($this->params['url']['url'])) {
			return null;
		}
		$contentName = '';
		$url = preg_replace('/^\//', '', $this->params['url']['url']);
		$url = split('/', $url);
		if($url[0]!=Configure::read('AgentPrefix.currentAlias')) {
			if(!empty($this->params['prefix']) && $url[0] == $this->params['prefix']) {
				if(isset($url[1])) {
					$contentName = $url[1];
				}
			}else {
				$contentName = $url[0];
			}
		}else {
			if(!empty($this->params['prefix']) && $url[0] == $this->params['prefix']) {
				$contentName = $url[2];
			}else {
				$contentName = $url[1];
			}
		}

		// プラグインと同じ名前のコンテンツ名の場合に正常に動作しないので
		// とりあえずコメントアウト
		/*if( Inflector::camelize($url) == $this->name){
			return null;
		}*/
		$pluginContent = $this->PluginContent->findByName($contentName);
		if($pluginContent) {
			return $pluginContent['PluginContent']['content_id'];
		}else {
			return null;
		}

	}
/**
 * プラグインの一覧を表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		$listDatas = $this->Plugin->find('all');
		if(!$listDatas) {
			$listDatas = array();
		}
		// プラグインフォルダーのチェックを行う。
		// データベースに登録されていないプラグインをリストアップ
		$pluginFolder = new Folder(APP.'plugins'.DS);
		$plugins = $pluginFolder->read(true,true);
		$unRegistereds = array();
		$registereds = array();
		foreach($plugins[0] as $plugin) {
			$exists = false;
			$pluginData = array();
			foreach($listDatas as $data) {
				if($plugin == $data['Plugin']['name']) {
					$pluginData = $data;
					$exists = true;
					break;
				}
			}
			// プラグインのバージョンを取得
			$version = $this->getBaserVersion($plugin);

			// 設定ファイル読み込み
			$title = $description = $author = $url = $adminLink = '';
			$appConfigPath = APP.DS.'plugins'.DS.$plugin.DS.'config'.DS.'config.php';
			$baserConfigPath = BASER_PLUGINS.$plugin.DS.'config'.DS.'config.php';
			if(file_exists($appConfigPath)) {
				include $appConfigPath;
			}elseif(file_exists($baserConfigPath)) {
				include $baserConfigPath;
			}

			if(isset($title))
				$pluginData['Plugin']['title'] = $title;
			if(isset($description))
				$pluginData['Plugin']['description'] = $description;
			if(isset($author))
				$pluginData['Plugin']['author'] = $author;
			if(isset($url))
				$pluginData['Plugin']['url'] = $url;

			$pluginData['Plugin']['update'] = false;
			$pluginData['Plugin']['old_version'] = false;
			if($exists) {
				if(isset($adminLink))
					$pluginData['Plugin']['admin_link'] = $adminLink;
				// バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
				if(!$pluginData['Plugin']['version'] && preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version, $matches)) {
					$pluginData['Plugin']['version'] = $matches[1];
					$pluginData['Plugin']['old_version'] = true;
				}elseif(verpoint ($pluginData['Plugin']['version']) < verpoint($version) && !in_array($pluginData['Plugin']['name'],array('blog', 'feed', 'mail'))) {
					$pluginData['Plugin']['update'] = true;
				}
				$registereds[] = $pluginData;
			} else {
				// バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
				if(preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version,$matches)) {
					$version = $matches[1];
					$pluginData['Plugin']['old_version'] = true;
				}
				$pluginData['Plugin']['id'] = '';
				$pluginData['Plugin']['name'] = $plugin;
				$pluginData['Plugin']['created'] = '';
				$pluginData['Plugin']['version'] = $version;
				$pluginData['Plugin']['status'] = false;
				$pluginData['Plugin']['modified'] = '';
				$pluginData['Plugin']['admin_link'] = '';
				$unRegistereds[] = $pluginData;
			}
		}
		$listDatas = array_merge($registereds,$unRegistereds);

		// 表示設定
		$this->set('listDatas',$listDatas);
		$this->subMenuElements = array('site_configs', 'plugins');
		$this->pageTitle = 'プラグイン一覧';

	}
/**
 * [ADMIN] ファイル削除
 *
 * @param string プライグイン名
 * @return void
 * @access public
 */
	function admin_delete_file($pluginName) {
		
		$this->__deletePluginFile($pluginName);
		$message = 'プラグイン「'.$pluginName.'」 を完全に削除しました。';
		$this->Session->setFlash($message);
		$this->redirect(array('action'=>'index'));
		
	}
/**
 * プラグインファイルを削除する
 *
 * @param string $pluginName
 * @return void
 * @access private
 */
	function __deletePluginFile($pluginName) {

		$appPath = APP.'plugins'.DS.$pluginName.DS.'config'.DS.'sql'.DS;
		$baserPath = BASER_PLUGINS.$pluginName.DS.'config'.DS.'sql'.DS;
		$tmpPath = TMP.'schemas'.DS.'uninstall'.DS;
		$folder = new Folder();
		$folder->delete($tmpPath);
		$folder->create($tmpPath);

		if(is_dir($appPath)) {
			$path = $appPath;
		}else {
			$path = $baserPath;
		}

		// インストール用スキーマをdropスキーマとして一時フォルダに移動
		$folder = new Folder($path);
		$files = $folder->read(true, true);
		if(is_array($files[1])) {
			foreach($files[1] as $file) {
				if(preg_match('/\.php$/', $file)) {
					$from = $path.DS.$file;
					$to = $tmpPath.'drop_'.$file;
					copy($from, $to);
					chmod($to, 0666);
				}
			}
		}

		// テーブルを削除
		$this->Plugin->loadSchema('plugin', $tmpPath);

		// プラグインフォルダを削除
		$folder->delete(APP.'plugins'.DS.$pluginName);

		// 一時フォルダを削除
		$folder->delete($tmpPath);
		
	}
/**
 * [ADMIN] 登録処理
 *
 * @param string 	$name
 * @return  void
 * @access  public
 */
	function admin_add($name) {

		if(!$this->data) {
			if(file_exists(APP.'plugins'.DS.$name.DS.'config'.DS.'config.php')) {
				include APP.'plugins'.DS.$name.DS.'config'.DS.'config.php';
			}elseif(file_exists(BASER_PLUGINS.$name.DS.'config'.DS.'config.php')) {
				include BASER_PLUGINS.$name.DS.'config'.DS.'config.php';
			}
			$this->data['Plugin']['name']=$name;
			if(isset($title)) {
				$this->data['Plugin']['title'] = $title;
			} else {
				$this->data['Plugin']['title'] = $name;
			}
			$this->data['Plugin']['status'] = true;
			$this->data['Plugin']['version'] = $this->getBaserVersion($name);

			if(!empty($installMessage)) {
				$this->Session->setFlash($installMessage);
			}

		}else {

			$data = $this->Plugin->find('first',array('conditions'=>array('name'=>$this->data['Plugin']['name'])));

			if($data) {
				// 既にインストールデータが存在する場合は、DBのバージョンは変更しない
				$data['Plugin']['name']=$this->data['Plugin']['name'];
				$data['Plugin']['title']=$this->data['Plugin']['title'];
				$data['Plugin']['status']=$this->data['Plugin']['status'];
				$this->Plugin->set($data);
			} else {
				if(file_exists(APP.'plugins'.DS.$name.DS.'config'.DS.'init.php')) {
					include APP.'plugins'.DS.$name.DS.'config'.DS.'init.php';
				}elseif(file_exists(BASER_PLUGINS.$name.DS.'config'.DS.'init.php')) {
					include BASER_PLUGINS.$name.DS.'config'.DS.'init.php';
				}
				$data = $this->data;
				$this->Plugin->create($data);
			}

			// データを保存
			if($this->Plugin->save()) {

				Cache::clear(false,'_cake_model_');
				Cache::clear(false,'_cake_core_');
				$message = '新規プラグイン「'.$data['Plugin']['name'].'」を BaserCMS に登録しました。';
				$this->Session->setFlash($message);
				$this->Plugin->saveDbLog($message);
				$this->redirect(array('action'=>'index'));

			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('site_configs', 'plugins');
		$this->pageTitle = '新規プラグイン登録';
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int ID
 * @return void
 * @access public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}


		$data = $this->Plugin->read(null, $id);
		$data['Plugin']['status'] = false;

		/* 削除処理 */
		if($this->Plugin->save($data)) {
			$message = 'プラグイン「'.$data['Plugin']['title'].'」 を 無効化しました。';
			$this->Session->setFlash($message);
			$this->Plugin->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}

}
?>