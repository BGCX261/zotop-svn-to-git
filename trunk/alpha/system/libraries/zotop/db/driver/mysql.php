<?php
class Database_Mysql extends database implements DatabaseInterface
{


    public function __construct($config = array())
    {
         $default = array(
			 'driver'=>'mysql',
			 'username'=>'root',
			 'password'=>'',
			 'hostname'=>'localhost',
			 'hostport'=>'3306',
			 'database'=>'',
			 'charset'=>'utf8',
			 'pconnect'=>false
			);
		 $this->config = array_merge( $default , $this->config , $config);
    }

    public function connect($test = false)
    {
        if(is_resource($this->link))
        {
            return $this->link;
        }
        $connect = ( $this->config['pconnect'] == TRUE ) ? 'mysql_pconnect' : 'mysql_connect';

        $host = $this->config['hostname'];
        $host = empty($this->config['hostport']) ? $host : $host.':'.$this->config['hostport'];
        $user = $this->config['username'];
        $pass = $this->config['password'];
        $database = $this->config['database'];

        if($this->link = @$connect($host,$user,$pass,true))
        {

			if(@mysql_select_db($database , $this->link))
            {

				$version = $this->version();
				if ($version >= '4.1' && $charset = $this->config['charset'] )
    			{
					mysql_query("SET NAMES '".$charset."'" , $this->link);//使用UTF8存取数据库 需要mysql 4.1.0以上支持
    			}
				if($version >'5.0.1'){
					mysql_query("SET sql_mode=''",$this->link);//设置 sql_model
				}
            }
            else
            {
			   if($test){return -2;}
			   zotop::error(-2,'Could not connect to database');
            }
        }
        else
        {
			if($test){return -1;}
			zotop::halt(-1,'Could not connect to database server ('.$this->config['hostname'].')');
        }
        return false;
    }

	public function free()
	{
		@mysql_free_result($this->query);
        $this->query = 0;
	}

	public function query($sql,$silent=false)
    {

        if( !is_resource($this->link) )
        {
			$this->connect();
        }
		if($this->error == 0 && $this->escape($sql))
		{
			//echo $this->sql;
			//释放前次的查询结果
			if ( $this->query ) {$this->free();}
			//查询数据
			$this->query = @mysql_query($this->sql , $this->link );
			if(!$this->query)
			{
				if($silent) return false;
				zotop::error(-1,mysql_errno($this->link),mysql_error( $this->link  )." SQL : $this->sql");
			}
			return $this->query;
		}
		return false;
    }

	public function fetch($sql,$type='')
	{
		if($query = $this->query($sql))
		{
			$result = array();
			$this->numRows = mysql_num_rows($query);
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

	public function execute($sql)
	{
		if($result = $this->query($sql))
		{
			if($result === false)
			{
				return false;
			}
			$this->numRows = mysql_affected_rows($this->link);
			return $this->numRows;
		}
		return false;
	}

	public function create()
	{

		$database = $this->config['database'];
		$charset = $this->config['charset'];
		if($create = $this->query("CREATE DATABASE IF NOT EXISTS ".$database." DEFAULT CHARACTER SET ".$charset."",$link))
		{
			if(!$create)
			{
				$this->error = -20;//创建数据库失败
				$this->description = 'ERROR : Could not connect to database server ('.$this->config['hostname'].')';
				$this->halt();
				return false;
			}
		}
	}


	public function table($tablename='')
	{
		$table = new DataBase_MySql_Table(&$this , $tablename );
		$table -> prefix = $this->config['prefix'];
		return $table;
	}

	public function escape($sql)
	{
		if( $this->sql = $sql )
		{
			return mysql_escape_string($sql);
		}
		return false;
	}


    public function insertId()
    {
        return mysql_insert_id($this->link);
    }

    /**
	 *
	 *
     * @return number || string 返回当前数据库的版本号
     */
    public function version($complete = false)
    {

        if( !is_resource($this->link) )
        {
			$this->connect();
        }
		$version = mysql_get_server_info($this->link);
		if(!$complete)
		{
			$v = explode('.' , $version);
			$version = $v[0].'.'.$v[1].'.'.(int)$v[2];
		}
		return $version;
    }

	public function size()
	{
		$tables = $this->table()->get(true);
		foreach($tables as $table)
		{
			$size  +=  $table['Data_length'] + $table['Index_length'];
		}
		return format::size($size);
	}

}

//mysql对应的表操作
class DataBase_MySql_Table
{
	public $db = null; //表隶属于的db
	public $name = ''; //表的名称
	public $prefix = ''; //表的前缀名称prefix

	public function __construct(&$db , $name='' , $prefix='')
	{
		$this->db = $db;
		$this->name = empty($name) ? $this->name : $name ;
		$this->prefix = empty($prefix) ? $this->prefix : $prefix ;
	}

	public function get($status = false)
	{
		if($status)
		{
			return $this->db->fetch('SHOW TABLE STATUS');
		}
		$tables = array();
		$result = $this->db->fetch('SHOW TABLES');
		if($result)
		{
			foreach($result as $key=>$value)
			{
				$tables[$key] = current($value);
			}
		}
		return $tables;
	}

	//表名称以{tablename}包围将被自动加上前缀
	public function name($tablename='')
	{
		if(empty($tablename)){$tablename = $this->name;}
		if($tablename)
		{
			$tablename = str_replace( array('{','}') , array($this->prefix,'') , $tablename );
		}
		return $tablename;
	}

	public function exist()
	{
		if($tablename = $this->name())
		{
			$tables = $this->get();
			if(array_search($tablename , $tables))
			{
				return true;
			}
		}
		return false;
	}

    public function isValidName($tablename)
    {
        if ($tablename !== trim($tablename))
		{
            return false;
        }
        if (! strlen($tablename))
		{
            return false;// zero length

        }
        if (preg_match('/[.\/\\\\]+/i', $tablename))
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
			if( $this->exist())
			{
				zotop::error(-1,'数据表已经存在',zotop::t('您视图创建的数据表<b>{$tablename}</b>已经存在',array('{$tablename}'=>$tablename)));
				if($overwirte)
				{
					$this->drop();
				}
				else
				{
					zotop::error(-1,'数据表已经存在',zotop::t('您视图创建的数据表<b>{$tablename}</b>已经存在',array('{$tablename}'=>$tablename)));
				}
				return false;
			}
			elseif( false !== $this->db->execute('CREATE TABLE `'.$tablename.'`( id VARCHAR( 32 ) NOT NULL)ENGINE = MYISAM ;') )
			{
				return true;
			}
		}
		return false;
	}

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

	public function alter()
	{}

	public function rename($newname)
	{
		$newname = str_replace( array('{','}') , array($this->prefix,'') , $newname );

		if( $this->isValidName($newname) && $tablename = $this->name())
		{
			$tables = $this->get();
			if( !array_search($newname , $tables) )
			{

				if( false !== $this->db->execute('RENAME TABLE `'.$tablename.'` TO `'.$newname.'`;') )
				{
					return true;
				}
			}
		}
		return false;
	}

	public function field($fieldname='')
	{}
}
//mysql对应的字段操作
class DataBase_MySql_Table_Field
{

}
?>