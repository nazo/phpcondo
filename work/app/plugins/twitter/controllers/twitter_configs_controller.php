<?php
/* SVN FILE: $Id: twitter_configs_controller.php 27 2011-05-16 08:23:23Z ryuring $ */
/**
 * Twitter設定コントローラー
 *
 * PHP versions 4 and 5
 *
 * Baser :  Basic Creating Support Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								18-1 nagao 1-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			twitter.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision: 27 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-05-16 17:23:23 +0900 (月, 16  5 2011) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * Twitter設定コントローラー
 *
 * @package			twitter.controllers
 */
class TwitterConfigsController extends AppController {
/**
 * コントローラー名
 * @var		string
 * @access	public
 */
	var $name = 'TwitterConfigs';
/**
 * モデル
 * @var		array
 * @access	public
 */
	var $uses = array('Twitter.TwitterConfig', 'Twitter.Twitter');
/**
 * コンポーネント
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
	var $helpers = array('FormEx');
/**
 * ぱんくずナビ
 * @var		string
 * @access 	public
 */
	var $navis = array('Twitter管理'=>'/admin/twitter/twitter_configs/form');
/**
 * コンストラクタ
 *
 * @access	public
 */
	function __construct(){
		// セッションのセキュリティレベルが、medium の場合、session.referer_check が設定されてしまい、
		// Twitter からのリダイレクトでセッションが引き継げない為、一旦 low に設定する
		// 但し、ノーマルモードの場合、bootstrapでセッションがスタートされてしまうので、
		// デバッグモードが前提
		Configure::write('Security.level', 'low');
		parent::__construct();
	}
/**
 * beforeFilter
 * @return	void
 * @access	public
 */
	function beforeFilter(){
		
		parent::beforeFilter();
		$this->Auth->allow('authorize_callback');

	}
/**
 * Twitterアプリケーション認証
 * @return	void
 * @access	public
 */
	function admin_authorize () {
		$data = $this->TwitterConfig->findExpanded();
		$redirectUri = Router::url('/twitter/twitter_configs/authorize_callback',true);
		$authorizeUri = $this->Twitter->authorize($data['consumer_key'],
													$data['consumer_secret'],
													$redirectUri,
													$this->Session);
		if($authorizeUri){
			$this->redirect($authorizeUri);
		} else {
			$this->Session->setFlash('Twitterへのアクセスに失敗しました。');
		}

		$this->redirect(array('admin'=>true, 'action'=>'form'));
	}
/**
 * Twitterアプリケーション認証コールバック処理
 * @return	void
 * @access	public
 */
	function authorize_callback () {

		if (isset($this->params['url']['denied'])) {

			$this->Session->SetFlash('アプリケーションの登録が拒否されました。');
			$this->redirect(array('admin'=>true, 'action'=>'authorize'));

		}elseif(isset($this->params['url']['oauth_verifier'])) {

			$data = $this->TwitterConfig->findExpanded();

			if($this->Twitter->createConsumer($data['consumer_key'], $data['consumer_secret'])){
				$accessToken = $this->Twitter->getAccessToken($this->Session);
				if($accessToken){
					$data['access_token_key'] = $accessToken->key;
					$data['access_token_secret'] = $accessToken->secret;
					if($this->TwitterConfig->saveKeyValue($data)){
						$result = true;
					} else {
						$result = false;
					}
				} else {
					$result = false;
				}
			} else {
				$result = false;
			}

			if($result){
				$this->Session->SetFlash('アプリケーションの登録が完了しました。<br />制作・開発モードをノーマルモードに戻しておいてください。');
			} else {
				$this->Session->SetFlash('アプリケーションの登録に失敗しました。');
			}

			$this->redirect(array('admin'=>true, 'action'=>'form'));

		}

		$this->notFound();

	}
/**
 * Twitter設定
 * @return	void
 * @access	public
 */
	function admin_form() {

		$this->pageTitle = 'Twitterプラグイン設定';
		$this->subMenuElements = array('twitter');

		if(!$this->data){

			$this->data['TwitterConfig'] = $this->TwitterConfig->findExpanded();
			if(!empty($this->data['TwitterConfig']['tweet_settings'])){
				$this->data['TwitterConfig']['tweet_settings_array'] = unserialize($this->data['TwitterConfig']['tweet_settings']);
				foreach($this->data['TwitterConfig']['tweet_settings_array'] as $key => $settings) {
					$this->data['TwitterConfig']['tweet_setting_'.$key] = $settings['status'];
				}
			}
		} else {

			if($this->TwitterConfig->validates()){

				// テストデータ生成用↓
				//$tweetSettings = array(array('id'=>1,'name'=>'ブログ記事','plugin'=>'blog','controller'=>'blog_posts','action'=>'edit','status_template'=>'blog','status'=>1));

				$tweetSettings = unserialize($this->data['TwitterConfig']['tweet_settings']);
				$i = 0;
				while(isset($this->data['TwitterConfig']['tweet_setting_'.$i])) {
					$tweetSettings[$i]['status'] = $this->data['TwitterConfig']['tweet_setting_'.$i];
					unset($this->data['TwitterConfig']['tweet_setting_'.$i]);
					$i++;
				}

				$this->data['TwitterConfig']['tweet_settings'] = serialize($tweetSettings);

				if($this->TwitterConfig->saveKeyValue($this->data)) {
					$message = 'Twitterプラグイン設定を保存しました。';
					$this->Session->setFlash($message);
					$this->TwitterConfig->saveDbLog($message);
					$this->redirect(array('action'=>'form'));
				}else{
					$this->Session->setFlash('データベース保存時にエラーが発生しました。');
				}

			} else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

	}

}
?>