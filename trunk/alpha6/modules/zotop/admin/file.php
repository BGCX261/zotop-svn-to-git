<?php
class zotop_controller_file extends controller
{

	public function navbar()
	{
		return array(
			'index' => array('id'=>'index', 'title'=>zotop::t('文件管理'), 'href'=>zotop::url('zotop/file/index')),
			'upload' => array('id'=>'upload', 'title'=>zotop::t('文件上传'), 'href'=>zotop::url('zotop/file/upload'), 'class'=>'dialog'),
		);
	}

	public function actionIndex($type='')
	{
		$file = zotop::model('zotop.file');
		
		if ( !empty($type) )
		{
			$where = array('type','=',$type);
		}
		
		$files = $file->db()->where($where)->orderby('createtime','desc')->getPage();
		
		$pagination = new pagination();
		$pagination->page = $files['page'];
		$pagination->pagesize = $files['pagesize'];
		$pagination->total = $files['total'];
		$p = $pagination->render();


		$totalsize = $file->totalsize();
		$totalcount = $file->count();
	
		$page = new page();
		$page->set('title',zotop::t('文件管理'));
		$page->set('navbar',$this->navbar());
		$page->set('page',$files['page']);
		$page->set('pagesize',$files['pagesize']);
		$page->set('total',$files['total']);
		$page->set('files',$files['data']);
		$page->set('totalsize',$totalsize);
		$page->set('totalcount',$totalcount);
		$page->set('pagination',$p);
		$page->display();
	}

	public function actionUpload()
	{
	
	}
	
	public function actionDownload($id)
	{
	
	}

	public function actionDelete($id)
	{
		$file = zotop::model('zotop.file');

		$delete = $file->delete($id);

		if ( $delete )
		{
			msg::success('删除成功',request::referer());
		}		
	}


}
?>