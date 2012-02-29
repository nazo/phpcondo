<?php
/* SVN FILE: $Id$ */
/**
 * ブログ記事コントローラー
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
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ブログ記事コントローラー
 *
 * @package			baser.plugins.blog.controllers
 */
class BlogController extends BlogAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Blog';
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
	var $helpers = array('Html', 'TextEx', 'TimeEx', 'Freeze', 'Array', 'Paginator', 'Blog.Blog', 'Cache');
/**
 * コンポーネント
 * 
 * @var array
 * @access public
 */
	var $components = array('AuthEx', 'Cookie', 'AuthConfigure', 'RequestHandler', 'EmailEx', 'Security');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array();
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ブログデータ
 * 
 * @var array
 * @access public
 */
	var $blogContent = array();
/**
 * プレビューフラグ
 * 
 * @var boolean
 * @access public
 */
	var $preview = false;
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		
		parent::beforeFilter();

		/* 認証設定 */
		$this->AuthEx->allow(
			'index', 'mobile_index', 'smartphone_index',
			'archives', 'mobile_archives', 'smartphone_archives',
			'posts', 'mobile_posts', 'smartphone_posts',
			'get_calendar', 'get_categories', 'get_posted_months', 'get_posted_years', 'get_recent_entries'
		);
		
		$this->BlogContent->recursive = -1;
		if($this->contentId) {
			$this->blogContent = $this->BlogContent->read(null,$this->contentId);
		}else {
			$this->blogContent = $this->BlogContent->read(null,$this->params['pass'][0]);
		}

		$this->subMenuElements = array('default');
		$this->navis = array($this->blogContent['BlogContent']['title']=>'/'.$this->blogContent['BlogContent']['name'].'/index');

		// ページネーションのリンク対策
		// コンテンツ名を変更している際、以下の設定を行わないとプラグイン名がURLに付加されてしまう
		// Viewで $paginator->options = array('url' => $this->passedArgs) を行う事が前提
		if(!isset($this->params['admin'])) {
			$this->passedArgs['controller'] = $this->blogContent['BlogContent']['name'];
			$this->passedArgs['plugin'] = $this->blogContent['BlogContent']['name'];
			$this->passedArgs['action'] = $this->action;
		}

		// コメント送信用のトークンを出力する為にセキュリティコンポーネントを利用しているが、
		// 表示用のコントローラーなのでポストデータのチェックは必要ない
		$this->Security->enabled = true;
		$this->Security->validatePost = false;
		
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

		if($this->blogContent['BlogContent']['widget_area']){
			$this->set('widgetArea',$this->blogContent['BlogContent']['widget_area']);
		}

	}
/**
 * [PUBLIC] ブログを一覧表示する
 *
 * @return void
 * @access public
 */
	function index() {

		if ($this->RequestHandler->isRss()) {
			Configure::write('debug', 0);
			$this->set('channel', array(
				'title'			=> h($this->blogContent['BlogContent']['title'].'｜'.$this->siteConfigs['name']),
				'description'	=> h(strip_tags($this->blogContent['BlogContent']['description']))
			));
			$this->layout = 'default';
			$template = 'index';
			$listCount = $this->blogContent['BlogContent']['feed_count'];
		}else {
			$this->layout = $this->blogContent['BlogContent']['layout'];
			$template = $this->blogContent['BlogContent']['template'].DS.'index';
			$listCount = $this->blogContent['BlogContent']['list_count'];
		}

		$datas = $this->_getBlogPosts(array('listCount' => $listCount));
		
		$this->set('posts', $datas);
		$this->set('single', false);
		$this->subMenuElements = array_merge($this->subMenuElements, array('blog_calendar', 'blog_recent_entries', 'blog_category_archives', 'blog_monthly_archives'));
		$this->pageTitle = $this->blogContent['BlogContent']['title'];
		$this->navis = array();
		$this->render($template);

	}
/**
 * [MOBILE] ブログ記事を一覧表示する
 *
 * @return void
 * @access public
 */
	function mobile_index() {

		$this->setAction('index');

	}
/**
 * [SMARTPHONE] ブログ記事を一覧表示する
 *
 * @return void
 * @access public
 */
	function smartphone_index() {

		$this->setAction('index');

	}
