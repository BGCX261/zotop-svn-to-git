<?php
if (!defined('ZOTOP')) exit();
//000000003600
return array (
  'zotop.name' => 'zotop',
  'zotop.title' => 'zotop cms',
  'zotop.version' => '0.1 alpha',
  'zotop.author' => 'zotop.chenlei,zotop.chenyan',
  'zotop.authors' => '……',
  'zotop.homepage' => 'http://www.zotop.com',
  'zotop.install' => '2009-8-8 16:24:35',
  'zotop.url.model' => 1,
  'zotop.url.pathinfo' => 0,
  'zotop.url.separator' => '/',
  'zotop.url.suffix' => '.html',
  'zotop.database' => 
  array (
    'driver' => 'mysql',
    'hostname' => '125.46.248.198',
    'username' => 'root',
    'password' => '123456',
    'hostport' => '8081',
    'database' => 'zotop',
    'charset' => 'utf8',
    'prefix' => 'zotop_',
    'autocreate' => true,
  ),
  'zotop.application' => 
  array (
    'admin' => 
    array (
      'name' => 'admin',
      'path' => 'system/admin',
      'url' => 'system/admin',
      'base' => 'index.php',
    ),
    'member' => 
    array (
      'name' => 'member',
      'path' => 'web/member',
      'url' => 'web/member',
      'base' => 'index.php',
    ),
    'site' => 
    array (
      'name' => 'site',
      'path' => 'web/site',
      'url' => '',
      'base' => 'index.php',
    ),
    'install' => 
    array (
      'name' => 'install',
      'path' => 'system/install',
      'url' => 'system/install',
      'base' => 'index.php',
    ),
  ),
  'zotop.module' => 
  array (
  ),
  'zotop.router' => 
  array (
  ),
);
?>