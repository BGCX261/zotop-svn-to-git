<?php
define('DS',DIRECTORY_SEPARATOR);
define('APP_NAME','admin');
define('APP_BASE',basename(__FILE__));
define('APP_ROOT',dirname(__FILE__).DS.'admin');
define('APP_URL',dirname($_SERVER['SCRIPT_NAME']).'/admin');
define('ZPATH_SYSTEM',dirname(__FILE__));
define('ZURL_ROOT',dirname(dirname($_SERVER['SCRIPT_NAME'])));
//加载启动文件
require ZPATH_SYSTEM.DS.'boot.php';
//系统运行
zotop::run('system.boot');
zotop::run('system.route');
zotop::run('system.ready');
zotop::run('system.run');
zotop::run('system.shutdown');
?>