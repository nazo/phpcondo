<?php
/* SVN FILE: $Id$ */
/**
 * ページモデル
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
 * ページモデル
 * 
 * @package baser.models
 */
class Page extends AppModel {
/**
 * クラス名
 * @var string
 * @access public
 */
	var $name = 'Page';
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
	var $belongsTo = array(
			'PageCategory' =>   array(  'className'=>'PageCategory',
							'foreignKey'=>'page_category_id'),
			'User' => array('className'=> 'User',
							'foreignKey'=>'author_id'));
/**
 * ビヘイビア
 *
 * @var array
 * @access public
 */
	var $actsAs = array('ContentsManager', 'Cache');
/**
 * 更新前のページファイルのパス
 * 
 * @var string
 * @access public
 */
	var $oldPath = '';
/**
 * ファイル保存可否
 * true の場合、ページデータ保存の際、ページテンプレートファイルにも内容を保存する
 * テンプレート読み込み時などはfalseにして保存しないようにする
 * 
 * @var boolean
 * @access public
 */
	var $fileSave = true;
/**
 * 検索テーブルへの保存可否
 *
 * @var boolean
 * @access public
 */
	var $contentSaving = true;
/**
 * 非公開WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @deprecated
 * @access protected
 */
	var $_unpublishes = -1;
/**
 * 公開WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @access protected
 */
	var $_publishes = -1;
/**
 * WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @access protected
 */
	var $_pages = -1;
/**
 * 最終登録ID
 * モバイルページへのコピー処理でスーパークラスの最終登録IDが上書きされ、
 * コントローラーからは正常なIDが取得できないのでモバイルページへのコピー以外の場合だけ保存する
 *
 * @var int
 * @access private
 */
	var $__pageInsertID = null;
/**
 * バリデーション
 *
 * @var array
 * @access	public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'ページ名を入力してください。',
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'ページ名は50文字以内で入力してください。'),
			array(	'rule'		=> array('pageExists'),
					'message'	=> '指定したページは既に存在します。ファイル名、またはカテゴリを変更してください。')
		),
		'page_category_id' => array(
			array(	'rule'		=> array('pageExists'),
					'message'	=> '指定したページは既に存在します。ファイル名、またはカテゴリを変更してください。')
		),
		'title' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'ページタイトルは255文字以内で入力してください。')
		),
		'description' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '説明文は255文字以内で入力してください。')
		)
	);
/**
 * フォームの初期値を設定する
 * 
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue() {

		if(!empty($_SESSION['Auth']['User'])) {
			$data[$this->name]['author_id'] = $_SESSION['Auth']['User']['id'];
		}
		$data[$this->name]['sort'] = $this->getMax('sort')+1;
		$data[$this->name]['status'] = false;
		return $data;

	}
/**
 * beforeSave
 * 
 * @return boolean
 * @access public
 */
	function beforeSave() {

		if(!$this->fileSave) {
			return true;
		}

		// 保存前のページファイルのパスを取得
		if($this->exists()) {
			$this->oldPath = $this->_getPageFilePath(
					$this->find('first', array(
						'conditions' => array('Page.id' => $this->data['Page']['id']),
						'recursive' => -1)
					)
			);
		}else {
			$this->oldPath = '';
		}

		// 新しいページファイルのパスが開けるかチェックする
		$result = true;
		if(!$this->checkOpenPageFile($this->data)){
			$result = false;
		}
		
		if(isset($this->data['Page'])){
			$data = $this->data['Page'];
		} else {
			$data = $this->data;
		}
		
		if(!empty($data['reflect_mobile'])){
			$data['url'] = '/'.Configure::read('AgentSettings.mobile.prefix').$this->removeAgentPrefixFromUrl($data['url']);
			if(!$this->checkOpenPageFile($data)){
				$result = false;
			}
		}
		if(!empty($data['reflect_smartphone'])){
			$data['url'] = '/'.Configure::read('AgentSettings.smartphone.prefix').$this->removeAgentPrefixFromUrl($data['url']);
			if(!$this->checkOpenPageFile($data)){
				$result = false;
			}
		}
		return $result;

	}
/**
 * プレフィックスを取り除く
 * 
 * @param type $url
 * @return type 
 */
	function removeAgentPrefixFromUrl($url) {
		if(preg_match('/^\/'.Configure::read('AgentSettings.mobile.prefix').'\//', $url)) {
			$url = preg_replace('/^\/'.Configure::read('AgentSettings.mobile.prefix').'\//', '/', $url);
		} elseif(preg_match('/^\/'.Configure::read('AgentSettings.smartphone.prefix').'\//', $url)) {
			$url = preg_replace('/^\/'.Configure::read('AgentSettings.smartphone.prefix').'\//', '/', $url);
		}
		return $url;
	}
/**
 * 最終登録IDを取得する
 *
 * @return	int
 * @access	public
 */
	function getInsertID(){
		
		if(!$this->__pageInsertID){
			$this->__pageInsertID = parent::getInsertID();
		}
		return $this->__pageInsertID;
		
	}
/**
 * ページテンプレートファイルが開けるかチェックする
 * 
 * @param	array	$data	ページデータ
 * @return	boolean
 * @access	public
 */
	function checkOpenPageFile($data){
		
		$path = $this->_getPageFilePath($data);
		$File = new File($path);
		if($File->open('w')) {
			$File->close();
			$File = null;
			return true;
		}else {
			return false;
		}
		
	}
/**
 * afterSave
 * 
 * @param array $created
 * @return boolean
 * @access public
 */
	function afterSave($created) {

		if(isset($this->data['Page'])){
			$data = $this->data['Page'];
		} else {
			$data = $this->data;
		}
		// タイトルタグと説明文を追加
		if(empty($data['id'])) {
			$data['id'] = $this->id;
		}
		
		if($this->fileSave) {
			$this->createPageTemplate($data);
		}

		// 検索用テーブルに登録
		if($this->contentSaving) {
			if(!$data['exclude_search']) {
				$this->saveContent($this->createContent($data));
			} else {
				$this->deleteContent($data['id']);
			}
		}

		// モバイルデータの生成
		if(!empty($data['reflect_mobile'])) {
			$this->refrect('mobile', $data);
		}
		if(!empty($data['reflect_smartphone'])){
			$this->refrect('smartphone', $data);
		}
			

	}
/**
 * 関連ページに反映する
 * 
 * @param string $type
 * @param array $data
 * @return boolean
 */
	function refrect($type, $data) {
		
		if(isset($this->data['Page'])){
			$data = $this->data['Page'];
		}
		
		// モバイルページへのコピーでスーパークラスのIDを上書きしてしまうので退避させておく
		$this->__pageInsertID = parent::getInsertID();

		$agentId = $this->PageCategory->getAgentId($type);
		if(!$agentId){
			// カテゴリがない場合は trueを返して終了
			return true;
		}

		$data['url'] = '/'.Configure::read('AgentSettings.'.$type.'.prefix').$this->removeAgentPrefixFromUrl($data['url']);
		
		$agentPage = $this->find('first',array('conditions'=>array('Page.url'=>$data['url']),'recursive'=>-1));

		unset($data['id']);
		unset($data['sort']);
		unset($data['status']);

		if($agentPage){
			$agentPage['Page']['name'] = $data['name'];
			$agentPage['Page']['title'] = $data['title'];
			$agentPage['Page']['description'] = $data['description'];
			$agentPage['Page']['draft'] = $data['draft'];
			$agentPage['Page']['modified'] = $data['modified'];
			$agentPage['Page']['contents'] = $data['contents'];
			$agentPage['Page']['reflect_mobile'] = false;
			$agentPage['Page']['reflect_smartphone'] = false;
			$this->set($agentPage);
		}else{
			if($data['page_category_id']){
				$fields = array('parent_id','name','title');
				$pageCategoryTree = $this->PageCategory->getTreeList($fields,$data['page_category_id']);
				$path = getViewPath().'pages'.DS.Configure::read('AgentSettings.'.$type.'.prefix');
				$parentId = $agentId;
				foreach($pageCategoryTree as $pageCategory) {
					$path .= '/'.$pageCategory['PageCategory']['name'];
					$categoryId = $this->PageCategory->getIdByPath($path);
					if(!$categoryId){
						$pageCategory['PageCategory']['parent_id'] = $parentId;
						$this->PageCategory->create($pageCategory);
						$ret = $this->PageCategory->save();
						$parentId = $categoryId = $this->PageCategory->getInsertID();
					}else{
						$parentId = $categoryId;
					}
				}
				$data['page_category_id'] = $categoryId;
			}else{
				$data['page_category_id'] = $agentId;
			}
			$data['author_id'] = $_SESSION['Auth']['User']['id'];
			$data['sort'] = $this->getMax('sort')+1;
			$data['status'] = false;	// 新規ページの場合は非公開とする
			unset($data['publish_begin']);
			unset($data['publish_end']);
			unset($data['created']);
			unset($data['modified']);
			$data['reflect_mobile'] = false;
			$data['reflect_smartphone'] = false;
			$this->create($data);

		}
		return $this->save();

	}
/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 * @access public
 */
	function createContent($data) {

		if(isset($data['Page'])) {
			$data = $data['Page'];
		}
		if(!isset($data['publish_begin'])) {
			$data['publish_begin'] = '';
		}
		if(!isset($data['publish_end'])) {
			$data['publish_end'] = '';
		}

		if(!$data['title']) {
			$data['title'] = Inflector::camelize($data['name']);
		}
		
		// モバイル未対応
		$PageCategory = ClassRegistry::init('PageCategory');
		$excludeIds = am($PageCategory->getAgentCategoryIds('mobile'), $PageCategory->getAgentCategoryIds('smartphone'));
		if(in_array($data['page_category_id'], $excludeIds)) {
			return array();
		}

		$_data = array();
		$_data['Content']['type'] = 'ページ';
		// $this->idに値が入ってない場合もあるので
		if(!empty($data['id'])) {
			$_data['Content']['model_id'] = $data['id'];
		} else {
			$_data['Content']['model_id'] = $this->id;
		}
		$_data['Content']['category'] = '';
		if(!empty($data['page_category_id'])) {
			$categoryPath = $PageCategory->getPath($data['page_category_id'], array('title'));
			if($categoryPath) {
				$_data['Content']['category'] = $categoryPath[0]['PageCategory']['title'];
			}
		}
		$_data['Content']['title'] = $data['title'];
		$parameters = split('/', preg_replace("/^\//", '', $data['url']));
		$detail = $this->requestAction(array('controller' => 'pages', 'action' => 'display'), array('pass' => $parameters, 'return') );
		$detail = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $detail);
		$_data['Content']['detail'] = $data['description'].' '.$detail;
		$_data['Content']['url'] = $data['url'];
		$_data['Content']['status'] = $this->allowedPublish($data['status'], $data['publish_begin'], $data['publish_end']);

