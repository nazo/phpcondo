<?php
/* SVN FILE: $Id$ */
/**
 * [SMARTPHONE] フィード
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
 * @package			baser.plugins.feed.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<cake:nocache>
	<?php $baser->cacheHeader() ?>
</cake:nocache>

<?php if(!empty($items)): ?>
	<?php foreach($items as $key => $item): ?>
		<?php $class = array('clearfix', 'post-'.($key+1)) ?>
		<?php if($array->first($items, $key)): ?>
			<?php $class[] = 'first' ?>
		<?php elseif($array->last($items, $key)): ?>
			<?php $class[] = 'last' ?>
		<?php endif ?>
<li class="<?php echo implode(' ', $class) ?>">
	<a href="<?php echo $item['link']['value']; ?>">
		<span class="date"><?php echo date("Y.m.d",strtotime($item['pubDate']['value'])) ?></span><br />
		<span class="title"><?php echo $item['title']['value']; ?></span>
	</a>
</li>
	<?php endforeach ?>
<?php else: ?>
<p style="text-align:center">ー</p>
<?php endif ?>