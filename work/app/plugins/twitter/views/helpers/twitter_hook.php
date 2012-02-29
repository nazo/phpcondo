<?php
/* SVN FILE: $Id: twitter_hook.php 34 2011-05-16 10:24:23Z ryuring $ */
/**
 * Twitterフックモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			twitter.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision: 34 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-05-16 19:24:23 +0900 (月, 16  5 2011) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * Twitterフックモデル
 *
 * @package			twitter.models
 */
class TwitterHookHelper extends AppHelper{
/**
 * 登録フック
 * @var		array
 * @access	public
 */
	var $registerHooks = array('afterFormCreate');
/**
 * formExCreate
 * @param	string	$out
 * @return	string	$out
 * @access	public
 */
	function afterFormCreate($form, $out){
		
		if($form->model() == 'Twitter') {
			return $out;
		}
		$TwitterConfig = ClassRegistry::init('Twitter.TwitterConfig');
		$config = $TwitterConfig->findExpanded();
		if(empty($config['tweet_settings']) || empty($config['consumer_secret']) || empty($config['access_token_secret'])){
			return $out;
		}
		$settings = unserialize($config['tweet_settings']);

		if(!$settings) {
			return $out;
		}

		$plugin = $controller = $action = '';
		
		if(empty($form->params['admin'])){
			return $out;
		}
		if(!empty($form->params['plugin'])){
			$plugin = $form->params['plugin'];
		}
		$controller = $form->params['controller'];
		$action = $form->params['action'];

		$tweet = false;
		foreach ($settings as $setting) {
			if($plugin == $setting['plugin'] && $controller == $setting['controller'] && $action == $setting['action'] && $setting['status']){
				$tweet = true;
				break;
			}
		}

		if($tweet) {
			$View = ClassRegistry::getObject('View');
			return $View->renderElement('admin/twitter_update', array('plugin' => 'twitter','statusTemplate'=>$setting['status_template'])).$out;
		}
		
		return $out;

	}
}
?>
