<?php
class upload_controller extends controller
{

	public function navbar()
	{
		return array(
			array('id'=>'default','title'=>'图片库','href'=>url::build('zotop/upload/default')),
			array('id'=>'upload','title'=>'本地上传','href'=>url::build('zotop/upload/image')),
			array('id'=>'bing','title'=>'Bing搜索','href'=>url::build('zotop/upload/bing')),
		);
	}

   public function onDefault()
   {
        $header['title'] = '图片库';

        dialog::header($header);
		dialog::navbar($this->navbar());

		form::header(array('title'=>'','description'=>'<span class="zotop-tip">请点击图片进行选择，然后插入被选择的图片</span>'));
		dialog::add(html::iframe('images','about:blank',array('width'=>'100%','style'=>'width:100%;height:200px;')));
		form::buttons(
		   array('id'=>'UploadImages','type'=>'submit','value'=>'插入图片'),
		   array(
			'type'=>'button',
			'value'=>zotop::t('取消'),
			'class'=>'zotop-dialog-close'
		   )
		);
		form::footer();
        dialog::footer();
   }

   public function onImage()
   {

        $header['title'] = '上传图片';
		$header['js'][] = url::module().'/admin/js/upload.js';
        dialog::header($header);
		dialog::navbar($this->navbar(),'upload');

			   form::header(array('title'=>'','description'=>'请选择本机图片并上传','class'=>'small'));

			   form::field(array(
				   'type'=>'text',
				   'label'=>zotop::t('图片地址'),
				   'name'=>'image',
				   'value'=>'http://www.baidu.com/logo.gif',
				   'description'=>'请输入图片地址测试赋值',
			   ));

				form::field(array(
				   'type'=>'textarea',
				   'label'=>zotop::t('图片说明'),
				   'name'=>'content',
				   'value'=>'',
			   ));

			   form::buttons(
				   array('id'=>'UploadImages','type'=>'submit','value'=>'上传图片'),
				   array(
					'type'=>'button',
					'value'=>zotop::t('取消'),
					'class'=>'zotop-dialog-close'
				   )
			   );
			   form::footer();


       dialog::footer();
   }

   public function onBing()
   {
        $header['title'] = 'Bing搜索';

        dialog::header($header);
		dialog::navbar($this->navbar(),'bing');

		form::header(array('title'=>'','description'=>'<span class="zotop-tip">请先搜索图片，然后插入</span>'));
		dialog::add(html::iframe('images','about:blank',array('width'=>'100%','style'=>'width:100%;height:200px;')));
		form::buttons(
		   array('id'=>'UploadImages','type'=>'submit','value'=>'插入图片'),
		   array(
			'type'=>'button',
			'value'=>zotop::t('取消'),
			'class'=>'zotop-dialog-close'
		   )
		);
		form::footer();
        dialog::footer();
   }

}
?>