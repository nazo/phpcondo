<!-- BaserPageTagBegin -->
<?php $baser->setTitle('サイトマップ') ?>
<?php $baser->setDescription('BaserCMS inc.のサイトマップページ') ?>
<?php $baser->editPage(4) ?>
<!-- BaserPageTagEnd -->
<h2 class="contents-head">サイトマップ</h2>
<?php $baser->sitemap() ?>
<ul class="section">
	<li><?php $baser->link("新着情報","/news/index") ?></li>
	<li><?php $baser->link("お問い合わせ","/contact/index") ?>	</li>
</ul>
