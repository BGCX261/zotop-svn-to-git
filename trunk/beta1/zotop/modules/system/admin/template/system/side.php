<?php $this->header();?>
<?php $this->top();?>
<?php $this->navbar();?>
<script type="text/javascript">
	//top.go("<?php echo zotop::url('system/system/index')?>");
</script>
<?php
box::header(array('title'=>'系统工具','icon'=>'','class'=>'expanded'));
box::add('<ul class="list">');
box::add('<li><a href="'.zotop::url('system/setting').'" target="mainIframe">系统设置</a></li>');
box::add('<li><a href="'.zotop::url('system/config').'" target="mainIframe">注册表管理</a></li>');
box::add('<li><a href="'.zotop::url('system/module').'" target="mainIframe">模块管理</a><span class="extra"><a href="'.zotop::url('zotop/module/uninstalled').'" target="mainIframe">模块安装</a></span></li>');
zotop::run('zotop.system.side.tools');
box::add('</ul>');
box::footer();
?>

<?php

box::header(array('title'=>'文件管理','icon'=>'','class'=>'expanded'));
box::add('<ul class="list">');
box::add('<li><a href="'.zotop::url('system/file').'" target="mainIframe">文件管理</a></li>');
box::add('<li><a href="'.zotop::url('system/file/add').'" target="mainIframe">上传文件</a></li>');
box::add('</ul>');
box::footer();

zotop::run('zotop.system.side.file');
?>

<?php
box::header(array('title'=>'系统用户','icon'=>'','class'=>'expanded'));
box::add('<ul class="list">');
box::add('<li><a href="'.zotop::url('system/user').'" target="mainIframe">系统用户管理</a></li>');
box::add('<li><a href="'.zotop::url('system/usergroup').'" target="mainIframe">系统用户组管理</a></li>');
zotop::run('zotop.system.side.user');
box::add('</ul>');
box::footer();
?>
<?php $this->bottom();?>
<?php $this->footer(); ?>