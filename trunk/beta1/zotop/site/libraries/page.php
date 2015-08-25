<?php
class page extends page_base
{
	public function header()
	{
		$header = $this->render('header');
		
		if( !$header )
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
			$html[] = '<div id="page">';
			$html[] = '';
			
			$header =  implode("\n",$html);
		}

		echo $header;

	}
	
	public function footer()
	{
		$footer = $this->render('footer');

		if( !$footer )
		{
			$html[] = '';
			$html[]	= '</div><!--page-->';
			$html[]	= '</div><!--wrapper-->';
			$html[] = '</body>';
			$html[] = '</html>';

			$footer = implode("\n",$html);
		}

		echo $footer;
	}

	public function top()
	{
		$top = $this->render('top');

		if( !$top )
		{
			$html[] = '';
			$html[] = '<div id="header"><span id="page-title">'.$this->title.'</span></div>';		
			$html[] = '<div id="body" class="clearfix">';
			$top = implode("\n",$html);
		}

		echo $top;		
	}

	public function bottom()
	{
		$bottom = $this->render('bottom');

		if( !$bottom )
		{
			$html[] = '';
			$html[] = '</div>';
			$html[] = '<div id="footer">';
			$html[] = '	<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{#runtime}</b>,memory:<b>{#memory}</b>,includefiles:<b>{#include}</b>,queries:<b>{#queries}</b></div>';
			$html[] = '</div>';

			$bottom = implode("\n",$html);
		}

		echo $bottom;
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
	
}
?>