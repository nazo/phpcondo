<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メールフォーム共通メニュー
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
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<div class="side-navi">
	<h2>メールプラグイン<br />
		共通メニュー</h2>
	<ul>
		<li><?php $baser->link('メールフォーム一覧',array('controller'=>'mail_contents','action'=>'index')) ?></li>
		<li><?php $baser->link('新規メールフォームを登録',array('controller'=>'mail_contents','action'=>'add')) ?></li>
		<li><?php $baser->link('プラグイン基本設定',array('controller'=>'mail_configs','action'=>'form')) ?></li>
	</ul>
</div>
