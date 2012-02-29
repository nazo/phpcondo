<!-- BaserPageTagBegin -->
<?php $baser->setTitle('') ?>
<?php $baser->setDescription('') ?>
<?php $baser->editPage(6) ?>
<!-- BaserPageTagEnd -->

<div id="news" class="clearfix">
<div class="news" style="margin-right:28px;">
<h2 id="newsHead01">NEWS RELEASE</h2>
<div class="body">
<?php $baser->blogPosts('news', 5) ?>
</div>
</div>
<div class="news">
<h2 id="newsHead02">BaserCMS NEWS</h2>
<div class="body">
<?php $baser->js('/s/feed/ajax/1') ?>
</div>
</div>
</div>