<?php
class BaseMsg
{
    public static function show($msg=array())
    {
        //暂时这样写，应该是当ajax时，输出json数据，普通模式输出html
        exit($msg['content']); 
    }
    
    public static function error($title,$content='',$lifetime=0)
    {
        $msg = array();
        $msg['type'] = 'error';
        $msg['title'] = empty($content) ? 'error' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['lifetime'] = $lifetime;        
        msg::show($msg);
    }
    
    public static function success($title,$content='',$url='',$lifetime=3)
    {
        $msg = array();
        $msg['type'] = 'success';
        $msg['title'] = empty($content) ? 'success' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['url'] = $url;
        $msg['lifetime'] = $lifetime;        
        msg::show($msg);
    }
    public static function alert($title,$content='',$lifetime=0)
    {
        $msg = array();
        $msg['type'] = 'alert';
        $msg['title'] = empty($content) ? 'alert' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['lifetime'] = $lifetime;        
        msg::show($msg);
    }

    public static function template()
    {
    }
}
?>