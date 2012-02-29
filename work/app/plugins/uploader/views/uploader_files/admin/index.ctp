<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ファイル一覧
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
$baser->css('/uploader/css/uploader',null,null,false);
?>
<style type="text/css">
#fileList{
	padding:5px;
}
</style>

<h2><?php $baser->contentsTitle() ?></h2>

<?php $baser->element('index') ?>