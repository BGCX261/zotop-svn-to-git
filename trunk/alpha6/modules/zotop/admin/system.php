<?php
class zotop_controller_system extends controller
{
    public function indexAction()
    {}
    
    public function actionAbout()
    {
        $page = new dialog();
        $page->title = 'About';        
        $page->display();
    }
    
    public function actionSide()
    {
        
        $modules = (array)zotop::modules();
        
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
        $page->title = '系统控制面板'; 
        $page->set('modules',$modules);
        $page->display();        
    }
}
?>