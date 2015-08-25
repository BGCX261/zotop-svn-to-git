<?php
//定义常量
define('DS',DIRECTORY_SEPARATOR);
define('TIME',time());
define('ZOTOP',true);
define('DEBUG',false);
define('MAGIC_QUOTES_GPC', (bool) get_magic_quotes_gpc());

//定义启动数据
define('ZOTOP_START_TIME',microtime(TRUE));
define('ZOTOP_START_MEMORY',memory_get_usage());

//定义路径常量
define('ZOTOP_PATH_ROOT',dirname(dirname(__FILE__)));
define('ZOTOP_PATH_SYSTEM',ZOTOP_PATH_ROOT.DS.'system');
define('ZOTOP_PATH_MODULES',ZOTOP_PATH_ROOT.DS.'modules');
define('ZOTOP_PATH_APPLICATION',ZOTOP_PATH_SYSTEM);
define('ZOTOP_PATH_LIBRARIES',ZOTOP_PATH_SYSTEM.DS.'libraries');
define('ZOTOP_PATH_BACKUP',ZOTOP_PATH_SYSTEM.DS.'backup');
define('ZOTOP_PATH_DATA',ZOTOP_PATH_SYSTEM.DS.'data');
define('ZOTOP_PATH_CACHE',ZOTOP_PATH_SYSTEM.DS.'cache');
define('ZOTOP_PATH_RUNTIME',ZOTOP_PATH_SYSTEM.DS.'runtime');

//定义URL常量
define('ZOTOP_URL_ROOT', dirname($_SERVER['SCRIPT_NAME']));
define('ZOTOP_URL_SYSTEM', ZOTOP_URL_ROOT.'/'.basename(ZOTOP_PATH_SYSTEM));
define('ZOTOP_URL_MODULES', ZOTOP_URL_ROOT.'/'.basename(ZOTOP_PATH_MODULES));

//定义app常量
define('ZOTOP_APP_NAME',ZOTOP_APP);
define('ZOTOP_APP_ROOT',ZOTOP_PATH_APPLICATION.DS.ZOTOP_APP_NAME);
define('ZOTOP_APP_URL',ZOTOP_URL_SYSTEM.'/'.ZOTOP_APP_NAME);

//加载系统核心文件
if ( file_exists(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'.php') && !DEBUG )
{
    require ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'.php';
    //加载hook文件
    zotop::load(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'_hooks.php');
    //加载配置参数
    zotop::config(include(ZOTOP_PATH_RUNTIME.DS.DS.'config.php'));
    //系统启动
    zotop::boot();
}
else
{
    //加载系统核心
    require ZOTOP_PATH_LIBRARIES.DS.'zotop'.DS.'classes'.DS.'zotop.php';
    //注册自动加载系统库文件
    zotop::register(include(ZOTOP_PATH_LIBRARIES.DS.'zotop'.DS.'classes.php'));
    zotop::register(include(ZOTOP_APP_ROOT.DS.'libraries'.DS.'classes.php'));
    
    //重载系统
    zotop::build();
}