		return $_data;

	}
/**
 * beforeDelete
 * 
 * @return boolean
 * @access public
 */
	function beforeDelete() {
		
		return $this->deleteContent($this->id);
		
	}
/**
 * データが公開済みかどうかチェックする
 * 同様のメソッド checkPublish があり DB接続前提でURLでチェックする仕組みだが
 * こちらは、実データで直接チェックする
 * TODO メソッド名のリファクタリング要
 *
 * @param boolean $status
 * @param boolean $publishBegin
 * @param boolean $publishEnd
 * @return	array
 * @access public
 */
	function allowedPublish($status, $publishBegin, $publishEnd) {

		if(!$status) {
			return false;
		}

		if($publishBegin && $publishBegin != '0000-00-00 00:00:00') {
			if($publishBegin < date('Y-m-d H:i:s')) {
				return false;
			}
		}

		if($publishEnd && $publishEnd != '0000-00-00 00:00:00') {
			if($publishEnd > date('Y-m-d H:i:s')) {
				return false;
			}
		}

		return true;

	}
/**
 * DBデータを元にページテンプレートを全て生成する
 * 
 * @return boolean
 * @access public
 */
	function createAllPageTemplate(){
		
		$pages = $this->find('all', array('recursive' => -1));
		$result = true;
		foreach($pages as $page){
			if(!$this->createPageTemplate($page)){
				$result = false;
			}
		}
		return $result;
		
	}
