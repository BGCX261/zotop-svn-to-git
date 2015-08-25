<?php
class BaseForm extends Base
{
    public static function header($header=array())
	{
	    $attrs['class'] = isset($header['class']) ? $header['class'] : 'form';
		$attrs['method'] = isset($header['method']) ? $header['method'] : 'post';
		$attrs['action'] = isset($header['action']) ? $header['action'] : url::current();

		$html[] = '';
		$html[] = '<form'.html::attributes($attrs).'>';
		$html[] = isset($header['title']) ? '<div class="form-title">'.$header['title'].'</div>' : '';
        $html[] = isset($header['title']) ? '<div class="form-description">'.$header['description'].'</div>' : '';

		$html[] = html::script(url::theme().'/js/jquery.form.js');
		$html[] = html::script(url::theme().'/js/zotop.form.js');

        echo implode("\n",$html);
	}
	public static function footer()
	{
		$html[] = '';
	    $html[] = '</form>';

		echo implode("\n",$html);
	}

	public static function add($attrs)
	{
        echo form::item($attrs);
	}

	public static function buttons()
	{
	    $buttons = func_get_args();
	    $html[] = '<div class="buttons">';
		foreach($buttons as $button)
		{
			$html[] = form::field($button);
		}
		$html[] = '</div>';
	    echo implode("\n",$html);
	}

	public static function item($attrs,$template='')
	{
	    $description = array_take('description',$attrs);
	    $label = array_take('label',$attrs);

		$str = empty($template) ? form::template() : $template;
		$str = str_replace('{$field:label}',html::label($label,$attrs['name']),$str);
		$str = str_replace('{$field:required}',form::required($attrs['valid']),$str);
		$str = str_replace('{$field:description}', form::description($description),$str);
		$str = str_replace('{$field:controller}',form::field($attrs), $str);
		return $str;
	}

	public static function template()
	{
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

	public static function field($attrs)
	{
	    $html[] = '';
		if( is_array($attrs) )
		{
			$type = array_take('type',$attrs);
			$type = isset($type) ? $type : 'text';
			$html[] = field::get($type,$attrs);
		}
		else
		{
			 $html[] = $attrs;
		}
		return implode("\n",$html);
	}

}
?>