<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  0 => 
  array (
    'id' => '1284017507',
    'modelid' => 'news',
    'name' => 'title',
    'label' => '标题',
    'field' => 'title',
    'value' => NULL,
    'valid' => 'required:true,maxlength:100',
    'description' => NULL,
    'settings' => '{"class":"long","style":null}',
    'type' => 'VARCHAR',
    'maxlength' => '100',
    'required' => '1',
    'system' => '1',
    'order' => '1',
    'status' => '1',
  ),
  1 => 
  array (
    'id' => '1284017508',
    'modelid' => 'news',
    'name' => 'image',
    'label' => '标题图片',
    'field' => 'image',
    'value' => NULL,
    'valid' => 'maxlength:160',
    'description' => NULL,
    'settings' => '{"class":"long","style":null}',
    'type' => 'VARCHAR',
    'maxlength' => '160',
    'required' => '0',
    'system' => '1',
    'order' => '2',
    'status' => '1',
  ),
  2 => 
  array (
    'id' => '1284017509',
    'modelid' => 'news',
    'name' => 'url',
    'label' => '转向链接',
    'field' => 'link',
    'value' => NULL,
    'valid' => 'maxlength:100',
    'description' => '如果填写转向链接则点击标题就直接跳转而内容设置无效',
    'settings' => '{"class":"long","style":null}',
    'type' => 'VARCHAR',
    'maxlength' => '100',
    'required' => '0',
    'system' => '1',
    'order' => '3',
    'status' => '1',
  ),
  3 => 
  array (
    'id' => '1284016178',
    'modelid' => 'news',
    'name' => 'from',
    'label' => '来源',
    'field' => 'text',
    'value' => '',
    'valid' => 'maxlength:50',
    'description' => '',
    'settings' => '{"class":"","style":""}',
    'type' => 'VARCHAR',
    'maxlength' => '50',
    'required' => '0',
    'system' => '0',
    'order' => '4',
    'status' => '1',
  ),
  4 => 
  array (
    'id' => '1284017739',
    'modelid' => 'news',
    'name' => 'author',
    'label' => '作者',
    'field' => 'text',
    'value' => '',
    'valid' => 'maxlength:50',
    'description' => '',
    'settings' => '{"class":"","style":""}',
    'type' => 'VARCHAR',
    'maxlength' => '50',
    'required' => '0',
    'system' => '0',
    'order' => '5',
    'status' => '1',
  ),
  5 => 
  array (
    'id' => '1284014983',
    'modelid' => 'news',
    'name' => 'content',
    'label' => '内容',
    'field' => 'editor',
    'value' => '',
    'valid' => '',
    'description' => '',
    'settings' => '{"toolbar":"standard","class":"long","style":""}',
    'type' => 'MEDIUMTEXT',
    'maxlength' => '0',
    'required' => '0',
    'system' => '0',
    'order' => '6',
    'status' => '1',
  ),
  6 => 
  array (
    'id' => '1284017511',
    'modelid' => 'news',
    'name' => 'summary',
    'label' => '摘要',
    'field' => 'summary,textarea',
    'value' => NULL,
    'valid' => 'maxlength:255',
    'description' => NULL,
    'settings' => '{"class":"long","style":null}',
    'type' => 'VARCHAR',
    'maxlength' => '255',
    'required' => '0',
    'system' => '1',
    'order' => '7',
    'status' => '1',
  ),
  7 => 
  array (
    'id' => '1284017510',
    'modelid' => 'news',
    'name' => 'keywords',
    'label' => '关键词',
    'field' => 'keywords',
    'value' => NULL,
    'valid' => 'maxlength:50',
    'description' => NULL,
    'settings' => '{"class":"long","style":null}',
    'type' => 'VARCHAR',
    'maxlength' => '50',
    'required' => '0',
    'system' => '1',
    'order' => '8',
    'status' => '1',
  ),
);
?>