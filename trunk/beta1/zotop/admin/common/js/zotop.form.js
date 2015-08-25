//form
$(function(){
	$.metadata.setType("attr", "valid");	
	
	$('form.form').validate({
			errorPlacement:function(error, element) {
				error.appendTo(element.closest('.field').find('.field-valid'));
			},
			submitHandler: function(form) {
				zotop.msg.wait();
				$form = $(form);
				$form.submiting(true);
				$form.ajaxSubmit({
					success:function(html){						
						msg = zotop.msg.parse(html);	
						if(  msg.type != 'success' || !msg.url ){							
							$form.submiting(false);
							zotop.msg.hide();
						}
						zotop.form.callback(msg,$form);
					}					
				});
			}				
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

	$('form.small').bind('submit.small',function(){
			var $form = $(this);
				$form.submiting(true);				
				$form.ajaxSubmit({
					success:function(data){
						msg = zotop.msg.parse(data);
						$form.find('.form-description').html('<span class="'+msg.type+'">'+msg.content+'</span>');
						if(msg.type == 'success'){
							zotop.url.redirect(msg.url,msg.life);
						}else{
							$form.submiting(false);
						}
					}
				});		
			return false;
	});	
})