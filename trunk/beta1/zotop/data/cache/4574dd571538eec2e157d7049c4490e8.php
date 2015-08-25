<?php
if (!defined('ZOTOP')) exit();
//000000000000
return array (
  'news' => 
  array (
    'id' => 'news',
    'name' => '新闻模型',
    'title' => '新闻',
    'description' => '用于存储新闻类型的数据',
    'tablename' => 'content_model_news',
    'unit' => '篇',
    'type' => NULL,
    'settings' => '{"template_index":"content\\/index.php","template_list":"content\\/list.php","template_detail":"content\\/detail.php","template_print":"content\\/print.php"}',
    'status' => '1',
    'order' => '0',
  ),
);
?>