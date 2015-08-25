<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  'name' => 'zotop_user',
  'size' => 5232,
  'datalength' => '112',
  'indexlength' => '5120',
  'rows' => '2',
  'engine' => 'MyISAM',
  'collation' => 'utf8_general_ci',
  'createtime' => '2009-10-08 10:36:59',
  'updatetime' => '2009-10-10 09:38:13',
  'comment' => '用户表，用于存储用户相关数据和信息',
  'primarykey' => 'id',
  'fields' => 
  array (
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
      'comment' => '用户编号',
    ),
    'username' => 
    array (
      'name' => 'username',
      'type' => 'varchar',
      'length' => '32',
      'null' => 'NO',
      'key' => 'UNI',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户帐号',
    ),
    'password' => 
    array (
      'name' => 'password',
      'type' => 'varchar',
      'length' => '32',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户密码',
    ),
    'question' => 
    array (
      'name' => 'question',
      'type' => 'varchar',
      'length' => '100',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '安全问题',
    ),
    'answer' => 
    array (
      'name' => 'answer',
      'type' => 'varchar',
      'length' => '100',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '安全问题答案',
    ),
    'groupid' => 
    array (
      'name' => 'groupid',
      'type' => 'int',
      'length' => '6',
      'null' => 'YES',
      'key' => 'MUL',
      'default' => NULL,
      'collation' => NULL,
      'extra' => '',
      'comment' => '用户组编号',
    ),
    'modelid' => 
    array (
      'name' => 'modelid',
      'type' => 'varchar',
      'length' => '32',
      'null' => 'YES',
      'key' => 'MUL',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户模型编号',
    ),
    'logintime' => 
    array (
      'name' => 'logintime',
      'type' => 'int',
      'length' => '10',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => NULL,
      'extra' => '',
      'comment' => '用户最后登录时间',
    ),
    'loginip' => 
    array (
      'name' => 'loginip',
      'type' => 'char',
      'length' => '15',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户最后登录IP地址',
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
      'comment' => '用户状态，-100锁定，0登出，1登录',
    ),
    'updatetime' => 
    array (
      'name' => 'updatetime',
      'type' => 'int',
      'length' => '10',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => NULL,
      'extra' => '',
      'comment' => '帐户更新时间',
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
      'comment' => '帐户创建时间',
    ),
    'name' => 
    array (
      'name' => 'name',
      'type' => 'varchar',
      'length' => '64',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户昵称或者姓名',
    ),
    'gender' => 
    array (
      'name' => 'gender',
      'type' => 'char',
      'length' => '10',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户性别',
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
      'comment' => '用户头像',
    ),
    'email' => 
    array (
      'name' => 'email',
      'type' => 'varchar',
      'length' => '100',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户的email',
    ),
    'sign' => 
    array (
      'name' => 'sign',
      'type' => 'varchar',
      'length' => '200',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '用户签名',
    ),
    'data' => 
    array (
      'name' => 'data',
      'type' => 'text',
      'length' => '',
      'null' => 'YES',
      'key' => '',
      'default' => NULL,
      'collation' => 'utf8_general_ci',
      'extra' => '',
      'comment' => '其他数据',
    ),
  ),
);
?>