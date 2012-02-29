<?php
/**
 * ヘッダー
 */
?>

<div id="header">
	<div id="headMain" class="clearfix">
		<h1><?php $baser->link($baser->siteConfig['name'],'/') ?></h1>
		<?php $baser->element('search') ?>
	</div>
	<?php if($baser->isTop()): ?>
<div id="topMain">
	<div id="about">
		<p>2012年4月21日(土)</p>
		<p><strong>PHPカンファレンス北海道2012</strong></p>
		<p>札幌市産業振興センター　セミナールームA</p>
	</div>
	<p class="discription">ついに、あのPHPカンファレンスが北海道でも開催されます。道内外から様々な技術者の方々の講演を予定しています。皆様の参加をお待ちしております！</p>
</div>
	<?php endif ?>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php $baser->element('global_menu') ?>
	</div>
	<?php if(!$baser->isTop()): ?>
	<!-- navigation -->
	<div id="navigation">
		<?php $baser->element('navi',array('title_for_element'=>$baser->getContentsTitle())); ?>
	</div>
	<?php endif ?>
</div>
