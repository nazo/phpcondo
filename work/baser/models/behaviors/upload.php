<?php
/* SVN FILE: $Id$ */
/**
 * ファイルアップロードビヘイビア
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
 * @package			baser.models.behaviors
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ファイルアップロードビヘイビア
 * 
 * @subpackage baser.models.behaviors
 */
class UploadBehavior extends ModelBehavior {
/**
 * 保存ディレクトリ
 * 
 * @var string
 * @access public
 */
	var $savePath = '';
/**
 * 設定
 * 
 * @var array
 * @access public
 */
	var $settings =  null;
/**
 * 一時ID
 * 
 * @var string
 * @access public
 */
	var $tmpId = null;
/**
 * Session
 * 
 * @var Session
 * @access public
 */
	var $Session = null;
/**
 * セットアップ
 * 
 * @param Model	$model
 * @param array	actsAsの設定
 * @return void
 * @access public
 */
	function setup(&$model, $config = array()) {

		$this->settings = Set::merge(array('saveDir'=> '')
				, $config);
		$this->savePath = WWW_ROOT . 'files'.DS.$this->settings['saveDir'] . DS;
		if(!is_dir($this->savePath)) {
			mkdir($this->savePath);
			chmod($this->savePath,0777);
		}
		$this->Session = new SessionComponent();

	}
/**
 * Before save
 * 
 * @param Model $model
 * @param Model $options
 * @return boolean
 * @access public
 */
	function beforeSave(&$model, $options) {
		
		return $this->saveFiles($model);
		
	}
/**
 * After save
 * 
 * @param Model $model
 * @param Model $created
 * @param Model $options
 * @return boolean
 * @access public
 */
	function afterSave(&$model, $created, $options) {
		
		$this->renameToFieldBasename($model);
		$model->data = $model->save($model->data, array('callbacks'=>false,'validate'=>false));
		
	}
/**
 * 一時ファイルとして保存する
 * 
 * @param Model $model
 * @param array $data
 * @param string $tmpId
 * @return boolean
 * @access public
 */
	function saveTmpFiles(&$model,$data,$tmpId) {
		
		$this->Session->del('Upload');
		$model->data = $data;
		$this->tmpId = $tmpId;
		if($this->saveFiles($model)) {
			return $model->data;
		}else {
			return false;
		}
		
	}
/**
 * ファイル群を保存する
 * 
 * @param Model $model
 * @return boolean
 * @access public
 */
	function saveFiles(&$model) {

		$imageExt = array('gif','jpg','png');
		$serverData = $model->findById($model->id);

		foreach($this->settings['fields'] as $key => $field) {

			if(empty($field['name'])) $field['name'] = $key;

			if(!empty($model->data[$model->name][$field['name'].'_delete'])) {
				$file = $serverData[$model->name][$field['name']];
				if(!$this->tmpId) {
					$this->delFile($model,$file,$field);
					$model->data[$model->name][$field['name']] = '';
				}else {
					$model->data[$model->name][$field['name']] = $file;
				}
				continue;
			}

			if(empty($model->data[$model->name][$field['name']]['name']) && !empty($model->data[$model->name][$field['name'].'_'])) {
				// 新しいデータが送信されず、既存データを引き継ぐ場合は、元のフィールド名に戻す
				$model->data[$model->name][$field['name']] = $model->data[$model->name][$field['name'].'_'];
				unset($model->data[$model->name][$field['name'].'_']);
			}elseif(!empty($model->data[$model->name][$field['name'].'_tmp'])) {
				// セッションに一時ファイルが保存されている場合は復元する
				$this->moveFileSessionToTmp($model,$field['name']);
			}elseif(!isset($model->data[$model->name][$field['name']]) ||
					!is_array($model->data[$model->name][$field['name']])) {
				continue;
			}

			if(!empty($model->data[$model->name][$field['name']]) && is_array($model->data[$model->name][$field['name']])) {

				if($model->data[$model->name][$field['name']]['size'] == 0) {
					unset($model->data[$model->name][$field['name']]);
					continue;
				}

				// 拡張子を取得
				$field['ext'] = decodeContent($model->data[$model->name][$field['name']]['type'],$model->data[$model->name][$field['name']]['name']);

				/* タイプ別除外 */
				if($field['type'] == 'image') {
					if(!in_array($field['ext'], $imageExt)) {
						unset($model->data[$model->name][$field['name']]);
						continue;
					}
				}else {
					if(is_array($field['type'])) {
						if(!in_array($field['ext'], $field['type'])) {
							unset($model->data[$model->name][$field['name']]);
							continue;
						}
					}else {
						if($field['type'] != 'all' && $field['type']!=$field['ext']) {
							unset($model->data[$model->name][$field['name']]);
							continue;
						}
					}
				}

				if(empty($model->data[$model->name][$field['name']]['name'])) {

					/* フィールドに値がない場合はスキップ */
					unset($model->data[$model->name][$field['name']]);
					continue;

				}else {

					/* アップロードしたファイルを保存する */
					// ファイル名が重複していた場合は変更する
					$model->data[$model->name][$field['name']]['name'] = $this->getUniqueFileName($model,$field['name'],$model->data[$model->name][$field['name']]['name'],$field);

					// 画像を保存
					$fileName = $this->saveFile($model,$field);
					if($fileName) {

						if(!$this->tmpId && ($field['type']=='all' || $field['type']=='image') && !empty($field['imagecopy']) && in_array($field['ext'],$imageExt)) {

							/* 画像をコピーする */
							foreach($field['imagecopy'] as $copy) {
								// コピー画像が元画像より大きい場合はスキップして作成しない
								$size = $this->getImageSize($this->savePath . $fileName);
								if($size && $size['width'] < $copy['width'] && $size['height'] < $copy['height']) {
									if(isset($copy['smallskip']) && $copy['smallskip']===false) {
										$copy['width'] = $size['width'];
										$copy['height'] = $copy['height'];
									}else {
										continue;
									}
								}
								$copy['name'] = $field['name'];
								$copy['ext'] = $field['ext'];
								$ret = $this->copyImage($model,$copy);
								if(!$ret) {
									// 失敗したら処理を中断してfalseを返す
									return false;
								}
							}

						}

						// 一時ファイルを削除
						@unlink($model->data[$model->name][$field['name']]['tmp_name']);
						// フィールドの値をファイル名に更新
						if(!$this->tmpId) {
							$model->data[$model->name][$field['name']] = $fileName;
						}else {
							$model->data[$model->name][$field['name']]['session_key'] = $fileName;
						}
					}else {
						// 失敗したら処理を中断してfalseを返す
						return false;
					}
				}
			}
		}

		return true;

	}
/**
 * セッションに保存されたファイルデータをファイルとして保存する
 * 
 * @param Model $model
 * @param string $fieldName
 * @return void
 * @access public
 */
	function moveFileSessionToTmp(&$model,$fieldName) {

		$sessionKey = $model->data[$model->alias][$fieldName.'_tmp'];
		$tmpName = $this->savePath.$sessionKey;
		$fileData = $this->Session->read('Upload.'.$sessionKey);
		$fileType = $this->Session->read('Upload.'.$sessionKey.'_type');
		$this->Session->del('Upload.'.$sessionKey);
		$this->Session->del('Upload.'.$sessionKey.'_type');

		// サイズを取得
		if (ini_get('mbstring.func_overload') & 2 && function_exists('mb_strlen')) {
			$fileSize = mb_strlen($fileData, 'ASCII');
		} else {
			$fileSize = strlen($fileData);
		}

		if($fileSize == 0) {
			return false;
		}

		// ファイルを一時ファイルとして保存
		$file = new File($tmpName,true,0666);
		$file->write($fileData);
		$file->close();

		// 元の名前を取得
		$pos = strpos($sessionKey,'_');
		$fileName = substr($sessionKey,$pos+1,strlen($sessionKey));

		// アップロードされたデータとしてデータを復元する
		$uploadInfo['error'] = 0;
		$uploadInfo['name'] = $fileName;
		$uploadInfo['tmp_name'] = $tmpName;
		$uploadInfo['size'] = $fileSize;
		$uploadInfo['type'] = $fileType;
		$model->data[$model->alias][$fieldName] = $uploadInfo;
		unset($model->data[$model->alias][$fieldName.'_tmp']);

	}
/**
 * ファイルを保存する
 * 
 * @param Model $model
 * @param array 画像保存対象フィールドの設定
 * @return ファイル名 Or false
 * @access public
 */
	function saveFile(&$model,$field) {

		// データを取得
		$file = $model->data[$model->name][$field['name']];

		if (empty($file['tmp_name'])) return false;
		if (!empty($file['error']) && $file['error']!=0) return false;

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($field['prefix'])) $prefix = $field['prefix'];
		if(!empty($field['suffix'])) $suffix = $field['suffix'];

