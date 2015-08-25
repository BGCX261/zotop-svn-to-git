<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 表单辅助
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_form
{
	public static $template = '';

	public static function isPostBack()
	{

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
    		if((empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['_FORMHASH'] == form::hash())
			{
				return true;
			}
			else
			{
				zotop::error('invalid submit!');
			}
		}
		return false;
	}



	public static function hash()
	{
		$hash = zotop::config('safety.authkey');
		$hash = empty($hash) ? 'zotop form hash!' : $hash;
		$hash = substr(time(), 0, -7).$hash;
		$hash = strtoupper(md5($hash));
		//return substr(time(), 0, -7);
		return $hash;
	}
	
	public static function post()
	{
	    $post = array();
	    foreach($_POST as $key=>$val)
	    {
	        if($key[0] != '_' )
	        {
	            $post[$key] = $val;
	        }   
	    }	    
	    return $post;
	}

    public static function header($form=array())
	{
	    if(isset($form['template']))
		{
			form::$template = arr::take('template',$form);
		}
		$attrs['class'] = isset($form['class']) ? $form['class'] : 'form';
		$attrs['method'] = isset($form['method']) ? $form['method'] : 'post';
		$attrs['action'] = isset($form['action']) ? $form['action'] : url::current();
        //加载表头
		$html[] = '';
		$html[] = '<form'.html::attributes($attrs).'>';
		$html[] = field::hidden(array('name'=>'_REFERER','value'=>request::referer()));
		$html[] = field::hidden(array('name'=>'_FORMHASH','value'=>form::hash()));
        //加载常用js
		$html[] = html::script(url::common().'/js/jquery.validate.js');
		$html[] = html::script(url::common().'/js/jquery.validate.additional.js');
		$html[] = html::script(url::common().'/js/jquery.form.js');
		//表单头部		
		if( isset($form['title']) || isset($form['description']) )
		{
		    $html[] = '<div class="form-header clearfix">';
			$html[] = isset($form['icon']) ? '		<div class="form-icon"></div>' : '';
		    $html[] = isset($form['title']) ? '		<div class="form-title">'.$form['title'].'</div>' : '';
            $html[] = isset($form['description']) ? '		<div class="form-description">'.$form['description'].'</div>' : '';
            $html[] = '</div>';
		}
	    //表单body部分开始
        $html[] = '<div class="form-body">'; 
        
        echo implode("\n",$html);
	}
	public static function footer( $buttons = array(), $str ='')
	{
		$html[] = '';
		$html[] = '</div>';
	    if( !empty($buttons) )
	    {
    	    if( is_array($buttons) )
    	    {
        	    $html[] = '<div class="buttons">';
        		foreach($buttons as $button)
        		{
        			$html[] = form::control($button);
        		}
        		$html[] = '</div>';     
    	    }
    	    else
    	    {
    	        $html[] = $buttons;
    	    }
	    }
	    $html[] = '<div class="form-footer">'.$str.'</div>';
	    $html[] = '</form>';
		echo implode("\n",$html);
		form::$template = '';
	}

	public static function top()
	{
		$html[] = '';
		$html[] = '<div class="form-top clearfix">';		
		$html[] = '	<div class="form-title">'.$title.'</div>';
		$html[] = '	<div class="form-description">'.$description.'</div>';
	    $html[] = '</div>';
		echo implode("\n",$html);		
	}

	public static function bottom($main='',$extra='')
	{
		$html[] = '';
		$html[] = '<div class="form-bottom clearfix">';		
		$html[] = '	<div class="form-bottom-main">'.$main.'</div>';
		$html[] = '	<div class="form-bottom-extra">'.$extra.'</div>';
	    $html[] = '</div>';
		echo implode("\n",$html);
	}


	public static function buttons()
	{
	    $buttons = func_get_args();
	    $html[] = '<div class="buttons">';
		foreach($buttons as $button)
		{
			$html[] = form::control($button);
		}
		$html[] = '</div>';
	    echo implode("\n",$html);
	}

	public static function field($attrs)
	{

		if($attrs['type'] == 'hidden')
		{
			echo form::control($attrs);
		}
		else
		{
			$label = arr::take('label',$attrs);
			$description = arr::take('description',$attrs);
			$str =  form::template(form::$template);
			$str = str_replace('{$field:label}',html::label($label,$attrs['name']),$str);
			$str = str_replace('{$field:required}',form::required($attrs['valid']),$str);
			$str = str_replace('{$field:description}', form::description($description),$str);
			$str = str_replace('{$field:controller}',form::control($attrs), $str);
			echo $str;
		}
	}

	public static function template($template='div')
	{
		$template = empty($template) ? 'table' : $template;
		$html = array();
		switch($template)
		{
			case 'div':
				$html[] = '';
				$html[] = '<div class="field">';
				$html[] = '	<div class="field-side">';
				$html[] = '		{$field:label}{$field:required}';
				$html[] = '		{$field:description}';
				$html[] = '	</div>';
				$html[] = '	<div class="field-main">';
				$html[] = '	{$field:controller}';
				$html[] = '	</div>';
				$html[] = '</div>';
				break;
			case 'table':
				$html[] = '';
				$html[] = '<table class="field"><tr>';
				$html[] = '	<td class="field-side">';
				$html[] = '		{$field:label}{$field:required}';

				$html[] = '	</td>';
				$html[] = '	<td class="field-main">';
				$html[] = '	{$field:controller}';
				$html[] = '	{$field:description}';
				$html[] = '	</td>';
				$html[] = '</tr></table>';
				break;
			default:
				$html[] = '';
				$html[] = $template;
				break;
		}
	    return implode("\n",$html);
	}

	public static function required($str,$required='*')
	{
		if(strpos($str,'required')!==false)
		{
			return '<span class="field-required">'.$required.'</span>';
		}
		return '';
	}

	public static function description($str)
	{
		if($str)
		{
			return '<span class="field-description">'.$str.'</span>';
		}
		return '';
	}

	public static function control($attrs)
	{
	    $html[] = '';
		if( is_array($attrs) )
		{
			$type = arr::take('type',$attrs);
			$type = isset($type) ? $type : 'text';
			$html[] = field::get($type,$attrs);
		}
		else
		{
			 $html[] = $attrs;
		}
		return implode("\n",$html);
	}

	public static function referer($url='')
	{
		static $referer;
		if(empty($url))
		{
			$url = request::post('_REFERER');
			return $url;
		}
		$referer = $url;
		return $referer;
	}

}
?>