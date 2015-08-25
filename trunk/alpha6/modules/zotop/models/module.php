<?php
class zotop_model_module extends model
{
	protected $_key = 'id';
	protected $_table = 'module';
	
	
	public function getIndex($type = '')
	{
		$groups = zotop::data('module');
				
		foreach($groups as $group)
		{
		    if( !empty($type) )
		    {
		        if( $group['type'] == $type && $group['status'] >= 0 )
    		    {
    		        $index[$group['id']] = $group['title'];
    		    }		        
		    }
		    else
		    {
		        $index[$group['id']] = $group['title'];
		    }			
		}
		
		return $index;
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
	    $installed = (array) $this->getInstalled();
	    $folders = dir::folders(ZOTOP_PATH_MODULES,'.',false);       

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
	
	public function install($path)
	{
		
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

		if ($insert)
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
	    return $this->delete($id);
	}
	
	public function cache($reload=false)
	{
	    $name = $this->table();
	    
	    $data = zotop::data($name);
	    
		//设置缓存数据
		if ( $reload || $data===null )
		{
			$data = $this->getAll($sql);
			$data = arr::hashmap($data, $this->key());
			
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