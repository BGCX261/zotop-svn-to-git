<?php
class index_controller extends controller
{
    public function navbar()
    {
		$navbar = array(
			array('id'=>'default','title'=>'文件管理','href'=>url::build('filemanager/index')),
		);

		$navbar = zotop::filter('filemanager.index.navbar',$navbar);

		return $navbar;
    }

    public function onDefault($dir='')
    {
		$path = ROOT.DS.trim($dir,DS);
        $path = path::clean($path);

        $folders = dir::folders($path);
        $files = dir::files($path);
        $fileext = array('php','css','js','jpg','jpeg','gif','png','bmp','psd','html','htm','tpl','rar','zip','mp3');

        $page['title'] = '文件管理器';




        page::header($page);

		page::add('<div id="page" class="clearfix">');
		page::add('<div id="main">');
		page::add('<div id="main-inner">');

        page::top();
        page::navbar($this->navbar(),'default');



        $column = array();
    	$column['select'] = '';
    	$column['name'] = '名称';
    	$column['type'] = '类型';
    	$column['size w60'] = '大小';
    	$column['atime w120'] = '创建时间';
    	$column['mtime w120'] = '修改时间';
    	$column['manage rename w80'] = '重命名';
    	$column['manage edit w80'] = '编辑';
    	$column['manage delete'] = '删除';

        table::header('list',$column);
        foreach($folders as $folder)
        {
            $column = array();
            $column['select w20 center'] = html::image(url::theme().'/image/fileext/folder.gif');
            $column['name'] = '<a href="'.zotop::url('filemanager/index/default',array('dir'=>$dir.DS.$folder)).'"><b>'.$folder.'</b></a>';
        	$column['type w60'] = '文件夹';
        	$column['size w60'] = '--';
        	$column['atime w120'] = time::format(@fileatime($path.DS.$folder));
        	$column['mtime w120'] = time::format(@filemtime($path.DS.$folder));
        	$column['manage rename w80'] = '<a>重命名</a>';
        	$column['manage edit w80'] = '<a class="disabled">编辑</a>';
        	$column['manage delete'] = '<a>删除</a>';
            table::row($column);
        }
        foreach($files as $file)
        {
            $column = array();
            $column['select w20 center'] = in_array(file::ext($file),$fileext) ? html::image(url::theme().'/image/fileext/'.file::ext($file).'.gif') : html::image(url::theme().'/image/fileext/unknown.gif');
            $column['name'] = '<a href="'.zotop::url('filemanager/index/default',array('dir'=>$dir.DS.$file)).'"><b>'.$file.'</b></a>';
        	$column['type w60'] = '文件';
        	$column['size w60'] = format::byte(@filesize($path.DS.$file));;
        	$column['atime w120'] = time::format(@fileatime($path.DS.$file));
        	$column['mtime w120'] = time::format(@filemtime($path.DS.$file));
        	$column['manage rename w80'] = '<a>重命名</a>';
        	$column['manage edit w80'] = '<a href="'.zotop::url('filemanager/file/edit',array('filename'=>$dir.DS.$file,'dir'=>'***')).'">编辑</a>';
        	$column['manage delete'] = '<a>删除</a>';
            table::row($column);
        }
        table::footer();

        page::bottom();


		page::add('</div>');
		page::add('</div>');
		page::add('<div id="side">');

			block::header('快捷操作');
				echo '<ul class="list">';
				echo '<li class="file"><a href="'.zotop::url('zotop/file/newfile').'" class="dialog">新建文件</a></li>';
				echo '<li class="folder"><a href="'.zotop::url('zotop/file/newfolder').'" class="dialog">新建文件夹</a></li>';
				echo '<li class="folder"><a href="'.zotop::url('zotop/file/upload').'" class="dialog">文件上传</a></li>';
				echo '</ul>';
			block::footer();

			block::header('其他位置');
				echo '<ul class="list">';
				echo '<li class="root"><a>根目录</a></li>';
				echo '<li class="root"><a>模板目录</a></li>';
				echo '<li class="root"><a>模块目录</a></li>';
				echo '<li class="root"><a>缓存目录</a></li>';
				echo '</ul>';
			block::footer();

		page::add('</div>');
		page::add('</div>');

		 page::footer();
    }
}
?>