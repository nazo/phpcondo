<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ファイル一覧
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
 * @package			uploader.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!isset($listId)) {
	$listId = '';
}
$uploaderCategories = $formEx->getControlSource('UploaderFile.uploader_category_id');
//==============================================================================
// Ajaxで呼び出される事が前提の為インラインで呼びだし
//==============================================================================
$baser->css('/js/jquery.contextMenu-1.0/jquery.contextMenu');
$baser->js(array('jquery.contextMenu-1.0/jquery.contextMenu', 'jquery.upload-1.0.0.min'));
?>
<script type="text/javascript">
var listId = '<?php echo $listId ?>';
/**
 * 起動時処理
 */
	$(function(){

		var allFields = $([]).add($("#name")).add($("#alt"));
		var baseUrl = $("#BaseUrl").html();
		
		// 右クリックメニューをbodyに移動
		$("body").append($("#FileMenu1"));
		$("body").append($("#FileMenu2"));

		// 一覧を更新する
		updateFileList();

		// ファイルアップロードイベントを登録
		$('#UploaderFileFile'+listId).change(uploaderFileFileChangeHandler);

		/* ダイアログを初期化 */
		$("#dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			position: ['center', 20],
			width:420,
			modal: true,
			open: function(){
				var name = $("#fileList"+listId+" .selected .name").html();
				var imgUrl = baseUrl+'admin/uploader/uploader_files/ajax_image/'+name+'/midium';
				$("#UploaderFileId"+listId).val($("#fileList<?php echo $listId ?> .selected .id").html());
				$("#UploaderFileName"+listId).val(name);
				$("#UploaderFileAlt"+listId).val($("#fileList"+listId+" .selected .alt").html());
				$("#UploaderFileUserId"+listId).val($("#fileList"+listId+" .selected .user-id").html());
				$("#UploaderFileUserName"+listId).html($("#fileList"+listId+" .selected .user-name").html());
				if($("#_UploaderFileUploaderCategoryId"+listId).length) {
					$("#_UploaderFileUploaderCategoryId"+listId).val($("#fileList"+listId+" .selected .uploader-category-id").html());
				}

				$.get(imgUrl,function(res){
					$("#UploaderFileImage"+listId).html(res);
				});
			},
			buttons: {
				'キャンセル': function() {
					$(this).dialog('close');
					$("#UploaderFileImage"+listId).html('<img src="'+baseUrl+'img/ajax-loader.gif" />');
				},
				'保存': function() {
					// 保存処理
					var saveButton = $(this);
					// IEでform.serializeを利用した場合、Formタグの中にTableタグがあるとデータが取得できなかった
					var data = {"data[UploaderFile][id]":$("#UploaderFileId"+listId).val(),
						"data[UploaderFile][name]":$("#UploaderFileName"+listId).val(),
						"data[UploaderFile][alt]":$("#UploaderFileAlt"+listId).val(),
						"data[UploaderFile][user_id]":$("#UploaderFileUserId"+listId).val(),
						"data[UploaderFile][uploader_category_id]":$("#_UploaderFileUploaderCategoryId"+listId).val()
					};
					$.post($("#UploaderFileEditForm"+listId).attr('action'), data, function(res){
						if (res) {
							updateFileList();
							allFields.removeClass('ui-state-error');
							saveButton.dialog('close');
							$("#UploaderFileImage"+listId).html('<img src="'+baseUrl+'img/ajax-loader.gif" />');
						} else {
							alert('更新に失敗しました');
						}
					});
				}
			},
			close: function() {
				allFields.val('').removeClass('ui-state-error');
				$("#UploaderFileImage"+listId).html('<img src="'+baseUrl+'img/ajax-loader.gif" />');
			}
			
		});

	});
/**
 * アップロードファイル選択時イベント
 */
	function uploaderFileFileChangeHandler(){
	
		var url = $("#BaseUrl").html()+'admin/uploader/uploader_files/ajax_upload';
		$("#waiting"+listId).show();
		if($('#UploaderFileFile'+listId).val()){
			var data = [];
			if($("#UploaderFileUploaderCategoryId"+listId).length) {
				data = {'data[UploaderFile][uploader_category_id]':$("#UploaderFileUploaderCategoryId"+listId).val()};
			}
			$(this).upload(url, data, uploadSuccessHandler, 'html');
		}
		
	}
/**
 * アップロード完了後イベント
 */
	function uploadSuccessHandler(res){
		
		if(res){
			if($('#UploaderFileUploaderCategoryId'+listId).length) {
				$('#FilterUploaderCategoryId'+listId).val($('#UploaderFileUploaderCategoryId'+listId).val());
			}
			updateFileList();
		}else{
			$('#ErrorMessage').remove();
			$('#fileList'+listId).prepend('<p id="ErrorMessage" class="message">アップロードに失敗しました。ファイルサイズを確認してください。</p>');
			$("#waiting"+listId).hide();
		}
		// フォームを初期化
		// セキュリティ上の関係でvalue値を直接消去する事はできないので、一旦エレメントごと削除し、
		// spanタグ内に新しく作りなおす。
		$("#UploaderFileFile"+listId).remove();
		$("#SpanUploadFile"+listId).append('<input id="UploaderFileFile'+listId+'" type="file" value="" name="data[UploaderFile][file]"/>');
		$('#UploaderFileFile'+listId).change(uploaderFileFileChangeHandler);
		
	}
