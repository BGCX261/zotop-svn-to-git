<?php
class usergroup_model extends model
{
	protected $primaryKey = 'id';
	protected $tableName = 'usergroup';
	
	
	public function getIndex($type = '')
	{
		$groups = zotop::data('cache.usergroup');
				
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
	
	public function cache()
	{
	    $data = array();
	    $rows = $this->getAll();
	    foreach( $rows as $row )
	    {
	       $data[$row['id']] = $row; 
	    }
	    zotop::data('usergroup', $data);	    
	    return $data;
	}

}
?>