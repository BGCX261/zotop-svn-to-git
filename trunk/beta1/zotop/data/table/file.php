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
    'default' => '',
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '文件编号',
  ),
  'parentid' => 
  array (
    'name' => 'parentid',
    'type' => 'varchar',
    'length' => '32',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '文件父编号',
  ),
  'globalid' => 
  array (
    'name' => 'globalid',
    'type' => 'varchar',
    'length' => '128',
    'null' => 'YES',
    'key' => 'MUL',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '全局唯一识别符',
  ),
  'folderid' => 
  array (
    'name' => 'folderid',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '文件组编号',
  ),
  'field' => 
  array (
    'name' => 'field',
    'type' => 'varchar',
    'length' => '64',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '对应的字段名称',
  ),
  'guid' => 
  array (
    'name' => 'guid',
    'type' => 'varchar',
    'length' => '32',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '',
  ),
  'name' => 
  array (
    'name' => 'name',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '文件名称',
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
    'comment' => '文件类型',
  ),
  'size' => 
  array (
    'name' => 'size',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '文件大小',
  ),
  'path' => 
  array (
    'name' => 'path',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '文件存储路径',
  ),
  'ext' => 
  array (
    'name' => 'ext',
    'type' => 'varchar',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '文件格式',
  ),
  'width' => 
  array (
    'name' => 'width',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '宽度，仅图像有效',
  ),
  'height' => 
  array (
    'name' => 'height',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '高度，仅图像有效',
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
    'comment' => '文件描述信息',
  ),
  'url' => 
  array (
    'name' => 'url',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '文件链接',
  ),
  'status' => 
  array (
    'name' => 'status',
    'type' => 'int',
    'length' => '4',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '文件状态',
  ),
  'userid' => 
  array (
    'name' => 'userid',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '用户编号',
  ),
  'createip' => 
  array (
    'name' => 'createip',
    'type' => 'varchar',
    'length' => '15',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '创建IP',
  ),
  'createtime' => 
  array (
    'name' => 'createtime',
    'type' => 'int',
    'length' => '11',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '创建时间',
  ),
);
?>