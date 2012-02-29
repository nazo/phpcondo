<?php
/* SVN FILE: $Id$ */
/**
 * ブログヘルパー
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
 * @package			baser.plugins.blog.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ブログヘルパー
 * @package baser.plugins.blog.views.helpers
 */
class BlogHelper extends AppHelper {
/**
 * view
 * 
 * @var View
 * @access protected
 */
	var $_view = null;
/**
 * ヘルパー
 * 
 * @var array
 * @access public
 */
	var $helpers = array('Html','TimeEx','Baser');
/**
 * ブログカテゴリモデル
 * 
 * @var BlogCategory
 * @access public
 */
	var $BlogCategory = null;
/**
 * コンストラクタ
 *
 * @return void
 * @access public
 */
	function __construct() {
		
		$this->_view =& ClassRegistry::getObject('view');
		$this->_setBlogContent();
		
	}
/**
 * ブログコンテンツデータをセットする
 * 
 * @param int $blogContentId 
 * @return void
 * @access protected
 */
	function _setBlogContent($blogContentId = null) {

		if(isset($this->blogContent) && !$blogContentId) {
			return;
		}
		if($blogContentId) {
			$BlogContent = ClassRegistry::getObject('BlogContent');
			$BlogContent->expects(array());
			$this->blogContent = Set::extract('BlogContent', $BlogContent->read(null, $blogContentId));
		} elseif(isset($this->_view->viewVars['blogContent'])) {
			$this->blogContent = $this->_view->viewVars['blogContent']['BlogContent'];
		}

	}
/**
 * タイトルを表示する
 * 
 * @return void
 * @access public
 */
	function title() {
		
		echo $this->getTitle();
		
	}
/**
 * タイトルを取得する
 * 
 * @return string
 * @access public
 */
	function getTitle() {
		
		return $this->blogContent['title'];
		
	}
/**
 * ブログの説明文を取得する
 * 
 * @return string
 * @access public
 */
	function getDescription() {
		
		return $this->blogContent['description'];
		
	}
/**
 * ブログの説明文を表示する
 * 
 * @return void
 * @access public
 */
	function description() {
		echo $this->getDescription();
	}
/**
 * ブログの説明文が指定されているかどうか
 * 
 * @return boolean
 * @access public
 */
	function descriptionExists() {
		
		if(!empty($this->blogContent['description'])) {
			return true;
		}else {
			return false;
		}
		
	}
/**
 * 記事のタイトルを表示する
 * 
 * @param array $post
 * @return void
 */
	function postTitle($post, $link = true) {

		echo $this->getPostTitle($post, $link);

	}
/**
 * 記事タイトルを取得する
 *
 * @param array $post
 * @param boolean $link
 * @return string
 * @access public
 */
	function getPostTitle($post, $link) {

		if($link) {
			return $this->getPostLink($post, $post['BlogPost']['name']);
		} else {
			return $post['BlogPost']['name'];
		}
		
	}
/**
 * 記事へのリンクを取得する
 *
 * @param array $post
 * @param string $title
 * @param array $options
 * @return string
 * @access public
 */
	function getPostLink($post, $title, $options = array()) {

		$this->_setBlogContent($post['BlogPost']['blog_content_id']);
		$url = array('admin'=>false,'plugin'=>'','controller'=>$this->blogContent['name'],'action'=>'archives', $post['BlogPost']['no']);
		return $this->Baser->getLink($title, $url, $options);
		
	}
/**
 * 記事へのリンクを出力する
 *
 * @param array $post
 * @param string $title
 * @return void
 * @access public
 */
	function postLink($post, $title, $options = array()) {

		echo $this->getPostLink($post, $title, $options);

	}
/**
 * コンテンツを表示する
 * 
 * @param array $post
 * @param mixied boolean / string $moreLink
 * @return void
 * @access public
 */
	function postContent($post,$moreText = true, $moreLink = false, $cut = false) {
		
		echo $this->getPostContent($post, $moreText, $moreLink, $cut);
		
	}
/**
 * コンテンツデータを取得する
 * 
 * @param array $post
 * @param mixied boolean / string $moreLink
 * @return string
 * @access public
 */
	function getPostContent($post,$moreText = true, $moreLink = false, $cut = false) {

		if($moreLink === true) {
			$moreText = '≫ 続きを読む';
		}elseif($moreLink !== false) {
			$moreText = $moreLink;
		}

		$out =	'<div class="post-body">'.$post['BlogPost']['content'].'</div>';
		if($moreLink && trim($post['BlogPost']['detail']) && trim($post['BlogPost']['detail']) != "<br>") {
			$out .= '<p class="more">'.$this->Html->link($moreText, array('admin'=>false,'plugin'=>'', 'controller'=>$this->blogContent['name'],'action'=>'archives', $post['BlogPost']['no'],'#'=>'post-detail'), null,null,false).'</p>';
		}elseif($moreText && $post['BlogPost']['detail']) {
			$out .=	'<div id="post-detail">'.$post['BlogPost']['detail'].'</div>';
		}
		if($cut) {
			$out = mb_substr(strip_tags($out), 0, $cut, 'UTF-8');
		}
		return $out;

	}
/**
 * カテゴリを出力する

 * @param array $post
 * @return void
 * @access puublic
 */
	function category($post, $options = array()) {
		
		echo $this->getCategory($post, $options);
		
	}
/**
 * カテゴリを取得する
 * 
 * @param array $post
 * @return string
 */
   function getCategory($post, $options = array()) {
       
       if(!empty($post['BlogCategory']['name'])) {
           
           $options = am(array('link' => true), $options);
           $link = false;
           
           if($options['link']) {
               $link = true;
           }
           
           unset($options['link']);
           
           if($link) {
               if(!isset($this->Html)){
                   $this->Html = new HtmlHelper();
               }
               return $this->Html->link($post['BlogCategory']['title'],$this->getCategoryUrl($post['BlogCategory']['id']),$options,null,false);
           } else {
               return $post['BlogCategory']['title'];
           }
           
       }else {
           return '';
       }
       
   }
/**
 * タグを出力する
 *
 * @param array $post
 * @param string $separator
 * @return void
 * @access public
 */
	function tag($post, $separator = ' , ') {
		
		echo $this->getTag($post, $separator);
		
	}
/**
 * タグを取得する
 *
 * @param array $post
 * @param string $separator
 * @return void
 * @access public
 */
	function getTag($post, $separator = ' , ') {

		$tagLinks = array();
		if(!empty($post['BlogTag'])) {
			foreach($post['BlogTag'] as $tag) {
				$url = array('admin'=>false,'plugin'=>'','controller'=>$this->blogContent['name'],'action'=>'archives', 'tag', $tag['name']);
				$tagLinks[] = $this->Baser->getLink($tag['name'], $url);
			}
		}
		if($tagLinks) {
			return implode($separator, $tagLinks);
		} else {
			return '';
		}
		
	}
/**
 * カテゴリのURLを取得する
 * [注意] リンク関数でラップする前提の為、ベースURLは考慮されない
 * 
 * @param string $blogCategoyId
 * @return void
 */
	function getCategoryUrl($blogCategoryId) {

		if (!isset($this->BlogCategory)) {
			$this->BlogCategory =& ClassRegistry::init('BlogCategory','Model');
		}
		$categoryPath = $this->BlogCategory->getPath($blogCategoryId);
		$blogContentId = $categoryPath[0]['BlogCategory']['blog_content_id'];
		$this->_setBlogContent($blogContentId);
		$blogContentName = $this->blogContent['name'];
		
		$path = array('category');
		if($categoryPath) {
			foreach($categoryPath as $category) {
				$path[] = $category['BlogCategory']['name'];
			}
		}
		$url = Router::url(am(array('admin'=>false,'plugin'=>'','controller'=>$blogContentName,'action'=>'archives'),$path));
		$baseUrl = preg_replace('/\/$/', '', baseUrl());
		return preg_replace('/^'.preg_quote($baseUrl, '/').'/', '', $url);

	}
/**
 * 登録日
 * 
 * @param array $post
 * @param string $format
 * @return void
 * @access public
 */
	function postDate($post,$format = 'Y/m/d') {
		
		echo $this->getPostDate($post, $format);
		
	}
/**
 * 登録日
 * 
 * @param array $post
 * @param string $format
 * @return void
 * @access public
 */
	function getPostDate($post,$format = 'Y/m/d') {
		
		return $this->TimeEx->format($format,$post['BlogPost']['posts_date']);
		
	}
/**
 * 投稿者を出力
 * 
 * @param array $post
 * @return void
 * @access public
 */
	function author($post) {
		
		$author = '';
		if(!empty($post['User']['real_name_1'])) {
			$author .= $post['User']['real_name_1'];
		}
		if(!empty($post['User']['real_name_2'])) {
			$author .= " ".$post['User']['real_name_2'];
		}
		echo $author;
		
	}
/**
 * カテゴリーリストを取得する
 * 
 * @param $categories
 * @param $depth
 * @return string
 * @access public
 */
	function getCategoryList($categories,$depth=3, $count = false) {
		
		return $this->_getCategoryList($categories,$depth, 1, $count);
		
	}
/**
 * カテゴリーリストを取得する
 * 
 * @param $categories
 * @param $depth
 * @return string
 * @access public
 */
	function _getCategoryList($categories, $depth=3, $current=1, $count = false) {
		
		if($depth < $current) {
			return '';
		}
		$baseCurrentUrl = $this->blogContent['name'].'/archives/category/';
		if($categories) {
			$out = '<ul class="depth-'.$current.'">';
			$current++;
			foreach($categories as $category) {
				if($count && isset($category['BlogCategory']['count'])) {
					$category['BlogCategory']['title'] .= '('.$category['BlogCategory']['count'].')';
				}
				if($this->_view->params['url']['url'] == $baseCurrentUrl.$category['BlogCategory']['name']) {
					$class = ' class="current"';
				} elseif(!empty($this->_view->params['named']['category']) && $this->_view->params['named']['category'] == $category['BlogCategory']['name']) {
					$class = ' class="selected"';
				} else {
					$class = '';
				}
				$out .= '<li'.$class.'>'.$this->getCategory($category);
				if(!empty($category['children'])) {
					$out.= $this->_getCategoryList($category['children'],$depth,$current, $count);
				}
				$out.='</li>';
			}
			$out .= '</ul>';
			return $out;
		}else {
			return '';
		}
		
	}
/**
 * ブログ編集ページへのリンクを出力
 * 
 * @param string $id
 * @return void
 * @access public
 */
	function editPost($blogContentId,$blogPostId) {
		
		if(empty($this->params['admin']) && !empty($this->_view->viewVars['user']) && !Configure::read('AgentPrefix.on')) {
			echo '<div class="edit-link">'.$this->Baser->getLink('≫ 編集する',array('admin'=>true,'prefix'=>'blog','controller'=>'blog_posts','action'=>'edit',$blogContentId,$blogPostId),array('target'=>'_blank')).'</div>';
		}
		
	}
/**
 * 前の記事へのリンクを取得する
 * 
 * @param array $post
 * @param string $title
 * @param array $htmlAttributes
 * @return void
 * @access pulic
 */
	function prevLink($post,$title='',$htmlAttributes = array()) {

		if(ClassRegistry::isKeySet('BlogPost')) {
			$BlogPost = ClassRegistry::getObject('BlogPost');
		} else {
			$BlogPost = ClassRegistry::init('BlogPost');
		}
		$_htmlAttributes = array('class'=>'prev-link','arrow'=>'≪ ');
		$htmlAttributes = am($_htmlAttributes,$htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		$BlogPost =& ClassRegistry::getObject('BlogPost');
		$conditions = array();
		$conditions['BlogPost.posts_date <'] = $post['BlogPost']['posts_date'];
		$conditions["BlogPost.blog_content_id"] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		// 毎秒抽出条件が違うのでキャッシュしない
		$prevPost = $BlogPost->find('first', array(
			'conditions'	=> $conditions,
			'fields'		=> array('no','name'),
			'order'			=> 'posts_date DESC',
			'recursive'		=> -1,
			'cache'			=> false
		));
		if($prevPost) {
			$no = $prevPost['BlogPost']['no'];
			if(!$title) {
				$title = $arrow.$prevPost['BlogPost']['name'];
			}
			$this->Baser->link($title, array('admin'=>false,'plugin'=>'', 'controller'=>$this->blogContent['name'],'action'=>'archives', $no),$htmlAttributes);
		}

	}
/**
 * 次の記事へのリンクを取得する
 * 
 * @param array $post
 * @return void
 * @access public
 */
	function nextLink($post,$title='',$htmlAttributes = array()) {

		if(ClassRegistry::isKeySet('BlogPost')) {
			$BlogPost = ClassRegistry::getObject('BlogPost');
		} else {
			$BlogPost = ClassRegistry::init('BlogPost');
		}
		$_htmlAttributes = array('class'=>'next-link','arrow'=>' ≫');
		$htmlAttributes = am($_htmlAttributes,$htmlAttributes);
		$arrow = $htmlAttributes['arrow'];
		unset($htmlAttributes['arrow']);
		$BlogPost =& ClassRegistry::getObject('BlogPost');
		$conditions = array();
		$conditions['BlogPost.posts_date >'] = $post['BlogPost']['posts_date'];
		$conditions["BlogPost.blog_content_id"] = $post['BlogPost']['blog_content_id'];
		$conditions = am($conditions, $BlogPost->getConditionAllowPublish());
		// 毎秒抽出条件が違うのでキャッシュしない
		$nextPost = $BlogPost->find('first', array(
			'conditions'	=> $conditions,
			'fields'		=> array('no','name'),
			'order'			=> 'posts_date',
			'recursive'		=> -1,
			'cache'			=> false
		));
		if($nextPost) {
			$no = $nextPost['BlogPost']['no'];
			if(!$title) {
				$title = $nextPost['BlogPost']['name'].$arrow;
			}
			$this->Baser->link($title, array('admin'=>false,'plugin'=>'','mobile'=>false,'controller'=>$this->blogContent['name'],'action'=>'archives', $no),$htmlAttributes);
		}

	}
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 別のヘルパに移動
 * @return array
 * @access public
 */
	function getLayoutTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'layouts'.DS;
		}
		$templatesPathes[] = APP . 'plugins' . DS . 'blog'.DS.'views'.DS.'layouts'.DS;
		$templatesPathes = am($templatesPathes,array(BASER_PLUGINS.'blog'.DS.'views'.DS.'layouts'.DS,
														BASER_VIEWS.'layouts'.DS));
		
