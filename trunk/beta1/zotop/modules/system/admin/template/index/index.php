<?php $this->header();?>
<script type="text/javascript">
	//禁止页面被包含
	(function(){
		if(top!= self){top.location = self.location;}		
	})();

	//页面加载及重设
	$(function(){
		$("html").css("overflow","hidden");
		$(window).bind('load.frame',resize);
		$(window).bind('resize.frame',resize);
	});

	//记录iframe的当前URL
	$(function(){
		$('#mainIframe').load(function(){
			$(this).show();
			$.cookie('mainIframeUrl',top.mainIframe.location.href);
		});
		$('#sideIframe').load(function(){		
			$(this).show();
			$.cookie('sideIframeUrl',top.sideIframe.location.href);
			$('#side-body-inner').hide();
			$('html').css('overflow','hidden');
			
		})
	});

	$(function(){
		var navbar = $.cookie('navbar');
			navbar = navbar || 0;
		var sideIframeUrl = $.cookie('sideIframeUrl');
		var mainIframeUrl = $.cookie('mainIframeUrl');
		if ( sideIframeUrl && mainIframeUrl ) {
			window.setTimeout(function(){
				top.go(sideIframeUrl,mainIframeUrl);
			},50);
		}else{
			window.setTimeout(function(){
				$("#navbar li").eq(navbar).find('a').trigger('click');
			},500);			
		}
		
		$("#navbar li").eq(navbar).addClass('current');			

		$("#navbar li").click(function(){
			$(this).parent().find('li').removeClass('current');
			$(this).addClass('current');
			$.cookie('navbar', $(this).index());
		});
	});

	//side loading
	$(function(){
		$('a[target=sideIframe]').click(function(){
			$('#side-body-inner').show();
		});
	});

	//页面重新计算
	function resize(){
		var height;
		//主框架的高度
		height = $(window).height()-$('#header').height()-$('#footer').height();
		$('#body').height(height);
		$('#main').height(height);
		$('#mainIframe').height(height);
		$('#side').height(height);
		//设置side部分的高度	
		height = height-$('#side-header').height()-$('#side-footer').height();
		$('#side-body').height(height);
		$('#sideIframe').height(height);
		$('#container').css('visibility','visible');
	};

	//主页面加载
	function go(sideUrl,mainUrl){
		if( sideUrl ){
			$('#side-body-inner').show();
			$('#sideIframe').attr('src',sideUrl);
		}
		if( mainUrl ){
			$('#mainIframe').attr('src',mainUrl);
		}
	}

</script>
<div id="header">
	<div id="top">
		<div id="logo"><a href="<?php echo zotop::url('system/index/main') ?>" target="mainIframe" onfocus="this.blur();" title="控制中心"></a></div>
		<div id="action">
			<div id="topbar">			
				<?php zotop::run('system.quickbar') ?>
				<a href="<?php echo zotop::config('system.help') ?>" class="dialog {width:650,height:400}">帮助</a> <b>|</b>
				<a href="<?php echo zotop::url('system/system/about') ?>" target="mainIframe" class="dialog">关于</a>
			</div>
			<div id="user">
				<span id="user-info">
					<span id="welcome"></span>
					<span id="username">
						<a href="<?php echo zotop::url('system/mine/info') ?>" target="mainIframe"><?php echo $user['name']?> (<?php echo $user['username']?>)</a>
					</span>
					<span id="groupname"><a><?php echo $user['groupname']?></a></span>
				</span>
				<span id="user-action">
					<a href="<?php echo zotop::url('system/mine') ?>" target="mainIframe">个人中心</a><b>|</b>
					<?php zotop::run('system.useraction') ?>
					<a href="<?php echo zotop::url('system/mine/password') ?>" target="mainIframe">修改密码</a><b>|</b>
					<a href="<?php echo zotop::url('system/login/logout') ?>" id="logout" class="confirm {content:'<h1>您确定要退出登录？</h1><div>退出登陆后将默认将返回系统登录页面</div>',yes:'安全退出'}">安全退出</a>
				</span>
			</div>
			<div id="navbar">
				<ul>
					<li><a href="javascript:void(0);" onclick="top.go('<?php echo zotop::url('system/index/side') ?>','<?php echo zotop::url('system/index/main') ?>')"><span>控制中心</span></a></li>
					<?php zotop::run('system.navbar') ?>
					<li><a href="javascript:void(0);" onclick="top.go('<?php echo zotop::url('system/file/side') ?>','<?php echo zotop::url('system/file/index') ?>')"><span>文件管理</span></a></li>
					<li><a href="javascript:void(0);" onclick="top.go('<?php echo zotop::url('system/system/side') ?>','<?php echo zotop::url('system/system/index') ?>')"><span>系统管理</span></a></li>
				</ul>
			</div>
			<div id="favorate"><a href="<?php echo zotop::url('system/favorate') ?>" class="dialog" title="打开收藏夹"><span class="zotop-icon zotop-icon-favorate"></span></a></div>
		</div>
	</div>
	<div id="position">
	<div id="position-side">
	</div>
	<div id="position-main">
	</div>
	</div>
</div>
<div id="body" class="clearfix">
	<div id="main" class="clearfix">
	<div id="main-inner">
		<iframe id="mainIframe" name="mainIframe" src="about:blank" frameborder="no" scrolling="auto" width="100%" height="100%"></iframe>
	</div>
	</div>
	<div id="side">
	<div id="side-inner">
		<div id="side-header">
		</div>
		<div id="side-body">
			<div id="side-body-inner"></div>
			<iframe id="sideIframe" name="sideIframe" src="about:blank" frameborder="no" scrolling="auto" allowtransparency="true" width="100%" height="100%"></iframe>
		</div>
		<div id="side-footer">
			<div>Powered by <a href="http://www.zotop.com" target="_blank"><?php echo zotop::config('zotop.name')?></a></div>
		</div>
	</div>
	</div>
</div>
<div id="footer">
</div>
<?php
$this->footer();
?>