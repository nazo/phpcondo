<?php
/**
 * サイドバー
 */
?>
<div id="beta">

<div id="banner">
<p><a href="http://sakura.ad.jp/" target="_blank"><?php echo $baser->getImg("banner/sakurainternet.png", array("alt" => "さくらインターネット株式会社")); ?></a></p>
<p><a href="http://crocos.co.jp" target="_blank"><?php echo $baser->getImg("banner/crocos.png", array("alt" => "株式会社クロコス")); ?></a></p>
<p><a href="http://www.infiniteloop.co.jp" target="_blank"><?php echo $baser->getImg("banner/infiniteloop.png", array("alt" => "株式会社インフィニットループ")); ?></a></p>
<p><a href="http://www.phpexam.jp" target="_blank"><?php echo $baser->getImg("banner/phpexam.png", array("alt" => "PHP技術者認定機構")); ?></a></p>
<p><a href="http://codehead.jp" target="_blank"><?php echo $baser->getImg("banner/codehead.gif", array("alt" => "株式会社コードヘッド")); ?></a></p>
<p><a href="http://www.crowdworks.co.jp" target="_blank"><?php echo $baser->getImg("banner/crowdworks.jpg", array("alt" => "株式会社クラウドワークス")); ?></a></p>
<p><a href="http://garage-labs.jp" target="_blank"><?php echo $baser->getImg("banner/garagelabs.gif", array("alt" => "Garagelabs")); ?></a></p>
<p><a href="http://staff.mynavi.jp/hokkaido/" target="_blank"><?php echo $baser->getImg("banner/mynaviagent.jpg", array("alt" => "株式会社マイナビエージェント")); ?></a></p>
<p><a href="http://alleyoop.jp" target="_blank"><?php echo $baser->getImg("banner/alleyoop.gif", array("alt" => "有限会社アリウープ")); ?></a></p>
</div>
	<?php if(!empty($widgetArea)): ?>
	<?php $baser->element('widget_area',array('no'=>$widgetArea)) ?>
	<?php endif ?>
</div>