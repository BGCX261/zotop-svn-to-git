<?php
class module_controller extends controller
{
    public function navbar()
    {
        return array(
			array('id'=>'index','title'=>'模块管理','href'=>zotop::url('zotop/module/index')),
			array('id'=>'uninstalled','title'=>'安装新模块','href'=>zotop::url('zotop/module/uninstalled')),
			//array('id'=>'add','title'=>'创建新模块','href'=>zotop::url('zotop/module/add')),
			//array('id'=>'edit','title'=>'模块设置','href'=>''),
		);
    }

    public function indexAction()
    {
		$module = zotop::model('zotop.module');
		
        $modules = $module->getAll(array(
            //'where'=>array('type','=','system'),
            'orderby'=>array('order'=>'asc'),
        ));

		$page = new page();
        $page->set('title','系统模块管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('modules',$modules);
        $page->display();   
    }
    
    
    
    public function uninstalledAction($id='')
    {
		$module = zotop::model('zotop.module');		
        $modules = $module->getUnInstalled();
        
        if( !empty($id) && isset($modules[$id]) )
        {
            zotop::redirect('zotop/module/license',array('id'=>$id));
            exit();          
        }

		$page = new page();
        $page->set('title','系统模块管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('modules',$modules);
        $page->display();           
    }
    
    public function licenseAction($id)
    {
        $module = zotop::model('zotop.module');		
        $modules = $module->getUnInstalled();
        
        if( empty($id) || !isset($modules[$id]) )
        {
            msg::error(zotop::t('ID为<b>{$id}</b>的模块不存在，请确认是否已经上传该模块？'));
        }
        
        $licenseFile = $modules[$id]['path'].DS.'license.txt';
        
        if( !file::exists($licenseFile))
        {
            zotop::redirect('zotop/module/install',array('id'=>$id));
            exit();                    
        }
        
        $license = file::read($licenseFile);
        
		$page = new dialog();
        $page->set('title','许可协议');
		$page->set('license',html::decode($license));
		$page->set('next',zotop::url('zotop/module/install',array('id'=>$id)));
        $page->display();         
            
    }
    
    public function installAction($id)
    {
        $module = zotop::model('zotop.module');		
        $modules = $module->getUnInstalled();
        
        if( empty($id) || !isset($modules[$id]) )
        {
            msg::error(zotop::t('模块<b>{$id}</b>不存在或者已经安装',array('id'=>$id)));
        }
        
        if( form::isPostBack() )
        {
            $install = $module->install($modules[$id]);
            if( $install )
            {         
                $module->cache();
                msg::success(zotop::t('模块 <b>{$name}</b> 安装成功',$modules[$id]),zotop::url('zotop/module'));
        
            }
        }

		$page = new dialog();
        $page->set('title','安装模块');
        $page->set('module',$modules[$id]);
        $page->display();           
    }
    
    public function uninstallAction($id)
    {
        $module = zotop::model('zotop.module');
        $module->id = $id;
        $module->read();
        
        if( $module->type == 'system' )
        {
            msg::error('系统模块不能被卸载！');
        }
        
        $uninstall = $module->uninstall($id);
        
        if( $uninstall )
        {
            $module->cache(); 
            msg::success(zotop::t('模块卸载成功'),zotop::url('zotop/module'));
        }
    }
    
    public function aboutAction($id)
    {
        $module = zotop::model('zotop.module');		
        $module->id = $id;
        $data = $module->read();
        
		$page = new dialog();
        $page->set('title','模块简介');
        $page->set('module',$data);
        $page->display();        
    }

    public function lockAction($id,$status=-1)
    {
        $module = zotop::model('zotop.module');
        $module->id = $id;
        $module->read();
        if( $module->type == 'system' )
        {
            msg::error('系统模块不允许被禁用！');
        }
        
        $post = array(
            'status'=>(int)$status,
        );
        
        $update = $module->update($post, $id);
        if( $update )
        {
			$module->cache();
            msg::success('操作成功，正在刷新页面，请稍后……',zotop::url('zotop/module'));   
        }        
    }

}
?>