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
class field_base
{

    /**
     * 设置一个新的表单控件，用于覆盖系统默认的表单控件
     *
     * function mytextarea($attrs)
     * {
     *   return field::textarea($attrs).'<div>new textarea</div>';
     * }
     * field::set('textarea','mytextarea');
     *
     *
     * @param $name  string  控件名称
     * @param $callback function 控件函数
     * @return bool
     */
    public static function set($type,$callback='')
	{
	    static $fields = array();

	    $type = strtolower($type);

	    if(!empty($callback))
	    {
	        $fields[$type] = $callback;
	    }
	    if(isset($fields[$type]))
	    {
	        return $fields[$type];
	    }
	    return false;
	}

	/**
	 * 生成一个控件的Html数据
	 *
	 * @param $attrs array  控件参数
	 * @return string 返回控件的代码
	 */
	public static function get($attrs)
	{
		
		if ( is_array($attrs) )
		{
			//字段编号
			$attrs['id']= isset($attrs['id']) ?  $attrs['id'] : $attrs['name'];

			//字段类型,多个字段类型，用英文逗号分隔，如summary,textarea
			$types = isset($attrs['type']) ? arr::take('type',$attrs) : 'text';			
			$types = explode(',',$types);

			foreach( $types as $type )
			{
				$callback = field::set($type);

				if ( $callback )
				{
					return call_user_func_array($callback,array($attrs));
				}

				if ( method_exists('field',$type) )
				{
					return field::$type($attrs);
				}
			
			}
			return field::text($attrs);;
		}
		
		return $attrs;		
	}

	/**
	 * 判断控件类型是否存在
	 *
	 * @param $type string  控件类型
	 * @return bool 返回控件真假
	 */
	public static function exists($type)
	{
		if ( field::set($type) )
		{
			return true;
		}

		if ( method_exists('field',$type) )
		{
			return true;
		}

		return false;
	}

