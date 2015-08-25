<?php
class zotop_controller_image extends controller
{

	public function navbar($globalid='', $field='', $image='')
	{
		$navbar =  array(
			'library'=> array('id'=>'library','title'=>'图片库','href'=>zotop::url('zotop/image/library',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)))),
			'upload' => array('id'=>'upload','title'=>'本地上传','href'=>zotop::url('zotop/image/upload',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)))),
			'preview' => array('id'=>'preview','title'=>'图片预览','href'=>''),
			'url' => array('id'=>'url','title'=>'网络图片','href'=>zotop::url('zotop/image/url',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)))),			
		);

		if( !empty($image) && strtolower(substr($image,0,7)) != 'http://' && file_exists(ZOTOP_PATH_ROOT.DS.$image) )
		{
			$navbar['preview'] = array('id'=>'preview','title'=>'图片预览','href'=>zotop::url('zotop/image/preview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image))));
		}

		return $navbar;
	}

	public function actionIndex($globalid='', $field='', $image='')
	{
		if( strtolower(substr($image,0,7)) == 'http://' )
		{
			zotop::redirect('zotop/image/url',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)));
		}
		elseif( !empty($image) )
		{
			zotop::redirect('zotop/image/preview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)));
		}
		else
		{
			zotop::redirect('zotop/image/upload',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)));
		}
	}

	public function actionLibrary($globalid, $field, $image='')
	{
		if ( empty($image) )
		{
			//zotop::redirect('zotop/image/upload',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)));
		}
		
		$file = zotop::model('zotop.image');

		$images = $file->db()->where('type','=','image')->where('globalid','=',$globalid)->orderby('createtime','desc')->getPage();

		$pagination = new pagination();
		$pagination->page = $images['page'];
		$pagination->pagesize = $images['pagesize'];
		$pagination->total = $images['total'];
		$p = $pagination->render();
		
		$page = new dialog();
		$page->set('title','图片库');
		$page->set('globalid',$globalid);
		$page->set('field',$field);
		$page->set('navbar', $this->navbar($globalid, $field, $image));
		$page->set('image', $image);
		$page->set('page',$images['page']);
		$page->set('pagesize',$images['pagesize']);
		$page->set('total',$images['total']);
		$page->set('images',$images['data']);
		$page->set('pagination',$p);
		$page->display();			
	}

	public function actionPreview($globalid, $field, $image)
	{
		if( !file_exists(ZOTOP_PATH_ROOT.DS.$image) )
		{
			zotop::redirect('zotop/image/upload',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($image)));
		}

		$file = zotop::model('zotop.image');

		if( $file->isExist('path', $image) )
		{
			$image = $file->read(array('path','=',$image));
		}
		else
		{
			$image = array('path'=>$image);
		}

		$page = new dialog();
		$page->set('title','图片预览');
		$page->set('navbar', $this->navbar($globalid, $field, $image['path']));
		$page->set('image', $image);
		$page->display();		
	}

	public function actionUpload($globalid,$field,$image='')
	{
		$file = zotop::model('zotop.image');

		if ( form::isPostBack() )
		{
			$file->globalid = $globalid;
			$file->field = $field;
			$file->description = request::post('description');

			$files = $file->upload();

			if ( !$file->error() && isset($files[0]['path']) )
			{
				msg::success('图片上传成功', zotop::url('zotop/image/preview',array('globalid'=>$globalid, 'field'=>$field, 'image'=>url::encode($files[0]['path']))));
			}

			msg::error($file->msg());
		}

		$page = new dialog();
		$page->set('title','本地上传');
		$page->set('navbar', $this->navbar($globalid, $field, $image));
		$page->set('alowexts', $file->upload->alowexts);
		$page->set('maxsize', $file->upload->maxsize);
		$page->display();
	}

	public function actionUrl($globalid,$field,$image)
	{
		if ( form::isPostBack() )
		{
			msg::error('远程获取中……');
		}
		$page = new dialog();
		$page->set('title','网络图片');
		$page->set('navbar',$this->navbar($globalid, $field, $image));
		$page->set('image',$image);
		$page->display();		
	}

	public function actionDelete($image, $referer)
	{
		$file = zotop::model('zotop.image');

		$delete = $file->delete(array('path','=',$image));

		if ( $delete )
		{
			msg::success('图片删除成功', $referer);
		}
	}

}
?>