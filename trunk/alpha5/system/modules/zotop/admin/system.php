<?php
class system_controller extends controller
{
    public function indexAction()
    {}
    
    public function aboutAction()
    {
        $page = new dialog();
        $page->title = 'About';        
        $page->display();
    }
    
    public function sideAction()
    {
        $tools = array(
            'reboot'=>array('id'=>'reboot','title'=>'系统重启','href'=>zotop::url('zotop/system/reboot')),
            'close'=>array('id'=>'close','title'=>'关闭网站','href'=>zotop::url('zotop/system/close')),
            'config'=>array('id'=>'config','title'=>'注册表管理','href'=>zotop::url('zotop/config')),
            
        );
        $tools = zotop::filter('zotop.system.tools',$tools);

		$users = array(
            'user'=>array('id'=>'reboot','title'=>'系统用户管理','href'=>zotop::url('zotop/user')),
            'usergroup'=>array('id'=>'close','title'=>'系统用户组管理','href'=>zotop::url('zotop/usergroup')),
        );

		$modules = array(
            'list'=>array('id'=>'list','title'=>'模块列表','href'=>zotop::url('zotop/module')),
            'install'=>array('id'=>'uninstalled','title'=>'安装新模块','href'=>zotop::url('zotop/module/uninstalled')),
        );
        
        $page = new side();
        $page->title = '系统控制面板'; 
        $page->set('tools',$page->navlist($tools));
        $page->set('users',$page->navlist($users)); 
		$page->set('modules',$page->navlist($modules)); 
        $page->display();        
    }
}
?>