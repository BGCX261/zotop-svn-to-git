<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'varchar',
    'length' => '32',
    'null' => 'NO',
    'key' => 'PRI',
    'default' => '0',
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '模型编号',
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
    'comment' => '模型名称',
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
    'comment' => '模型标题',
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
    'comment' => '模型说明',
  ),
  'tablename' => 
  array (
    'name' => 'tablename',
    'type' => 'varchar',
    'length' => '64',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '模型数据表',
  ),
  'unit' => 
  array (
    'name' => 'unit',
    'type' => 'varchar',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '单位',
  ),
  'type' => 
  array (
    'name' => 'type',
    'type' => 'varchar',
    'length' => '64',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '模型类型',
  ),
  'settings' => 
  array (
    'name' => 'settings',
    'type' => 'text',
    'length' => '',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '高级设置',
  ),
  'status' => 
  array (
    'name' => 'status',
    'type' => 'tinyint',
    'length' => '1',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '状态',
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
    'comment' => '排序',
  ),
);
?>