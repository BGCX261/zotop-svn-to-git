top.zotop.loading.show('页面加载中，请稍候……');
$(function(){
	top.zotop.loading.hide();
});
//页面底部信息
$(function(){
	$("body").mouseover(function(){
		top.window.status = zotop.html.clean(document.title);					 
	});			
});

$(function(){
	//top.$('#footer').find('td').html(zotop.html.clean(document.title)+' ip:'+zotop.ip.current()+',from:'+zotop.ip.province()+'/'+zotop.ip.city());
});

//修正ie缓存问题
$(function(){   
	if($.browser.msie && $.browser.version == 6){
		try {
			document.execCommand("BackgroundImageCache",false,true);
		} catch(e) {};
	}		
});	

//修正ie横向滚动条的问题
$(function(){   
	if($.browser.msie&&$.browser.version=="6.0"&&$("html")[0].scrollHeight>$("html").height())   
		$("html").css("overflowY","scroll");   
});	



//修正f5的刷新问题
$(document).keydown(function(e){
	if(top.refresh){
		top.refresh(e);
	}			
});

//常见的绑定
$(function(){
	$('a.dialog,li.dialog,span.dialog').dialog();
	$('a.confirm').confirm();
	$('div.dropdown').dropdown();
})

$(function(){
	$('.block.expanded,.block.collapsed').each(function(){
		var $this = $(this);
		$this.find('.block-header h2').css('cursor','pointer').click(function(){
			$this.find('.block-body').slideToggle('fast',function(){
				$this.swapClass('expanded','collapsed');
			});			
		});
	});
});

//form
$(function(){
	$.metadata.setType("attr", "valid");
	
	
	$('form.form').bind('submit.form',function(){
		var $form = $(this);
		var $submit = $form.find(':submit');
		$form.ajaxSubmit({
			beforeSubmit:function(){
				$form.validate();
				if( $form.valid() ){
					$submit.blur().addClass("loading").addClass("disabled").disabled(true);
					return true;
				}
				return false;
			},
			success:function(data){
				msg = zotop.json.get(data);
				if(  msg.type != 'success' ){
					$submit.removeClass("disabled").removeClass("loading").disabled(false);
				}
				zotop.form.result(msg);
			}
		});	
		return false;		
	});


	$('form.list').bind('submit.list',function(){
		$checkbox = $(this).find('input[type=checkbox][class=select]:checked');
		if($checkbox.length == 0){
			zotop.msg.show('操作失败，请至少选择一项');
			return false;
		}
		var $submit = $(":submit",this);
			$submit.blur().addClass("loading").addClass("disabled").disabled(true);
		var $this = $(this);
			$this.ajaxSubmit({
				success:function(data){
					msg = zotop.json.get(data);
					if(msg.type == 'success'){
						msg.onClose = function(id){
							zotop.url.redirect(msg.url,msg.life);
						}
					}else{						
						$submit.removeClass("disabled").removeClass("loading").disabled(false);
					}
					zotop.dialog.msg(msg);
				}
			});		
		return false;
	});

	$('form.small').bind('submit.small',function(){
			var $submit = $(":submit",this);
				$submit.blur().addClass("loading").addClass("disabled").disabled(true);
			var $this = $(this);
				$this.ajaxSubmit({
					success:function(data){
						msg = zotop.json.get(data);
						$('.form-description',$this).html('<span class="zotop-tip '+msg.type+'">'+msg.content+'</span>');
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


//列表颜色及选择项
$(function(){
	$(".list .item").mouseover(function(){$(this).addClass("mouseover");}).mouseout(function(){$(this).removeClass("mouseover");});
	$(".list input.select").click(function(){
		var flag=$(this).attr("checked");
		if(flag){
			$(this).parents(".item").addClass("selected");
		}else{
			$(this).parents(".item").removeClass("selected");	
		}											  
	});		
	$(".selectAll").click(function(){
		var flag=$(this).attr("checked");
		if(!flag){flag=$(this).attr("flag")}
		//alert(flag)
		$(this).parents("form").find("input.select").each(function(){
			if(flag==true||flag=="true"){
				$(this).attr("checked","checked");
				$(this).parents(".item").addClass("selected")
			}else{
				$(this).attr("checked","");	
				$(this).parents(".item").removeClass("selected")
			}			
		})								 
	});
		
	$('table.list').each(function(){
		$(this).find('.title th:first').addClass('first');
	});	
});	

//sortable
$(function(){
	$("table.sortable").sortable({
		items: "tr.item",
		axis: "y",
		placeholder:"sorthelper",
		//opacity:0.6,
		forceHelperSize:true,
		helper: function(e, tr){
			tr.children().each(function(){
				$(this).width($(this).width());
			});
			return tr;
		}
	});			
});