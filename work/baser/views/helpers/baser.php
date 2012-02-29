<?php
/* SVN FILE: $Id$ */
/**
 * Baserヘルパー
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
 * @package			cake
 * @subpackage		baser.app.view.helpers
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
 * Baserヘルパー
 *
 * @package cake
 * @subpackage baser.app.views.helpers
 */
class BaserHelper extends AppHelper {
	var $_view = null;
	var $siteConfig = array();
	var $helpers = array('HtmlEx','Javascript','Session','XmlEx');
	var $_content = null;			// コンテンツ
	var $_categoryTitleOn = true;
	var $_categoryTitle = true;
	var $Page = null;
	var $Permission = null;
	var $pluginBasers = array();
/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	function __construct() {

		$this->_view =& ClassRegistry::getObject('view');

		if(isInstalled()){

			if (ClassRegistry::isKeySet('Permission')) {
				$this->Permission = ClassRegistry::getObject('Permission');
			}else {
				$this->Permission = ClassRegistry::init('Permission');
			}

			if (ClassRegistry::isKeySet('Page')) {
				$this->Page = ClassRegistry::getObject('Page');
			}else {
				$this->Page = ClassRegistry::init('Page');
			}

			if (ClassRegistry::isKeySet('PageCategory')) {
				$this->PageCategory = ClassRegistry::getObject('PageCategory');
			}else {
				$this->PageCategory = ClassRegistry::init('PageCategory');
			}

			if(isset($this->_view->viewVars['siteConfig'])) {
				$this->siteConfig = $this->_view->viewVars['siteConfig'];
			}

			// プラグインのBaserヘルパを初期化
			$this->_initPluginBasers();

		}

	}
/**
 * afterRender
 * 
 * @return void
 * @access public
 */
	function afterRender() {
		
		parent::afterRender();
		// コンテンツをフックする
		$this->_content = ob_get_contents();
		
	}
/**
 * グローバルメニューを取得する
 * 
 * @param string $menuType
 * @return array $globalMenus
 * @access public
 */
	function getGlobalMenus ($menuType = null) {

		if(!$menuType) {
			$menuType = 'default';
		}
		if (ClassRegistry::init('GlobalMenu')) {
			if(!file_exists(CONFIGS.'database.php')) {
				return '';
			}
			$dbConfig = new DATABASE_CONFIG();
			if(!$dbConfig->baser) {
				return '';
			}
			$GlobalMenu = ClassRegistry::getObject('GlobalMenu');
			// エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
			$db =& ConnectionManager::getDataSource('baser');
			if ($db->isInterfaceSupported('listSources')) {
				$sources = $db->listSources();
				if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'global_menus'), array_map('strtolower', $sources))) {
					if (empty($this->params['prefix'])) {
						$prefix = 'publish';
					} else {
						$prefix = $this->params['prefix'];
					}
					return $GlobalMenu->find('all',array('conditions'=>array('menu_type'=>$menuType),'order'=>'sort'));
				}
			}
		}
		return '';
		
	}
/**
 * タイトルをセットする
 * 
 * @param string $title
 * @access public
 */
	function setTitle($title,$categoryTitleOn = null) {
		
		if(!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}
		$this->_view->set('title',$title);
		
	}
/**
 * キーワードをセットする
 * 
 * @param string $title
 * @access public
 */
	function setKeywords($keywords) {
		
		$this->_view->set('keywords',$keywords);
		
	}
/**
 * 説明文をセットする
 * 
 * @param string $title
 * @access public
 */
	function setDescription($description) {
		
		$this->_view->set('description',$description);
		
	}
/**
 * レイアウト用の変数をセットする
 * $view->set のラッパー
 * 
 * @param string $title
 * @param mixed $value
 * @return void
 * @access public
 */
	function set($key,$value) {
		
		$this->_view->set($key,$value);
		
	}
