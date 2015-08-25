<?php
class main_controller extends controller
{
    public function navbar()
	{
		$navbar = array(
			array('id'=>'main','title'=>'首页','href'=>url::build('zotop/main')),
			array('id'=>'phpinfo','title'=>'phpinfo','href'=>url::build('zotop/main/phpinfo')),
		);

		$navbar = zotop::filter('zotop.main.navbar',$navbar);

		return $navbar;
	}
    
    public function indexAction()
    {
  
        $page = new page();
        $page->title = '控制中心首页';
        $page->addCSS('$this/css/main.css');
        $page->set('navbar',$this->navbar());
        $page->set('user',$this->user);
        $page->display();
    }
    
    public function sideAction()
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
    
    public function phpinfoAction()
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
    


}
?>