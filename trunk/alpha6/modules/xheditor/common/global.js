
$(function(){

	$('textarea.editor').each(function(){
		var name = $(this).attr('name');
		var editor = $(this).xheditor({
				skin:'zotop',
				emots:{'default':{name:'QQ',count:55,width:25,height:25,line:11},'zotop':{name:'zotop',count:40,width:25,height:25,line:8}},
				layerShadow:2
		});
		zotop.editor.instanse[name] = editor;
	});


	$('.editor-insert').click(function(event){
		event.preventDefault();
		var field = $(this).attr('name');
		var type = $(this).attr('type');
		var dislogtitle = $(this).html();
		var value = '';
		var handle = $(this).attr('href');		
			handle = handle.replace(/__image__/i, zotop.url.rawurlencode(zotop.url.rawurlencode(value)));
		var folderid = $(this).attr('folderid');
		var globalid = $(this).attr('globalid');
		var editor = zotop.editor.instanse[field];

		var callback = function(value,title,link){
				if(value.length > 10){
					switch(type){
						case 'image':
							if (link){
								editor.pasteHTML('<span class="image"><a href="'+link+'" target="_blank"><img src="/z6/'+value+'" title="'+title+'"></a></span>');
							}else{
								editor.pasteHTML('<span class="image"><img src="/z6/'+value+'" title="'+title+'"></span>');
							}							
							break;
						case 'file':
							title = title || value;
							editor.pasteHTML('<span class="file"><a href="/z6/'+value+'" title="'+title+'" target="_blank">'+title+'</span>');
							break;
						case 'audio':
							break;
						case 'media':
							break;
						case 'flash':
							break;
						case 'html':
						default:
							editor.pasteHTML(value);
							break;
					}
					
					return true;
				}
				zotop.msg.error('Error Value:'+ value +'!');
				return false;
		}

		//弹出窗口
		var dialog = zotop.dialog.open({
			id:'insert-'+type,
			width:650,
			height:200,
			type:'iframe',
			title:dislogtitle,
			url:handle,
			callback:callback,
			args:{'type':'editor','field':field,'value':value,'globalid':globalid,'folderid':folderid}		
		});
	});
});


