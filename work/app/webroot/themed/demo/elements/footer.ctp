<?php
/**
 * フッター
 */
?>

<div id="footer">
	<div id="footerInner">
		<?php $baser->element('global_menu') ?>
		<p id="copyright"> Copyright(C)
			<?php $baser->copyYear(2008) ?>
			BaserCMS All rights Reserved. <a href="http://basercms.net/" target="_blank"><?php echo $html->image('baser.power.gif', array('alt'=> 'BaserCMS : Based Website Development Project', 'border'=> "0")); ?></a>&nbsp; <a href="http://cakephp.org/" target="_blank"><?php echo $html->image('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")); ?></a> </p>
	</div>
</div>