/**
 * [PUBLIC] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 * @access public
 */
	function archives() {

		// パラメーター処理
		$pass = $this->params['pass'];
		$type = $year = $month = $day = $id = '';
		$navis = $posts = array();
		$single = false;
		$posts = array();
		
		if($pass[0] == 'category') {
			$type = 'category';
		}elseif($pass[0] == 'tag') {
			$type = 'tag';
		}elseif($pass[0] == 'date') {
			$type='date';
		}

		switch($type) {
			
			/* カテゴリ一覧 */
			case 'category':
			
				$category = $pass[count($pass)-1];
				if(empty($category)) {
					$this->notFound();
				}

				// 記事を取得
				$posts = $this->_getBlogPosts(array('conditions' => array('category' => $category)));

				// ナビゲーションを設定
				$categoryId = $this->BlogCategory->field('id', array(
					'BlogCategory.blog_content_id'	=> $this->contentId,
					'BlogCategory.name'				=> $category
				));
				$blogCategories = $this->BlogCategory->getpath($categoryId,array('name','title'));
				if(count($blogCategories) > 1){
					foreach($blogCategories as $key => $blogCategory) {
						if($key < count($blogCategories) -1 ) {
							$navis[$blogCategory['BlogCategory']['title']] = '/'.$this->blogContent['BlogContent']['name'].'/archives/category/'.$blogCategory['BlogCategory']['name'];
						}
					}
				}
				$this->pageTitle = $blogCategories[count($blogCategories)-1]['BlogCategory']['title'];
				$template = $this->blogContent['BlogContent']['template'].DS.'archives';
				break;
			
			/* タグ別記事一覧 */
			case 'tag':

				$tag = h($pass[count($pass)-1]);
				if(empty($this->blogContent['BlogContent']['tag_use']) || empty($tag)) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(array('conditions' => array('tag' => $tag)));
				$this->pageTitle = urldecode($tag);
				$template = $this->blogContent['BlogContent']['template'].DS.'archives';
				break;
				
			/* 月別アーカイブ一覧 */
			case 'date':

				$year = h($pass[1]);
				$month = h(@$pass[2]);
				$day = h(@$pass[3]);
				if(!$year && !$month && !$day) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(array('conditions' => array('year' => $year, 'month' => $month, 'day' => $day)));
				$this->pageTitle = $year.'年';
				if($month) $this->pageTitle .= $month.'月';
				if($day) $this->pageTitle .= $day.'日';
				$template = $this->blogContent['BlogContent']['template'].DS.'archives';
				break;
			
			/* 単ページ */
			default:

				// プレビュー
				if($this->preview) {
					
					$this->contentId = $pass[0];
					if(!empty($pass[1])) {
						$id = $pass[1];
					} elseif(empty($this->data['BlogPost'])) {
						$this->notFound();
					}
					
					$post['BlogPost'] = $this->data['BlogPost'];
					
					if($this->data['BlogPost']['blog_category_id']) {
						$blogCategory = $this->BlogPost->BlogCategory->find('first', array(
							'conditions'=> array('BlogCategory.id' => $this->data['BlogPost']['blog_category_id']),
							'recursive'	=> -1
						));
						$post['BlogCategory'] = $blogCategory['BlogCategory'];
					}
					
					if($this->data['BlogPost']['user_id']) {
						$author = $this->BlogPost->User->find('first', array(
							'conditions'	=> array('User.id'	=> $this->data['BlogPost']['user_id']),
							'recursive'		=> -1
						));
						$post['User'] = $author['User'];
					}
					
					if(isset($this->data['BlogTag'])) {
						$tags = $this->BlogPost->BlogTag->find('all', array('conditions' => $this->data['BlogTag']['BlogTag']));
						if($tags) {
							$tags = Set::extract('/BlogTag/.', $tags);
							$post['BlogTag'] = $tags;
						}
					}
					
				} else {
					
					if(!empty($pass[0])) {
						$id = $pass[0];
					} else {
						$this->notFound();
					}
					// コメント送信
					if(isset($this->data['BlogComment'])) {
						$this->add_comment($id);
					}
					
					$_posts = $this->_getBlogPosts(array('conditions' => array('id' => $id)));
					if(!empty($_posts[0])) {
						$post = $_posts[0];
					} else {
						$this->notFound();
					}
					
				}

				// ナビゲーションを設定
				if(!empty($post['BlogPost']['blog_category_id'])) {
					$blogCategories = $this->BlogCategory->getpath($post['BlogPost']['blog_category_id'],array('name','title'));
					if($blogCategories) {
						foreach($blogCategories as $blogCategory) {
							$this->navis[$blogCategory['BlogCategory']['title']] = '/'.$this->blogContent['BlogContent']['name'].'/archives/category/'.$blogCategory['BlogCategory']['name'];
						}
					}
				}
				$this->pageTitle = $post['BlogPost']['name'];
				$single = true;
				$template = $this->blogContent['BlogContent']['template'].DS.'single';
				if($this->preview) {
					$this->blogContent['BlogContent']['comment_use'] = false;
				}
				$this->set('post', $post);

		}

		// 表示設定
		$this->navis += $navis;
		$this->set('single',$single);
		$this->set('posts', $posts);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->contentsTitle = $this->pageTitle;
		$this->subMenuElements = array_merge($this->subMenuElements,array('blog_calendar', 'blog_recent_entries', 'blog_category_archives', 'blog_monthly_archives'));
		$this->layout = $this->blogContent['BlogContent']['layout'];
		$this->render($template);

	}
