<?php
class system_model_module extends model
{
	protected $_key = 'id';
	protected $_table = 'module';
	

	public function getAll()
	{
		$data = $this->db()->orderby('order','asc')->getAll();
		$data = arr::hashmap($data,'id');
		return $data;
	}

	public function types($type='')
	{
		$types = array(
			'core' => zotop::t('核心模块'),
			'com' => zotop::t('功能模块'),
			'plugin' => zotop::t('插件模块'),
		);

		if ( empty($type) )
		{
			return $types;
		}
		return $types[$type];
	}

	public function getActive()
	{
		$active = array();

		$modules = zotop::module();
				
		foreach($modules as $module)
		{
			if ( $module['status']  >= 0 )
			{
				$active[$module['id']] = $module;
			}
		}
		
		return $active;
	}
	
    /**
     * 获取已经安装过的模块名称
     *
     */
    public function getInstalled()
    {
        $data = $this->db()->select('*')->orderby(array('order'=>'asc','updatetime'=>'desc'))->getAll();
        
        if ( is_array($data) )
        {
            $modules = arr::hashmap($data, $this->key());

            return $modules;
        }        
        return array();
    }	
	
	public function getUnInstalled()
	{
	    //获取已经安装的模块
		$installed = (array) $this->getInstalled();
		
		//获取目录
	    $folders = folder::folders(ZOTOP_PATH_MODULES,false);       

	    $modules = array();

        foreach($folders as $folder)
        {
            $modulePath = ZOTOP_PATH_MODULES.DS.$folder;
            $moduleUrl = '$modules/'.$folder;
            $moduleFile = $modulePath.DS.'module.php';

            $m = @include($moduleFile);            
            
            if (  is_array($m) && isset($m['id']) && !in_array($m['id'], array_keys($installed)) )
            {
                $m['path'] = '$modules/'.$folder;
                $m['url'] = '$modules/'.$folder;
                if( !isset($m['icon']) )
                {
                    if( file::exists($modulePath.'/icon.png') )
                    {
                        $m['icon'] = $moduleUrl.'/icon.png';
                    }
                }
                $modules[$m['id']] = $m;
            }

        }

        return $modules;  
	}
	
	public function install($path='')
	{
		$path = empty($path) ? $this->path : $path;
		
		$module = @include(path::decode($path.DS.'module.php'));
		

		if ( is_array($module) )
		{
	        $module['path'] = $path;
            $module['url'] = $path;
			if( !isset($module['icon']) )
			{
				if( file::exists($path.'/icon.png') )
				{
					$module['icon'] = $module['url'].'/icon.png';
				}
			}
			$module['type'] = empty($module['type']) ? 'plugin' : $module['type'];
			$module['status'] = 0;
			$module['order'] = $this->max('order') + 1;
			$module['installtime'] = TIME;
			$module['updatetime'] = TIME;

			$insert = $this->insert($module);
		}

		if ( $insert )
		{
			$driver = $this->db()->config('driver');
			
			$sqls = file::read($path.DS.'install'.DS.$driver.'.sql');

			if ($sqls)
			{
				$this->db()->run($sqls);
			}
		}

	    return true;
	}
	
	public function uninstall($id)
	{
	    $this->delete($id);
		$this->cache($true);
	}
	
	public function cache($flush=false)
	{
	    $name = $this->table();	    
	    $data = zotop::data($name);	    
		if ( $flush || $data === null )
		{
			$data = $this->getAll();			
    		if( is_array($data) )
    		{
    		    zotop::data($name, $data);
    		}
    		//重启系统
    		zotop::reboot();
		}		
		return $data;
	}

}
?>