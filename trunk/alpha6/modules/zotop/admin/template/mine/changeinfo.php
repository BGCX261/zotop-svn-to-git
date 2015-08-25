<?php
$this->header();
$this->top();
$this->navbar();


			form::header();
            
			block::header('账户信息');

			form::field(array(
			   'type'=>'label',
			   'label'=>zotop::t('账户名称'),
			   'name'=>'username',
			   'value'=>$data['username'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
						
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('安全问题'),
			   'name'=>'question',
			   'value'=>$data['question'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('安全答案'),
			   'name'=>'answer',
			   'value'=>$data['answer'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			
			
			block::footer();
			
			block::header('个人信息');
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('姓名'),
			   'name'=>'name',
			   'value'=>$data['name'],
			   'valid'=>'required:true',
			   'description'=>zotop::t(''),
			));			
			form::field(array(
			   'type'=>'radio',
			   'options'=>array('1'=>'先生','0'=>'女士'),
			   'label'=>zotop::t('性别'),
			   'name'=>'gender',
			   'value'=>$data['gender'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			form::field(array(
			   'type'=>'image',
			   'label'=>zotop::t('头像'),
			   'name'=>'image',
			   'value'=>$data['image'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
		    form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('电子邮件'),
			   'name'=>'email',
			   'value'=>$data['email'],
			   'valid'=>'required:true,email:true',
			   'description'=>zotop::t(''),
			));	
			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('个人签名'),
			   'name'=>'sign',
			   'value'=>$data['sign'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('更新时间'),
			   'name'=>'updatetime',
			   'value'=>$data['updatetime'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			
								
			block::footer();
			
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);

			form::footer();


$this->bottom();
$this->footer();
?>