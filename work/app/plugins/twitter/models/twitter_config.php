<?php
/* SVN FILE: $Id: twitter_config.php 18 2011-04-11 10:57:27Z ryuring $ */
/**
 * Twitter設定モデル
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
 * Twitter設定モデル
 *
 * @package			twitter.models
 */
class TwitterConfig extends AppModel{
/**
 * モデル名
 * @var		string
 * @access	public
 */
	var $name = 'TwitterConfig';
/**
 * DB設定
 * @var		string
 * @access 	public
 */
	var $useDbConfig = 'plugin';
/**
 * プラグイン名
 * @var		string
 * @access 	public
 */
	var $plugin = 'Twitter';
}
?>