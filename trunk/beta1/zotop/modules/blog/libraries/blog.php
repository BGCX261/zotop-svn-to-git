<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * blog模块类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class blog
{
	public static function config($name,$value=null)
	{
		return zotop::config('blog.'.$name);
	}

	public static function info()
	{
		zotop::dump(dirname(dirname(dirname(__FILE__))).DS.'module.php');
	}

}