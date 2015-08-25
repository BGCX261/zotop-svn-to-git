<?php
define('DS',DIRECTORY_SEPARATOR);
define('APPBASE',basename(__FILE__));
define('APPROOT',dirname(__FILE__).DS.'site'.DS.'web');
define('SYSROOT',dirname(__FILE__).DS.'system');
//加载全局文件
require SYSROOT.DS.'global.php';
//require APPROOT.DS.'global.php';
//运行系统事件
Zotop::run('system.ready');
Zotop::run('system.routing');
Zotop::run('system.operation');
Zotop::run('system.shutdown');
?>