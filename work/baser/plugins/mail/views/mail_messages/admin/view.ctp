<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 受信メール詳細
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

<!-- title -->
<h2><?php $baser->contentsTitle() ?></h2>

<!-- view -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr><th>NO</th><td><?php echo $message['Message']['id'] ?></td></tr>
	<tr><th>受信日時</th><td><?php echo $timeEx->format('Y/m/d H:i:s', $message['Message']['created']) ?></td></tr>
<?php 
$groupField = null;
foreach($mailFields as $key => $mailField) {
	$field = $mailField['MailField'];
	if($field['use_field'] && $field['type'] != 'hidden') {
		$nextKey = $key + 1;
		/* 項目名 */
		if ($groupField != $field['group_field']  || (!$groupField && !$field['group_field'])) {
			echo '<tr>';
			echo '<th class="col-head" width="160">'.$field['head'].'</th>';
			echo '<td class="col-input">';
		}
		if(!empty($message['Message'][$mailField['MailField']['field_name']])) {
			echo $field['before_attachment'];
		}
		if (!$field['no_send']) {
			echo $textEx->autoLink(nl2br($maildata->control(
				$mailField['MailField']['type'],
				$message['Message'][$mailField['MailField']['field_name']],
				$mailfield->getOptions($mailField['MailField'])
			)));
		}
		if(!empty($message['Message'][$mailField['MailField']['field_name']])) {
			echo $field['after_attachment'];
		}
		echo '&nbsp;';
		if (($array->last($mailFields, $key)) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field']) ||
				(!$field['group_field'] && !$mailFields[$nextKey]['MailField']['group_field']) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field'] && $array->first($mailFields,$key))) {
			echo '</td></tr>';
		}
		$groupField=$field['group_field'];
	}
}
?>
</table>

<!-- button -->
<p class="align-center">
	<?php $baser->link('削除',
					array('action'=>'delete', $mailContent['MailContent']['id'], $message['Message']['id']),
					array('class'=>'btn-gray button'),
					sprintf('受信メール NO「%s」を削除してもいいですか？', $message['Message']['id']), false) ?>
</p>