/**
 * 一覧を更新する
 */
	function updateFileList(){

		$("#waiting"+listId).show();
		$.get(getListUrl(),updateFileListCompleteHander);

	}
/**
 * 選択イベントを初期化する
 */
	function initFileList(){

		var usePermission = $("#UsePermission").html();

		/* 一旦イベントを全て解除 */
		$(".selectable-file").unbind('click.selectEvent');
		$(".selectable-file").unbind('mouseenter.selectEvent');
		$(".selectable-file").unbind('mouseleave.selectEvent');
		$(".page-numbers a").unbind('click.paginationEvent');
		$(".selectable-file").unbind('dblclick.dblclickEvent');
		$(".filter-control").unbind('change.filterEvent');
		
		$(".selectable-file").each(function(){
			if($(this).contextMenu) {
				/* 右クリックメニューを追加 */
				if($(this).find('.user-id').html() == $("#LoginUserId").html() || $("#LoginUserGroupId").html() == 1 || !Number(usePermission)) {
					$(this).contextMenu({menu: 'FileMenu1'}, contextMenuHander);
					$(this).bind('dblclick.dblclickEvent', function(){
						$('#dialog').dialog('open');
					});
				} else {
					$(this).contextMenu({menu: 'FileMenu2'}, contextMenuHander);
					$(this).bind('dblclick.dblclickEvent', function(){
						alert('このファイルの編集・削除はできません。');
					});
				}
			}

			// IEの場合contextmenuを検出できなかったので、mousedownに変更した
			$(this).bind('mousedown', function(){
				$(".selectable-file").removeClass('selected');
				$(this).addClass('selected');
			});

		});

		$(".selectable-file").bind('mouseenter.selectEvent', function(){
			$(this).css('background-color','#FFCC00');
		});
		$(".selectable-file").bind('mouseleave.selectEvent', function(){
			$(this).css('background-color','#FFFFFF');
		});

		/* ページネーションイベントを追加 */
		$('.page-numbers a').bind('click.paginationEvent', function(){
			$("#waiting"+listId).show();
			$.get($(this).attr('href'),updateFileListCompleteHander);
			return false;
		});

		$('#FilterUploaderCategoryId'+listId).bind('change.filterEvent', function() {
			$("#waiting"+listId).show();
			$.get(getListUrl(),updateFileListCompleteHander);
		});
		$('input[name="data[Filter][uploader_type]"]').bind('click.filterEvent', function() {
			$("#waiting"+listId).show();
			$.get(getListUrl(),updateFileListCompleteHander);
		});

		$('.selectable-file').corner("5px");
		$('.corner5').corner("5px");
		$("#fileList"+listId).trigger("filelistload");
		$("#fileList"+listId).effect("highlight",{},1500);

	}
/**
 * ファイルリスト取得完了イベント
 */
	function updateFileListCompleteHander(result) {
		$("#fileList"+listId).html(result);
		initFileList();
		$("#waiting"+listId).hide();
	}
/**
 * Ajax List 取得用のURLを取得する
 */
	function getListUrl() {
		var listUrl = $("#ListUrl"+listId).attr('href');
		if($('#FilterUploaderCategoryId'+listId).length) {
			listUrl += '/uploader_category_id:'+$('#FilterUploaderCategoryId'+listId).val();
		}
		if($('input[name="data[Filter][uploader_type]"]:checked').length) {
			listUrl += '/uploader_type:'+$('input[name="data[Filter][uploader_type]"]:checked').val();
		}
		return listUrl;
	}
/**
 * コンテキストメニューハンドラ
 */
	function contextMenuHander(action, el, pos) {

		var delUrl = $("#BaseUrl").html()+'admin/uploader/uploader_files/delete';
		
		// IEの場合、action値が正常に取得できないので整形する
		var pos = action.indexOf("#");

		if(pos != -1){
			action = action.substring(pos+1,action.length);
		}

		switch (action){

			case 'edit':
				$('#dialog').dialog('open');
				break;

			case 'delete':
				if(confirm('本当に削除してもよろしいですか？')){
					$("#waiting"+listId).show();
					$.post(delUrl, {"data[UploaderFile][id]": $("#fileList"+listId+" .selected .id").html()}, function(res){
						if(!res){
							$("#waiting"+listId).hide();
							alert("サーバーでの処理に失敗しました。");
						}else{
							$("#fileList"+listId).trigger("deletecomplete");
							updateFileList();
						}
					});
				}
				break;
		}
	}