/**
 * タイトルへのカテゴリタイトル表示を設定
 * コンテンツごとの個別設定
 * 
 * @param mixed $on boolean / 文字列（カテゴリ名として出力した文字を指定する）
 * @return void
 * @access public
 */
	function setCategoryTitle($on = true) {
		
		$this->_categoryTitle = $on;
		
	}
/**
 * キーワードを取得する
 * 
 * @return string $keyword
 * @return string
 * @access public
 */
	function getKeywords() {
		
		$keywords = '';
		if(!empty($this->_view->viewVars['keywords'])) {
			$keywords = $this->_view->viewVars['keywords'];
		}elseif(!empty($this->siteConfig['keyword'])) {
			$keywords = $this->siteConfig['keyword'];
		}
		return $keywords;
		
	}
/**
 * 説明文を取得する
 * 
 * @return string $description
 * @return string
 * @access public
 */
	function getDescription() {
		
		$description = '';
		if(!empty($this->_view->viewVars['description'])) {
			$description = $this->_view->viewVars['description'];
		}elseif(!empty($this->siteConfig['description'])) {
			$description = $this->siteConfig['description'];
		}
		return $description;
		
	}
/**
 * タイトルを取得する
 * 
 * @param string $separator
 * @param string $categoryTitleOn
 * @return string $description
 * @access public
 */
	function getTitle($separator='｜',$categoryTitleOn = null) {

		// ページコントローラーでタイトルが指定されてない場合はページタイトルを出力しない
		if(strpos($this->_view->pageTitle,'.html') !== false) {
			$title = '';
		}else {
			$title = $this->_view->pageTitle;
		}

		$navis = $this->getNavis($categoryTitleOn);
		if($navis){
			$navis = array_reverse($navis,true);
			foreach ($navis as $key => $value) {
				if($title){
					$title .= $separator;
				}
				$title .= $key;
			}
		}

		// サイトタイトルを追加
		if ($title && !empty($this->siteConfig['name'])) {
			$title .= $separator;
		}
		if (!empty($this->siteConfig['name'])) {
			$title .= $this->siteConfig['name'];
		}

		return $title;

	}
/**
 * ナビゲーション配列を取得する
 *
 * @param mixid $categoryTitleOn
 * @return array
 * @access public
 */
	function getNavis($categoryTitleOn = null){

		// ページカテゴリを追加
		if(!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}

		$navis = array();
		if($this->_categoryTitleOn && $this->_categoryTitle) {
			if($this->_categoryTitle === true) {
				if($this->_view->viewVars['navis']){
					$navis = $this->_view->viewVars['navis'];
				}
			}else {
				if(is_array($this->_categoryTitle)){
					$navis = $this->_categoryTitle;
				}else{
					$navis = array($this->_categoryTitle=>'');
				}
			}
		}

		return $navis;

	}
/**
 * コンテンツタイトルを取得する
 * @return string $description
 * @access public
 */
	function getContentsTitle() {

		$contentsTitle = '';
		// トップページの場合は、タイトルをサイト名だけにする
		if (!empty($this->_view->viewVars['contentsTitle'])) {
			$contentsTitle = $this->_view->viewVars['contentsTitle'];
		}elseif($this->params['url']['url'] == '/' || $this->params['url']['url'] == '/pages/index.html') {
			if(!empty($this->siteConfig['name'])) {
				$contentsTitle = $this->siteConfig['name'];
			}
		}elseif($this->_view->pageTitle) {
			$contentsTitle = $this->_view->pageTitle;
		}

		if ($this->_view->name != 'CakeError' && !empty($contentsTitle)) {
			return $contentsTitle;
		}

	}
/**
 * コンテンツタイトルを出力する
 * 
 * @return void
 * @access public
 */
	function contentsTitle() {
		
		echo $this->getContentsTitle();
		
	}
/**
 * タイトルを出力する
 * 
 * @param string $separator
 * @param string $categoryTitleOn
 * @return void
 * @access public
 */
	function title($separator='｜',$categoryTitleOn = null) {
		
		echo '<title>'.strip_tags($this->getTitle($separator,$categoryTitleOn)).'</title>';
		
	}
