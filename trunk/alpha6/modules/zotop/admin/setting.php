<?php
class zotop_controller_setting extends controller
{
    public function navbar()
    {
        $navbar = array(
            'index'=>array('id'=>'index', 'title'=>'站点设置', 'href'=>zotop::url('zotop/setting/index')),
            'upload'=>array('id'=>'upload', 'title'=>'上传设置', 'href'=>zotop::url('zotop/setting/upload')),
        );
        
        return $navbar;
    }
    
	public function actionIndex($id='site')
	{
	    $config = zotop::model('zotop.config');

	    if ( form::isPostBack() )
	    {
	        $post = form::post();
			
			$save = $config->save($post);
			
			if( $save )
			{
				msg::success('保存成功，重新加载中，请稍后……');
			}		
			msg::error($save);	        
	    }

	    $fields = $config->fields('site');
	    	    	    
	    $page = new page();        
		$page->set('title', '系统设置');
		$page->set('navbar',$this->navbar());
		$page->set('fields',$fields);		
		$page->display();	    
	}
	
	public function actionUpload()
	{
	    $config = zotop::model('zotop.config');

	    if ( form::isPostBack() )
	    {
	        $post = form::post();
			
			$save = $config->save($post);
			
			if( $save )
			{
				msg::success('保存成功，重新加载中，请稍后……');
			}		
			msg::error($save);	        
	    }

	    $fields = $config->fields('upload');
	    	    	    
	    $page = new page();        
		$page->set('title', '上传设置');
		$page->set('navbar',$this->navbar());
		$page->set('fields',$fields);		
		$page->display();	    
	}
}
?>