/**
 * コメントを送信する
 * 
 * @param int $id
 * @return void
 * @access public 
 */
	function add_comment($id) {
		
		// blog_post_idを取得
		$conditions = array(
			'BlogPost.no'				=> $id,
			'BlogPost.blog_content_id'	=> $this->contentId
		);
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		
		// 毎秒抽出条件が違うのでキャッシュしない
		$data = $this->BlogPost->find('first', array(
			'conditions'=> $conditions,
			'fields'	=> array('BlogPost.id'),
			'cache'		=> false,
			'recursive'	=> -1
		));
		
		if(empty($data['BlogPost']['id'])) {
			$this->notFound();
		} else {
			$postId = $data['BlogPost']['id'];
		}

		if($this->BlogPost->BlogComment->add($this->data, $this->contentId, $postId, $this->blogContent['BlogContent']['comment_approve'])) {
			
			$this->_sendComment();
			if($this->blogContent['BlogContent']['comment_approve']) {
				$commentMessage = '送信が完了しました。送信された内容は確認後公開させて頂きます。';
			}else {
				$commentMessage = 'コメントの送信が完了しました。';
			}
			$this->data = null;
			
		}else {
			
			$commentMessage = 'コメントの送信に失敗しました。';
			
		}
		
		$this->set('commentMessage',$commentMessage);
		
	}
