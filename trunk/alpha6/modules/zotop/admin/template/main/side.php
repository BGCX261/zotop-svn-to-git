<?php $this->header();?>

<?php
block::header(array('title'=>'快捷操作','class'=>'expanded','icon'=>'','action'=>'<a href="#">管理</a>'));
block::add($modules);
block::footer();
?>
<?php
block::header(array('title'=>'我的信息','class'=>'expanded','icon'=>''));
block::add('<ul class="list">');
block::add('<li><a href="'.zotop::url('zotop/mine/changeinfo').'" target="mainIframe">修改我的资料</a></li>');
block::add('<li><a href="'.zotop::url('zotop/mine/changepassword').'" target="mainIframe">修改我的密码</a></li>');
zotop::run('zotop.index.side.mine');
block::add('</ul>');
block::footer();
?>
<?php $this->footer(); ?>