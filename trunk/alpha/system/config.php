<?php
return array(
	//系统默认的参数
    'zotop.name'=>'zotop',
    'zotop.title'=>'Zotop cms',
	'zotop.version'=>'0.1 alpha',
	'zotop.author'=>'zotop.chenlei,zotop.chenyan',
	'zotop.authors'=>'……',
	'zotop.homepage'=>'http://www.zotop.com',
	'zotop.install'=>'2009-8-8 16:24:35',
	//url模式
	/*
	 
	 * 0 普通模式 http://localhost/zotop/app/index.php?m=module&c=controller&a=action&id=1&args=2
	 * 1 pathinfo模式 http://localhost/zotop/app/index.php/module/controller/action/1/2 或者 http://localhost/zotop/app/index.php/module,controller,action,1,2 
	 * 2 兼容模式  http://localhost/zotop/app/index.php?zotop=/module/controller/action/1/2
	 * 3 rewrite模式 http://localhost/zotop/app/module/controller/action/1/2
	*/ 
	'zotop.url.model'=> 1,		//默认为pathinfo模式
	'zotop.url.pathinfo'=> 0,	 //pathinfo 默认使用操精简模式
	'zotop.url.separator'=>'/',	 //默认分割符号
	'zotop.url.suffix'=>'.html',	 //url后缀，http://localhost/zotop/app/index.php/module/controller/action/1/2.html
);
?>