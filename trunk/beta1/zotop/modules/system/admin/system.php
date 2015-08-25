<?php
class system_controller_system extends controller
{
	public function navbar()
	{
		$navbar = array(
			'index'=>array('id'=>'index','title'=>'首 页','href'=>zotop::url('system/system')),
		);

		$navbar = zotop::filter('system.system.navbar',$navbar);

		return $navbar;
	}

    public function actionIndex()
    {
        $page = new page();
        $page->title = zotop::t('系统管理'); 
		$page->navbar = $this->navbar(); 
        $page->display();
    }

	public function actionSide()
    {
        
        $modules = (array)zotop::module();
        
        foreach($modules as $id=>$module)
        {
            if( $module['type']  != 'com' )
            {
                unset($modules[$id]);
            }
            else
            {
            $modules[$id]['href'] = zotop::url($module['id']);
            }             
        }

                
        $page = new side();
        $page->title = '系统管理'; 
        $page->set('modules',$modules);
        $page->display();        
    }

    
    public function actionAbout()
    {
        $page = new dialog();
        $page->title = 'About';        
        $page->display();
    }
    

}
?>