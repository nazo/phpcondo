<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メールコンテンツ 一覧
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

<!-- title -->
<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>メールフォームプラグインでは複数のメールフォームの登録が可能です。</p>
	<ul>
		<li>新しいメールフォームを登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>各メールフォームの表示を確認するには、「確認」ボタンをクリックします。</li>
		<li>各メールフォームの内容を変更するには、「管理」ボタンをクリックします。</li>
		<li>各メールフォームの送信先メールアドレスなど、基本設定を変更するには、「編集」ボタンをクリックします。</li>
		<li>メールフォームプラグインの基本設定を変更するには、サイドメニューの「メールプラグイン基本設定」をクリックします。</li>
	</ul>
</div>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableMailContents">
	<tr>
		<th style="width:180px">操作</th>
		<th>NO</th>
		<th>メールフォームアカウント</th>
		<th>メールフォームタイトル</th>
		<th>登録日<br />更新日</th>
	</tr>
<?php if(!empty($listDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($listDatas as $listData): ?>
		<?php if ($count%2 === 0): ?>
			<?php $class=' class="altrow"'; ?>
		<?php else: ?>
			<?php $class=''; ?>
		<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
			<?php $baser->link('確認', array('admin' => false, 'plugin' => '', 'controller' => $listData['MailContent']['name'], 'action' => 'index'), array('target' => '_blank', 'class' => 'btn-green-s button-s')) ?>
			<?php $baser->link('管理', array('controller' => 'mail_fields', 'action' => 'index', $listData['MailContent']['id']), array('class' => 'btn-red-s button-s'), null, false) ?>
			<?php $baser->link('編集', array('action' => 'edit', $listData['MailContent']['id']), array('class' => 'btn-orange-s button-s'), null, false) ?>
			<?php $baser->link('削除', array('action' =>'delete', $listData['MailContent']['id']), array('class' => 'btn-gray-s button-s'), sprintf('本当に「%s」を削除してもいいですか？\n\n※ 現在このメールフォームに設定されているフィールドは全て削除されます。', $listData['MailContent']['title']), false); ?>
		</td>
		<td><?php echo $listData['MailContent']['id'] ?></td>
		<td><?php $baser->link($listData['MailContent']['name'], array('action' => 'edit', $listData['MailContent']['id'])) ?></td>
		<td><?php echo $listData['MailContent']['title'] ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['MailContent']['created']) ?><br />
			<?php echo $timeEx->format('y-m-d',$listData['MailContent']['modified']) ?></td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr><td colspan="6"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<!-- button -->
<div class="align-center">
	<?php $baser->link('新規登録', array('action' => 'add'), array('class' => 'btn-red button')) ?>
</div>
