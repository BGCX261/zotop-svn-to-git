<?php
class application extends application_base
{
	public static $uri = 'system/index/index';
    
	public static function theme()
    {
		//系统默认返回的主题为system
        $theme = zotop::config('system.theme');
        $theme = empty($theme) ? 'system' : $theme;        
        return $theme;
    }
}
?>