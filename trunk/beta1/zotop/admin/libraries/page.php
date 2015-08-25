<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 页面类
 *
 * @package		zotop
 * @class		page
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class page extends page_base
{
	public function header()
	{
        $javascript = (array)$this->js;
        $css = (array)$this->css;
        $metas = (array)$this->meta;
        
		$html[] = '<!DOCTYPE html>';
		$html[] = '<html>';
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
		$html[] = '	'.html::stylesheet('$theme/css/zotop.css',array('id'=>'zotop'));
		$html[] = '	'.html::stylesheet('$theme/css/global.css',array('id'=>'global'));
	    foreach($css as $stylesheet)
		{
		    $html[] = '	'.html::stylesheet($stylesheet);
		}			
		$html[] = '	'.html::script('$common/js/jquery.js',array('id'=>'jquery'));
		$html[] = '	'.html::script('$common/js/jquery.plugins.js',array('id'=>'plugins'));
		$html[] = '	'.html::script('$common/js/zotop.js',array('id'=>'zotop'));
		$html[] = '	'.html::script('$common/js/global.js',array('id'=>'global'));
		foreach($javascript as $js)
		{
		    $html[] = '	'.html::script($js);
		}	
		$html[] = '	'.html::link('$theme/image/favicon.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link('$theme/image/favicon.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link('$theme/image/favicon.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($this->body).'>';
        $html[] = '<div id="wrapper">';
        $html[] = '<div id="container" class="clearfix">';
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
	    $html[] = '<div id="header" class="clearfix">';
		$html[] = '	<div id="page-title">'.$this->title.'</div>';
		$html[] = '	<div id="page-description">'.$this->position().'</div>';
		$html[] = '</div>';
		$html[] = '<div id="topbar">';
		$html[] = '	<a id="favorate" href="'.zotop::url('zotop/favorate/add').'" class="dialog" title="'.zotop::t('将该页面加入我的收藏夹').'"><span class="zotop-icon zotop-icon-favorate"></span><span>'.zotop::t('加入收藏').'</span></a>';
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
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{#runtime}</b>,memory:<b>{#memory}</b>,includefiles:<b>{#include}</b>,queries:<b>{#queries}</b>,caches:<b>{#caches}</b></div>';

		

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
			foreach($data as $key=>$item)
			{
				if(is_array($item))
				{
					$class=($current==$key || $current==$item['id']) ? 'current' : (empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$class.'"><a href="'.$href.'"  id="'.$item['id'].'" class="'.$item['class'].'"><span>'.$item['title'].'</span></a></li>';
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

	public function position()
	{
		$position = '';

		if( is_string($this->position) )
		{
			$position = $this->position;
		}
		
		if ( is_array($this->position) )
		{
			$pos = array();

			foreach($this->position as $url=>$text)
			{
				if( is_string($url) )
				{
					$pos[] = '<a href="'.$url.'">' . $text .'</a>';
				}

				if( is_numeric($url) || empty($url) )
				{
					$pos[] =  $text;
				}
			}
			$position = implode(' <cite>></cite> ',$pos);			
		}
		
		return empty($position) ? '' : '<span class="position">现在位置：</span>'.$position;
		return empty($position) ? $this->title : $position.' <cite>></cite> '.$this->title;
	}
}
?>