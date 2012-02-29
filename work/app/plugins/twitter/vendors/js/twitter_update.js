var tinyurls = [];

$(function(){
	$("#TwitterStatus").keyup(countStatus);
	$("#TwitterStatus").val($("#TwitterStatusSrc").html().replace(/^\s+|\s+$/g, ""));
	$("#TwitterUpdateSubmit").click(updateStatus);
	$("#TwitterTinyurl").change(tinyurl);
	countStatus();
});
function tinyurl(){

	var errorMessage = 'URLの変換に失敗しました。変換サーバーが高負荷になっている可能性があります。';
	var status = $("#TwitterStatus").val();
	var matches = status.match(/(http[^\s]+)(\s|$)/ig);
	var url = '';
	var key = '';
	var tinyurl = '';
	if(!matches){
		return;
	}
	
	if($("#TwitterTinyurl").attr('checked')){
		for(key in matches){
			url = matches[key].replace(/^\s+|\s+$/g, "");
			if(tinyurls[url] != undefined){
				status = status.split(url).join(tinyurls[url]);
				$("#TwitterStatus").val(status);
			}else{
				$.ajax({
					url: $("#TwitterTinyurlForm").attr('action'),
					type: 'POST',
					data: {'data[Twitter][url]':url},
					dataType: 'text',
					beforeSend: function() {
						$("#AjaxLoader").show();
					},
					success: function(result){
						if(result){
							status = status.split(url).join(result);
							$("#TwitterStatus").val(status);
							tinyurls[url] = result;
						}else{
							$("#ResultMessage").html(errorMessage);
							$("#ResultMessage").show('slide',{direction:"up"},500);
						}
					},
					error: function(){
						$("#ResultMessage").html(errorMessage);
						$("#ResultMessage").show('slide',{direction:"up"},500);
					},
					complete: function(xhr, textStatus) {
						$("#AjaxLoader").hide();
					}
				});
			}
		}
	} else {
		for(key in matches){
			tinyurl = matches[key].replace(/^\s+|\s+$/g, "");
			for(url in tinyurls){
				if(tinyurls[url] == tinyurl){
					status = status.split(tinyurl).join(url);
					$("#TwitterStatus").val(status);
					break;
				}
			}
		}
	}
}
function updateStatus(){
	
	var resultMessage = 'Twitterへの送信が完了しました。';
	var errorMessage = 'Twitterへの送信に失敗しました。Twitterプラグインの設定を見なおしてみてください。Twitterが高負荷になっている可能性もあります。';

	$.ajax({
		url: $("#TwitterUpdateForm").attr('action'),
		type: 'POST',
		data: $("#TwitterUpdateForm").serialize(),
		dataType: 'text',
		beforeSend: function() {
			$("#TwitterUpdateSubmit").attr('disabled', 'disabled');
			$("#AjaxLoader").show();
			if($("#ResultMessage").css('display')!='none'){
				$("#ResultMessage").hide('slide',{direction:"up"},500);
			}
		},
		success: function(result){
			if(result){
				var link = $(document.createElement('a')).attr('href',result).attr('target','_blank').html(result);
				$("#TwitterStatus").val('');
				$("#ResultMessage").html(resultMessage+'<br />');
				$("#ResultMessage").append(link);
				$("#ResultMessage").show('slide',{direction:"up"},500);				
			}else{
				$("#ResultMessage").html(errorMessage);
				$("#ResultMessage").show('slide',{direction:"up"},500);
			}
		},
		error: function(){
			$("#ResultMessage").html(errorMessage);
			$("#ResultMessage").show('slide',{direction:"up"},500);
		},
		complete: function(xhr, textStatus) {
			$("#TwitterUpdateSubmit").removeAttr('disabled');
			$("#AjaxLoader").hide();
			countStatus();
		}
	});
	
	return false;

}
function countStatus(){
	var len = $("#TwitterStatus").val().length;
	$("#TextCounter").html(len);
	if(len > 140){
		$("#TextCounter").css('color',"#C00");
		$("#TwitterUpdateSubmit").attr('disabled', 'disabled');
	}else if(len == 0){
		$("#TwitterUpdateSubmit").attr('disabled', 'disabled');
	}else{
		$("#TextCounter").css('color',"#999");
		$("#TwitterUpdateSubmit").removeAttr('disabled');
	}
}