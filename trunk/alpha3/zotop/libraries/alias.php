<?php
return array
(
    //核心部分的别名
	'zotop' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'zotop.php',
	'zotop_runtime' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'runtime.php',
	'zotop_application' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'application.php',
	'zotop_router' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'router.php',
    'zotop_module' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'module.php',
    'zotop_controller' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'controller.php',
	'zotop_model' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'core'.DS.'model.php',
    //ui部分
    'Zotop_page' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'ui'.DS.'page.php',
    'zotop_form' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'ui'.DS.'form.php',
	'zotop_field' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'ui'.DS.'field.php',
    'zotop_block' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'ui'.DS.'block.php',
 	'zotop_table' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'ui'.DS.'table.php',
	'zotop_msg' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'ui'.DS.'msg.php',
    //io部分
    'zotop_path' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'io'.DS.'path.php',
    'zotop_dir' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'io'.DS.'dir.php',
 	'zotop_file' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'io'.DS.'file.php',
     //util部分
    'zotop_arr' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'array.php',
    'zotop_string' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'string.php',
 	'zotop_url' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'url.php',
    'zotop_rand' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'rand.php',
	'zotop_format' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'format.php',
	'zotop_request' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'request.php',
	'zotop_valid' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'valid.php',
	'zotop_html' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'html.php',
	'zotop_ubb' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'ubb.php',
	'zotop_ip' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'ip.php',
    'zotop_time' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'util'.DS.'time.php',
    //user
    'zotop_user' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'user'.DS.'user.php',

    //datazotop
    'zotop_database' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'db'.DS.'database.php',
	'Zotop_database_interface' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'db'.DS.'interface.php',
	'zotop_database_mysql' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'db'.DS.'driver'.DS.'mysql.php',
	'zotop_database_mysqli' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'db'.DS.'driver'.DS.'mysqli.php',
	'zotop_database_sqlite' => ZOTOP_LIBRARIES.DS.'zotop'.DS.'db'.DS.'driver'.DS.'sqlite.php'
);
?>