<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザーグループ登録/編集フォーム
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

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ユーザーグループを指定してアクセス制限を登録します。</p>
	<ul>
		<li>ルールを何も追加しない状態では、全てのユーザーが全てのコンテンツにアクセスできるようになっています。</li>
		<li>URL設定ではワイルドカード（*）を利用して一定のURL階層内のコンテンツに対し一度に設定を行う事ができます。</li>
	</ul>
	<div class="example-box">
		<p class="head">（例）ページ管理全体を許可しない設定</p>
		<p>　/admin/pages*</p>
		<p class="head">（例）ブログコンテンツNO:2 の管理を許可しない設定</p>
		<p>　/admin/blog/*/*/2</p>
	</div>
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('Permission') ?>
<?php echo $formEx->input('Permission.id', array('type' => 'hidden')) ?>

<!-- form -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><?php echo $formEx->label('Permission.user_group_id', 'ユーザーグループ') ?></th>
		<td class="col-input">
			<?php $userGroups = $formEx->getControlSource('user_group_id') ?>
			<?php echo $userGroups[$formEx->value('Permission.user_group_id')] ?>
			<?php echo $formEx->input('Permission.user_group_id', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Permission.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('Permission.no') ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Permission.name', 'ルール名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Permission.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $form->error('Permission.name') ?>
			<div id="helptextName" class="helptext"> ルール名には日本語が利用できます。特定しやすいわかりやすい名称を入力してください。 </div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Permission.url', 'URL設定') ?></th>
		<td class="col-input">
			<strong>/<?php echo $authPrefix ?>/</strong>
			<?php echo $formEx->input('Permission.url', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpUrl', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $form->error('Permission.url') ?>
			<div id="helptextUrl" class="helptext">
				<ul>
					<li>BaserCMSの設置URLを除いたスラッシュから始まるURLを入力してください。<br />
						（例）/admin/users/index</li>
					<li>管理画面など認証がかかっているURLしか登録できません。</li>
					<li>特定のフォルダ配下に対しアクセスできないようにする場合などにはワイルドカード（*）を利用します。<br />
						（例）ユーザー管理内のURL全てアクセスさせない場合： /admin/users* </li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Permission.auth', 'アクセス') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Permission.auth', array(
				'type'		=> 'radio',
				'options'	=> $formEx->getControlSource('auth'),
				'legend'	=> false,
				'separator'	=> '　')) ?>
			<?php echo $formEx->error('Permission.auth') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Permission.status', '利用状態') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Permission.status', array(
				'type'		=> 'radio',
				'options'	=> $textEx->booleanStatusList(),
				'legend'	=> false,
				'separator'	=> '　')) ?>
			<?php echo $formEx->error('Permission.status') ?>
		</td>
	</tr>
</table>

<div class="align-center">
<?php if ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削　除', 
			array('action'=>'delete', $formEx->value('Permission.id')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('Permission.name')),
			false); ?>
<?php else: ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>