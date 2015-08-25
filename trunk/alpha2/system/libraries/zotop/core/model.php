<?php
class zotop_model
{
	// 模型名称
    protected $name = '';
	// 当前数据库操作对象
	protected $db = null;
	// 主键名称
	protected $primaryKey  = '';
	// 数据表前缀
	protected $prefix  = '';
	// 数据表名称(不包含前缀)
	protected $table = '';

	public function __construct()
	{
		$this->db = zotop::db();
	}

	public function read($value,$key='')
	{
		if( empty($key) ) $key = $this->getPrimaryKey();
		/**/
		$sql = array(
		    'select'=>'*',
		    'from'=>$this->getTableName(),
		    'where'=>array($key,'=',$value),
		    'limit'=>1
		);
		//$sql = "SELECT * FROM {$this->getTableName()} WHERE {$key}= '{$value}'";
		$read = $this->db->getRow($sql);
		zotop::dump($this->db->lastSql());
		return $read;
	}

    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
	public function getModelName()
	{
        if(empty($this->name)) {
            $this->name =   substr(get_class($this),0,-5);
        }
        return $this->name;
	}

	public function getTableName($status = false)
	{
		if( empty($this->table) )
		{
			$this->table = $this->getModelName();
		}
		if( $status )
		{
			return $this->db->table('#'.$this->table)->name();
		}
		return $this->table;
	}

	public function getPrimaryKey()
	{
		if( empty($this->primaryKey) ) {
			$tableName = $this->getTableName();
			$this->primaryKey = $this->db->table($tableName)->primary();
        }
		return $this->primaryKey;
	}

}
?>