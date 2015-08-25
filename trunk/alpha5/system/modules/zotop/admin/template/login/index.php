<?php
$this->header();

    block::header(array('title'=>'系统管理登录','action'=>'<a href="'.zotop::url('site://').'">网站首页</a>'));
    
		   form::header(array('title'=>'','description'=>'请输入您的帐户和密码登录','class'=>'small'));

		   form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('帐 户(U)'),
			   'name'=>'username',
			   'value'=>zotop::cookie('admin.username'),
			   'valid'=>'required:true'
			   //'description'=>'请输入您的用户名或者电子信箱',
		   ));

			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('密 码(P)'),
			   'name'=>'password',
			   'value'=>'',
			   'valid'=>'required:true'
		   ));

		   form::buttons(
			   array('type'=>'submit','value'=>'登 录'),
			   array(
				'type'=>'button',
				'name'=>'options',
				'value'=>'选 项',
			   )
		   );
		   form::footer();

		   block::footer();    
    
    block::footer();

$this->footer();
?>