		// 保存ファイル名を生成
		$basename = preg_replace("/\.".$field['ext']."$/is",'',$file['name']);

		if(!$this->tmpId) {
			$fileName = $prefix . $basename . $suffix . '.'.$field['ext'];
		}else {
			if(!empty($field['namefield'])) {
				$model->data[$model->alias][$field['namefield']] = $this->tmpId;
				$fileName = $this->getFieldBasename($model, $field, $field['ext']);
			} else {
				$fileName = $this->tmpId.'_'.$field['name'].'.'.$field['ext'];
			}
		}
		$filePath = $this->savePath . $fileName;

		if(!$this->tmpId) {

			if(copy($file['tmp_name'], $filePath)) {

				chmod($filePath,0666);
				// ファイルをリサイズ
				if(!empty($field['imageresize']) && ($field['ext']=='jpg' || $field['ext']=='gif' || $field['ext']=='png')) {
					if(!empty($field['imageresize']['thumb'])) {
						$thumb = $field['imageresize']['thumb'];
					}else {
						$thumb = false;
					}
					$this->resizeImage($filePath,$filePath,$field['imageresize']['width'],$field['imageresize']['height'],$thumb);
				}
				$ret = $fileName;

			}else {
				$ret =  false;
			}

		}else {
			$_fileName = str_replace('.','_',$fileName);
			$this->Session->write('Upload.'.$_fileName, $field);
			$this->Session->write('Upload.'.$_fileName.'.type', $file['type']);
			$this->Session->write('Upload.'.$_fileName.'.data', file_get_contents($file['tmp_name']));
			return $fileName;
		}

