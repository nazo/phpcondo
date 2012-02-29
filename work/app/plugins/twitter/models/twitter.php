<?php
/* SVN FILE: $Id: twitter.php 18 2011-04-11 10:57:27Z ryuring $ */
/**
 * Twitterモデル
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
 * @package			twitter.models
 * @since			Baser v 0.1.0
 * @version			$Revision: 18 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-04-11 19:57:27 +0900 (月, 11  4 2011) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * Twitterモデル
 *
 * @package			twitter.models
 */
class Twitter extends AppModel {
/**
 * モデル名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Twitter';
/**
 * プラグイン名
 *
 * @var		string
 * @access 	public
 */
	var $plugin = 'Twitter';
/**
 * 利用テーブル
 *
 * @var		string
 * @access 	public
 */
	var $useTable = false;
/**
 * ビヘイビア
 *
 * @var		string
 * @access 	public
 */
	var $actsAs = array('Twitter.Twitter');
/**
 * Twitterビヘイビアのセットアップ
 * @return	boolean	Consumer生成の結果値
 * @access	void
 */
	function setupTwitterBehavior(){
		
		$TwitterConfig = ClassRegistry::init('Twitter.TwitterConfig');
		$data = $TwitterConfig->findExpanded();
		$config['consumerKey'] = $data['consumer_key'];
		$config['consumerSecret'] = $data['consumer_secret'];
		$config['accessTokenKey'] = $data['access_token_key'];
		$config['accessTokenSecret'] = $data['access_token_secret'];;
		$this->Behaviors->attach('Twitter.Twitter',$config);
		return $this->createConsumer();
		
	}
	
}
?>