<?php
/* SVN FILE: $Id$ */
/**
 * メールフォームヘルパー
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
 * @package			baser.plugins.mail.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Helper', 'Freeze');
/**
 * メールフォームヘルパー
 *
 * @package baser.plugins.mail.views.helpers
 *
 */
class MailformHelper extends FreezeHelper {
/**
 * メールフィールドのデータよりコントロールを生成する
 *
 * @param string $type コントロールタイプ
 * @param string $fieldName フィールド文字列
 * @param array $options コントロールソース
 * @param array $attributes html属性
 * @return string htmlタグ
 * @access public
 */
	function control($type,$fieldName,$options, $attributes = array()) {

		$attributes['escape'] = false;

		switch($type) {

			case 'text':
			case 'email':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->text($fieldName,$attributes);
				break;

			case 'radio':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['legend'] = false;
				$attributes['div'] = true;
				if(!empty($attributes['separator'])) {
					$attributes['separator'] = $attributes['separator'];
				}else {
					$attributes['separator'] = "&nbsp;&nbsp;";
				}
				$out = $this->radio($fieldName, $options, $attributes);
				break;

			case 'select':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				if(isset($attributes['empty'])) {
					$showEmpty = $attributes['empty'];
				}else {
					$showEmpty = true;
				}
				$out = $this->select($fieldName, $options, null, $attributes, $showEmpty);
				break;

			case 'pref':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['empty']);
				$out = $this->prefTag($fieldName, null, $attributes);
				break;

			case 'autozip':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$address1 = $this->__name(array(),$options[1]);
				$address2 = $this->__name(array(),$options[2]);
				$attributes['onKeyUp'] = "AjaxZip3.zip2addr(this,'','{$address1['name']}','{$address2['name']}')";
				$out = $this->Javascript->link('ajaxzip3.js').$this->text($fieldName,$attributes);
				break;

			case 'check':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['empty']);
				$out = $this->checkbox($fieldName, $attributes);
				break;

			case 'multi_check':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				if($this->freezed) {
					unset($attributes['separator']);
				}
				$attributes['multiple'] = 'checkbox';
				$out = $this->select($fieldName,$options,null,$attributes,false);
				break;

			case 'date_time_calender':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);;
				$out = $this->datepicker($fieldName, $attributes);
				break;

			case 'date_time_wareki':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['monthNames'] = false;
				$attributes['separator'] = '&nbsp;';
				if(isset($attributes['minYear']) && $attributes['minYear'] == 'today') {
					$attributes['minYear'] = intval(date('Y'));
				}
				if(isset($attributes['maxYear']) && $attributes['maxYear'] == 'today') {
					$attributes['maxYear'] = intval(date('Y'));
				}
				$out = $this->dateTime($fieldName,'WMD',null,null,$attributes);
				break;

			case 'textarea':
				$attributes['cols'] = $attributes['size'];
				unset($attributes['separator']);
				unset($attributes['empty']);
				unset($attributes['size']);
				$out = $this->textarea($fieldName, $attributes);
				break;
			case 'hidden':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->hidden($fieldName, $attributes);
		}
		return $out;

	}
	
}
?>