/**
 * ブログ記事を取得する
 * 
 * @param array $options
 * @return array
 * @access protected
 */
	function _getBlogPosts($options = array()) {
		
		$_options = array(
			'listDirection'	=> $this->blogContent['BlogContent']['list_direction'],
			'listCount'		=> $this->blogContent['BlogContent']['list_count'],
			'conditions'	=> array()
		);
		$_conditions = array(
			'category'	=> null,
			'tag'			=> null,
			'year'			=> null,
			'month'			=> null,
			'day'			=> null,
			'id'			=> null,
			'keyword'		=> null
		);

		$options = am($_options, $options);
		
		$__conditions = array();
		
		if(!empty($this->params['named'])) {
			$__conditions = am($options['conditions'], $this->params['named']);
		} else {
			$__conditions = $options['conditions'];
		}
		unset($options['conditions']);
		$_conditions = am($_conditions, $__conditions);
		
		extract($options);
		
		$expects = array('BlogContent', 'BlogCategory', 'User', 'BlogTag');
		$conditions = array('BlogPost.blog_content_id'	=> $this->contentId);
		
		// カテゴリ条件
		if($_conditions['category']) {
			$category = $_conditions['category'];
			$categoryId = $this->BlogCategory->field('id', array(
				'BlogCategory.blog_content_id'	=> $this->contentId,
				'BlogCategory.name'				=> $category
			));
			$categoryIds = array(0 => $categoryId);

			// 指定したカテゴリ名にぶら下がる子カテゴリを取得
			$catChildren = $this->BlogCategory->children($categoryId);
			if($catChildren) {
				$catChildren = Set::extract('/BlogCategory/id',$catChildren);
				$categoryIds = am($categoryIds, $catChildren);
			}
			$conditions['BlogPost.blog_category_id'] = $categoryIds;
			
		}
		
		// タグ条件
		if($_conditions['tag']) {
			
			$tag = $_conditions['tag'];
			if(!is_array($tag)) {
				$tag = array($tag);
			}
			
			foreach($tag as $key => $value) {
				$tag[$key] = urldecode($value);
			}
			
			$tags = $this->BlogPost->BlogTag->find('all', array(
				'conditions'=> array('BlogTag.name' => $tag), 
				'recursive'	=> 1
			));
			if(isset($tags[0]['BlogPost'][0]['id'])) {
				$ids = Set::extract('/BlogPost/id',$tags);
				$conditions['BlogPost.id'] = $ids;
			} else {
				return array();
			}

		}
		
		// キーワード条件
		if($_conditions['keyword']) {
			$keyword = $_conditions['keyword'];
			if(preg_match('/\s/', $keyword)) {
				$keywords = explode("\s", $keyword);
			} else {
				$keywords = array($keyword);
			}
			foreach($keywords as $key => $value) {
				$keywords[$key] = urldecode($value);
				$conditions['or'][]['BlogPost.name LIKE'] = '%'.$value.'%';
				$conditions['or'][]['BlogPost.content LIKE'] = '%'.$value.'%';
				$conditions['or'][]['BlogPost.detail LIKE'] = '%'.$value.'%';
			}
		}
		
		// 年月日条件
		if($_conditions['year'] || $_conditions['month'] || $_conditions['day']) {
			$year = $_conditions['year'];
			$month = $_conditions['month'];
			$day = $_conditions['day'];
			
			$db=& ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
			switch (str_replace('_ex','',$db->config['driver'])) {
				case 'mysql':
				case 'csv':
					if($year) $conditions["YEAR(BlogPost.posts_date)"] = $year;
					if($month) $conditions["MONTH(BlogPost.posts_date)"] = $month;
					if($day) $conditions["DAY(BlogPost.posts_date)"] = $day;
					break;
				case 'postres':
					if($year) $conditions["date_part('year'(BlogPost.posts_date)"] = $year;
					if($month) $conditions["date_part('month'(BlogPost.posts_date)"] = $month;
					if($day) $conditions["date_part('day'(BlogPost.posts_date)"] = $day;
					break;
				case 'sqlite':
				case 'sqlite3':
					if($year) $conditions["strftime('%Y',BlogPost.posts_date)"] = $year;
					if($month) $conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d',$month);
					if($day) $conditions["strftime('%d',BlogPost.posts_date)"] = sprintf('%02d',$day);
					break;
			}

		}
		
		if($_conditions['id']) {
			$conditions["BlogPost.no"] = $_conditions['id'];
			$expects[] = 'BlogComment';
			$this->BlogPost->hasMany['BlogComment']['conditions'] = array('BlogComment.status'=>true);
			$listCount = 1;
		}
		
		unset($_conditions['category']);
		unset($_conditions['tag']);
		unset($_conditions['keyword']);
		unset($_conditions['year']);
		unset($_conditions['month']);
		unset($_conditions['day']);
		unset($_conditions['id']);
		unset($_conditions['page']);
		unset($_conditions['num']);
		unset($_conditions['sort']);
		unset($_conditions['direction']);
		
		if($_conditions) {
			// とりあえず BlogPost のフィールド固定
			$conditions = am($conditions, $this->postConditions(array('BlogPost' => $_conditions)));
		}
		
		// プレビューの場合は公開ステータスを条件にしない
		if(!$this->preview) {
			$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		}

		$this->BlogPost->expects($expects, false);

		if(!empty($direction)) {
			$listDirection = $direction;
		}
		$order = "BlogPost.posts_date {$listDirection}";
		if(!empty($sort)) {
			$order = "BlogPost.{$sort} {$listDirection}";
			if($sort != 'id') {
				$order .= ", BlogPost.id ASC";
			}
		}
		
		// 毎秒抽出条件が違うのでキャッシュしない
		$this->paginate = array(
				'conditions'=> $conditions,
				'fields'	=> array(),
				'order'		=> $order,
				'limit'		=> $listCount,
				'recursive'	=> 1,
				'cache'		=> false
		);
		
		return $this->paginate('BlogPost');

	}
/**
 * [MOBILE] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 * @access public
 */
	function mobile_archives() {

		$this->setAction('archives');

	}
/**
 * [SMARTPHONE] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 * @access public
 */
	function smartphone_archives() {

		$this->setAction('archives');

	}
/**
 * [ADMIN] プレビューを表示する
 * 
 * @param int $blogContentsId
 * @param int $id
 * @param string $mode
 * @return void
 * @access public
 */
	function admin_preview($blogContentsId, $id, $mode) {
		
		if($mode == 'create') {
			$this->_createPreview($blogContentsId, $id);
		} elseif($mode == 'view') {
			$this->_viewPreview($blogContentsId, $id);
		}
		
	}
/**
 * ブログ記事をプレビュー
 *
 * @param int $blogContentsId / type
 * @param int $id / ""
 * @return void
 * @access protected
 */
	function _createPreview($blogContentsId, $id) {

		Cache::write('blog_posts_preview_'.$id, $this->data);
		echo true;
		exit();
		
	}
