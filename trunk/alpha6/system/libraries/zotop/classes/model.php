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
class zotop_model
{
    protected $_db = null; // 当前数据库操作对象
    protected $_name = ''; //模型名称
    protected $_table = ''; //数据表名称
    protected $_prefix = ''; //数据表的前缀
    protected $_key = ''; //主键名称
    protected $_bind = array(); //属性设置
	protected $_user = array();
	protected $_error = 0;
	protected $_msg = array();
    
    
	public function __construct()
	{
	    if ( ! is_object($this->_db) )
		{
	       $this->_db  = zotop::db();
		}
		
		$this->_user = zotop::user();
	}
	
    /**
     * 返回错误
     * 
     */
	public function error($err=0, $msg=array())
	{
		if ( $err )
		{
			$this->_error = $err;
			$this->msg($msg);
		}
		return $this->_error;
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

	public function bind($name='', $value='')
	{
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
			$this->_bind[$name]  =   $value;
		}

		return $this->_bind;
	}

    /**
     * 获取当前的db对象
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function db()
    {
        return $this->_db->from($this->table());
    }

	public function user($key='')
	{
		if( empty($key) )
		{
			return $this->_user;
		}
		return $this->_user[$key];
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
    public function table($fullName = false)
    {
        if( empty($this->_table) )
        {
            $this->_table =  $this->name();
        }
        if( $fullName )
        {
            return $this->prefix().$this->_table;
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
            $table = $this->table(true);
            $structure = $this->structure();
            
            if( $structure )
            {
                $this->_key = $structure['primarykey'];
            }
            if( empty($this->_key) )
            {
               $this->_key = $this->db()->primaryKey();
            }
        }
        return $this->_key;
    }

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
     * 默认条件：$model->read(1) 相当于 $model->read(array('id','=',1))
     * 自定义条件：$model->read(array('id','=',1))
     * 
     * @param mix $where 键值
     * 
     * @return array
     */
    public function read($where='')
    {
        if ( !is_array($where) )
        {
            $key = $this->key();
            $value = empty($where) ? $this->$key : $where;
            $where = array($key, '=', $value);
        }
               
        //读取并设置属性
        $this->_bind = $this->db()->select('*')->where($where)->getRow();

		$this->_bind = zotop::filter($this->table().'.read', $this->_bind, $where);

        if ( empty($this->_bind) )
        {
			$this->error(1, array(
                'title' => zotop::t('读取数据失败'),
                'content' => zotop::t('未能找到 <b>{$0}</b> <b>{$1}</b> <b>{$2}</b> 的数据', $where),
                'detail' => $this->db()-> lastSql()
            ));
        }
        return $this->_bind;
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
        
		$data = zotop::filter($this->table().'.insert',$data);

		$insert = $this->db()->set($data)->insert();

        if ( $insert )
        {
            //返回插入数据的主键？
            return true;
        }
        return false;             
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

		
		if ( is_array($data) )
        {
            $this->db()->set($data);
        }
        
        if ( !is_array($where) )
        {
            $key = $this->key();
            
            if ( empty($where) )
            {
                $set = $this->db()->sqlBuilder('set');
                
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
        }
        
		$data = zotop::filter($this->table().'.update', $data, $where);

        $update = $this->db()->where($where)->update();
        
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
    public function isExist($key='', $value='')
    {
        if ( !is_array($key)  )
        {
            $key = empty($key) ? $this->key() : $key;
            $value = empty($value) ? $this->$key : $value;
            $where = array($key,'=',$value);
        }
        else
        {
            $where = $key;
        }
        
        $count = $this->db()->select('count('.$key.') as num')->where($where)->getOne();
        
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
    public function delete($where)
    {

        
        if( !is_array($where) )
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

		$data = zotop::filter($this->table().'.delete', $data);
            
        return $this->db()->where($where)->delete();     
    }

	public function count($key='',$value='')
	{
	    
		if ( is_string($key) )
        {
            $key = empty($key) ? $this->key() : $key;

            $value = empty($value) ? $this->$key : $value;
			
			if( !empty($value) )
			{
				$where = array($key,'=',$value);
			}
        }
        elseif ( is_array($key) )
        {
            $where = $key;
        }
		
		$count = $this->db()->select('count('.$key.') as num')->where($where)->getOne();
		
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