/**
 * メタキーワードタグを出力する
 * 
 * @return void
 * @access public
 */
	function metaKeywords() {
		
		echo $this->HtmlEx->meta('keywords',$this->getkeywords());
		
	}
/**
 * メタディスクリプションを出力する
 * 
 * @return void
 * @access public
 */
	function metaDescription() {
		
		echo $this->HtmlEx->meta('description', strip_tags($this->getDescription()));
		
	}
/**
 * RSSリンクタグを出力する
 * 
 * @param string $title
 * @param string $link
 * @return void
 * @access public
 */
	function rss($title, $link) {
		
		echo $this->HtmlEx->meta($title, $link, array('type' => 'rss'));
		
	}
/**
 * トップページかどうか判断する
 * 
 * @return boolean
 * @access public
 */
	function isTop() {
		
		return ($this->params['url']['url'] == '/' ||
						$this->params['url']['url'] == 'index' ||
						$this->params['url']['url'] == Configure::read('AgentPrefix.currentAlias').'/' ||
						$this->params['url']['url'] == Configure::read('AgentPrefix.currentAlias').'/index');
		
	}
/**
 * webrootを出力する為だけのラッパー
 * 
 * @return void
 * @access public
 */
	function root() {
		
		echo $this->getRoot();
		
	}
/**
 * webrootを取得する為だけのラッパー
 * 
 * @return string
 * @access public
 */
	function getRoot() {
		
		return $this->base.'/';
		
	}
/**
 * ベースを考慮したURLを出力
 * 
 * @param string $url
 * @param boolean $full
 * @return void
 * @access public
 */
	function url($url,$full = false) {
		
		echo $this->getUrl($url,$full);
		
	}
/**
 * ベースを考慮したURLを取得
 * 
 * @param string $url
 * @param boolean $full
 */
	function getUrl($url,$full = false) {
		
		return parent::url($url,$full);
		
	}
/**
 * エレメントを取得する
 * View::elementを取得するだけのラッパー
 * 
 * @param string $name
 * @param array $params
 * @param boolean $loadHelpers
 * @param boolean $subDir
 * @return string
 * @access public
 */
	function getElement($name, $params = array(), $loadHelpers = false, $subDir = true) {

		if(!empty($this->_view->subDir) && $subDir) {
			$name = $this->_view->subDir.DS.$name;
			$params['subDir'] = true;
		} else {
			$params['subDir'] = false;
		}
		return $this->_view->element($name, $params, $loadHelpers);

	}
/**
 * エレメントを出力する
 * View::elementを出力するだけのラッパー
 * 
 * @param string $name
 * @param array $params
 * @param boolean $loadHelpers
 * @return void
 * @access public
 */
	function element($name, $params = array(), $loadHelpers = false, $subDir = true) {
		
		echo $this->getElement($name, $params, $loadHelpers, $subDir);
		
	}
/**
 * ヘッダーを出力する
 * 
 * @param array $params
 * @param mixed $loadHelpers
 * @param boolean $subDir
 */
	function header($params = array(), $loadHelpers = false, $subDir = true) {
		
		$out = $this->getElement('header', $params, $loadHelpers, $subDir);
		echo $this->executeHook('baserHeader', $out);
		
	}
/**
 * フッターを出力する
 *
 * @param array $params
 * @param mixed $loadHelpers
 * @param boolean $subDir
 * @return void
 * @access public
 */
	function footer($params = array(), $loadHelpers = false, $subDir = true) {
		
		$out = $this->getElement('footer', $params, $loadHelpers, $subDir);
		echo $this->executeHook('baserFooter', $out);
		
	}
/**
 * ページネーションを出力する
 * 
 * @param string $name
 * @param array $params
 * @param boolean $loadHelpers
 * @return void
 * @access public
 */
	function pagination($name = 'default', $params = array(), $loadHelpers = false, $subDir = true) {
		
		if(!$name) {
			$name = 'default';
		}
		$file = 'paginations'.DS.$name;
		echo $this->getElement($file,$params,$loadHelpers, $subDir);
		
	}
