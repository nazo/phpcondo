<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ヘッダー
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
<div id="header">
	<div id="headMain">
		<?php if(isset($baser->siteConfig['name'])): ?>
		<h1>
			<?php $baser->link($baser->siteConfig['name'],'/',array('target'=>'_blank')) ?>
		</h1>
		<?php else: ?>
		<h1><?php echo Configure::read('Baser.title') ?></h1>
		<?php endif ?>
		<p id="fontChanger">フォントサイズ： <a href="#" onclick="setActiveStyleSheet('Large'); return false;">大</a>｜ <a href="#" onclick="setActiveStyleSheet('Medium'); return false;">中</a>｜ <a href="#" onclick="setActiveStyleSheet('Small'); return false;">小</a> </p>
	</div>
	<div id="glbMenus">
		<h2 class="display-none">グローバルメニュー</h2>
		<?php if($useNavi): ?>
		<?php $baser->element('global_menu',array('menuType'=>'admin')) ?>
		<?php endif; ?>
	</div>
</div>
