<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的运行时类 Application
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_runtime
{
	public static $hooks = array();

    public static function reboot()
    {
        runtime::clear();
        runtime::config();
        runtime::library();                
        runtime::hooks();
		runtime::run();
    }

    /**
     * 清理全部运行时文件
     *
     */
    public static function clear()
    {
        dir::clear(ZOTOP_PATH_RUNTIME);      
    }    
    
    /**
     * 打包全部类库文件
     *
     */
    public static function library()
    {
        zotop::register(include(ZOTOP_PATH_LIBRARIES.DS.'zotop'.DS.'classes.php'));
        zotop::register(include(ZOTOP_APP_ROOT.DS.'libraries'.DS.'classes.php'));        
    }
    
    /**
     * 打包全部的配置文件
     */
    public static function config()
    {
        //加载全部配置
        zotop::config(include(ZOTOP_PATH_DATA.DS.'config.php'));
        zotop::config('zotop.database',@include(ZOTOP_PATH_DATA.DS.'database.php'));
        zotop::config('zotop.application',@include(ZOTOP_PATH_DATA.DS.'application.php'));
        zotop::config('zotop.module',@include(ZOTOP_PATH_DATA.DS.'module.php'));
        zotop::config('zotop.router',@include(ZOTOP_PATH_DATA.DS.'router.php'));    	
    }
    
    /**
     * 打包全部的hook文件
     *
     */
    public static function hooks()
    {
        $modules = zotop::data('module');        
        foreach($modules as $module)
        {
            if( (int)$module['status'] >= 0 && dir::exists($module['path']) )
            {
				//只加载相应的hook文件
				runtime::$hooks[] = $module['path'].DS.'hooks'.DS.ZOTOP_APP_NAME.'.php';
            }
        }
    }


    /**
     * 运行时执行，并加载相关文件
     */
	public static function run()
	{
		
		//打包配置文件
		zotop::data(ZOTOP_PATH_RUNTIME.DS.'config.php',zotop::config());

		//打包全部hook文件
        $hooks = runtime::compile(runtime::$hooks);
        if( !empty($hooks) )
        {
            file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'_hooks.php', $hooks,true);
        }
		
		//加载hooks以便核心文件使用
        zotop::load(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'_hooks.php');
	
        //打包核心文件
		$libraries = zotop::register();        
        $libraries = runtime::compile($libraries);        
        if( !empty($libraries) )
        {
            file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APP_NAME.'.php', $libraries, true);
        }
	}
    
    /**
     * 文件打包
     */
    public static function compile($files)
    {
        $content = "<?php\n";
        foreach($files as $file)
        {
            $content .= file::compile($file);
        }
        $content .= "\n?>";
        
        return $content;
    }    
}