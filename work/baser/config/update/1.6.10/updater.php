<?php
/* SVN FILE: $Id$ */
/**
 * バージョン アップデートスクリプト
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
 * page_categories スキーマの読み込み
 *
 * contentns_navi フィールド追加
 */
	if(!$this->loadSchema('1.6.10', '', 'page_categories')){
		$this->setMessage('page_categories のテーブル構造の更新に失敗しました。', true);
	} else {
		$this->setMessage('page_categories のテーブル構造の更新に成功しました。');
	}
/**
 * モバイル設定を更新
 */
	$result = $this->writeInstallSetting('Baser.mobile', 'true');
	if($result) {
		$this->setMessage('モバイル設定の更新に成功しました。');
	} else {
		$this->setMessage('モバイル設定の更新に失敗しました。', true);
	}