<?php
class page extends zotop_page
{
	public function header()
	{
        $javascript = (array)$this->js;
        $css = (array)$this->css;
        $metas = (array)$this->meta;
        
		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$this->title.' '. zotop::config("zotop.title").'</title>';
        $html[] = '	'.html::meta('keywords',$this->keywords.' '.zotop::config("zotop.keywords"));
        $html[] = '	'.html::meta('description',$this->description.' '.zotop::config("zotop.description"));
        $html[] = '	'.html::meta('Content-Type','text/html;charset=utf-8');
        $html[] = '	'.html::meta('X-UA-Compatible','IE=EmulateIE7');
	    foreach($metas as $meta)
		{
		    $html[] = '	'.html::meta($meta);
		}        
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet(ZOTOP_APP_URL_CSS.'/global.css',array('id'=>'global'));
	    foreach($css as $stylesheet)
		{
		    $html[] = '	'.html::stylesheet($stylesheet);
		}			
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/jquery.js',array('id'=>'jquery'));
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/jquery.plugins.js',array('id'=>'plugins'));
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/zotop.js',array('id'=>'zotop'));
		$html[] = '	'.html::script(ZOTOP_APP_URL_JS.'/global.js',array('id'=>'global'));
		foreach($javascript as $js)
		{
		    $html[] = '	'.html::script($js);
		}	
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(ZOTOP_APP_URL_IMAGE.'/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';
        $html[] = '<div id="wrapper">';
        $html[] = '<div id="page">';
        $html[] = '';
        
		echo implode("\n",$html);	    
	}
	
	public function footer()
	{
	    $html[] = '';
        $html[]	= '</div>';
	    $html[]	= '</div>';
		$html[] = '<div class="clear"></div>';
		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop" class="clearfix">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>';
		$html[] = '	<span id="page-title">'.$this->title.'</span>';
		
		
		if( !empty($this->data['position']) )
		{
		$html[] = '	<span id="page-position"><span>当前位置：</span>'.$this->data['position'].'</span>';
		}
		$html[] = '</h2>';
		$html[] = '<h3><a id="favorate" href="'.zotop::url('zotop/favorate/add').'" class="button dialog" title="将该页面加入我的收藏夹"><span class="button-icon zotop-icon zotop-icon-favorate"></span><span class="button-text">加入收藏</span></a></h3>';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public function bottom($str='')
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		if(!empty($str))
		{
			$html[] = '<div id="bottom" class="clearfix">'.$str.'</div>';
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{#runtime}</b>,memory:<b>{#memory}</b>,includefiles:<b>{#include}</b>,queries:<b>{#queries}</b></div>';
		$html[] = '</div>';

		

		echo implode("\n",$html);
	}

	public function navbar($data='',$current='')
	{
		$html = array();
		
        if( !is_array($data) )
        {
            $data = $this->data['navbar'];
        }
        
		if(is_array($data))
		{
            $current=empty($current) ? $this->data['navbar.current'] : $current;
			$current=empty($current) ? application::action() : $current;
			$current=empty($current) ? $data[0]['id'] : $current;
            $html[] = '';
			$html[] = '<div class="navbar">';
			$html[] = '	<ul>';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id']) ? 'current' : (empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$item['class'].' '.$class.'"><a href="'.$href.'"  id="'.$item['id'].'"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[] = '		<li class="'.$item['class'].' '.$class.'">'.$item.'</li>';
				}
			}
			$html[] = '	</ul>';
			$html[] = '</div>';
		}
		echo implode("\n",$html);
	}
	
	public function mainHeader()
	{
		echo '<div id="main">';
		echo '<div id="main-inner">';
	}

	public function mainFooter()
	{
		echo '</div>';
		echo '</div>';
	}

	public function sideHeader()
	{
		echo '<div id="side">';
		echo '<div id="side-inner">';
	}

	public function sideFooter()
	{
		echo '</div>';
		echo '</div>';	
	}
}
?>