</script>

<!-- LoginUserId -->
<div id="LoginUserId" style="display: none"><?php echo $user['id'] ?></div>

<!-- ListUrl -->
<?php $baser->link('ListUrl', array('action' => 'ajax_list', $listId, 'num' => $this->passedArgs['num']), array('id' => 'ListUrl'.$listId, 'style' => 'display:none')) ?>

<!-- LoginUserGroupId -->
<div id="LoginUserGroupId" style="display: none"><?php echo $user['user_group_id'] ?></div>

<!-- BaseUrl -->
<div id="BaseUrl" style="display: none"><?php echo $baser->root() ?></div>

<!-- UsePermission -->
<div id="UsePermission" style="display: none"><?php echo $uploaderConfigs['use_permission'] ?></div>

<!-- コンテキストメニュー -->
<ul id="FileMenu1" class="contextMenu">
    <li class="edit"><a href="#edit">編集</a></li>
    <li class="delete"><a href="#delete">削除</a></li>
</ul>
<ul id="FileMenu2" class="contextMenu">
    <li class="edit disabled"><a href="#">編集</a></li>
    <li class="delete disabled"><a href="#">削除</a></li>
</ul>

<!-- loader -->
<div id="waiting<?php echo $listId ?>" class="waiting corner5">
	<?php echo $html->image('ajax-loader.gif') ?>
    Waiting...
</div>

<!-- 編集ダイアログ -->
<div id="dialog" title="ファイル情報編集">
	<?php echo $formEx->create('UploaderFile',array('action' => 'edit', 'id' => 'UploaderFileEditForm'.$listId)) ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th class="col-head"><?php echo $formEx->label('UploaderFile.id', 'NO') ?></th>
            <td class="col-input">
				<?php echo $formEx->text('UploaderFile.id', array('size'=>30,'maxlength'=>255,'readonly'=>'readonly','id'=>'UploaderFileId'.$listId, 'class' => 'uploader-file-id')) ?>&nbsp;
            </td>
        </tr>
		<tr>
			<th class="col-head"><!--<span class="required">*</span>&nbsp;--><?php echo $formEx->label('UploaderFile.name', 'ファイル名') ?></th>
			<td class="col-input">
				<?php echo $formEx->text('UploaderFile.name', array('size'=>30,'maxlength'=>255,'readonly'=>'readonly','id'=>'UploaderFileName'.$listId, 'class' => 'uploader-file-name')) ?>
				<?php echo $formEx->error('UploaderFile.name', 'ファイル名を入力して下さい') ?>&nbsp;
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('UploaderFile.real_name_1', '説明文') ?></th>
			<td class="col-input">
				<?php echo $formEx->text('UploaderFile.alt', array('size'=>30,'maxlength'=>255,'id'=>'UploaderFileAlt'.$listId)) ?>&nbsp;
			</td>
		</tr>
<?php if($uploaderCategories): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('UploaderFile.real_name_1', 'カテゴリ') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('UploaderFile.uploader_category_id', array('type' => 'select', 'options' => $uploaderCategories, 'empty' => '指定なし', 'id' => '_UploaderFileUploaderCategoryId'.$listId)) ?>
			</td>
		</tr>
<?php endif ?>
		<tr>
			<th class="col-head">保存者</th>
			<td class="col-input">
				<span id="UploaderFileUserName<?php echo $listId ?>">&nbsp;</span>
				<?php echo $formEx->input('UploaderFile.user_id', array('type' => 'hidden', 'id' => 'UploaderFileUserId'.$listId)) ?>
			</td>
		</tr>
		<tr><td colspan="2" id="UploaderFileImage<?php echo $listId ?>" class="uploader-file-image"><?php echo $html->image('ajax-loader.gif') ?></td></tr>
    </table>
	<?php echo $formEx->end() ?>
</div>

<!-- form -->
<?php if(!$installMessage): ?>
<p>アップロード&nbsp;
	<?php if($uploaderCategories): ?>
		<?php echo $formEx->input('UploaderFile.uploader_category_id', array('type' => 'select', 'options' => $uploaderCategories, 'empty' => 'カテゴリ指定なし', 'id' => 'UploaderFileUploaderCategoryId'.$listId)) ?>&nbsp;
	<?php endif ?>
	<span id="SpanUploadFile<?php echo $listId ?>">
		<?php echo $formEx->file('UploaderFile.file', array('id'=>'UploaderFileFile'.$listId, 'class' => 'uploader-file-file')) ?>
	</span>
</p>
<?php else: ?>
<p style="color:#C00;font-weight:bold"><?php echo $installMessage ?></p>
<?php endif ?>

<!-- list-num -->
<?php if(empty($this->params['isAjax'])): ?>
<?php $baser->element('list_num') ?>
<?php endif ?>

<!-- ファイルリスト -->
<div id="fileList<?php echo $listId ?>" class="corner5 file-list"></div>
