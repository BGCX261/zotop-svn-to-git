<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * site操作类，后台操作相关类
 *
 * @package		zotop
 * @class		admin_base
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class site_base
{
	public static function theme()
	{
        $theme = zotop::config('site.theme');
        $theme = empty($theme) ? 'default' : $theme;
        
        return $theme;		
	}

	public static function template($file='')
	{
		$template = ZOTOP_PATH_THEMES.DS.site::theme().DS.'template';

		if ( !empty($file) )
		{	
			$template .= DS.str_replace('/',DS,trim($file,'/'));
		}
		return $template;
	}
	
	public static function user()
	{
		
	}


}
?>