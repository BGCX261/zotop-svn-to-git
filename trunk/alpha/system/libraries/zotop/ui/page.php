<?php
class BasePage extends Base
{
	public static function header($header=array())
	{
		$header = self::settings($header);

		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$header['title'].' '. zotop::config("zotop.title").'</title>';
        $html[] = self::meta($header['meta']);
		$html[] = self::stylesheet($header['css']);
		$html[] = '	'.html::style($header['style']);
		$html[] = self::script($header['js']);
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($header['body']).'>';

		$str =  implode("\n",$html);

		echo $str;
	}

	public static function footer()
	{
	    $html[] = '';

		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public static function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop">';
		$html[] = '<div id="page">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>'.page::settings('title').'</h2>';
		$html[] = '</div>';
		$html[] = '<div id="body" class="clearfix">';

		echo implode("\n",$html);
	}

	public static function bottom($str='')
	{
	    $html[] = '';
	    $html[] = '</div>';
	    $html[] = '<div id="footer">';
		if(!empty($str))
		{
			$html[] = $str;
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{$runtime}</b>,memory:<b>{$memory}</b>,includefiles:<b>{$include}</b></div>';
	    $html[] = '</div>';
		$html[] = '</div>';

		echo implode("\n",$html);
	}

	public static function navbar($data,$current='')
	{
		$html = array();

		if(is_array($data))
		{

			$current=empty($current) ? router::method(false) : $current;
			$current=empty($current) ? $data[0]['id'] : $current;

			$html[]='<div class="navbar">';
			$html[]='	<ul>';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
					$html[]='		<li class="'.$class.'"><a href="'.$item['href'].'"  id="'.$item['id'].'" class="'.$item['class'].'">'.$item['title'].'</a></li>';
				}
				else
				{
					$html[]='		<li class="'.$class.'">'.$item.'</li>';
				}
			}
			$html[]='	</ul>';
			$html[]='</div>';
		}
		echo implode("\n",$html);
	}

	public static function settings($name='')
	{
		static $settings = array();

		if(empty($name)) return $settings;
		if(is_array($name))
		{
			$settings = array_merge($settings,array_change_key_case($name));
			return $settings;
		}
		return $settings[strtolower($name)];
	}

	public static function meta($metas)
	{
		//默认的meta
	    $default = array('keywords'=>'zotop cms','description'=>'simple,beautiful','Content-Type'=>'text/html;charset=utf-8','X-UA-Compatible'=>'IE=EmulateIE7');
		//用户的meta
		$metas = array_merge($default,(array)$metas);
		foreach($metas as $name=>$value)
		{
		    $html[]	= '	'.html::meta($name,$value).'';
		}
		return implode("\n",$html);
	}

	public static function stylesheet($files)
	{
	    $default['zotop'] = url::theme().'/css/zotop.css';
	    $default['global'] = url::theme().'/css/global.css';
		//用户的meta
		$files = array_merge($default,(array)$files);
		foreach($files as $file)
		{
		    $html[]	= '	'.html::stylesheet($file).'';
		}
		return implode("\n",$html);
	}

	public static function script($files)
	{
	    $default['jquery'] = url::theme().'/js/jquery.js';
	    $default['plugins'] = url::theme().'/js/jquery.plugins.js';
	    $default['zotop'] = url::theme().'/js/zotop.js';
		//用户的meta
		$files = array_merge($default,(array)$files);
		foreach($files as $file)
		{
		    $html[]	= '	'.html::script($file);
		}
		return implode("\n",$html);
	}

	public static function add($str)
	{
		echo $str."\n";
	}


}
?>