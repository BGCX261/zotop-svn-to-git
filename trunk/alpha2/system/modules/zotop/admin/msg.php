<?php
class msg_controller extends controller
{
   public function onDefault($status = -1)
   {
        $header['title'] = '短消息';

        page::header($header);
		   page::top();
		   page::navbar(array(
				array('id'=>'main','title'=>'短消息列表','href'=>url::build('zotop/msg')),
				array('id'=>'send','title'=>'发送短消息','href'=>url::build('zotop/msg/send'),'class'=>'dialog {width:600}'),
		   ),'main');

			   echo '<div style="padding:4px 15px;">';
			   echo '<table class="list">';
			   echo '<tr><td class="list-side">程序版本：</td><td>'.zotop::config('zotop.version').'</td></tr>';
			   echo '<tr><td class="list-side">程序设计：</td><td>'.zotop::config('zotop.author').'</td></tr>';
			   echo '<tr><td class="list-side">程序开发：</td><td>'.zotop::config('zotop.authors').'</td></tr>';
			   echo '<tr><td class="list-side">官方网站：</td><td><a href="'.zotop::config('zotop.homepage').'" target="_blank">'.zotop::config('zotop.homepage').'</a></td></tr>';
			   echo '<tr><td class="list-side">安装时间：</td><td>'.zotop::config('zotop.install').'</td></tr>';

			   echo '</table>';
			   echo '</div>';

		   page::bottom();
       page::footer();
   }

   public function onUnread()
   {
		echo '{title:"未读短消息",num:5}';
   }

   public function onSend()
   {
        $header['title'] = '发送短消息';

        dialog::header($header);


			   form::header(array('title'=>'','description'=>'请输入收信人的账户并输入短消息内容','class'=>'small'));

			   form::field(array(
				   'type'=>'text',
				   'label'=>zotop::t('收信人'),
				   'name'=>'sendto',
				   'value'=>'',
				   'description'=>'请输入收信人的账户名称，多个账户之间请用’逗号‘隔开',
			   ));

				form::field(array(
				   'type'=>'textarea',
				   'label'=>zotop::t('内 容'),
				   'name'=>'content',
				   'value'=>'',
				   //'description'=>'',
			   ));

			   form::buttons(
				   array('type'=>'submit','value'=>'发 送'),
				   array(
					'type'=>'button',
					'name'=>'close',
					'value'=>'取消',
					'class'=>'zotop-dialog-close'
				   )
			   );
			   form::footer();


       dialog::footer();
   }

}
?>