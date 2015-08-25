<?php
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
?>