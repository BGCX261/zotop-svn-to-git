<?php
define('ROOT',dirname(ZOTOP));
define('ZOTOP_LIBRARIES',ZOTOP.DS.'libraries');
define('ZOTOP_MODULES',ZOTOP.DS.'modules');
define('ZOTOP_CONFIG',ZOTOP.DS.'config');
define('ZOTOP_CACHE',ZOTOP.DS.'cache');
define('ZOTOP_BACKUP',ZOTOP.DS.'backup');
define('ZOTOP_DATA',ZOTOP.DS.'data');
define('ZOTOP_RUNTIME',ZOTOP.DS.'runtime');
define('TIME', time());
//加载核心文件
if( file_exists(ZOTOP_RUNTIME.DS.'~runtime.php') && $debug=='ddd' )
{
    require ZOTOP_RUNTIME.DS.'~runtime.php';
}
else
{
    //加载系统核心
    require ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'zotop.php';
    //注册别名，自动加载系统库文件
    zotop::register(include(ZOTOP_LIBRARIES.DS.'zotop'.DS.'library.php'));
    zotop::register(include(APP_ROOT.DS.'library.php'));
}
//配置的初始化
if( file_exists(ZOTOP_RUNTIME.DS.'~config.php') )
{
    zotop::config(include(ZOTOP_RUNTIME.DS.'~config.php'));
}
else
{
    zotop::config(include(ZOTOP_CONFIG.DS.'zotop.php'));
    zotop::config(include(ZOTOP_CONFIG.DS.'setting.php'));
    zotop::config('zotop.database',include(ZOTOP_CONFIG.DS.'database.php'));
    zotop::config('zotop.application',include(ZOTOP_CONFIG.DS.'application.php'));
    zotop::config('zotop.module',include(ZOTOP_CONFIG.DS.'module.php'));
    zotop::config('zotop.router',include(ZOTOP_CONFIG.DS.'router.php'));    
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