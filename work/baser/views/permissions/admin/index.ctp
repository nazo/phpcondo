<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] アクセス制限設定一覧
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
$baser->js('sorttable',false);
?>

<script type="text/javascript">
$(function(){
	$("#PermissionsSearchBody").show();
});
</script>

<?php echo $formEx->create('Sort', array(
	'action' => 'update_sort',
	'url' => am(array('controller'=>'permissions'), $this->passedArgs))) ?>
<?php echo $formEx->input('Sort.id', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Sort.offset', array('type' => 'hidden')) ?>
<?php echo $formEx->end() ?>

<div id="pageMessage" class="message" style="display:none"></div>

<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?>
	<?php $baser->img('ajax-loader-s.gif', array('id' => 'ListAjaxLoader')) ?>
</h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>サイト運営者には必要最低限のメニューしか表示しないなど、ユーザーグループごとのアクセス制限をかける事でシンプルなインターフェイスを実現する事ができます。<br />
		画面下の「新規登録」ボタンより新しいルールを追加します。</p>
	<ul>
		<li>ルールを何も追加しない状態では、全てのユーザーが全てのコンテンツにアクセスできるようになっています。</li>
		<li>複数のルールを追加した場合は、上から順に設定が上書きされ、下にいくほど優先されます。</li>
		<li>URL設定ではワイルドカード（*）を利用して一定のURL階層内のコンテンツに対し一度に設定を行う事ができます。</li>
		<li>管理者グループ「admins」には、アクセス制限の設定はできません。</li>
		<li>画面一番下の「並び替えモード」をクリックすると、表示される<?php $baser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして行の並び替えができます。</li>
	</ul>
	<div class="example-box">
		<div class="head">（例）ページ管理全体は許可しないが、特定のページ「NO: ２」のみ許可を与える場合</div>
		<ol>
			<li>1つ目のルールとして、　/admin/pages*　を「不可」として追加します。</li>
			<li>2つ目のルールとして、　/admin/pages/edit/2　を「可」として追加します。</li>
		</ol>
	</div>
</div>

<!-- search -->
<h3><a href="javascript:void(0);" class="slide-trigger" id="PermissionsSearch">検索</a></h3>
<div class="function-box corner10" id="PermissionsSearchBody">
	<?php echo $formEx->create('Permission', array('url' => array('action' => 'index'), 'type' => 'get')) ?>
	<p><small>ユーザーグループ</small>
		<?php echo $formEx->input('Permission.user_group_id', array(
			'type'		=> 'select', 
			'options'	=> $formEx->getControlSource('user_group_id'))) ?>
		<?php echo $formEx->submit('検　索', array('div' => false, 'class' => 'btn-orange button')) ?>
		<?php echo $formEx->end() ?></p>
</div>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="PermissionsTable">
	<tr>
		<th>操作</th>
		<th>NO</th>
		<th>ルール名<br />URL設定</th>
		<th>アクセス</th>
		<th>登録日<br />更新日</th>
	</tr>
<?php if(!empty($listDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($listDatas as $listData): ?>
		<?php if (!$listData['Permission']['status']): ?>
			<?php $class=' class="disablerow sortable"'; ?>
		<?php elseif ($count%2 === 0): ?>
			<?php $class=' class="altrow sortable"'; ?>
		<?php else: ?>
			<?php $class=' class="sortable"'; ?>
		<?php endif; ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td style="width:15%" class="operation-button">
		<?php if($sortmode): ?>
			<span class="sort-handle"><?php $baser->img('sort.png', array('alt' => '並び替え')) ?></span>
			<?php echo $formEx->input('Sort.id' . $listData['Permission']['id'], array(
					'type'	=> 'hidden',
					'class'	=> 'id',
					'value'=>$listData['Permission']['id'])) ?>
		<?php endif ?>
			<?php $baser->link('編集', array('action' => 'edit', $listData['Permission']['id']), array('class' => 'btn-orange-s button-s'), null, false) ?>
		<?php if($listData['Permission']['name']!='admins'): ?>
			<?php $baser->link('削除', 
					array('action' => 'delete', $listData['Permission']['id']),
					array('class' => 'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？', $listData['Permission']['name']),
					false); ?>
		<?php endif ?>
		</td>
		<td style="width:10%"><?php echo $listData['Permission']['no']; ?></td>
		<td style="width:55%">
			<?php $baser->link($listData['Permission']['name'], array('action' => 'edit', $listData['Permission']['id'])); ?><br />
			<?php echo $listData['Permission']['url']; ?>
		</td>
		<td style="width:10%" class="align-center"><?php echo $textEx->arrayValue($listData['Permission']['auth'], array(0 => '×', 1 => '〇')) ?></td>
		<td style="width:10%">
			<?php echo $timeEx->format('y-m-d', $listData['Permission']['created']); ?><br />
			<?php echo $timeEx->format('y-m-d', $listData['Permission']['modified']); ?>
		</td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
<?php endif; ?>
</table>

<div class="align-center">
	<?php $baser->link('新規登録', array('action' => 'add'), array('class' => 'btn-red button')) ?>
<?php if(!$sortmode): ?>
	<?php $baser->link('並び替えモード', array('sortmode' => 1), array('class' => 'btn-orange button')) ?>
<?php else: ?>
	<?php $baser->link('ノーマルモード', array('sortmode' => 0), array('class' => 'btn-orange button')) ?>
<?php endif ?>
</div>
