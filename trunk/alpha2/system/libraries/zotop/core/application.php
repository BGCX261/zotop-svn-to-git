<?php
class zotop_application
{
    /**
     * 应用程序初始化
     *
     * @return null
     */
    public static function boot()
    {
        //错误及异常处理
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
        set_error_handler(array('application', 'error'));
        set_exception_handler(array('application', 'exception'));
        //时区设置
		if (function_exists('date_default_timezone_set'))
		{
			$timezone = zotop::config('zotop.locale.timezone');
			$timezone = empty($timezone) ? date_default_timezone_get() : $timezone;
			if($timezone)
			{
				date_default_timezone_set($timezone);
			}
		}
		//输出头
		header("Content-Type: text/html;charset=utf-8");
    }

    /**
     * 应用程序执行
     *
     *
     * @return null
     */
    public static function run()
    {
        $classname = router::controllerName();
        $filepath = router::controllerPath();
        $method = router::controllerMethod();
        $arguments = router::arguments();
        //加载controller
        if( file_exists($filepath) )
        {
            zotop::load($filepath);
        }
		if(class_exists($classname,false))
		{
			$controller=new $classname();
			if(method_exists($controller,$method) && $method{0}!='_')
			{
				return call_user_func_array(array($controller,$method),$arguments);
			}
			else
			{
			    //当方法不存在时，默认调用类的_empty()函数，你可以在控制器中重写此方法
			   return call_user_func_array(array($controller,'_empty'),$arguments);
			}
		}
		zotop::run('system.404',array('filepath'=>$filepath));
    }
    /**
     * 渲染输出内容
     *
     * @param string $output 待渲染输出的内容
     * @return string
     */
    public static function render($output)
    {
		$mark = zotop::mark('system.begin','system.end');
		$output=str_replace
		(
			array('{$runtime}','{$memory}','{$include}'),
			array($mark['time'].' S',$mark['memory'].' MB',count(get_included_files())),
			$output
		);
        return $output;
    }

	public static function error($errno, $message='', $file='', $line=0, $extra=array())
	{
	   switch ($errno) {
          case E_ERROR:
          case E_USER_ERROR:
            $error = "[{$errno}] {$message} {$file} 第 {$line} 行.";
    	    exit('<div style="color:red;">{$error}</div>');
            break;
          case E_STRICT:
          case E_USER_WARNING:
          case E_USER_NOTICE:
          default:
            $error = "[{$errno}] {$message} {$file} 第 {$line} 行.";
            break;
          }
	}
    /**
	 * 异常处理，待完成
	 *
	 * @param $error
	 * @param $message
	 * @param $file
	 * @param $line
	 * @return null
	 */
	public static function exception($error,$message='',$file='',$line=0)
	{
	    echo '<div style="color:red;">error:'.$error.'('.$message.'////'.$file.$line.')</div>';
	    exit();
	}

	public static function show404($data)
	{
		msg::error('未能找到相应页面',zotop::t('<h2>请检查相应的页面或者控制器是否存在？</h2>{$filepath}',$data));
	}

}
?>