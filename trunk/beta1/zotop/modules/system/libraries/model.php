<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统模型类，所有的模型都继承自此类
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class model_base
{
    protected $_db = null; // 当前数据库操作对象
    protected $_name = ''; //模型名称
    protected $_table = ''; //数据表名称
    protected $_prefix = ''; //数据表的前缀
    protected $_key = ''; //主键名称
	protected $_field = array(); //表的结构
    protected $_bind = array(); //属性设置
	protected $_error = 0;
	protected $_msg = array();
    
    
	public function __construct()
	{
	    if ( ! is_object($this->_db) )
		{
	       $this->_db  = zotop::db();
		}
		
	}
	
    /**
     * 返回错误
     * 
     */
	public function error($msg=array(), $err=100)
	{
		if ( empty($msg) )
		{
			return $this->_error;
		}

		$this->_error = $err;
		$this->msg($msg);
		return $this->msg();
	}

    /**
     * 返回消息
     * 
     */
	public function msg($msg='')
	{
		if ( !empty($msg) )
		{
			if( is_string($msg) )
			{
				$this->_msg['content'] = $msg;
			}
			else
			{
				$this->_msg = array('title'=>$msg['title'],'content'=>$msg['content'],'description'=>$msg['description']);
			}
		}
		return $this->_msg;
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
        $this->_bind[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_bind[$name]) ? $this->_bind[$name] : null;
    }


    /**
     * 获取或者绑定数据
     * 
     * @param mixed $name 名称/数据数组
	 * @param mixed $value 值
     * @return mixed
     */

	public function bind($name='', $value=null)
	{
		
		if ( $name === null )
		{
			$this->_bind = array();
			return true;
		}

		if ( empty($name) )
		{
			return $this->_bind;
		}

		if ( is_array($name) )
		{
			$this->_bind = array_merge($this->_bind, $name);
		}

		if ( is_string($name) )
		{
			if ( is_null($value) )
			{
				return $this->_bind[$name];
			}
			else
			{
				$this->_bind[$name]  =   $value;
				
			}
		}
		
		return $this->_bind;
	}

    /**
     * 获取当前的db对象
     * 
     * @param string $alias 数据表别名
     * @return mixed
     */
    public function db($alias='')
    {
		if ( is_string($alias) && !empty($alias) )
		{
			return $this->_db->from($this->table().' AS '.$alias);
		}		
		return $this->_db->from($this->table());	
    }

    /**
     * 获取当前的user cookie 数据
     * 
     * @param string $key 字段名称，如：id,username
     * @return mixed
     */
	public function user($key='')
	{
		static $user = array();

		if ( empty($user) )
		{
			$user = zotop::user();
		}

		if ( empty($key) )
		{
			return $user;
		}

		return $user[$key];
	}
    
    /**
     * 得到当前的模块名称
     * 
     * @access public
     * @return string
     */
    public function module()
    {
        if( empty($this->_module) )
        {
            $class = explode('_model_', get_class($this));
            $this->_module =  $class[0]; 
        }
        return $this->_module;
    }    

    /**
     * 得到当前的模型名称
     * 
     * @access public
     * @return string
     */
    public function name()
    {
        if( empty($this->_name) )
        {
            $class = explode('_model_', get_class($this));
            $this->_name =  $class[1]; 
        }
        return $this->_name;
    }
    
    /**
     * 得到当前的数据表的前缀名称
     * @access public
     * @return string
     */
    public function prefix()
    {
        if( empty($this->_prefix) )
        {
            $this->_prefix =  $this->db()->config('prefix');
        }
        return $this->_prefix;
    }
    
    
    /**
     * 得到当前的数据表名称
     * 
     * @access public
     * @return string
     */
    public function table()
    {
        if( empty($this->_table) )
        {
            $this->_table =  $this->name();
        }
        return $this->_table;
    }

    /**
     * 得到当前的数据表的主键名称
     * 
     * @access public
     * @return string
     */
    public function key()
    {
        if( empty($this->_key) )
        {
            $this->_key = $this->db()->table($this->table(true))->primaryKey();
        }
        return $this->_key;
    }

    /**
     * 得到当前的数据表的结构
     * 
     * @access public
     * @return string
     */
	public function field($flush=false)
	{
		$field = $this->_field;

		if( is_array($field) && !empty($field) && !$flush )
		{
			return $field;
		}

		if ( $flush === false )
		{
			$field = zotop::data('table.'.$this->table());

			if( is_array($field) && !empty($field) )
			{
				$this->_field = $field;
				return $field;
			}
		}	

		$field = $this->db()->table($this->table())->fields();		
		
		if( is_array($field) && !empty($field) )
		{
			zotop::data('table.'.$this->table(), $field);

			$this->_field = $field;

			return $field;
		}

		return array();		
	}

	public function globalid($id='')
	{
		$globalid =  empty($id) ? $this->_bind[$this->key()] : $id;

		if ( empty($globalid) )
		{
			$globalid = zotop::cookie('file.globalid');

			if ( empty($globalid) )
			{
				$globalid = TIME.rand(100,10000);

				zotop::cookie('file.globalid',$globalid);

				return md5($globalid);
			}
		}

		$globalid= $this->table().'.'.$globalid;

		return md5($globalid);
	}


	/**
	 * 获取全部数据
	 *
	 */	
    public function getAll($sql='')
    {
        return $this->db()->getAll($sql);
    }
	

	/**
	 * 返回limit限制的数据,用于带分页的查询数据
	 *
	 * @param $page int 页码
	 * @param $pagesize int 每页显示条数
	 * @param $num int|bool 总条数|缓存查询条数，$toal = (false||0) 不缓存查询
	 * @return mixed
	 */
	public function getPage($page=0, $pagesize=30, $num = false)
	{
        
		$page = $page <=0 ? (int)$_GET['page'] :$page;
		$page = $page <=0 ? 1 :$page;

		//获取查询参数
		$sqlBuilder = $this->db()->sqlBuilder($sql);

		if ( is_numeric($num) && $num > 0 )
		{
			$total = $num;
		}
		else
		{
			$hash = md5(serialize($sqlBuilder['where']));

			if ( $page == 1 || $num == true || !is_numeric(zotop::cookie($hash)))
			{
				//获取符合条件数据条数
				$total = $this->count($sqlBuilder['where']);				

				zotop::cookie($hash,$total);				
			}
			else
			{
				$total = zotop::cookie($hash);
			}

		}

		//zotop::dump($this->db()->lastsql());

		//计算$offset
		$offset = intval($page) > 0 ? (intval($page)-1)*intval($pagesize) : 0;
		
		//设置limit
		$this->db()->sqlBuilder($sqlBuilder);
		$this->db()->limit($pagesize, $offset);

		//获取指定条件的数据
		$data = $this->db()->getAll();

		return array(
			'data' => (array) $data,
			'page' => intval($page),
			'pagesize' => intval($pagesize),
			'total' => intval($total),
		);
	}
    
    /**
     * 读取具体的某条数据
     * 
  	 * 空条件：$model->read();前面必须定义过主键值： $model->id = 1; 
     * 唯一编号：$model->read(1) 
     * 
     * @param mix $where 键值
     * 
     * @return array
     */
    public function read($id='')
    {
        if ( !is_array($where) )
        {
            $key = $this->key();
            $value = empty($id) ? $this->$key : $id;
            $where = array($key, '=', $value);
        }
               
        //读取并设置属性
        $this->_bind = $this->db()->select('*')->where($where)->getRow();

		$this->_bind = zotop::filter($this->table().'.read', $this->_bind, $id);

        if ( empty($this->_bind) )
        {
			$this->error(array(
                'title' => zotop::t('读取数据失败'),
                'content' => zotop::t('未能找到 <b>{$0}</b> <b>{$1}</b> <b>{$2}</b> 的数据', $where),
                'detail' => $this->db()-> lastSql()
            ));
        }
        return $this->_bind;
    }

	public function _check_data($data)
	{
		$fields = $this->field();
		
		$keys = array_keys($fields);

		foreach( $data as $key=>$field )
		{
			if ( !in_array(strtolower($key), $keys) )
			{
				unset($data[$key]);
			}
		}

		return $data;		
	}
    
    /**
     * 创建数据
     * 
     * @param mix $data 待创建的数据
     * 
     * @return mix
     */     
    public function insert($data=array())
    {
        $data = empty($data) ? $this->bind() : $data;		
		$data = zotop::filter($this->table().'.insert',$data);
		$data = $this->_check_data($data);

		if ( is_array($data) && !empty($data) && !$this->error() )
		{
			$result = $this->db()->set($data)->insert();
		}

		if ( $result !== false )
		{
			$insertID = $this->db()->insertID();
			$insertID = ($insertID === 0) ? $data[$this->key()] : $insertID;
			$this->bind($this->key(), $insertID);
			return $insertID;
		}
		unset($data);		
		return $insert;       
    }
	
    
    /**
     * 更新数据
     * 
     * @param mix $data 待更新的数据
     * @param mix $where 条件
     * 
     * @return array
     */    
    public function update($data=array(), $where = '')
    {
		$data = empty($data) ? $this->bind() : $data;
		$data = zotop::filter($this->table().'.update', $data, $where);
		$data = $this->_check_data($data);

        if ( !is_array($where) )
        {
            $key = $this->key();
            
            if ( empty($where) )
            {
              
                if( isset($data[$key]) )
                {
                    $where = array($key,'=',$data[$key]);
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
        }
		
		if ( is_array($data) && !empty($data) && !$this->error() )
		{
			$update = $this->db()->set($data)->where($where)->update();
        }

		unset($data);
        return $update;
    }
      
    /**
     * 判断是否存在
     * 
     * 
     * @param mix $key 键
     * @param mix $value 键值     * 
     * 
     * @return array
     */    
    public function isExist($key='', $op='', $value='')
    {     
        $count = $this->count($key,$op,$value);
        
        if( is_numeric($count) && $count > 0 )
        {
            return true;
        }
        return false;
    }

    /**
     * 删除数据
     * 
     * @param mix $where 删除条件
     * 
     * @return mix
     */     
    public function delete($where=array())
    {        
        if( empty($where) || !is_array($where) )
        {
            $key = $this->key();
            
            if( empty($where) )
            {
               $where = array($key,'=',$this->$key); 
            }
            
            if( is_numeric($where) || is_string($where) )
            {
               $where = array($key,'=',$where);
            }            
        }

		zotop::run($this->table().'.delete', $where);
            
        return $this->db()->where($where)->delete();     
    }

	public function count($key='', $op='', $value='')
	{
	    
		if ( !is_array($key) )
        {
		    $where = func_get_args();
			
			switch ( count(array_filter($where)) )
			{
				case 0:
					$k = $this->key();
					$where = array($k,'=',$this->$k); 
					break;				
				case 1:
					$where = array($this->key(),'=',$key); 
					break;					
				case 2:
					$where = array($where[0],'=',$where[1]); //where('id',1)  => array('id','=',1)
					break;
				case 3:
					$where = array($where[0],$where[1],$where[2]); //where('id','<',1)  => array('id','<',1)
					break;
			}

        }
        elseif ( is_array($key) )
        {
            $where = $key;
        }

	
		$count = $this->db()->select('count('.$this->key().') as num')->where($where)->getOne();
		
		if( !is_numeric($count) )
		{
			$count = 0;
		}
		return $count;
	}

    public function max($key='', $where='', $default=0)
    {
        $key = empty($key) ? $this->key() : $key;
        
        $max = $this->db()->select('max('.$key.') as max')->where($where)->getOne();
        
        if ( is_numeric($max) )
        {
            return $max;
        }
        return $default;
    }

	public function cache($reload=false)
	{
		$name = $this->table();
		
		$data = zotop::data($name);
		
		//设置缓存数据
		if ( $reload || $data===null )
		{
			$data = $this->getAll($sql);
			
    		if( is_array($data) )
    		{
    		    zotop::data($name, $data);
    		}
		}
		
		return $data;	
	}

}