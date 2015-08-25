<?php
define('ZOTOP',true);
define('DEBUG',true);
define('ZOTOP_APPLICATION','admin');

//定义启动数据
define('DS',DIRECTORY_SEPARATOR);
define('ZOTOP_START_TIME',microtime(TRUE));
define('ZOTOP_START_MEMORY',memory_get_usage());
define('MAGIC_QUOTES_GPC', (bool) get_magic_quotes_gpc());

//定义系统路径
define('ZOTOP_PATH_ROOT',dirname(__FILE__));
define('ZOTOP_PATH_MODULES',ZOTOP_PATH_ROOT.DS.'modules');
define('ZOTOP_PATH_THEMES',ZOTOP_PATH_ROOT.DS.'themes');

//定义常用路径
define('ZOTOP_PATH_DATA', ZOTOP_PATH_ROOT.DS.'data');
define('ZOTOP_PATH_BACKUP', ZOTOP_PATH_DATA.DS.'backup');
define('ZOTOP_PATH_CACHE', ZOTOP_PATH_DATA.DS.'cache');
define('ZOTOP_PATH_RUNTIME', ZOTOP_PATH_DATA.DS.'runtime');
define('ZOTOP_PATH_APPLICATION', ZOTOP_PATH_ROOT.DS.'admin');


//定义URL路径及参数
define('ZOTOP_URL_ROOT', dirname($_SERVER['SCRIPT_NAME']));
define('ZOTOP_URL_MODULES', ZOTOP_URL_ROOT.'/modules');
define('ZOTOP_URL_THEMES', ZOTOP_URL_ROOT.'/themes');

define('ZOTOP_URL_COMMON', ZOTOP_URL_ROOT.'/admin/common');

//加载运行文件
if ( file_exists(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.core.php') && !DEBUG )
{
    require ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.core.php';
    //加载hook文件
    zotop::load(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.hooks.php');
    //加载配置参数
    zotop::config(include(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION.'.config.php'));
    //系统启动
    zotop::boot();
}
else
{
	//加载系统核心
	require ZOTOP_PATH_MODULES.DS.'system'.DS.'libraries'.DS.'zotop.php';

	//注册自动加载系统库文件
	zotop::register(@include(ZOTOP_PATH_MODULES.DS.'system'.DS.'libraries.php'));
	zotop::register(@include(ZOTOP_PATH_APPLICATION.DS.'libraries.php'));

	zotop::reboot();
}

//系统启动并运行
zotop::run('system.init');
zotop::run('system.route');
zotop::run('system.ready');
zotop::run('system.run');
zotop::run('system.shutdown');
?>