		return $ret;

	}
/**
 * 画像をコピーする
 * 
 * @param Model $model
 * @param array 画像保存対象フィールドの設定
 * @return boolean
 * @access public
 */
	function copyImage(&$model,$field) {

		// データを取得
		$file = $model->data[$model->name][$field['name']];

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($field['prefix'])) $prefix = $field['prefix'];
		if(!empty($field['suffix'])) $suffix = $field['suffix'];

		// 保存ファイル名を生成
		$basename = preg_replace("/\.".$field['ext']."$/is",'',$file['name']);
		$fileName = $prefix . $basename . $suffix . '.'.$field['ext'];
		$filePath = $this->savePath . $fileName;

		if(!empty($field['thumb'])) {
			$thumb = $field['thumb'];
		}else {
			$thumb = false;
		}

		return $this->resizeImage($model->data[$model->name][$field['name']]['tmp_name'],$filePath,$field['width'],$field['height'], $thumb);

	}
/**
 * 画像ファイルをコピーする
 * リサイズ可能
 * 
 * @param Model	$model
 * @param string コピー元のパス
 * @param string コピー先のパス
 * @param int 横幅
 * @param int 高さ
 * @return boolean
 * @access public
 */
	function resizeImage($source,$distination,$width=0,$height=0,$thumb = false) {

		if($width>0 || $height>0) {
			App::import('Vendor','Imageresizer');
			$imageresizer = new Imageresizer(APP.'tmp');
			$ret = $imageresizer->resize($source,$distination,$width,$height, $thumb);
		}else {
			$ret = copy($source,$distination);
		}

		if($ret) {
			chmod($distination,0666);
		}

		return $ret;

	}
/**
 * 画像のサイズを取得
 *
 * @param string $path
 * @return mixed array / false
 * @access public
 */
	function getImageSize($path) {
		
		$imginfo = getimagesize($path);
		if($imginfo) {
			return array('width' => $imginfo[0], 'height' => $imginfo[1]);
		}
		return false;
		
	}
/**
 * After delete
 * 画像ファイルの削除を行う
 * 削除に失敗してもデータの削除は行う
 * 
 * @param Model $model
 * @return void
 * @access public
 */
	function beforeDelete(&$model) {

		$model->data = $model->findById($model->id);
		$this->delFiles($model);

	}
/**
 * 画像ファイル群を削除する
 * 
 * @param Model $model
 * @return boolean
 * @access public
 */
	function delFiles(&$model,$fieldName = null) {

		foreach($this->settings['fields'] as $key => $field) {
			if(empty($field['name'])) $field['name'] = $key;
			$file = $model->data[$model->name][$field['name']];
			$ret = $this->delFile($model,$file,$field);
		}

	}
/**
 * ファイルを削除する
 * 
 * @param Model $model
 * @param array 保存対象フィールドの設定
 * @return boolean
 * @access public
 */
	function delFile(&$model,$file,$field,$delImagecopy=true) {

		if(!$file) {
			return true;
		}

		if(empty($field['ext'])) {
			$pathinfo = pathinfo($file);
			$field['ext'] = $pathinfo['extension'];
		}

		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($field['prefix'])) $prefix = $field['prefix'];
		if(!empty($field['suffix'])) $suffix = $field['suffix'];

		// 保存ファイル名を生成
		$basename = preg_replace("/\.".$field['ext']."$/is",'',$file);
		$fileName = $prefix . $basename . $suffix . '.'.$field['ext'];
		$filePath = $this->savePath . $fileName;

		if(!empty($field['imagecopy']) && $delImagecopy) {
			foreach($field['imagecopy'] as $copy) {
				$copy['name'] = $field['name'];
				$copy['ext'] = $field['ext'];
				$this->delFile($model,$file,$copy,false);
			}
		}

		if(file_exists($filePath)) {
			return unlink($filePath);
		}

		return true;

	}
