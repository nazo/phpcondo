<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $baser->xmlHeader() ?>
<?php $baser->docType() ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<?php $baser->charset() ?>
<?php $baser->title() ?>
<?php $baser->metaDescription() ?>
<?php $baser->metaKeywords() ?>
<?php $baser->icon() ?>
<?php $baser->rss('ニュースリリース RSS 2.0', '/news/index.rss') ?>
<?php $baser->css(array('import')) ?>
<?php $baser->js(array(
	'jquery-1.6.4.min',
	'jquery.corner',
	'startup'
)) ?>
<?php $baser->scripts() ?>
<?php $baser->element('google_analytics') ?>
</head>
<body id="<?php $baser->contentsName() ?>">

<!-- begin page -->
<div id="page">

	<!-- begin header -->
	<?php $baser->header() ?>
	<!-- end header -->
	
	<!-- begin contents -->
	<div id="contents" class="clearfix">
	
		<!-- begin alfa -->
		<div id="alfa" >
		
			<!-- begin contentsBody -->
			<div id="contentsBody" class="clearfix">
				<?php $baser->flash() ?>
				<?php $baser->content() ?>
				<?php $baser->element('contents_navi') ?>
			</div>
			<!-- end contentsBody -->
			
		</div>
		<!-- end alfa -->
		
		<?php if(!$baser->isTop()): ?>
		<!-- begin beta -->
		<?php $baser->element('sidebar') ?>
		<!-- end beta -->
		<?php endif ?>
		
		<?php if(!$baser->isTop()): ?>
		<div class="to-top"> <a href="#page">このページの先頭へ戻る</a> </div>
		<?php endif ?>
		
	</div>
	<!-- end contents -->
	
</div>
<!-- end page -->

<!-- begin footer -->
<?php $baser->footer() ?>
<!-- end footer -->

<?php echo $cakeDebug; ?>
</body>
</html>