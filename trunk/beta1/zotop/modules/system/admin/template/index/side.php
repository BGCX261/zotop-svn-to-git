<?php $this->header();?>
<?php $this->top();?>
<?php
box::header(array('title'=>'快捷操作','class'=>'expanded','icon'=>'','action'=>'<a href="#">管理</a>'));
box::add($modules);
box::footer();
?>
<?php
box::header(array('title'=>'个人信息','class'=>'expanded','icon'=>''));
	box::add('<ul class="list">');
	box::add('<li><a href="'.zotop::url('system/mine/info').'" target="mainIframe">修改资料</a></li>');
	box::add('<li><a href="'.zotop::url('system/mine/password').'" target="mainIframe">修改密码</a></li>');
	zotop::run('zotop.index.side.mine');
	box::add('</ul>');
box::footer();
?>
<?php $this->bottom();?>
<?php $this->footer(); ?>