    /**
     * disabled 控件，显示一个disabled的文本输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function disabled($attrs)
	{
		$value = arr::take('value',$attrs);		
	    return field::text(array('type'=>'text','value'=>$value,'disabled'=>'disabled','class'=>'disabled'));

	}

	public static function label($attrs)
	{
		return '-----------';
	}
    /**
     * html控件，显示Html
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function html($attrs)
	{
		$value = arr::take('value',$attrs);
		$html = arr::take('html',$attrs);
		$html = empty($html) ? $value : $html;
		
	    return '<div class="field-wrapper inline-block"><div '.html::attributes($attrs).'>'.$html.'</div></div>';
	}

	/**
     * text文本控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
    public static function text($attrs)
	{
		$attrs['type'] = 'text';
		$attrs['class'] = isset($attrs['class']) ? 'text '.$attrs['class'] : 'text';
		return html::input($attrs);
	}
    /**
     * 隐藏类型控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function hidden($attrs)
	{
		$attrs['type'] = 'hidden';
		return html::input($attrs);
	}
    /**
     * 密码输入框控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function password($attrs)
	{
		$attrs['type'] = 'password';
		$attrs['class'] = isset($attrs['class']) ? 'password '.$attrs['class'] : 'password';
		return html::input($attrs);
	}
    /**
     * 按钮控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function button($attrs)
	{
		$attrs['type'] = 'button';
		return html::button($attrs);
	}
    /**
     * 表单提交按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function submit($attrs)
	{

	    $attrs['type'] = 'submit';
		$attrs['class'] = isset($attrs['class']) ? 'submit '.$attrs['class'] : 'submit';
		$attrs += array
		(
			'id'=>'submitform',
			'value'=>zotop::t('提交')
		);
		return html::button($attrs);
	}
    /**
     * 表单重置按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function reset($attrs)
	{
		$attrs['type'] = 'reset';
		$attrs['class'] = isset($attrs['class']) ? 'reset '.$attrs['class'] : 'reset';
		$attrs += array
		(
			'id'=>'resetform',
			'value'=>zotop::t('重置')
		);
		return html::input($attrs);
	}

    /**
     * 返回前页按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function back($attrs)
	{
		$attrs += array
		(
			'class'=>'zotop-back',
			'onclick'=>'window.history.go(-1);',
			'value'=>zotop::t('返回前页'),
		);
		return field::button($attrs);
	}

    /**
     * 返回前页按钮
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function close($attrs)
	{
		$attrs += array
		(
			'class'=>'zotop-close',
			'value'=>zotop::t('关闭')
		);
		return field::button($attrs);
	}

    /**
     * 文本段输入控件
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function textarea($attrs)
	{
	    $attrs['class'] = isset($attrs['class']) ? 'textarea '.$attrs['class'] : 'textarea';
		$attrs += array
		(
			'rows'=>'8',
			'cols'=>'5'
		);
		$value = arr::take('value',$attrs);
		return '<textarea'.html::attributes($attrs).'>'.html::encode($value).'</textarea>';
	}

	/**
	 * 生成一个标准的select控件
	 *
	 * @param $attrs
	 * @return string
	 */
	public static function select($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = arr::take('options',$attrs);
		$options = field::_option($options);
	    $value = arr::take('value',$attrs);//当value为数组时，则为多选
		if (is_array($value))
		{
			$attrs['multiple'] = 'multiple';
		}
		else
		{
			$value = array($value);
		}
		//为所有的select都加上select样式，便于全局统一控制select的样式，同input
	    if(isset($attrs['multiple']))
	    {
	        $defaultClass='select multiple';
	    }
	    else
	    {
	        $defaultClass='select';
	    }

		$attrs['class'] = isset($attrs['class']) ? $defaultClass.' '.$attrs['class'] : $defaultClass;
	    $html[] = '';
	    $html[] = '<select'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        foreach($options as $val=>$text)
	        {
	            $selected = in_array($val,$value) ? ' selected="selected"' : '';
	            $html[] = '	<option value="'.$val.'"'.$selected.'>'.$text.'</option>';
	        }
	    }
	    $html[] = '</select>';
	    $html[] = '';
	    return implode("\n",$html);
	}

	public static function dropdown($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr:take
		$html[] = '';
		$html[] = '<div class="inline-block dropdown">';
		$html[] = '<input type="text" value="'.$options[$attrs['value']].'" class="text '.$attrs['class'].'" style="'.$attrs['style'].'" readonly="readonly">';
		$html[] = '<input type="hidden" value="'.$attrs['value'].'" valid="'.$attrs['valid'].'" title="'.$attrs['title'].'" class="value">';
		$html[] = '<div class="dropdownBox">';
		$html[] = '<ul class="dropdownOptions">';
		foreach($options as $val=>$text)
		{
			$html[] = '<li rel="'.$val.'">'.$text.'</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';
		$html[] = '</div>';
	    $html[] = '';
	    return implode("\n",$html);
	}

    /**
     * 多选输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function checkbox($attrs)
	{
	    $options = arr::take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数arr::take
	    $options = field::_option($options);
	   
		$value = arr::take('value',$attrs);//即取出了value和options，又把他们从$attrs中去掉了		
		$value = json_decode($value);

		if ( !is_array($value) && isset($value) && $value !== '' )
		{
			$value = array($value);
		}
		//zotop::dump($value);
	    $attrs['class'] = isset($attrs['class']) ? 'checkbox '.$attrs['class'] : 'checkbox';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $valid = arr::take('valid',$attrs);
		$html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {

				$checked = is_array($value) && in_array($val,$value) ? ' checked="checked"' : '';

	            $html[] = '<li><input type="checkbox" name="'.$attrs['name'].'[]" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.''.((isset($valid) && $i==1) ? ' valid = "'.$valid.'"':'').'/>';
				$html[] = '<label for="'.$attrs['name'].'-item'.$i.'">'.$text.'</label></li>';//这儿代码不完美
				$i++;
	        }
	    }
	    $html[] = '</ul>';

	    return implode("\n",$html);
	}

    /**
     * 单选输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function radio($attrs)
	{
	    $options = arr::take('options',$attrs);
		$options = field::_option($options);
	    $value = arr::take('value',$attrs);	    
	    $attrs['class'] = isset($attrs['class']) ? 'radio '.$attrs['class'] : 'radio';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $valid = arr::take('valid',$attrs);
		$html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {
	            $checked = ($val==$value) ? ' checked="checked"' : ''; //这儿代码可能有问题，请检查
	            $html[] = '	<li>';
	            $html[] = '		<input type="radio" name="'.$attrs['name'].'" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.''.((isset($valid) && $i==1) ? ' valid = "'.$valid.'"':'').'/>';
				$html[] = '		<label for="'.$attrs['name'].'-item'.$i.'">'.$text.'</label>';
				$html[] = '	</li>';//这儿代码不完美
				$i++;
	        }
	    }
	    $html[] = '</ul>';

	    return implode("\n",$html);
	}
    /**
     * 图片上传输入框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function image($attrs)
	{
	   //上传handle
	   $handle = arr::take('handle',$attrs);
	   $handle = empty($handle) ? zotop::url('system/image/upload') : $handle;

	   $html[] = html::script('$common/js/zotop.upload.js');
	   $html[] = '<div class="field-wrapper clearfix">';
	   $html[] = '	'.field::text($attrs);
       $html[] = '	<span class="field-handle">';
	   $html[] = '		&nbsp;<a href="'.$handle.'" class="imageuploader" title="'.zotop::t('选择或者上传图片').'"><span class="zotop-icon zotop-icon-imageuploader"></span></a>';
	   $html[] = '	</span>';
	   $html[] = '</div>';

	   return implode("\n",$html);
	}

    /**
     * 标题输入框，含有标题样式
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function title($attrs)
	{
		$style = arr::decode($attrs['style'], ';', ':');
		$attrs['class'] = empty($attrs['class']) ? 'title ruler' : $attrs['class'].' title ruler';

		$html[] = html::script('$common/js/jquery.colorpicker.js');
		$html[] = html::script('$common/js/zotop.title.js');
		$html[] = '<div class="field-wrapper clearfix">';
		$html[] = '	'.field::text($attrs);
		$html[] = '	'.field::hidden(array('name'=>$attrs['name'].'_color','id'=>$attrs['name'].'_style','class'=>'short','value'=>$style['color']));
		$html[] = '	'.field::hidden(array('name'=>$attrs['name'].'_weight','id'=>$attrs['name'].'_weight','class'=>'short','value'=>$style['font-weight']));
		$html[] = '	<span class="field-handle">';
		$html[] = '		<a class="setweight" style="display:inline-block;" valueto="'.$attrs['name'].'_weight" weightto="'.$attrs['name'].'" title="'.zotop::t('加粗').'"><span class="zotop-icon zotop-icon-b"></span></a>';
		$html[] = '		<a class="setcolor" style="display:inline-block;" valueto="'.$attrs['name'].'_color" colorto="'.$attrs['name'].'" title="'.zotop::t('色彩').'"><span class="zotop-icon zotop-icon-setcolor '.$style['font-weight'].'"></span></a>';
		$html[] = '	</span>';
		$html[] = '</div>';

		return implode("\n",$html);
	}

	/**
     * 文件上传框
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function file($attrs)
	{
		$attrs['type'] = 'file';
		$attrs['class'] = isset($attrs['class']) ? 'file '.$attrs['class'] : 'file';
		return html::input($attrs);
	}



    /**
     * 富文本编辑器
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function editor($attrs)
	{
		$attrs['class'] = isset($attrs['class']) ? 'editor '.$attrs['class'] : 'editor';

		return field::textarea($attrs);
	}

	
    public static function source($attrs)
    {
        $attrs['style'] = 'width:600px;height:460px;';

        return field::textarea($attrs);
    }


	public static function time($attrs)
	{
		$attrs = array_merge((array)$attrs, array('onfocus'=>'WdatePicker({dateFmt:"yyyy-MM-dd HH:mm:ss",isShowClear:false,readOnly:true})'));

		$html[] = html::script('$common/js/datepicker/WdatePicker.js');
		$html[] = '<div class="field-wrapper clearfix">';
		$html[] = '	'.field::text($attrs);
		//$html[] = '	<span class="field-handle">';
		//$html[] = '		<a href="javascript:void(0);"><span class="zotop-icon zotop-icon-datapicker"></span></a>';
		//$html[] = '	</span>';
		$html[] = '</div>';
		return implode("\n",$html);
	}


	public static function date($attrs)
	{
		$attrs = array_merge((array)$attrs, array('onfocus'=>'WdatePicker({dateFmt:"yyyy-MM-dd",isShowClear:false,readOnly:true})'));

		$html[] = html::script('$common/js/datepicker/WdatePicker.js');
		$html[] = field::text($attrs);

		return implode("\n",$html);
	}
    
    /**
     * 控件组
     *
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function group($attrs)
	{
		$html[] = '<div class="field-group">';

		$fields = arr::take('field',$attrs);
		
		if ( is_array($fields) )
		{
			foreach($fields as $field)
			{
				if ( is_array($field) )
				{
					$field['class'] =  isset($field['class']) ? 'short '.$field['class'] : 'short';
					$html[] = '	<div class="field-group-item">';
					$html[] = '		<label for="'.$field['name'].'">'.arr::take('label',$field).'</label>';
					$html[] = '		'.field::get($field);
					$html[] = '	</div>';
				}
				else
				{
					$html[] = $field;
				}
			}
		}
		else
		{
			$html[] = $fields;
		}
		
		$html[] = '</div>';

		return implode("\n",$html);
	}

	/**
	 * 将字符串转化成标准的选项数组
	 *
	 * @param  string $options 选项字符串
	 * @param  string $s1  第一分割符号
	 * @param  string $s2  第二分割符号
	 * @return array
	 */
	public function _option($options, $s1 = "\n", $s2 = '|')
	{
		if( is_array($options) )
		{
			return $options;
		}

		$options = explode($s1, $options);
		
		foreach($options as $option)
		{
			if(strpos($option, $s2))
			{
				list($name, $value) = explode($s2, trim($option));
			}
			else
			{
				$name = $value = trim($option);
			}
			$os[$value] = $name;
		}

		return $os;
	}

}
?>