/**
 * ページテンプレートを生成する
 * 
 * @param array $data ページデータ
 * @return boolean
 * @access public
 */
	function createPageTemplate($data){

		if(isset($data['Page'])){
			$data = $data['Page'];
		}
		$contents = $this->addBaserPageTag($data['id'], $data['contents'], $data['title'],$data['description']);

		// 新しいページファイルのパスを取得する
		$newPath = $this->_getPageFilePath($data);

		// ファイルに保存
		$newFile = new File($newPath);
		if($newFile->open('w')) {
			if($newFile->append($contents)) {
				// テーマやファイル名が変更された場合は元ファイルを削除する
				if($this->oldPath && ($newPath != $this->oldPath)) {
					$oldFile = new File($this->oldPath);
					$oldFile->delete();
					unset($oldFile);
				}
			}
			$newFile->close();
			unset($newFile);
			@chmod($newPath, 0666);
			return true;
		}else {
			return false;
		}

	}
/**
 * ページファイルのディレクトリを取得する
 * 
 * @param array $data
 * @return string
 * @access protected
 */
	function _getPageFilePath($data) {

		if(isset($data['Page'])){
			$data = $data['Page'];
		}

		$file = $data['name'];
		$categoryId = $data['page_category_id'];
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		if(!$SiteConfig){
			$SiteConfig = ClassRegistry::init('SiteConfig');
		}
		$SiteConfig->cacheQueries = false;
		$siteConfig = $SiteConfig->findExpanded();
		$theme = $siteConfig['theme'];

		// pagesディレクトリのパスを取得
		if($theme) {
			$path = WWW_ROOT.'themed'.DS.$theme.DS.'pages'.DS;
		}else {
			$path = VIEWS.'pages'.DS;

		}

		if(!is_dir($path)) {
			mkdir($path);
			chmod($path,0777);
		}

		if($categoryId) {
			$this->PageCategory->cacheQueries = false;
			$categoryPath = $this->PageCategory->getPath($categoryId, null, null, -1);
			if($categoryPath) {
				foreach($categoryPath as $category) {
					$path .= $category['PageCategory']['name'].DS;
					if(!is_dir($path)) {
						mkdir($path,0777);
						chmod($path,0777);
					}
				}
			}
		}
		return $path.$file.'.ctp';

	}
