<?php
class module_model extends model
{
	protected $primaryKey = 'id';
	protected $tableName = 'module';
	
	
	public function getIndex($type = '')
	{
		$groups = zotop::data('cache.module');
				
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
        $data = $this->getAll(array(
            'select'=>'*',
            'orderby'=>array('order'=>'asc','updatetime'=>'desc'),
        ));
        
        if ( is_array($data) )
        {
            foreach( $data as $module )
            {
                $modules[$module['id']] = $module;
            }
            return $modules;  
        }        
        return array();
    }	
	
	public function getUnInstalled()
	{
	    $installed = (array) $this->getInstalled();
	    $folders = dir::folders(ZPATH_MODULES,'.',false);       
        $folders = array_diff($folders,array_keys($installed));
        
        $modules = array();
        foreach($folders as $folder)
        {
            $modulePath = ZPATH_MODULES.DS.$folder;
            $moduleUrl = url::modules().'/'.$folder;
            $moduleFile = $modulePath.DS.'module.php';
            
            if( file::exists($moduleFile) )
            {
                $m = include($moduleFile);
                $m['path'] = '$modules/'.$folder;
                $m['url'] = '$modules/'.$folder;
                if( !isset($m['icon']) )
                {
                    if( !file::exists($modulePath.'/icon.png') )
                    {
                        $m['icon'] = url::theme().'/image/skin/none.png';    
                    }
                    else
                    {
                        $m['icon'] = $moduleUrl.'/icon.png';
                    }
                }
                $modules[$m['id']] = $m;
            }
        }
        return $modules;  
	}
	
	public function install($module)
	{
	    if( !is_array($module) )
	    {
	        $module = include(ZPATH_MODULES.DS.$module.DS.'module.php');
	    }
	    
	    $module['type'] = empty($module['type']) ? 'plugin' : $module['type'];
	    $module['status'] = 0;
	    $module['order'] = $this->max('order') + 1;
	    $module['installtime'] = TIME;
	    $module['updatetime'] = TIME;
	    $insert = $this->insert($module);
	    
	    return true;
	}
	
	public function uninstall($id)
	{
	    return $this->delete($id);
	}
	
	public function cache()
	{
	    $data = array();
	    $rows = $this->getAll(array(
	        'where'=>array('status','>=','0')
	    ));
	    foreach( $rows as $row )
	    {
	       $data[$row['id']] = $row; 
	    }
	    zotop::data('module', $data);
	    zotop::reboot();  
	    return $data;
	}

}
?>