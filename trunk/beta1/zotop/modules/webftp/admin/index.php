<?php
class webftp_controller_index extends controller
{
	public function navbar($dir='/')
	{
		$navbar = array(
			array('id'=>'index','title'=>'文件管理','href'=>zotop::url('webftp/index/index',array('dir'=> rawurlencode($dir)))),
			array('id'=>'folder/new','title'=>'新建文件夹','href'=>zotop::url('webftp/folder/new',array('dir'=> rawurlencode($dir))), 'class'=>'dialog'),
			array('id'=>'file/new','title'=>'新建文件','href'=>zotop::url('webftp/file/new',array('dir'=> rawurlencode($dir))), 'class'=>'dialog'),
		);

		$navbar = zotop::filter('webftp.admin.index.navbar',$navbar);

		return $navbar;	
	}

    public function actionIndex($dir='')
    {
		$dir = empty($dir) ? zotop::get('dir') : $dir;
		$dir = url::decode($dir);


		$path = ZOTOP_PATH_ROOT.DS.trim($dir,DS);
		$path = path::clean($path);

		//获取当前目录的子目录及子文件
		$folders = (array)folder::folders($path);
        $files = (array)folder::files($path);

		$position = '<a href="'.zotop::url('webftp/index/index').'">root</a>';

		$dirs = arr::dirpath($dir, '/');

		foreach($dirs as $d)
		{
			$position .= ' <em>/</em> <a href="'.zotop::url('webftp/index/index',array('dir'=> rawurlencode($d[1]))).'">'.$d[0].'</a>';
		}

		$page = new page();
		$page->title = '文件管理器';
		$page->set('position',$position);
		$page->set('navbar',$this->navbar($dir));
		$page->set('folders',$folders);
		$page->set('files',$files); 
		$page->set('path',$path); 
		$page->set('dir',$dir);		
		$page->display();
    }
}
?>