<?php
class UploadController extends controller
{
   public function onDefault()
   {
        $header['title'] = '图片库';

        page::header($header);
		   page::top();

		   page::bottom();
       page::footer();
   }

   public function onImage()
   {
        $header['title'] = '上传图片';
		$header['js'][] = url::module().'/admin/js/upload.js';
        dialog::header($header);

			   form::header(array('title'=>'','description'=>'','class'=>'ajax'));

			   form::add(array(
				   'type'=>'text',
				   'label'=>t('图片地址'),
				   'name'=>'image',
				   'value'=>'http://www.baidu.com/logo.gif',
				   'description'=>'请输入图片地址测试赋值',
			   ));

				form::add(array(
				   'type'=>'textarea',
				   'label'=>t('图片说明'),
				   'name'=>'content',
				   'value'=>'',
			   ));

			   form::buttons(
				   array('id'=>'UploadImage','type'=>'button','value'=>'上传图片'),
				   array(
					'type'=>'button',
					'value'=>'取消',
					'class'=>'zotop-dialog-close'
				   )
			   );
			   form::footer();


       dialog::footer();
   }

}
?>