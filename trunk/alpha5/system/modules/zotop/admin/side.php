<?php
class side_controller extends controller
{
    public function indexAction()
    {        
        $apps = array();
        $modules = (array)zotop::module();
        
        foreach($modules as $module)
        {
            if( $module['type']  == 'plugin' )
            {
                $apps[] = $module;
            }
        }
        $page = new page();
        $page->set('apps',$apps);
        $page->addScript('$common/js/side.js');
        $page->body = array('class'=>'side');
        $page->display();
    }
}
?>