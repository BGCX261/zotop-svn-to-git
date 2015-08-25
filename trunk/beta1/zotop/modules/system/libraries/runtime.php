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
class runtime_base
{
	public static $hooks = array();

    public static function reboot()
    {
		runtime::clear();
		runtime::build();
    }

    /**
     * 清理全部运行时文件
     *
     */
    public static function clear()
    {
		folder::clear(ZOTOP_PATH_RUNTIME);
    }    
    
    /**
     * 打包全部类库文件
     *
     */
    public static function library()
    {
        zotop::register(@include(ZOTOP_PATH_LIBRARIES.DS.'classes.php'));
        zotop::register(@include(ZOTOP_APPLICATION_ROOT.DS.'libraries'.DS.'classes.php'));        
    }
    
    /**
     * 打包全部的配置文件
     */
    public static function config()
    {
        //加载全部配置
        zotop::config(@include(ZOTOP_PATH_DATA.DS.'config.php'));
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
		
        foreach( (array)$modules as $module)
        {
            if( (int)$module['status'] >= 0 && folder::exists($module['path']) )
            {
				//加载hook文件
				runtime::$hooks[] = $module['path'].DS.'hooks'.DS.ZOTOP_APPLICATION_GROUP.'.php';
				//加载库文件
				zotop::register(@include(path::decode($module['path']).DS.'classes.php'));
            }
        }
    }


    /**
     * 运行时执行，并加载相关文件
     */
	public static function build()
	{
        runtime::config();

        runtime::library();

        runtime::hooks();
		
		//打包配置文件
		zotop::data(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION_GROUP.'.config.php',zotop::config());

		//打包全部hook文件
        $hooks = runtime::compile(runtime::$hooks);

        if( !empty($hooks) )
        {
            file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION_GROUP.'.hooks.php', $hooks,true);
        }
		
		//加载hooks以便核心文件使用
        zotop::load(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION_GROUP.'.hooks.php');
	
        //打包核心文件
		$libraries = zotop::register();
        $libraries = runtime::compile($libraries);
		
        if( !empty($libraries) )
        {
            file::write(ZOTOP_PATH_RUNTIME.DS.ZOTOP_APPLICATION_GROUP.'.core.php', $libraries, true);
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