/**
 * ページファイルを削除する
 * 
 * @param array $data
 */
	function delFile($data) {
		
		$path = $this->_getPageFilePath($data);
		if($path) {
			return unlink($path);
		}
		return true;
		
	}
/**
 * ページのURLを取得する
 * 
 * @param array $data
 * @return string
 * @access public
 */
	function getPageUrl($data) {

		if(isset($data['Page'])) {
			$data = $data['Page'];
		}
		$categoryId = $data['page_category_id'];
		$url = '/';
		if($categoryId) {
			$this->PageCategory->cacheQueries = false;
			$categoryPath = $this->PageCategory->getPath($categoryId);
			if($categoryPath) {
				foreach($categoryPath as $key => $category) {
					if($key == 0 && $category['PageCategory']['name'] == Configure::read('AgentSettings.mobile.prefix')) {
						$url .= Configure::read('AgentSettings.mobile.prefix').'/';
					} else {
						$url .= $category['PageCategory']['name'].'/';
					}
				}
			}
		}
		return $url.$data['name'];
		
	}
/**
 * Baserが管理するタグを追加する
 * 
 * @param string $id
 * @param string $contents
 * @param string $title
 * @param string $description
 * @return string
 * @access public
 */
	function addBaserPageTag($id,$contents,$title,$description) {
		
		$tag = '<!-- BaserPageTagBegin -->'."\n";
		$tag .= '<?php $baser->setTitle(\''.$title.'\') ?>'."\n";
		$tag .= '<?php $baser->setDescription(\''.$description.'\') ?>'."\n";
		if($id) {
			$tag .= '<?php $baser->editPage('.$id.') ?>'."\n";
		}
		$tag .= '<!-- BaserPageTagEnd -->'."\n";
		return $tag . $contents;
		
	}
/**
 * ページ存在チェック
 *
 * @param string チェック対象文字列
 * @return boolean
 * @access public
 */
	function pageExists($check) {
		
		if($this->exists()) {
			return true;
		}else {
			$conditions['Page.name'] = $this->data['Page']['name'];
			if(empty($this->data['Page']['page_category_id'])) {
				if($this->data['Page']['page_type'] == 2) {
					$conditions['Page.page_category_id'] = $this->PageCategory->getAgentId('mobile');
				} elseif($this->data['Page']['page_type'] == 3) {
					$conditions['Page.page_category_id'] = $this->PageCategory->getAgentId('smartphone');
				} else {
					$conditions['Page.page_category_id'] = NULL;
				}
			}else {
				$conditions['Page.page_category_id'] = $this->data['Page']['page_category_id'];
			}
			if(!$this->find('first', array('conditions' => $conditions, 'recursive' => -1))) {
				return true;
			}else {
				return !file_exists($this->_getPageFilePath($this->data));
			}
		}
		
	}
