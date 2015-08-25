<?php
class login_controller extends controller
{

	public function __construct()
    {

    }

	public function onDefault()
	{
		if( form::isPostBack() )
		{
			$post = array();
			$post['username'] = request::post('username');
			$post['password'] = request::post('password');
			$post['logintime'] = time();

			$user = zotop::model('zotop.user');

			$data = $user->read($post['username'],'username');

			//zotop::dump($data);

			if( $data == false )
			{
				msg::error('登陆失败',zotop::t('账户名称`{$username}`不存在，请检查！',array('username'=>$post['username'])));
			}
			zotop::user($data);
			msg::success('登陆成功','登陆成功，系统正在加载中','reload',2);
		}

		if( zotop::user() != null )
		{
			zotop::redirect('zotop/index');
		}



		$header['title'] = '用户登录';
		$header['js'] = url::module().'/admin/js/login.js';
		$header['body']['class']="login";


		page::header($header);

		   block::header(array('id'=>'LoginWindow','title'=>'用户登录'));

		   form::header(array('title'=>'','description'=>'请输入用户名和密码','class'=>'small'));

		   form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('帐 户(U)'),
			   'name'=>'username',
			   'value'=>zotop::user('username'),
			   'valid'=>'required:true'
			   //'description'=>'请输入您的用户名或者电子信箱',
		   ));

			form::field(array(
			   'type'=>'password',
			   'label'=>zotop::t('密 码(P)'),
			   'name'=>'password',
			   'value'=>'',
			   //'description'=>'',
		   ));

		   form::buttons(
			   array('type'=>'submit','value'=>'登 陆'),
			   array(
				'type'=>'button',
				'name'=>'options',
				'value'=>'选 项',
			   )
		   );
		   form::footer();

		   block::footer();

	   page::footer();
	}

	public function onLogout()
	{
		zotop::user(null);
		msg::success('登出成功','登出成功，系统正在关闭中','reload',2);
	}

}
?>