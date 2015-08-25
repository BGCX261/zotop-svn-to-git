<?php
return array
(
    //核心部分的别名
	'Base' => LIBROOT.DS.'zotop'.DS.'core'.DS.'base.php',
	'BaseRouter' => LIBROOT.DS.'zotop'.DS.'core'.DS.'router.php',
	'BaseRouter' => LIBROOT.DS.'zotop'.DS.'core'.DS.'router.php',
    'BaseModule' => LIBROOT.DS.'zotop'.DS.'core'.DS.'module.php',
    'BaseController' => LIBROOT.DS.'zotop'.DS.'core'.DS.'controller.php',
    //ui部分
    'BasePage' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'page.php',
    'BaseHtml' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'html.php',
    'BaseForm' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'form.php',
	'BaseField' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'field.php',
    'BaseBlock' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'block.php',
 	'BaseTable' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'table.php',
	'BaseMsg' => LIBROOT.DS.'zotop'.DS.'ui'.DS.'msg.php',
    //io部分
    'BasePath' => LIBROOT.DS.'zotop'.DS.'io'.DS.'path.php',
    'BaseFolder' => LIBROOT.DS.'zotop'.DS.'io'.DS.'folder',
 	'BaseFile' => LIBROOT.DS.'zotop'.DS.'io'.DS.'file.php',
     //util部分
    'BaseArr' => LIBROOT.DS.'zotop'.DS.'util'.DS.'array.php',
    'BaseString' => LIBROOT.DS.'zotop'.DS.'util'.DS.'string.php',
 	'BaseURL' => LIBROOT.DS.'zotop'.DS.'util'.DS.'url.php',
    'BaseRand' => LIBROOT.DS.'zotop'.DS.'util'.DS.'rand.php',
	'BaseFormat' => LIBROOT.DS.'zotop'.DS.'util'.DS.'format.php',
    //user
    'BaseUser' => LIBROOT.DS.'zotop'.DS.'user'.DS.'user.php',

    //database
    'BaseDatabase' => LIBROOT.DS.'zotop'.DS.'db'.DS.'database.php',
	'DatabaseInterface' => LIBROOT.DS.'zotop'.DS.'db'.DS.'interface.php',
	'Database_Mysql' => LIBROOT.DS.'zotop'.DS.'db'.DS.'driver'.DS.'mysql.php',
	'Database_Mysqli' => LIBROOT.DS.'zotop'.DS.'db'.DS.'driver'.DS.'mysqli.php',
	'Database_Sqlite' => LIBROOT.DS.'zotop'.DS.'db'.DS.'driver'.DS.'sqlite.php'
);
?>