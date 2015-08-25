<?php
class LoginController extends controller
{
   public function onDefault()
   {
        $header['title'] = '用户登录';
        $header['js'] = '
        	$(function(){
				$("div.block").show().center().drag(".block-header");
				window.onresize=function(){
					$("div.block").center();
				};
   		})
        ';
        $header['body']['class']="login";


        page::header($header);

           block::header(array('id'=>'LoginWindow','title'=>'用户登录'));

           form::header(array('title'=>'','description'=>'请输入用户名和密码','class'=>'ajax'));

           form::add(array(
               'type'=>'text',
               'label'=>t('帐 户(U)'),
			   'name'=>'username',
               'value'=>'',
               //'description'=>'请输入您的用户名或者电子信箱',
           ));

            form::add(array(
               'type'=>'password',
               'label'=>t('密 码(P)'),
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

}
?>