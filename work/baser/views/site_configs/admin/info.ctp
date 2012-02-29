<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] サイト設定 フォーム
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
<style type="text/css">
body {
	background-color: #EDF8C9!important;
}
h1 {
	color:#000;
	text-align: left;
}
td,
th {
	padding:5px;
}
a:link {
	background:none!important;
}
#headMain a:link {
	text-decoration: underline!important;
}
#navigation a:link{
	color:#688A00!important;
}
#glbMenus a:hover{
	text-decoration: none!important;
}
.side-navi a:link,
.to-top a:link{
	text-decoration: underline!important;
	color:#688A00!important;
}
.side-navi a:hover,
.to-top a:hover,
#navigation a:hover,
#loginUser a:hover{
	color:#CC0000!important;
}
body,
td,
th,
h1,
h2 {
	font-family: "ヒラギノ角ゴ Pro W3", "ＭＳ Ｐゴシック", Arial, sans-serif!important;
}
#headMain h1 {
	text-align: right;
	font-size:90%;
	font-family: Arial, Helvetica, sans-serif!important;
}
#contentsBody h2 {
	background:none;
	padding-left:0;
	padding-top:0;
	padding-bottom:0;
	font-size:16px;
	height:auto;
	color:#000000;
}
#contentsBody h2.pageTitle {
	background:url(<?php echo $baser->getUrl('/css/admin/images/bg_main_head.jpg') ?>) no-repeat left top;
	padding-left:25px;
	padding-top:10px;
	padding-bottom:10px;
	font-size:16px;
	height:20px;
	color:#688A00;
}
</style>

<h2 class="pageTitle">
	<?php $baser->contentsTitle() ?>
</h2>

<h3>BaserCMS環境</h3>

<ul style="margin:20px 40px">
	<li>スマートURL： <?php echo $smartUrl ?></li>
	<li>設置フォルダ： <?php echo ROOT.DS ?></li>
	<li>セーフモード：<?php if($safeModeOn): ?>On<?php else: ?>Off<?php endif ?>
	<li>データベース： <?php echo $driver ?></li>
	<li>BaserCMSバージョン： <?php echo $baserVersion ?></li>
	<li>CakePHPバージョン： <?php echo $cakeVersion ?></li>
</ul>

<h3 style="margin-bottom:20px">PHP環境</h3>
<?php phpinfo() ?>