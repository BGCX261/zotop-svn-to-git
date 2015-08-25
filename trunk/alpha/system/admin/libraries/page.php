<?php
class page extends BasePage
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
		$html[] = self::script($header['js']);
		$html[] = '	<script type="text/javascript">';
		$html[] = '		zotop.user.id =0;';
		$html[] = '		zotop.user.username = "";';
		$html[] = '		zotop.url.base = "'.url::base().'";';
		$html[] = '	</script>';
		$html[] = '</head>';
		$html[] = '<body'.html::attributes($header['body']).'>';
		$html[] = '<div id="wrapper">';
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
		$html[] = '</div>';
		$html[] = '<div id="body">';

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

}
?>