/**
 * コンテンツを出力する
 * $content_for_layout を出力するだけのラッパー
 * 
 * @return void
 * @access public
 */
	function content() {
		
		echo $this->_content;
		
	}
/**
 * セッションメッセージをフラッシュするだけのラッパー
 * 
 * @param array $key
 * @return void
 * @access public
 */
	function flash($key='flash') {
		
		if ($this->Session->check('Message.'.$key)) {
			$this->Session->flash($key);
		}
		
	}
/**
 * スクリプトを出力する
 * $scripts_for_layout を出力するだけのラッパー
 * 
 * @return void
 * @access public
 */
	function scripts() {
		
		echo join("\n\t", $this->_view->__scripts);
		
	}
/**
 * サブメニューをセットする
 * 
 * @param array $submenus
 * @return void
 * @access public
 */
	function setSubMenus($submenus) {
		
		$this->_view->set('subMenuElements',$submenus);
		
	}
/**
 * XMLヘッダを出力する
 * 
 * @param array $attrib
 * @return void
 * @access public 
 */
	function xmlHeader($attrib = array()) {
		
		if(empty($attrib['encoding']) && Configure::read('AgentPrefix.currentAgent') == 'mobile'){
			$attrib['encoding'] = 'Shift-JIS';
		}
		echo $this->XmlEx->header($attrib)."\n";
		
	}
/**
 * アイコンタグを出力するだけのラッパー
 * 
 * @return void
 * @access public
 */
	function icon() {
		
		echo  $this->HtmlEx->meta('icon');
		
	}
/**
 * DOC TYPE を出力するだけのラッパー
 * 
 * @param type $type
 * @return void
 * @access public
 */
	function docType($type = 'xhtml-trans') {
		
		echo $this->HtmlEx->docType($type)."\n";
		
	}
/**
 * 
 * CSSタグを出力するだけのラッパー
 * 
 * @param string $path
 * @param string $rel
 * @param array $htmlAttributes
 * @param boolean $inline
 * @return void
 * @access public 
 */
	function css($path, $rel = null, $htmlAttributes = array(), $inline = true) {
		
		$ret = $this->HtmlEx->css($path, $rel, $htmlAttributes, $inline);
		if($inline) {
			echo $ret;
		}
		
	}
/**
 * Javascriptのlinkタグを出力するだけのラッパー
 * 
 * @param boolean $url
 * @param boolean $inline 
 * @return void
 * @access public
 */
	function js($url, $inline = true) {
		
		$ret = $this->Javascript->link($url, $inline);
		if($inline) {
			echo $ret;
		}
		
	}
/**
 * imageタグを出力するだけのラッパー
 * 
 * @param array $path
 * @param array $options 
 * @return void
 * @access pub
 */
	function img($path, $options = array()) {
		
		echo $this->getImg($path, $options);
		
	}
/**
 * imageタグを取得するだけのラッパー
 * 
 * @param array $path
 * @param array $options
 * @return array
 * @access public
 */
	function getImg($path, $options = array()) {
		
		return $this->HtmlEx->image($path, $options);
		
	}
/**
 * aタグを表示するだけのラッパー関数
 * 
 * @param string $title
 * @param string $url
 * @param array $htmlAttributes
 * @param boolean $confirmMessage
 * @param boolean $escapeTitle 
 * @return void
 * @access public
 */
	function link($title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = false) {
		
		echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
		
	}
/**
 *
 */