/**
 * ファイル名をフィールド値ベースのファイル名に変更する

 * @param Model $model
 * @return boolean
 * @access public
 */
	function renameToFieldBasename(&$model) {

		foreach($this->settings['fields'] as $key => $setting) {

			if(empty($setting['name'])) $setting['name'] = $key;

			if(!empty($setting['namefield']) && !empty($model->data[$model->alias][$setting['name']])) {

				$oldName = $model->data[$model->alias][$setting['name']];

				if(file_exists($this->savePath.$oldName)) {

					$pathinfo = pathinfo($oldName);
					$newName = $this->getFieldBasename($model,$setting,$pathinfo['extension']);
					if(!$newName) {
						return true;
					}
					if($oldName != $newName) {

						rename($this->savePath.$oldName,$this->savePath.$newName);
						$model->data[$model->alias][$setting['name']] = $newName;

						if(!empty($setting['imagecopy'])) {
							foreach($setting['imagecopy'] as $copysetting) {
								$oldCopyname = $this->getFileName($model,$copysetting,$oldName);
								if(file_exists($this->savePath.$oldCopyname)) {
									$newCopyname = $this->getFileName($model,$copysetting,$newName);
									rename($this->savePath.$oldCopyname,$this->savePath.$newCopyname);
								}
							}
						}
					}
				}else {
					$model->data[$model->alias][$setting['name']] = '';
				}
			}
		}
		return true;
		
	}
/**
 * フィールドベースのファイル名を取得する
 *
 * @param Model $model
 * @param array $setting
 * @param string $ext
 * @return mixed false / string
 * @access public
 */
	function getFieldBasename(&$model,$setting,$ext) {

		if(empty($setting['namefield'])) {
			return false;
		}
		$data = $model->data[$model->alias];
		if(!isset($data[$setting['namefield']])){
			if($setting['namefield'] == 'id' && $model->id) {
			$basename = $model->id;
			} else {
				return false;
			}
		} else {
			$basename = $data[$setting['namefield']];
		}

		if(!empty($setting['nameformat'])) {
			$basename = sprintf($setting['nameformat'],$basename);
		}
		return $basename . '_' . $setting['name'] . '.' . $ext;

	}
/**
 * ベースファイル名からプレフィックス付のファイル名を取得する
 * 
 * @param Model $model
 * @param array $setting
 * @param string $filename
 * @return string
 * @access public
 */
	function getFileName(&$model,$setting,$filename) {

		$pathinfo = pathinfo($filename);
		$ext = $pathinfo['extension'];
		// プレフィックス、サフィックスを取得
		$prefix = '';
		$suffix = '';
		if(!empty($setting['prefix'])) $prefix = $setting['prefix'];
		if(!empty($setting['suffix'])) $suffix = $setting['suffix'];

		$basename = preg_replace("/\.".$ext."$/is",'',$filename);
		return $prefix . $basename . $suffix . '.' . $ext;

	}
/**
 * ファイル名からベースファイル名を取得する
 * 
 * @param Model $model
 * @param array $setting
 * @param string $filename
 * @return string
 * @access public
 */
	function getBasename(&$model,$setting,$filename) {
		
		$pattern = "/^".$prefix."(.*?)".$suffix."\.[a-zA-Z0-9]*$/is";
		if(preg_match($pattern, $filename,$maches)) {
			return $maches[1];
		}else {
			return '';
		}
		
	}
/**
 * 一意のファイル名を取得する
 * 
 * @param string $fieldName
 * @param string $fileName
 * @return string
 * @access public
 */
	function getUniqueFileName(&$model, $fieldName, $fileName, $setting = null) {

		$pathinfo = pathinfo($fileName);
		$ext = $pathinfo['extension'];

		$basename = preg_replace("/\.".$ext."$/is",'',$fileName);

		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions[$model->name.'.'.$fieldName.' LIKE'] = $basename.'%'.$ext;
		if(!empty($model->data[$model->name]['id'])) {
			$conditions[$model->name.'.id <>'] = $model->data[$model->name]['id'];
		}
		$datas = $model->findAll($conditions,$fieldName);

		if($datas) {
			$prefixNo = 1;
			foreach($datas as $data) {
				$_basename = preg_replace("/\.".$ext."$/is",'',$data[$model->name][$fieldName]);
				$lastPrefix = str_replace($basename,'',$_basename);
				if(preg_match("/^__([0-9]+)$/s",$lastPrefix,$matches)) {
					$no = (int)$matches[1];
					if($no > $prefixNo) $prefixNo = $no;
				}

			}
			return $basename.'__'.($prefixNo+1).'.'.$ext;

		}else {
			return $fileName;
		}

	}
	
}
?>