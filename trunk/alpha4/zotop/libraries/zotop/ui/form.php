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
    		return true;
		}
		return false;
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
        //加载常用js
		$html[] = html::script(url::common().'/js/jquery.validate.js');
		$html[] = html::script(url::common().'/js/jquery.validate.additional.js');
		$html[] = html::script(url::common().'/js/jquery.form.js');
		$html[] = html::script(url::common().'/js/zotop.form.js');
		//表单头部		
		if( isset($form['title']) || isset($form['description']) )
		{
		    $html[] = '<div class="form-header clearfix">';
		    $html[] = '	<div class="form-header-inner '.(isset($form['icon']) ? 'form-icon' : '').' clearfix"'.(empty($form['icon']) ? '' : ' style="background-image:url('.$form['icon'].');"').'>';
		    $html[] = isset($form['title']) ? '		<div class="form-title">'.$form['title'].'</div>' : '';
            $html[] = isset($form['description']) ? '		<div class="form-description">'.$form['description'].'</div>' : '';
		    $html[] = '	</div>';
            $html[] = '</div>';
		}
	    //表单body部分开始
        $html[] = '<div class="form-body">';
        $html[] = field::hidden(array('name'=>'_REFERER','value'=>request::referer()));
        
        echo implode("\n",$html);
	}
	public static function footer($str='')
	{
		$html[] = '';
	    $html[] = '</div>';
	    if( !empty($str) )
	    {
	        $html[] = '<div class="form-footer">'.$str.'</div>';
	    }
	    $html[] = '</form>';
		echo implode("\n",$html);
		form::$template = '';
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