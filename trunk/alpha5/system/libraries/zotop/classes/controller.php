<?php
class zotop_controller
{
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        $this->__init();
        $this->__check();
    }
    /**
     * 初始化动作，当控制器被初始化的时候调用
     *
     */
    public function __init()
    {
        
    }
    /**
     * 初始化权限检查，当控制器被初始化的时候调用
     *
     */
    public function __check()
    {
        
    }        
    /**
     * 动作触发之前调用
     *
     */
    public function __before($arguments='')
    {
        zotop::dump($arguments);
    }
    
    /**
     * 当动作被触发之后调用
     *
     */
    public function __after($arguments='')
    {
        
    }
        
    /**
     * 空动作，当找不到对应动作时候触发，可以被重载 
     *
     */
    public function __empty($method='',$arguments='')
    {
        msg::error(array(
        	'title'=>'404 error',
            'content'=>zotop::t('<h2>未能找到相应的动作，请检查控制器中动作是否存在？</h2>控制器文件：{$file}<br>动作名称：{$method}',array('file'=>application::getControllerPath(),'method'=>$method))
        ));
    }
    
    public function redirect($uri , $params=array() , $fragment='')
    {
        $url = zotop::url($uri,$params,$fragment);
        header("Location: ".$url);
        exit();
    }
    
    public function error($content='', $life=9)
    {
        msg::error($content, $life=9);
    }
    
    public function success($content='', $url='', $life=5, $extra='')
    {
        msg::success($content, $url, $life, $extra);
    }

}
?>