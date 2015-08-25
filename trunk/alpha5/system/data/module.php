<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  'zotop' => 
  array (
    'id' => 'zotop',
    'name' => '系统核心模块',
    'title' => '系统核心模块',
    'description' => '系统核心模块，实现系统必须的一些基本功能',
    'icon' => '',
    'version' => '1',
    'type' => 'system',
    'path' => '$modules/zotop',
    'url' => '$modules/zotop',
    'status' => '0',
    'order' => '0',
    'installtime' => '1259394471',
    'updatetime' => '1259394471',
    'author' => 'zotop.chenlei',
    'email' => 'zotop.chenlei@gmail.com',
    'homepage' => 'http://www.zotop.com',
  ),
  'msg' => 
  array (
    'id' => 'msg',
    'name' => '站内消息系统',
    'title' => '站内消息',
    'description' => '用于站内用户通信功能',
    'icon' => '/z5/system/modules/msg/icon.png',
    'version' => '1',
    'type' => 'plugin',
    'path' => '$modules/msg',
    'url' => '$modules/msg',
    'status' => '0',
    'order' => '2',
    'installtime' => '1266053168',
    'updatetime' => '1266053168',
    'author' => 'zotop.chenlei',
    'email' => 'zotop.chenlei@gmail.com',
    'homepage' => 'http://www.zotop.com',
  ),
  'database' => 
  array (
    'id' => 'database',
    'name' => '数据库管理器',
    'title' => '数据库管理',
    'description' => '在线管理数据库，可以对数据库进行设置以及优化',
    'icon' => '/z5/system/admin/themes/blue/image/skin/none.png',
    'version' => '1',
    'type' => 'com',
    'path' => '$modules/database',
    'url' => '$modules/database',
    'status' => '0',
    'order' => '1',
    'installtime' => '1266048765',
    'updatetime' => '1266048765',
    'author' => 'zotop.chenlei',
    'email' => 'zotop.chenlei@gmail.com',
    'homepage' => 'http://www.zotop.com',
  ),
  'content' => 
  array (
    'id' => 'content',
    'name' => '内容管理模块',
    'title' => '内容管理',
    'description' => '发布、管理站点的内容',
    'icon' => '/z5/system/modules/content/icon.png',
    'version' => '1',
    'type' => 'com',
    'path' => '$modules/content',
    'url' => '$modules/content',
    'status' => '0',
    'order' => '3',
    'installtime' => '1266076801',
    'updatetime' => '1266076801',
    'author' => 'zotop.chenlei',
    'email' => 'zotop.chenlei@gmail.com',
    'homepage' => 'http://www.zotop.com',
  ),
);
?>