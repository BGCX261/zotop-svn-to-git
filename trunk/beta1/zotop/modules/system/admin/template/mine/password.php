<?php
$this->header();
$this->top();
$this->navbar();
       
    echo html::msg('<h2>安全提示</h2><div>请不要使用过于简单的密码，如：111111、123456、生日密码、电话号码等，并及时的更换密码</div>');
    
	form::header();
	
	form::field(array(
	   'type'=>'disabled',
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
	   'valid'=>"required:true,equalTo:'#newpassword'",
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