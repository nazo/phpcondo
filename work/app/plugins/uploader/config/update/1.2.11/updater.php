<?php
/* SVN FILE: $Id$ */
/**
 * アップデータープラグイン バージョン 1.2.11 アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /baser/controllers/updaters_controller.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 *
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
 * @package			baser.config.update
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * スキーマの読み込み
 */
	if($this->loadSchema('1.2.11', 'uploader', 'uploader_configs')){
		$this->setMessage('uploader_configs テーブルの作成に成功しました。');
	} else {
		$this->setMessage('uploader_configs テーブルの作成に失敗しました。', true);
	}
	if($this->loadSchema('1.2.11', 'uploader', 'uploader_categories')){
		$this->setMessage('uploader_categories テーブルの作成に成功しました。');
	} else {
		$this->setMessage('uploader_categories テーブルの作成に失敗しました。', true);
	}
/**
 * 初期データ読み込み
 * baserCMS 1.6.10 以下の場合、$this->loadCsv にバグがある為処理を分けた
 */
if(verpoint($this->getBaserVersion()) >= 1006011000) {

	if($this->loadCsv('1.2.11', 'uploader')){
		$this->setMessage('uploader_configs テーブルの初期データ作成に成功しました。');
	} else {
		$this->setMessage('uploader_configs テーブルの初期データ作成に失敗しました。', true);
	}

} else {

	$path = $this->_getUpdatePath('1.2.11', 'uploader');

	if($this->Updater->loadCsv('plugin', $path)) {
		$this->setMessage('uploader_configs テーブルの初期データ作成に成功しました。');
	} else {
		$this->setMessage('uploader_configs テーブルの初期データ作成に失敗しました。', true);
	}

}