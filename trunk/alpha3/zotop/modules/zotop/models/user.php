<?php
class user_model extends model
{
	protected $primaryKey = 'id';
	protected $tableName = 'user';

	/**
	 * 对密码进行加密，返回加密后的密码
	 *
	 * @param string $str 原密码
	 * @return string 加密后的密码 
	 */
	public function password($str)
	{
	    $password = $str;
	    //$password = md5($password);
	    return $password;
	}
	
	
	/**
	 * 刷新用户状态，包括登录时间以及登录次数等
	 *
	 */
	public function refresh($id = '')
	{
	    $id = empty($id) ? $this->id : $id;
	    if( empty($id) ) return false;
	    
	    $result = $this->update(array(
	        'id' => (int)$id,
	        'logintime' => time::current(),
	    	'loginnum' => 'loginnum + 1',
	        'loginip'=>ip::current()
	    ));
	    return $result;    
	}
	
	
}
?>