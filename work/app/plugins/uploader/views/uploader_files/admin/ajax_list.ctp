<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ファイルリスト
 *
 * PHP versions 4 and 5
 *
 * Baser :  Basic Creating Support Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			uploader.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
// IE文字化け対策
header('Content-type: text/html; charset=utf-8');
$users = $formEx->getControlSource("UploaderFile.user_id");
$uploaderCategories = $formEx->getControlSource("UploaderFile.uploader_category_id");
$this->passedArgs['action'] = 'ajax_list';
?>

<div style="text-align: left">
<?php if($uploaderCategories): ?>
	<small style="font-weight:bold">カテゴリ</small>&nbsp;<?php echo $formEx->input('Filter.uploader_category_id', array('type' => 'select', 'options' => $uploaderCategories, 'empty' => '指定なし', 'id' => 'FilterUploaderCategoryId'.$listId, 'class' => 'filter-control')) ?>　
<?php endif ?>
	<small style="font-weight:bold">タイプ</small>&nbsp;<?php echo $formEx->input('Filter.uploader_type', array('type' => 'radio', 'options' => array('all'=>'指定なし', 'img' => '画像', 'etc' => '画像以外'), 'id' => 'FilterUploaderType'.$listId, 'class' => 'filter-control')) ?>
</div>
<?php $baser->pagination('default',array(),null,false) ?>
<div class="file-list-body clearfix">
<?php if ($files): ?>
	<?php foreach ($files as $file): ?>
<span class="selectable-file" id="selectedFile<?php echo $file['UploaderFile']['id'] ?>">
	<?php echo $uploader->file($file,array('width'=>120,'height'=>120,'size'=>'small','alt'=>$file['UploaderFile']['alt'],'style'=>'width:120px;height:120px')) ?>
	<div style="text-align:right">
		<span class="id"><?php echo $file['UploaderFile']['id'] ?></span>.<span class="alt"><?php echo $textEx->mbTruncate($file['UploaderFile']['alt'], 14) ?></span>
	</div>
	<span class="name"><?php echo $file['UploaderFile']['name'] ?></span>
	<div style="text-align:right;margin-top:2px">
		<span class="created"><?php echo $timeEx->format('Y.m.d',$file['UploaderFile']['created']) ?></span>
	</div>
	<span class="modified"><?php echo $timeEx->format('Y.m.d',$file['UploaderFile']['modified']) ?></span>
	<span class="small"><?php echo $file['UploaderFile']['small'] ?></span>
	<span class="midium"><?php echo $file['UploaderFile']['midium'] ?></span>
	<span class="large"><?php echo $file['UploaderFile']['large'] ?></span>
	<span class="url"><?php echo $uploader->getFileUrl($file['UploaderFile']['name']) ?></span>
	<span class="user-id"><?php echo $file['UploaderFile']['user_id'] ?></span>
	<span class="uploader-category-id"><?php echo $file['UploaderFile']['uploader_category_id'] ?></span>
	<span class="user-name"><?php echo $textEx->arrayValue($file['UploaderFile']['user_id'], $users) ?></span>
</span>
	<?php endforeach ?>
<?php else: ?>
<p class="no-data">ファイルが存在しません</p>
<?php endif ?>
</div>