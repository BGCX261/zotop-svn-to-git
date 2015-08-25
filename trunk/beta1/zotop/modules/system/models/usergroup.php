<?php
class system_model_usergroup extends model
{
	protected $_key = 'id';
	protected $_table = 'usergroup';
	
	
	public function getIndex($type = '')
	{
		$groups = zotop::data('usergroup');
				
		foreach($groups as $group)
		{
		    if( !empty($type) )
		    {
		        if( $group['type'] == $type && $group['status'] >= 0 )
    		    {
    		        $index[$group['id']] = $group['title'];
    		    }		        
		    }
		    else
		    {
		        $index[$group['id']] = $group['title'];
		    }			
		}
		
		return $index;
		
	}
	
	public function cache($reload=false)
	{
	    $name = $this->table();
	    
	    $data = zotop::data($name);
	    
		//设置缓存数据
		if ( $reload || $data===null )
		{
			$data = $this->getAll($sql);
			$data = arr::hashmap($data, $this->key());
			
    		if( is_array($data) )
    		{
    		    zotop::data($name, $data);
    		}
		}
		
		return $data;	
	}

}
?>