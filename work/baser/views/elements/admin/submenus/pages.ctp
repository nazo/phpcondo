<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページ管理メニュー
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
	<h2>ページ管理メニュー</h2>
	<ul>
		<li>
			<?php $baser->link('一覧を表示する',array('controller'=>'pages','action'=>'admin_index')) ?>
		</li>
<?php if($newCatAddable): ?>
		<li>
			<?php $baser->link('新規に登録する',array('controller'=>'pages','action'=>'admin_add')) ?>
		</li>
<?php endif ?>
		<li>
			<?php $baser->link('ページテンプレート読込',array('controller'=>'pages','action'=>'admin_entry_page_files'),array('confirm'=>'テーマ '.Inflector::camelize($baser->siteConfig['theme']).' フォルダ内のページテンプレートを全て読み込みます。\n本当によろしいですか？')) ?>
		</li>
		<li>
			<?php $baser->link('ページテンプレート書出',array('controller'=>'pages','action'=>'admin_write_page_files'),array('confirm'=>'データベース内のページデータを、'.'テーマ '.Inflector::camelize($baser->siteConfig['theme']).' のページテンプレートとして全て書出します。\n本当によろしいですか？')) ?>
		</li>
	</ul>
</div>
