<?php
$url = $baser->getUri('/'.$blogContent['BlogContent']['name'].'/archives/'.$formEx->value('BlogPost.no'));
$title = $blogContent['BlogContent']['title'];
$comment = $formEx->value('BlogPost.name');
?>
<div id="TwitterStatusSrc" style="display: none">
	[<?php echo $title ?>] <?php echo $comment ?> <?php echo $url ?>
</div>