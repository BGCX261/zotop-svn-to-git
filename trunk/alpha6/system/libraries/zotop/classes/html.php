<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 数组操作类
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.util
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_html
{
	
	//encode的别名
	public static function specialchars($str)
	{
		return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
	}
	//编码字符串
	public static function encode($str)
	{
		return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
	}
	//解码字符串
	public static function decode($str)
	{
		return htmlspecialchars_decode($str, ENT_QUOTES);
	}
	//创建标签
	public static function attributes($attrs,$value=NULL)
	{
		if ( empty($attrs) )
		{
			return '';
		}
		
		if ( is_string($attrs) )
		{
			if ( !isset($value) )
			{
				return ' '.$attrs;
			}
			if ( empty($value) )
			{
			    return '';
			}
			return ' '.$attrs.'="'.$value.'"';
		}
		
		$str = '';
		
		if ( is_array($attrs) )
		{
			foreach ( $attrs as $key=>$val )
			{
			    if(!is_null($val))
			    {
				    $str .= ' '.$key.'="'.$val.'"';
			    }
			}
		}
		
		return $str;
	}
	/**
	 * 创建一个超链接传入uri或者完整的url
	 *
	 * @param string URL或者URI字符串
	 * @param string 链接显示文字
	 * @param array 链接的其它属性
	 * @param string 协议，如：http,https,ftp
	 * @return string 链接字符串
	 *
	 */
	public static function a($url,$title=NULL,$attrs=NULL,$protocol=NULL)
	{
		$title = isset($title) ? $title : $url;
	    return '<a href="'.$url.'" '.html::attributes($attrs).'>'.$title.'</a>';
	}

	public static function b($text,$attrs)
	{
		return '<b'.html::attributes($attrs).'>'.$text.'</b>';
	}

	/**
	 * 创建一个超链接传入uri或者完整的url
	 *
	 * @param string URL或者URI字符串
	 * @param array 图片的其它属性
	 * @return string 链接字符串
	 *
	 */
	public static function image($url, $attrs=array())
	{
		//如果不是完整的链接，如：http://www.zotop.com/a/b/1.gif ，则将相对连接处理成绝对链接
	    if( strpos($url, '://') === false && $url[0]!='/' && $url[0]!='$' )
		{
		    $url = '$root/'. $url;
		}
		
		//解析url中的特殊字符串
		$url = url::decode($url);
		
	    return '<img src="'.$url.'" '.html::attributes($attrs).'/>';
	}

	public static function flash($url,$width,$height,$attrs=array())
	{

	}

	public static function link($href,$attrs=array())
	{
		$links = array();
           
		$str='';
		if(is_array($href))
		{
			foreach($href as $h)
			{
				$str .= html::link($h,$attrs);
			}
		}
		else
		{
            $href = url::clean($href);
            $href = url::decode($href);
            //只加载一次
    	    $links[strtolower($href)] = true;
		    $attrs['href'] = $href;
			$str = '<link'.html::attributes($attrs).' />';
		}
		return $str;
	}


	public static function stylesheet($href,$attrs='')
	{
	    $attrs['rel']= 'stylesheet';
	    $attrs['type']= 'text/css';
	    return html::link($href,$attrs);
	}

	public static function style($style)
	{
	    if($style)
	    {
	        return '<style type="text/css">'.$style.'</style>';
	    }
	    return '';
	}


	public static function script($href,$attrs=array())
	{
		static $scripts = array();

	    $str = '';
		if(is_array($href))
		{
			foreach($href as $src)
			{
				$str .= html::script($src,$attrs);
			}
		}
		else
		{
		    //如果不是是直接输出的话
		    if(strpos($href , ';')==0)
		    {
				$href = url::clean($href);
				$href = url::decode($href);
			    //一个页面只允许加载一次
			    if( isset($scripts[strtolower($href)]) )
			    {
			       return '';
			    }
			    $scripts[strtolower($href)] = true;

			    $attrs['type'] = 'text/javascript';
			    $attrs['src'] = $href.'?v'.zotop::config('zotop.version');
			    $str = '<script'.html::attributes($attrs).'></script>';
		    }
		    else
		    {
		        $attrs['type'] = 'text/javascript';
		        $str = '<script'.html::attributes($attrs).'>'.$href.'</script>';
		    }

		}
		return $str;
	}

	public static function meta($tag,$value='')
	{
		$str= '';
		if(is_array($tag))
		{
			foreach($tag as $attr=>$value)
			{
				$str .= html::meta($attr,$value)."\n";
			}
		}
		else
		{
			$attr = in_array(strtolower($tag), array('content-type','content-language',strtolower('X-UA-Compatible'))) ? 'http-equiv' : 'name';
			$str = '<meta '.$attr.'="'.$tag.'" content="'.$value.'" />';
		}
		return $str;
	}
	//input标签
	public static function label($text,$for='',$attrs=array())
	{
		$attrs['for']=$for;
		return '<label'.html::attributes($attrs).'>'.$text.'</label>';
	}

	public static function input(array $attrs)
	{
		$attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'text';

		//if( isset($attrs['name']) )
		//{
		//    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
		//}

		$attrs['value'] = isset($attrs['value']) ? $attrs['value'] : '';

		return '<input'.html::attributes($attrs).'/>';
	}

	public static function button(array $attrs)
	{
		$attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'button';
		$attrs['class'] = isset($attrs['class']) ? 'button '.$attrs['class'] : 'button';

		$value = arr::take('value',$attrs);
		$icon = arr::take('icon',$attrs);
		$icon = empty($icon) ? 'empty' : $icon;
		
		return '<button'.html::attributes($attrs).'><p><span class="button-icon zotop-icon zotop-icon-'.$icon.'"></span><span class="button-text">'.html::encode($value).'</span></p></button>';		
	}

    public static function iframe($name,$src,$extra=array(),$noframe='')
    {
        $attrs = array(
            'id'=>$name,
        	'name'=>$name,
            'src'=>$src,
        );
        $attrs += (array)$extra;
        return '<iframe'.html::attributes($attrs).'>'.$noframe.'</iframe>';
    }

	public static function media()
	{

	}

	public static function checkbox($attrs, $value='', $checked=false , $label='')
	{
		if(!is_array($attrs))
		{
			$attrs = array(
        		'name'=>$attrs,
				'value'=>$value,
				'checked'=>$checked,
				'label'=>$label
			);
		}

		$attrs['type'] = 'checkbox';
		$attrs['id'] = empty($attrs['id']) ? $attrs['name'] : $attrs['id'];

		if ($checked == TRUE OR (isset($attrs['checked']) AND $attrs['checked'] == TRUE))
		{
			$attrs['checked'] = 'checked';
		}
		else
		{
			unset($attrs['checked']);
		}
		
		$label = arr::take('label',$attrs);
		$label = empty($label) ? '' : ' <label for="'.$attrs['id'].'">'.$label.'</label>';

		return html::input($attrs).$label;
	}

	public static function radio($attrs, $value='', $checked=false , $extra=array())
	{
		if(!is_array($attrs))
		{
			$attrs = array(
        		'name'=>$attrs,
				'value'=>$value,
			);
		}

		$attrs['type'] = 'radio';

		if ($checked == TRUE OR (isset($attrs['checked']) AND $attrs['checked'] == TRUE))
		{
			$attrs['checked'] = 'checked';
		}
		else
		{
			unset($attrs['checked']);
		}
		return html::input($attrs);
	}


	public static function nbs($num = 1)
	{
		return str_repeat("&nbsp;", $num);
	}

	public static function h($text = '', $h = '1')
	{
		return "<h".$h.">".$text."</h".$h.">";
	}

	public static function br($num = 1)
	{
		return str_repeat("<br />", $num);
	}
	
	public static function ul($data,$attrs=array('class'=>'list'))
	{
	    $html[] = '<ul'.html::attributes($attrs).'>';
	    foreach($data as $item)
	    {
	        $html[] = '<li>'.$item.'</li>';
	    }
	    $html[] = '</ul>';
	    return implode("\n",$html);
	}
	
	public static function msg($messages,$type='notice')
	{
        $html[] = '<div class="zotop-msg clearfix '.$type.'">';
        $html[] = '	<div class="zotop-msg-icon"><div class="zotop-icon zotop-icon-'.$type.'"></div></div>';
        $html[] = '	<div class="zotop-msg-content">';
        $html[] = is_array($messages) ? html::ul($messages) : $messages;
        $html[] = '	</div>';
        $html[] = '</div>';
        return implode("\n",$html);	   
	}
	
	public static function icon($name='')
	{
	    if ( empty($name) )
	    {
	       return '<div class="zotop-icon"></div>'; 
	    }
	    return '<div class="zotop-icon zotop-icon-'.$name.'"></div>';
	}

}
?>