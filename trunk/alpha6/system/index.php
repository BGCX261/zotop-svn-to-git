<?php
define('DS',DIRECTORY_SEPARATOR);
define('DEBUG',false);
define('ZOTOP_APP','admin');
define('ZOTOP_APP_BASE',basename(__FILE__));
//define root url
define('ZOTOP_URL_ROOT',dirname(dirname($_SERVER['SCRIPT_NAME'])));
//require the boot file
require dirname(__FILE__).DS.'boot.php';
//system run
zotop::run('system.init');
zotop::run('system.route');
zotop::run('system.ready');
zotop::run('system.run');
zotop::run('system.shutdown');