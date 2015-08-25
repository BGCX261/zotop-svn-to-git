<?php $this->header();?>

<?php
block::header(array('title'=>'系统工具','class'=>'expanded','action'=>'<a href="#">管理</a>'));
block::add($tools);
block::footer();
?>

<?php
block::header(array('title'=>'系统用户管理','class'=>'expanded'));
block::add($users);
block::footer();
?>

<?php
block::header(array('title'=>'模块管理','class'=>'expanded'));
block::add($modules);
block::footer();
?>

<?php $this->footer(); ?>