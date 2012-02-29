<?php
/* SVN FILE: $Id$ */
/**
 * ブログコメントモデル
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
 * @package			baser.plugins.blog.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ブログコメントモデル
 *
 * @package baser.plugins.blog.models
 */
class BlogComment extends BlogAppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogComment';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Cache');
/**
 * belongsTo
 *
 * @var array
 * @access public
 */
	var $belongsTo = array('BlogPost' =>    array(  'className'=>'Blog.BlogPost',
							'foreignKey'=>'blog_post_id'));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'お名前を入力してください。'),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'お名前は50文字以内で入力してください。')
		),
		'email' => array(
			'email' => array(
				'rule'		=> array('email'),
				'message'	=> 'Eメールの形式が不正です。',
				'allowEmpty'=> true),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'Eメールは255文字以内で入力してください。')
		),
		'url' => array(
			'url' => array(
				'rule'		=> array('url'),
				'message'	=> 'URLの形式が不正です。',
				'allowEmpty'=> true),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'URLは255文字以内で入力してください。')
		),
		'message' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "コメントを入力してください。")
		)
	);
/**
 * 初期値を取得する
 *
 * @return array 初期値データ
 * @access public
 */
	function getDefaultValue() {
		$data[$this->name]['name'] = 'NO NAME';
		return $data;
	}
/**
 * コメントを追加する
 * @param array $data
 * @param string $contentId
 * @param string $postId
 * @param string $commentApprove
 * @return boolean
 */
	function add($data,$contentId,$postId,$commentApprove) {

		if(isset($data['BlogComment'])) {
			$data = $data['BlogComment'];
		}

		// サニタイズ
		$data = Sanitize::html($data);
		$data['url'] = str_replace('&#45;','-',$data['url']);
		$data['email'] = str_replace('&#45;','-',$data['email']);
		$data['blog_post_id'] = $postId;
		$data['blog_content_id'] = $contentId;
		if($commentApprove) {
			$data['status'] = false;
		}else {
			$data['status'] = true;
		}

		$data['no'] = $this->getMax('no',array('blog_content_id'=>$contentId))+1;
		$this->create($data);
		return $this->save();

	}
	
}
?>