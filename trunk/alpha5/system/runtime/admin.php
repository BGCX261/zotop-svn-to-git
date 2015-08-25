<?php

defined('ZOTOP') OR die('No direct access allowed.');
defined('TIME') OR define('TIME', time());
defined('ZOTOP_START_TIME') OR define('ZOTOP_START_TIME',microtime(TRUE));
defined('ZOTOP_START_MEMORY') OR define('ZOTOP_START_MEMORY',memory_get_usage());
defined('MAGIC_QUOTES_GPC') OR define('MAGIC_QUOTES_GPC', (bool) get_magic_quotes_gpc());

/**
 * 系统的核心文件，包含系统底层的常用功能
 *
 * @package    zotop
 * @author     zotop team
 * @copyright  (c)2009 zotop team 
 * @license    http://zotop.com/license.html
 */
class zotop
{
    public static $events=array();
    public static $marks=array();
    
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

        //mark
        zotop::start('zotop');

        //缓存开始
        ob_start();

        //注册加载函数
        spl_autoload_register(array('zotop','autoload'));

        //设置系统事件
        zotop::add('system.boot',array('application','init'));//运行时
        zotop::add('system.route',array('router','init'));
        zotop::add('system.route',array('router','execute'));
        zotop::add('system.404',array('application','show404'));
        zotop::add('system.run',array('application','run'));                
        zotop::add('system.render',array('application','render'));
        zotop::add('system.shutdown',array('zotop','shutdown'));		
		zotop::add('system.reboot',array('runtime','reboot'));
		zotop::add('system.reboot',array('application','reboot'));      

		// Sanitize all request variables
		$_GET    = zotop::sanitize($_GET);
		$_POST   = zotop::sanitize($_POST);
		$_COOKIE = zotop::sanitize($_COOKIE);

