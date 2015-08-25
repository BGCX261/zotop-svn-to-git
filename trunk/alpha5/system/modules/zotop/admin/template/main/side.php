<?php $this->header();?>

<?php
block::header(array('title'=>'应用列表','class'=>'expanded','action'=>'<a href="#">管理</a>'));
block::add($modules);
block::footer();
?>

<?php $this->footer(); ?>