<?php
if (!defined('ZOTOP')) exit();
//000000000000
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
    'hostname' => 'w211.dns-china.com',
    'username' => 'zotopcms',
    'password' => 'chanlaye',
    'hostport' => '3306',
    'database' => 'zotopcms',
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
    'zotop' => 
    array (
      'id' => 'zotop',
      'name' => '核心模块',
      'description' => '系统核心模块',
      'path' => 'zotop',
      'url' => 'zotop',
      'type' => 'plugin',
      'version' => '1.0',
      'author' => 'zotop.chenlei',
      'email' => 'zotop.chenlei@gmail.com',
      'site' => 'http://www.zotop.com',
      'order' => '1',
      'status' => '1',
      'installtime' => '1259134784',
      'updatetime' => '1259134784',
    ),
    'database' => 
    array (
      'id' => 'database',
      'name' => '数据库管理器',
      'description' => '管理数据库，添加删除数据表以及字段',
      'path' => 'database',
      'url' => 'database',
      'type' => 'plugin',
      'version' => '1.0',
      'author' => 'zotop.chenlei',
      'email' => 'zotop.chenlei@gmail.com',
      'site' => 'http://www.zotop.com',
      'order' => '1',
      'status' => '1',
      'installtime' => '1259136212',
      'updatetime' => '1259136212',
    ),
  ),
  'zotop.router' => 
  array (
  ),
);
?>