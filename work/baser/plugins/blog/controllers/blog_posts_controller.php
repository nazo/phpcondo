<?php
/* SVN FILE: $Id: blog_posts_controller.php 42 2011-08-23 19:20:59Z ryuring $ */
/**
 * 記事コントローラー
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
 * @package			baser.plugins.blog.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision: 42 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-08-24 04:20:59 +0900 (水, 24 8 2011) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * 記事コントローラー
 *
 * @package baser.plugins.blog.controllers
 */
class BlogPostsController extends BlogAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogPosts';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Blog.BlogCategory', 'Blog.BlogPost', 'Blog.BlogContent');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('TextEx','TimeEx','FormEx','Ckeditor', 'Blog.Blog');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure', 'EmailEx');
/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	var $navis = array('ブログ管理'=>'/admin/blog/blog_contents/index');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ブログコンテンツデータ
 *
 * @var array
 * @access public
 */
	var $blogContent;
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		parent::beforeFilter();
		if(isset($this->params['pass'][0])) {
			$this->BlogContent->recursive = -1;
			$this->blogContent = $this->BlogContent->read(null,$this->params['pass'][0]);
			$this->navis = am($this->navis,array($this->blogContent['BlogContent']['title'].'管理'=>'/admin/blog/blog_posts/index/'.$this->params['pass'][0]));
			if($this->params['prefix']=='admin') {
				$this->subMenuElements = array('blog_posts','blog_categories','blog_common');
			}
		}
		
	}
/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	function beforeRender() {

		parent::beforeRender();
		$this->set('blogContent',$this->blogContent);

	}
/**
 * [ADMIN] 一覧表示
 *
 * @return void
 * @access public
 */
	function admin_index($blogContentId) {

		if(!$blogContentId || !$this->blogContent) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('BlogPost', array('group' => $blogContentId, 'default' => $default));
		
		/* 検索条件生成 */
		$joins = array();

		if(!empty($this->data['BlogPost']['blog_tag_id'])) {
			$db =& ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
			if($db->config['driver'] != 'csv') {
				$joins = array(
					array(
						'table' => $db->config['prefix'].'blog_posts_blog_tags',
						'alias' => 'BlogPostsBlogTag',
						'type' => 'inner',
						'conditions'=> array('BlogPostsBlogTag.blog_post_id = BlogPost.id')
					),
					array(
						'table' => $db->config['prefix'].'blog_tags',
						'alias' => 'BlogTag',
						'type' => 'inner',
						'conditions'=> array('BlogTag.id = BlogPostsBlogTag.blog_tag_id', 'BlogTag.id' => $this->data['BlogPost']['blog_tag_id'])
				));
			}
		}
		$conditions = $this->_createAdminIndexConditions($blogContentId, $this->data);

		// データを取得
		$this->paginate = array('conditions'=>$conditions,
				'joins'	=> $joins,
				'order'	=>'BlogPost.no DESC',
				'limit'	=>$this->passedArgs['num']
		);
		
		// 表示設定
		$this->set('posts', $this->paginate('BlogPost'));
		$this->pageTitle = '['.$this->blogContent['BlogContent']['title'].'] 記事一覧';

	}
/**
 * ページ一覧用の検索条件を生成する
 *
 * @param array $blogContentId
 * @param array $data
 * @return array $conditions
 * @access protected
 */
	function _createAdminIndexConditions($blogContentId, $data) {

		$name = '';
		if(isset($data['BlogPost']['name'])) {
			$name = $data['BlogPost']['name'];
		}
		
		unset($data['BlogPost']['name']);
		unset($data['_Token']);
		if(isset($data['BlogPost']['status']) && $data['BlogPost']['status'] === '') {
			unset($data['BlogPost']['status']);
		}
		if(isset($data['BlogPost']['user_id']) && $data['BlogPost']['user_id'] === '') {
			unset($data['BlogPost']['user_id']);
		}
		unset($data['BlogPost']['open']);

		$conditions = array('BlogPost.blog_content_id'=>$blogContentId);

		// CSVの場合はHABTM先のテーブルの条件を直接設定できない為、タグに関連するポストを抽出して条件を生成
		$db =& ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
		if($db->config['driver'] == 'csv') {
			if(!empty($data['BlogPost']['blog_tag_id'])) {
				$blogTags = $this->BlogPost->BlogTag->read(null, $data['BlogPost']['blog_tag_id']);
				if($blogTags) {
					$conditions['BlogPost.id'] = Set::extract('/BlogPost/id', $blogTags);
				}
			}
		}

		unset($data['BlogPost']['blog_tag_id']);
		
		// ページカテゴリ（子カテゴリも検索条件に入れる）
		if(!empty($data['BlogPost']['blog_category_id'])) {
			$blogCategoryIds = array($data['BlogPost']['blog_category_id']);
			$children = $this->BlogCategory->children($data['BlogPost']['blog_category_id']);
			if($children) {
				foreach($children as $child) {
					$blogCategoryIds[] = $child['BlogCategory']['id'];
				}
			}
			$conditions['BlogPost.blog_category_id'] = $blogCategoryIds;
		} else {
			unset($data['BlogPost']['blog_category_id']);
		}

		$_conditions = $this->postConditions($data);
		if($_conditions) {
			$conditions = am($conditions, $_conditions);
		}

		if($name) {
			$conditions['BlogPost.name LIKE'] = '%'.$name.'%';
		}
		
		return $conditions;

	}
