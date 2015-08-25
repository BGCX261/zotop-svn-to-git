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
    public $uid ='';
	public $template = '';
	public $data = array();
    
    /**
     * 初始化控制器
     * 
     */
    public function __construct()
    {
        
    }    
    
	
	public function getTemplatePath($action='')
	{
	    if( empty($this->template) )
	    {
            $path = application::template($action);
            
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
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/global.css',array('id'=>'global'));		
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));        
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

	public function render($action='')
	{
	    $this->template = $this->getTemplatePath($action);

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
            'title' => '404 error',
            'content' => zotop::t('未能找到页面模板，请检查确认模板文件是否存在'),
            'detail' => zotop::t('模板文件：{$file}',array('file'=>$this->template)) 
        ));	    
	}
	
	public function display($action='')
	{
        static $display = false;
        
        if($display) return true;
        
	    echo $this->render($action);
	    
	    $display = true;
	}
}
?>