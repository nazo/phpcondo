<?php
/* SVN FILE: $Id: form.ctp 18 2011-04-11 10:57:27Z ryuring $ */
/**
 * [ADMIN] Twitterプラグイン設定画面
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			twitter.views
 * @since			Baser v 0.1.0
 * @version			$Revision: 18 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-04-11 19:57:27 +0900 (月, 11  4 2011) $
 * @license			http://basercms.net/license/index.html
 */
?>
<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>Twitterプラグインでは次の二つの機能を提供しています。</p>
	<ul>
		<li>任意のユーザーのTwitterユーザータイムラインを任意の場所に表示</li>
		<li>ブログ記事編集画面へのTwitter投稿フォームの表示</li>
	</ul>
	<h5>Twitterタイムラインを表示するには</h5>
	<p>Twitterのユーザー名と表示件数を下のフォームに入力します。その後、<?php $baser->link('ウィジェットエリア管理',array('plugin'=>null,'controller'=>'widget_areas','action'=>'index')) ?>より「Twitterユーザータイムライン」を選択します。</p>
	<h5>ブログ記事の編集画面からTwitterの投稿を行うには</h5>
	<p>BaserCMSからTwitterへ投稿するには、BaserCMSがあなたのTwitterアカウントにアクセスできるように認証を行う必要があります。
	<br />まず、サイドメニューの「Twitterアプリ認証」をクリックします。
	Twitterの認証画面が表示されますので、「許可する」をクリックします。その後、この画面に戻ってきますので、
	下のフォームの「ブログ記事」にチェックを入れて保存すると完了です。ブログの記事編集画面にTwitterへの投稿フォームが表示されます。<br />
	別のアカウントに対して投稿を行うには、再度、サイドメニューの「Twitterアプリ認証」をクリックします。次の画面でログインしなおして「許可する」ボタンをクリックして下さい。</p>
</div>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('TwitterConfig',array('action'=>'form')) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><?php echo $formEx->label('TwitterConfig.username', 'Twitterユーザー名') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('TwitterConfig.username', array('size'=>35,'maxlength'=>255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpUsername','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('TwitterConfig.username') ?>
			<div id="helptextUsername" class="helptext">
				<ul>
					<li>アルファベットのユーザー名を入力します。</li>
					<li>Twitterのタイムライン出力に利用します。</li>
					<li>Twitterにサインアップされていない方は、<a href="http://twitter.com/signup" target="_blank">こちら</a>より取得します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('TwitterConfig.view_num', 'タイムライン表示件数') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('TwitterConfig.view_num', array('size'=>5,'maxlength'=>3)) ?> 件
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpViewNum','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('TwitterConfig.view_num') ?>
			<div id="helptextViewNum" class="helptext">タイムラインに表示する件数を入力します。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('TwitterConfig.description', 'Twitter投稿機能') ?></th>
		<td class="col-input">
			<?php echo $formEx->hidden('TwitterConfig.tweet_settings') ?>
			<?php if(!$formEx->value('TwitterConfig.consumer_secret') || !$formEx->value('TwitterConfig.access_token_secret')): ?>
			<div class="error">
				Twitterアプリケーションとしての登録が完了していないのでこの機能はまだ利用できません。
				<?php if(Configure::read('debug') < 1): ?>
				<br />この機能を有効にするには、<?php $baser->link('システム設定',array('plugin'=>null, 'controller'=>'site_configs', 'action'=>'form')) ?>より、
				「制作・開発モード」をデバッグモードに切り替えると、ここに認証リンクが表示されますのでクリックします。
				<?php endif ?>
			</div>
				<?php if(Configure::read('debug') > 0): ?>
					<br /><?php $baser->link('≫ Twitterアプリ認証',array('action'=>'authorize')) ?>
				<?php endif ?>
			<?php else: ?>
				<?php if($formEx->value('TwitterConfig.tweet_settings_array')): ?>
					<?php foreach($formEx->value('TwitterConfig.tweet_settings_array') as $key => $setting): ?>
						<?php echo $formEx->checkbox('TwitterConfig.tweet_setting_'.$key, array('label'=>$setting['name'])) ?><br />
					<?php endforeach ?>
				<?php endif ?>
			<?php endif ?>
		</td>
	</tr>
</table>
<div class="align-center"> <?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?> </div>