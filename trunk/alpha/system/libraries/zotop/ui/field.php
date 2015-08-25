<?php
class BaseField extends Base
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
    public static function set($name,$callback='')
	{
	    static $fields = array();
	    $name = strtolower($name);
	    if(!empty($callback))
	    {
	        $fields[$name] = $callback;
	    }
	    if(isset($fields[$name]))
	    {
	        return $fields[$name];
	    }
	    return false;
	}

	/**
	 * 生成一个控件的Html数据
	 * 
	 * 
	 * @param $name string 控件名称
	 * @param $attrs array  控件参数
	 * @return string 返回控件的代码
	 */
	public static function get($name,$attrs=array())
	{
	    $callback = field::set($name);
	    if($callback)
	    {
	        return call_user_func_array($callback,array($attrs));
	    }
		if( method_exists('field',$name) )
		{
		    return field::$name($attrs);
		}

		return 'Unkown FieldController : <b>'.$name.'</b>';
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
		$attrs['type'] = 'text';
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
		$attrs['class'] = isset($attrs['class']) ? 'button '.$attrs['class'] : 'button';
		return html::input($attrs);
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
			'name'=>'submitform',
			'value'=>t('提 交')
		);
		return html::input($attrs);
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
			'name'=>'resetform',
			'value'=>'重 置'
		);
		return html::input($attrs);
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
		$value=array_take('value',$attrs);
		return '<textarea'.html::attributes($attrs).'>'.html::encode($value).'</textarea>';
	}

	/**
	 * 生成一个标准的select控件
	 *
	 * @param $attrs
	 * @return string
	 */
	//TODO 考虑多选select 的支持问题
	public static function select($attrs)
	{
	    $attrs['id'] = isset($attrs['id']) ? $attrs['id'] : $attrs['name'];
	    $options = array_take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数array_take
	    //$options = html::options($options); //格式化数组
	    $value = array_take('value',$attrs);//当value为数组时，则为多选
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
	            $selected = in_array($val,$value) ? ' selected="selected"' : ''; //这儿代码可能有问题，选项值不区分大小写，in_array写法？
	            $html[] = '	<option value="'.$val.'"'.$selected.'>'.html::encode($text).'</option>';
	        }
	    }
	    $html[] = '</select>';
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
	    $options = array_take('options',$attrs);//取出options，并unset，这儿用了一个自定义的函数array_take
	    //$options = html::options($options); //格式化数组
	    $value = array_take('value',$attrs);//即取出了value和options，又把他们从$attrs中去掉了
		if (!is_array($value))
		{
			$value = array($value);
		}
	    $attrs['class'] = isset($attrs['class']) ? 'checkbox '.$attrs['class'] : 'checkbox';//默认样式inline，允许传入block使得checkbox每个元素显示一行
	    $html[] = '<ul'.html::attributes($attrs).'>';
	    if(is_array($options))
	    {
	        $i = 1;
	        foreach($options as $val=>$text)
	        {
	            $checked = in_array($val,$value) ? ' checked="checked"' : ''; //这儿代码可能有问题，请检查
	            $html[] = '<li><input type="checkbox" name="'.$attrs['name'].'[]" id="'.$attrs['name'].'-item'.$i.'" value="'.$val.'"'.$checked.'/><label for="'.$attrs['name'].'-item'.$i.'">'.html::encode($text).'</label></li>';//这儿代码不完美
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
	//TODO 请完成radio相关代码，注意inline和block的显示问题处理（radio项显示在一行还是现实成多行，先生成代码，最后使用css控制）
	public static function radio($attrs)
	{

	}
    /**
     * 图片上传输入框
     * 
     * @param $attrs array 控件参数
     * @return string 控件代码
     */
	public static function image($attrs)
	{
	   $html[] = '<div class="field-inner">';
	   $html[] = field::text($attrs);
	   $html[] = html::input( array('type'=>'button','class'=>'upload-image','for'=>$attrs['name'],'value'=>t('上传图片'),'title'=>t('上传图片')) );
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
         return field::textarea($attrs);
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
		$fields = array_take('fields',$attrs);
		if(is_array($fields))
		{
			foreach($fields as $field)
			{
				if(is_array($field))
				{
					$type = array_take('type',$field);
					$type = isset($type) ? $type : 'text';
					$html[] = '	<div class="field-group-item">';
					$html[] = '		<label for="'.$field['name'].'">'.array_take('label',$field).'</label>';
					$html[] = '		'.field::get($type,$field);
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

}
?>