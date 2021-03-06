<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int',
    'length' => '10',
    'null' => 'NO',
    'key' => 'PRI',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '栏目编号',
  ),
  'parentid' => 
  array (
    'name' => 'parentid',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '栏目父编号',
  ),
  'parentids' => 
  array (
    'name' => 'parentids',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '逐级父编号',
  ),
  'childid' => 
  array (
    'name' => 'childid',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '子栏目编号',
  ),
  'childids' => 
  array (
    'name' => 'childids',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '全部子栏目编号',
  ),
  'name' => 
  array (
    'name' => 'name',
    'type' => 'varchar',
    'length' => '50',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目名称',
  ),
  'title' => 
  array (
    'name' => 'title',
    'type' => 'varchar',
    'length' => '50',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目标题',
  ),
  'description' => 
  array (
    'name' => 'description',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目说明',
  ),
  'url' => 
  array (
    'name' => 'url',
    'type' => 'varchar',
    'length' => '100',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目url',
  ),
  'image' => 
  array (
    'name' => 'image',
    'type' => 'varchar',
    'length' => '100',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目图片',
  ),
  'type' => 
  array (
    'name' => 'type',
    'type' => 'varchar',
    'length' => '32',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目类型',
  ),
  'modelid' => 
  array (
    'name' => 'modelid',
    'type' => 'varchar',
    'length' => '32',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目模型',
  ),
  'settings' => 
  array (
    'name' => 'settings',
    'type' => 'mediumtext',
    'length' => '',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '栏目设置',
  ),
  'order' => 
  array (
    'name' => 'order',
    'type' => 'int',
    'length' => '6',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '栏目排序',
  ),
  'status' => 
  array (
    'name' => 'status',
    'type' => 'tinyint',
    'length' => '3',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '栏目状态',
  ),
);
?>