/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $options
 * @return mixed $controlSource コントロールソース
 * @access public
 */
	function getControlSource($field, $options = array()) {

		switch ($field) {
			
			case 'page_category_id':
								
				$catOption = array();
				$isSuperAdmin = false;
				$agentRoot = true;
				
				extract($options);

				if(!empty($userGroupId)) {
					
					if(!isset($pageCategoryId)) {
						$pageCategoryId = '';
					}

					if($userGroupId == 1) {
						$isSuperAdmin = true;
					}

					// 現在のページが編集不可の場合、現在表示しているカテゴリも取得する
					if(!$pageEditable && $pageCategoryId) {
						$catOption = array('conditions' => array('OR' => array('PageCategory.id' => $pageCategoryId)));
					}

					// super admin でない場合は、管理許可のあるカテゴリのみ取得
					if(!$isSuperAdmin) {
						$catOption['ownerId'] = $userGroupId;
					}
				
					if($pageEditable && !$rootEditable && !$isSuperAdmin) {
						unset($empty);
						$agentRoot = false;
					}
				
				}
				
				$options = am($options, $catOption);
				$categories = $this->PageCategory->getControlSource('parent_id', $options);
				
				// 「指定しない」追加
				if(isset($empty)) {
					if($categories) {
						$categories = array('' => $empty) + $categories;
					} else {
						$categories = array('' => $empty);
					}
				}
				if(!$agentRoot) {
					// TODO 整理
					$agentId = $this->PageCategory->getAgentId('mobile');
					if(isset($categories[$agentId])) {
						unset($categories[$agentId]);
					}
					$agentId = $this->PageCategory->getAgentId('smartphone');
					if(isset($categories[$agentId])) {
						unset($categories[$agentId]);
					}
				}
				
				$controlSources['page_category_id'] = $categories;
				
				break;
				
			case 'user_id':
				
				$controlSources['user_id'] = $this->User->getUserList($options);
				break;
			
		}
		
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * 非公開チェックを行う
 * 
 * @param	string	$url
 * @return	boolean
 * @access	public
 * @deprecated isPageUrl と組み合わせて checkPublish を利用してください。
 */
	function checkUnPublish($url) {

		if($this->_unpublishes == -1) {
			
			$conditions['or']['Page.status'] = false;
			$conditions['or'][] = array(array('Page.publish_begin >' => date('Y-m-d H:i:s')),
												array('Page.publish_begin <>' => '0000-00-00 00:00:00'),
												array('Page.publish_begin <>' => NULL));
			$conditions['or'][] = array(array('Page.publish_end <' => date('Y-m-d H:i:s')),
												array('Page.publish_end <>' => '0000-00-00 00:00:00'),
												array('Page.publish_end <>' => NULL));
			$pages = $this->find('all',array('fields'=>'url','conditions'=>$conditions,'recursive'=>-1));
			
			if(!$pages) {
				$this->_unpublishes = array();
				return false;
			}
			
			$this->_unpublishes = Set::extract('/Page/url', $pages);
			
		}

		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/'.Configure::read('AgentPrefix.currentAlias').'\//', '/'.Configure::read('AgentPrefix.currentPrefix').'/', $url);
		
		return in_array($url,$this->_unpublishes);

	}
/**
 * キャッシュ時間を取得する
 * 
 * @param string $url
 * @return mixed int or false
 */
	function getCacheTime($url) {
		
		$url = preg_replace('/^\/'.Configure::read('AgentPrefix.currentAlias').'\//', '/'.Configure::read('AgentPrefix.currentPrefix').'/', $url);
		$page = $this->find('first', array('conditions' => array('Page.url' => $url), 'recursive' => -1));
		if(!$page) {
			return false;
		}
		if($page['Page']['status'] && $page['Page']['publish_end'] && $page['Page']['publish_end'] != '0000-00-00 00:00:00') {
			return strtotime($page['Page']['publish_end']) - time();
		} else {
			return Configure::read('Baser.cachetime');
		}
		
	}
/**
 * 公開チェックを行う
 * 
 * @param string $url
 * @return boolean
 * @access public
 */
	function checkPublish($url) {

		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/'.Configure::read('AgentPrefix.currentAlias').'\//', '/'.Configure::read('AgentPrefix.currentPrefix').'/', $url);
		
		if($this->_publishes == -1) {
			$conditions = $this->getConditionAllowPublish();
			// 毎秒抽出条件が違うのでキャッシュしない
			$pages = $this->find('all', array(
				'fields'	=> 'url',
				'conditions'=> $conditions,
				'recursive'	=> -1,
				'cache'		=> false
			));
			if(!$pages) {
				$this->_publishes = array();
				return false;
			}
			$this->_publishes = Set::extract('/Page/url', $pages);
		}
		return in_array($url,$this->_publishes);

	}
/**
 * 公開済の conditions を取得
 *
 * @return array
 * @access public
 */
	function getConditionAllowPublish() {

		$conditions[$this->alias.'.status'] = true;
		$conditions[] = array('or'=> array(array($this->alias.'.publish_begin <=' => date('Y-m-d H:i:s')),
										array($this->alias.'.publish_begin' => NULL),
										array($this->alias.'.publish_begin' => '0000-00-00 00:00:00')));
		$conditions[] = array('or'=> array(array($this->alias.'.publish_end >=' => date('Y-m-d H:i:s')),
										array($this->alias.'.publish_end' => NULL),
										array($this->alias.'.publish_end' => '0000-00-00 00:00:00')));
		return $conditions;

	}
/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param string $pagePath
 * @param string $parentCategoryId
 * @return array 処理結果 all / success
 * @access protected
 */
	function entryPageFiles($targetPath,$parentCategoryId = '') {

		$this->fileSave = false;
		$folder = new Folder($targetPath);
		$files = $folder->read(true,true,true);
		$insert = 0;
		$update = 0;
		$all = 0;

		// カテゴリの取得・登録
		$categoryName = basename($targetPath);
		
		$pageCategoryId = '';
		$this->PageCategory->updateRelatedPage = false;
		if($categoryName != 'pages') {
			
			// カテゴリ名の取得
			// 標準では設定されてないので、利用する場合は、あらかじめ bootstrap 等で宣言しておく
			$categoryTitles = Configure::read('Baser.pageCategoryTitles');
			$categoryTitle = -1;
			if($categoryTitles) {
				$categoryNames = explode('/', str_replace(getViewPath().'pages'.DS, '', $targetPath));
				foreach($categoryNames as $key => $value) {
					if(isset($categoryTitles[$value])) {
						if(count($categoryNames) == ($key + 1)) {
							$categoryTitle = $categoryTitles[$value]['title'];
						}elseif(isset($categoryTitles[$value]['children'])) {
							$categoryTitles = $categoryTitles[$value]['children'];
						}
					}
				}
			}
			
			$categoryId = $this->PageCategory->getIdByPath($targetPath);
			if($categoryId) {
				$pageCategoryId = $categoryId;
				if($categoryTitle != -1) { 
					$pageCategory = $this->PageCategory->find('first', array('conditions' => array('PageCategory.id' => $pageCategoryId), 'recursive' => -1));
					$pageCategory['PageCategory']['title'] = $categoryTitle;
					$this->PageCategory->set($pageCategory);
					$this->PageCategory->save();
				}
			}else {
				$pageCategory['PageCategory']['parent_id'] = $parentCategoryId;
				$pageCategory['PageCategory']['name'] = $categoryName;
				if($categoryTitle == -1) { 
					$pageCategory['PageCategory']['title'] = $categoryName;
				} else {
					$pageCategory['PageCategory']['title'] = $categoryTitle;
				}
				$pageCategory['PageCategory']['sort'] = $this->PageCategory->getMax('sort')+1;
				$this->PageCategory->cacheQueries = false;
				$this->PageCategory->create($pageCategory);
				if($this->PageCategory->save()) {
					$pageCategoryId = $this->PageCategory->getInsertID();
				}
			}
		}else {
			$categoryName = '';
		}

		// ファイル読み込み・ページ登録
		if(!$files[1]) $files[1] = array();
		foreach($files[1] as $file) {

			if(preg_match('/\.ctp$/is',$file) == false) {
				continue;
			}

			$pageName = basename($file, '.ctp');
			$file = new File($file);
			$contents = $file->read();
			$file->close();
			$file = null;

			// タイトル取得・置換
			$titleReg = '/<\?php\s+?\$baser->setTitle\(\'(.*?)\'\)\s+?\?>/is';
			if(preg_match($titleReg,$contents,$matches)) {
				$title = trim($matches[1]);
				$contents = preg_replace($titleReg,'',$contents);
			}else {
				$title = Inflector::camelize($pageName);
			}

			// 説明文取得・置換
			$descriptionReg = '/<\?php\s+?\$baser->setDescription\(\'(.*?)\'\)\s+?\?>/is';
			if(preg_match($descriptionReg,$contents,$matches)) {
				$description = trim($matches[1]);
				$contents = preg_replace($descriptionReg,'',$contents);
			}else {
				$description = '';
			}

			// PageTagコメントの削除
			$pageTagReg = '/<\!\-\- BaserPageTagBegin \-\->.*?<\!\-\- BaserPageTagEnd \-\->/is';
			$contents = preg_replace($pageTagReg,'',$contents);

			$conditions['Page.name'] = $pageName;
			if($pageCategoryId) {
				$conditions['Page.page_category_id'] = $pageCategoryId;
			}else{
				$conditions['Page.page_category_id'] = null;
			}
			$page = $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
			if($page) {
				$chage = false;
				if($title != $page['Page']['title']) {
					$chage = true;
				}
				if($description != $page['Page']['description']) {
					$chage = true;
				}
				if(trim($contents) != trim($page['Page']['contents'])) {
					$chage = true;
				}
				if($chage) {
					$page['Page']['title'] = $title;
					$page['Page']['description'] = $description;
					$page['Page']['contents'] = $contents;
					$this->set($page);
					if($this->save()) {
						$update++;
					}
				}
			}else {
				$page = $this->getDefaultValue();
				$page['Page']['name'] = $pageName;
				$page['Page']['title'] = $title;
				$page['Page']['description'] = $description;
				$page['Page']['contents'] = $contents;
				$page['Page']['page_category_id'] = $pageCategoryId;
				$page['Page']['url'] = $this->getPageUrl($page);
				$this->create($page);
				if($this->save()) {
					$insert++;
				}
			}
			$all++;
		}

		// フォルダー内の登録
		if(!$files[0]) $files[0] = array();
		foreach($files[0] as $file) {
			$folderName = basename($file);
			if($folderName != '_notes' && $folderName != 'admin') {
				$result = $this->entryPageFiles($file,$pageCategoryId);
				$insert += $result['insert'];
				$update += $result['update'];
				$all += $result['all'];
			}
		}

		return array('all'=>$all,'insert'=>$insert,'update'=>$update);

	}
/**
 * 関連ページの存在チェック
 * 存在する場合は、ページIDを返す
 * 
 * @param array $data ページデータ
 * @return mixed ページID / false
 * @access public
 */
	function agentExists ($type, $data) {
		
		if(isset($data['Page'])){
			$data = $data['Page'];
		}
		$url = $this->removeAgentPrefixFromUrl($data['url']);
		if(preg_match('/^\/'.Configure::read('AgentSettings.'.$type.'.prefix').'\//is', $url)){
			// 対象ページがモバイルページの場合はfalseを返す
			return false;
		}
		return $this->field('id',array('Page.url'=>'/'.Configure::read('AgentSettings.'.$type.'.prefix').$url));
		
	}
/**
 * ページで管理されているURLかチェックする
 * 
 * @param string $url
 * @return boolean
 * @access public
 */
	function isPageUrl($url) {
		
		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/'.Configure::read('AgentPrefix.currentAlias').'\//', '/'.Configure::read('AgentPrefix.currentPrefix').'/', $url);
		
		if($this->_pages == -1) {
			$pages = $this->find('all', array(
				'fields'	=> 'url',
				'recursive'	=> -1
			));
			if(!$pages) {
				$this->_pages = array();
				return false;
			}
			$this->_pages = Set::extract('/Page/url', $pages);
		}
		return in_array($url,$this->_pages);
		
	}
	
}
?>