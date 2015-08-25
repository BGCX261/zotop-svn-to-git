<?php

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的核心文件，完成系统的大部分常用功能
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop
{
    public static $events=array();
    /**
     * 启动系统，完成系统的一些初始化设置
     *
     * @return bool
     */
    public static function boot()
    {
        static $boot = false;
        //boot函数只能运行一次
        if($boot) return true;
        zotop::mark('system.begin');
        //缓存开始
        ob_start();
        //注册加载函数
        spl_autoload_register(array('zotop','autoload'));
        //设置系统事件
        zotop::add('system.boot',array('application','init'));//运行时
        zotop::add('system.boot',array('application','runtime'));
        zotop::add('system.route',array('router','init'));
        zotop::add('system.route',array('router','execute'));
        zotop::add('system.404',array('application','show404'));
        zotop::add('system.run',array('application','run'));        
        zotop::add('system.shutdown',array('zotop','shutdown'));        
        zotop::add('system.render',array('application','render'));
    }

    /**
     * 系统关闭，并输出渲染内容
     *
     * @return string
     */
    public static function shutdown()
    {
        zotop::mark('system.end');
        //获取页面内容
        $contents = ob_get_contents();
        //清理输出数据
        ob_end_clean();
        //渲染页面内容
        $contents = zotop::filter('system.render',$contents);
        //输入页面内容
        echo $contents;
    }


    /**
     * load 用于加载文件,相当于include_once，不返回任何错误
     * @param string $file 要加载的文件
     */
    public static function load($file){
        static $loads = array();
        
        if( isset($loads[$file]) )
        {
            return false;
        }
        
        if( file_exists($file) )
        {
            require $file;            
            $loads[$file]=true;
            return true;
        }
        
        return false;
    }

    /**
     * 类注册，用于自动加载文件
     *
     * @param string $name 类的名称
     * @param string $file 类对应的文件
     * @return array|string
     */
    public static function register($name='', $file='')
    {
        static $register = array();

        if( empty($name) )
        {
            return $register;//无参数的时候返回整个别名数据
        }
        if( is_array($name) )//第一个参数为数组时，将整个别名加入数组中
        {
            $register = array_merge($register,$name);
            return $register;
        }
        if( empty($file) )//第二个参数即路径为空时候根据名称返回路径
        {
            $name = strtolower($name);
            $register = array_change_key_case($register);
            $file = isset($register[$name]) ? $register[$name] : false;
            return $file;
        }
        $register[$name] = $file;//加入别名
        return true;
    }

    /**
     * 自动加载，用于自动加载系统的类
     *
     * @param string $class 类名
     * @return unknown_type
     */
    public static function autoload($class)
    {
        if(class_exists($class,false))
        {
            return true;
        }        
       
        //如果存在该类的注册，则加载该类
        if( self::register($class) )
        {
            return self::import($class);
        }      

        $baseclass='zotop_'.$class;
        if( !class_exists($baseclass,false) )
        {            
            if( self::register($baseclass) == false )
            {
                return false;
            }
            self::import($baseclass);
        }
        
        
        if( class_exists($baseclass,false) )
        {
            $newclass = 'class '.$class.' extends '.$baseclass.'{}';
            eval($newclass);
            return true;
        }
        return false;
    }

    /**
     * 导入文件，支持别名和名称空间
     * @param string $name 别名或者名称空间，如：page或者system.ui.page,其中点号标识目录分割，最后一个单元（page）指的是文件名称
     * @param string $path 文件位置，系统默认的为系统的库文件夹
     * @param string $base 名称空间的前缀
     * @return bool;
     */
    public static function import($name, $path=ZOTOP_LIBRARIES, $base='')
    {
        static $imports=array();
        //zotop::dump(self::register());
        if( self::register($name) != false )
        {
            $file = self::register($name);
        }
        else
        {
            if(!empty($base))
            {
                $name=trim($base , '.').'.'.$name;
            }
            $file = $path.DS.str_replace( '.', DS, $name).'.php';
        }
        //echo $file.'<br>';
        if(isset($imports[$file]))
        {
            return true;
        }
        $imports[$file] = true;
        $load = self::load($file);        
        return $load;
    }



    /**
     * 用于实例化一个模型{module}.{model}，如 zotop.user,实例化系统模块的user模型
     *
     *
     * @param $name 模型名称空间
     * @return object(model)
     */
    public static function model($name='')
    {
        static $models = array();
        if( empty($name) )
        {
            return new model();
        }
        if( isset($models[$name]) )
        {
            return $models[$name];
        }
        list($module,$model) = explode('.',$name);
        $modelName = $model.'_model';
        if( !class_exists($modelName) )
        {
            $modelPath = zotop::module($module,'root').DS.'models'.DS.$model.'.php';
            zotop::load($modelPath);
        }
        if( class_exists($modelName) )
        {
            $m = new $modelName();
            $models[$name] = $m;
            return $m;
        }
        zotop::error(100,'未能找到模型',zotop::t('<h2>未能找到模型{$modelName}，请检查模型文件是否存在错误</h2>文件地址：{$modelPath}',array('modelName'=>$modelName,'modelPath'=>$modelPath)));
    }

    /**
     *   获取特定的事件的函数集,相关函数参考Kohana的事件
     *
     * @param string $name 事件的名称，如果为空则返回全部事件
     * @return array 事件函数集
     */
    public static function event($name='')
    {
        if( empty($name) )
        {
            return self::$events;
        }
        return empty(self::$events[$name]) ? array() : self::$events[$name];
    }

    /**
     * 在末尾添加一个新的回调函数
     *
     * @param $name
     * @param $callback
     * @return boolean
     */
    public static function add($name, $callback)
    {
        if ( ! isset(self::$events[$name]))
        {
            self::$events[$name] = array();
        }
        elseif (in_array($callback, self::$events[$name], TRUE))
        {
            return FALSE;
        }
        self::$events[$name][] = $callback;
        return TRUE;
    }

    /**
     * 从事件集合中删除特定的事件或者事件组
     *
     * @param string $name 事件名称
     * @param string $callback
     * @return null
     */
    public static function remove($name, $callback = false)
    {
        if ($callback === FALSE)
        {
            self::$events[$name] = array();
        }
        elseif (isset(self::$events[$name]))
        {
            foreach (self::$events[$name] as $i => $event_callback)
            {
                if ($callback === $event_callback)
                {
                    unset(self::$events[$name][$i]);
                }
            }
        }
    }

    /**
     * 在特定的事件组的特定位置插入事件
     *
     * @param string $name 事件组名称
     * @param int $key		插入事件的位置
     * @param array $callback 被插入的事件
     * @return boolean 插入是否成功
     */
    public static function insert($name, $key, $callback)
    {
        if(in_array($callback,self::$events[$name],TRUE))
        {
            return false;
        }
        self::$events[$name]=array_merge
        (
        array_slice(self::$events[$name],0,$key),
        array($callback),
        array_slice(self::$events[$name],$key)
        );
        return true;
    }

    /**
     * 在特定的事件组前插入特定的事件
     *
     * zotop::before('system.routing', array('router', 'boot'), array('myrouter', 'setup'));
     *
     * @param string $name 事件名称
     * @param array $existing 函数
     * @param array $callback 插入的函数
     * @return boolean 返回插入是否成功
     */
    public static function before($name, $existing, $callback)
    {
        if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE)
        {
            return self::add($name, $callback);
        }
        else
        {
            return self::insert($name, $key, $callback);
        }
    }


    /**
     * 在特定的事件组后插入特定的事件
     *
     * @param string $name 事件名称
     * @param array $existing 函数
     * @param array $callback 插入的函数
     * @return boolean 返回插入是否成功
     */
    public static function after($name, $existing, $callback)
    {
        if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name])) === FALSE)
        {
            return self::add($name, $callback);
        }
        else
        {
            return self::insert($name, $key+1, $callback);
        }
    }

    /**
     * 替换事件
     *
     * @param   string  $name  事件组名称
     * @param   array   $existing  被替换的事件
     * @param   array   $callback 新的事件
     * @return  boolean 替换是否成功
     */
    public static function replace($name, $existing, $callback)
    {
        if (empty(self::$events[$name]) OR ($key = array_search($existing, self::$events[$name], TRUE)) === FALSE)
        return FALSE;

        if ( ! in_array($callback, self::$events[$name], TRUE))
        {
            self::$events[$name][$key] = $callback;
        }
        else
        {
            unset(self::$events[$name][$key]);
            self::$events[$name] = array_values(self::$events[$name]);
        }
        return TRUE;
    }

    /**
     * 运行一个事件，并传入相关参数，结果是每次运行函数的和
     *
     * @param string $name Event Name
     * @param array $args 相关参数
     * @return bool 如果运行了事件就返回真，否则返回假
     */
    public static function run($name, $args='')
    {

        if( $callbacks = self::event($name) )
        {
            $args = func_get_args();
            $str = '';
            //zotop::dump($callbacks);
            foreach($callbacks as $callback)
            {
                $str .=(string)call_user_func_array($callback,array_slice($args,1));
            }
            return $str;
        }
        return false;
    }

    /**
     * 运行一系列的对$var的处理函数，最终返回的还是$var,此函数与run相似，只是功能不同，返回的结果不同
     *
     * @param string $name 事件 的 名称
     * @param mix $value 待处理的数据，可以使string，也可以是array或者其它数据
     * @return mix 处理后的$value
     */
    public static function filter($name, $value)
    {
        if($callbacks = self::event($name))
        {
            //处理可能的传入的多个参数,其他参数为辅助参数
            $args=func_get_args();
            foreach($callbacks as $callback)
            {
                $args[1] = $value;
                $value = call_user_func_array($callback , array_slice($args,1));
            }
        }
        return $value;
    }

    /**
     * 系统config设置函数
     *
     * @param string|array $key string:设置的键名获取或者设置该键值，array：配置数组赋值数组，空返回整个设置数组
     * @param string $value 键值，用于赋值
     * @return mix
     */
    public static function config($key='', $value=null)
    {
        static $configs=array();
        //value 值不为空，则为赋值
        if(!is_null($value))
        {
            $configs[strtolower($key)]=$value;
            return ;
        }
        //$name 为空，则获取整个config数组
        if(empty($key))
        {
            return $configs;
        }
        //$name为数组，则追加整个数组
        if(is_array($key))
        {
            $configs=array_merge($configs,array_change_key_case($key));
            return $configs;
        }
        $key=strtolower($key);
        if( ! array_key_exists($key,$configs))
        {
            return NULL;
        }
        return $configs[$key];
    }

    public static function application($id='', $key='')
    {
        static $applications = array();
        if( empty($applications) )
        {
            $applications = zotop::config('zotop.application');
        }
        if( empty($id) )
        {
            return $applications;
        }
        if( is_array($id) )
        {
            $applications = array_merge($applications , $id);
            return $applications;
        }
        $application = array();
        if( isset($applications[strtolower($id)]) )
        {
            $application = $applications[strtolower($id)];
        }
        if( isset($application) )
        {
            $application['path'] = ZOTOP.DS.$application['path'];
            $application['url'] = url::root().'/'.$application['url'];
            $application['base'] = trim($application['url'],'/').'/'.$application['base'];
        }
        if(empty($key))
        {
            return $application;
        }
        return $application[$key];
    }

    public static function module($id='', $key='')
    {
        static $modules = array();
        if( empty($modules) )
        {
            $modules = zotop::config('zotop.module');
        }
        if( empty($id) )
        {
            return $modules;
        }
        if( is_array($id) )
        {
            $modules = array_merge($modules , $id);
            return $modules;
        }
        if( isset($modules[strtolower($id)]) )
        {
            $module = $modules[strtolower($id)];
        }
        if( !isset($module) )
        {
            $module = array('id'=>$id , 'name'=>$id , 'path'=>$id , 'url'=>$id , 'type'=>'system','status' => '0','publishtime' => '0','installtime' => '0','updatetime' => '0');
        }
        //修正module的路径
        if(empty($module['path']))
        {
            $module['path']	= $module['id'];
        }
        $module['root'] = ZOTOP.DS.'modules'.DS.$module['path'];
        $module['url'] = url::zotop().'/modules/'.$module['path'];
        if(empty($key))
        {
            return $module;
        }
        return $module[strtolower($key)];
    }

    //存储简单类型数据，字符串，数组等,$file=完整的名称
    public static function data($file, $value='', $expire=0)
    {
        static $files = array();
        
		if( strtolower(substr($file , -4)) != '.php' && strpos($file, DS) == false )
		{
			$file = ROOT.DS.str_replace( '.', DS, $file).'.php';
		}

        if(''!== $value)
        {
            if(is_null($value))
            {
                $result=unlink($file);
                if($result)
                {
                    unset($files[$file]);
                }
                return $result;
            }
            else
            {
                $content = "<?php\nif (!defined('ZOTOP')) exit();\n//".sprintf('%012d',$expire)."\nreturn ".var_export($value,true).";\n?>";
                $result	= file_put_contents($file,$content);
                $files[$file] = $value;
            }
            return true;
        }
        if(isset($files[$file]))
        {
            return $files[$file];
        }
        if(file_exists($file) && false !== $content = file_get_contents($file))
        {
            $expire=(int)substr($content,strpos($content,'//')+2,12);
            if($expire!= 0 && time() > filemtime($file) + $expire)
            {
                unlink($file);//过期删除
                return false;
            }
            $value = eval(substr($content,strpos($content,'//')+14,-2));
            $files[$file] = $value;
        }
        else
        {
            $value = false;
        }
        return $value;
    }

    public static function instance($classname,$method='',$args=array())
    {
        static $instances = array();
        $id = empty($args) ? strtolower($classname.$method) : strtolower($classname.$method.rand::guid($args));
        if(!isset($instances[$id]))
        {
            if(class_exists($classname))
            {
                $instance = new $classname();
                if(method_exists($instance,$method))
                {
                    $instances[$id] = call_user_func_array(array(&$instance, $method), $args);
                }
                else
                {
                    $instances[$id] = $instance;
                }
            }
            else
            {
                zotop::error($classname.' not found!');
            }
        }
        return $instances[$id];
    }



    /**
     * 输出变量的内容
     * @param mixed $var 要输出的变量
     * @param string $label 多个变量输出问题可以加上标签
     * @param boolean $return 是否返回输出内容
     */
    //TODO 多个变量支持
    public static function dump($var, $return=false)
    {
        $content = "\n<pre>\n".'('.gettype($var).') '.htmlspecialchars(print_r($var, TRUE))."\n</pre>\n";
        if($return)
        {
            return $content;
        }
        else
        {
            echo $content;
            return false;
        }
    }

    public static function error($code='',$title='',$message='',$type='')
    {
        static $error = array('code'=>0,'title'=>'ERROR','message'=>'');
        if(is_int($code)){
            $error['code']	=	$code;
            if(!empty($message))
            {
                $error['title'] = $title;
                $error['message'] = $message;
            }
            else
            {
                $error['message'] = $title;
            }
        }
        if(is_array($code))
        {
            $error = array_merge($error,array_change_key_case($code));
        }
        if(is_string($code))
        {
            if(empty($title))
            {
                $error['message'] = $code;
            }
            else
            {
                $error['title'] = $code;
                $error['message'] = $title;
            }

        }
        if($error['code'] < 0)
        {
            //记录错误
        }
        msg::error($error['title'],$error['message']);
        exit;
    }

    /**
     * 性能测试，通过标记记录特定区域之间的性能
     *
     * @param string $start 标记点名称
     * @param string $end 结束标记点名称
     * @param int $decimals 返回的数据有效位数
     * @return array|bool
     */
    public static function mark($start='', $end='', $decimals = 4)
    {
        static $marks=array();
        //zotop::mark(),返回全部标记
        if(empty($start))
        {
            return $marks;
        }
        //zotop::mark($name),做一个标记
        $start = strtolower($start);
        if(empty($end))
        {
            $marks[$start]['time']=microtime(TRUE);
            $marks[$start]['memory']=function_exists('memory_get_usage') ? memory_get_usage() : 0 ;
            return true;
        }
        //zotop::mark($start,$end,6);返回两个标记之间的数据
        $end = strtolower($end);
        if( !isset($marks[$end]) )
        {
            self::mark($end);
        }
        return array(
			'time'=>number_format($marks[$end]['time']-$marks[$start]['time'],$decimals),//返回单位为秒
			'memory'=>number_format(($marks[$end]['memory']-$marks[$start]['memory'])/1024/1024,$decimals) //返回单位为Mb
        );
    }


    public static function cookie($name='', $value='', $expire='', $path='', $domain='')
    {
        $prefix = zotop::config('zotop.cookie.prefix');
        $prefix = empty($prefix) ? 'zotop_' : $prefix;

        $name = isset($name) ? str_replace('.', '_', $prefix.$name) : null ;


        $expire = empty($expire) ? zotop::config('zotop.cookie.expire') : $expire;
        $expire = empty($expire) ? 0 : (int)$expire + time();

        $path = empty($path) ? zotop::config('zotop.cookie.path') : $path;
        $path = empty($path) ? '/' : $path;

        $domain = empty($domain) ? zotop::config('zotop.cookie.domain') : $domain;
        $domain = empty($domain) ? '' : $domain;

        if( $name === null)
        {

            unset($_COOKIE); //zotop::cookie(null) , 清除全部的cookie;
            return true;
        }

        if( $name === '' ) return $_COOKIE;

        if( $value === null )
        {
            unset($_COOKIE[$name]);
            return setcookie($name,'', time()-3600 , $path, $domain);
        }
        if( $value === '' )
        {
            if( isset($_COOKIE[$name]) )
            {
                $value   = $_COOKIE[$name];
                $value   =  unserialize(base64_decode($value));
                return $value;
            }
            return false;
        }
        //设置cookie
        $value   =  base64_encode(serialize($value));
        return setcookie($name, $value, $expire, $path, $domain);
    }



    public static function t($string, $params=array())
    {
        if(is_array($params))
        {
            foreach($params as $key=>$value)
            {
                $string = str_ireplace('{$'.$key.'}', $value, $string);
            }
        }
        return $string;
    }



    public static function url($uri , $params=array() , $fragment='')
    {
        return url::build($uri,$params,$fragment);
    }



    public static function redirect($uri , $params=array() , $fragment='')
    {
        $url = zotop::url($uri,$params,$fragment);
        header("Location: ".$url);
        exit();
    }

    /**
     * 缓存
     *
     * @param $config array  数据库参数
     * @return object 数据库连接
     */
	public static function cache($name, $value='', $expire=0)
	{

	}

    /**
     * 连接系统默认的数据库
     *
     * @param $config array  数据库参数
     * @return object 数据库连接
     */
    public static function db($config = array())
    {
		if( empty($config) )
		{
			$config = zotop::config('zotop.database');
		}
		$db = database::instance($config);

		return $db;
    }

    public static function user($key='')
    {
        $user = array();

        if( empty($user) )
        {
            $user = zotop::cookie('zotop.user');
            $user = is_array($user) ? array_change_key_case($user) : array();
        }

        if( $key === null )
        {
            return zotop::cookie('zotop.user',null);
        }

        if( empty($key) )
        {
            return empty($user) ? false : $user;
        }

        if( is_array($key) )
        {
            $user = array_merge($user , array_change_key_case($key));
            return zotop::cookie('zotop.user',$user);
        }

        $value = $user[strtolower($key)];

        if( isset($value) )
        {
            return $value;
        }
        return null;
    }
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的运行时类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_runtime
{
    

}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的应用类 Application
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_application
{
    /**
     * 应用程序初始化
     *
     * @return null
     */
    public static function init()
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
    
    public static function runtime()
    {
        if( !is_file(ZOTOP_RUNTIME.DS.'~runtime.php') )
        {
            $registers = zotop::register();    
            $content = array();
            $content[] = '<?php';
            foreach($registers as $file)
            {
                $content[] = file::compile($file);  
            }
            $content[] = '?>';
            file::write(ZOTOP_RUNTIME.DS.'~runtime.php',implode("\n",$content));
        }
        //缓存配置文件
        zotop::data(ZOTOP_RUNTIME.DS.'~config.php',zotop::config());        
    }

    /**
     * 应用程序执行
     *
     *
     * @return null
     */
    public static function run()
    {
        $className = application::getControllerName();
        $classPath = application::getControllerPath();
        $method = application::getControllerMethod();
        $arguments = router::arguments();        
        
        //加载controller
        if( file_exists($classPath) )
        {
            zotop::load($classPath);
        }
        else
        {
           zotop::run('system.404',array('filepath'=>$classPath));
           return false; 
        }
        if( class_exists($className,false) )
        {
            $controller=new $className();
            if(method_exists($controller,$method) && $method{0}!='_')
            {
                return call_user_func_array(array($controller,$method),$arguments);
            }
            //当方法不存在时，默认调用类的_empty()函数，你可以在控制器中重写此方法
            return call_user_func_array(array($controller,'__empty'),$arguments);
        }
        return true;
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

     /**
     * 错误控制器
     *
     * @param string $errno 	错误代码
     * @param string $message 	错误信息
     * @param string $file 		错误发生的文件名称
     * @param string $line 		错误行号
     * @return string
     */   
    public static function error($errno, $message='', $file='', $line=0, $extra=array())
    {
        switch ($errno)
        {
            case E_ERROR:
            case E_USER_ERROR:
                $error = "[{$errno}] {$message} {$file} 第 {$line} 行.";
                exit('<div style="color:red;">'.$error.'</div>');
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                $error = "[{$errno}] {$message} {$file} 第 {$line} 行.";
                //echo('<div style="color:red;">'.$error.'</div>');
                break;
        }
    }
    /**
     * 异常处理，待完善
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

    /**
     * 404错误显示
     *
     */
    public static function show404($data)
    {
        msg::error('404 error',zotop::t('<h2>未能找到相应页面，请检查页面文件是否存在？</h2>{$filepath}',$data));
    }

    public static function getApplication()
    {
        return router::application();
    }
    
    /**
     * 返回当前的模块名称
     *
     * @return string
     */
    public static function getModule()
    {
        $module = router::module();
        if( empty($module) )
        {
            $module = zotop::application(APP_NAME,'module');
             
        }
       
        return empty($module) ? 'zotop' : $module;
    }

    /**
     * 返回当前URL路由的控制器名称，如果未能获取路由分发的控制器，则获取当前应用的默认路由
     *
     * @return string
     */
    public static function getController()
    {
        $controller = router::controller();
              
        if( empty($controller) )
        {
            $controller = zotop::application(APP_NAME,'controller');
             
        }
        return empty($controller) ? 'index' : $controller;
    }

    /**
     * 返回当前URL路由的动作名称，未能获取则返回当前应用的默认动作
     *
     * @return string
     */
    public static function getAction()
    {
        $action = router::action();
        if( empty($action) )
        {
            $action = zotop::application(APP_NAME,'action');
             
        }
        return empty($action) ? 'default' : $action;
    }
    
    
    /**
     * 返回当前的控制器的真实名称，含“_controller”
     *
     * @return string
     */
    public static function getControllerName()
    {
        $controller = application::getController();
        if( empty($controller) )
        {
            $controller = zotop::application(APP_NAME,'controller');
            $controller = empty($controller) ? 'index' : $controller;
        }
        return $controller.'_controller';
    }

    /**
     * 返回当前的控制器的真实路径
     *
     * @return string
     */
    public static function getControllerPath()
    {
        $controller = application::getController();
        $module = application::getModule();
        $path = zotop::module($module,'root').DS.router::application().DS.$controller.'.php';
        return $path;
    }
    
    /**
     * 返回当前触发的控制器的真实方法名称，一般如“onEdit”
     *
     * @return string
     */
    public static function getControllerMethod()
    {
        $action = application::getAction();
        if($action)
        {
            return 'on'.ucfirst($action);
        }
        return 'onDefault';
    }
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的路由类，完成对url的解析
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_router
{
    public static $uri = '';
    public static $application = '';
    public static $module = '';
    public static $controller = '';
    public static $action = '';
    public static $arguments = array();

    public static function init()
    {
        $uri = '';
        if( PHP_SAPI === 'cli' )
        {
            //cli
        }
        elseif( isset($_GET['zotop']) ) //URL兼容模式，最先获取的是唯一参数，index.php?zotop=cms/index/index/1/2
        {
            $uri = $_GET['zotop'];
        }
        elseif( isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] ) //pathinfo模式：index.php/cms/index/index/1/2
        {
            $uri = $_SERVER['PATH_INFO'];
        }
        elseif( isset($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] )
        {
            $uri = $_SERVER['ORIG_PATH_INFO'];
        }
        //elseif( isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF' ])
        //{
       //     $uri = $_SERVER['PHP_SELF'];
       // }
        
       // if (($pos = strpos($uri, APP_BASE)) !== FALSE)
       // {
            //$uri = (string) substr($uri, $pos + strlen(APP_BASE));
       // }
        router::$uri = trim($uri,'/');
        router::$uri = preg_replace('#//+#', '/', router::$uri);
        router::$uri = router::alias(router::$uri);
    }

    /**
     * URI别名，实现自定义路由，如果需要请覆写此函数
     *
     * @param string $uri 当前的URI
     * @return string 处理过的URI
     */
    public static function alias($uri)
    {
        return $uri;
    }

    public static function execute()
    {
        if($uri = trim(router::$uri , '/') )
        {
            router::$arguments = explode('/',$uri);
            router::$module = array_shift(router::$arguments);
            router::$controller = array_shift(router::$arguments);
            router::$action = array_shift(router::$arguments);
        }
        else
        {
            //当$uri 为空，则尝试Query_string模式
            router::$arguments = $_GET;
            router::$module = arr::take('module',router::$arguments);
            router::$controller = arr::take('controller',router::$arguments);
            router::$action = arr::take('action',router::$arguments);
        }
    }

    /**
     * 获取当前的application，如：admin,site
     *
     * @return string
     */
    public static function application()
    {
        return empty(self::$application) ? APP_NAME : self::$application;
    }

    /**
     * 获取当前的模型名称
     *
     * @return string;
     */
    public static function module()
    {
        return self::$module;
    }

    public static function controller()
    {
        return self::$controller;
    }

    public static function action()
    {
        return self::$action;
    }

    public static function arguments()
    {
        $arguments = array();
        $args = (array)router::$arguments;
        foreach($args as $arg)
        {
           $arguments[] = url::decodeParam($arg);     
        }
        return $arguments;
    }

}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的模块类，完成对模块的基本操作
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_module
{

}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统控制器类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_controller
{
    public function __empty()
    {

    }
    
    public function __init()
    {
        
    }
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统模型类，所有的模型都继承自此类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_model
{
    protected $db = null; // 当前数据库操作对象
    protected $modelName = ''; //模型名称
    protected $tableName = ''; //数据表名称
    protected $tablePrefix = ''; //数据表的前缀
    protected $primaryKey = ''; //主键名称
    protected $data = array(); //属性设置
    
	public function __construct()
	{
		if ( ! is_object($this->db))
		{
	        $this->db  = zotop::db();
		}
	}

    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->data[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 获取当前的db对象
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function db()
    {
        return $this->db;
    }

    /**
     * 得到当前的模型名称
     * 
     * @access public
     * @return string
     */
    public function getModelName()
    {
        if( empty($this->modelName) )
        {
            $this->modelName =   substr(get_class($this),0,-5);
        }
        return $this->modelName;
    }

    /**
     * 得到当前的数据表名称
     * 
     * @access public
     * @return string
     */
    public function getTableName($fullName = false)
    {
        if( empty($this->tableName) )
        {
            $this->tableName =  $this->getModelName();
        }
        if( $fullName )
        {
            return $this->getTablePrefix().$this->tableName;
        }
        return $this->tableName;
    }

    /**
     * 得到当前的数据表的前缀名称
     * @access public
     * @return string
     */
    public function getTablePrefix()
    {
        if( empty($this->tablePrefix) )
        {
            $this->tablePrefix =  $this->db->config('prefix');
        }
        return $this->tablePrefix;
    }

    /**
     * 获取字段信息并缓存
     * 
     * @access public
     * @return void
     */    
    public function getTableMeta($flush=false)
    {
        static $table;
        
        $tableName = $this->getTableName(true);
        $dataName = 'zotop.data.tables.'.$tableName;
        
        if( empty($table) )
        {
           $table = zotop::data($dataName);
        }  
        
        //如果缓存不存在
        if( empty($table) or $flush===true )
        { 
           $table = $this->flush();
        }
        return $table;
    }
    
    /**
     * 得到当前的数据表的主键名称
     * 
     * @access public
     * @return string
     */
    public function getPrimaryKey()
    {
        if( empty($this->primaryKey) )
        {
            $tableName = $this->getTableName(true);
            $tableMeta = $this->getTableMeta();
            if( $tableMeta )
            {
                $this->primaryKey = $tableMeta['primarykey'];
            }
            if( empty($this->primaryKey) )
            {
               $this->primaryKey = $this->db()->table($tableName)->primaryKey();
            }
        }
        return $this->primaryKey;
    }

    /**
     * 刷新数据表的meta数据
     * 
     * @access public
     * @return string
     */
    public function flush()
    {
        //获取字段信息
        $tables = $this->db()->tables(true);
        if( is_array($tables) )
        {
            $table = $tables[$tableName];
            if( is_array($table) )
            {
                $fields = $this->db()->table($tableName)->fields();
                $primaryKey = $this->db()->table($tableName)->primaryKey();
                $table['primarykey'] = $primaryKey;
                $table['fields'] = (array)$fields;
                //写入table数据
                zotop::data($dataName,$table);
                //返回table数据
                return $table;
            }
        }
        return false;          
    }
    
    

    
    /**
     * 读取具体的某条数据
     * 
  	 * 空条件：$model->read();前面必须定义过主键值： $model->id = 1; 
     * 默认条件：$model->read(1) 相当于 $model->read(array('id','=',1))
     * 自定义条件：$model->read(array('id','=',1))
     * 
     * @param mix $value 键值
     * 
     * @return array
     */
    public function read($value='')
    {
        if( is_array($value) )
        {
            $this->db()->where($value);   
        }
        else
        {
            $key = $this->getPrimaryKey();
            if( empty($value) )
            {
                $value = $this->$key;
            }
            $this->db()->where($key,'=',$value);
        }
        
        $this->data = $this->db()->select('*')->from($this->getTableName())->getRow();
        
        if( $this->data===null )
        {
            msg::error('读取失败',zotop::t('未能找到 <b>{$key}</b> 等于 <b>{$value}</b> 的数据<br>'.reset($this->db->sql()),array('key'=>$key,'value'=>$value)));            
        }
		return $this->data;    
    }
    
    /**
     * 更新数据
     * 
     * @param mix $data 待更新的数据
     * @param mix $where 条件
     * 
     * @return array
     */    
    public function update($data=array() , $where = array() )
    {        
        if( is_array($data) )
        {
            $this->db()->set($data);
        }
                
        $key = $this->getPrimaryKey();
        
        $set = $this->db()->sqlBuilder('set');       
        
        if( empty($where) )
        {
            if( isset($set[$key]) )
            {
                $where = array($key,'=',$set[$key]);
            }
            else
            {
                $where = array($key,'=',$this->$key);
            } 
        }
        if( is_numeric($where) || is_string($where) )
        {
    
            $where = array($key,'=',$where);
        }
        
        if( is_array($where) )
        {
            $this->db()->where($where);
        }
        
        $this->db()->from($this->getTableName());
        
        return $this->db()->update();
    }

    /**
     * 创建数据
     * 
     * @param mix $data 待创建的数据
     * 
     * @return mix
     */     
    public function create($data)
    {
        
    }
    
    /**
     * 删除数据
     * 
     * @param mix $where 删除条件
     * 
     * @return mix
     */     
    public function delete($where)
    {
        
    }

}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 页面组件
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_page
{
	public static function id()
	{
	    $id = self::settings('id');
	    if( strlen($id)== 32 )
	    {
	        return $id;
	    }
	    $namespace = application::getApplication().'://'.application::getModule().'.'.application::getController().'.'.application::getAction();	    
	    $namespace = empty($id) ? $namespace : $namespace.'/'.$id;
	    $namespace = md5($namespace);
	    return $namespace;
	}
	    
    public static function header($header=array())
	{
		$header = self::settings($header);

		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$header['title'].' '. zotop::config("zotop.title").'</title>';
        $html[] = page::meta($header['meta']);
		$html[] = page::stylesheet($header['css']);
		$html[] = page::script($header['js']);
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($header['body']).'>';

		$str =  implode("\n",$html);

		echo $str;
	}

	public static function footer()
	{
	    $html[] = '';

		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public static function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop">';
		$html[] = '<div id="page">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>'.page::settings('title').'</h2>';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public static function bottom($str='')
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		if(!empty($str))
		{
			$html[] = $str;
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{$runtime}</b>,memory:<b>{$memory}</b>,includefiles:<b>{$include}</b></div>';
	    $html[] = '</div>';
		$html[] = '</div>';

		echo implode("\n",$html);
	}

	public static function navbar($data,$current='')
	{
		$html = array();

		if(is_array($data))
		{

			$current=empty($current) ? router::method(false) : $current;
			$current=empty($current) ? $data[0]['id'] : $current;

			$html[]='<div class="navbar">';
			$html[]='	<ul>';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
					$html[]='		<li class="'.$class.'"><a href="'.$item['href'].'"  id="'.$item['id'].'" class="'.$item['class'].'"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[]='		<li class="'.$class.'">'.$item.'</li>';
				}
			}
			$html[]='	</ul>';
			$html[]='</div>';
		}
		echo implode("\n",$html);
	}

	public static function settings($name='')
	{
		static $settings = array();
		
		if(empty($name)) return $settings;
		if(is_array($name))
		{
			$settings = array_merge($settings,array_change_key_case($name));
			return $settings;
		}
		return $settings[strtolower($name)];
	}

	public static function meta($metas)
	{
		//默认的meta
	    $default = array('keywords'=>'zotop cms','description'=>'simple,beautiful','Content-Type'=>'text/html;charset=utf-8','X-UA-Compatible'=>'IE=EmulateIE7');
		//用户的meta
		$metas = array_merge($default,(array)$metas);
		foreach($metas as $name=>$value)
		{
		    $html[]	= '	'.html::meta($name,$value).'';
		}
		return implode("\n",$html);
	}

	public static function stylesheet($files)
	{
		foreach($files as $file)
		{
		    $html[]	= '	'.html::stylesheet($file).'';
		}
		return implode("\n",$html);
	}

	public static function script($files)
	{
		foreach($files as $file)
		{
		    $html[]	= '	'.html::script($file);
		}
		return implode("\n",$html);
	}

	public static function add($str)
	{
		echo $str."\n";
	}


}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 表单辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_form
{
	public static $template = '';

	public static function isPostBack()
	{

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
    		return true;
		}
		return false;
	}
	
	public static function post()
	{
	    $post = array();
	    foreach($_POST as $key=>$val)
	    {
	        if($key[0] != '_' )
	        {
	            $post[$key] = $val;
	        }   
	    }	    
	    return $post;
	}

    public static function header($form=array())
	{
	    if(isset($form['template']))
		{
			form::$template = arr::take('template',$form);
		}
		$attrs['class'] = isset($form['class']) ? $form['class'] : 'form';
		$attrs['method'] = isset($form['method']) ? $form['method'] : 'post';
		$attrs['action'] = isset($form['action']) ? $form['action'] : url::current();
        //加载表头
		$html[] = '';
		$html[] = '<form'.html::attributes($attrs).'>';
        //加载常用js
		$html[] = html::script(url::common().'/js/jquery.validate.js');
		$html[] = html::script(url::common().'/js/jquery.validate.additional.js');
		$html[] = html::script(url::common().'/js/jquery.form.js');
		$html[] = html::script(url::common().'/js/zotop.form.js');
		//表单头部		
		if( isset($form['title']) || isset($form['description']) )
		{
		    $html[] = '<div class="form-header clearfix">';
		    $html[] = '	<div class="form-header-inner '.(isset($form['icon']) ? 'form-icon' : '').' clearfix"'.(empty($form['icon']) ? '' : ' style="background-image:url('.$form['icon'].');"').'>';
		    $html[] = isset($form['title']) ? '		<div class="form-title">'.$form['title'].'</div>' : '';
            $html[] = isset($form['description']) ? '		<div class="form-description">'.$form['description'].'</div>' : '';
		    $html[] = '	</div>';
            $html[] = '</div>';
		}
	    //表单body部分开始
        $html[] = '<div class="form-body">';
        $html[] = field::hidden(array('name'=>'_REFERER','value'=>request::referer()));
        
        echo implode("\n",$html);
	}
	public static function footer($str='')
	{
		$html[] = '';
	    $html[] = '</div>';
	    if( !empty($str) )
	    {
	        $html[] = '<div class="form-footer">'.$str.'</div>';
	    }
	    $html[] = '</form>';
		echo implode("\n",$html);
		form::$template = '';
	}


	public static function buttons()
	{
	    $buttons = func_get_args();
	    $html[] = '<div class="buttons">';
		foreach($buttons as $button)
		{
			$html[] = form::control($button);
		}
		$html[] = '</div>';
	    echo implode("\n",$html);
	}

	public static function field($attrs)
	{

		if($attrs['type'] == 'hidden')
		{
			echo form::control($attrs);
		}
		else
		{
			$label = arr::take('label',$attrs);
			$description = arr::take('description',$attrs);
			$str =  form::template(form::$template);
			$str = str_replace('{$field:label}',html::label($label,$attrs['name']),$str);
			$str = str_replace('{$field:required}',form::required($attrs['valid']),$str);
			$str = str_replace('{$field:description}', form::description($description),$str);
			$str = str_replace('{$field:controller}',form::control($attrs), $str);
			echo $str;
		}
	}

	public static function template($template='div')
	{
		$template = empty($template) ? 'table' : $template;
		$html = array();
		switch($template)
		{
			case 'div':
				$html[] = '';
				$html[] = '<div class="field">';
				$html[] = '	<div class="field-side">';
				$html[] = '		{$field:label}{$field:required}';
				$html[] = '		{$field:description}';
				$html[] = '	</div>';
				$html[] = '	<div class="field-main">';
				$html[] = '	{$field:controller}';
				$html[] = '	</div>';
				$html[] = '</div>';
				break;
			case 'table':
				$html[] = '';
				$html[] = '<table class="field"><tr>';
				$html[] = '	<td class="field-side">';
				$html[] = '		{$field:label}{$field:required}';

				$html[] = '	</td>';
				$html[] = '	<td class="field-main">';
				$html[] = '	{$field:controller}';
				$html[] = '	{$field:description}';
				$html[] = '	</td>';
				$html[] = '</tr></table>';
				break;
			default:
				$html[] = '';
				$html[] = $template;
				break;
		}
	    return implode("\n",$html);
	}

	public static function required($str,$required='*')
	{
		if(strpos($str,'required')!==false)
		{
			return '<span class="field-required">'.$required.'</span>';
		}
		return '';
	}

	public static function description($str)
	{
		if($str)
		{
			return '<span class="field-description">'.$str.'</span>';
		}
		return '';
	}

	public static function control($attrs)
	{
	    $html[] = '';
		if( is_array($attrs) )
		{
			$type = arr::take('type',$attrs);
			$type = isset($type) ? $type : 'text';
			$html[] = field::get($type,$attrs);
		}
		else
		{
			 $html[] = $attrs;
		}
		return implode("\n",$html);
	}

	public static function referer($url='')
	{
		static $referer;
		if(empty($url))
		{
			$url = request::post('_REFERER');
			return $url;
		}
		$referer = $url;
		return $referer;
	}

}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 表单辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_field
{

    /**
     * 设置一个新的表单控件，用于覆盖系统默认的表单控件
     *
     * function mytextarea($attrs)
     * {
     *   return field::textarea($attrs).'<div>new textarea</div>';
     * }
     * field::set('textarea','mytextarea');
     *
     *
     * @param $name  string  控件名称
     * @param $callback function 控件函数
     * @return bool
     */
    public static function set($name,$callback='')
	{
	    static $fields = array();
	    $name = strtolower($name);
	    if(!empty($callback))
	    {
	        $fields[$name] = $callback;
	    }
	    if(isset($fields[$name]))
	    {
	        return $fields[$name];
	    }
	    return false;
	}

	/**
	 * 生成一个控件的Html数据
	 *
	 *
	 * @param $name string 控件名称
	 * @param $attrs array  控件参数
	 * @return string 返回控件的代码
	 */
	public static function get($name,$attrs=array())
	{
	    $callback = field::set($name);
	    if($callback)
	    {
	        return call_user_func_array($callback,array($attrs));
	    }
		if( method_exists('field',$name) )
		{
		    return field::$name($attrs);
		}

		return 'Unkown FieldController : <b>'.$name.'</b>';
	}

    /**
     * label控件，显示一个文本
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function label($attrs)
	{
		$value = arr::take('value',$attrs);
		
	    return '<span class="label" '.html::attributes($attrs).'>'.$value.'</span>';
	}
	
	/**
     * text文本控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function text($attrs)
	{
		$attrs['type'] = 'text';
		$attrs['class'] = isset($attrs['class']) ? 'text '.$attrs['class'] : 'text';
		return html::input($attrs);
	}
    /**
     * 隐藏类型控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function hidden($attrs)
	{
		$attrs['type'] = 'hidden';
		return html::input($attrs);
	}
    /**
     * 密码输入框控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function password($attrs)
	{
		$attrs['type'] = 'password';
		$attrs['class'] = isset($attrs['class']) ? 'password '.$attrs['class'] : 'password';
		return html::input($attrs);
	}
    /**
     * 按钮控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function button($attrs)
	{
		$attrs['type'] = 'button';
		$attrs['class'] = isset($attrs['class']) ? 'button '.$attrs['class'] : 'button';
		return html::input($attrs);
	}
    /**
     * 表单提交按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function submit($attrs)
	{

	    $attrs['type'] = 'submit';
		$attrs['class'] = isset($attrs['class']) ? 'submit '.$attrs['class'] : 'submit';
		$attrs += array
		(
			'id'=>'submitform',
			'value'=>zotop::t('提 交')
		);
		return html::input($attrs);
	}
    /**
     * 表单重置按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function reset($attrs)
	{
		$attrs['type'] = 'reset';
		$attrs['class'] = isset($attrs['class']) ? 'reset '.$attrs['class'] : 'reset';
		$attrs += array
		(
			'id'=>'resetform',
			'value'=>zotop::t('重 置')
		);
		return html::input($attrs);
	}

    /**
     * 返回前页按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function back($attrs)
	{
		$attrs += array
		(
			'class'=>'zotop-back',
			'onclick'=>'history.go(-1);',
			'value'=>zotop::t('返回前页')
		);
		return field::button($attrs);
	}
    /**
     * 文本段输入控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function textarea($attrs)
	{
	    $attrs['class'] = isset($attrs['class']) ? 'textarea '.$attrs['class'] : 'textarea';
		$attrs += array
		(
			'rows'=>'8',
			'cols'=>'5'
		);
		$value = arr::take('value',$attrs);
		return '<textarea'.html::attributes($attrs).'>'.html::encode($value).'</textarea>';
	}

	/**
	 * 生成一个标准的select控件
	 *
	 * @param $attrs
	 * @return string
	 */
	public static function select($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr:take
	    //$options = html::options($options); //格式化数组
	    $value = arr::take('value',$attrs);//当value为数组时，则为多选
		if (is_array($value))
		{
			$attrs['multiple'] = 'multiple';
		}
		else
		{
			$value = array($value);
		}
		//为所有的select都加上select样式，便于全局统一控制select的样式，同input
	    if(isset($attrs['multiple']))
	    {
	        $defaultClass='select multiple';
	    }
	    else
	    {
	        $defaultClass='select';
	    }

		$attrs['class'] = isset($attrs['class']) ? $defaultClass.' '.$attrs['class'] : $defaultClass;
	    $html[] = '';
	    $html[] = '<select'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        foreach($options as $val=>$text)
	        {
	            $selected = in_array($val,$value) ? ' selected="selected"' : '';
	            $html[] = '	<option value="'.$val.'"'.$selected.'>'.html::encode($text).'</option>';
	        }
	    }
	    $html[] = '</select>';
	    $html[] = '';
	    return implode("\n",$html);
	}

	public static function dropdown($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr:take
		$html[] = '';
		$html[] = '<div class="inline-block dropdown">';
		$html[] = '<input type="text" value="'.$options[$attrs['value']].'" class="text '.$attrs['class'].'" style="'.$attrs['style'].'" readonly="readonly">';
		$html[] = '<input type="hidden" value="'.$attrs['value'].'" valid="'.$attrs['valid'].'" title="'.$attrs['title'].'" class="value">';
		$html[] = '<div class="dropdownBox">';
		$html[] = '<ul class="dropdownOptions">';
		foreach($options as $val=>$text)
		{
			$html[] = '<li rel="'.$val.'">'.$text.'</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';
		$html[] = '</div>';
	    $html[] = '';
	    return implode("\n",$html);
	}

    /**
     * 多选输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function checkbox($attrs)
	{
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr::take
	    //$options = html::options($options); //格式化数组
	    $value = arr::take('value',$attrs);//即取出了value和options，又把他们从$attrs中去掉了
		if (!is_array($value))
		{
			$value = array($value);
		}
	    $attrs['class'] = isset($attrs['class']) ? 'checkbox '.$attrs['class'] : 'checkbox';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $valid = arr::take('valid',$attrs);
		$html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {
	            $checked = in_array($val,$value) ? ' checked="checked"' : ''; //这儿代码可能有问题，请检查
	            $html[] = '<li><input type="checkbox" name="'.$attrs['name'].'[]" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.''.((isset($valid) && $i==1) ? ' valid = "'.$valid.'"':'').'/>';
				$html[] = '<label for="'.$attrs['name'].'-item'.$i.'">'.html::encode($text).'</label></li>';//这儿代码不完美
				$i++;
	        }
	    }
	    $html[] = '</ul>';
		if(isset($valid))
		{
			$html[] = '<label for="'.$attrs['name'].'[]" class="error">'.$attrs['title'].'</label>';
		}

	    return implode("\n",$html);
	}

    /**
     * 单选输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function radio($attrs)
	{
	    $options = arr::take('options',$attrs);
	    $value = arr::take('value',$attrs);	    
	    $attrs['class'] = isset($attrs['class']) ? 'radio '.$attrs['class'] : 'radio';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $valid = arr::take('valid',$attrs);
		$html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {
	            $checked = ($val==$value) ? ' checked="checked"' : ''; //这儿代码可能有问题，请检查
	            $html[] = '	<li>';
	            $html[] = '		<input type="radio" name="'.$attrs['name'].'" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.''.((isset($valid) && $i==1) ? ' valid = "'.$valid.'"':'').'/>';
				$html[] = '		<label for="'.$attrs['name'].'-item'.$i.'">'.html::encode($text).'</label>';
				$html[] = '	</li>';//这儿代码不完美
				$i++;
	        }
	    }
	    $html[] = '</ul>';
		if(isset($valid))
		{
			$html[] = '<label for="'.$attrs['name'].'" class="error">'.$attrs['title'].'</label>';
		}

	    return implode("\n",$html);
	}
    /**
     * 图片上传输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function image($attrs)
	{
	   $html[] = '<div class="field-inner inline-block">';
	   $html[] = field::text($attrs);
	   $html[] = html::input( array('type'=>'button','class'=>'upload-image','for'=>$attrs['name'],'value'=>zotop::t('上传图片'),'title'=>zotop::t('上传图片')) );
       $html[] = '</div>';
	   return implode("\n",$html);
	}

	/**
     * 文件上传框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function file($attrs)
	{
		$attrs['type'] = 'file';
		$attrs['class'] = isset($attrs['class']) ? 'file '.$attrs['class'] : 'file';
		return html::input($attrs);
	}

    /**
     * 富文本编辑器
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function editor($attrs)
	{
         return field::textarea($attrs);
	}
	
    public static function source($attrs)
    {
        $attrs['style'] = 'width:600px;height:460px;';
        return field::textarea($attrs);
    }
    /**
     * 控件组
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function group($attrs)
	{
		$html[] = '<div class="field-group">';
		$fields = arr::take('fields',$attrs);
		if(is_array($fields))
		{
			foreach($fields as $field)
			{
				if(is_array($field))
				{
					$type = arr::take('type',$field);
					$type = isset($type) ? $type : 'text';
					$field['class'] =  isset($field['class']) ? 'short '.$field['class'] : 'short';
					$html[] = '	<div class="field-group-item">';
					$html[] = '		<label for="'.$field['name'].'">'.arr::take('label',$field).'</label>';
					$html[] = '		'.field::get($type,$field);
					$html[] = '	</div>';
				}
				else
				{
					$html[] = $field;
				}
			}
		}
		else
		{
			$html[] = $fields;
		}
		$html[] = '</div>';
		return implode("\n",$html);
	}

}


/**
 * 页面辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_block
{
	/**
	 * 区块的头部输出
	 * 
	 *	
	 * @param array|string $header  头部参数
	 * @return null 输出头部的代码
	 */
	public static function header($header=array())
	{
	    if( !is_array($header) )
		{
		    $header = array('title'=>$header);
		}
	    $html[] = '';
		$html[] = '<div class="block clearfix '.$header['class'].'"'.(isset($header['id']) ? ' id="'.$header['id'].'"':'').'>';
		if(isset($header['title']))
		{
		    $html[] = '	<div class="block-header">';
		    $html[] = '		<h2>'.$header['title'].'</h2>';
		    if( isset($header['action']) )
		    {
		        $html[] = '		<h3>'.$header['action'].'</h3>';
		    }
		    $html[] = '	</div>';
		}
		$html[] = '	<div class="block-body clearfix">';
		$html[] = '';
		echo implode("\n",$html);

	}
	
	
	/**
	 * 区块的尾部输出，闭合区块代码
	 * 
	 * @return null 
	 */
	public static function footer($footer=null)
	{
		$html[] = '';
	    $html[] = '	</div>';
	    $html[] = '	<div class="block-footer">'.$footer.'</div>';
	    $html[] = '</div>';

		echo implode("\n",$html);
	}
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 简化表格输出
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_table
{
	public static function header($classname='',$titles='')
	{
		$html[] = '';
		$html[] = '<table class="table '.$classname.'">';
		if(is_array($titles))
		{
			$html[] = '<tr class="title">';
			foreach($titles as $name=>$title)
			{
				$html[] = '<th class="'.$name.'"><b>'.$title.'</b></th>';
			}

			$html[] = '</tr>';
		}
		$html[] = '	<tbody>';
		$html[] = '';
		echo implode("\n",$html);
	}
	public static function footer()
	{
		$html[] = '';
		$html[] = '	</tbody>';
		$html[] = '</table>';
		echo implode("\n",$html);
	}

	public static function row($rows,$classname='')
	{
		static $i=0;
		if(is_array($rows))
		{
			$html[] = '';
			$html[] = '		<tr class="item '.($i%2==0?'odd':'even').' '.$classname.'">';
			foreach($rows as $key=>$value)
			{
				if( is_string($value) )
				{
					$html[] = '			<td class="'.$key.'">'.$value.'</td>';
				}
				else
				{
					$inner = arr::take('inner',$value);

					$html[] ='			<td class="'.$key.'" '.html::attributes($value).'>'.$inner.'</td>';
				}
			}
			$html[] = '		</tr>';
			$i++;
		}
		echo implode("\n",$html);
	}



}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 消息提示
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_msg
{
    public static function show($msg=array())
    {
		$header['title'] = '用户登录';
		$header['body']['class']="msg";

		page::header($header);


			page::add('<div id="msg" class="'.$msg['type'].' clearfix">');
			page::add('	<div id="msg-type">'.$msg['type'].'</div>');
			page::add('	<div id="msg-life">'.$msg['life'].'</div>');
			page::add('	<div class="zotop-msg zotop-msg-'.$msg['type'].'">');
			page::add('		<div class="zotop-msg-icon"></div>');
			page::add('		<div class="zotop-msg-content">');
			page::add('			<div id="msg-title">'.$msg['title'].'</div>');
			page::add('			<div id="msg-content">'.$msg['content'].'</div>');
			page::add('			<a href="'.$msg['url'].'" id="msg-url">'.$msg['url'].'</a>');
			page::add('			<div id="msg-extra">'.$msg['extra'].'</div>');
			page::add('			<div id="msg-powered">'.zotop::config('zotop.name').' '.zotop::config('zotop.version').'</div>');
			page::add('		</div>');
			page::add('		</div>');

			page::add('</div>');


		page::footer();
		exit;
    }

    public static function error($title, $content='', $life=9 , $extra='')
    {
        $msg = array();
        $msg['type'] = 'error';
        $msg['title'] = empty($content) ? 'error' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
		$msg['extra'] = $extra.'<div class="msg-title"><b>如果问题未能解决，请尝试以下操作：</b></div><ul><li>· 点击<a href="javascript:location.reload();"> 刷新 </a>重试，或者以后再试</li><li>· 或者尝试点击<a href="javascript:history.go(-1);"> 返回前页 </a>后再试</li></ul>';
        $msg['life'] = $life;
        msg::show($msg);
    }

    public static function success($title, $content='', $url='', $life=3, $extra='')
    {
        $msg = array();
        $msg['type'] = 'success';
        $msg['title'] = empty($content) ? 'success' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['extra'] = $extra.'';
        $msg['url'] = $url;
        $msg['life'] = $life;
        msg::show($msg);
    }
    public static function alert($title,$content='',$life=0)
    {
        $msg = array();
        $msg['type'] = 'alert';
        $msg['title'] = empty($content) ? 'alert' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['life'] = $life;
        msg::show($msg);
    }

    public static function template()
    {
    }
}


class zotop_path
{
	/* path解析
	 *
	 * @parem string $path 路径名称
	 * @return string
	 */
	public static function decode()
	{

	}

	public static function encode()
	{

	}

	public static function clean($path,$ds='')
	{
		$path = trim($path);
		$ds = empty($ds) ? DS : $ds;
		if(empty($path))
		{
			return ZOTOP;
		}
		//$path = realpath($path);
		return preg_replace('#[/\\\\]+#', $ds, $path); //清理并转化
	}
}


class zotop_dir
{

	public static function exists($dir)
	{
		return is_dir(path::clean($dir));
	}

	public static function size($dir)
	{
		$handle = @opendir($dir);
        $size = 0;
		while (false!==($f = readdir($handle)))
        {

			if($f != "." && $f != "..")
            {
                if(is_dir("$dir/$f"))
                {
                    $size += dir::size("$dir/$f");
                }
                else
                {
                    $size += filesize("$dir/$f");
                }
            }
        }
        @closedir($handle);
        return $size;
	}

	//创建文件夹，返回true或者false
	public static function create($dir, $mode = 0755)
	{
	  if(is_dir($dir) || @mkdir($dir,$mode))
	  {
		  return true;
	  }
	  if(!dir::create(dirname($dir),$mode))
	  {
		  return false;
	  }
	  return @mkdir($dir,$mode);
	}

	//删除文件夹
	public static function delete($dir)
	{

	}
    
	/**
	 * 返回目录下的全部文件的数组
	 * @param string $path 路径
	 * @param array $ext 特定的文件格式,如只获取jpg,png格式
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */
	public static function files($path, $ext='', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{

    	$files =array();   
	    $path = path::clean($path);
	    if( !is_dir($path) )
	    {
	        return false;
	    }
	    $handle = opendir($path);
	    while (($file = readdir($handle)) !== false)
	    {
	        if( $file != '.' && $file != '..' && !in_array($file,$ignore) )
	        {
	            $f = $path .DS. $file;
	            if( is_dir($f) )
	            {
	                if ( $recurse )
	                {
    	                if( is_bool($recurse) )
    	                {
    	                    $subfiles = dir::files($f,$ext,$recurse,$fullpath);
    	                }
    	                else
    	                {
    	                    $subfiles = dir::files($f,$ext,$recurse-1,$fullpath);
    	                }
                    	if( is_array($subfiles) )
	                    {
	                        $files = array_merge($files,$subfiles);
	                    }    	                
	                }	                
	            }
	            else
	            {   
	                if( !empty($ext) )
	                {
	                    if( is_array($ext) && in_array(file::ext($file),$ext) )
	                    {
	                        $files[] = $fullpath ? $f :  $file;
	                    }
	                }
	                else
	                {
	                    $files[] = $fullpath ? $f :  $file;
	                }
	                
	            }
	        }
	       
	        
	        
	    }
	    closedir($handle);
	    return $files;
	}
	/**
	 * 返回目录下的全部文件夹的数组
	 * @param string $path 路径
	 * @param array $filter 
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */	
	public static function folders($path, $filter='.', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{
	    $folders = array();
	    $path = path::clean($path);
	    
	    if( !is_dir($path) )
	    {
	       return false;
	    }
	    
	    $handle = opendir($path);
	    while (($file = readdir($handle)) !== false)
	    {
	        $f = $path .DS. $file;
	        if( $file != '.' && $file != '..' && !in_array($file,$ignore) && is_dir($f) )
	        {
                if (preg_match("/$filter/", $file)) {
                	if ($fullpath) {
                		$folders[] = $f;
                	} else {
                		$folders[] = $file;
                	}
                }
	            if ($recurse) {
					if (is_integer($recurse)) {
						$recurse--;
					}
					$subfolders = dir::folders($f, $filter, $recurse, $fullpath, $ignore);
					$folders = array_merge($folders, $subfolders);
				}       
	        }	        
	    }
	    closedir($handle);
	    return $folders;	    
	}
}


class zotop_file
{

	/**
	 * 获取文件的扩展名
	 * @param string $file 文件名称
	 * @return string
	 */
	public static function ext($file)
	{
		$dot = strrpos($file, '.') + 1;
		return substr($file, $dot);
	}

	/**
	 * 获取文件名称 , 第二个参数控制是否返回文件扩展名，默认为返回带扩展名的文件名称
	 * @param string $file
	 * @param boolean $stripext
	 * @return string
	 */
	public static function name($file,$stripext=false)
	{
		$name=basename($file);
		if($stripext)
		{
			$ext = file::ext($file);
			$name=basename($file,'.'.$ext);
		}
		return $name;
	}

	/**
	 * 判断文件是否存在
	 * @param string $file
	 * @return boolean
	 */
	public static function exists($file)
	{
	    if (empty($file)) return false;
	    $file = path::clean($file);
	    return is_file($file);
	}


	/**
	 * 读取文件内容
	 *
	 * @param string $file
	 * @return string
	 */
	public static function read($file)
	{
       $file = path::clean($file);
       return @file_get_contents($file);
	}


	/**
	 * 写入文件
	 *
	 * @param string $file
	 * @param string $content
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public static function write($file , $content='' , $overwrite=TRUE)
	{
	    //当目录不存在的情况下先创建目录
	    if (!dir::exists(dirname($file)))
		{
			dir::create(dirname($file));
		}

		if (!file::exists($file) || $overwrite)
		{
		    $file = path::clean($file);
		    return @file_put_contents($file,$content);
		}
        return false;
	}


	/**
	 * 删除文件
	 * @param string $file
	 * @return boolean
	 */
	public static function delete($file)
	{

	    if (file::exists($file))
	    {
	        $file = path::clean($file);
	        return @unlink($file);
	    }
	    return true;
	}

	/**
	 * 返回目录下的全部文件的数组,当level为0时候返回全部子文件夹目录
	 * @param string $path 路径
	 * @param array $ext 特定的文件格式,如只获取jpg,png格式
	 * @param bool|int $recurse 子目录，或者子目录级数
	 * @param bool $fullpath 全路径或者仅仅获取文件名称
	 * @param array $ignore 忽略的文件夹名称
	 * @return array
	 */
	public static function brower($path, $ext='', $recurse=false, $fullpath=false, $ignore = array('.svn', 'CVS','.DS_Store','__MACOSX'))
	{
        return dir::files($path,$ext,$recurse,$fullpath,$ignore);
	}

	public static function copy()
	{
	    return false;
	}

	public static function move()
	{
        return false;
	}

    /**
     * 上传文件
     *
     * @param string $name  FILE字段名称
     * @param string $path  上传的路径
     * @param string $ext   扩展名
     * @param boolean $rename 是否重新命名
     * @return array
     */
    public static function upload($name,$path,$ext,$rename=true)
    {
	    if (!dir::exists(dirname($path)))
		{
			dir::create(dirname($path));
		}
		$ext = explode(',',$ext);
		
		$files = $_FILES[$name];
		
		$attachments = array();
		//转换数组
		if(is_array($files['name']))
		{
                    foreach($files as $key => $var)
                    {
                            foreach($var as $id => $val)
                            {
                               $attachments[$id][$key] = $val;
                            }
                    }
		}
		else
		{
		    $attachments[] =$files;
		}



		//上传
		$return = array();
		foreach ($attachments as $k=>$file)
		{
		    if (in_array(self::ext($file['name']),$ext))
		    {
		        $tmp = $path;
		        if ($rename)
		        {
		            $tmp .=DS.rand::string(10).self::ext($file['name']);
		        }
		        else
		        {
		            $tmp .=DS.$file['name'];
		        }

		        @move_uploaded_file($file['name'],$tmp);
                        $return[] = $tmp;
                        @unlink($file['tmp_name']);
		    }
		    else
		    {
		        $return[] = false;
		    }
		}

		return $return ;
	}

	public static function find()
	{

	}
	
	public static function compile($file)
	{
	    $content = file::read($file);
	    $content = trim($content);
	    //strip <?php	    

	    $content = substr($content,5); 
	    //strip <?php
		if( strtolower(substr($content,-2)) == '?>' )
	    {
	       $content = substr($content,0,-2); 
	    }
	    return $content;	    
	}
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_arr
{
	/**
	 * 从数组中弹出键，类似于array_pop,但是是根据键名弹出
	 *
	 * @param string $key 弹出的键名
	 * @param array $array 目标数组
	 * @param boolean $bool 是否区分大小写
	 * @return $mix	被弹出 的数据
	 */
	public static function take($key,&$array,$bool=TRUE)
	{
		$array = (array)$array;
		if($bool)
		{
			$key=strtolower($key);
			$array=array_change_key_case($array);
		}

		if(array_key_exists($key,$array))
		{
			$str=$array[$key];
			unset($array[$key]);
			return $str;
		}
		return NULL;
	}
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 字符串操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_string
{
	public static function substr($str, $start, $length, $charset='utf-8')
	{
		if(function_exists("mb_substr"))
		{
			return mb_substr($str, $start, $length, $charset);
		}
		elseif(function_exists('iconv_substr'))
		{
			return iconv_substr($str,$start,$length,$charset);
		}
		$regex['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$regex['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$regex['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$regex['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($regex[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		return $slice;
	}

	public static function len($str, $charset='utf-8')
	{

	}


	/*
	 * 格式化字符串或者数组，去掉首尾空白字符与空白的字符串项
	 *
	 * $input='item1, item2, ,item3';
	 * $output=string::split($input,',');	 *
	 * $output 现在是一个数组
	 * $output = array('item1','item2','item3');
	 *
	 * @return array
	 */
	public static function split($input , $delimiter = ',' , $trim=true , $empty=false)
	{
        if (!is_array($input)){
            $input = explode($delimiter, $input);
        }
		if ($trim){
        	$input = array_map('trim', $input);
		}
		if (!$empty){
			$input = array_filter($input, 'strlen');
		}
		return $input;
	}
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * URL操作类，完成对URL的操作
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_url
{
    /**
     * 根据参数生成完整的URL，如：url::build('zotop/index/default',array('id'=>'1'))
     *
     * @param string 		$uri 		一般由{module}/{controller}/{action}组成
     * @param array|string 	$params 	URL参数 ，一般为数组
     * @param string 		$extra	 	额外参数 ，一般为数组
     * @return string
     */
    public static function build($uri , $params = '' , $extra = '')
    {
        $app = '';
        $uri = trim($uri , '/');
        if( strpos($uri , '://') !== false )
        {
            $uris = explode('://',$uri);
            $app = $uris[0];
            $uri = $uris[1];
        }
        $urls = array();
        $urls['base'] =empty($app) ? url::scriptname() : url::application($app);
        if( $paths = explode('/',trim($uri,'/')) )
        {
            $urls['module'] = $paths[0];
            $urls['controller'] = $paths[1];
            $urls['action'] = $paths[2];
        }

        $url = $urls['base'];
        $url = $url.'/'.$urls['module'].'/'.$urls['controller'].'/'.$urls['action'];

		if( !empty($params) )
		{
			if( is_string($params) )
			{
			    $keyvalues = explode('&',$params);
			    $params = array();
			    foreach($keyvalues as $keyvalue)
			    {
			        list($key,$value) = explode('=',$keyvalue);
			        if($value)
			        {
			            $params[$key] = $value;
			        }
			    }
			}
		    foreach($params as $key=>$value)
			{
				$url .= '/'.url::encodeParam($value);
			}
		}
        if( is_string($extra) )
	    {
	        $url .= $extra;
	    }
	    return url::clean(rtrim($url,'/'));
    }

    public static function redirect($url,$time=0)
    {
        $url = url::build($url);
        header("Location: ".$url);
        exit();
    }

	/**
	 * 获取当前页面的URl
	 *
	 * @param	bool $complete	是否返回完整的地址
	 * @return	string	页面地址
	 */
	public static function current($complete=TRUE)
	{
		$current = $_SERVER['REQUEST_URI'];
		if($complete)
		{
		   $current = url::protocol().'://'.url::domain().$current;
		}
	    return $current;
	}

	/**
	 * 返回 URL 中的基础部分， 如：/zotop/admin/index.php
	 *
	 * @return string
	 */
	public static function base()
	{
	    $scriptname = url::scriptname();
		return $scriptname;
	}

	/**
	 * 返回url中的页面名称， 如：index.php
	 *
	 * @return string
	 */
	public static function basename()
	{
	    $scriptname=url::scriptname();
        $pathinfo = pathinfo($scriptname);
        return $pathinfo['basename'];
	}
	
	/**
	 * 返回url中的页面名称， 如：/zotop/admin
	 *
	 * @return string
	 */
	public static function dirname()
	{
	    $scriptname=url::scriptname();
        $pathinfo = pathinfo($scriptname);
        return $pathinfo['dirname'];
	}	

	/**
	 * 如果网站安装在目录或者目录（如：/test/cms）下面，则返回：'/test/cms',如果安装在根目录下则返回 '/'
	 * 在不同的应用中必须重写该函数
	 *
	 *
	 * @param $domain 是否包含网站域名
	 * @return string
	 */
	public static function root()
	{
		return zotop::config('zotop.install.root');
	}
	
    /**
     * 获取当前应用的url
     *
     * @return string 如：<install>/system/admin
     */
    public static function application($id = '')
    {
        if( empty($id) )
        {
            return dirname(url::scriptname());
        }
        $url = zotop::application($id , 'url');
        if( $url[0] !=='/' && strpos($url,'://')===false )
        {
            $url =  url::root().'/'.$url;
        }
        return $url;
    }
    
    public static function module($id='')
    {
        if( empty($id) )
        {
            $id = application::getModule();            
        }
        return zotop::module($id,'url');
    }

    /**
     * 获取当前应用的主题信息
     *
     * @return string 如：<install>/system/admin/theme
     */
	public static function theme()
	{
	    return url::application().'/themes';
	}

    /**
     * 获取当前应用的公共目录
     *
     * @return string 如：<install>/system/admin/common
     */
	public static function common()
	{
	    return url::application().'/common';
	}
	
    /**
     * 获取系统的url地址
     *
     * @return string 如：<install>/zotop
     */
	public static function zotop()
	{
	    return url::root().'/zotop';
	}

	/**
	 * 返回当前的使用的协议，一般为 http 或者 https
	 *
	 * @return https|http
	 */
	public static function protocol()
	{
		return (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS']==='off') ? 'http':'https';
	}

	/**
	 * 获取当前的主域名(含端口),返回如：www.zotop.com
	 *
	 * @param boolean $complete //是否返回全部参数
	 * @param boolean $protocol //
	 * @return string
	 */
	public static function domain()
	{
		return $_SERVER['HTTP_HOST'];
	}

	/**
	 * 返回当前的pathinfo 如：REQUEST_URI='/hc/admin/index.php/cms/index/index/1'，返回：/cms/index/index/1
	 *
	 * @return string or bool
	 */
	public static function pathinfo()
	{
		if( isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO'] )
		{
			return $_SERVER['PATH_INFO'];
		}
		return false;
	}


	/**
	 * 返回当前脚本名称 /system/admin/index.php
	 *
	 * @return $string
	 */
	public static function scriptname()
	{
	    return $_SERVER['SCRIPT_NAME'];
	}

	public static function clean($url)
	{
        //替换掉多余的 / 符号，并且保护如：http:// 中的双斜杠，下面代码可能有错误
	    $url = str_replace('//','/',$url);
	    $url = str_replace('//','/',$url);
        $url = str_replace(':/','://',$url);
	    return $url;
	}

	//站内相对连接转化成绝对链接
	public static function abs($href)
	{
	    if( strpos($href,'://') === false )
	    {
	        //如果格式如 /system/admin/themes/default/css/system.css
	        if($href[0] === '/')
	        {
	            $root = url::root();
	            $href = $root.'/'.ltrim($href , $root);
	        }
	        else
	        {
	            $href = url::dirname().'/'.$href;
	        }
	    }
	    return url::clean($href);
	}

	public static function encode($url)
	{

	}

	public static function decode($url)
	{
		$url = str_replace(
			array('%root%','%zotop%','%theme%','%module%'),
			array(
				url::root(),
				url::zotop(),
				url::theme(),
				url::module(),
			),
			$url
		);
		return $url;
	}
	
	public static function encodeParam($param)
	{
	    $param = str_replace(array(DS),array('∕'),$param);
	    return $param;
	}
	
	public static function decodeParam($param)
	{
	    $param = str_replace(array('∕'),array(DS),$param);
	    return $param;
	}
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 产生随机字符
 * Thanks for Thinkphp！
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_rand
{
    public static  function string($len,$add='')
    {
		// 默认去掉了容易混淆的字符oOLl和数字01
		$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$add;
		if( $len > 10 ){
			$chars= str_repeat($chars,5);
		}
        $chars   =   str_shuffle($chars);
		//返回指定长度的字字符
		return substr($chars,0,$len);
    }

    public static function number($len=4)
    {
		$chars= str_repeat('0123456789',5);
		if( $len > 10 ){
			$chars= str_repeat($chars,$len);
		}
		//返回指定长度的数字
		return substr($chars,0,$len);
    }

	public static function letter($len=4,$type='',$add='')
	{
		switch(strtoupper($type))
		{
			case 'UPPER':
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$add;
				break;
			case 'LOWER':
				$chars='abcdefghijklmnopqrstuvwxyz'.$add;
				break;
			default:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$add;
				break;
		}
		if( $len > 10 ){
			$chars= str_repeat($chars,5);
		}
        $chars   =   str_shuffle($chars);
		//返回指定长度的字母
		return substr($chars,0,$len);
	}

	public static function guid($mix='',$len=32)
	{
        if(is_object($mix) && function_exists('spl_object_hash'))
        {
            return spl_object_hash($mix);
        }
        elseif(is_resource($mix))
        {
            $mix = get_resource_type($mix).strval($mix);
        }else
        {
            $mix = serialize($mix);
        }
        return md5($mix);
	}

	public static function chinese($len=4,$add='')
	{
		$chars = "的一是在不了有和人这中大为上个国我以要他时来用们生到作地于出就分对成会可主发年动同工也能下过子说产种面而方后多定行学法所民得经十三之进着等部度家电力里如水化高自二理起小物现实加量都两体制机当使点从业本去把性好应开它合还因由其些然前外天政四日那社义事平形相全表间样与关各重新线内数正心反你明看原又么利比或但质气第向道命此变条只没结解问意建月公无系军很情者最立代想已通并提直题党程展五果料象员革位入常文总次品式活设及管特件长求老头基资边流路级少图山统接知较将组见计别她手角期根论运农指几九区强放决西被干做必战先回则任取据处队南给色光门即保治北造百规热领七海口东导器压志世金增争济阶油思术极交受联什认六共权收证改清己美再采转更单风切打白教速花带安场身车例真务具万每目至达走积示议声报斗完类八离华名确才科张信马节话米整空元况今集温传土许步群广石记需段研界拉林律叫且究观越织装影算低持音众书布复容儿须际商非验连断深难近矿千周委素技备半办青省列习响约支般史感劳便团往酸历市克何除消构府称太准精值号率族维划选标写存候毛亲快效斯院查江型眼王按格养易置派层片始却专状育厂京识适属圆包火住调满县局照参红细引听该铁价严".$add;

        for($i=0;$i<$len;$i++){
          $str.= string::substr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }

		return $str;

	}


}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_format
{
	public static function byte($size,$len=2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$pos=0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return number_format($size,$len).' '.$units[$pos];
	}
	
	/**
	 * 表情转换，可以通过hook(zotop.smiles)扩展
	 * 
	 *
	 */
	public static function smiles($str)
	{
	    $smiles = array(
	        ':)'=>url::theme().'/image/smiles/smile.gif',
	    	':-)'=>url::theme().'/image/smiles/cool.gif',
	    );
	    
	    $smiles = zotop::filter('zotop.smiles',$smiles);
	    
	    foreach($smiles as $key=>$val)
	    {
	        $str = str_replace($key,'<img src='.$val.' class="zotop-smile"/>',$str);
	    }
	    
	    return $str;
	}
	
	public static function email()
	{
	    
	}
	
	public static function link()
	{
	    
	}


}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * REQUEST
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_request
{
	public static function get($name='', $valid = NULL, $default = NULL)
	{
		if(empty($name))
		{
			return $_GET;
		}
		$get = $_GET[$name];
		return trim($get);
	}

	public static function post($name ='', $valid = NULL, $default = NULL)
	{
		if(empty($name))
		{
			return $_POST;
		}
		$post = $_POST[$name];
		$post = is_string($post) ? trim($post) : $post;
		return $post;
	}

	public static function referer()
	{
		return $_SERVER['HTTP_REFERER'];
	}



}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * vilidation
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_valid
{
    public static function isNum($val)
    {
		return is_numeric($val);
	}

	public static function isInt($val)
	{
		return is_int($val);
	}

	public static function regex($val,$regex)
	{

	}
	/**
	 * 检查字符串是否是UTF8编码
	 * @param string $string 字符串
	 * @return Boolean
	 */
	function isUtf8($string)
	{
		return preg_match('%^(?:
			 [\x09\x0A\x0D\x20-\x7E]            # ASCII
		   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	   )*$%xs', $string);
	}

	public static function test($val,$valid)
	{
		//用于自定义的测试 $valid = {required:true,maxlength:5}
	}
}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_html
{
	//encode的别名
	public static function specialchars($str)
	{
		return self::encode($str);
	}
	//编码字符串
	public static function encode($str)
	{
	    if(is_array($str))
	    {
	        return ;
	    }
	    if(function_exists('htmlspecialchars'))
		{
			return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
		}
		$str = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $str);
		$str = str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#39;', '&quot;'), $str);
		return $str;
	}
	//解码字符串
	public static function decode($str)
	{
		return htmlspecialchars_decode($str, ENT_QUOTES, 'UTF-8');
	}
	//创建标签
	public static function attributes($attrs,$value=NULL)
	{
		if(empty($attrs))
		{
			return '';
		}
		if(is_string($attrs))
		{
			if(!isset($value))
			{
				return ' '.$attrs;
			}
			if(empty($value))
			{
			    return '';
			}
			return ' '.$attrs.'="'.html::encode($value).'"';
		}
		$str = '';
		if(is_array($attrs))
		{
			foreach($attrs as $key=>$val)
			{
			    if(!empty($val))
			    {
				    $str .= ' '.$key.'="'.html::encode($val).'"';
			    }
			}
		}
		return $str;
	}
	/**
	 * 创建一个超链接传入uri或者完整的url
	 *
	 * @param string URL或者URI字符串
	 * @param string 链接显示文字
	 * @param array 链接的其它属性
	 * @param string 协议，如：http,https,ftp
	 * @return string 链接字符串
	 *
	 */
	public static function a($url,$title=NULL,$attrs=NULL,$protocol=NULL)
	{
		//如果不是完整的链接，如：http://www.zotop.com 或者 https://www.zotop.com ,或者 锚点#framename 等，则将相对连接处理成绝对链接
	    if( strpos($url, '://') === false or strpos($url,'#') !== 0 )
		{
		    $url = url::base() .'/'. $url;
		}
		$title = isset($title) ? $title : $url;
	    return '<a href="'.$url.'" '.html::attributes($attrs).'>'.$title.'</a>';
	}

	public static function b($text,$attrs)
	{
		return '<b'.html::attributes($attrs).'>'.$text.'</b>';
	}

	/**
	 * 创建一个超链接传入uri或者完整的url
	 *
	 * @param string URL或者URI字符串
	 * @param array 图片的其它属性
	 * @return string 链接字符串
	 *
	 */
	public static function image($url,$attrs=array())
	{
		//如果不是完整的链接，如：http://www.zotop.com/a/b/1.gif ，则将相对连接处理成绝对链接
	    if( strpos($url, '://') === false && $url[0]!='/' )
		{
		    $url = url::root() .'/'. $url;
		}
		
		//解析url中的特殊字符串
		$url = url::decode($url);
		
	    return '<img src="'.$url.'" '.html::attributes($attrs).'/>';
	}

	public static function flash($url,$width,$height,$attrs=array())
	{

	}

	public static function link($href,$attrs=array())
	{
		$links = array();

	    $str='';
		if(is_array($href))
		{
			foreach($href as $h)
			{
				$str .= html::link($h,$attrs);
			}
		}
		else
		{
            //使用绝对路径
		    $href = url::abs($href);
		    //一个页面只允许加载一次
		    //if( isset($links[strtolower($href)]) )
		    //{
		    //   return '';
		    //}
		    $links[strtolower($href)] = true;

		    $attrs['href'] = $href;
			$str = '<link'.html::attributes($attrs).' />';
		}
		return $str;
	}


	public static function stylesheet($href,$attrs='')
	{
	    $attrs['rel']= 'stylesheet';
	    $attrs['type']= 'text/css';
	    return html::link($href,$attrs);
	}

	public static function style($style)
	{
	    if($style)
	    {
	        return '<style type="text/css">'.$style.'</style>';
	    }
	    return '';
	}


	public static function script($href,$attrs=array())
	{
		static $scripts = array();

	    $str = '';
		if(is_array($href))
		{
			foreach($href as $src)
			{
				$str .= html::script($src,$attrs);
			}
		}
		else
		{
		    //如果不是是直接输出的话
		    if(strpos($href , ';')==0)
		    {
				$href = url::abs($href);
			    //一个页面只允许加载一次
			    if( isset($scripts[strtolower($href)]) )
			    {
			       return '';
			    }
			    $scripts[strtolower($href)] = true;

			    $attrs['type'] = 'text/javascript';
			    $attrs['src'] = $href;
			    $str = '<script'.html::attributes($attrs).'></script>';
		    }
		    else
		    {
		        $attrs['type'] = 'text/javascript';
		        $str = '<script'.html::attributes($attrs).'>'.$href.'</script>';
		    }

		}
		return $str;
	}

	public static function meta($tag,$value='')
	{
		$str= '';
		if(is_array($tag))
		{
			foreach($tag as $attr=>$value)
			{
				$str .= html::meta($attr,$value)."\n";
			}
		}
		else
		{
			$attr = in_array(strtolower($tag), array('content-type','content-language',strtolower('X-UA-Compatible'))) ? 'http-equiv' : 'name';
			$str = '<meta '.$attr.'="'.$tag.'" content="'.$value.'" />';
		}
		return $str;
	}
	//input标签
	public static function label($text,$for='',$attrs=array())
	{
		$attrs['for']=$for;
		return '<label'.html::attributes($attrs).'>'.$text.'</label>';
	}

	public static function input(array $attrs)
	{
		$attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'text';

		if( isset($attrs['name']) )
		{
		    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
		}
		$attrs['value'] = isset($attrs['value']) ? html::encode($attrs['value']) : '';

		return '<input'.html::attributes($attrs).'/>';
	}

    public static function iframe($name,$src,$extra=array(),$noframe='')
    {
        $attrs = array(
            'id'=>$name,
        	'name'=>$name,
            'src'=>$src,
        );
        $attrs += (array)$extra;
        return '<iframe'.html::attributes($attrs).'>'.$noframe.'</iframe>';
    }

	public static function media()
	{

	}

	public static function checkbox($attrs, $value='', $checked=false , $extra=array())
	{
		if(!is_array($attrs))
		{
			$attrs = array(
        		'name'=>$attrs,
				'value'=>$value,
			);
		}

		$attrs['type'] = 'checkbox';

		if ($checked == TRUE OR (isset($attrs['checked']) AND $attrs['checked'] == TRUE))
		{
			$attrs['checked'] = 'checked';
		}
		else
		{
			unset($attrs['checked']);
		}
		return html::input($attrs);
	}

	public static function radio($attrs, $value='', $checked=false , $extra=array())
	{
		if(!is_array($attrs))
		{
			$attrs = array(
        		'name'=>$attrs,
				'value'=>$value,
			);
		}

		$attrs['type'] = 'radio';

		if ($checked == TRUE OR (isset($attrs['checked']) AND $attrs['checked'] == TRUE))
		{
			$attrs['checked'] = 'checked';
		}
		else
		{
			unset($attrs['checked']);
		}
		return html::input($attrs);
	}


	public static function nbs($num = 1)
	{
		return str_repeat("&nbsp;", $num);
	}

	public static function h($text = '', $h = '1')
	{
		return "<h".$h.">".$text."</h".$h.">";
	}

	public static function br($num = 1)
	{
		return str_repeat("<br />", $num);
	}

}


defined('ZOTOP') OR die('No direct access allowed.');
/**
 * UBB转换
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_ubb
{
	public static function decode($str) {
	  $str =trim($Text);
	  $str =preg_replace("/\\t/is","  ",$str);
	  $str =preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$str);
	  $str =preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$str);
	  $str =preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$str);
	  $str =preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$str);
	  $str =preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$str);
	  $str =preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$str);
	  $str =preg_replace("/\[separator\]/is","",$str);
	  $str =preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$str);
	  $str =preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$str);
	  $str =preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$str);
	  $str =preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\1</a>",$str);
	  $str =preg_replace("/\[url\]([^\[]*)\[\/url\]/is","<a href=\"\\1\" target=_blank>\\1</a>",$str);
	  $str =preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$str);
	  $str =preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$str);
	  $str =preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$str);
	  $str =preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$str);
	  $str =preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$str);
	  $str =preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$str);
	  $str =preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$str);
	  $str =preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis","color_txt('\\1')",$str);
	  $str =preg_replace("/\[emot\](.+?)\[\/emot\]/eis","emot('\\1')",$str);
	  $str =preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$str);
	  $str =preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$str);
	  $str =preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$str);
	  $str =preg_replace("/\[quote\](.+?)\[\/quote\]/is"," <div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $str);
	  $str =preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $str);
	  $str =preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $str);
	  $str =preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div class='sign'>\\1</div>", $str);
	  $str =preg_replace("/\\n/is","<br/>",$str);
	  return $str;
	}

	public static function encode($str)
	{

	}


}


class zotop_ip
{
    /**
     * 获取当前的ip地址
     * 
     *
     */
    public static function current()
    {
        return '127.0.0.2';
    }
    
    /**
     * 获取当前或者特定ip地址的国家，城市参数，需要使用纯真数据库，方法来自discuz
     * 
     *
     */
    public static function location($ip='')
    {
        
    }
}


class zotop_time
{
    /**
     * 返回当前时间或者当前时间戳
     * 
     * @param bool $format 是否格式化时间
     *
     * @return string
     */
    public static function current($format=false)
    {
        static $time ='';
        if( empty($time) )
        {
            $time = time();
        }
        $time = $format ? time::format($time) : $time;
        return $time;
    }
    
    /**
     * 等同于 time::current() 函数
     *
     */
    public static function now($format=false)
    {
        return time::current($format);
    }
    
    /**
     * 对时间进行格式化，支持多种格式化方式
     * 
     * @param string 待格式化的时间戳或者时间标准串
     * @param string 时间格式
     * 
     * @return string 格式化后的时间
     */
    public static function format($time,$format='')
    {
        $format = empty($format) ? '{YYYY}-{MM}-{DD} [HH]:[MM]:[SS]' : strtoupper($format);
        $formatTime = strtr($format,array(
            '{YYYY}' => date('Y',$time),//2009
            '{YY}' => date('y',$time),//09
        	'{MM}' => date('m',$time),//01
            '{M}' => date('n',$time),//1
            '{DD}' => date('d',$time),//03
            '{D}' => date('j',$time),//3
            '[HH]' => date('H',$time),//12
            '[H]' => date('G',$time),//5
            '[MM]' => date('i',$time),//00
            '[M]' => date('i',$time),//00
            '[SS]' => date('s',$time),//00
            '[S]' => date('s',$time), //00               
        ));
        return $formatTime;
    }
}



abstract class zotop_database
{
    protected $config = array(); //数据库配置
    protected $connect = null; //当期数据库链接
    protected $sql = array(); //查询语句容器
    protected $sqlBuilder = array(); //查询语句构建容器
    protected $query = null; //查询对象
    protected $numRows = 0; //影响的数据条数
    protected $selectSql	= 'SELECT%DISTINCT% %FIELDS% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT%';
    
     /**
     * 生成数据库唯一实例
     *
     * @param $config
     * @return object
     */   
    public function &instance($config = array())
    {
        static $instances = array();
        
        //实例唯一的编号        
        $id = serialize($config);
        if( !isset($instances[$id]) )
        {
            
            if( is_string($config) )
            {
                 $config = $this->parseDNS($config);
            }
            if( empty($config['driver']) )
            {
               zotop::error(-1,zotop::t('错误的数据库配置文件',$config));
            }
            //数据库驱动程序
            $driver = 'database_'.strtolower($config['driver']);
            
            //加载驱动程序
            if( !zotop::autoload($driver) )
            {
              zotop::error(-1,zotop::t('未能找到数据库驱动 "{$driver}"',$config));
            }
            
            //取得驱动实例
            $instance	= new $driver($config);
            
            $instances[$id] = &$instance;            
        }
        
        return $instances[$id];
    }
    
    public function connect()
    {
        die('ERROR : function (connect) need to be rewrited');
    }
    
    public function query()
    {
        die('ERROR : function (query) need to be rewrited');
    }
    
    public function update($table='',$data='',$where='')
    {
        $sqlBuilder = (array)$this->sqlBuilder;          
        
        if( is_array($where) )
        {
            $sqlBuilder['where'] = array_merge($sqlBuilder['where'],$where);
        }
        if( is_array($data) )
        {
             $sqlBuilder['set'] = array_merge($sqlBuilder['set'],$data);
        }
        if( !empty($table) && is_string($table))
        {
            $sqlBuilder['from'] = $table;
        }
                
        if( empty($sqlBuilder['where']) )
        {
            return false;
        }
               
        $this->sqlBuilder = array();    
            
        $sql = 'UPDATE %TABLE%%SET%%WHERE%';
        $sql = str_replace(
            array('%TABLE%','%SET%','%WHERE%'),
            array(
				$this->parseFrom($sqlBuilder['from']),
				$this->parseSet($sqlBuilder['set']),
				$this->parseWhere($sqlBuilder['where']),
            ),
            $sql
        );
        
        return $this->execute($sql);
    }

    
	public function select($fields = '*')
	{
	    if( func_num_args()>1 )
		{
			$fields = func_get_args(); //select('id','username','password')
		}
		elseif( is_string($fields) )
		{
			$fields = explode(',', $fields); //select('id,username,password')
		}else
		{
			$fields = (array)$fields; //select(array('id','title'))
		}

		$this->sqlBuilder['select'] = $fields;

		return $this;
	}
	 
	public function from($tables)
	{
		if(  func_num_args()>1 )
		{
			$tables = func_get_args(); //from('user','config')
		}
		elseif( is_string($tables) )
		{
			$tables = explode(',', $tables); //from('user,config')
		}else
		{
			$tables = (array)$tables; //from(array('user','config'))
		}
		$this->sqlBuilder['from'] = $tables;
		return $this;
	}
	
	/* 设置where
	 * where('id',1)，where('id','<',1)，where(array('id','<',1),'and',array('id','>',1))，where(array('id','like',1),'and',array(array('id','>',1),'or',array('id','>',1)))
	 *
	 */

	public function where($key,$value=null)
	{
		if( !is_array($key) )
		{
			$where = func_get_args();
			switch( count($where) )
			{
				case 2:
					$where = array($where[0],'=',$where[1]); //where('id',1)  => array('id','=',1)
					break;
				case 3:
					$where = array($where[0],$where[1],$where[2]); //where('id','<',1)  => array('id','<',1)
					break;
			}
		}
		else
		{
			if( func_num_args()==1 )
			{
				$where = $key;
			}
			else
			{
				$where = func_get_args(); //where(array('id','=','1'),'and',array('status','>','0'))
			}

		}
		
		if( !empty($where) )
		{
    		if( count($this->sqlBuilder['where']) >0 )
    		{
    			$this->sqlBuilder['where'][] = 'AND';
    		}
    		$this->sqlBuilder['where'][] = $where;
		}
		return $this;
	}
	
	public function limit($limit, $offset = 0)
	{
		$this->sqlBuilder['limit']  = (int) $limit;

		if( $offset !== NULL || !is_int($this->sqlBuilder['offset']) )
		{
			$this->sqlBuilder['offset'] = (int) $offset;
		}

		return $this;
	}

	public function offset($value)
	{
		$this->sqlBuilder['offset'] = (int) $value;

		return $this;
	}

	public function orderby($orderby, $direction=null)
	{
		if( !is_array($orderby) )
		{
			$orderby = array($orderby => $direction);

		}
		$this->sqlBuilder['orderby'] = array_merge((array)$this->sqlBuilder['orderby'], $orderby);
		return $this;
	}
	
	public function set($name, $value='')
	{
	    if( is_string($name) )
	    {
	        $data = array($name=>$value);
	    }
	    $data = (array)$name;
		$this->sqlBuilder['set'] = array_merge((array)$this->sqlBuilder['set'], $data);
		return $this;	    
	}
	
    public function compileSelect($sql)
    {
        $str = '';
        if( empty($sql) )
		{
			$sql = $this->sqlBuilder;
		}
		elseif( is_array($sql) )
		{
			$sql = array_merge($this->sqlBuilder , $sql);
		}
		else
		{
			$str = $sql;
		}
		
        $this->sqlBuilder = array();
        
		if( is_array($sql) )
        {
 			$str = str_replace(
				array('%TABLE%','%DISTINCT%','%FIELDS%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%'),
				array(
					$this->parseFrom($sql['from']),
					$this->parseDistinct($sql['distinct']),
					$this->parseSelect($sql['select']),
					$this->parseJoin($sql['join']),
					$this->parseWhere($sql['where']),
					$this->parseGroupBy($sql['groupby']),
					$this->parseHaving($sql['having']),
					$this->parseOrderBy($sql['orderby']),
					$this->parseLimit($sql['limit'],$sql['offset']),
				),
				$this->selectSql
			);           
        }
        return $str;        
    }	
	   
    /**
    * 将数据库连接的DNS字符串转化为数组
    *
    * @param string $str 数据库连接DNS，如：mysql://root:123456@localhost:80/test
    * @return array|bool 数据库连接信息
    */
    public function parseDNS($str)
    {
       $str = str_replace('@/', '@localhost/', trim($str));

       $info = parse_url($str);

       if( !empty($info['scheme']) )
       {
           $dns = array();
           $dns['driver'] = $info['scheme'];
           $dns['username'] = isset($info['user']) ? $info['user'] : '';
           $dns['password'] = isset($info['pass']) ? $info['pass'] : '';
           $dns['hostname'] = isset($info['host']) ? $info['host'] : '';
           $dns['hostport'] = isset($info['port']) ? $info['port'] : '';
           $dns['database'] = isset($info['path']) ? $info['path'] : '';

           return $dns;
       }
       return false;
    }
    
    public function parseSQL($sql)
    {
        $this->sql[] = $sql;
        return $sql;
    }
    
    /**
    * from 分析
    * @access protected
    * @param mixed $distinct
    * @return string
    */   
    protected function parseFrom($tables)
    {
        $array = array();
        if(is_string($tables))
        {
            $tables = explode(',',$tables);
        }
        foreach($tables as $key=>$table)
        {
			$array[] = $this->escapeTable($table);
        }
        return implode(',',$array);
    }
    
    /**
     * distinct分析
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct) {
        return empty($distinct) ? '' : ' DISTINCT ';
    }
    
    /**
     * select 分析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseSelect($fields)
    {
          if( !empty($fields) )
          {
              if(is_string($fields))
              {
                  $fields = explode('.',$fields);
              }
			  $array = array();
			  foreach($fields as $key=>$filed)
			  {
				$array[] = $this->escapeColumn($filed);
			  }
			  return implode(',',$array);
          }
          return '*';
    }
    
    /**
     * join 分析
     * @access protected
     * @param mixed $join
     * @return string
     */
    protected function parseJoin($join) {
        return '';
    }
        
    /**
     * where 分析
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseWhere($where)
    {
        $str = '';
        if( is_array($where) )
        {
            $str = $this->parseWhereArray($where);
        }
        else
        {
            $str = $where;
        }
        return empty($str) ? '' : ' WHERE '.$str;
    }

    protected function parseWhereArray($where)
    {
        if( !empty($where) )
        {
            if( is_string($where[0]) && count($where)==3 )
            {
				$operator = trim(strtoupper($where[1]));
				switch($operator)
				{
					case '=':
					case '!=':
					case '>':
					case '<':
					case '>=':
					case '<=':
						return $this->escapeColumn($where[0]).' '.$where[1].' '.$this->escapeValue($where[2]);
						break;
					case 'IS':
					case 'IS NOT':
					case 'IN':
					case 'NOT IN':
					case 'BETWEEN':
						return $this->escapeColumn($where[0]).' '.$where[1].' '.$this->escapeValue($where[2]);
						break;
					case 'LIKE':
					case '%LIKE%':
						return $this->escapeColumn($where[0]).' '.trim($where[1],'%').' '.$this->escapeValue('%'.trim($where[2],'%').'%');
						break;
					case 'LIKE%':
						return $this->escapeColumn($where[0]).' '.trim($where[1],'%').' '.$this->escapeValue(''.trim($where[2],'%').'%');
						break;
					case '%LIKE':
						return $this->escapeColumn($where[0]).' '.trim($where[1],'%').' '.$this->escapeValue('%'.trim($where[2],'%').'');
						break;
					default :
						//die("错误的SQL参数");
						return '';
				}
            }
			$str = '';
			for( $i=0,$j=count($where); $i<$j ; $i++ )
			{
				if( is_array($where[$i][0]) )
				{
					$str .= '('.$this->parseWhereArray($where[$i]).')';
				}
				elseif( is_array($where[$i]) )
				{
					$str .= $this->parseWhereArray($where[$i]);
				}
				elseif( is_string($where[$i]) )
				{
					$str .= ' '.strtoupper(trim($where[$i])).' ';
				}

			}
        }
        return $str;        
    }
    
    protected function parseGroupBy($group)
    {
        return empty($group) ? '' : ' GROUP BY '.$group;
    }
    
    protected function parseHaving($having)
    {
        return empty($having)? '' : ' HAVING '.$having;
    }
    
	public function parseOrderby($orderby)
	{
		$str = '';
		if( is_array($orderby) )
		{
			foreach( $orderby as $key=>$direction )
			{
				$direction = strtoupper(trim($direction));
				if ( !in_array($direction, array('ASC', 'DESC', 'RAND()', 'RANDOM()', 'NULL')) )
				{
					$direction = 'ASC';
				}
				$str .= ','.$this->escapeColumn($key).' '.$direction;
			}
		}
		$str = trim($str,',');
		return empty($str) ? '' : ' ORDER BY '.$str;
	}    
 
    protected function parseLimit($limit, $offset=0)
	{
		$str = '';

		if( is_int($offset) )
		{
			$str .= (int)$offset.',';
		}
		if( is_int($limit) )
		{
		    $str .= $limit;
		}
		return empty($str) ? '' : ' LIMIT '.$str;
    }
    
    protected function parseSet($data)
    {
        $str = '';
        foreach($data as $key=>$val)
        {
                
            //解析值中的如：num = num + 1 或者 num = num - 1          
            if( preg_match('/^([a-zA-Z0-9_]+){1,32}(\s*)(\+|\-|\*|\%)(\s*)([0-9]){1,6}$/i',$val,$matches) )
            {
               $str .= ','.$this->escapeColumn($key).'='.$this->escapeColumn($matches[1]).$matches[3].(int)$matches[5]; 
            }
            else
            {
               $str .= ','.$this->escapeColumn($key).'='.$this->escapeValue($val);
            }
        }
        $str = trim($str,',');
        
        return empty($str) ? '' : ' SET '.$str;
    }
    /**
     * 对字符串进行安全处理
     *
     * @return string
     */
    public function escape($str)
    {
        
        return addslashes($str);
    }

	/**
	 * 对输入的值进行处理
	 *
	 * @param   mixed   value to escape
	 * @return  string
	 */
	public function escapeValue($value)
	{
	    switch (gettype($value))
		{
			case 'string':
				$value = '\''.$this->escape($value).'\'';
			    break;
			case 'boolean':
				$value = (int) $value;
			    break;
			case 'double':
				$value = sprintf('%F', $value);
			    break;
			default:
				$value = ($value === NULL) ? 'NULL' : $value;
			    break;
		}

		return (string) $value;
	}
	
    /**
     * 对字段名称进行安全处理
     *
     * @return string
     */
    public function escapeColumn($str)
    {
        return $str;
    }
    
    /**
     * 对表名称进行安全处理
     *
     * @return string
     */
    public function escapeTable($str)
    {
        return $str;
    }    
    
    /**
    * 返回查询语句的数组
    *
    * @return array
    */   
    public function sql()
    {
        return $this->sql;
    }
    
     /**
    * 返回最后一条查询语句
    *
    * @return array
    */      
    public function lastSql()
    {
        if( is_array($this->sql) )
        {
            return end($this->sql);
        }
        return '';
    }
    
    /**
    * sql语句构建
    *
    * @return array
    */      
    public function sqlBuilder($key='', $value=null)
    {
        if( empty($key) )
        {        
            return $this->sqlBuilder;
        }
        if( is_array($key) )
        {
            $this->sqlBuilder = array_merge($this->sqlBuilder , $key);
            return $this->sqlBuilder;       
        }
        if( isset($value) )
        {
            $this->sqlBuilder[$key] = $value;
            return $this->sqlBuilder;
        }
        $sqlBuilder = $this->sqlBuilder[$key];
        if( isset($sqlBuilder) )
        {
            return $sqlBuilder;
        }
        return false;        
    }
    
    /**
    * CONFIG 设置
    *
    * @return array
    */      
    public function config($key='' , $value=null)
    {
        if( empty($key) )
        {
            return $this->config;
        }
        if( is_array($key) )
        {
            $this->config = array_merge($this->config , $key);
            return $this->config;
        }
        if( isset($value) )
        {
            $this->config[$key] = $value;
            return $this->config;
        }
        $config = $this->config[$key];
        if( isset($config) )
        {
            return $config;
        }
        return false;
    }    

	public function size()
	{
		return 'Unknow!';
	}
	    
	public function version()
	{
		return 'Unknow!';
	}
	       
}



class zotop_database_mysql extends zotop_database
{
    public function __construct($config = array())
    {
         $default = array(
			 'driver'=>'mysql',
			 'username'=>'root',
			 'password'=>'',
			 'hostname'=>'localhost',
			 'hostport'=>'3306',
			 'database'=>'zotop',
			 'charset'=>'utf8',
			 'pconnect'=>false,
			 'autocreate'=>false
		 );
		 $this->config = array_merge( $default , $this->config , $config);
    }
    
	public function __destruct()
	{
		is_resource($this->connect) and mysql_close($this->connect);
	}

	public function connect($test = false)
	{
	    if( is_resource($this->connect) )
	    {
	        return $this->connect;
	    }
	    
	    $connect = ( $this->config('pconnect') == TRUE ) ? 'mysql_pconnect' : 'mysql_connect';
	    
        $host = $this->config('hostname');
        $port = $this->config('hostport');
        $host = empty($port) ? $host : $host.':'.$port;
        $user = $this->config('username');
        $pass = $this->config('password');
        

        if( $this->connect = @$connect($host, $user, $pass, true) )
        {
            $database = $this->config('database');
            
            if( @mysql_select_db($database, $this->connect) )
            {
                $version = $this->version();
                
				if($version > '4.1' && $charset = $this->config('charset') )
    			{
					@mysql_query("SET NAMES '".$charset."'" , $this->connect);//使用UTF8存取数据库 需要mysql 4.1.0以上支持
    			}
				if($version > '5.0.1'){
					@mysql_query("SET sql_mode=''",$this->connect);//设置 sql_model
				}
                return true;
            }
            if( $test ) return false;//测试连接是否有效
            zotop::error(-2,zotop::t('Cannot use database `{$database}`',$this->config()));
        }
        zotop::error(-1,'Cannot connect to database server'.mysql_error());
	}

	/**
	 * 执行一个sql语句 query，相当于包装后的mysql_query
	 *
	 * @param $sql
	 * @param $silent
	 * @return unknown_type
	 */
	public function query($sql, $silent=false)
    {
        if( !is_resource($this->connect) )
        {
			$this->connect();
        }
		if( $sql = $this->parseSql($sql) )
		{
			//echo $this->sql;
			if ( $this->query )
			{
			    $this->free(); //释放前次的查询结果
			}
			
			$this->query = @mysql_query($sql , $this->connect );//查询数据
			
			if( $this->query === false )
			{
				if( $silent ) return false;
				
				zotop::error( -3 , '查询语句错误' ,zotop::t('<h2>SQL: {$sql}</h2>{$error}',array('sql'=>$sql,'error'=>@mysql_error())));
			}
			
			$this->numRows = mysql_num_rows($this->query);
			
			return $this->query;
		}
		return false;
    }
    	
	public function free()
	{
		@mysql_free_result($this->query);
        $this->query = 0;
	}

	/**
	 * 执行一个sql语句，并返回影响的数据行数
	 *
	 * @param $sql
	 * @param $silent
	 * @return bool||number
	 */
	public function execute($sql,$silent=false)
	{
		if($result = $this->query($sql,$silent))
		{
			if($result === false)
			{
				return false;
			}
			
			$this->numRows = mysql_affected_rows($this->connect);
						
			return $this->numRows;
		}
		return false;
	}

	/**
	 * 执行一个sql语句并返回结果数组
	 *
	 * @param $sql
	 * @return array
	 */
	public function getAll($sql)
	{
		$sql = $this->compileSelect($sql);

		if($query = $this->query($sql))
		{
			$result = array();
			if($this->numRows >0) {
				while($row = mysql_fetch_assoc($query)){
					$result[]   =   $row;
				}
				mysql_data_seek($this->query,0);
			}
			return $result;
		}
		return false;
	}
	
	/**
	 * 从查询句柄提取一条记录
	 *
	 * @param $sql
	 * @return array
	 */
	public function getRow($sql)
	{
		$sql = $this->compileSelect($sql);
		if($query = $this->query($sql))
		{
			$row = mysql_fetch_assoc($query);
			if($row) {
                return $row;
			}
			return null;
		}
		return false;
	}	
	
	
	public function escape($str)
	{
		is_resource($this->connect) or $this->connect();
		return mysql_real_escape_string($str, $this->connect);
	}

	public function escapeColumn($field)
	{
		if( $field=='*' )
		{
			return $field;
		}

		if( strpos($field,'.') !==false )
		{
			$field = $this->config('prefix').$field;

			$field = str_replace('.', '`.`', $field);
		}
		if( stripos($field,' as ') !==false )
		{
			$field = str_replace(' as ', '` AS `', $field);
		}

		return '`'.$field.'`';
	}

	public function escapeTable($table)
	{
		$table = $this->config('prefix').$table;
		if (stripos($table, ' AS ') !== FALSE)
		{
			$table = str_ireplace(' as ', ' AS ', $table);
			$table = array_map(array($this, __FUNCTION__), explode(' AS ', $table));
			return implode(' AS ', $table);
		}
		return '`'.str_replace('.', '`.`', $table).'`';
	}
		
	public function version()
	{
	    if( !is_resource($this->connect) )
        {
			$this->connect();
        }		
	    return mysql_get_server_info($this->connect);
	}

	public function size()
	{
		$tables = $this->tables();
		foreach($tables as $table)
		{
			$size  +=  $table['size'];
		}
		return format::byte($size);
	}
		
	public function tables($status=false)
	{
		static $tables = array();
		if( !empty($tables) && $status==false )
		{
			return $tables;
		};
		$results = $this->getAll('SHOW TABLE STATUS');
		foreach($results as $table)
		{
			$tables[$table['Name']] = array(
				'name' => $table['Name'],
				'size' => $table['Data_length'] + $table['Index_length'],
			    'datalength' => $table['Data_length'],
				'indexlength' => $table['Index_length'],
				'rows' => $table['Rows'],
				'engine' => $table['Engine'],
				'collation' => $table['Collation'],
				'createtime' => $table['Create_time'],
				'updatetime' => $table['Update_time'],
				'comment' => $table['Comment'],
			);
		}
		return $tables;
	}
	
	public function table($tablename='')
	{
		$table = new database_mysql_table(&$this , $tablename);
		return $table;
	}	
}

class zotop_database_mysql_table
{
	protected $db = null; //表隶属于的db
	protected $name = ''; //表的名称
	protected $prefix = ''; //表的前缀名称prefix

	public function __construct(&$db , $name='' , $prefix='')
	{
		$this->db = $db;
		$this->name = empty($name) ? $this->name : $name ;
		$this->prefix = empty($prefix) ? $this->db->config('prefix') : $prefix ;
	}

	//表名称以#tablename开始将被自动加上前缀
	public function name($tableName='')
	{
		if(empty($tableName)){$tableName = $this->name;}
		if($tableName[0] == '#')
		{
			$tableName = $this->prefix . substr($tableName,1);
		}
		return $tableName;
	}
	
	//表是否存在
	public function exist()
	{
		if($tableName = $this->name())
		{
			$tables = $this->db->tables();
			if(in_array($tableName , array_keys($tables)))
			{
				return true;
			}
		}
		return false;
	}
    
	//表名称是否有效
    public function isValidName($tableName='')
    {
        if(empty($tableName)){$tableName = $this->name;}
        if ($tableName !== trim($tableName))
		{
            return false;
        }
        if (! strlen($tableName))
		{
            return false;// zero length

        }
        if (preg_match('/[.\/\\\\]+/i', $tableName))
		{
            return false;  // illegal char . / \
        }
        return true;
    }
    
	//创建表功能，应该使用field的创建功能自动创建表
	public function create($overwirte = false)
	{
		//如果已经存在，是否覆写
		if( $tablename = $this->name())
		{

			if($this->exist())
			{
				if($overwirte)
				{
					$this->drop();
				}
			}
			if( false !== $this->db->execute('CREATE TABLE `'.$tablename.'`( id int( 10 ) NOT NULL,PRIMARY KEY  (`id`)) ENGINE = MYISAM ;') )
			{
				return true;
			}
		}
		return false;
	}
	
	//drop表
	public function drop()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('DROP TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}	

	public function rename($newname)
	{
		$newname = ($newname[0]==='#') ? $this->prefix . substr($newname,1) : $newname;

		if( $this->isValidName($newname) && $tablename = $this->name())
		{
			$tables = $this->db->tables();
			if( !in_array($newname , $tables) )
			{

				if( false !== $this->db->execute('RENAME TABLE `'.$tablename.'` TO `'.$newname.'`;') )
				{
					return true;
				}
			}
		}
		return false;
	}

	public function optimize()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('OPTIMIZE TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

	public function check()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('CHECK TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

	public function repair()
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('REPAIR TABLE `'.$tablename.'`') )
			{
				return true;
			}
		}
		return false;
	}

	public function comment($comment)
	{
		if( $tablename = $this->name())
		{
			if( false !== $this->db->execute('ALTER TABLE `'.$tablename.'` COMMENT=\''.$comment.'\'') )
			{
				return true;
			}
		}
		return false;
	}
	
	public function primaryKey($key='')
	{
		static $fields = array();
		if( empty($fields) )
		{
			$fields = $this->fields(true);
		}
		if(empty($key))
		{

			$indexes = $this->index();
			if ( isset($indexes['PRIMARY']) )
			{
				return $indexes['PRIMARY']['field'];
			}
			return false;
		}
		if( array_key_exists(strtolower($key) , array_change_key_case($fields)) )
		{
			if( $this->primaryKey() )
			{
				$sql = 'ALTER TABLE `'.$this->name().'` DROP PRIMARY KEY, ADD PRIMARY KEY ( `'.$key.'` )';
			}
			else
			{
				$sql = 'ALTER TABLE `'.$this->name().'` ADD PRIMARY KEY ( `'.$key.'` )';
			}
			if( false !== $this->db->execute($sql) )
			{
				return true;
			}
		}
		return false;
	}

	public function index($key='', $action='INDEX')
	{
		if( empty($key) )
		{
			static $indexs = array();
			if(!empty($indexs)) return $indexs;
			$result = $this->db->getAll('SHOW INDEX FROM `'.$this->name().'`');
			if( $result )
			{
				foreach( $result as $index )
				{
					if ($index['Index_type']=='FULLTEXT')
					{
						$type = 'FULLTEXT';
					}
					elseif ($index['Key_name'] == 'PRIMARY')
					{
						$type = 'PRIMARY';
					}
					elseif ($index['Non_unique'] == '0')
					{
						$type = 'UNIQUE';
					}
					else
					{
						$type = 'INDEX';
					}

					$indexs[$index['Key_name']] = array(
						'name' => $index['Key_name'],
						'field' => $index['Column_name'],
						'unique' => ($index['Non_unique']==true)? 0 : 1,
						'index' => $index['Seq_in_index'],
						'type' => $type,
						'cardinality ' => $index['Cardinality '],
						'comment' => $index['Comment'],
					);
				}
			}
			return $indexs;
		}
		switch(strtoupper($action))
		{
			case 'INDEX':
				$sql = 'ALTER TABLE `'.$this->name().'` ADD INDEX `'.$key.'` ( `'.$key.'` )';
				break;
			case 'UNIQUE':
				$sql = 'ALTER TABLE `'.$this->name().'` ADD UNIQUE `'.$key.'` ( `'.$key.'` )';
				break;
			case 'FULLTEXT':
				$sql = 'ALTER TABLE `'.$this->name().'` ADD FULLTEXT `'.$key.'` ( `'.$key.'` )';
				break;
			case 'DROP':
				$sql = 'ALTER TABLE `'.$this->name().'` DROP INDEX `'.$key.'`';
				break;
		}
		if( !empty($sql) )
		{
			if( false !== $this->db->execute($sql) )
			{
				return true;
			}
		}
		return false;
	}
	
	public function fields($status = false)
	{
		static $fields = array();
		if( !empty($fields) && $status== false )
		{
			return $fields;
		}
		$result = $this->db->getAll('SHOW FULL FIELDS FROM `'.$this->name().'`');
		//zotop::dump($result);
		foreach($result as $field)
		{
			$fields[$field['Field']] = array(
				'name' => $field['Field'],
				'type' => strpos($field['Type'], '(') ? chop(substr($field['Type'], 0, strpos($field['Type'], '('))) : $field['Type'],
				'length' => strpos($field['Type'], '(') ? chop(substr($field['Type'], (strpos($field['Type'], '(') + 1), (strpos($field['Type'], ')') - strpos($field['Type'], '(') - 1))) : '',
				'null' => $field['Null'],
				'key' => $field['Key'],
				'default' => $field['Default'],
				'collation' => $field['Collation'],
				'extra' => $field['Extra'],
				'comment' => $field['Comment'],
			);
		}
		return $fields;
	}

	public function add($field)
	{
		$tablename = $this->name();

		$sql = 'ALTER TABLE `'.$tablename.'` ADD '.$this->specification($field);

		if( false !== $this->db->execute($sql) )
		{
			return true;
		}
		return false;
	}

	public function modify($field)
	{
		//ALTER TABLE `zotop_msg` MODIFY `title` INT( 10 ) AFTER `id`
		$tablename = $this->name();
		$fields = $this->fields(true);
		$data = $fields[$field['name']];
		if( !isset($data) ) return false;

		$field = array_merge($data,$field);

		$sql = 'ALTER TABLE `'.$tablename.'` MODIFY '.$this->specification($field);

		if( false !== $this->db->execute($sql) )
		{
			return true;
		}
		return false;
	}

	public function specification($field)
	{
		$name = $field['name'];
		$type = $field['type'];
		$length = isset($field['length']) ? $field['length'] :'';
		$null = isset($field['null']) ? $field['null'] :'';
		$attribute = isset($field['attribute']) ? $field['attribute'] :'';
		$comment = isset($field['comment']) ? $field['comment'] :'';
		$position = (isset($field['position']) || !empty($field['position'])) ? $field['position'] : '0';
		$default = isset($field['default']) ? $field['default'] :'';
		$extra = isset($field['extra']) ? $field['extra'] :'';
		$collation = isset($field['collation']) ? $field['collation'] :'';

		//VARCHAR
		$sql = '`'.$name.'` '.$type;
		//VARCHAR(32)
		if($length !='' && !preg_match('@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)$@i', $type))
		{
			$sql .= '('.$length.')';
		}
		//VARCHAR(32) UNSIGNED
        if ($attribute != '') {
            $sql .= ' ' . $attribute;
        }
		//VARCHAR(32) UNSIGNED NOT NULL
		if ($null !== false) {
            if (!empty($null)) {
                $sql .= ' NOT NULL';
            } else {
                $sql .= ' NULL';
            }
        }
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value'
		if(strtoupper($type) == 'TIMESTAMP' && strtoupper($default) == 'NOW')
		{
			$sql .= ' DEFAULT CURRENT_TIMESTAMP';
		}
		elseif( $extra !== 'AUTO_INCREMENT' && strlen($default)>0 )
		{
            if (strtoupper($default) == 'NULL') {
                $sql .= ' DEFAULT NULL';
            } else {
                if (strlen($default)) {
                    $sql .= ' DEFAULT \'' .$default . '\'';
                }
            }
		}
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value' COMMENT 'ddddd'
        if (!empty($comment)) {
            $sql .= " COMMENT '" . $comment . "'";
        }
		//VARCHAR(32) UNSIGNED NOT NULL DEFAULT 'value' COMMENT 'ddddd' AFTER `id`
		if(!empty($position)){
			if($position == '-1')
			{
				$sql .=' FIRST';
			}
			else
			{
				$sql .= ' AFTER `'.$position.'`';
			}
		}
		return $sql;
	}

	public function field($fieldname)
	{
		$field = new database_mysql_table_field(&$this->db , $this->name(), $fieldname );
		return $field;
	}
}

class zotop_database_mysql_table_field
{
	public $db = null; //表隶属于的db
	public $tablename = ''; //表的名称
	public $fieldname =''; //字段名称

	public function __construct(&$db , $tablename , $fieldname)
	{
		$this->db = $db;
		$this->tablename = $tablename ;
		$this->fieldname = $fieldname ;
	}

	public function rename($newname)
	{
		$tablename = $this->tablename;
		$fieldname = $this->fieldname;
		$fields = $this->db->table($tablename)->fields();
		$field = $fields[$fieldname];
		if($newname !== $fieldname)
		{
			$sql = 'ALTER TABLE `'.$tablename.'` CHANGE `'.$fieldname.'` `'.$newname.'` '.$field['type'];
			if( false !== $this->db->execute($sql) )
			{
				return true;
			}
			return false;
		}
		return true;
	}

	public function drop()
	{
		$tablename = $this->tablename;
		$fieldname = $this->fieldname;
		if( false !== $this->db->execute('ALTER TABLE `'.$tablename.'` DROP `'.$fieldname.'`') )
		{
			return true;
		}
		return false;
	}
}



class zotop_database_sqlite extends zotop_database
{

}


class router extends zotop_router
{

}


class controller extends zotop_controller
{

	public function __construct()
    {
        if( !zotop::user() )
        {
			zotop::redirect('zotop/login');
        }
        $this->__init();
    }
    
    public function __init()
    {
    }

    public function onDefault()
    {
        echo 'Hello Zotop Administrator!';
    }
}


class page extends zotop_page
{

	public static function header($page=array())
	{

	    $page['js']= array_merge(array(
	              'jquery'=> url::common().'/js/jquery.js',
				  'plugins'=> url::common().'/js/jquery.plugins.js',
				  'zotop'=> url::common().'/js/zotop.js',
	    ),(array)$page['js']);

	    $page['css']= array_merge(array(
	              'zotop'=> url::theme().'/css/zotop.css',
				  'global'=> url::theme().'/css/global.css'
	    ),(array)$page['css']);

	    $page = self::settings($page);

		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$page['title'].' Powered by '. zotop::config("zotop.title").'</title>';
        $html[] = self::meta($page['meta']);
		$html[] = self::stylesheet($page['css']);
		$html[] = self::script($page['js']);
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));
		$html[] = '	<script type="text/javascript">';
		$html[] = '		zotop.url.base = "'.url::base().'";';
		$html[] = '		zotop.url.common = "'.url::common().'";';
		$html[] = '		zotop.page.id = "'.page::id().'";';
		$html[] = '		zotop.user.id =0;';
		$html[] = '		zotop.user.username = "";';
		$html[] = '	</script>';
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($page['body']).'>';
		$html[] = '<div id="wrapper">';
		$html[] = '';
		$str =  implode("\n",$html);

		echo $str;
	}

	public static function footer()
	{
	    $html[] = '';
		$html[]	= '</div>';
		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public static function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop" class="clearfix">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>'.self::settings('title').'</h2>';
		$html[] = '<h3><a id="favorate" href="'.zotop::url('zotop/favorate/add').'" class="dialog" title="将该页面加入我的收藏夹">加入收藏</a></h3>';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public static function bottom($str='')
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		if(!empty($str))
		{
			$html[] = '<div id="bottom" class="clearfix">'.$str.'</div>';
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{$runtime}</b>,memory:<b>{$memory}</b>,includefiles:<b>{$include}</b></div>';
		$html[] = '</div>';

		echo implode("\n",$html);
	}

	public static function navbar($data,$current='')
	{
		$html = array();

		if(is_array($data))
		{

			$current=empty($current) ? router::action() : $current;
			$current=empty($current) ? $data[0]['id'] : $current;
            $html[] = '';
			$html[] = '<div class="navbar">';
			$html[] = '	<ul>';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$item['class'].' '.$class.'"><a href="'.$href.'"  id="'.$item['id'].'"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[] = '		<li class="'.$item['class'].' '.$class.'">'.$item.'</li>';
				}
			}
			$html[] = '	</ul>';
			$html[] = '</div>';
		}
		echo implode("\n",$html);
	}

    protected function config()
    {

    }

}


class form extends zotop_form
{

}


class dialog extends page
{
	public static function header($header=array())
	{
		$header['js']['dialog'] = url::common().'/js/zotop.dialog.js';
		$header['body']['class'] = 'dialog';
		parent::header($header);
	}

	public static function top()
	{
	    $html[] = '';
	    $html[] = '<div id="header">';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public static function bottom()
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		$html[] = '</div>';

		echo implode("\n",$html);
	}
}


class url extends zotop_url
{
    public static function root()
    {
        $application = url::application();
        $dir = explode('/',$application);
        array_pop($dir);//默认情况下，admin位于zotop下面
        array_pop($dir);
        $root = implode('/',$dir);
        return $root;
    }

    public static function theme()
    {
        $theme = zotop::config('zotop.theme');
        $theme = empty($theme) ? 'blue' : $theme ;
        return url::application().'/themes/'.$theme;
    }

}

?>