<?php
class system_controller_module extends controller
{
    public function navbar()
    {
        return array(
			'index'=>array('id'=>'index','title'=>'模块管理','href'=>zotop::url('system/module/index')),
			'uninstalled'=>array('id'=>'uninstalled','title'=>'模块安装','href'=>zotop::url('system/module/uninstalled')),
			//array('id'=>'add','title'=>'创建新模块','href'=>zotop::url('system/module/add')),
			//array('id'=>'edit','title'=>'模块设置','href'=>''),
		);
    }

    public function actionIndex()
    {        
		$module = zotop::model('system.module');
		
        $modules = $module->cache();

		$page = new page();
        $page->set('title','模块管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('modules',$modules);
        $page->display();   
    }
    
    
    
    public function actionUninstalled()
    {
		$module = zotop::model('system.module');		
        $modules = $module->getUnInstalled();   

		$page = new page();
        $page->set('title','模块安装 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('modules',$modules);
        $page->display();           
    }
    
  
    public function actionInstall($id='')
    {
        
		$id = empty($id) ? zotop::post('id') : $id;

		$module = zotop::model('system.module');

		$modules = $module->getUnInstalled();

		if ( !isset($modules[$id]) )
		{
			msg::error(array(
				'title'=>zotop::t('安装失败'),
				'content'=>zotop::t('未找到模块<b>{$id}</b>，请检查',array('id'=>$id)),
			));	
		}

       
        if( form::isPostBack() )
        {
            $install = $module->install($modules[$id]['path']);

            if( $install )
            {         
                $module->cache(true);

                msg::success(zotop::t('模块安装成功'),zotop::url('system/module'));
        
            }
        }

		$moduleFile = $modules[$id]['path'].DS.'module.php';
		
		if ( !file::exists($moduleFile) )
		{
            msg::error(array(
				'title'=>zotop::t('安装失败'),
				'content'=>zotop::t('未找到模块标记文件<b>module.php</b>，请检查'),
			));			
		}

		
		$page = new dialog();
        $page->set('title','安装模块');
        $page->set('module',$modules[$id]);
        $page->display();           
    }
    
    public function actionUninstall($id)
    {
        $module = zotop::model('system.module');
        $module->id = $id;
        $module->read();
        
        if( $module->type == 'core' )
        {
            msg::error('系统模块不能被卸载！');
        }
        
        $uninstall = $module->uninstall($id);
        
        if( $uninstall )
        {
            $module->cache(true); 
            msg::success(zotop::t('模块卸载成功'),zotop::url('system/module'));
        }
    }
    
    public function actionAbout($id)
    {
        $module = zotop::model('system.module');		
        $module->id = $id;
        $data = $module->read();
        
		$page = new dialog();
        $page->set('title','模块简介');
        $page->set('module',$data);
        $page->display();        
    }

    public function actionStatus($id,$status=-1)
    {
        $module = zotop::model('system.module');
        $module->id = $id;
        $module->read();
        
        if( $module->type == 'core' )
        {
            msg::error('系统模块不允许被禁用！');
        }
                
        $update = $module->update(array('status'=>(int)$status), $id);
        
        if( !$module->error() )
        {
			$module->cache(true);
            msg::success('操作成功',zotop::url('system/module'));   
        }

		msg::error($module->msg());
    }

}
?>