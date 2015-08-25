<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  1 => 
  array (
    'id' => '1',
    'title' => '超级管理员',
    'description' => '拥有全部的管理权限',
    'type' => 'system',
    'status' => '0',
    'order' => '0',
  ),
  2 => 
  array (
    'id' => '2',
    'title' => '普通管理员',
    'description' => '拥有所有栏目和所有专题的所有权限，并且可以添加栏目和专题	',
    'type' => 'system',
    'status' => '0',
    'order' => '0',
  ),
  3 => 
  array (
    'id' => '3',
    'title' => '网站编辑',
    'description' => '拥有某些栏目的信息录入、审核及管理权限，需要进一步详细设置。	',
    'type' => 'system',
    'status' => '0',
    'order' => '0',
  ),
  0 => 
  array (
    'id' => '0',
    'title' => '系统管理员',
    'description' => '系统初始管理员隶属的管理员组',
    'type' => '',
    'status' => '0',
    'order' => '0',
  ),
);
?>