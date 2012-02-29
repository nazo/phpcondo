<?php
/**
 * Facebook LikeBoxプラグイン バージョン 1.1.0 アップデートスクリプト
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
 * @copyright		Copyright 2011, materializing.
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			fb_likebox.config.update
 * @since			Baser v 1.6.11.2
 * @version			1.1.0
 * @license			GPL
 */
 /**
 * ----------------------------------------
 * 　CSVファイルを読み込む
 * ----------------------------------------
 * $this->loadCsv($version, $plugin = '', $filterTable = '');
 * $version			アップデート対象のバージョン番号を指定します。（例）'1.6.7'
 * $plugin			プラグイン内のCSVを読み込むにはプラグイン名を指定します。（例）'mail'
 * $filterTable		指定したテーブルのみCSVを読み込む場合は、プレフィックスを除外したテーブル名を指定します。（例）'permissions'
 *					指定しない場合は全てのテーブルが対象になります。
 */
	if($this->loadCsv('1.1.0', 'fb_likebox')){

		// Facebook LikeBoxプラグインのコンテンツ更新
		App::import('Model', 'FbLikebox.FbLikeboxConfig');
		$FbLikeboxConfig = new FbLikeboxConfig();
		$datas = $FbLikeboxConfig->find('all');
		$result = true;

		// 追加フィールドを定義
		$_datas = array (
			array(
				'FbLikeboxConfig' => array (
					'name' => 'border_color',
					'value' => '',
				)
			),
			array(
				'FbLikeboxConfig' => array (
					'name' => 'language',
					'value' => 1,
				)
			)
		);

		$datas = array_merge($datas, $_datas);

		foreach($datas as $data) {
			$FbLikeboxConfig->create();

			if($data['FbLikeboxConfig']['name'] == 'show_faces' || $data['FbLikeboxConfig']['name'] == 'stream' || $data['FbLikeboxConfig']['name'] == 'header') {
				// 返ってくる値をチェックして、空文字、FALSE、NULLの場合は0を入れる
				$data['FbLikeboxConfig']['value'] = $FbLikeboxConfig->checkEmpty($data['FbLikeboxConfig']['value']);
			}

			if($FbLikeboxConfig->save($data)) {
				continue;
			} else {
				$result = false;
				break;
			}

		}

		if($result){
			$this->setMessage('fb_likebox_configs テーブルのデータ更新に成功しました。');
		} else {
			$this->setMessage('fb_likebox_configs テーブルのデータ更新に失敗しました。', true);
		}
	}
?>