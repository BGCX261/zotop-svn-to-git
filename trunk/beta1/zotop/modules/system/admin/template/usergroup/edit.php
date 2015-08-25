<?php
$this->header();
$this->top();
$this->navbar();
	form::header();
            

			form::field(array(
				'type'=>'hidden',
				'label'=>zotop::t('用户组编号'),
				'name'=>'id',
				'value'=>$data['id'],
			));

			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('用户组名称'),
			   'name'=>'title',
			   'value'=>$data['title'],
			   'valid'=>'{required:true}',
			   //'description'=>zotop::t('姓名或者昵称，显示在文章或者相关内容的编辑名称位置'),
			));			
			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('用户组说明'),
			   'name'=>'description',
			   'value'=>$data['description'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
		
								
			box::footer();

		form::buttons(
			array('type'=>'submit'),
			array('type'=>'back')
		);

	form::footer();

$this->bottom();
$this->footer();
?>