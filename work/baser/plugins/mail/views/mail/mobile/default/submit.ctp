<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] メールフォーム送信完了ページ
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
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">
	<?php $baser->contentsTitle() ?>
	</span> </div>
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
<br />
<?php if(Configure::read('debug')>0): ?>
<?php $this->addScript($html->meta(array('http-equiv'=>'Refresh'),null,array('content'=>'5;url='.$mailContent['MailContent']['redirect_url']))); ?>
<?php endif; ?>
メール送信完了<br />
<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
お問い合わせ頂きありがとうございました。<br />
確認次第、ご連絡させて頂きます。<br />