		$_templates = array();
		foreach($templatesPathes as $templatesPath){
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[1]){
				if($_templates){
					$_templates = am($_templates,$files[1]);
				}else{
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'installations.ctp'){
				$template = basename($template, '.ctp');
				$templates[$template] = $template;
			}
		}
		return $templates;
		
	}
/**
 * ブログテンプレートを取得
 * コンボボックスのソースとして利用
 * TODO 別のヘルパに移動
 * @return array
 * @access public
 */
	function getBlogTemplates() {

		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'blog'.DS;
		}
		$templatesPathes[] = APP . 'plugins' . DS . 'blog'.DS.'views'.DS.'blog'.DS;
		$templatesPathes[] = BASER_PLUGINS.'blog'.DS.'views'.DS.'blog'.DS;
		
		$_templates = array();
		foreach($templatesPathes as $templatePath){
			$folder = new Folder($templatePath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[0]){
				if($_templates){
					$_templates = am($_templates,$files[0]);
				}else{
					$_templates = $files[0];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			if($template != 'rss' && $template != 'mobile'){
				$templates[$template] = $template;
			}
		}
		return $templates;
		
	}
/**
 * 公開状態を取得する
 *
 * @param array $data データリスト
 * @return boolean 公開状態
 * @access	public
 */
	function allowPublish($data){

		if(ClassRegistry::isKeySet('BlogPost')) {
			$BlogPost = ClassRegistry::getObject('BlogPost');
		} else {
			$BlogPost = ClassRegistry::init('BlogPost');
		}
		return $BlogPost->allowPublish($data);

	}
/**
 * 記事中の画像を出力する
 *
 * @param array $post
 * @param array $options
 * @return void
 * @access public
 */
	function postImg($post, $options = array()) {

		echo $this->getPostImg($post, $options);
		
	}
/**
 * 記事中の画像を取得する
 *
 * @param array $post
 * @param array $options
 * @return void
 * @access public
 */
	function getPostImg($post, $options = array()) {
		
		$this->_setBlogContent($post['BlogPost']['blog_content_id']);
		$_options = array('num' => 1, 'link' => true, 'alt' => $post['BlogPost']['name']);
		$options = am($_options, $options);
		extract($options);
		unset($options['num']);
		unset($options['link']);
		
		$contents = $post['BlogPost']['content'].$post['BlogPost']['detail'];
		$pattern = '/<img.*?src="([^"]+)"[^>]*>/is';
		if(!preg_match_all($pattern, $contents, $matches)){
			return '';
		}

		if(isset($matches[1][$num-1])) {
			$url = $matches[1][$num-1];
			$url = preg_replace('/^'.preg_quote($this->base, '/').'/', '', $url);
			$img = $this->Baser->getImg($url, $options);
			if($link) {
				return $this->Baser->getLink($img, $url = array('admin'=>false,'plugin'=>'','controller'=>$this->blogContent['name'],'action'=>'archives', $post['BlogPost']['no']));
			} else {
				return $img;
			}
		} else {
			return '';
		}
		
	}
/**
 * 記事の本文、詳細の中で指定したIDの中のHTMLを取得する
 *
 * @param array $post
 * @param string $id
 * @return string
 * @access public
 */
	function getHtmlById($post, $id) {
		
		$content = $post['BlogPost']['content'].$post['BlogPost']['detail'];
		
		$values = array();
		$pattern = '/<([^\s]+)\s[^>]*?id="'.$id.'"[^>]*>(.*?)<\/\1>/is';
		if(preg_match($pattern, $content, $matches)){
			return $matches[2];
		}else{
			return '';
		}
	
	}
/**
 * 親カテゴリを取得する
 *
 * @param array $post
 * @return array $parentCategory
 * @access public
 */
	function getParentCategory($post) {

		if(empty($post['BlogCategory']['id'])) {
			return null;
		}

		$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		return $BlogCategory->getparentnode($post['BlogCategory']['id']);
		
	}
}
?>