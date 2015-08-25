<?php
return array
(
    //core
	'zotop' => dirname(__FILE__).DS.'libraries'.DS.'zotop.php',
	'runtime_base' => dirname(__FILE__).DS.'libraries'.DS.'runtime.php',
	'application_base' => dirname(__FILE__).DS.'libraries'.DS.'application.php',
	'router_base' => dirname(__FILE__).DS.'libraries'.DS.'router.php',
    'module_base' => dirname(__FILE__).DS.'libraries'.DS.'module.php',
    'controller_base' => dirname(__FILE__).DS.'libraries'.DS.'controller.php',
	'model_base' => dirname(__FILE__).DS.'libraries'.DS.'model.php',
	'admin_base' => dirname(__FILE__).DS.'libraries'.DS.'admin.php',
	'site_base' => dirname(__FILE__).DS.'libraries'.DS.'site.php',
	//ui
    'page_base' => dirname(__FILE__).DS.'libraries'.DS.'page.php',
    'form_base' => dirname(__FILE__).DS.'libraries'.DS.'form.php',
	'field_base' => dirname(__FILE__).DS.'libraries'.DS.'field.php',
    'box_base' => dirname(__FILE__).DS.'libraries'.DS.'box.php',
 	'table_base' => dirname(__FILE__).DS.'libraries'.DS.'table.php',
	'msg_base' => dirname(__FILE__).DS.'libraries'.DS.'msg.php',
	'pagination_base' => dirname(__FILE__).DS.'libraries'.DS.'pagination.php',
	'theme_base' => dirname(__FILE__).DS.'libraries'.DS.'theme.php',
    //io
    'path_base' => dirname(__FILE__).DS.'libraries'.DS.'path.php',
    'folder_base' => dirname(__FILE__).DS.'libraries'.DS.'folder.php',
 	'file_base' => dirname(__FILE__).DS.'libraries'.DS.'file.php',
	'image_base' => dirname(__FILE__).DS.'libraries'.DS.'image.php',
	'upload_base' => dirname(__FILE__).DS.'libraries'.DS.'upload.php',
     //util
    'arr_base' => dirname(__FILE__).DS.'libraries'.DS.'array.php',
    'string_base' => dirname(__FILE__).DS.'libraries'.DS.'string.php',
 	'url_base' => dirname(__FILE__).DS.'libraries'.DS.'url.php',
    'rand_base' => dirname(__FILE__).DS.'libraries'.DS.'rand.php',
	'format_base' => dirname(__FILE__).DS.'libraries'.DS.'format.php',
	'request_base' => dirname(__FILE__).DS.'libraries'.DS.'request.php',
	'valid_base' => dirname(__FILE__).DS.'libraries'.DS.'valid.php',
	'html_base' => dirname(__FILE__).DS.'libraries'.DS.'html.php',
	'ubb_base' => dirname(__FILE__).DS.'libraries'.DS.'ubb.php',
	'ip_base' => dirname(__FILE__).DS.'libraries'.DS.'ip.php',
    'time_base' => dirname(__FILE__).DS.'libraries'.DS.'time.php',
	'tree_base' => dirname(__FILE__).DS.'libraries'.DS.'tree.php',
    //user
    'user_base' => dirname(__FILE__).DS.'libraries'.DS.'user.php',
    //datazotop
    'database_base' => dirname(__FILE__).DS.'libraries'.DS.'database.php',
	'database_mysql_base' => dirname(__FILE__).DS.'libraries'.DS.'database'.DS.'mysql.php',
	'database_mysqli_base' => dirname(__FILE__).DS.'libraries'.DS.'database'.DS.'mysqli.php',
	'database_sqlite_base' => dirname(__FILE__).DS.'libraries'.DS.'database'.DS.'sqlite.php',

    //datazotop
    'cache_base' => dirname(__FILE__).DS.'libraries'.DS.'cache.php',
	'cache_file_base' => dirname(__FILE__).DS.'libraries'.DS.'cache'.DS.'file.php',
	'cache_memcache_base' => dirname(__FILE__).DS.'libraries'.DS.'cache'.DS.'memcache.php',
);
?>