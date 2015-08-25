<?php
class application extends application_base
{
	public static $uri = 'site';
    
	public static function theme()
    {
		//系统默认返回的主题为system
        $theme = zotop::config('site.theme');
        $theme = empty($theme) ? 'default' : $theme;        
        return $theme;
    }
}
?>