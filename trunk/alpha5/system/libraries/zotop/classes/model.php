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
?>