/**
 * aタグを取得するだけのラッパー
 * 
 * @param string $title
 * @param string $url
 * @param array $htmlAttributes
 * @param boolean $confirmMessage
 * @param boolean $escapeTitle
 * @return string
 * @access public
 */
	function getLink($title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = false) {
		
		$htmlAttributes = $this->executeHook('beforeBaserGetLink', $title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);

		if(!empty($htmlAttributes['prefix'])) {
			if(!empty($this->params['prefix'])) {
				$url[$this->params['prefix']] = true;
			}
			unset($htmlAttributes['prefix']);
		}
		if(isset($htmlAttributes['forceTitle'])) {
			$forceTitle = $htmlAttributes['forceTitle'];
			unset($htmlAttributes['forceTitle']);
		}else {
			$forceTitle = false;
		}

		if(isset($htmlAttributes['ssl'])) {
			$ssl = true;
			unset($htmlAttributes['ssl']);
		}else {
			$ssl = false;
		}
		$url = $this->getUrl($url);
		$_url = str_replace($this->base, '', $url);
		$enabled = true;

		// 認証チェック
		if(isset($this->Permission) && !empty($this->_view->viewVars['user']['user_group_id'])) {
			$userGroupId = $this->_view->viewVars['user']['user_group_id'];
			if(!$this->Permission->check($_url,$userGroupId)) {
				$enabled = false;
			}
		}

		// ページ公開チェック
		if(empty($this->params['admin'])) {
			if(isset($this->Page) && !preg_match('/^\/admin/', $_url)) {
				if($this->Page->isPageUrl($_url) && !$this->Page->checkPublish($_url)) {
					$enabled = false;
				}
			}
		}

		if(!$enabled) {
			if($forceTitle) {
				return "<span>$title</span>";
			}else {
				return '';
			}
		}

		// 現在SSLのURLの場合、フルパスで取得
		if($this->isSSL() && $ssl) {
			$_url = preg_replace("/^\//", "", $_url);
			if(preg_match('/^admin\//', $_url)) {
				$admin = true;
			} else {
				$admin = false;
			}
			if(Configure::read('App.baseUrl')) {
				$_url = 'index.php/'.$_url;
			}
			if(!$ssl && !$admin) {
				$url = Configure::read('Baser.siteUrl').$_url;
			} else {
				$url = Configure::read('Baser.sslUrl').$_url;
			}
		} else {
			$url = $_url;
		}

		$out = $this->HtmlEx->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);

		return $this->executeHook('afterBaserGetLink', $url, $out);
		
	}
/**
 * 現在がSSL通信か確認する
 *
 * @return boolean
 * @access public
 */
	function isSSL() {
		
		if(!empty($this->_view->viewVars['isSSL'])){
			return true;
		} else {
			return false;
		}
		
	}
/**
 * charsetを出力するだけのラッパー
 * 
 * @param string $charset
 * @return void
 * @access public
 */
	function charset($charset = null) {
		
		if(!$charset && Configure::read('AgentPrefix.currentAgent') == 'mobile'){
			$charset = 'Shift-JIS';
		}
		echo $this->HtmlEx->charset($charset);
		
	}
/**
 * コピーライト用の年を出力する
 * 
 * @param string 開始年
 * @return void
 * @access public
 */
	function copyYear($begin) {
		
		$year = date('Y');
		if($begin == $year) {
			echo $year;
		}else {
			echo $begin.' - '.$year;
		}
		
	}
/**
 * ページ編集へのリンクを出力する
 * 
 * @param string $id
 * @return void
 * @access public
 */
	function editPage($id) {
		
		if(empty($this->params['admin']) && !empty($this->_view->viewVars['user']) && !Configure::read('AgentPrefix.on')) {
			echo '<div class="edit-link">'.$this->getLink('≫ 編集する',array('admin'=>true,'controller'=>'pages','action'=>'edit',$id),array('target'=>'_blank')).'</div>';
		}
		
	}
/**
 * アップデート処理が必要かチェックする
 * TODO 別のヘルパに移動する
 * @return boolean
 * @access public
 */
	function checkUpdate() {
		
		$baserVerpoint = verpoint($this->_view->viewVars['baserVersion']);
		if(isset($this->siteConfig['version'])) {
			$siteVerpoint = verpoint($this->siteConfig['version']);
		}else {
			$siteVerpoint = 0;
		}
		return ($baserVerpoint > $siteVerpoint);
		
	}