		//boot
        $boot = true;
    }
    
    /**
     * 系统关闭，并输出渲染内容
     *
     * @return string
     */
    public static function shutdown()
    {
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
     * 系统重启
     *
     * @return string
     */
	public static function reboot()
	{
	    zotop::boot();
	    zotop::run('system.reboot');
		
	}
	
	public static function sanitize($value)
	{
		if (is_array($value) OR is_object($value))
		{
			foreach ($value as $key => $val)
			{
				// Recursively clean each value
				$value[$key] = zotop::sanitize($val);
			}
		}
		elseif (is_string($value))
		{
			if ( MAGIC_QUOTES_GPC === TRUE )
			{
				// Remove slashes added by magic quotes
				$value = stripslashes($value);
			}

			if (strpos($value, "\r") !== FALSE)
			{
				// Standardize newlines
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}

		return $value;
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
     * @param string $ext 类的后缀名称
     * @return bool;
     */
    public static function import($name, $path=ZPATH_LIBRARIES, $ext='.php')
    {
        static $imports=array();
        
        if( self::register($name) != false )
        {
            $file = self::register($name);
        }
        else
        {
            $file = $path.DS.str_replace( '.', DS, $name).$ext;
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
     * load 用于加载文件,相当于include_once，不返回任何错误
     * 
     * @param string $file 要加载的文件
     */
    public static function load($file){
        static $loads = array();
        
        if( isset($loads[$file]) )
        {
            return true;
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

        //$key 为空，则获取整个config数组
        if(empty($key))
        {
            return $configs;
        }
        
        //$key为数组，则追加整个数组
        if(is_array($key))
        {
            $configs=array_merge($configs,array_change_key_case($key));
            return $configs;
        }
        
        //$key为字符串，根据$key取值或者赋值
        if( is_string($key) )
        {
            $key=strtolower($key);
            if( is_null($value) )
            {
                if( isset($configs[$key]) )
                {
                    return $configs[$key];
                }
                return null;//未能找到对应的翻译时候，直接返回原语句                
            }
            $configs[$key] = $value;
            return $value;            
        }
        return $configs;
    }
    
    /**
     * 语言设置函数
     *
     * @param string|array $key string:设置的键名获取或者设置该键值，array：配置数组赋值数组，空返回整个设置数组
     * @param string $value 键值，用于赋值
     * @return mix
     */
    public static function lang($key=null, $value=null)
    {
        static $langs = array();
        
        //返回语言集
        if( empty($key) )
        {
            return $langs;
        }
        
        //批量设定语言
        if( is_array($key) )
        {
            $langs=array_merge($langs,array_change_key_case($key));
            return $langs;            
        }
        
        //赋值或者取值
        if( is_string($key) )
        {
            $key = strtolower($key);
            if( is_null($value) )
            {
                if( isset($langs[$key]) )
                {
                    return $langs[$key];
                }
                return $key;//未能找到对应的翻译时候，直接返回原语句
            }
            $langs[$key] = $value;
            return $value;
        }
        return $langs;
    }

    /**
     * 
     * 翻译并替换字符串中的变量
     * 
     * @param string $string 待转换字符串
     * @param array $params 参数 
     * @return string
     */
    public static function t($string, $params=array())
    {
        //翻译
        $string = zotop::lang($string);
        //替换掉变量      
        if(is_array($params))
        {
            foreach($params as $key=>$value)
            {
                $string = str_ireplace('{$'.$key.'}', $value, $string);
            }
        }
        return $string;
    }

	/**
	 * 标记一个监测开始点，并记录时间和内存消耗
	 * 
	 * @param string $tag 标记名称
	 * @return bool；
	 */
	public static function start($tag)
	{
		$tag=strtolower($tag);
		if(!isset(self::$marks[$tag]['start']))
		{
			self::$marks[$tag]['start']=array(
				'time'=>microtime(TRUE),
				'memory'=>function_exists('memory_get_usage')?memory_get_usage():0
			);
		}
		return true;
	}

	/**
	 * 标记一个监测结束点，并记录用时和内存消耗
	 * 
	 * @param string $tag 标记名称
	 * @return true;
	 */
	public static function stop($tag)
	{
		$tag=strtolower($tag);
		if(!isset(self::$marks[$tag]['stop']))
		{
			self::$marks[$tag]['stop']=array(
				'time'=>microtime(TRUE),
				'memory'=>function_exists('memory_get_usage')?memory_get_usage():0
			);
		}
		return true;
	}

	/**
	 * 获取特定的监测数据
	 * 
	 * @param string $tag 如果标记点为空则返回全部的监测点数据
	 * @return array；
	 */
	public static function mark($tag='')
	{
		if(empty($tag))
		{
			return self::$marks;
		}
		$tag=strtolower($tag);

		if(!isset(self::$marks[$tag]['start']))
		{
			return false;
		}
		if(!isset($marks[$tag]['stop']))
		{
			self::stop($tag);
		}
		return array(
			'time'=>number_format(self::$marks[$tag]['stop']['time']-self::$marks[$tag]['start']['time'],4),//返回单位为秒
			'memory'=>number_format((self::$marks[$tag]['stop']['memory']-self::$marks[$tag]['start']['memory'])/1024/1024,4) //返回单位为Mb
		);
	}	

     /**
     * 输出变量的内容
     * @param mixed $var 要输出的变量
     * @param string $label 多个变量输出问题可以加上标签
     * @param boolean $return 是否返回输出内容
     */
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

    /**
     * 错误输出
     * 
     *
     */
    public static function error($message='')
    {
        $error = array('code'=>0,'title'=>'ZOTOP ERROR','content'=>'Unknown System Error!');       
        //数组设置
        if( is_array($message) )
        {
            $error = array_merge($error,array_change_key_case($message));
        }
        if( is_string($message) )
        {
            $error['content'] = $message;
        }
        msg::error($error);        
    }
    
    /**
     * 系统应用的配置获取
     *
     * @param string|array $id 应用的ID，如：admin
     * @param string $key 键名称，如：name
     * @return mix
     */    
    public function application($id='', $key='')
    {
        static $applications = array();
        
        //获取配置
        if( empty($applications) )
        {
            $applications = zotop::config('zotop.application');
        }
        
        //获取 默认的设置
        if( empty($applications) )
        {
            $applications = include(ZPATH_CONFIG.DS.'application.php');
            //赋值
            zotop::config('zotop.application',$applications);
        }
        
        //返回配置
        if( empty($id) )
        {
            return $applications;
        }
        //赋值
        if( is_array($id) )
        {
            $applications = array_merge($applications,$id);
            zotop::config('zotop.application',$applications);
            return $applications;
        }
        $application = array();

        if( isset($applications[strtolower($id)]) )
        {
            $application = $applications[strtolower($id)];
        }
        
        if( isset($application) )
        {
            //$application['path'] = ZPATH_SYSTEM.DS.$application['path'];
            //$application['url'] = url::root().'/'.$application['url'];
            //$application['base'] = trim($application['url'],'/').'/'.$application['base'];
        }
        
        //返回应用信息
        if(empty($key))
        {
            return $application;
        }
        //返回具体项
        return $application[strtolower($key)];           
    }    

    /**
     * 模块的配置获取
     *
     * @param string|array $id 应用的ID，如：admin
     * @param string $key 键名称，如：name
     * @return mix
     */    
    public function module($id='', $key='')
    {
        static $modules = array();
        //fetch config
        if( empty($modules) )
        {
            $modules = zotop::config('zotop.module');
        }
        //fetch default config
        if( empty($modules) )
        {
            $modules = include(ZPATH_CONFIG.DS.'module.php');
            //set config
            zotop::config('zotop.module',$modules);
        }
        
        if( is_array($modules) )
        {
            foreach($modules as $k=>$m)
            {
                $modules[$k]['path'] = path::decode($modules[$k]['path']);
                $modules[$k]['url'] = url::decode($modules[$k]['url']);
            }
        }
        //return modules
        if( empty($id) )
        {
            return $modules;
        }        
        //set and return modules
        if( is_array($id) )
        {
            $modules = array_merge($modules,$id);
            zotop::config('zotop.module',$modules);
            return $modules;
        }
        
        //return module
        if( is_string($id) )
        {
            $module = array();
            if( isset($modules[strtolower($id)]) )
            {
                $module = $modules[strtolower($id)];
            }
           
            if(empty($key))
            {
                return $module;
            }
            return $module[strtolower($key)];            
        }
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
     * 存储简单类型数据，字符串，数组
     * @param string $file 完整的file名称或者cache.setting,config.system
     * @param mix $value 值
     * @param int $expire 缓存时间
     * 
     * @return mix
     */
    public static function data($file, $value='', $expire=0)
    {
        static $files = array();
        
		if( strtolower(substr($file , -4)) != '.php' && strpos($file, DS) == false )
		{
		    $file = ZPATH_DATA.DS.str_replace( '.',DS,$file).'.php';		    
		}
        
        if( '' !== $value )
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
    
    /**
     * 缓存
     *
     * @param $config array  数据库参数
     * @return object 数据库连接
     */
	public static function cache($name, $value='', $expire=0)
	{

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

    public function session()
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
            $modelPath = zotop::module($module,'path').DS.'models'.DS.$model.'.php';            
            if( zotop::load($modelPath)== false )
            {
                zotop::error(zotop::t('<h2>请检查相应的模型文件是否存在</h2>文件地址：{$modelPath}',array('modelPath'=>$modelPath))); 
            }
        }
        if( class_exists($modelName) )
        {
            $m = new $modelName();
            $m -> moduleName = $module;
            $models[$name] = $m;
            return $m;
        }
        zotop::error(zotop::t('<h2>请检查相应的模型文件中是否存在模型类 {$modelName}</h2>文件地址：{$modelPath}',array('modelPath'=>$modelPath,'modelName'=>$modelName)));
    }

   /**
     * 读取存储的用户信息
     *
     */
    public static function user($key='',$application='')
    {
        $user = array();
        
        $application = empty($application) ? APP_NAME : $application;
        $cookieName = 'zotop.user.'.$application;
        
        if( empty($user) )
        {
            $user = zotop::cookie($cookieName);
            $user = is_array($user) ? array_change_key_case($user) : array();
        }

        if( $key === null )
        {
            return zotop::cookie($cookieName,null);
        }

        if( empty($key) )
        {
            return empty($user) ? false : $user;
        }

        if( is_array($key) )
        {
            $user = array_merge($user , array_change_key_case($key));
            return zotop::cookie($cookieName,$user);
        }

        $value = $user[strtolower($key)];

        if( isset($value) )
        {
            return $value;
        }
        return null;
    }    
    
}

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
    
    public static function reboot()
    {
        
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
                call_user_func_array(array($controller,'__before'),$arguments);
                call_user_func_array(array($controller,$method),$arguments);
                call_user_func_array(array($controller,'__after'),$arguments);
                return true;
            }
            //当方法不存在时，默认调用类的_empty()函数，你可以在控制器中重写此方法
            return call_user_func_array(array($controller,'__empty'),array($method,$arguments));
        }
        return false;
    }
    
    /**
     * 渲染输出内容
     *
     * @param string $output 待渲染输出的内容
     * @return string
     */
    public static function render($output)
    {
        $time = number_format(microtime(TRUE) - ZOTOP_START_TIME , 4);
        $memory = number_format((memory_get_usage() - ZOTOP_START_MEMORY)/1024/1024 , 4);
        $output=str_ireplace
        (
            array('{#runtime}','{#memory}','{#include}'),
            array($time.' S',$memory.' MB',count(get_included_files())),
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
                $error = "<div>errno:[{$errno}]</div><div>message: {$message}</div><div>file: {$file} </div><div>line: {$line} </div>";
                exit('<div>'.$error.'</div>');
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                $error = "<div>errno:[{$errno}]</div><div>message: {$message}</div><div>file: {$file} </div><div>line: {$line} </div>";
                //exit('<div>'.$error.'</div>');
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
        msg::error(array(
        	'title'=>'404 error',
            'content'=>zotop::t('<h2>未能找到相应页面，请检查页面文件是否存在？</h2>{$filepath}',$data)
        ));
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
        return empty($action) ? 'index' : $action;
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
        $path = zotop::module($module,'path');

        if( empty($path)  )
        {
             zotop::error(array(
            	'title'=>'系统错误',
                'content'=>zotop::t('<h2>未能找到相应模块，请检查模块是否未安装或者已被禁用？</h2>模块名称：{$module}',array('module'=>$module))
             ));            
        }            
        $path = $path.DS.router::application().DS.$controller.'.php';
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
            return $action.'Action';
        }
        return 'indexAction';
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
        
    /**
     * 路由初始化
     * 
     *
     */
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
    
    /**
     * 解析URI
     * 
     *
     */
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
     * 获取当前的模块名称
     *
     * @return string;
     */
    public static function module()
    {
        return self::$module;
    }

    /**
     * 获取控制器的名称
     * 
     * @return string;
     */
    public static function controller()
    {
        return self::$controller;
    }

    /**
     * 获取动作名称
     *
     * @return string;
     */
    public static function action()
    {
        return self::$action;
    }

    /**
     * 获取参数
     * 
     * @return array;
     */
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

class zotop_controller
{
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        $this->__init();
        $this->__check();
    }
    /**
     * 初始化动作，当控制器被初始化的时候调用
     *
     */
    public function __init()
    {
        
    }
    /**
     * 初始化权限检查，当控制器被初始化的时候调用
     *
     */
    public function __check()
    {
        
    }        
    /**
     * 动作触发之前调用
     *
     */
    public function __before($arguments='')
    {
        zotop::dump($arguments);
    }
    
    /**
     * 当动作被触发之后调用
     *
     */
    public function __after($arguments='')
    {
        
    }
        
    /**
     * 空动作，当找不到对应动作时候触发，可以被重载 
     *
     */
    public function __empty($method='',$arguments='')
    {
        msg::error(array(
        	'title'=>'404 error',
            'content'=>zotop::t('<h2>未能找到相应的动作，请检查控制器中动作是否存在？</h2>控制器文件：{$file}<br>动作名称：{$method}',array('file'=>application::getControllerPath(),'method'=>$method))
        ));
    }
    
    public function redirect($uri , $params=array() , $fragment='')
    {
        $url = zotop::url($uri,$params,$fragment);
        header("Location: ".$url);
        exit();
    }
    
    public function error($content='', $life=9)
    {
        msg::error($content, $life=9);
    }
    
    public function success($content='', $url='', $life=5, $extra='')
    {
        msg::success($content, $url, $life, $extra);
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
    public $db = null; // 当前数据库操作对象
    public $moduleName=''; //当前的模块名称
    protected $modelName = ''; //模型名称
    protected $tableName = ''; //数据表名称
    protected $tablePrefix = ''; //数据表的前缀
    protected $primaryKey = ''; //主键名称
    protected $data = array(); //属性设置
    
	public function __construct()
	{
		if ( ! is_object($this->db) )
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
            $this->modelName =   substr(get_class($this),0,-6);
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
            $tableMeta = $this->getTableStructure();
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
     * 获取数据表的结构
     * 
     *
     */
    public function getTableStructure($flush = false)
    {
        static $table;
        
        $tableName = $this->getTableName(true);

        $tableFile = dirname(__FILE__);
        
        zotop::dump($tableFile);
    }

    
    public function getAll($sql='')
    {
        return $this->db()->from($this->getTableName())->getAll($sql);
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
            zotop::error(zotop::t('未能找到 <b>{$key}</b> 等于 <b>{$value}</b> 的数据<br>'.reset($this->db->sql()),array('key'=>$key,'value'=>$value)));            
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
    public function insert($data)
    {
        if( is_array($data) )
        {
            $this->db()->set($data);
        }
        
        $key = $this->getPrimaryKey();
        
        $set = $this->db()->sqlBuilder('set');

        if( !isset($set[$key]) )
        {
            return false;
        }
        $this->db()->from($this->getTableName());
               
        return $this->db()->insert();        
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
               
        return $this->db()->delete();     
    }
    
    public function max($key='', $where='', $default=0)
    {
        $key = empty($key) ? $this->getPrimaryKey() : $key;
        
        $max = $this->db()->select('max('.$key.') as max')->from($this->getTableName())->where($where)->getOne();
        
        if( is_numeric($max) )
        {
            return $max;
        }
        return $default;
    }
    
    public function isExist($key='', $value='')
    {
        $key = empty($key) ? $this->getPrimaryKey() : $key;
        $value = empty($value) ? $this->$key : $value;
        $where = array($key,'=',$value);
        
        $count = $this->db()->select('count('.$key.') as num')->from($this->getTableName())->where($where)->getOne();
        if( is_numeric($count) && $count > 0 )
        {
            return true;
        }
        return false;
    }

	public function count($key='',$value='')
	{
        $key = empty($key) ? $this->getPrimaryKey() : $key;
        $value = empty($value) ? $this->$key : $value;
        $where = array($key,'=',$value);
		
		$count = $this->db()->select('count('.$key.') as num')->from($this->getTableName())->where($where)->getOne();
		if( !is_numeric($count) )
		{
			$count = 0;
		}
		return $count;
	}

	public function cache($name='' , $sql = '')
	{
		$name = empty($name) ? $this->getModelName() : $name;
		$data = array();
		$data = $this->getAll($sql);		
		zotop::data($name, $data);
		return $data;	
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
    public $data = array();
    
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        
    }    
    
	/**
	 * 为页面生成一个全局编号，用于media等数据定位
	 *
	 */
	public function getUid()
	{
	    if( empty($this->uid) )
	    {
    	    $this->uid = application::getApplication().'://'.application::getModule().'.'.application::getController().'.'.application::getAction();	    
    	    $this->uid = empty($id) ? $namespace : $namespace.'/'.$id;
    	    $this->uid = md5($namespace);
    	    
	    }	    
	    return $this->uid;
	}
	
	public function getTemplatePath($action='')
	{
	    if( empty($this->template) )
	    {
            if(empty($action))
            {
                $action = application::getAction();
            }
            $module = application::getModule();
            $controller = application::getController(); 
            $path = zotop::module($module,'path');
            $path = $path.DS.router::application().DS.'template'.DS.$controller.DS.$action.'.php';
            return $path;   	        
	    }
	    return $this->template;   
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

	public function set($name, $value=null)
	{
        if( is_array($name) )
        {
            $this->data = array_merge($this->data,$name);
        }
        
        if( is_string($name) )
        {
            $this->data[$name] = $value;
        }
	}
	
	public function data()
	{
	    return $this->data;
	}

	
	public function header()
	{

		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$this->title.' '. zotop::config("zotop.title").'</title>';
        $html[] = '	'.html::meta('keywords',$this->keywords);
        $html[] = '	'.html::meta('description',$this->description);
        $html[] = '	'.html::meta('Content-Type','text/html;charset=utf-8');
        $html[] = '	'.html::meta('X-UA-Compatible','IE=EmulateIE7');
		$html[] = '	'.html::stylesheet(url::theme().'/css/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(url::theme().'/css/global.css',array('id'=>'global'));		
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));        
        
		//$html[] = page::meta($header['meta']);
		//$html[] = page::stylesheet($header['css']);
		//$html[] = page::script($header['js']);
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';

		$str =  implode("\n",$html);

		echo $str;	    
	}
	
	public function footer()
	{
	    $html[] = '';

		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}	
	
	public function add($str)
	{
		echo $str."\n";
	}

	/**
	 * 给页面附加一个js文件，必须在header之前声明
	 *
	 */
	public function addScript($file)
	{
	    $this->js = array_merge((array)$this->js,(array)$file);
	}
	public function addJS($file)
	{
	    $this->addScript($file);
	}	
	
	/**
	 * 给页面附加一个css文件，必须在header之前声明
	 * 
	 */
	public function addStyleSheet($file)
	{    
	    $this->css = array_merge((array)$this->css,(array)$file);
	}
	public function addCSS($file)
	{
	    $this->addStyleSheet($file);
	}

	public function render($file='')
	{
	    if( !empty($file) )
	    {
	        $this->template = $file;
	    }
	    
	    $this->template = $this->getTemplatePath();

	    if( file_exists($this->template) )
        {
            ob_start();            
            extract($this->data(), EXTR_SKIP);
            include $this->template;
    		$content = ob_get_contents();
    		ob_clean();
    		return $content;
        }
        msg::error(array(
            'title'=>'404 error',
            'content'=>zotop::t('<h2>未能找到页面模板，请检查确认模板文件是否存在</h2> 模板文件：{$file}',array('file'=>$this->template)),
        ));	    
	}
	
	public function display($file='')
	{
        static $display = false;
        if($display) return true;
	    echo $this->render($file);
	    $display = true;
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
    		if((empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['_FORMHASH'] == form::hash())
			{
				return true;
			}
			else
			{
				zotop::error('invalid submit!');
			}
		}
		return false;
	}



	public static function hash()
	{
		$hash = zotop::config('safety.authkey');
		$hash = empty($hash) ? 'zotop form hash!' : $hash;
		$hash = substr(time(), 0, -7).$hash;
		$hash = strtoupper(md5($hash));
		//return substr(time(), 0, -7);
		return $hash;
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
		$html[] = field::hidden(array('name'=>'_REFERER','value'=>request::referer()));
		$html[] = field::hidden(array('name'=>'_FORMHASH','value'=>form::hash()));
        //加载常用js
		$html[] = html::script(url::common().'/js/jquery.validate.js');
		$html[] = html::script(url::common().'/js/jquery.validate.additional.js');
		$html[] = html::script(url::common().'/js/jquery.form.js');
		//表单头部		
		if( isset($form['title']) || isset($form['description']) )
		{
		    $html[] = '<div class="form-header clearfix">';
			$html[] = isset($form['icon']) ? '		<div class="form-icon"></div>' : '';
		    $html[] = isset($form['title']) ? '		<div class="form-title">'.$form['title'].'</div>' : '';
            $html[] = isset($form['description']) ? '		<div class="form-description">'.$form['description'].'</div>' : '';
            $html[] = '</div>';
		}
	    //表单body部分开始
        $html[] = '<div class="form-body">'; 
        
        echo implode("\n",$html);
	}
	public static function footer( $buttons = array(), $str ='')
	{
		$html[] = '';
		$html[] = '</div>';
	    if( !empty($buttons) )
	    {
    	    if( is_array($buttons) )
    	    {
        	    $html[] = '<div class="buttons">';
        		foreach($buttons as $button)
        		{
        			$html[] = form::control($button);
        		}
        		$html[] = '</div>';     
    	    }
    	    else
    	    {
    	        $html[] = $buttons;
    	    }
	    }
	    $html[] = '<div class="form-footer">'.$str.'</div>';
	    $html[] = '</form>';
		echo implode("\n",$html);
		form::$template = '';
	}

	public static function top()
	{
		$html[] = '';
		$html[] = '<div class="form-top clearfix">';		
		$html[] = '	<div class="form-title">'.$title.'</div>';
		$html[] = '	<div class="form-description">'.$description.'</div>';
	    $html[] = '</div>';
		echo implode("\n",$html);		
	}

	public static function bottom($main='',$extra='')
	{
		$html[] = '';
		$html[] = '<div class="form-bottom clearfix">';		
		$html[] = '	<div class="form-bottom-main">'.$main.'</div>';
		$html[] = '	<div class="form-bottom-extra">'.$extra.'</div>';
	    $html[] = '</div>';
		echo implode("\n",$html);
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
	    $options = arr::take('options',$attrs);
		$options = field::_option($options);
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
	    $options = field::_option($options);
	    $value = arr::take('value',$attrs);//即取出了value和options，又把他们从$attrs中去掉了
		$value = json_decode($value);
		if (!is_array($value))
		{
			$value = array($value);
		}
		//zotop::dump($value);
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
		$options = field::_option($options);
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
	   $attrs['handle'] = empty($attrs['handle']) ? zotop::url('zotop/upload/image') : $attrs['handle'];

	   $html[] = html::script(url::common().'/js/zotop.upload.js');
	   $html[] = '<div class="field-inner inline-block">';
	   $html[] = '	'.field::text($attrs);
	   $html[] = '	'.html::input( array('type'=>'button','class'=>'upload-image','title'=>zotop::t('上传图片')) );
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

	/**
	 * 将字符串转化成标准的选项数组
	 *
	 * @param  string $options 选项字符串
	 * @param  string $s1  第一分割符号
	 * @param  string $s2  第二分割符号
	 * @return array
	 */
	public function _option($options, $s1 = "\n", $s2 = '|')
	{
		if( is_array($options) )
		{
			return $options;
		}
		$options = explode($s1, $options);
		foreach($options as $option)
		{
			if(strpos($option, $s2))
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			$os[$value] = $name;
		}
		return $os;
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
	public static function header($block=array())
	{
	    if( !is_array($block) )
		{
		    $block = array('title'=>$block);
		}
	    $html[] = '';
		$html[] = '<div class="block clearfix '.$block['class'].'"'.(isset($block['id']) ? ' id="'.$block['id'].'"':'').'>';
		if(isset($block['title']))
		{
		    $html[] = '	<div class="block-header">';
		    $html[] = '		<h2>'.$block['title'].'</h2>';
		    if( isset($block['action']) )
		    {
		        $html[] = '		<h3>'.$block['action'].'</h3>';
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
	
	public static function add($str)
	{
	    echo $str;    
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
    public static function show(array $msg)
    {

		$page = new page;
		$page->title = $msg['type'];
		$page->body = array('class'=>'msg');
		$page->header();
				
			$page->add('<div id="msg" class="'.$msg['type'].' clearfix">');
			$page->add('	<div id="msg-type">'.$msg['type'].'</div>');
			$page->add('	<div id="msg-life">'.$msg['life'].'</div>');
		    $page->add('	<div id="msg-title">'.$msg['title'].'</div>');
			$page->add('	<div id="msg-content">'.$msg['content'].'</div>');
  			$page->add('	<div id="msg-extra">'.$msg['extra'].'</div>');

  			if( !empty($msg['url']) )
  			{
    			$page->add('	<div>');
    			$page->add('		<div><b>如果页面没有自动跳转，请点击以下链接</b></div>');
    			$page->add('		<a href="'.$msg['url'].'" id="msg-url">'.$msg['url'].'</a>');
    			$page->add('	</div>');
  			}
			$page->add('</div>');
            $page->add('<div id="powered">'.zotop::config('zotop.name').' '.zotop::config('zotop.version').'</div>');
			
		$page->footer();
		exit;
    }
    
    /**
     * 显示错误消息
     *
     */
    public static function error($content='', $life=3)
    {
        $msg = array('type'=>'error','title'=>'error','content'=>'','life'=>0);
        
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content);
        }
        else
        {
           $msg['content'] =  $content;
           $msg['life'] =  $life; 
        }        
		$msg['type'] = 'error';
        $msg['extra'] = $msg['extra'].'<div class="msg-title"><b>如果问题未能解决，请尝试以下操作：</b></div><ul class="list"><li>点击<a href="javascript:location.reload();"> 刷新 </a>重试，或者以后再试</li><li>或者尝试点击<a href="javascript:history.go(-1);"> 返回前页 </a>后再试</li></ul>';
        msg::show($msg);
    }

    /**
     * 显示成功消息
     *
     */
    public static function success($content='', $url='', $life=3, $extra='')
    {
        $msg = array('type'=>'success','title'=>'success','content'=>'','life'=>0);
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content);
        }
        else
        {
           $msg['content'] =  $content;
           $msg['url'] =  $url;
           $msg['extra'] =  $extra;
           $msg['life'] =  $life;
        }          
        $msg['type'] = 'success';
        msg::show($msg);
    }    
}

class zotop_path
{
	/* path解析
	 *
	 * @parem string $path 路径名称
	 * @return string
	 */
	public static function decode($path)
	{
        
	    $p = array(
	        '$modules'=>ZPATH_MODULES,
	    );
	    $path = strtr($path,$p);
	    $path = path::clean($path);
	    return $path;
	}

	/**
	 * 将真实的path转化为系统的path表示方法
	 * 
	 *
	 */
	public static function encode($path)
	{
        return $path;
	}
    
	
	/**
	 * 清理路径中多余的斜线
	 * 
	 *
	 */
	public static function clean($path,$ds='')
	{
		$ds = empty($ds) ? DIRECTORY_SEPARATOR : $ds;
		
	    $path = trim($path);
		$path = empty($path) ? ZPATH_ROOT : $path;
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
	    $file = path::decode($file);
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
       $file = path::decode($file);
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

	/**
	 * 将字符串转化为数组
	 *
	 * @param string $key 弹出的键名
	 * @param array $array 目标数组
	 * @param boolean $bool 是否区分大小写
	 * @return $mix	被弹出 的数据
	 */
	public static function decode($array,$s1 = "\n", $s2 = '|')
	{
		$options = explode($s1, $array);
		foreach($array as $option)
		{
			if(strpos($option, $s2))
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			$os[$value] = $name;
		}
		return $os;
	}

	public static function trim($input)
	{
		if (!is_array($input) )
		{
			return trim($input);
		} 
		return array_map(array('arr','trim'), $input);
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
		$root = ZURL_ROOT;
		$root = trim($root,'\\');
	    return $root;
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
            return APP_URL;
        }
        $url = zotop::application($id , 'url');
        if( $url[0] !=='/' && strpos($url,'://')===false )
        {
            $url =  url::root().'/'.$url;
        }
        return $url;
    }
    
    public static function modules()
    {
        return url::system().'/modules';
    }
    
    public static function module($id='')
    {
        if( empty($id) )
        {
            $id = application::getModule();            
        }
        $url = url::system().'/modules/'.$id;
        return $url;
    }
    
    public static function controller()
    {
        $url = url::module();
        $url = $url.'/'.APP_NAME;
        return $url;
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
	public static function system()
	{
	    return url::root().'/'.basename(ZPATH_SYSTEM);
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


	public static function encode($url)
	{

	}

	public static function decode($url)
	{
    	$url = strtr($url,array(
		    '$root'=>url::root(),
		    '$system'=>url::system(),
		    '$theme'=>url::theme(),
		    '$modules'=>url::modules(),
			'$this'=>url::controller(),
    	    '$common'=>url::common()
		));
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
		return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
	}
	//编码字符串
	public static function encode($str)
	{
		return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
	}
	//解码字符串
	public static function decode($str)
	{
		return htmlspecialchars_decode($str, ENT_QUOTES);
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
            $href = url::clean($href);
            $href = url::decode($href);
            //只加载一次
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
				$href = url::clean($href);
				$href = url::decode($href);
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
		$attrs['value'] = isset($attrs['value']) ? $attrs['value'] : '';

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
	
	public static function ul($data,$attrs=array('class'=>'list'))
	{
	    $html[] = '<ul'.html::attributes($attrs).'>';
	    foreach($data as $item)
	    {
	        $html[] = '<li>'.$item.'</li>';
	    }
	    $html[] = '</ul>';
	    return implode("\n",$html);
	}
	
	public static function msg($messages,$type='notice')
	{
        $html[] = '<div class="zotop-msg clearfix '.$type.'">';
        $html[] = '	<div class="zotop-msg-icon"></div>';
        $html[] = '	<div class="zotop-msg-content">';
        $html[] = is_array($messages) ? html::ul($messages) : $messages;
        $html[] = '	</div>';
        $html[] = '</div>';
        return implode("\n",$html);	   
	}

}

class zotop_ip
{
    /**
     * 获取当前的ip地址     * 
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
    public static function format($time,$format='{YYYY}-{MM}-{DD} [HH]:[MM]:[SS]')
    {
		if( is_null($time) )
		{
			return null;
		}
        $format = strtoupper($format);
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

defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 树形操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_tree
{
    public $nodes = array(); //树形的元数据2维数组
    public $root = 'root'; //根元素
    
	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      array('id'=>'1','parentid'=>'0','name'=>'一级栏目一'),
	*      array('id'=>'2','parentid'=>'0','name'=>'一级栏目二'),
	*      array('id'=>'3','parentid'=>'1','name'=>'二级栏目一'),
	*      array('id'=>'4','parentid'=>'1','name'=>'二级栏目二'),
	*      array('id'=>'5','parentid'=>'2','name'=>'二级栏目三'),
	*      array('id'=>'6','parentid'=>'3','name'=>'三级栏目一'),
	*      array('id'=>'7','parentid'=>'3','name'=>'三级栏目二')
	* )
	*/    
    public function __construct($trees=array(), $root='root')
    {
        $nodes =array();
        if( is_array($trees) )
        {
            foreach($trees as $tree)
            {
                $nodes[$tree['id']] = $tree;
            }
        }
        $this->nodes = $nodes;
        $this->root = $root;
    }
    
    /**
	* 获取父级节点数组
	* @param int|string
	* @return array
	*/
    public function getParent($id)
    {
        $parent = array();
        if( isset($this->nodes[$id]) )
        {
            $parentid = $this->nodes[$id]['parentid'];
            $parentid = $this->nodes[$parentid]['parentid'];
            foreach($this->nodes as $key=>$node)
            {
                if( $node['parentid'] == $parentid )
                {
                    $parent[$key] = $node; 
                }
            }
            return $parent;
        }
        return false;
    }
    
    /**
	* 获取子级节点数组
	* @param int|string
	* @return array
	*/    
    public function getChild($parentid)
    {
        $child = array();
        foreach($this->nodes as $key=>$node)
        {
            if( $node['parentid'] == $parentid )
            {
                $child[$key] = $node; 
            }
        }
        return $child;
    }

    /**
	* 得到当前位置的节点数组
	* @param int|string
	* @return array
	*/    
    public function getPosition($id,&$pos=array())
    {
        $position = array();
        if( isset($this->nodes[$id]) )
        {
            $pos[] = $this->nodes[$id];            
            $parentid = $this->nodes[$id]['parentid'];
            if( isset($this->nodes[$parentid]) )
            {
                $this->getPosition($parentid,$pos);
            }
            
            if( is_array($pos) )
            {
                krsort($pos);//逆向排序
                foreach($pos as $node)
                {
                    $position[$node['id']] = $node;
                }
            }
            return $position;
        }
        return false;        
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
    protected static $queryNum =0;
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
               zotop::error(zotop::t('错误的数据库配置文件',$config));
            }
            //数据库驱动程序
            $driver = 'database_'.strtolower($config['driver']);
            
            //加载驱动程序
            if( !zotop::autoload($driver) )
            {
              zotop::error(zotop::t('未能找到数据库驱动 "{$driver}"',$config));
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
    
    public function insert($table='',$data='')
    {
        $sqlBuilder = (array)$this->sqlBuilder;          
       
        if( is_array($data) )
        {
             $sqlBuilder['set'] = array_merge($sqlBuilder['set'],$data);
        }
        if( !empty($table) && is_string($table))
        {
            $sqlBuilder['from'] = $table;
        }               
        
        $this->sqlBuilder = array();    
        
        $data = $sqlBuilder['set'];
        
        if ( !is_array($data) ) return false;
        foreach( $data as $field=>$value )
        {
            $fields[] = $this->escapeColumn($field);
            $values[] = $this->escapeValue($value);
        }
        
        $sql = 'INSERT INTO %TABLE% (%FIELDS%) VALUES (%VALUES%)';
        $sql = str_replace(
            array('%TABLE%','%FIELDS%','%VALUES%'),
            array(
				$this->parseFrom($sqlBuilder['from']),
				implode(',', $fields),
				implode(',', $values)
            ),
            $sql
        );
        
        return $this->execute($sql);
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
	
	public function delete($table='',$where='')
	{
        $sqlBuilder = (array)$this->sqlBuilder;          
        
        if( is_array($where) )
        {
            $sqlBuilder['where'] = array_merge($sqlBuilder['where'],$where);
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
            
        $sql = 'DELETE FROM %TABLE%%WHERE%';
        $sql = str_replace(
            array('%TABLE%','%WHERE%'),
            array(
				$this->parseFrom($sqlBuilder['from']),
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
		
		if( !empty($where) && !empty($where[0]))
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
		else
		{
			$str = $orderby;
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
                
            //解析值中的如：num = array('num','+',1) 或者array('num','-',1) 
			if( is_array($val) && count($val)==3 && in_array($val[1],array('+','-','*','%')) && is_numeric($val[2]) )
			{
				 $str .= ','.$this->escapeColumn($key).' = '.$this->escapeColumn($val[0]).$val[1].(int)$val[2];
			}
			else
			{
				$str .= ','.$this->escapeColumn($key).' = '.$this->escapeValue($val);
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
				//$value = (int) $value;
				$value = ($value === FALSE) ? 0 : 1;
			    break;
			case 'double':
				$value = sprintf('%F', $value);
			    break;
			case 'array':
				$value = $this->escapeValue(json_encode($value));
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
            zotop::error(zotop::t('Cannot use database `{$database}`',$this->config()));
        }
        zotop::error('Cannot connect to database server'.mysql_error());
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
				
				zotop::error(zotop::t('<h2>SQL: {$sql}</h2>{$error}',array('sql'=>$sql,'error'=>@mysql_error())));
			}
			
			$this->numRows = @mysql_num_rows($this->query);
			
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
			//当使用 UPDATE 查询，MySQL 不会将原值与新值一样的列更新。这样使得 mysql_affected_rows() 函数返回值不一定就是查询条件所符合的记录数，只有真正被修改的记录数才会被返回
			$this->numRows = mysql_affected_rows($this->connect);						
						
			return true;
		}
		return false;
	}

	/**
	 * 执行一个sql语句并返回结果数组
	 *
	 * @param $sql
	 * @return array
	 */
	public function getAll($sql='')
	{
		
		$sql = $this->compileSelect($sql);

		if($query = $this->query($sql))
		{
			$result = array();
			if( $this->numRows >0 ) {
				while( $row = mysql_fetch_assoc($query) ){
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
	public function getRow($sql='')
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

	/**
	 * 从查询句柄提取一条记录，并返回该记录的第一个字段
	 *
	 * @param $sql
	 * @return mixed
	 */
	public function getOne($sql='')
	{
	    $sql = $this->compileSelect($sql);

		$row = $this->getRow($sql);

		if( $row && $this->numRows > 0 )
	    {
	       return reset($row);
	    }
	    return false;
	}

	/**
	 * 返回limit限制的数据,用于带分页的查询数据
	 *
	 * @param $sql
	 * @return mixed
	 */
	public function getRange($sql='',$from=0,$count=10)
	{

	}
	
    /**
     * SQL指令安全过滤
	 *
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */	
	public function escape($str)
	{
		if( is_array($str) )
		{
			foreach($str as $key => $val)
	   		{
				$str[$key] = $this->escape($val);
	   		}
   		
	   		return $str;
		}

		if (function_exists('mysql_real_escape_string') AND is_resource($this->connect))
		{
			$str = mysql_real_escape_string($str, $this->connect);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			$str = mysql_escape_string($str);
		}
		else
		{
			$str = addslashes($str);
		}

		return $str;
	}

	public function escapeColumn($field)
	{
		if( $field=='*' )
		{
			return $field;
		}
		
	    if ( preg_match('/(avg|count|sum|max|min)\(\s*(.*)\s*\)(\s*as\s*(.+)?)?/i', $field, $matches))
		{
		    if ( count($matches) == 3)
			{
				return $matches[1].'('.$this->escapeColumn($matches[2]).')';
			}
			else if ( count($matches) == 5)
			{
				return $matches[1].'('.$this->escapeColumn($matches[2]).') AS '.$this->escapeColumn($matches[4]);
			}
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
    public $page;
    public $user;
    
    public function __init()
    {
        $this->user = zotop::user();
    }
    
    public function __check()
    {                
        if( empty($this->user) )
        {
            zotop::redirect('zotop/login');
        }        
    }

    public function __before()
    {

    }
    
    public function __after()
    {
       
    }
    

    public function navbar()
    {
        
    }
    

}

class dialog extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'dialog'));
        $this->addScript('$common/js/zotop.dialog.js');
        parent::header();
    }
}

class page extends zotop_page
{
	public function header()
	{
        $javascript = (array)$this->js;
        $css = (array)$this->css;
        $metas = (array)$this->meta;
        
		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$this->title.' '. zotop::config("zotop.title").'</title>';
        $html[] = '	'.html::meta('keywords',$this->keywords.' '.zotop::config("zotop.keywords"));
        $html[] = '	'.html::meta('description',$this->description.' '.zotop::config("zotop.description"));
        $html[] = '	'.html::meta('Content-Type','text/html;charset=utf-8');
        $html[] = '	'.html::meta('X-UA-Compatible','IE=EmulateIE7');
	    foreach($metas as $meta)
		{
		    $html[] = '	'.html::meta($meta);
		}        
		$html[] = '	'.html::stylesheet(url::theme().'/css/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(url::theme().'/css/global.css',array('id'=>'global'));
	    foreach($css as $stylesheet)
		{
		    $html[] = '	'.html::stylesheet($stylesheet);
		}			
		$html[] = '	'.html::script(url::common().'/js/jquery.js',array('id'=>'jquery'));
		$html[] = '	'.html::script(url::common().'/js/jquery.plugins.js',array('id'=>'plugins'));
		$html[] = '	'.html::script(url::common().'/js/zotop.js',array('id'=>'zotop'));
		$html[] = '	'.html::script(url::common().'/js/global.js',array('id'=>'global'));
		foreach($javascript as $js)
		{
		    $html[] = '	'.html::script($js);
		}	
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';
        $html[] = '<div id="wrapper">';
        $html[] = '<div id="page">';
        $html[] = '';
        
		echo implode("\n",$html);	    
	}
	
	public function footer()
	{
	    $html[] = '';
        $html[]	= '</div>';
	    $html[]	= '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop" class="clearfix">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>';
		$html[] = '	<span id="page-title">'.$this->title.'</span>';
		
		
		if( !empty($this->data['position']) )
		{
		$html[] = '	<span id="page-position"><span>当前位置：</span>'.$this->data['position'].'</span>';
		}
		$html[] = '</h2>';
		$html[] = '<h3><a id="favorate" href="'.zotop::url('zotop/favorate/add').'" class="dialog" title="将该页面加入我的收藏夹">加入收藏</a></h3>';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public function bottom($str='')
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		if(!empty($str))
		{
			$html[] = '<div id="bottom" class="clearfix">'.$str.'</div>';
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{#runtime}</b>,memory:<b>{#memory}</b>,includefiles:<b>{#include}</b></div>';
		$html[] = '</div>';

		

		echo implode("\n",$html);
	}

	public function navbar($data='',$current='')
	{
		$html = array();
        if( !is_array($data) )
        {
            $data = $this->data['navbar'];
        }
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
}

class form extends zotop_form
{

}

class side extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'side'));
        $this->addScript('$common/js/zotop.side.js');
        parent::header();
    }
    
	public function navlist($data,$current='')
	{
        $html = array();        
		if(is_array($data))
		{
            $html[] = '';
			$html[] = '	<ul class="list">';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$item['class'].' '.$class.'"><a href="'.$href.'"  id="'.$item['id'].'" target="mainIframe"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[] = '		<li class="'.$item['class'].' '.$class.'">'.$item.'</li>';
				}
			}
			$html[] = '	</ul>';
		}
		return implode("\n",$html);        
	}
	
}

class url extends zotop_url
{
    public static function theme()
    {
        $theme = zotop::config('zotop.theme');
        $theme = empty($theme) ? 'blue' : $theme ;
        return url::application().'/themes/'.$theme;
    }

}

?>