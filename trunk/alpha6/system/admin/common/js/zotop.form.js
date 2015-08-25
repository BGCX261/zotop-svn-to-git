//form
$(function(){
	$.metadata.setType("attr", "valid");	
	
	$('form.form').bind('submit.form',function(){
		var $form = $(this);
		var $submit = $form.find(':submit');
		$form.ajaxSubmit({
			beforeSubmit:function(){
				$form.validate({
					errorPlacement:function(error, element) {
						error.appendTo(element.closest('.field').find('.field-valid'));
					}
				});
				if( $form.valid() ){
					$submit.blur().addClass("loading").addClass("disabled").disabled(true);
					return true;
				}
				return false;
			},
			success:function(html){				
				msg = zotop.msg.parse(html);	
				if(  msg.type != 'success' || !msg.url ){
					$submit.removeClass("disabled").removeClass("loading").disabled(false);
				}
				zotop.form.callback(msg);
			}
		});	
		return false;		
	});


	$('form.list').bind('submit.list',function(){
		$checkbox = $(this).find('input[type=checkbox][class=select]:checked');
		if($checkbox.length == 0){
			zotop.msg.error('未选中任何项！');
			return false;
		}
		var $submit = $(":submit",this);
			$submit.blur().addClass("loading").addClass("disabled").disabled(true);
		var $this = $(this);
			$this.ajaxSubmit({
				success:function(data){
					msg = zotop.msg.parse(data);
					if(msg.type == 'success'){
						msg.onClose = function(id){
							zotop.url.redirect(msg.url,msg.life);
						}
					}else{						
						$submit.removeClass("disabled").removeClass("loading").disabled(false);
					}
					zotop.msg.show(msg);
				}
			});		
		return false;
	});

	$('form.list').each(function(){
		var $form = $(this); 
		$('button[type=button]',$form).click(function(){
			var classname = $(this).attr('class');
		});
	});

	$('form.small').bind('submit.small',function(){
			var $submit = $(":submit",this);
				$submit.blur().addClass("loading").addClass("disabled").disabled(true);
			var $this = $(this);
				$this.ajaxSubmit({
					success:function(data){
						msg = zotop.msg.parse(data);
						$('.form-description',$this).html('<span class="zotop-icon zotop-icon-'+msg.type+'"></span><span class="'+msg.type+'">'+msg.content+'</span>');
						if(msg.type == 'success'){
							zotop.url.redirect(msg.url,msg.life);
						}else{
							$submit.removeClass("disabled").removeClass("loading").disabled(false);
						}
					}
				});		
			return false;
	});	
})