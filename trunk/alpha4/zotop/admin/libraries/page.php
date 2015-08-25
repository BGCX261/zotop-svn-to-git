<?php
class page extends zotop_page
{

	public static function header($page=array())
	{

	    $page['js']= array_merge(array(
	              'jquery'=> url::common().'/js/jquery.js',
				  'plugins'=> url::common().'/js/jquery.plugins.js',
				  'zotop'=> url::common().'/js/zotop.js',
	    ),(array)$page['js']);

	    $page['css']= array_merge(array(
	              'zotop'=> url::theme().'/css/zotop.css',
				  'global'=> url::theme().'/css/global.css'
	    ),(array)$page['css']);
	    
	    $page = self::settings($page);

		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = '	<title>'.$page['title'].' Powered by '. zotop::config("zotop.title").'</title>';
        $html[] = self::meta($page['meta']);
		$html[] = self::stylesheet($page['css']);
		$html[] = self::script($page['js']);
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'shortcut icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'icon','type'=>'image/x-icon'));
		$html[] = '	'.html::link(url::theme().'/image/fav.ico',array('rel'=>'bookmark','type'=>'image/x-icon'));
		$html[] = '	<script type="text/javascript">';
		$html[] = '		zotop.url.base = "'.url::base().'";';
		$html[] = '		zotop.url.common = "'.url::common().'";';
		$html[] = '		zotop.page.id = "'.page::id().'";';
		$html[] = '		zotop.user.id =0;';
		$html[] = '		zotop.user.username = "";';
		$html[] = '	</script>';
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($page['body']).'>';
		$html[] = '<div id="wrapper">';
		$html[] = '';
		$str =  implode("\n",$html);

		echo $str;
	}

	public static function footer()
	{
	    $html[] = '';
		$html[]	= '</div>';
		$html[] = '</body>';
		$html[] = '</html>';

		echo implode("\n",$html);
	}

	public static function top()
	{
	    $html[] = '';
		$html[] = '<div id="zotop" class="clearfix">';
	    $html[] = '<div id="header">';
		$html[] = '<h2>'.self::settings('title').'</h2>';
		$html[] = '<h3><a id="favorate" href="'.zotop::url('zotop/favorate/add').'" class="dialog" title="将该页面加入我的收藏夹">加入收藏</a></h3>';
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
			$html[] = '<div id="bottom" class="clearfix">'.$str.'</div>';
		}
		$html[] = '</div>';
		$html[] = '<div id="powered">powered by <b>'.zotop::config('zotop.name').'</b> runtime:<b>{$runtime}</b>,memory:<b>{$memory}</b>,includefiles:<b>{$include}</b></div>';
		$html[] = '</div>';

		echo implode("\n",$html);
	}

	public static function navbar($data,$current='')
	{
		$html = array();

		if(is_array($data))
		{

			$current=empty($current) ? router::action() : $current;
			$current=empty($current) ? $data[0]['id'] : $current;
            $html[] = '';
			$html[] = '<div class="navbar">';
			$html[] = '	<ul>';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
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

    protected function config()
    {

    }

}
?>