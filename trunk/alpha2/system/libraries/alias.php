<?php
return array
(
    //核心部分的别名
	'zotop_application' => ZOTOP_LIB.DS.'zotop'.DS.'core'.DS.'application.php',
	'zotop_router' => ZOTOP_LIB.DS.'zotop'.DS.'core'.DS.'router.php',
    'zotop_module' => ZOTOP_LIB.DS.'zotop'.DS.'core'.DS.'module.php',
    'zotop_controller' => ZOTOP_LIB.DS.'zotop'.DS.'core'.DS.'controller.php',
	'zotop_model' => ZOTOP_LIB.DS.'zotop'.DS.'core'.DS.'model.php',
    //ui部分
    'Zotop_page' => ZOTOP_LIB.DS.'zotop'.DS.'ui'.DS.'page.php',

    'zotop_form' => ZOTOP_LIB.DS.'zotop'.DS.'ui'.DS.'form.php',
	'zotop_field' => ZOTOP_LIB.DS.'zotop'.DS.'ui'.DS.'field.php',
    'zotop_block' => ZOTOP_LIB.DS.'zotop'.DS.'ui'.DS.'block.php',
 	'zotop_table' => ZOTOP_LIB.DS.'zotop'.DS.'ui'.DS.'table.php',
	'zotop_msg' => ZOTOP_LIB.DS.'zotop'.DS.'ui'.DS.'msg.php',
    //io部分
    'zotop_path' => ZOTOP_LIB.DS.'zotop'.DS.'io'.DS.'path.php',
    'zotop_folder' => ZOTOP_LIB.DS.'zotop'.DS.'io'.DS.'folder',
 	'zotop_file' => ZOTOP_LIB.DS.'zotop'.DS.'io'.DS.'file.php',
     //util部分
    'zotop_arr' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'array.php',
    'zotop_string' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'string.php',
 	'zotop_url' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'url.php',
    'zotop_rand' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'rand.php',
	'zotop_format' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'format.php',
	'zotop_request' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'request.php',
	'zotop_valid' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'valid.php',
	'zotop_html' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'html.php',
	'zotop_ubb' => ZOTOP_LIB.DS.'zotop'.DS.'util'.DS.'ubb.php',
    //user
    'zotop_user' => ZOTOP_LIB.DS.'zotop'.DS.'user'.DS.'user.php',

    //datazotop
    'zotop_database' => ZOTOP_LIB.DS.'zotop'.DS.'db'.DS.'database.php',
	'Zotop_database_interface' => ZOTOP_LIB.DS.'zotop'.DS.'db'.DS.'interface.php',
	'zotop_database_mysql' => ZOTOP_LIB.DS.'zotop'.DS.'db'.DS.'driver'.DS.'mysql.php',
	'zotop_database_mysqli' => ZOTOP_LIB.DS.'zotop'.DS.'db'.DS.'driver'.DS.'mysqli.php',
	'zotop_database_sqlite' => ZOTOP_LIB.DS.'zotop'.DS.'db'.DS.'driver'.DS.'sqlite.php'
);
?>