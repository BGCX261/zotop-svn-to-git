<?php
class zotop_controller_upload extends controller
{

	public function navbar($globalid='', $field='', $image='')
	{
		$navbar =  array(
			'imageLibrary'=> array('id'=>'imageLibrary','title'=>'图片库','href'=>zotop::url('zotop/upload/imageLibrary',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)))),
			'imageFromLocal' => array('id'=>'imageFromLocal','title'=>'本地上传','href'=>zotop::url('zotop/upload/imageFromLocal',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)))),
			'imageFromUrl' => array('id'=>'imageFromUrl','title'=>'网络图片','href'=>zotop::url('zotop/upload/imageFromUrl',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)))),
			'imagePreview' => array('id'=>'imagePreview','title'=>'图片预览','href'=>''),
		);

		if( !empty($image) )
		{
			$navbar['imagePreview'] = array('id'=>'imagePreview','title'=>'图片预览','href'=>zotop::url('zotop/upload/imagePreview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image))));
		}

		return $navbar;
	}

	public function actionIndex()
	{
		
	}

	public function actionImage($globalid='', $field='', $image='')
	{
		if ( empty($image) )
		{
			zotop::redirect('zotop/upload/imageFromLocal',array('globalid'=>$globalid, 'field'=>$field, 'image'=>''));
		}
		else
		{
			zotop::redirect('zotop/upload/imagePreview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)));
		}
	}

	public function actionImagePreview($globalid, $field, $image)
	{
		$upload = zotop::model('zotop.upload');

		$image  = $upload->read(array('path','=',$image));

		if(!is_array($image))
		{
			$image = array('path'=>$image);
		}

		$page = new dialog();
		$page->set('title','图片预览');
		$page->set('navbar', $this->navbar($globalid, $field, $image['path']));
		$page->set('image', $image);
		$page->display();		
	}

	public function actionImageFromLocal($globalid,$field,$image)
	{
		$upload = zotop::model('zotop.upload');

		$upload->alowexts = array('jpg','jpeg','gif','png','bmp');

		if ( form::isPostBack() )
		{
			$upload->bind('globalid', request::post('globalid'));
			$upload->bind('field', request::post('field'));
			$upload->bind('description', request::post('description'));

			$files = $upload->save();
			$image = $files[0];

			if ( $upload->error() == 0 && $image )
			{
				msg::success($upload->msg(), zotop::url('zotop/upload/imagePreview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image['path']))));
			}
			msg::error($upload->msg());
		}

		$page = new dialog();
		$page->set('title','本地上传');
		$page->set('navbar', $this->navbar($globalid, $field, $image));
		$page->set('alowexts', $upload->alowexts);
		$page->set('maxsize', $upload->maxsize);
		$page->display();
	}

	public function actionImageFromUrl($globalid,$field,$image)
	{
		if ( form::isPostBack() )
		{
			msg::error('远程获取中……');
		}
		$page = new dialog();
		$page->set('title','网络图片');
		$page->set('navbar',$this->navbar($globalid, $field, $image));
		$page->display();		
	}

}
?>