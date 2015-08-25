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
    'comment' => '内容编号',
  ),
  'content' => 
  array (
    'name' => 'content',
    'type' => 'mediumtext',
    'length' => '',
    'null' => 'NO',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '内容',
  ),
  'from' => 
  array (
    'name' => 'from',
    'type' => 'varchar',
    'length' => '50',
    'null' => 'NO',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '来源',
  ),
  'author' => 
  array (
    'name' => 'author',
    'type' => 'varchar',
    'length' => '50',
    'null' => 'NO',
    'key' => '',
    'default' => NULL,
    'collation' => 'utf8_general_ci',
    'extra' => '',
    'comment' => '作者',
  ),
);
?>