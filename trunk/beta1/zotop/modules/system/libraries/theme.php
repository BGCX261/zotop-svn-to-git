<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * URL操作类，完成对URL的操作
 *
 * @package		zotop
 * @class		zotop_theme
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class theme_base
{
	public function name()
	{
	
	}

	public function url()
	{
		return ZOTOP_URL_THEMES.'/'.application::theme();
	}

	public function path()
	{
		
	}


	public function folders($path='')
	{
		$folders = folder::folders($path,false,false);
		
		foreach( $folders as $folder )
		{
			
		}

		return $folders;
	}

	public function files($path='')
	{
		$files = folder::files($path,false,true);

		return $files;
	}



	public function template($path='')
	{
		$path = trim($path,'/');
		if ( empty($path) )
		{
			$path = application::module().'/'.application::controller().'/'.application::action().'.php';
		}		
		$template = ZOTOP_PATH_THEMES.DS.application::theme().DS.'template'.DS.str_replace('/',DS,$path);
		return $template;
	}

}
?>