/**
 * アップデート用のメッセージを出力する
 * TODO 別のヘルパーに移動する
 * @return void
 * @access public
 */
	function updateMessage() {
		
		if($this->checkUpdate() && $this->params['controller'] != 'updaters') {
			$updateLink = $this->HtmlEx->link('ここ','/admin/updaters');
			echo '<div id="UpdateMessage">WEBサイトのアップデートが完了していません。'.$updateLink.' からアップデートを完了させてください。</div>';
		}
		
	}
/**
 * コンテンツ名を出力する
 * 
 * @param boolean $detail 
 * @return void
 * @access public
 */
	function contentsName($detail = false) {
		
		echo $this->getContentsName($detail);
		
	}
/**
 * コンテンツ名を取得する
 * ・キャメルケースで取得
 * ・URLのコントローラー名までを取得
 * ・ページの場合は、カテゴリ名（カテゴリがない場合は Default）
 * 
 * @param boolean $detail
 * @return string
 * @access public
 */
	function getContentsName($detail = false) {

		$prefix = '';
		$plugin = '';
		$controller = '';
		$action = '';
		$pass = '';
		$url0 = '';
		$url1 = '';
		$url2 = '';

		if(!empty($this->params['prefix']) && Configure::read('AgentPrefix.currentPrefix') != $this->params['prefix']) {
			$prefix = h($this->params['prefix']);
		}
		if(!empty($this->params['plugin'])) {
			$plugin = h($this->params['plugin']);
		}
		$controller = h($this->params['controller']);
		if($prefix) {
			$action = str_replace($prefix.'_', '', h($this->params['action']));
		}else {
			$action = h($this->params['action']);
		}
		if(!empty($this->params['pass'])) {
			foreach($this->params['pass'] as $key => $value) {
				$pass[$key] = h($value);
			}
		}

		$url = split('/', h($this->params['url']['url']));
		
		if(Configure::read('AgentPrefix.on')) {
			array_shift($url);
		}

		if(isset($url[0])) {
			$url0 = $url[0];
		}
		if(isset($url[1])) {
			$url1 = $url[1];
		}
		if(isset($url[2])) {
			$url2 = $url[2];
		}

		// ページ機能の場合
		if($controller=='pages' && $action=='display') {
			
			if(strpos($pass[0], 'pages/') !== false) {
				$pageUrl = str_replace('pages/','', $pass[0]);
			} else {
				$pageUrl = h($this->params['url']['url']);
			}
			
			$pageUrl = preg_replace('/\.html$/', '', $pageUrl);
			
			if(preg_match('/^[^\/]/', $pageUrl)) {
				$pageUrl = '/'.$pageUrl;
			}
			if(preg_match('/\/$/', $pageUrl)) {
				$pageUrl .= 'index';
			}
			
			if(!$detail) {
				$aryPageUrl = split('/',$pageUrl);
				$controller = $aryPageUrl[0];
			} else {
				return Inflector::camelize(str_replace('/', '_', $pageUrl));
			}
			
		}

		// プラグインルーティングの場合
		if((($url1==''&&$action=='index')||($url1==$action)) && $url2!=$action && $plugin) {
			$plugin = '';
			$controller = $url0;
		}
// NewsArchives2
		if($prefix)	$prefix .= '_';
		if($plugin) $plugin .= '_';
		if($controller) $controller .= '_';
		if($action) $action .= '_';

		$contentsName = $prefix.$plugin.$controller;

		if($detail) {
			$contentsName .= $action;
			if($pass) {
				$contentsName .= '_'.implode('_', $pass);
			}
		}

		$contentsName = Inflector::camelize($contentsName);

		if(!$contentsName) {
			$contentsName = 'Default';
		}
		
		return $contentsName;

	}
