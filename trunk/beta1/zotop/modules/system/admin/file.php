<?php
class system_controller_file extends controller
{

	public function navbar()
	{
		return array(
			'index' => array('id'=>'index', 'title'=>zotop::t('文件管理'), 'href'=>zotop::url('system/file/index')),
			'upload' => array('id'=>'upload', 'title'=>zotop::t('文件上传'), 'href'=>zotop::url('system/file/upload'), 'class'=>'dialog'),
		);
	}

	public function actionSide()
	{
		$folder = zotop::model('system.folder');
		
		$folders = $folder->getAll();		
		$tree = new tree($folders,0);
		$folders_tree = $tree->getHtml(0,'<span class="zotop-icon zotop-icon-folder"></span><a href="'.zotop::url('system/file/index/all/$id').'" target="mainIframe">$title</a>',$folderid);

		$page = new side();
		$page->set('title',zotop::t('文件管理'));
		$page->set('folderid',$folderid);
		$page->set('folders_tree', $folders_tree);
		$page->display();

	}

	public function actionIndex($type='all', $folderid=0)
	{
		$file = zotop::model('system.file');
		$folder = zotop::model('system.folder');
		
		if ( !empty($type) && $type != 'all' )
		{
			$where = array('file.type','=',$type);
		}
		
		$files = $file->db()->select('file.*','user.name as user_name','user.username as user_username')->join('user','user.id','file.userid')->where($where)->orderby('file.createtime','desc')->getPage();

		//zotop::dump($file->db()->Sql());

		$types = $file->types();
		
		$pagination = new pagination();
		$pagination->page = $files['page'];
		$pagination->pagesize = $files['pagesize'];
		$pagination->total = $files['total'];
		$p = $pagination->render();
		$totalsize = $file->totalsize($where);
		$totalcount = $file->count();
		
		$folders = $folder->getAll();		
		$tree = new tree($folders,0);
		$position = $tree->getPosition($folderid);
		foreach($position as $p)
		{
			$pos[zotop::url('system/file/index/'.$type.'/'.$p['id'])] = $p['title'];
		}
	
		$page = new page();
		$page->set('title',zotop::t('文件管理'));
		$page->set('position',array(
			zotop::url('system/file') => zotop::t('文件管理'),
			zotop::url('system/file/'.$type.'/0') => zotop::t('全部文件')
		) + (array)$pos + array('列表'));
		$page->set('navbar',$this->navbar());
		$page->set('page',$files['page']);
		$page->set('pagesize',$files['pagesize']);
		$page->set('total',$files['total']);
		$page->set('files',$files['data']);
		$page->set('totalsize',$totalsize);
		$page->set('totalcount',$totalcount);
		$page->set('pagination',$p);
		$page->set('types',$types);
		$page->set('type',$type);
		$page->set('folderid',$folderid);
		$page->display();
	}

	public function actionUpload()
	{
		
	}
	
	public function actionDownload($id)
	{
	
	}

	public function actionEdit($id)
	{
		$file = zotop::model('system.file');
		$data = $file->read($id);

		$file->update(array('status'=>1),$id);

		zotop::dump($data);
	}

    public function actionEditor($file)
    {
		$filepath = realpath(ZOTOP_PATH_ROOT.DS.trim($file,'/'));

		if ( empty($file) )
		{
			return false;
		}

        if(form::isPostBack())
        {
            
            $content = zotop::post('source');
			$content = trim($content);
			if ( empty($content) ) 
			{
				msg::error('内容为空，无法保存！');
			}

			file::write($filepath, trim($content));
            
            msg::success('内容保存成功！');
        }

		
		
		$content = file::read($filepath);
		
		$page = new dialog();
		$page->title = '文件编辑器';
		$page->set('file',$file);
		$page->set('filepath',$filepath);
		$page->set('content',$content);
		$page->display();
    }

	public function actionDelete($id)
	{
		$file = zotop::model('system.file');

		$delete = $file->delete($id);

		if ( $delete )
		{
			msg::success('删除成功',url::referer());
		}		
	}


}
?>