<?php
class system_controller_image extends controller
{

	public function navbar()
	{
		$navbar =  array(
			'library'=> array('id'=>'library','title'=>'图片库','href'=>zotop::url('system/image/library')),
			'upload' => array('id'=>'upload','title'=>'本地上传','href'=>zotop::url('system/image/upload')),
			'preview' => array('id'=>'preview','title'=>'图片预览','href'=>''),
			'url' => array('id'=>'url','title'=>'网络图片','href'=>zotop::url('system/image/url')),			
		);

		return $navbar;
	}

	public function actionIndex()
	{

	}

	public function actionLibrary($folderid=0)
	{
		
		
		$folder = zotop::model('system.folder');

		$folders = $folder->getAll();		
		$tree = new tree($folders,0);
		$folders_tree = $tree->getHtml(0,'<span class="zotop-icon zotop-icon-folder"></span><a href="'.zotop::url('system/image/browser/$id').'" target="browserIframe">$title</a>',$folderid);
		
		$page = new dialog();
		$page->set('title','图片库');
		$page->set('navbar', $this->navbar());
		$page->set('folders_tree', $folders_tree);
		$page->display();			
	}

	public function actionBrowser($folderid=0)
	{
		$file = zotop::model('system.file');

		if ( $folderid == 0 )
		{
			$images = $file->db()->where('type','=','image')->orderby('createtime','desc')->getPage();
		}
		else
		{
			$images = $file->db()->where('type','=','image')->where('folderid','=',$folderid)->orderby('createtime','desc')->getPage();
		}
		
		$pagination = new pagination();
		$pagination->page = $images['page'];
		$pagination->pagesize = $images['pagesize'];
		$pagination->total = $images['total'];
		$p = $pagination->render();

		$page = new page();
		$page->set('title','图片库');
		$page->set('image', $image);
		$page->set('page',$images['page']);
		$page->set('pagesize',$images['pagesize']);
		$page->set('total',$images['total']);
		$page->set('images',$images['data']);
		$page->set('pagination',$p);
		$page->display();	
	}

	public function actionLocation($globalid)
	{
		$file = zotop::model('system.file');
		$images = $file->db()->where('type','=','image')->where('globalid','=',$globalid)->orderby('createtime','desc')->getAll();
		
		$page = new page();
		$page->set('title','已上传图片');
		$page->set('globalid',$globalid);		
		$page->set('images', $images);
		$page->display();
	}

	public function actionPreview($id)
	{
		$file = zotop::model('system.file');
		$image = $file->read(array('id','=',$id));

		$page = new dialog();
		$page->set('title','图片预览');
		$page->set('image', $image);
		$page->display();		
	}

	public function actionUpload()
	{
		$file = zotop::model('system.file');

		$file->upload->allowexts = 'jpg|jpeg|png|gif';

		$folder = zotop::model('system.folder');		

		if ( form::isPostBack() )
		{
			$file->globalid = zotop::post('globalid');
			$file->field = zotop::post('field');
			$file->description = zotop::post('description');

			$files = $file->upload();

			if ( !$file->error() && isset($files[0]['path']) )
			{
				msg::success('图片上传成功', zotop::url('system/image/upload'));
			}

			msg::error($file->msg());
		}

		$folders = $folder->getAll();		
		$tree = new tree($folders,0);
		$categorys = $tree->getOptionsArray();

		$page = new dialog();
		$page->set('title','本地上传');
		$page->set('navbar', $this->navbar());
		$page->set('allowexts', $file->upload->allowexts);
		$page->set('maxsize', $file->upload->maxsize);
		$page->set('categorys', $categorys);
		$page->display();
	}

	public function actionUrl()
	{
		if ( form::isPostBack() )
		{
			msg::error('远程获取中……');
		}
		$page = new dialog();
		$page->set('title','网络图片');
		$page->set('navbar',$this->navbar());
		$page->set('image',$image);
		$page->display();		
	}

	public function actionEdit($id)
	{
		$file = zotop::model('system.file');
		if ( form::isPostBack() )
		{
			$post = form::post();

			$file->update($post,$id);

			if ( !$file->error() )
			{
				msg::success('编辑成功');
			}
			msg::error($file->msg());
		}
		

		$folder = zotop::model('system.folder');
		$image = $file->read(array('id','=',$id));

		$folders = $folder->getAll();		
		$tree = new tree($folders,0);
		$categorys = $tree->getOptionsArray();

		$page = new dialog();
		$page->set('title','图片编辑');
		$page->set('image', $image);
		$page->set('categorys', $categorys);
		$page->display();		
	}

	public function actionDelete($id, $referer='')
	{
		$file = zotop::model('system.file');

		$delete = $file->delete($id);

		if ( $delete )
		{
			msg::success('图片删除成功', url::referer());
		}
	}

}
?>