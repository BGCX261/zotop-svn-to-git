<?php
if(!defined('ZOTOP_SYSTEM')){
    define('ZOTOP_SYSTEM',dirname(__FILE__));
}
define('ZOTOP',dirname(ZOTOP_SYSTEM));
define('ZOTOP_WEB',ZOTOP.DS.'web');
define('ZOTOP_LIB',ZOTOP_SYSTEM.DS.'libraries');
define('ZOTOP_CONFIG',ZOTOP_WEB.DS.'config');
define('ZOTOP_CACHE',ZOTOP_WEB.DS.'cache');
define('ZOTOP_BACKUP',ZOTOP_WEB.DS.'backup');
define('ZOTOP_DATA',ZOTOP_WEB.DS.'data');
define('ZOTOP_RUNTIME',ZOTOP_WEB.DS.'runtime');
//加载核心文件
if( is_file(ZOTOP_APP.DS.'~runtime.php') ){
    require ZOTOP_APP.DS.'~runtime.php';
}
else
{
    //加载系统核心
    require ZOTOP_LIB.DS.'zotop'.DS.'core'.DS.'zotop.php';
	//注册别名，自动加载系统库文件
	zotop::register(include(ZOTOP_LIB.DS.'alias.php'));
	zotop::register(include(ZOTOP_APP.DS.'alias.php'));
}
//配置的初始化
if(is_file(ZOTOP_RUNTIME.DS.'~config.php') )
{
	zotop::config(include(ZOTOP_RUNTIME.DS.'~config.php'));
}
else
{
	zotop::config(include(ZOTOP_SYSTEM.DS.'config.php'));
	zotop::config(include(ZOTOP_CONFIG.DS.'config.php'));
	zotop::config('zotop.database',include(ZOTOP_CONFIG.DS.'database.php'));
	zotop::config('zotop.application',include(ZOTOP_CONFIG.DS.'application.php'));
	zotop::config('zotop.module',include(ZOTOP_CONFIG.DS.'module.php'));
	zotop::config('zotop.router',include(ZOTOP_CONFIG.DS.'router.php'));
	//缓存配置文件
	zotop::data(ZOTOP_RUNTIME.DS.'~config.php',zotop::config());
}
//启动底层系统
zotop::boot();
//hook的初始化
if( is_file(ZOTOP_RUNTIME.DS.'~hook.php') )
{
	zotop::load(ZOTOP_RUNTIME.DS.'~hook.php');
}
else
{
	//生成全局hook文件
}
?>