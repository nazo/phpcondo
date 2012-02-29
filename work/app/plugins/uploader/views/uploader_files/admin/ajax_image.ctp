<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ファイルリスト
 *
 * PHP versions 4 and 5
 *
 * Baser :  Basic Creating Support Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			uploader.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$url = $uploader->getFileUrl($file['UploaderFile']['name']);
?>
<p class="url">
	<a href="<?php echo $url ?>" target="_blank"><?php echo FULL_BASE_URL.$url ?></a>
</p>
<p class="image">
	<a href="<?php echo $url ?>" target="_blank"><?php echo $uploader->file($file, array('size' => $size,'alt' => $file['UploaderFile']['name'])) ?></a>
</p>