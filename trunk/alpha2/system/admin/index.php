<?php
define('DS',DIRECTORY_SEPARATOR);
define('ZOTOP_APP',dirname(__FILE__));
define('ZOTOP_APP_BASE',basename(__FILE__));
define('ZOTOP_SYSTEM',dirname(ZOTOP_APP));
//加载核心文件
require ZOTOP_SYSTEM.DS.'global.php';
//系统运行
zotop::run('system.boot');
zotop::run('system.route');
zotop::run('system.ready');
zotop::run('system.run');
zotop::run('system.shutdown');
?>