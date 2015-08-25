<?php
class zotop_model_config extends model
{
	protected $_key = 'id';
	protected $_table = 'config';

	public function types()
	{
		$types = array(
		    ''=>'请选择控件类型',
			'folder'=>'文件夹',
			'text'=>'单行文本输入框',
			'textarea'=>'多行文本输入框',
			'select'=>'单选下拉选择框',
			'radio'=>'单项选择框',
			'checkbox'=>'多项选择框',
			'editor'=>'富文本编辑器',
			'image'=>'图片上传控件',
			'date'=>'日期选择控件',
		);

		$types = zotop::filter('zotop.config.field.types',$types);

		return $types;
	}

	public function attrs($type)
	{
		$attrs = array(
			'text'=>'width,class',	
			'textarea'=>'width,height,class',
			'select'=>'options,width,multi',
			'radio'=>'options,class',
			'checkbox'=>'options,class',
			'editor'=>'width,height,upload',
			'image'=>'upload',
			'date'=>'select',
		);

		$attrs = zotop::filter('zotop.config.field.attrs',$attrs);

		return $attrs[$type];
	}

	public function controls()
	{
		$controls = array(
			'width'=>array('type'=>'text','name'=>'settings[width]','label'=>'控件宽度','value'=>'','valid'=>'','description'=>'控件的宽度，单位为<b>px</b>'),
			'height'=>array('type'=>'text','name'=>'settings[height]','label'=>'控件高度','value'=>'','valid'=>'','description'=>'控件的高度，单位为<b>px</b>'),
			'style'=>array('type'=>'text','name'=>'settings[style]','label'=>'控件样式','value'=>'','valid'=>'','description'=>'控件的style属性'),
			'class'=>array('type'=>'text','name'=>'settings[class]','label'=>'控件风格','value'=>'','valid'=>'','description'=>'控件的class属性'),
			'options'=>array('type'=>'textarea','name'=>'settings[options]','label'=>'控件选项','value'=>'选项名称1|选项值1','valid'=>'','description'=>'每行一个，值和数据使用<b> | </b>隔开'),
			'upload'=>array('type'=>'radio','name'=>'settings[upload]','options'=>array('1'=>'允许上传','0'=>'不允许上传'),'label'=>'上传设置','value'=>'0','valid'=>''),
	
		);
		$controls = zotop::filter('zotop.config.field.controls',$controls);

		return $controls;
	}

	public function position($id='',$template = '')
	{
	    $id = empty($id) ? $this->id : $id;
        
		$nodes = $this->getAll();
		
	    $tree = new tree($nodes);
	    
	    $position = $tree->getPosition($id);
	    if( empty($template) )
	    {
	        return $position;
	    }
	    
	    $str ='';
	    if( is_array($position) )
	    {
    	    foreach($position as $pos)
    	    {
    	        @extract($pos);
    	        eval("\$temp = \"$template\";");
    	        $str .= $temp;
    	    }
	    }
	    return $str;
	}

	public function newOrder($parentid)
	{
		return $this->max('order',array('parentid','=',$parentid))+1;
	}

	public function childNum($parentid)
	{
		return $this->count('parentid',$parentid);
	}
	
	public function childs($parentid='')
	{
	    if( empty($parentid) )
	    {
	        $parentid = $this->parentid;
	    }
	    
	    //读取子项
	    $childs =  $this->db()->select('*')->orderby('order')->where('parentid', '=', $parentid)->getAll();
	    
	    return $childs;
	}
	
	public function fields($parentid='')
	{
	    $fields = array();
	    //获取子结点
	    $childs = $this->childs($parentid);
	    
	    if ( is_array($childs) )
	    {
	    	foreach($childs as $child)
    	    {
    	        if ( $child['type'] != 'folder' )
    	        {
        	        $settings = json_decode($child['settings']);
        	        
        	        $field = array(
        	        	'type' => $child['type'],
        	            'name' => $child['id'],    
        	        	'label' => '<span title="'.$child['id'].'">'.$child['title'].'</span>',
        	        	'value' => $child['value'],
						'valid' => $child['valid'],
        	            'description' => $child['description'],
        	        );
        	        
        	        $field = array_merge($field, (array)$settings);
        	        
        	        $fields[] = $field;
    	        }
    	    }	        
	    }
	    return $fields;
	    
	}

	public function save($configs, $order=false)
	{
		//$configs = (array) $configs;
		$i = 0;
		$config = array();
		foreach($configs as $key=>$value)
		{
		    if( $order )
		    {
			    $config['order'] = ++$i;
		    }
			$config['value'] = $value;
			$this->update($config, str_replace('_','.',$key));
			
		}
		$this->cache(true);
		return true;
	}

	public function cache($reload=false)
	{
		$data = $this->db()->select('id','value')->getAll();		
		$data = arr::hashmap($data,'id','value');
		zotop::data('config', $data);
		zotop::reboot();
		return $data;		
	}
	
}
?>