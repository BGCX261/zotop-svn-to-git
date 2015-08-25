<?php
class system_controller_index extends controller
{
    public function actionIndex()
    {
        $page = new page();
        $page->title = '系统管理中心';
        $page->body = array('class'=>'frame');
        $page->set('user',$this->user);
        $page->display();
    }

    public function navbar()
	{
		$navbar = array(
			'main'=>array('title'=>'首页','href'=>url::build('system/index/main')),
			'phpinfo'=>array('title'=>'phpinfo','href'=>url::build('system/index/phpinfo')),
		);

		$navbar = zotop::filter('system.main.navbar',$navbar);

		return $navbar;
	}
    
    public function actionMain()
    {
  
        $page = new page();
        $page->title = '控制中心';
        $page->set('navbar',$this->navbar());
        $page->set('user',$this->user);
        $page->display();
    }
    
    public function actionSide()
    {
        
        $m = array();

        $modules = (array)zotop::module();
        
        
        
        foreach($modules as $module)
        {
            if( $module['type']  == 'com' )
            {
                $module['href'] = zotop::url($module['id']);
                $m[$module['id']] = $module;                
            }
        }
                
        $page = new side();
        $page->set('modules',$page->navlist($m));
        $page->display();        
    }
    
    public function actionPhpinfo()
    {
	    ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_clean();

		if( preg_match('/<body><div class="center">([\s\S]*?)<\/div><\/body>/',$phpinfo,$match) )
		{
		    $phpinfo =$match[1];
		}

	    $phpinfo = str_replace('class="e"','style="color:#ff6600;"',$phpinfo);
		$phpinfo = str_replace('class="v"','',$phpinfo);
		$phpinfo = str_replace('<table','<table class="table list" style="table-layout:fixed;"',$phpinfo);
		$phpinfo = str_replace('<tr class="h">','<tr class="title">',$phpinfo);
	    $phpinfo = preg_replace('/<a href="http:\/\/www.php.net\/"><img(.*)alt="PHP Logo" \/><\/a><h1 class="p">(.*)<\/h1>/',"<h1>\\2</h1>",$phpinfo);
        
	    $page = new page();
	    $page->title = 'PHP探针';
	    $page->set('navbar',$this->navbar());
	    $page->set('phpinfo',$phpinfo);
	    $page->display();   
    }

	public function actionImage()
	{
	    $page = new page();
	    $page->title = 'css image';
	    $page->display(); 		
	}

}
?>