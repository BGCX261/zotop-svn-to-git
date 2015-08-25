<?php
//定义常量
define('DS',DIRECTORY_SEPARATOR);
define('ZOTOP',true);
//路径常量
define('ZPATH_ROOT',dirname(ZPATH_SYSTEM));
define('ZPATH_LIBRARIES',ZPATH_SYSTEM.DS.'libraries');
define('ZPATH_MODULES',ZPATH_SYSTEM.DS.'modules');
define('ZPATH_CONFIG',ZPATH_SYSTEM.DS.'config');
define('ZPATH_BACKUP',ZPATH_SYSTEM.DS.'backup');
define('ZPATH_DATA',ZPATH_SYSTEM.DS.'data');
define('ZPATH_CACHE',ZPATH_DATA.DS.'cache');
define('ZPATH_RUNTIME',ZPATH_SYSTEM.DS.'runtime');
//url常量
defined('ZURL_ROOT') OR define('ZURL_ROOT',dirname($_SERVER['SCRIPT_NAME']));


//加载系统核心
if( file_exists(ZPATH_RUNTIME.DS.APP_NAME.'.php') )
{   
    require ZPATH_RUNTIME.DS.APP_NAME.'.php';
    //加载常用参数
    zotop::config(include(ZPATH_RUNTIME.DS.'config.php'));
    zotop::load(ZPATH_RUNTIME.DS.'hook.php');
    //系统启动
    zotop::boot();
}
else
{
    //加载系统核心
    require ZPATH_LIBRARIES.DS.'zotop'.DS.'classes'.DS.'zotop.php';
    //注册别名，自动加载系统库文件
    zotop::register(include(ZPATH_LIBRARIES.DS.'zotop'.DS.'library.php'));
    zotop::register(include(APP_ROOT.DS.'library.php'));     
    //重新加载系统
    zotop::reboot();
}


?>