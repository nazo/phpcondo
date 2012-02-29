<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アップローダーメニュー
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
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>アップローダーメニュー</h2>
	<ul>
		<li><?php $baser->link('アップロードファイル一覧', array('plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index')) ?></li>
		<li><?php $baser->link('カテゴリ一覧', array('plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'index')) ?></li>
		<li><?php $baser->link('カテゴリ新規登録', array('plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'add')) ?></li>
		<li><?php $baser->link('プラグイン基本設定', array('plugin' => 'uploader', 'controller' => 'uploader_configs', 'action' => 'index')) ?></li>
	</ul>
</div>
