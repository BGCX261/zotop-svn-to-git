<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * admin操作类，后台操作相关类
 *
 * @package		zotop
 * @class		admin_base
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class admin_base
{
	public static function theme()
	{
        $theme = zotop::config('admin.theme');
        $theme = empty($theme) ? 'system' : $theme;
        
        return $theme;		
	}

	public static function template($file='')
	{
		$template = ZOTOP_PATH_THEMES.DS.admin::theme().DS.'template';

		if ( !empty($file) )
		{	
			$template .= DS.$file;
		}
		return $template;
	}

	public static function user()
	{
	
	}


}
?>