/**
 * [ADMIN] 登録処理
 *
 * @param int $blogContentId
 * @return void
 * @access public
 */
	function admin_add($blogContentId) {

		if(!$blogContentId || !$this->blogContent) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->BlogPost->getDefaultValue($this->AuthEx->user());
		}else {

			$this->data['BlogPost']['blog_content_id'] = $blogContentId;
			$this->data['BlogPost']['no'] = $this->BlogPost->getMax('no',array('BlogPost.blog_content_id'=>$blogContentId))+1;
			$this->data['BlogPost']['posts_date'] = str_replace('/','-',$this->data['BlogPost']['posts_date']);
			$this->BlogPost->create($this->data);

			// データを保存
			if($this->BlogPost->saveAll()) {
				clearViewCache();
				$id = $this->BlogPost->getLastInsertId();
				$message = '記事「'.$this->data['BlogPost']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->BlogPost->saveDbLog($message);
				$this->PluginHook->executeHook('afterBlogPostAdd', $this);
				// 編集画面にリダイレクト
				$this->redirect('/admin/blog/blog_posts/edit/'.$blogContentId.'/'.$id);
			}else {
				$this->Session->setFlash('エラーが発生しました。内容を確認してください。');
			}

		}

		// 表示設定
		$user = $this->AuthEx->user();
		$userModel = $this->getUserModel();
		$categories = $this->BlogPost->getControlSource('blog_category_id', array(
			'blogContentId'	=> $this->blogContent['BlogContent']['id'],
			'rootEditable'	=> $this->checkRootEditable(),
			'userGroupId'	=> $user[$userModel]['user_group_id'],
			'postEditable'	=> true,
			'empty'			=> '指定しない'
		));
		$this->set('editable', true);
		$this->set('categories', $categories);
		$this->set('previewId', 'add_'.mt_rand(0, 99999999));
		$this->set('ckEditorOptions1', array('useDraft' => true, 'draftField' => 'content_draft', 'disableDraft' => true));
		$this->set('ckEditorOptions2', array('useDraft' => true, 'draftField' => 'detail_draft', 'disableDraft' => true));
		$this->set('users',$this->BlogPost->User->getUserList(array('User.id' => $user[$userModel]['id'])));
		$this->pageTitle = '['.$this->blogContent['BlogContent']['title'].'] 新規記事登録';
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 * @param int $blogContentId
 * @param int $id
 * @return void
 * @access public
 */
	function admin_edit($blogContentId,$id) {

		if(!$blogContentId || !$id) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->BlogPost->read(null, $id);
			$this->data['BlogPost']['content_tmp'] = $this->data['BlogPost']['content'];
			$this->data['BlogPost']['detail_tmp'] = $this->data['BlogPost']['detail'];
		}else {
			if(!empty($this->data['BlogPost']['posts_date'])){
				$this->data['BlogPost']['posts_date'] = str_replace('/','-',$this->data['BlogPost']['posts_date']);
			}
			$this->BlogPost->set($this->data);
			// データを保存
			if($this->BlogPost->saveAll()) {
				clearViewCache();
				$message = '記事「'.$this->data['BlogPost']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->BlogPost->saveDbLog($message);
				$this->PluginHook->executeHook('afterBlogPostEdit', $this);
				$this->redirect('/admin/blog/blog_posts/edit/'.$blogContentId.'/'.$id);
			}else {
				$this->Session->setFlash('エラーが発生しました。内容を確認してください。');
			}

		}

		// 表示設定
		$user = $this->AuthEx->user();
		$userModel = $this->getUserModel();
		$editable = false;
		$blogCategoryId = '';
		
		if(isset($this->data['BlogPost']['blog_category_id'])) {
			$blogCategoryId = $this->data['BlogPost']['blog_category_id'];
		}
		if(!$blogCategoryId) {
			$currentCatOwner = $this->siteConfigs['root_owner_id'];
		} else {
			$currentCatOwner = $this->data['BlogCategory']['owner_id'];
		}
		
		$editable = ($currentCatOwner == $user[$userModel]['user_group_id'] ||
					$user[$userModel]['user_group_id'] == 1 || !$currentCatOwner);
		
		$categories = $this->BlogPost->getControlSource('blog_category_id', array(
			'blogContentId'	=> $this->blogContent['BlogContent']['id'],
			'rootEditable'	=> $this->checkRootEditable(),
			'blogCategoryId'=> $blogCategoryId,
			'userGroupId'	=> $user[$userModel]['user_group_id'],
			'postEditable'	=> $editable,
			'empty'			=> '指定しない'
		));
		
		$this->set('editable', $editable);
		$this->set('categories', $categories);
		$this->set('previewId', $this->data['BlogPost']['id']);
		$this->set('users',$this->BlogPost->User->getUserList());
		$this->set('ckEditorOptions1', array('useDraft' => true, 'draftField' => 'content_draft', 'disableDraft' => false));
		$this->set('ckEditorOptions2', array('useDraft' => true, 'draftField' => 'detail_draft', 'disableDraft' => false));
		$this->pageTitle = '['.$this->blogContent['BlogContent']['title'].'] 記事編集： '.$this->data['BlogPost']['name'];
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int $blogContentId
 * @param int $id
 * @return void
 * @access public
 */
	function admin_delete($blogContentId,$id = null) {

		if(!$blogContentId || !$id) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller'=>'blog_contents','action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$post = $this->BlogPost->read(null, $id);

		// 削除実行
		if($this->BlogPost->del($id)) {
			clearViewCache();
			$message = $post['BlogPost']['name'].' を削除しました。';
			$this->Session->setFlash($message);
			$this->BlogPost->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'admin_index',$blogContentId));

	}
