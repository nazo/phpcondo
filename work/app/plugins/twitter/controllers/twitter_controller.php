<?php
/* SVN FILE: $Id: twitter_controller.php 18 2011-04-11 10:57:27Z ryuring $ */
/**
 * Twitterコントローラー
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
 * @version			$Revision: 18 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-04-11 19:57:27 +0900 (月, 11  4 2011) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * Twitterコントローラー
 *
 * @package			twitter.controllers
 */
class TwitterController extends AppController {
/**
 * コントローラー名
 * @var		string
 * @access	public
 */
	var $name = 'Twitter';
/**
 * コンポーネント
 * @var		array
 * @access	public
 */
	var $components = array('RequestHandler');
/**
 * [AJAX] Twitterのステータスを更新する（ツイート）
 * @return	mixid	TwitterユーザープロフィールへのURL / false
 * @access	public
 */
	function admin_update(){

		if(!$this->data){
			$this->notFound();
		}else{
			
			$result = false;
			if(!empty($this->data['Twitter']['status'])){
				if($this->Twitter->setupTwitterBehavior()){
					$result = $this->Twitter->update($this->data['Twitter']['status']);
					if($result){
						App::import('Core','Xml');
						$xml = new Xml($result);
						$array = Set::reverse($xml);
						if(!empty($array['Status']['User']['screen_name'])){
							$result = 'http://twitter.com/'.$array['Status']['User']['screen_name'];
						}else{
							$result = false;
						}
					}
				}
			}
			$this->set('result',$result);
		}
		$this->render('ajax_result');
		
	}
/**
 * [AJAX] URLを短いURLに変換する（tinyurl）
 * @return	mixed	変換後のURL / false
 * @access	public
 */
	function admin_tinyurl(){
		if(!$this->data){
			$this->notFound();
		}else{
			$url = $this->convertTinyurl($this->data['Twitter']['url']);
			if($url){
				$this->set('result', $url);
			}else{
				$this->set('result', false);
			}
		}
		$this->render('ajax_result');
	}
/**
 * TinyUrlのWebサービスを利用して短いURLに変換する
 * TinyUrlのサイトに接続できなかった場合は変換せずに返却
 * @param	string	$url
 * @return	string	成功した場合は変換後のURL / 失敗した場合は元のURL
 * @access	public
 */
	function convertTinyurl($url){
		$requestUrl = 'http://tinyurl.com/api-create.php?url='.$url;
		App::import('Core','HttpSocket');
		$sock = new HttpSocket();
		$tinyurl = $sock->get($requestUrl);
		if($tinyurl) {
			return $tinyurl;
		} else {
			return $url;
		}
	}

}
?>