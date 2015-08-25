<?php
class webftp_controller_file extends controller
{

    public function actionEdit($file='')
    {
		$file = empty($file) ? zotop::get('file') : $file;
		$file = trim(url::decode($file),'/');
		$filepath = ZOTOP_PATH_ROOT.DS.str_replace('/',DS,$file);

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

		
		
		$filecontent = file::read($filepath);
		
		$page = new dialog();
		$page->title = '文件编辑器';
		$page->set('file',$file);
		$page->set('filepath',$filepath);
		$page->set('filecontent',$filecontent);
		$page->display();
    }
}
?>