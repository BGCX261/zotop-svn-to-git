<?php
class BaseModule
{
    public static function setting($id,$key='')
	{
		static $modules;

		if(!is_array($modules))
		{
			$modules=include(SYSROOT.DS.'modules.php');
		}
		//设置空的module
		$module=$modules[strtolower($id)];
		if(!isset($module))
		{
			$module=array('id'=>$id , 'name'=>$id , 'path'=>$id , 'url'=>$id , 'type'=>'system','status' => '0','publishtime' => '0','installtime' => '0','updatetime' => '0');
		}
		//修正module的路径
		if(empty($module['path']))
		{
	        $module['path']	= $module['id'];
		}

		switch(strtolower($module['type']))
		{
		    case 'core':
		    case 'system':
		       $module['root'] = SYSROOT.DS.'modules'.DS.$module['path'];
		       
		       $module['url'] = url::system().'/modules/'.$module['path'];
		       break;
		    case 'site':
		       $module['root'] = SITEROOT.DS.'modules'.DS.$module['path'];
		       $module['url'] = url::site().'/modules/'.$module['path'];
		       break;
		    default:
		       $module['root'] = realpath($module['path']);
		       $module['url'] = url::abs($module['path']);
		       break;
		}		

		if(empty($key))
		{
			return $module;
		}
		return $module[strtolower($key)];
	}


	public static function operation()
	{
        $filepath = Router::controller('filepath');
        $classname = Router::controller('classname');
        $method = Router::method();
        $arguments = Router::arguments();
        //加载controller
        if( file_exists($filepath) )
        {
            Zotop::load($filepath);
        }
		if(class_exists($classname,false))
		{
			$controller=new $classname();
			if(method_exists($controller,$method) && $method{0}!='_')
			{
				call_user_func_array(array($controller,$method),$arguments);
			}
			else
			{
			    //Zotop::run('system.status.404');
			    //当方法不存在时，默认调用类的_empty()函数，改函数默认显示一个404错误，你可以在控制器中重写此方法
			    call_user_func_array(array($controller,'_empty'),$arguments);
			}
		}
		else
		{
			Zotop::run('system.status.404');
		}

	}
}
?>