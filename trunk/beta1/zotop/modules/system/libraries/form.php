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
class form_base
{
	public static $template = 'table';
	public static $buttons = array();


	public static function isPostBack()
	{
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
		{
			
    		if ( (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['_FORMHASH'] == form::hash() )
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
		$hash = zotop::config('system.safety.authkey');
		$hash = empty($hash) ? 'zotop form hash!' : $hash;
		$hash = substr(time(), 0, -7).$hash;
		$hash = strtoupper(md5($hash));
		return $hash;
	}

	public static function globalid()
	{
		$globalid = zotop::cookie('file.globalid');

		if ( empty($globalid) )
		{
			$globalid = TIME.rand(100,10000);
			$globalid = md5($globalid);
			zotop::cookie('file.globalid',$globalid);
		}
		
		return $globalid;
	}
	
	public static function post()
	{
	    return $_POST;
	}

	public static function referer($url='')
	{
		static $referer;
		if(empty($url))
		{
			$url = zotop::post('_REFERER');
			return $url;
		}
		$referer = $url;
		return $referer;
	}

    public static function header($form=array())
	{		
		if( is_string($form) || is_numeric($form) )
		{
			$form = array('globalid'=>$form);
		}

		if ( is_array($form) )
		{
			$form += array('class'=>'form','method'=>'post','action'=>url::location(),'globalid'=>0);
		}

		$html[] = '';
        
		if ( arr::take('valid',$form) !== false )
		{
			$html[] = html::script('$common/js/jquery.validate.js');
		}
		
		if ( arr::take('ajax',$form) !== false )
		{
			$html[] = html::script('$common/js/jquery.form.js');
		}

		$icon = arr::take('icon',$form);
		$title = arr::take('title',$form);
		$description = arr::take('description',$form);
		$globalid = arr::take('globalid',$form);

		$template = arr::take('template',$form);
		if ( !empty($template) )
		{
			form::$template = $template;
		}

        //加载表头
		
		$html[] = '<form'.html::attributes($form).'>';
		$html[] = field::hidden(array('name' => '_REFERER','value' => url::referer()));
		$html[] = field::hidden(array('name' => '_FORMHASH','value' => form::hash()));
		$html[] = field::hidden(array('name' => '_GLOBALID','value' => empty($globalid) ? form::globalid() : $globalid));
		
		//表单头部
		if ( isset($title) || isset($description) )
		{
			$html[] = '<div class="form-header clearfix">';			
			if ( isset($icon) )
			{
				$html[] = '<div class="form-icon"><div class="zotop-icon zotop-icon-'.$icon.'"></div></div>';
			}
			if ( isset($title) )
			{
				$html[] = '	<div class="form-title">'.$title.'</div>';
			}
			if ( isset($description) )
			{
				$html[] = '	<div class="form-description">'.$description.'</div>';
			}
			$html[] = '</div>';
		}

		$html[] = '<div class="form-body clearfix">'; 
		$html[] = '';

        echo implode("\n",$html);
	}

	public static function footer($str ='', $buttons = array())
	{
		$html[] = '';
		$html[] = '</div>';	
	    $html[] = '<div class="form-footer clearfix">';
		$html[] = '	<div class="form-footer-main">'.form::$buttons.'</div>';
		$html[] = '	<div class="form-footer-sub">'.$str.'</div>';
		$html[] = '</div>';	    
		$html[] = '</form>';
		$html[] = html::script('$common/js/zotop.form.js');

		echo implode("\n",$html);

		form::$buttons = '';
	}

	//创建buttons
	public static function buttons()
	{
	    $buttons = func_get_args();

	    if( !empty($buttons) && !empty($buttons[0]))
	    {
    	    if( is_array($buttons) )
    	    {
        	    $html[] = '<div class="buttons">';

        		foreach($buttons as $button)
        		{
					$html[] = field::get($button);
				}

        		$html[] = '</div>';     
    	    }
    	    else
    	    {
    	        $html[] = $buttons;
    	    }

			form::$buttons = implode("\n",$html);
	    }	    
	}

	public static function field($attrs)
	{
		//判断控件类型
		$field = is_array($attrs) ? form::_field($attrs) : $attrs;

		echo $field;
	}

	public static function _field($attrs)
	{
		if ( $attrs['type'] == 'hidden' )
		{
			$str = field::get($attrs);
		}
		else
		{
			$str =  form::template(form::$template);
			$str = str_replace('{$label}',form::label($attrs), $str);
			$str = str_replace('{$description}', form::description($attrs), $str);
			$str = str_replace('{$field}', field::get($attrs), $str);
			$str = str_replace('{$display}', arr::take('display',$attrs) == 'none' ? ' style="display:none;"' : '', $str);
		}
		
		return $str;
	}

	public static function fieldset($attrs)
	{
		$label = arr::take('label',$attrs);
		$description = arr::take('description',$attrs);
		$fields = (array)arr::take('fields',$attrs);

		$attrs['class'] = empty($attrs['class']) ? 'fieldset' : 'fieldset '.$attrs['class'];
		
		$html[] = '';
		$html[] = '<div'.html::attributes($attrs).'>';
		$html[] = empty($label) ? '' : '<div class="fieldset-title">'.$label.'</div>';
		$html[] = '<div class="fieldset-body">';
		foreach($fields as $field)
		{
			$html[] = form::_field($field);
		}
		$html[] = '</div>';
		$html[] = '</div>';
	}

	public static function template($name='table', $value=null)
	{
		static $template = array();

		if ( empty($template) )
		{
			$template['div'] = implode("\n",array(
				'<div class="field"{$display}>',
				'	<div class="field-side">',
				'		{$label}{$description}<span class="field-valid inline-block"></span>',
				'	</div>',
				'	<div class="field-main">',
				'	{$field}',
				'	</div>',
				'</div>'
			));

			$template['table'] = implode("\n",array(
						'<table class="field"{$display}>',
						'<tr>',
						'	<td class="field-side">',
						'		{$label}',
						'	</div>',
						'	<td class="field-main">',
						'	{$field}<span class="field-valid inline-block"></span>',
						'	{$description}',
						'	</td>',
						'</tr>',
						'</table>'
			));

			$template['p'] = implode("\n",array(
						'<p class="field"{$display}>',
						'	{$label}',
						'	{$field}',
						'	<span class="field-valid inline-block">{$description}</span>',
						'</p>'
			));
		}

		if ( !is_null($value) )
		{
			$template[$name] = $value;
		}
		
	    return $template[$name];
	}

	public static function label(&$attrs,$required='*')
	{
		
		$label = arr::take('label',$attrs);
		$label = '<span>'.$label.'</span>';

		if ( strpos($attrs['valid'],'required') !== false )
		{
			$label = '<span class="field-required">'.$required.'</span>'.$label;
		}
		
		$label = html::label($label,$attrs['name']);

		return $label;
	}


	public static function description(&$attrs)
	{
		$description = arr::take('description',$attrs);

		if($description)
		{
			return '<span class="field-description">'.$description.'</span>';
		}
		return '';
	}


}
?>