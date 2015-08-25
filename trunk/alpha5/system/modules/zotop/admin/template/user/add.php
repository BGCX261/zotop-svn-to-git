<?php
$this->header();
$this->top();
$this->navbar();
	form::header();
            
			block::header('账户信息');

			form::field(array(
				'type'=>'hidden',
				'label'=>zotop::t('账户编号'),
				'name'=>'id',
				'value'=>$data['id'],
			));

			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('账户名称'),
			   'name'=>'username',
			   'value'=>$data['username'],
			   'valid'=>'{required:true,username:true,minlength:5,maxlength:32,remote:"'.zotop::url('zotop/user/checkusername').'",messages:{remote:"该名称已经被占用，请选择其它名称"}}',
			   'description'=>zotop::t('允许使用中文、数字、英文字符(不区分大小写)或者下划线，不允许使用其它特殊字符，5~32位'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('账户密码'),
			   'id'=>'password',
			   'name'=>'password',
			   'value'=>'',
			   'valid'=>'required:true,minlength:6,maxlength:32',
			   'description'=>zotop::t('请输入账户密码，6~32位之间'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('确认密码'),
			   'name'=>'_password',
			   'value'=>'',
			   'valid'=>'required:true,equalTo:"#password"',
			   'description'=>zotop::t('为确保安全，请再次输入账户密码'),
			));						
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('安全问题'),
			   'name'=>'question',
			   'value'=>$data['question'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));
			
			form::field(array(
			   'type'=>'hidden',
			   'label'=>zotop::t('安全答案'),
			   'name'=>'answer',
			   'value'=>$data['answer'],
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));

			form::field(array(
			   'type'=>'select',
			   'options'=>$usergroups,
			   'label'=>zotop::t('用户组'),
			   'name'=>'groupid',
			   'value'=>$data['groupid'],
			   'valid'=>'required:true',
			   'description'=>zotop::t('不同用户组所属角色不同，权限也不同'),
			));				
			
			block::footer();
			
			block::header('个人信息');
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('姓名'),
			   'name'=>'name',
			   'value'=>$data['name'],
			   'valid'=>'{required:true,messages:{required:\'请输入你的姓名或者昵称\'}}',
			   'description'=>zotop::t('姓名或者昵称，显示在文章或者相关内容的编辑名称位置'),
			));			
			form::field(array(
			   'type'=>'radio',
			   'options'=>array('1'=>'先生','0'=>'女士'),
			   'label'=>zotop::t('性别'),
			   'name'=>'gender',
			   'value'=>$data['gender'] || 1 ,
			   //'valid'=>"{required:true}",			   
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

form::footer(array(
    array('type'=>'submit','value'=>'创 建'),
    array('type'=>'back','value'=>'返回前页')
));

$this->bottom();
$this->footer();
?>