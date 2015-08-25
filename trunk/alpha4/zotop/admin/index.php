<?php
define('DS',DIRECTORY_SEPARATOR);
define('APP_ROOT',dirname(__FILE__));
define('APP_BASE',basename(__FILE__));
define('APP_NAME','admin');
define('ZOTOP',dirname(APP_ROOT));
//加载启动文件
require ZOTOP.DS.'boot.php';
//系统运行
zotop::run('system.boot');
zotop::run('system.route');
zotop::run('system.ready');
zotop::run('system.run');
zotop::run('system.shutdown');
?>