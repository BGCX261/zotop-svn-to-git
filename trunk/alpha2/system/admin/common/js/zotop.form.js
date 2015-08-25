$.metadata.setType("attr", "valid");
$(function(){
	$('form.form').each(function(){
		$(this).validate({
			submitHandler: function(form) {
				var $submit = $(":submit",form);
					$submit.blur().addClass("loading").addClass("disabled").get(0).disabled=true;
					$(form).ajaxSubmit({
						success:function(data){
							msg = zotop.msg.get(data);
							if(msg.type == 'success'){
								zotop.msg.success(msg.title,msg.content,function(id){
									zotop.url.redirect(msg.url,msg.life);
								});
							}else{
								zotop.msg.error(msg.title,msg.content);
								$submit.removeClass("disabled").removeClass("loading").get(0).disabled=false;
							}
						}
					});	
				return false;
			}
		});
	});

	$('form.list').submit(function(){
		$checkbox = $(this).find('input[type=checkbox][class=select]:checked');
		if($checkbox.length == 0){
			zotop.msg.error('操作失败','<h2>请至少选择一项</h2>');
			return false;
		}
		var $submit = $(":submit",this);
			$submit.blur().addClass("loading").addClass("disabled").get(0).disabled=true;
		var $this = $(this);
			$this.ajaxSubmit({
				success:function(data){
					msg = zotop.msg.get(data);
					if(msg.type == 'success'){
						zotop.msg.success(msg.title,msg.content,function(id){
							zotop.url.redirect(msg.url,msg.life);
						});
					}else{
						zotop.msg.error(msg.title,msg.content);
						$submit.removeClass("disabled").removeClass("loading").get(0).disabled=false;
					}
					
				}
			});		
		return false;
	});

	$('form.small').submit(function(){
			var $submit = $(":submit",this);
				$submit.blur().addClass("loading").addClass("disabled").get(0).disabled=true;
			var $this = $(this);
				$this.ajaxSubmit({
					success:function(data){
						msg = zotop.msg.get(data);
						$('.form-description',$this).html('<span class="zotop-tip '+msg.type+'">'+msg.content+'</span>');
						if(msg.type == 'success'){
							zotop.url.redirect(msg.url,msg.life);
						}else{
							$submit.removeClass("disabled").removeClass("loading").get(0).disabled=false;
						}
						
					}
				});		
			return false;
	});	
	
	//图片对话框
	$("form .upload-image").click(function(){
		var $this = $(this);
		var fieldtype = 'input';
		var fieldname = $(this).prev('input').attr('name');
		var $dialog =zotop.upload.image(fieldtype,fieldname);		
	});
});

//列表颜色及选择项
$(function(){
	$("table.list tr:not(.title)").mouseover(function(){$(this).addClass("mouseover");}).mouseout(function(){$(this).removeClass("mouseover");});
	$("table.list input.select").click(function(){
		var flag=$(this).attr("checked");
		if(flag){
			$(this).parents("tr").addClass("selected");
		}else{
			$(this).parents("tr").removeClass("selected");	
		}											  
	});

	$(".selectAll").click(function(){
		var flag=$(this).attr("checked");
		if(!flag){flag=$(this).attr("flag")}
		//alert(flag)
		$(this).parents("form").find("input.select").each(function(){
			if(flag==true||flag=="true"){
				$(this).attr("checked","checked");
				$(this).parents("tr").addClass("selected")
			}else{
				$(this).attr("checked","");	
				$(this).parents("tr").removeClass("selected")
			}			
		})								 
	});
	

});

zotop.namespace('zotop.upload');
zotop.upload.image = function(fieldtype,fieldname){
	var _callback = function(){
			$('input[name='+fieldname+']').focus();
	}
	var upload = zotop.dialog.show({
		id:'upload-image',
		width:600,
		height:200,
		type:'iframe',
		title:'上传图片',
		url:zotop.url.build('zotop/upload/image'),
		args:{type:fieldtype,name:fieldname},
		callback:_callback
	});	

	return upload;
}
zotop.upload.insert = function(fieldtype,fieldname,file)
{
	$('input[name='+fieldname+']').val(file).focus();
}