<?php
class BaseUser
{
    /**
     * 返回用户是否登录，暂时写成静态函数，改功能尚未设计完成
     *
     * @return bool
     */
    public static function isLogin()
    {
        return false;
    }

    /**
     * 存储和读取cookie，暂时留着，该功能待设计，还未想清楚如何把实例和用户功能结合
     *
     * @param $data
     * @return unknown_type
     */
    public static function cookie($data=array())
    {

    }
    
    
    /**
     * 设置登陆状态
     * 
     * @param array $data
     * @return bool
     */
    public static function setLogin(array $data = array()){
       return false;  
    }
    
    
}
?>