<?php
/* SVN FILE: $Id$ */
/**
 * ファイルアップローダー設定
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
 * @package			uploader.config
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$title = 'アップローダー';
$description = 'Webページやブログ記事で、画像等のファイルを貼り付ける事ができます。';
$author = 'ryuring';
$url = 'http://www.e-catchup.jp';
$adminLink = '/admin/uploader/uploader_files/index';
if(!is_writable(WWW_ROOT.'files')){
	$viewFilesPath = str_replace(ROOT,'',WWW_ROOT).'files';
	$installMessage = '登録ボタンをクリックする前に、サーバー上の '.$viewFilesPath.' に書き込み権限を与えてください。';
}
?>