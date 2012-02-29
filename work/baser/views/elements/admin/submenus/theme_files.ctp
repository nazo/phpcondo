<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマファイル管理メニュー
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
$types = array('layouts'=>'レイアウト','elements'=>'エレメント','etc'=>'コンテンツ','css'=>'スタイルシート','img'=>'イメージ','js'=>'Javascript');
if($theme == 'core'){
	$themeFiles = array(0=>array('name'=>'','title'=>'コア'));
	
	$Plugin = ClassRegistry::init('Plugin');
	$plugins = $Plugin->find('all',array('fields'=>array('name','title')));
	$themeFiles = am($themeFiles,Set::extract('/Plugin/.',$plugins));
}else{
	$themeFiles = array(0=>array('name'=>'','title'=>Inflector::camelize($theme)));
}
?>
<?php foreach($themeFiles as $themeFile): ?>

<div class="side-navi">
	<h2><?php echo $themeFile['title'] ?> テーマ管理メニュー</h2>
	<ul>
		<?php foreach($types as $key => $type): ?>
		<li>
			<?php $baser->link($type.' 一覧',array('action'=>'index',$theme,$themeFile['name'],$key)) ?>
		</li>
		<?php endforeach ?>
	</ul>
</div>
<?php endforeach ?>