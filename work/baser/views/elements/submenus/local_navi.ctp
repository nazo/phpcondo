<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ページカテゴリリスト
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
$pageCategory = $page->getCategory();
?>
<?php if($pageCategory): ?>
<div id="local-navi">
	<h2><?php echo $pageCategory['title'] ?></h2>
	<?php $baser->element('page_list',array('categoryId'=>$pageCategory['id'])) ?>
</div>
<?php endif ?>