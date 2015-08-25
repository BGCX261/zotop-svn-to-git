<?php
class system_controller_setting extends controller
{
    public function navbar()
    {
        $navbar = array(
            'index'=>array('id'=>'index', 'title'=>'站点设置', 'href'=>zotop::url('system/setting/index')),
            'upload'=>array('id'=>'upload', 'title'=>'上传设置', 'href'=>zotop::url('system/setting/upload')),
			'cache'=>array('id'=>'cache', 'title'=>'缓存设置', 'href'=>zotop::url('system/setting/cache')),
			'theme'=>array('id'=>'theme', 'title'=>'主题设置', 'href'=>zotop::url('system/setting/theme')),
			'email'=>array('id'=>'email', 'title'=>'邮件设置', 'href'=>zotop::url('system/setting/email')),
        );
        
		$navbar = zotop::filter('system.setting.navbar',$navbar);

        return $navbar;
    }
    
	public function actionIndex($id='site')
	{
	    $config = zotop::model('system.config');

	    if ( form::isPostBack() )
	    {
	        $post = form::post();
			
			$config->save($post);
			
			if( $config->error() )
			{
				msg::error($config->msg());
			}
			msg::success('保存成功');
				        
	    }

	    $fields = $config->fields('site');
	    	    	    
	    $page = new page();        
		$page->set('title', zotop::t('系统设置'));
		$page->set('navbar',$this->navbar());
		$page->set('fields',$fields);		
		$page->display();	    
	}
	
	public function actionUpload()
	{
	    $config = zotop::model('system.config');

	    if ( form::isPostBack() )
	    {
	        $post = form::post();
			
			$config->save($post);
			
			if( $config->error() )
			{
				msg::error($config->msg());
			}
			msg::success('保存成功');     
	    }

	    $fields = $config->fields('system.upload');
	    	    	    
	    $page = new page();        
		$page->set('title', zotop::t('系统设置'));
		$page->set('navbar',$this->navbar());
		$page->set('fields',$fields);		
		$page->display();	    
	}

	public function actionCache()
	{
	    $config = zotop::model('system.config');

	    if ( form::isPostBack() )
	    {
	        $post = form::post();
			
			$config->save($post);
			
			if( $config->error() )
			{
				msg::error($config->msg());
			}
			msg::success('保存成功');	        
	    }

	    $cache = $config->fields('system.cache');
		$memcache = $config->fields('system.cache.memcache');
	    	    	    
	    $page = new page();        
		$page->set('title', zotop::t('系统设置'));
		$page->set('navbar',$this->navbar());
		$page->set('cache',$cache);
		$page->set('memcache',$memcache);
		$page->display();	    
	}

	public function actionTheme()
	{
	    $config = zotop::model('system.config');

	    if ( form::isPostBack() )
	    {
	        $post = form::post();
			
			$config->save($post);
			
			if( $config->error() )
			{
				msg::error($config->msg());
			}
			msg::success('保存成功');	        
	    }

	    $theme = $config->fields('system.theme');

	    	    	    
	    $page = new page();        
		$page->set('title', zotop::t('系统设置'));
		$page->set('navbar',$this->navbar());
		$page->set('theme',$theme);
		$page->display();			
	}
}
?>