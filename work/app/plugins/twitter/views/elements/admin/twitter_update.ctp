<?php $baser->js('/twitter/js/twitter_update') ?>
<?php $baser->css('/twitter/css/twitter') ?>
<?php $baser->element('status/'.$statusTemplate, array('plugin'=>'twitter')) ?>
<div id="TwitterUpdateBox" class="corner5">
	<div class="clearfix">
		<?php echo $formEx->create('Twitter', array('url'=> array('plugin'=>'twitter', 'controller'=>'twitter','action'=>'tinyurl'),'action'=>'tinyurl'), false) ?>
		<span id="TextCounter">0</span>
		<strong>Twitterへ送信</strong>　　<?php echo $formEx->checkbox('Twitter.tinyurl',array('label'=>'URLを短くする')) ?>　
		<?php $baser->img('/img/ajax-loader-s.gif',array('alt'=>'loding...','id'=>'AjaxLoader','class'=>'display-none')) ?>
		<?php echo $formEx->end(null, false) ?>
	</div>
	<?php echo $formEx->create('Twitter', array('url'=> array('plugin'=>'twitter','controller'=>'twitter', 'action'=>'update'), 'action'=>'update'), false) ?>
	<?php echo $formEx->textarea('Twitter.status',array('cols'=>76)) ?>
	<?php echo $formEx->end(array('lable'=>'ツイート','div'=>false,'id'=>'TwitterUpdateSubmit'), false) ?>
	<div id="ResultMessage" style="display:none">&nbsp;</div>
</div>