/**
 * パンくずリストを出力する
 * アクセス制限がかかっているリンクはテキストのみ表示する
 *
 * @param string $separator Text to separate crumbs.
 * @param string $startText This will be the first crumb, if false it defaults to first crumb in array
 * @return string
 * @access public
 */
	function crumbs($separator = '&raquo;', $startText = false) {
		
		if (!empty($this->HtmlEx->_crumbs)) {
			$out = array();
			if ($startText) {
				$out[] = $this->getLink($startText, '/');
			}

			foreach ($this->HtmlEx->_crumbs as $crumb) {
				if (!empty($crumb[1])) {
					$out[] = $this->getLink($crumb[0], $crumb[1], $crumb[2]);
				} else {
					$out[] = $crumb[0];
				}
			}
			echo $this->output(implode($separator, $out));
		}
		
	}
/**
 * パンくずリストに要素を追加する
 * アクセス制限がかかっているリンクの場合でもタイトルを表示できるオプションを付加
 * $options に forceTitle を指定する事で表示しない設定も可能
 *
 * @param string $name Text for link
 * @param string $link URL for link (if empty it won't be a link)
 * @param mixed $options Link attributes e.g. array('id'=>'selected')
 * @return void
 * @access public 
 */
	function addCrumb($name, $link = null, $options = null) {
		
		$_options = array('forceTitle'=>true);
		if($options) {
			$options = am($_options,$options);
		} else {
			$options = $_options;
		}
		$this->HtmlEx->_crumbs[] = array($name, $link, $options);
		
	}
/**
 * ページリストを取得する
 * 
 * @param string $categoryId
 * @return mixed boolean / array
 * @access public
 */
	function getPageList($categoryId=null) {
		
		if ($this->Page) {
			$conditions = array('Page.status'=>1);
			if($categoryId) {
				$conditions['Page.page_category_id'] = $categoryId;
			}
			$this->Page->unbindModel(array('belongsTo'=>array('PageCategory')));
			$pages = $this->Page->find('all',array('conditions'=>$conditions,
					'fields'=>array('title','url'),
					'order'=>'Page.sort'));
			return Set::extract('/Page/.',$pages);
		}else {
			return false;
		}
		
	}
/**
 *
 */
/**
 * ブラウザにキャッシュさせる為のヘッダーを出力する
 * 
 * @param type $expire
 * @param array $type 
 * @return void
 * @access public
 */
	function cacheHeader($expire = DAY, $type='html') {

		$contentType = array(
			'html' => 'text/html',
			'js' => 'text/javascript', 'css' => 'text/css',
			'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png'
		);
		$fileModified = filemtime(WWW_ROOT.'index.php');

		if(!$expire) {
			$expire = strtotime(DAY);
		} elseif(!is_numeric($expire)){
			$expire = strtotime($expire);
		}
		header("Date: " . date("D, j M Y G:i:s ", $fileModified) . 'GMT');
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s", $fileModified) . " GMT");
		header('Content-type: ' . $contentType[$type]);
		header("Expires: " . gmdate("D, j M Y H:i:s", time() + $expire) . " GMT");
		header('Cache-Control: max-age=' . $expire);
		// Firefoxの場合は不要↓
		//header("Cache-Control: cache");
		header("Pragma: cache");

	}
/**
 * httpから始まるURLを取得する
 * 
 * @param string $url
 * @return string
 * @access public
 */
	function getUri($url){
		if(preg_match('/^http/is', $url)) {
			return $url;
		}else {
			if(empty($_SERVER['HTTPS'])) {
				$protocol = 'http';
			}else {
				$protocol = 'https';
			}
			return $protocol . '://'.$_SERVER['HTTP_HOST'].$this->getUrl($url);
		}
	}
