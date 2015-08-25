<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 页面组件
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_page
{
    public $data = array();
    
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        
    }    
    
	/**
	 * 为页面生成一个全局编号，用于media等数据定位
	 *
	 */
	public function getUid()
	{
	    if( empty($this->uid) )
	    {
    	    $this->uid = application::getApplication().'://'.application::getModule().'.'.application::getController().'.'.application::getAction();	    
    	    $this->uid = empty($id) ? $namespace : $namespace.'/'.$id;
    	    $this->uid = md5($namespace);
    	    
	    }	    
	    return $this->uid;
	}
	
	public function getTemplatePath($action='')
	{
	    if( empty($this->template) )
	    {
            if(empty($action))
            {
                $action = application::getAction();
            }
            $module = application::getModule();
            $controller = application::getController(); 
            $path = zotop::module($module,'path');
            $path = $path.DS.router::application().DS.'template'.DS.$controller.DS.$action.'.php';
            return $path;   	        
	    }
	    return $this->template;   
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

	public function set($name, $value=null)
	{
        if( is_array($name) )
        {
            $this->data = array_merge($this->data,$name);
        }
        
        if( is_string($name) )
        {
            $this->data[$name] = $value;
        }
	}
	
	public function data()
	{
	    return $this->data;
	}

	
	public function header()
	{

		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$this->title.' '. zotop::config("zotop.title").'</title>';
        $html[] = '	'.html::meta('keywords',$this->keywords);
        $html[] = '	'.html::meta('description',$this->description);
        $html[] = '	'.html::meta('Content-Type','text/html;charset=utf-8');
        $html[] = '	'.html::meta('X-UA-Compatible','IE=EmulateIE7');
		$html[] = '	'.html::stylesheet(url::theme().'/css/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(url::theme().'/css/global.css',array('id'=>'global'));		
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));        
        
		//$html[] = page::meta($header['meta']);
		//$html[] = page::stylesheet($header['css']);
		//$html[] = page::script($header['js']);
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';

		$str =  implode("\n",$html);

		echo $str;	    
	}
	
	public function footer()
	{
	    $html[] = '';

		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}	
	
	public function add($str)
	{
		echo $str."\n";
	}

	/**
	 * 给页面附加一个js文件，必须在header之前声明
	 *
	 */
	public function addScript($file)
	{
	    $this->js = array_merge((array)$this->js,(array)$file);
	}
	public function addJS($file)
	{
	    $this->addScript($file);
	}	
	
	/**
	 * 给页面附加一个css文件，必须在header之前声明
	 * 
	 */
	public function addStyleSheet($file)
	{    
	    $this->css = array_merge((array)$this->css,(array)$file);
	}
	public function addCSS($file)
	{
	    $this->addStyleSheet($file);
	}

	public function render($file='')
	{
	    if( !empty($file) )
	    {
	        $this->template = $file;
	    }
	    
	    $this->template = $this->getTemplatePath();

	    if( file_exists($this->template) )
        {
            ob_start();            
            extract($this->data(), EXTR_SKIP);
            include $this->template;
    		$content = ob_get_contents();
    		ob_clean();
    		return $content;
        }
        msg::error(array(
            'title'=>'404 error',
            'content'=>zotop::t('<h2>未能找到页面模板，请检查确认模板文件是否存在</h2> 模板文件：{$file}',array('file'=>$this->template)),
        ));	    
	}
	
	public function display($file='')
	{
        static $display = false;
        if($display) return true;
	    echo $this->render($file);
	    $display = true;
	}
}
?>