/**
 * 外部データインポート
 * WordPressのみ対応（2.2.3のみ検証済）
 *
 * @return void
 * @access public
 */
	function admin_import() {

		// 入力チェック
		$check = true;
		$message = '';
		if(!isset($this->data['Import']['blog_content_id']) || !$this->data['Import']['blog_content_id']) {
			$message .= '取り込み対象のブログを選択してください<br />';
			$check = false;
		}
		if(!isset($this->data['Import']['user_id']) || !$this->data['Import']['user_id']) {
			$message .= '記事の投稿者を選択してください<br />';
			$check = false;
		}
		if(!isset($this->data['Import']['file']['tmp_name'])) {
			$message .= 'XMLデータを選択してください<br />';
			$check = false;
		}
		if($this->data['Import']['file']['type'] != 'text/xml') {
			$message .= 'XMLデータを選択してください<br />';
			$check = false;
		}else {

			// XMLデータを読み込む
			App::import('Xml');
			$xml = new Xml($this->data['Import']['file']['tmp_name']);

			$_posts = Set::reverse($xml);

			if(!isset($_posts['Rss']['Channel']['Item'])) {
				$message .= 'XMLデータが不正です<br />';
				$check = false;
			}else {
				$_posts = $_posts['Rss']['Channel']['Item'];
			}

		}

		// 送信内容に問題がある場合には元のページにリダイレクト
		if(!$check) {
			$this->Session->setFlash($message);
			$this->redirect(array('controller'=>'blog_configs','action'=>'form'));
		}

		// カテゴリ一覧の取得
		$blogCategoryList = $this->BlogCategory->find('list',array('conditions'=>array('blog_content_id'=>$this->data['Import']['blog_content_id'])));
		$blogCategoryList = array_flip($blogCategoryList);

		// ポストデータに変換し１件ずつ保存
		$count = 0;
		foreach($_posts as $_post) {
			if(!$_post['Encoded'][0]) {
				continue;
			}
			$post = array();
			$post['blog_content_id'] = $this->data['Import']['blog_content_id'];
			$post['no'] = $this->BlogPost->getMax('no',array('BlogPost.blog_content_id'=>$this->data['Import']['blog_content_id']))+1;
			$post['name'] = $_post['title'];
			$_post['Encoded'][0] = str_replace("\n","<br />",$_post['Encoded'][0]);
			$encoded = split('<!--more-->',$_post['Encoded'][0]);
			$post['content'] = $encoded[0];
			if(isset($encoded[1])) {
				$post['detail'] = $encoded[1];
			}else {
				$post['detail'] = '';
			}
			if(isset($_post['Category'])) {
				$_post['category'] = $_post['Category'][0];
			}elseif(isset($_post['category'])) {
				$_post['category'] = $_post['category'];
			}else {
				$_post['category'] = '';
			}
			if(isset($blogCategoryList[$_post['category']])) {
				$post['blog_category_no'] = $blogCategoryList[$_post['category']];
			}else {
				$no = $this->BlogCategory->getMax('no',array('BlogCategory.blog_content_id'=>$this->data['Import']['blog_content_id']))+1;
				$this->BlogCategory->create(array('name'=>$_post['category'],'blog_content_id'=>$this->data['Import']['blog_content_id'],'no'=>$no));
				$this->BlogCategory->save();
				$post['blog_category_id'] = $this->BlogCategory->getInsertID();
				$blogCategoryList[$_post['category']] = $post['blog_category_id'];
			}

			$post['user_id'] = $this->data['Import']['user_id'];
			$post['status'] = 1;
			$post['posts_date'] = $_post['post_date'];

			$this->BlogPost->create($post);
			if($this->BlogPost->save()) {
				$count++;
			}

		}

		$this->Session->setFlash( $count . ' 件の記事を取り込みました');
		$this->redirect(array('controller'=>'blog_configs','action'=>'form'));

	}

}
?>