/**
 * プラグインのBaserヘルパを初期化する
 * BaserHelperに定義されていないメソッドをプラグイン内のヘルパに定義する事で
 * BaserHelperから呼び出せるようになる仕組みを提供する。
 * コアからプラグインのヘルパメソッドをBaserHelper経由で直接呼び出せる為、
 * コア側のコントローラーでいちいちヘルパの定義をしなくてよくなり、
 * プラグインを導入しただけでテンプレート上でプラグインのメソッドが呼び出せるようになる。
 * 例えばページ機能のWISIWIG内でプラグインのメソッドを書き込む事ができる。
 *
 * プラグインのBaserヘルパの命名規則：{プラグイン名}BaserHelper
 * （呼びだし方）$baser->feed(1);
 *
 * @return void
 * @access public
 */
	function _initPluginBasers(){

		$view = $this->_view;
		$plugins = Configure::read('Baser.enablePlugins');

		if(!$plugins) {
			return;
		}

		$pluginBasers = array();
		foreach($plugins as $plugin) {
			$pluginName = Inflector::camelize($plugin);
			if(App::import('Helper',$pluginName.'.'.$pluginName.'Baser')) {
				$pluginBasers[] = $pluginName.'BaserHelper';
			}
		}
		$vars = array(
				'base', 'webroot', 'here', 'params', 'action', 'data', 'themeWeb', 'plugin'
		);
		$c = count($vars);
		foreach($pluginBasers as $key => $pluginBaser) {
			$this->pluginBasers[$key] =& new $pluginBaser();
			for ($j = 0; $j < $c; $j++) {
				if(isset($view->{$vars[$j]})) {
					$this->pluginBasers[$key]->{$vars[$j]} = $view->{$vars[$j]};
				}
			}
		}

	}
/**
 * プラグインBaserヘルパ用マジックメソッド
 * Baserヘルパに存在しないメソッドが呼ばれた際プラグインのBaserヘルパを呼び出す
 *
 * @param string $method
 * @param array $params
 * @return ixed
 * @access protected
 */
	function call__($method, $params) {
		
		foreach($this->pluginBasers as $pluginBaser){
			if(method_exists($pluginBaser,$method)){
				return call_user_func_array(array(&$pluginBaser, $method), $params);
			}
		}
		
	}
/**
 * 文字列を検索しマークとしてタグをつける
 *
 * @param string $search 検索文字列
 * @param string $text 検索対象文字列
 * @param string $name マーク用タグ
 * @param array $attributes タグの属性
 * @param boolean $escape エスケープ有無
 * @return string $text 変換後文字列
 * @access public
 */
	function mark($search, $text, $name = 'strong', $attributes = array(), $escape = false) {

		if(!is_array($search)) {
			$search = array($search);
		}
		foreach($search as $value) {
			$text = str_replace($value, $this->HtmlEx->tag($name, $value, $attributes, $escape), $text);
		}
		return $text;
		
	}
/**
 * サイトマップを出力する
 * 
 * @param mixid $pageCategoryId / '' / 0
 * @param string $recursive
 * @return void
 * @access public
 */
	function sitemap($pageCategoryId = null, $recursive = null) {

		$pageList = $this->requestAction('/contents/get_page_list_recursive', array('pass' => array($pageCategoryId, $recursive)));
		$params = array('pageList' => $pageList);
		if(empty($_SESSION['Auth']['User'])) {
			$params = am($params, array(
				'cache' => array(
					'time' => Configure::read('Baser.cachetime'),
					'key' => $pageCategoryId))
			);
		}
		
		$this->element('sitemap', $params);
		
	}
/**
 * Flashを表示する
 * 
 * @param string $path
 * @param string $id
 * @param int $width
 * @param int $height
 * @param array $options
 * @return string 
 */
	function swf($path, $id, $width, $height, $options = array()) {
		
		$options = array_merge(array(
			'version'	=> 7,
			'script'	=> 'swfobject-2.2',
			'noflash'	=> '&nbsp;'
		), $options);
		extract($options);
		
		if(!preg_match('/^\//', $path)) {
			$path = '/img/'.$path;
		}
		if(strpos($path, "\.") === false) {
			$path .= '.swf';
		}
		$path = $this->webroot($path);
		$path = 'http://projects.localhost:8888'.$path;
		$out .= $this->js($script, true)."\n";
		$out = <<< END_FLASH
<div id="{$id}">{$noflash}</div>
<script type="text/javascript">
	swfobject.embedSWF("{$path}", "{$id}", "{$width}", "{$height}", "{$version}");
</script>
END_FLASH;
		
		echo $out;
		
	}
}
?>
