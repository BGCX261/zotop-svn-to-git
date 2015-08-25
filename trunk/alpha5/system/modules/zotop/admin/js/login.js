if(top!= self){top.location = self.location;}
$(function(){
	$("html").css("overflow","hidden");
	$("#page").css('background','transparent');
	$("div.block").show().center().draggable({handle:'.block-header',containment:'parent'});		;
	$(window).bind('resize',function(){
		$("div.block").center();
	});
	$('#options').click(function(){
		$title = $(this).val();
		zotop.dialog.show({
			title:$title,
			width:400,
			onClose:function(){
				
			}
		});
	});
});


