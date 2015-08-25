<?php
class system_controller_template extends controller
{

    public function navbar()
    {
        return array();
    }

    public function actionIndex()
    {
 		$page = new page();        
		$page->title = zotop::t('模板管理');
		$page->set('navbar',$this->navbar());
		$page->display();         
    }

	public function actionSelect($field='',$dir='')
	{	
		$dir = empty($dir) ? zotop::get('dir') : $dir;
		$dir = trim(url::decode($dir),'/');

		$path = site::template();
		$path = $path.DS.str_replace('/',DS,$dir);
		$path = path::clean($path);

		$folders = folder::folders($path);
		$files = folder::files($path);

		$position = '<a href="'.zotop::url('system/template/select').'">'.zotop::t('根目录').'</a><em> : //</em> ';		
		if ( !empty($dir) )
		{
			$dirs = arr::dirpath($dir, '/');
			foreach($dirs as $d)
			{
				$position .= '<a href="'.zotop::url('system/template/select',array('dir'=> rawurlencode($d[1]))).'">'.$d[0].'</a> <em>/</em>';
			}
		}

 		$page = new dialog();        
		$page->title = zotop::t('模板管理');
		$page->set('field',$field);
		$page->set('dir',$dir);
		$page->set('position',$position);
		$page->set('folders',$folders);
		$page->set('files',$files);
		$page->display();  		
	}

	public function actionEditor($file='')
	{
		$file = empty($file) ? zotop::get('file') : $file;
		$file = trim(url::decode($file),'/');
		$filepath = site::template($file);

		if ( form::isPostBack() )
		{
			$filecontent = zotop::post('filecontent');
			if ( file::write($filepath, $filecontent) )
			{
				msg::success('保存成功');
			}
			msg::error('保存失败');
		}
		
		$filecontent = file::read($filepath);

 		$page = new dialog();        
		$page->title = zotop::t('模板编辑器');
		$page->set('file',$file);
		$page->set('filepath',$filepath);
		$page->set('filecontent',$filecontent);
		$page->display();  	
	}

	public function actionRename($file='')
	{
		$file = empty($file) ? zotop::get('file') : $file;
		$file = trim(url::decode($file),'/');
		$filepath = site::template($file);

		if ( form::isPostBack() )
		{
			$newname = zotop::post('newname');

			if ( file::rename($filepath, $newname) )
			{
				msg::success('重命名成功');
			}
			msg::error('重命名失败');
		}
		
 		$page = new dialog();        
		$page->title = zotop::t('模板编辑器');
		$page->set('file',$file);
		$page->set('filepath',$filepath);
		$page->display();	
	}

	public function actionNewfile($dir='')
	{
		$dir = empty($dir) ? zotop::get('dir') : $dir;
		$dir = trim(url::decode($dir),'/');
		
		$file = 'newfile.php';

		if ( form::isPostBack() )
		{
			$file = zotop::post('name');
			$title = zotop::post('title');
			$description = zotop::post('description');
			$filepath = site::template($dir.DS.$file);
			$filecontent = "<?php
/**
 * title:$title
 * description:$description
*/
?>";

			if ( file::exists($filepath) )
			{
				msg::error(zotop::t('新建失败，当前目录已经存在文件：{$file}',array('file'=>$file)));
			}

			if ( file::write($filepath,$filecontent) )
			{
				msg::success('新建文件成功');
			}
			msg::error('新建文件失败');
		}
		
 		$page = new dialog();        
		$page->title = zotop::t('模板编辑器');
		$page->set('dir',$dir);
		$page->set('file',$file);
		$page->display();	
	}

	public function actionDelete($file)
	{
		$file = empty($file) ? zotop::get('file') : $file;
		$file = trim(url::decode($file),'/');
		$filepath = site::template($file);
		
		if ( file::delete($filepath) )
		{
			msg::success('删除成功',url::referer());
		}
		msg::error('删除失败');
	}
}
?>