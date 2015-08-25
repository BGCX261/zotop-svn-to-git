<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的模块类，完成对模块的基本操作
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class module_model extends model
{
	protected $primaryKey = 'id';
	protected $tableName = 'module';
	
    public function install($id,$path='')
    {
        $path = empty($path) ? $id : $path;
        $modulePath = ZOTOP_MODULES.DS.$id;
        $modulePath = path::clean($modulePath);
        $moduleFile = $modulePath.DS.'module.php';
        
        $module = @include($moduleFile);
        
        $module['path'] = $path;
        $module['type'] = '1';
        $module['url'] = $path;
        $module['status'] = 1;
        $module['order'] = $this->max()+1;
        $module['installtime'] = time::now();
        $module['updatetime'] = time::now();
        
        if( is_array($module) )
        {
            $insert = $this->insert($module);
            if( $insert )
            {
                return $this->reload();
            }
        }
        return false;
    }
    
    public function uninstall($id)
    {
        
    }
    
    public function reload()
    {
        $data = $this->getAll(array(
            'select'=>'*',
            'where'=>array('status','>',0),
            'orderby'=>'order desc,updatetime desc',
        ));        
        if ( is_array($data) )
        {
            foreach( $data as $module )
            {
                $modules[$module['id']] = $module;
            }
            zotop::data('zotop.config.module',$modules);  
        }
        return true;
    }
    
    /**
     * 获取已经安装过的模块名称
     *
     */
    public function datalist()
    {
        $data = $this->getAll(array(
            'select'=>'*',
            'orderby'=>'order desc,updatetime desc',
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
    
    public function notInstalled()
    {
        $datalist = (array)$this->datalist();
        $folders = dir::folders(ZOTOP_MODULES,'.',false);       
        $folders = array_diff($folders,array_keys($datalist));

        $modules = array();
        foreach($folders as $folder)
        {
            $modulePath = ZOTOP_MODULES.DS.$folder;
            $moduleUrl = url::modules().'/'.$folder;
            $moduleFile = $modulePath.DS.'module.php';
            
            if( file::exists($moduleFile) )
            {
                $m = include($moduleFile);
                if( !isset($m['icon']) )
                {
                    $m['icon'] = $moduleUrl.'/icon.gif';
                    if( !file::exists($m['icon']) )
                    {
                        $m['icon'] = url::theme().'/image/icon/module.gif';    
                    }
                }
                $modules[$m['id']] = $m;
            }
        }
        return $modules;       
    }
}
?>