<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<?php
        $column = array();
    	$column['w30 center'] = '';
    	$column['name'] = '名称';
    	$column['type'] = '类型';
    	$column['size w60'] = '大小';
    	$column['atime w120'] = '创建时间';
    	$column['mtime w120'] = '修改时间';
    	//$column['manage rename w80'] = '重命名';
    	//$column['manage edit w80'] = '编辑';
    	//$column['manage delete'] = '删除';

        table::header('list',$column);
        foreach($folders as $folder)
        {
            $column = array();
            $column['center'] = '<div class="zotop-icon zotop-icon-file folder"></div>';
            $column['name'] = '<a href="'.zotop::url('webftp/index/index',array('dir'=>rawurlencode($dir.'/'.$folder))).'"><b>'.$folder.'</b></a>';
			$column['name'] .= '<h5>';
			$column['name'] .= '<a href="'.zotop::url('webftp/folder/rename',array('dir'=>rawurlencode($dir), 'file'=>$file)).'" class="dialog">重命名</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('webftp/folder/delete',array('dir'=>rawurlencode($dir), 'file'=>$file)).'" class="dialog">删除</a>';
        	$column['name'] .= '</h5>';
        	$column['type w60'] = '文件夹';
        	$column['size w60'] = '--';
        	$column['atime w120'] = time::format(@fileatime($path.DS.$folder));
        	$column['mtime w120'] = time::format(@filemtime($path.DS.$folder));
        	//$column['manage rename w80'] = '<a>重命名</a>';
        	//$column['manage edit w80'] = '<a class="disabled">编辑</a>';
        	//$column['manage delete'] = '<a>删除</a>';
            table::row($column);
        }
        foreach($files as $file)
        {
            $column = array();
            $column['center'] = '<div class="zotop-icon zotop-icon-file '.file::ext($file).'"></div>';
            $column['name'] = '<div><b>'.$file.'</b></div>';
			$column['name'] .= '<h5>';
			$column['name'] .= '<a href="'.zotop::url('webftp/file/edit',array('file'=>rawurlencode($dir.'/'.$file))).'" class="dialog">编辑</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('webftp/file/rename',array('file'=>rawurlencode($dir.'/'.$file))).'" class="dialog">重命名</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('webftp/file/delete',array('file'=>rawurlencode($dir.'/'.$file))).'" class="dialog">删除</a>';
        	$column['name'] .= '</h5>';
			$column['type w60'] = '文件';
        	$column['size w60'] = format::byte(@filesize($path.DS.$file));
        	$column['atime w120'] = time::format(@fileatime($path.DS.$file));
        	$column['mtime w120'] = time::format(@filemtime($path.DS.$file));
        	//$column['manage rename w80'] = '<a>重命名</a>';
        	//$column['manage edit w80'] = '<a href="'.zotop::url('webftp/file/edit',array('filename'=>$dir.DS.$file,'dir'=>'***')).'">编辑</a>';
        	//$column['manage delete'] = '<a>删除</a>';
            table::row($column);
        }
        table::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>