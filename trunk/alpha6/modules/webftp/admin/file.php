<?php
class webftp_controller_file extends controller
{

    public function actionEdit($file)
    {
		$filepath = realpath(ZOTOP_PATH_ROOT.DS.trim($file,'/'));

		if ( empty($file) )
		{
			return false;
		}

        if(form::isPostBack())
        {
            
            $content = request::post('source');
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
}
?>