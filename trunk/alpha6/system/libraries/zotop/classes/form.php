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
	public static $globalid = '';
	public static $template = '';
	public static $buttons = '';


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

	/**
	 * 为表单生成一个全局编号，用于media等数据定位
	 *
	 */
	public static function globalid()
	{
		$globalid = form::$globalid;

		if( empty($globalid) )
		{
			$globalid = ZOTOP_MODULE.'.'.ZOTOP_CONTROLLER.'.'.ZOTOP_ACTION;
		}

		$globalid = md5($globalid);

		return $globalid;
	}

	public static function hash()
	{
		$hash = zotop::config('system.safety.authkey');
		$hash = empty($hash) ? 'zotop form hash!' : $hash;
		$hash = substr(time(), 0, -7).$hash;
		$hash = strtoupper(md5($hash));
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
		
		if( is_string($form) )
		{
			$form['description'] = $form;
		}

		form::$template = isset($form['template']) ? $form['template'] : form::$template;
		form::$globalid = isset($form['globalid']) ? $form['globalid'] : form::$globalid;

		//form 标签
		$attrs['class'] = isset($form['class']) ? $form['class'] : 'form';
		$attrs['method'] = isset($form['method']) ? $form['method'] : 'post';
		$attrs['target'] = isset($form['target']) ? $form['target'] : '';
		$attrs['action'] = isset($form['action']) ? $form['action'] : url::location();

		if( isset($form['enctype']) || isset($form['upload']) )
		{
			$attrs['enctype'] = 'multipart/form-data';
		}

        //加载表头
		$html[] = '';
		$html[] = '<form'.html::attributes($attrs).'>';
		$html[] = field::hidden(array('name' => '_REFERER','value' => request::referer()));
		$html[] = field::hidden(array('name' => '_FORMHASH','value' => form::hash()));
		$html[] = field::hidden(array('name' => '_GLOBALID','value' => form::globalid()));

        //加载常用js
		if ( $form['valid'] !== false )
		{
			$html[] = html::script(ZOTOP_APP_URL_JS.'/jquery.validate.js');
		}
		
		if ( $form['ajax'] !== false )
		{
			$html[] = html::script(ZOTOP_APP_URL_JS.'/jquery.form.js');
		}

		//表单头部		
		if( isset($form['title']) || isset($form['description']) )
		{
		    $html[] = '<div class="form-header clearfix">';
		    $html[] = isset($form['title']) ? '		<div class="form-title">'.$form['title'].'</div>' : '';
            $html[] = isset($form['description']) ? '		<div class="form-description">'.$form['description'].'</div>' : '';
            $html[] = '</div>';
		}

	    //表单body部分开始
        $html[] = '<div class="form-body">'; 
        
        echo implode("\n",$html);
	}

	public static function footer($str ='', $buttons = array())
	{
		$html[] = '';
		$html[] = '</div>';
	    $html[] = '<div class="form-footer"><div class="form-footer-main">'.form::$buttons.'</div><div class="form-footer-sub">'.$str.'</div></div>';
	    $html[] = html::script(ZOTOP_APP_URL_JS.'/zotop.form.js');
		$html[] = '</form>';

		echo implode("\n",$html);

		form::$template = '';
		form::$buttons = '';
	}

	public static function top()
	{
		echo '<div class="form-top clearfix"><div class="form-title">'.$title.'</div><div class="form-description">'.$description.'</div></div>';
	}

	public static function bottom($main='',$extra='')
	{
		echo '<div class="form-bottom clearfix"><div class="form-bottom-main">'.$main.'</div><div class="form-bottom-sub">'.$extra.'</div></div>';
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
					$html[] = form::control($button);
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
		if ( $attrs['type'] == 'hidden' )
		{
			$str = form::control($attrs);
		}
		else
		{
			$label = arr::take('label',$attrs);
			$description = arr::take('description',$attrs);

			$str =  form::template(form::$template);
			$str = str_replace('{$field:label}',form::label($label,$attrs),$str);
			$str = str_replace('{$field:description}', form::description($description),$str);
			$str = str_replace('{$field:controller}', form::control($attrs), $str);
			$str = str_replace('{$field:display}', arr::take('display',$attrs) == 'none' ? ' style="display:none;"' : '', $str);
		}
		echo $str;
	}

	public static function template($template='div')
	{
		$template = empty($template) ? 'table' : $template;
		$html = array();
		switch($template)
		{
			case 'div':
				$html[] = '';
				$html[] = '<div class="field"{$field:display}>';
				$html[] = '	<div class="field-side">';
				$html[] = '		{$field:label}<span class="field-valid inline-block"></span>';
				$html[] = '		{$field:description}';
				$html[] = '	</div>';
				$html[] = '	<div class="field-main">';
				$html[] = '	{$field:controller}';
				$html[] = '	</div>';
				$html[] = '</div>';
				break;
			case 'table':
				$html[] = '';
				$html[] = '<table class="field"{$field:display}><tr>';
				$html[] = '	<td class="field-side">';
				$html[] = '		{$field:label}';
				$html[] = '	</td>';
				$html[] = '	<td class="field-main">';
				$html[] = '	{$field:controller}<span class="field-valid inline-block"></span>';
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

	public static function label($label,$attrs,$required='*')
	{
		if ( strpos($attrs['valid'],'required') !== false )
		{
			$label .= '<span class="field-required">'.$required.'</span>';
		}
		
		$label = html::label($label,$attrs['name']);

		return $label;
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

	

	public function add()
	{
	
	}

}
?>