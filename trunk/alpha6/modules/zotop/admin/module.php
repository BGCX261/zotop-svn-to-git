<?php
class zotop_controller_module extends controller
{
    public function navbar()
    {
        return array(
			array('id'=>'index','title'=>'系统模块管理','href'=>zotop::url('zotop/module/index')),
			array('id'=>'uninstalled','title'=>'模块安装','href'=>zotop::url('zotop/module/uninstalled')),
			//array('id'=>'add','title'=>'创建新模块','href'=>zotop::url('zotop/module/add')),
			//array('id'=>'edit','title'=>'模块设置','href'=>''),
		);
    }

    public function actionIndex()
    {        
		$module = zotop::model('zotop.module');
		
        $modules = $module->db()->orderby('order','asc')->getAll();

		$page = new page();
        $page->set('title','系统模块管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('modules',$modules);
        $page->display();   
    }
    
    
    
    public function actionUninstalled($path='')
    {
		$module = zotop::model('zotop.module');		
        $modules = $module->getUnInstalled();
        
        if( !empty($path) )
        {
            zotop::redirect('zotop/module/license',array('path'=>url::encode($path)));
            exit();          
        }

		$page = new page();
        $page->set('title','系统模块管理 ');
        $page->set('position',$position);
        $page->set('navbar',$this->navbar());        
		$page->set('modules',$modules);
        $page->display();           
    }
    
    public function actionLicense($path)
    {
        $module = zotop::model('zotop.module');		
        $modules = $module->getUnInstalled();
        
        if( empty($path) || !dir::exists($path) )
        {
            msg::error(array(
				'content'=>zotop::t('模块不存在，请确认是否已经上传该模块？'),
				'description'=>zotop::t("路径：{$path}"),
			));
        }
        
        $licenseFile = $path.DS.'license.txt';
        
        if( !file::exists($licenseFile))
        {
            zotop::redirect('zotop/module/install',array('path'=>url::encode($path)));
            exit();                    
        }
        
        $license = file::read($licenseFile);
        
		$page = new dialog();
        $page->set('title','许可协议');
		$page->set('license',html::decode($license));
		$page->set('next',zotop::url('zotop/module/install',array('path'=>url::encode($path))));
        $page->display();         
            
    }
    
    public function actionInstall($path)
    {
        $module = zotop::model('zotop.module');		
       
        if( form::isPostBack() )
        {
            $install = $module->install($path);

            if( $install )
            {         
                $module->cache(true);

                msg::success(zotop::t('模块安装成功'),zotop::url('zotop/module'));
        
            }
        }

		$moduleFile = $path.DS.'module.php';
		
		if ( !file::exists($moduleFile) )
		{
            msg::error(array(
				'content'=>zotop::t('未找到模块标记文件<b>module.php</b>，请检查？'),
			));			
		}

		$m = @include(path::decode($moduleFile));

		$id = $m['id'];

		$modules = $module->getUnInstalled();
		
		$page = new dialog();
        $page->set('title','安装模块');
        $page->set('module',$modules[$id]);
        $page->display();           
    }
    
    public function actionUninstall($id)
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
            $module->cache(true); 
            msg::success(zotop::t('模块卸载成功'),zotop::url('zotop/module'));
        }
    }
    
    public function actionAbout($id)
    {
        $module = zotop::model('zotop.module');		
        $module->id = $id;
        $data = $module->read();
        
		$page = new dialog();
        $page->set('title','模块简介');
        $page->set('module',$data);
        $page->display();        
    }

    public function actionLock($id,$status=-1)
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
			$module->cache(true);
            msg::success('操作成功，正在刷新页面，请稍后……',zotop::url('zotop/module'));   
        }        
    }

}
?>