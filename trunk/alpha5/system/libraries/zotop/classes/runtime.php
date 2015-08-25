<?php
class zotop_runtime
{
    public static function reboot()
    {
        runtime::clear();
        runtime::library();
        runtime::config();        
        runtime::hook();
    }
    
    public static function clear()
    {
        //清除全部的运行时文件
        $files = (array)dir::files(ZPATH_RUNTIME);
        foreach($files as $file)
        {
           @unlink(ZPATH_RUNTIME.DS.$file);
        }        
    }
    
    public static function library()
    {
        //打包当前已经注册的类
        zotop::register(include(ZPATH_LIBRARIES.DS.'zotop'.DS.'library.php'));
        zotop::register(include(APP_ROOT.DS.'library.php')); 
        $files = zotop::register();
        $content = runtime::compile($files);
        if( !empty($content) )
        {
            file::write(ZPATH_RUNTIME.DS.APP_NAME.'.php', $content, true);
        }
    }
    
    public static function config()
    {
        //打包全部配置
        zotop::config(include(ZPATH_DATA.DS.'config.php'));
        zotop::config('zotop.database',include(ZPATH_DATA.DS.'database.php'));
        zotop::config('zotop.application',include(ZPATH_DATA.DS.'application.php'));
        zotop::config('zotop.module',include(ZPATH_DATA.DS.'module.php'));
        zotop::config('zotop.router',include(ZPATH_DATA.DS.'router.php'));
    	
    	zotop::data(ZPATH_RUNTIME.DS.'config.php',zotop::config());
    }
    
    public static function hook()
    {
        //打包全部hook
        $hooks = array();
        $modules = zotop::data('module');
        foreach($modules as $module)
        {
            $path = $module['path'].DS.'hook';
            $path = path::decode($path);
            $hook = (array) dir::files($path,'',true,true);
            $hooks = array_merge($hooks, $hook);
        }

        $content = runtime::compile($hooks);

        if( !empty($content) )
        {
            file::write(ZPATH_RUNTIME.DS.'hook.php', $content,true);
        }        
    }
    
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
?>