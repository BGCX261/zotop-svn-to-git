<?php $this->header();?>
<?php
block::header(array('title'=>'系统工具','icon'=>'','class'=>'expanded'));
block::add('<ul class="list">');
block::add('<li><a href="'.zotop::url('zotop/setting').'" target="mainIframe">系统设置</a></li>');
block::add('<li><a href="'.zotop::url('zotop/config').'" target="mainIframe">注册表管理</a></li>');
block::add('<li><a href="'.zotop::url('zotop/module').'" target="mainIframe">系统模块管理</a><span class="extra"><a href="'.zotop::url('zotop/module/uninstalled').'" target="mainIframe">模块安装</a></span></li>');
zotop::run('zotop.system.side.tools');
block::add('</ul>');
block::footer();
?>

<?php

block::header(array('title'=>'文件管理','icon'=>'','class'=>'expanded'));
block::add('<ul class="list">');
block::add('<li><a href="'.zotop::url('zotop/file').'" target="mainIframe">文件管理</a></li>');
block::add('<li><a href="'.zotop::url('zotop/file/add').'" target="mainIframe">上传文件</a></li>');
block::add('</ul>');
block::footer();

zotop::run('zotop.system.side.file');
?>

<?php
block::header(array('title'=>'系统用户','icon'=>'','class'=>'expanded'));
block::add('<ul class="list">');
block::add('<li><a href="'.zotop::url('zotop/user').'" target="mainIframe">系统用户管理</a></li>');
block::add('<li><a href="'.zotop::url('zotop/usergroup').'" target="mainIframe">系统用户组管理</a></li>');
zotop::run('zotop.system.side.user');
block::add('</ul>');
block::footer();
?>

<?php $this->footer(); ?>