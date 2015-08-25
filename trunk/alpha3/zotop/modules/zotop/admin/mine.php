<?php
class mine_controller extends controller
{
    public function __init()
    {
        field::set('image',array($this,'image'));
    }
    
    /**
     * 自定义一个图片控件，该控件只对当前控制器页面有效
     *  
     * @param array 控件参数
     */
    public function image($attrs)
    {
        $html = array();
        $html[] = field::hidden($attrs);
        $html[] = '<div class="textarea" style="margin-top:-1px;height:100px;">';
        $html[] = '	<ul class="zotop-image-list" id="'.$attrs['name'].'-images">';
        for($i=0; $i<10; $i++)
        {
            $image = url::theme().'/image/userface/'.$i.'.gif';
            $class = ($attrs['value'] == $image) ? 'selected' : 'normal';
            $html[] = '		<li class="'.$class.'"><a href="javascript:void(0);" onfocus="blur()"><img src="'.$image.'" style="width:64px;height:64px;"></a></li>';
        }
        $html[] = '	</ul>';
        $html[] = '</div>';
        $html[] = '
        <script>
        	$(function(){
    			var name = "'.$attrs['name'].'";
        		var id = "#'.$attrs['name'].'-images";
        		$(id).find("li").click(function(){
    				var image = $(this).find("img").attr("src");
    				$(this).parents("ul").find("li").removeClass("selected");
    				$(this).addClass("selected");
    				$("#"+name).val(image);
    			})
    		});        
        </script>
        ';
        
        return implode("\n",$html);
    }
    
    public function navbar()
    {
        return array(
			array('id'=>'changeinfo','title'=>'修改我的资料','href'=>url::build('zotop/mine/changeinfo')),
			array('id'=>'changepassword','title'=>'修改我的密码','href'=>url::build('zotop/mine/changepassword')),
		);
    }
    
    public function onDefault()
    {
        
    }
    
    public function onChangePassword()
    {
		$user = zotop::model('zotop.user');
        $user->id = (int)zotop::user('id');
        $user->username = (string)zotop::user('username');
        
        if(form::isPostBack())
		{

			$user->read();
							
			$password = request::post('password');
			$newpassword = request::post('newpassword');
						
			if( $user->password($password) != $user->password )
			{
			    msg::error('输入错误',zotop::t('您输入的原密码:<b>{$password}</b>错误，请确认',array('password'=>$password)));
			}			
			if( $newpassword != request::post('newpassword2') )
			{
			    msg::error('输入错误',zotop::t('两次输入的新密码不一致，请确认'));
			}			

			if($newpassword != $password)
			{
			   $update = $user->update(array(
			       'id' => $user->id,
			       'password' => $user->password($newpassword),
			   ));

			}
            msg::success('修改成功',zotop::t('密码修改成功，请记住您的新密码'),'reload');			
		}
		$page['title'] = '修改我的密码';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header(array(
				'title'=>'修改密码',
				'description'=>'为确保账户安全，请不要使用过于简单的密码，并及时的更换密码',
			    'icon'=>''
			));
			
			form::field(array(
			   'type'=>'label',
			   'label'=>zotop::t('账户名称'),
			   'name'=>'username',
			   'value'=>$user->username,
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			

			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('原密码'),
			   'name'=>'password',
			   'value'=>'',
			   'valid'=>'required:true',
			   'description'=>zotop::t('为确保安全，请输入你的密码'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('新密码'),
			   'id'=>'newpassword',
			   'name'=>'newpassword',
			   'value'=>'',
			   'valid'=>'required:true,minlength:6,maxlength:32',
			   'description'=>zotop::t('请输入您的新密码，6~32位之间'),
			));
			
			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('确认新密码'),
			   'name'=>'newpassword2',
			   'value'=>'',
			   'valid'=>'required:true,equalTo:"#newpassword"',
			   'description'=>zotop::t('为确保安全，请再次输入您的新密码'),
			));
			
			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();        
    }
    
    public function onChangeInfo()
    {
		$user = zotop::model('zotop.user');
		$user->id = (int)zotop::user('id');
		
		if( form::isPostBack() )
		{
		    $post = form::post();
		    
		    $update = $user->update($post,$user->id);
		    if( $update )
		    {
		        msg::success('保存成功','资料设置成功，正在刷新页面，请稍后……','reload');   
		    }		    
		}
		
		$data = $user->read();
		$data['updatetime'] = TIME;
		    
        $page['title'] = '修改我的资料';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

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
			   'label'=>zotop::t('真实姓名'),
			   'name'=>'name',
			   'value'=>$data['name'],
			   'valid'=>'required:true',
			   'description'=>zotop::t(''),
			));			
			form::field(array(
			   'type'=>'radio',
			   'options'=>array('男'=>'男','女'=>'女'),
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

		page::bottom();
		page::footer();         
    }
}
?>