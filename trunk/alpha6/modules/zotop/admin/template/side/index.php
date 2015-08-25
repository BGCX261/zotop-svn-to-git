<?php $this->header();?>

<?php
block::header(array('title'=>'应用列表','class'=>'expanded','action'=>'<a href="#">管理</a>'));
block::add('<ul class="list">');
foreach($apps as $id=>$app)
{
    block::add('<li><a href="'.zotop::url($app['id']).'" target="mainIframe"><span>'.$app['name'].'</span></a></li>');
}
block::add('</ul>');
block::footer();
?>

<?php $this->footer(); ?>