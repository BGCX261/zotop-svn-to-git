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

   public function indexAction()
   {

   }

   public function imageAction()
   {


   }

   public function bingAction()
   {

   }

}
?>