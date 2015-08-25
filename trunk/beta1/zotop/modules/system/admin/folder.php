<?php
class system_controller_folder extends controller
{
    
	public function navbar($id=0)
	{
		return array(
			'index' => array('id'=>'index','title'=>'分类管理','href'=>zotop::url('system/folder/index/'.$id)),
			'add' => array('id'=>'add','title'=>'添加分类','href'=>zotop::url('system/folder/add/'.$id),'class'=>'dialog'),
		);
	}

	public function actionIndex($parentid=0)
    {
		$folder = zotop::model('system.folder');

		if ( form::isPostBack() )
		{
			$post = form::post();

			foreach( (array)$post['id'] as $i=>$id )
			{
				$folder->update(array('order'=>$i+1),$id);
			}
			
			if ( !$folder->error() )
			{
				msg::success('保存成功',zotop::url('system/folder/index'));
			}
			msg::error($folder->msg());
		}		
			
		$folders = $folder->getAll();		
		$tree = new tree($folders,0);
		$rows = $tree->getChild($parentid);
		$position = $tree->getPosition($parentid);
		foreach($position as $p)
		{
			$pos[zotop::url('system/folder/index/'.$p['id'])] = $p['title'];
		}
		

        $page = new page();
        $page->set('title', zotop::t('文件管理'));
		$page->set('navbar', $this->navbar($parentid));
		$page->set('position',array(
			zotop::url('system/file') => zotop::t('文件管理'),
			zotop::url('system/folder') => zotop::t('分类管理'),
			//zotop::url('system/folder/index/0') => zotop::t('根分类')
		) + (array)$pos + array('列表'));
		$page->set('folders', $folders);
		$page->set('rows', $rows);
		$page->set('hash', $hash);
        $page->display();
    }

	public function actionAdd($parentid=0)
    {
        $folder = zotop::model('system.folder');
		
		if ( form::isPostBack() )
		{
			$post = form::post();

			$folder->save($post);

			if ( !$folder->error() )
			{
				msg::success('保存成功',zotop::url('system/folder/index/'.$parentid));
			}
			msg::error($folder->msg());
		}

		$folders = $folder->getAll();		
		$tree = new tree($folders,0);


		$data['parentid'] = $parentid;
		$data['parentid_options'] = $tree->getOptionsArray();
		
				
		$page = new dialog();
        $page->set('title', '添加分类');
		$page->set('referer', $referer);
		$page->set('data', $data);
        $page->display();
    }

	public function actionEdit($id)
    {
		$folder = zotop::model('system.folder');

		if ( form::isPostBack() )
		{
			$post = form::post();

			if( $post['id'] == $post['parentid'] )
			{
				msg::error('上级分类不能和当前分类相同');
			}

			$folder->update($post,$id);

			if ( !$folder->error() )
			{
				msg::success('保存成功',zotop::url('system/folder/index/'.$post['parentid']));
			}
			msg::error($folder->msg());
		}
		
		$folder->id = $id;
		$data = $folder->read();

		$folders = $folder->getAll();		
		$tree = new tree($folders,0);

		$data['parentid_options'] = $tree->getOptionsArray();

        $page = new dialog();
        $page->set('title', '编辑分类');
		$page->set('data', $data);
        $page->display();
    }

	public function actionDelete($id)
    {
		$folder = zotop::model('system.folder');
		$folder->id = $id;
		$folder->read();

		if( $folder->count('parentid',$folder->id) > 0 )
		{
			msg::error('删除失败，请先删除子类别');
		}

		$folder->delete();		
		if ( !$folder->error() )
		{
			msg::success('删除成功', zotop::url('system/folder/index/'.$folder->parentid));
		}
		msg::error($folder->msg());
    }
}
?>