<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] missing class
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
?>

<div id="errorPage">
	<h2>クラスが見つかりません</h2>
	<p class="error"> <strong>
		<?php __('Error'); ?>
		: </strong> <?php echo " <em>{$className}</em>"?> クラスが見つかりません。 </p>
	<p class="error"> <strong>
		<?php __('Error'); ?>
		: </strong> <?php echo " <em>{$className}</em>"?> クラスを定義するか、読み込まれているか確認してください。 </p>
	<?php if($notice): ?>
	<p class="notice"> <strong>
		<?php __('Notice'); ?>
		: </strong> <?php echo $notice ?> </p>
	<?php endif ?>
</div>
