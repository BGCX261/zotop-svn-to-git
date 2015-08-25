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
?>