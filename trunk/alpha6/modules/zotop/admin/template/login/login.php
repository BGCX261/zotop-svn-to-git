<?php $this->header(); ?>
<script>
	//禁止被包含
	if(top!= self){top.location = self.location;}

	$(function(){
		$("html").css("overflow","hidden");
		$("#page").css('background','transparent');
		$("div.block").show().center().draggable({handle:'.block-header',containment:'parent'});		;
		$(window).bind('resize',function(){
			$("div.block").center();
		});

		//加入收藏夹
		$("a.addFavorite").click(function(){
			if(!title) {   
				var title = window.document.title;   
			}   
			if(!url) {   
				var url = window.document.location;   
			}
			try{   
				if (document.all){    
					window.external.addFavorite(url,title);    
				} else if (window.sidebar) {    
					window.sidebar.addPanel(title, url,"");    
				} else {
					zotop.msg.show('对不起，您的浏览器不支持该功能');
				}  
			}catch(e){};
			
			return false;
		});
		
		//选项设置
		$('button[name=options]').click(function(){

			$title = $(this).text();

			zotop.dialog.show({
				title:$title			
			});
		});
	});
</script>
<div id="header">
	<h2 style="text-align:left;"><?php echo $title?></h2>
	<h3>
		<a href="<?php echo zotop::config('site.index') ?>">网站首页</a>
		<b>|</b>
		<a href="javascript:void(0);" class="addFavorite">加入收藏夹</a>
		<b>|</b>
		<a href="<?php echo zotop::url('zotop/login/shortcut',array('title'=>url::encode($title.' '.zotop::config('site.name')), 'url'=>url::encode(url::location()) )) ?>">设为桌面图标</a>		
	</h3>
</div>

<?php

    block::header(array('title'=>$title,'icon'=>'user','action'=>''));
    
		   form::header(array('title'=>'','description'=>' 请输入您的帐户和密码登录','class'=>'small'));

			   form::field(array(
				   'type'=>'text',
				   'label'=>zotop::t('帐 户(U)'),
				   'name'=>'username',
				   'value'=>zotop::cookie('admin.username'),
				   'valid'=>'required:true'
			   ));

				form::field(array(
				   'type'=>'password',
				   'label'=>zotop::t('密 码(P)'),
				   'name'=>'password',
				   'value'=>'',
				   'valid'=>'required:true'
			   ));

			   form::buttons(
				   array('type'=>'submit','value'=>'登录'),
				   array(
					'type'=>'button',
					'name'=>'options',
					'value'=>'选项',
				   )
			   );

		   form::footer();
    
    block::footer();
?>
<?php $this->footer(); ?>