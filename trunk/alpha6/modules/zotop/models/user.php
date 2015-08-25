<?php
class zotop_model_user extends model
{
	protected $_key = 'id';
	protected $_table = 'user';

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
	
	public function isValidUsername($username)
	{
	    if( empty($username) )
	    {
	         return false;
	    }
	    //首先检查是否含有特殊字符
	    $badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	    foreach($badwords as $badword)
	    {
	        if( strpos($username, $badword) !== false)
			{
				return false;
			}
	    }
	    //检查用户名是否有效
	    zotop::run('zotop.user.username.valid',$username);
	    return true;
	}
	
	public function isValidPassword($password)
	{
	    if( empty($password) )
	    {
	        return false;
	    }
	    return true;
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
	        'logintime' => TIME,
	    	'loginnum' => array('loginnum','+',1),
	        'loginip'=> ip::current()
	    ));
	    return $result;    
	}
	
	/**
	 * 写入登陆信息
	 *
	 */	
	public function login($data=array())
	{
        $data = array_merge($this->bind(), (array)$data);
        
        $data = zotop::filter('zotop.user.login',$data);

        //刷新信息
        $this->refresh();
        
        //记录用户数据        
        zotop::user($data);
		zotop::log('login',zotop::t('用户 <b>{$username}</b> 于 {$time} 登陆成功',array('username'=>$data['username'],'time'=>TIME)));
               
	    return true;
	}
	/**
	 * 清除登陆信息
	 *
	 */		
	public function logout()
	{
        //记录用户数据
        zotop::user(null);
	    return true;	    
	}

	public function countByGroupid($groupid=0 )
	{
		return $this->count('groupid',$groupid);
	}
	
	
	
	
	
	
}
?>