/**
 * プレビューを表示する
 *
 * @param int $blogContentId
 * @param int $id
 * @return void
 * @access protected
 */
	function _viewPreview($blogContentsId, $id){

		$data = Cache::read('blog_posts_preview_'.$id);
		Cache::delete('blog_posts_preview_'.$id);
		$this->data = $this->params['data'] = $data;
		$this->preview = true;
		$this->layoutPath = '';
		$this->subDir = '';
		unset($this->params['pass']);
		unset($this->params['prefix']);
		unset($this->params['plugin']);
		unset($this->params['admin']);
		$this->params['controller'] = $this->blogContent['BlogContent']['name'];
		$this->params['action'] = 'archives';
		$this->params['url']['url'] = $this->params['controller'].'/'.'archives'.'/'.$this->data['BlogPost']['no'];
		$this->params['pass'][0] = $this->data['BlogPost']['no'];
		$this->theme = $this->siteConfigs['theme'];
		$this->setAction('archives');

	}
/**
 * ブログカレンダー用のデータを取得する
 * 
 * @param int $id
 * @param int $year
 * @param int $month
 * @return array
 * @access public
 */
	function get_calendar($id,$year='',$month=''){

		$year = h($year);
		$month = h($month);
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$data['entryDates'] = $this->BlogPost->getEntryDates($id,$year,$month);

		if(!$year) {
			$year = date('Y');
		}
		if(!$month) {
			$month = date('m');
		}

		if($month==12) {
			$data['next'] = $this->BlogPost->existsEntry($id, $year+1, 1);
		} else {
			$data['next'] = $this->BlogPost->existsEntry($id, $year, $month+1);
		}
		if($month==1) {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year-1, 12);
		} else {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year, $month-1);
		}
		
		return $data;
		
	}
/**
 * カテゴリー一覧用のデータを取得する
 * 
 * @param int $id
 * @param mixed $count
 * @return array
 * @access public
 */
	function get_categories($id, $count = false){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$data['categories'] = $this->BlogCategory->getCategories($id, $count);
		return $data;
		
	}
/**
 * 月別アーカイブ一覧用のデータを取得する
 * 
 * @param int $id
 * @return mixed $count
 * @access public
 */
	function get_posted_months($id, $count = 12, $viewCount = false){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$data['postedDates'] = $this->BlogPost->getPostedDates($id, array(
			'type'		=> 'month', 
			'count'		=> $count, 
			'viewCount'	=> $viewCount
		));
		return $data;
		
	}
/**
 * 年別アーカイブ一覧用のデータを取得する
 * 
 * @param int $id
 * @param boolean $viewCount
 * @return mixed $count
 * @access public
 */
	function get_posted_years($id, $viewCount = false){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$data['postedDates'] = $this->BlogPost->getPostedDates($id, array(
			'type'		=> 'year', 
			'viewCount'	=> $viewCount
		));
		return $data;
		
	}
/**
 * 最近の投稿用のデータを取得する
 * 
 * @param int $id
 * @param mixed $count
 * @return array
 * @access public
 */
	function get_recent_entries($id, $count = 5){

		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null,$id);
		$this->BlogPost->recursive = -1;
		$conditions = array('BlogPost.blog_content_id'=>$id);
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());
		// 毎秒抽出条件が違うのでキャッシュしない
		$data['recentEntries'] = $this->BlogPost->find('all', array(
				'fields'	=> array('no','name'),
				'conditions'=> $conditions,
				'listCount'		=> $count,
				'order'		=> 'posts_date DESC',
				'recursive'	=> -1,
				'cache'		=> false
		));
		return $data;
		
	}
/**
 * 記事リストを出力
 * requestAction用
 * 
 * @param int $blogContentId
 * @param mixed $num
 * @access public
 */
	function posts($blogContentId, $num = 5) {

		if(!empty($this->params['named']['template'])) {
			$template = $this->params['named']['template'];
		} else {
			$template = 'posts';
		}
		unset($this->params['named']['template']);

		$this->layout = null;
		$this->contentId = $blogContentId;
		$datas = $this->_getBlogPosts(array('listCount' => $num));
		$this->set('posts', $datas);

		$this->render($this->blogContent['BlogContent']['template'].DS . $template);

	}
/**
 * [MOBILE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 * @access public
 */
	function mobile_posts($blogContentId, $num = 5) {
		
		$this->setAction('posts', $blogContentId, $num);
		
	}
/**
 * [SMARTPHONE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 * @access public
 */
	function smartphone_posts($blogContentId, $num = 5) {
		
		$this->setAction('posts', $blogContentId, $num);
		
	}
	
}
?>