$(function(){
	zotop.editor.plugins={
		Map:{c:'zotop-icon zotop-icon-map',t:'插入Google地图',e:function(){
			var _this=this;
			_this.showIframeModal('Google 地图','{editorRoot}xheditor_plugins/googlemap/googlemap.html',function(v){_this.pasteHTML('<img src="'+v+'" />');},538,404);
		}},
		Pager:{c:'zotop-icon zotop-icon-pager',t:'插入分页(Ctrl+P)',s:'ctrl+p',h:1,e:function(){
			var _this=this;
			var jTest=$('<div style="padding:10px;width:300px;"><div>请输入分页标题</div><div><input type="text" name="PageTitle" id="PageTitle" class="xheText"/></div></div><div style="text-align:right;"><input type="button" id="xheSave" value="确定" /></div>');
			var jSave=$('#xheSave',jTest);
			var jPageTitle=$('#PageTitle',jTest);
			jSave.click(function(){
				var title = jPageTitle.val();
				var pager = title ? '[page]'+title+'[/page]' : '[page]';
				_this.pasteHTML(pager);
				_this.hidePanel();
				return false;	
			});
			_this.showDialog(jTest);
			
		}}
	};
	
	$('textarea.editor').each(function(){
		var name = $(this).attr('name');
		var editor = $(this).xheditor({
				skin:'zotop',
				plugins:zotop.editor.plugins,
				tools:'Pastetext,Pager,Map,|,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,|,Align,List,Outdent,Indent,|,Link,Unlink,Img,Flash,Media,Emot,Table,|,Source,Fullscreen',
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
		var folderid = $('textarea[name='+field+']').attr('folderid');
		var globalid = $('textarea[name='+field+']').attr('globalid') || $(this).closest('form').find('input[name=_GLOBALID]').val();
		var editor = zotop.editor.instanse[field];

		var callback = function(value){
				if(value.length > 10){
					editor.pasteHTML(value);				
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
			args:{'type':type,'field':field,'value':value,'globalid':globalid,'folderid':folderid}		
		});
	});
});


