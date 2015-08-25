$(function(){
	//图片对话框
	$("form a.imageuploader").click(function(event){		
		event.preventDefault();

		var $input = $(this).parents('.field').find('input');
		var field = $input.attr('name');
		var value = $input.val();
		var folderid = $input.attr('folderid');
		var globalid = $input.attr('globalid') || $(this).closest('form').find('input[name=_GLOBALID]').val();
		var handle = $(this).attr('href');		

		var callback = function(v){
				if(v.length > 10){
					$('input[name=' + field + ']').val(v).focus();
					return true;
				}
				zotop.msg.error('Error image url!');
				return false;
		}

		//弹出窗口
		var dialog = zotop.dialog.open({
			id:'upload-image',
			width:650,
			height:200,
			type:'iframe',
			title:'插入图片',
			url:handle,
			callback:callback,
			args:{'type':'input','field':field,'value':value,'globalid':globalid,'folderid':folderid}			
		});
		
	});
});