<?php
/* SVN FILE: $Id$ */
/**
 * ウィジェットエリアコントローラー
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
 * ウィジェットエリアコントローラー
 *
 * @package baser.controllers
 */
class WidgetAreasController extends AppController {
/**
 * クラス名
 * @var string
 * @access public
 */
	var $name = 'WidgetAreas';
/**
 * コンポーネント
 * @var array
 * @access public
 */
	var $components = array('RequestHandler');
/**
 * ヘルパー
 * @var array
 * @access public
 */
	var $helpers = array('FormEx');
/**
 * モデル
 * @var array
 * @access public
 */
	var $uses = array('WidgetArea','Plugin');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form','ウィジェットエリア管理'=>'/admin/widget_areas/index');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array('site_configs','widget_areas');
	function beforeFilter() {
		parent::beforeFilter();
		$this->Security->validatePost = false;
	}
/**
 * 一覧
 * @return void
 * @access public
 */
	function admin_index () {

		$this->pageTitle = 'ウィジェットエリア一覧';
		$widgetAreas = $this->WidgetArea->find('all');
		if($widgetAreas){
			foreach($widgetAreas as $key => $widgetArea){
				$widgets = unserialize($widgetArea['WidgetArea']['widgets']);
				if(!$widgets) {
					$widgetAreas[$key]['WidgetArea']['count'] = 0;
				} else {
					$widgetAreas[$key]['WidgetArea']['count'] = count($widgets);
				}
			}
		}
		$this->set('widgetAreas',$widgetAreas);

	}
/**
 * 新規登録
 * 
 * @return void
 * @access public
 */
	function admin_add () {

		$this->pageTitle = '新規ウィジェットエリア登録';

		if($this->data){			
			$this->WidgetArea->set($this->data);
			if($this->WidgetArea->save()){
				$this->Session->setFlash('新しいウィジェットエリアを保存しました。');
				$this->redirect(array('action'=>'edit',$this->WidgetArea->getInsertID()));
			}else{
				$this->Session->setFlash('新しいウィジェットエリアの保存に失敗しました。');
			}
		}
		$this->render('form');
		
	}
/**
 * 編集
 * 
 * @return void
 * @access public
 */
	function admin_edit($id) {

		$this->pageTitle = 'ウィジェットエリア編集';

		$widgetArea = $this->WidgetArea->read(null,$id);
		if($widgetArea['WidgetArea']['widgets']){
			$widgetArea['WidgetArea']['widgets'] = $widgets = unserialize($widgetArea['WidgetArea']['widgets']);
			usort($widgetArea['WidgetArea']['widgets'], 'widgetSort');
			foreach($widgets as $widget){
				$key = key($widget);
				$widgetArea[$key] = $widget[$key];
			}
		}
		$this->data = $widgetArea;

		$widgetInfos = array(0=>array('title'=>'コアウィジェット','plugin'=>'','paths'=>array(BASER_VIEWS.'elements'.DS.'admin'.DS.'widgets')));
		if(is_dir(VIEWS.'elements'.DS.'admin'.DS.'widgets')){
			$widgetInfos[0]['paths'][] = VIEWS.'elements'.DS.'admin'.DS.'widgets';
		}

		$plugins = $this->Plugin->find('all', array('conditions'=>array('status'=>true)));

		if($plugins){
			$pluginWidgets = array();
			foreach($plugins as $plugin) {
				$appPath = APP.'plugins'.DS.$plugin['Plugin']['name'].DS.'views'.DS.'elements'.DS.'admin'.DS.'widgets';
				$baserPath = BASER_PLUGINS.$plugin['Plugin']['name'].DS.'views'.DS.'elements'.DS.'admin'.DS.'widgets';
				$pluginWidget['paths'] = array();
				if(is_dir($appPath)) {
					$pluginWidget['paths'][] = $appPath;
				}
				if(is_dir($baserPath)) {
					$pluginWidget['paths'][] = $baserPath;
				}
				if(!$pluginWidget['paths']) {
					continue;
				}else{
					$pluginWidget['title'] = $plugin['Plugin']['title'].'ウィジェット';
					$pluginWidget['plugin'] = $plugin['Plugin']['name'];
					$pluginWidgets[] = $pluginWidget;
				}
			}
			if($pluginWidgets){
				$widgetInfos = am($widgetInfos,$pluginWidgets);
			}
		}

		$this->set('widgetInfos',$widgetInfos);
		
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

		// メッセージ用にデータを取得
		$post = $this->WidgetArea->read(null, $id);

		/* 削除処理 */
		if($this->WidgetArea->del($id)) {
			$message = 'ウィジェットエリア「'.$post['WidgetArea']['name'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->WidgetArea->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}
		clearViewCache('element_widget','');
		$this->redirect(array('action'=>'index'));

	}
/**
 * [AJAX] タイトル更新
 * 
 * @return boolean
 * @access public
 */
	function admin_update_title() {

		if(!$this->data){
			$this->notFound();
		}

		$this->WidgetArea->set($this->data);
		if($this->WidgetArea->save()){
			echo true;
		}
		exit();
		
	}
/**
 * [AJAX] ウィジェット更新
 * 
 * @param int $widgetAreaId
 * @return boolean
 * @access public
 */
	function admin_update_widget($widgetAreaId) {
		
		if(!$widgetAreaId || !$this->data){
			exit();
		}

		$data = $this->data;
		if(isset($data['_Token'])) {
			unset($data['_Token']);
		}
		$dataKey = key($data);
		$widgetArea = $this->WidgetArea->read(null,$widgetAreaId);
		$update = false;
		if($widgetArea['WidgetArea']['widgets']) {
			$widgets = unserialize($widgetArea['WidgetArea']['widgets']);
			foreach($widgets as $key => $widget){
				if(isset($data[$dataKey]['id']) && isset($widget[$dataKey]['id']) && $widget[$dataKey]['id']==$data[$dataKey]['id']){
					$widgets[$key] = $data;
					$update = true;
					break;
				}
			}
		} else {
			$widgets = array();
		}
		if(!$update){
			$widgets[] = $data;
		}
		
		$widgetArea['WidgetArea']['widgets'] = serialize($widgets);
		
		$this->WidgetArea->set($widgetArea);
		if($this->WidgetArea->save()){
			echo true;
		}
		// 全てのキャッシュを削除しないと画面に反映できない。
		//clearViewCache('element_widget','');
		clearViewCache();

		exit();
		
	}
/**
 * 並び順を更新する
 * @param int $widgetAreaId
 * @return boolean
 * @access public
 */
	function admin_update_sort($widgetAreaId) {

		if(!$widgetAreaId || !$this->data){
			exit();
		}
		$ids = split(',',$this->data['WidgetArea']['sorted_ids']);
		$widgetArea = $this->WidgetArea->read(null,$widgetAreaId);
		if($widgetArea['WidgetArea']['widgets']){
			$widgets = unserialize($widgetArea['WidgetArea']['widgets']);
			foreach($widgets as $key => $widget){
				$widgetKey = key($widget);
				$widgets[$key][$widgetKey]['sort'] = array_search($widget[$widgetKey]['id'], $ids) + 1;
			}
			$widgetArea['WidgetArea']['widgets'] = serialize($widgets);
			$this->WidgetArea->set($widgetArea);
			if($this->WidgetArea->save()){
				echo true;
			}
		}else{
			echo true;
		}
		// 全てのキャッシュを削除しないと画面に反映できない。
		//clearViewCache('element_widget','');
		clearViewCache();
		exit();
		
	}
/**
 * [AJAX] ウィジェットを削除
 * 
 * @param int $widgetAreaId
 * @param int $id
 * @return void
 * @access public
 */
	function admin_del_widget($widgetAreaId, $id) {

		$widgetArea = $this->WidgetArea->read(null,$widgetAreaId);
		if(!$widgetArea['WidgetArea']['widgets']){
			exit();
		}
		$widgets = unserialize($widgetArea['WidgetArea']['widgets']);
		foreach($widgets as $key => $widget){
			$type = key($widget);
			if($id == $widget[$type]['id']){
				unset($widgets[$key]);
				break;
			}
		}
		if($widgets){
			$widgetArea['WidgetArea']['widgets'] = serialize($widgets);
		}else{
			$widgetArea['WidgetArea']['widgets'] = '';
		}
		$this->WidgetArea->set($widgetArea);
		if($this->WidgetArea->save()){
			echo true;
		}
		// 全てのキャッシュを削除しないと画面に反映できない。
		//clearViewCache('element_widget','');
		clearViewCache();
		exit();

	}
/**
 * ウィジェットを並び替えた上で取得する
 * 
 * @param int $id
 * @return array $widgets
 * @access public
 */
	function get_widgets($id){
		
		$widgetArea = $this->WidgetArea->read(null,$id);
		if($widgetArea['WidgetArea']['widgets']){
			$widgets = unserialize($widgetArea['WidgetArea']['widgets']);
			usort($widgets, 'widgetSort');
			return $widgets;
		}
	}
	
}
/**
 * ウィジェットの並べ替えを行う
 * usortのコールバックメソッド
 * 
 * @param array $a
 * @param array $b
 * @return int
 */
function widgetSort($a, $b){
	
	$aKey = key($a);
	$bKey = key($b);
	if($a[$aKey]['sort'] == $b[$bKey]['sort']){
		return 0;
	}
	if($a[$aKey]['sort'] < $b[$bKey]['sort']){
		return -1;
	}else{
		return 1;
	}
	
}
?>