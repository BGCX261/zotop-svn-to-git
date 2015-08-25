<?php
set_magic_quotes_runtime(0); //关闭自动转换
define('DS',DIRECTORY_SEPARATOR);
define('ZOTOP',true); //定义一个全局符号
define('SYSROOT',dirname(__FILE__)); //定义当前系统文件夹目录，SYTROOT是Zotop的系统目录，用于存放系统相关文件
define('LIBROOT',SYSROOT.DS.'libraries');//定义系统库文件的存放位置，zotop主框架就存放在该目录下
define('WWWROOT',dirname(SYSROOT));  //定义zotop的根目录，该目录可能并不是网站的根目录，但是一定是SYSROOT的上级目录,该目录名称有待商定，WWWROOT一般指的是网站的根目录
define('SITEROOT',WWWROOT.DS.'site'); //定义用户目录，所有的用户文件都存储与该目录，除该目录外，整个网站的其他目录都是可以抛弃的，便于备份和升级，该目录名称待定
define('PLUGINSROOT',SITEROOT.DS.'plugins');//定义插件目录

//加载编译的核心库，如果未编译则直接加载需要文件，开发模式不编译
if(is_file(LIBROOT.DS.'~runtime.php'))
{
    require LIBROOT.DS.'~runtime.php';
}
else
{
    //将系统核心部分（alias.php）中的文件打包
    require LIBROOT.DS.'zotop'.DS.'core'.DS.'zotop.php';
    require LIBROOT.DS.'zotop'.DS.'core'.DS.'functions.php';
    //启动系统
    zotop::register(include(LIBROOT.DS.'alias.php'));
	//zotop::boot();    
    //编译系统核心
}
//系统启动
zotop::boot();
//TODO 加载配置代码暂时放在这儿
//加载 系统配置
@zotop::config(include(SITEROOT.DS.'config.php'));
//加载全局配置，如果找不到这个配置文件就会重新生成缓存
@zotop::config(include(SYSROOT.DS.'config.php'));

//加载编辑的插件部分,暂时不处理此部分
/*
if(is_file(PLUGINSROOT.DS.'~runtime.php'))
{
    require PLUGINSROOT.DS.'~runtime.php';
}
else
{
   //打包核心插件部分
}
*/
?>