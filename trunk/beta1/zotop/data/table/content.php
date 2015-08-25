<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'mediumint',
    'length' => '8',
    'null' => 'NO',
    'key' => 'PRI',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '内容编号',
  ),
  'globalid' => 
  array (
    'name' => 'globalid',
    'type' => 'varchar',
    'length' => '32',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '全局编号',
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
    'comment' => '模型编号',
  ),
  'categoryid' => 
  array (
    'name' => 'categoryid',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => 'MUL',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '栏目编号',
  ),
  'title' => 
  array (
    'name' => 'title',
    'type' => 'varchar',
    'length' => '100',
    'null' => 'NO',
    'key' => 'MUL',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '内容标题',
  ),
  'style' => 
  array (
    'name' => 'style',
    'type' => 'varchar',
    'length' => '50',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '标题样式',
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
    'comment' => '链接',
  ),
  'image' => 
  array (
    'name' => 'image',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '图片',
  ),
  'keywords' => 
  array (
    'name' => 'keywords',
    'type' => 'varchar',
    'length' => '100',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '关键词',
  ),
  'summary' => 
  array (
    'name' => 'summary',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '摘要',
  ),
  'template' => 
  array (
    'name' => 'template',
    'type' => 'varchar',
    'length' => '255',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '模板文件',
  ),
  'comment' => 
  array (
    'name' => 'comment',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '评论条数，-1为禁止评论',
  ),
  'hits' => 
  array (
    'name' => 'hits',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '点击次数',
  ),
  'grade' => 
  array (
    'name' => 'grade',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '分数，综合计算点击+评论+浏览后的得分',
  ),
  'link' => 
  array (
    'name' => 'link',
    'type' => 'tinyint',
    'length' => '1',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '是否是链接',
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
  'status' => 
  array (
    'name' => 'status',
    'type' => 'tinyint',
    'length' => '3',
    'null' => 'YES',
    'key' => 'MUL',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '状态，1为通过审核，0为等待审核，-1为未通过审核，-50为草稿，-100 为回收站',
  ),
  'order' => 
  array (
    'name' => 'order',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => 'MUL',
    'default' => '0',
    'collation' => NULL,
    'extra' => '',
    'comment' => '排序数字',
  ),
  'createtime' => 
  array (
    'name' => 'createtime',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => '',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '创建时间',
  ),
  'updatetime' => 
  array (
    'name' => 'updatetime',
    'type' => 'int',
    'length' => '10',
    'null' => 'YES',
    'key' => 'MUL',
    'default' => NULL,
    'collation' => NULL,
    'extra' => '',
    'comment' => '更新时间',
  ),
);
?>