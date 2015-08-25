<?php
$this->header();
$this->top();
$this->navbar();

			form::header(array(
				//'title'=>'修改密码',
				'description'=>'为确保账户安全，请不要使用过于简单的密码，并及时的更换密码',
			    'icon'=>'',
			));
			
			form::field(array(
			   'type'=>'label',
			   'label'=>'账户名称',
			   'name'=>'username',
			   'value'=>$user->username,
			   'valid'=>'',
			   'description'=>'',
			));			

			form::field(array(
			   'type'=>'password',
			   'label'=>'原密码',
			   'name'=>'password',
			   'value'=>'',
			   'valid'=>'required:true',
			   'description'=>'为确保安全，请输入你的密码',
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>'新密码',
			   'id'=>'newpassword',
			   'name'=>'newpassword',
			   'value'=>'',
			   'valid'=>'required:true,minlength:6,maxlength:32',
			   'description'=>'请输入您的新密码，6~32位之间',
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>'确认新密码',
			   'name'=>'newpassword2',
			   'value'=>'',
			   'valid'=>'required:true,equalTo:"#newpassword"',
			   'description'=>'为确保安全，请再次输入您的新密码',
			));
			
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

$this->bottom();
$this->footer();
?>