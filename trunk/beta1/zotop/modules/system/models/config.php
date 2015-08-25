<?php
class system_model_config extends model
{
	protected $_key = 'id';
	protected $_table = 'config';

	public function controls()
	{

		$controls = array();

		$controls['folder']['name'] = '文件夹';
		$controls['text']['name'] = '单行文本输入控件';

		$controls['textarea']['name'] = '多行文本输入控件';

		$controls['select']['name'] = '单选下拉控件';
		$controls['select']['attr']['options'] = array('type'=>'textarea','label'=>'控件选项','value'=>'选项名称1|选项值1','description'=>'每行一个，选项名称和选项值使用<b>|</b>隔开');
		
		$controls['radio']['name'] = '单选按钮控件';
		$controls['radio']['attr']['options'] = array('type'=>'textarea','label'=>'控件选项','value'=>'选项名称1|选项值1','description'=>'每行一个，选项名称和选项值使用<b>|</b>隔开');

		$controls['checkbox']['name'] = '复选框控件';
		$controls['checkbox']['attr']['options'] = array('type'=>'textarea','label'=>'控件选项','value'=>'选项名称1|选项值1','description'=>'每行一个，选项名称和选项值使用<b>|</b>隔开');

		$controls['image']['name'] = '图片上传控件';
		$controls['image']['attr']['upload'] = array('type'=>'radio','label'=>'图片上传','options'=>array(true=>'允许上传',false=>'不允许上传'));

		$controls['editor']['name'] = '富文本编辑器';
		$controls['editor']['attr']['toolbar'] = array('type'=>'radio','label'=>'编辑器类型','options'=>array('basic'=>'简洁型','standard'=>'标准型','full'=>'全功能型'),'value'=>'standard');

		$controls = zotop::filter('config.controls',$controls);

		return $controls;	
	}

	public function getControlTypes()
	{
		$types = array();
		$controls = $this->controls();

		foreach( $controls as $type=>$control )
		{
			$types[$type] = $control['name'].'('.$type.')';
		}
		
		return $types;
	}

	public function getControlAttrs($type,$data=array())
	{
		$controls = $this->controls();
		$attrs = (array)$controls[$type]['attr'];
		
		foreach( $attrs as $t=>$attr )
		{
			if ( is_array($attr) )
			{
				$a = array();

				if ( !isset($attr['name']) )
				{
					$a['name'] = "settings[$t]";

					if ( is_array($data) && isset($data['settings'][$t]) )
					{
						$a['value'] = $data['settings'][$t];
					}
				}
				elseif ( is_array($data) && isset($data[$t]) )
				{
					$a['value'] = $data[$t];
				}
				

				$attrs[$t] = array_merge($attr,$a);
			}
		}
		return $attrs;
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