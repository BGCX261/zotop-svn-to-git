<?php $this->header();?>

<script type="text/javascript">
	var mainIframeUrl = "<?php echo zotop::url('zotop/main') ?>";
	var sideIframeUrl = "<?php echo zotop::url('zotop/main/side') ?>";

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

	//给iframe赋值
	$(function(){
		var mainUrl=$.cookie('mainIframeUrl');
		if( mainUrl == null || mainUrl == 'about:blank' ){
			mainUrl = mainIframeUrl;
		}
		var sideUrl=$.cookie('sideIframeUrl');
		if( sideUrl == null || sideUrl == 'about:blank' ){
			sideUrl = sideIframeUrl;
		}

		setTimeout(function(){
			top.go(mainUrl,sideUrl);
		},500);
	});

	$(function(){
		var navbar = $.cookie('navbar');
			navbar = navbar || 0;
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

	/*
	$(function(){
		$('#username').panel({
			content:'<div style="width:500px;height:200px;">panel content</div>',
			onShow:function(){
			
			}
		});
	});
	*/
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
		$('#page').css('visibility','visible');
	};

	//主页面加载
	function go(mainUrl,sideUrl){
		if(sideUrl){
			top.sideIframe.location.href = sideUrl;
		}
		if(mainUrl){
			top.mainIframe.location.href = mainUrl;
		}
	}

</script>
<div id="header">
	<div id="top">
		<div id="logo"><a href="<?php echo zotop::url('zotop/main') ?>" target="mainIframe" onfocus="this.blur();" title="控制中心首页"></a></div>
		<div id="action">
			<div id="topbar">			
<?php zotop::run('zotop.index.quickbar') ?>
				<a href="<?php echo zotop::config('zotop.help') ?>" class="dialog {width:650,height:400}">帮助</a> <b>|</b>
				<a href="<?php echo zotop::url('zotop/system/about') ?>" target="mainIframe" class="dialog">关于</a>
			</div>
			<div id="user">
				<span id="user-info">
					<span id="welcome"></span>
					<span id="username"><a href="<?php echo zotop::url('zotop/mine/changeinfo') ?>" target="mainIframe"><?php echo $user['name']?> (<?php echo $user['username']?>)</a></span>
					<span id="groupname"><a><?php echo $user['groupname']?></a></span>
				</span>
				<span id="user-action">
					<a href="<?php echo zotop::url('zotop/main') ?>" target="mainIframe">控制中心首页</a><b>|</b>
<?php zotop::run('zotop.index.useraction') ?>
					<a href="<?php echo zotop::url('zotop/mine/changepassword') ?>" target="mainIframe">修改我的密码</a><b>|</b>
					<a href="<?php echo zotop::url('zotop/login/logout') ?>" id="logout" class="confirm {content:'<h1>您确定要退出登录？</h1><div>退出登陆后将默认将返回系统登录页面</div>',yes:'安全退出'}">安全退出</a>
				</span>
			</div>
			<div id="navbar">
				<ul>
					<li><a href="<?php echo zotop::url('zotop/main/side') ?>" target="sideIframe"><span>我的面板</span></a></li>
<?php zotop::run('zotop.index.navbar') ?>
					<li><a href="<?php echo zotop::url('zotop/system/side') ?>" target="sideIframe"><span>系统管理</span></a></li>
				</ul>
			</div>
			<div id="favorate"><a href="<?php echo zotop::url('zotop/favorate') ?>" class="button ibutton dialog" title="打开收藏夹"><span class="button-icon zotop-icon zotop-icon-favorate"></span><span class="button-text">收藏夹</span></a></div>
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