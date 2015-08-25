<?php
define('DS',DIRECTORY_SEPARATOR);
define('APPBASE',basename(__FILE__));
define('APPROOT',dirname(__FILE__));
define('SYSROOT',dirname(APPROOT));
//加载全局文件
require SYSROOT.DS.'global.php';
require APPROOT.DS.'global.php';
//运行系统事件
zotop::run('system.ready');
zotop::run('system.routing');
zotop::run('system.operation');
zotop::run('system.shutdown');
//zotop::dump(zotop::mark());
//zotop::dump(get_included_files());
?>