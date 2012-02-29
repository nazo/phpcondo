<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] ブログ詳細
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->setTitle($this->pageTitle.'｜'.$blog->getTitle());
$baser->setDescription($blog->getTitle().'｜'.$blog->getPostContent($post,false,false,50));
?>

<!-- title -->
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;"><?php echo $baser->getContentsTitle(); ?></span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />

<!-- detail -->
<?php if(!empty($post)): ?>
<?php $blog->postContent($post) ?>
<br />
<p align="right">
	<?php $blog->category($post) ?>
	<br />
	<?php $blog->postDate($post) ?>
	<br />
	<?php $blog->author($post) ?>
</p>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>

<!-- comments -->
<?php $baser->element('blog_comments') ?>