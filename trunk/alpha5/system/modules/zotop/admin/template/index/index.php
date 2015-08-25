<?php
$this->addScript('$this/js/index.js');
$this->header();
?>
<script type="text/javascript">
    zotop.url.frame.main="<?php echo zotop::url('zotop/main') ?>"
    zotop.url.frame.side="<?php echo zotop::url('zotop/main/side') ?>"
	zotop.url.msg.unread="<?php echo zotop::url('zotop/msg/unread') ?>"
</script>
<div id="header">
	<div id="top">
		<div id="logo"><a href="<?php echo zotop::url('zotop/main') ?>" target="mainIframe" onfocus="this.blur();" title="返回控制中心首页"></a></div>
		<div id="action">
			<div id="topbar">
				<a href="<?php echo zotop::url('zotop/note/new') ?>" class="dialog {width:500,height:280}">记事本</a><b>|</b>
				<a href="<?php echo zotop::url('zotop/setting') ?>" target="mainIframe">系统设置</a><b>|</b>
				<a href="<?php echo zotop::url('zotop/system/about') ?>" target="mainIframe" class="dialog {width:450,height:160}">关于</a>
			</div>
			<div id="user">
				<span id="user-info"><b><a href="<?php echo zotop::url('zotop/mine/changeinfo') ?>" target="mainIframe"><?php echo $user['username']?></a></b>(administrator)</span>
				<span id="user-action">
					<a href="<?php echo zotop::url('zotop/main') ?>" target="mainIframe">控制中心首页</a><b>|</b>
					<span id="msg"><a href="<?php echo zotop::url('zotop/msg') ?>" target="mainIframe">短消息</a><span id="msg-unread"><a href="<?php echo zotop::url('zotop/msg/unread') ?>" target="mainIframe"><span id="msg-unread-num">0</span>条未读</a></span><b>|</b></span>
					<a href="<?php echo zotop::url('zotop/mine/changepassword') ?>" target="mainIframe">修改我的密码</a><b>|</b>
					<a href="<?php echo zotop::url('zotop/login/logout') ?>" id="logout" class="confirm {content:'<h1>您确定要退出登录？</h1><div>退出登陆后将默认将返回系统登录页面</div>',yes:'安全退出'}">安全退出</a>
				</span>
			</div>
			<div id="navbar">
				<ul>
					<li><a href="<?php echo zotop::url('zotop/main/side') ?>" target="sideIframe"><span>控制面板</span></a></li>
					<li><a href="<?php echo zotop::url('zotop/content') ?>" target="mainIframe"><span>内容管理</span></a></li>
					<li><a href="<?php echo zotop::url('zotop/member') ?>" target="mainIframe"><span>会员管理</span></a></li>
					<li><a href="<?php echo zotop::url('zotop/system/side') ?>" target="sideIframe"><span>系统管理</span></a></li>
				</ul>
			</div>
			<div id="favorate"><a href="<?php echo zotop::url('zotop/favorate') ?>" class="dialog" title="打开收藏夹">收藏夹</a></div>
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