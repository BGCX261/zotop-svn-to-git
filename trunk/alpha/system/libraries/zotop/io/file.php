<?php
class BaseFile extends Base
{
	
	/**
	 * 获取文件的扩展名
	 * @param string $file 文件名称
	 * @return string 
	 */
	public static function ext($file)
	{
		$dot = strrpos($file, '.') + 1;
		return substr($file, $dot);
	}

	/**
	 * 获取文件名称 , 第二个参数控制是否返回文件扩展名，默认为返回带扩展名的文件名称
	 * @param string $file
	 * @param boolean $stripext
	 * @return string
	 */
	public static function name($file,$stripext=false)
	{
		$name=basename($file);
		if($stripext)
		{
			$ext = self::ext($file);
			$name=basename($file,'.'.$ext);
		}
		return $name;
	}

	/**
	 * 判断文件是否存在
	 * @param string $file
	 * @return boolean
	 */
	public static function exists($file)
	{
	    if (empty($file)) return false;
	    else return is_file(path::clean($file));
	}

	
	/**
	 * 读取文件内容
	 * 
	 * @param string $file
	 * @return string
	 */
	public static function read($file)
	{
	   if (self::exists($file))
	   {
	       $file = path::clean($file);
	       return @file_get_contents($file);    
	   }
	   else 
	   {
	       return false;
	   }
	   
	}

	
	/**
	 * 写入文件
	 * 
	 * @param string $file
	 * @param string $content
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public static function write($file , $content='' , $overwrite=TRUE)
	{
	    //当目录不存在的情况下先创建目录
	    if (!file_exists(dirname($file)))
		{
			folder::create(dirname($file));
		}
		
		if (!self::exists($file) || $overwrite)
		{
		    $file = path::clean($file);
		    if (is_writable($file))
		    {
		        return @file_put_contents($file,$content);    
		    }
		    else 
		    {
		        return false;
		    }
		}
		else
		{
            return false;
		}
	    
	}
	
	
	/**
	 * 删除文件
	 * @param string $file
	 * @return boolean
	 */
	public static function delete($file)
	{
	    
	    if (self::exists($file))
	    {
	        $file = path::clean($file);
	        return @unlink($file);    
	    }else 
	    {
	        return false;
	    }
	}
	
	/**
	 * 返回目录下的全部文件的数组,当level为0时候返回全部子文件夹目录
	 * @param string $path
	 * @param string $ext
	 * @param int $level
	 * @return array
	 */
	public static function brower($path,$ext='',$level=1)
	{
	    if ($level<0) return false;
	    
	    $path = path::clean($path);
	    if (is_dir($path))
	    {
	        $fso  = opendir($path);
	        $arr  = array();
	        while($name=readdir($fso))
	        {
	             if ($name!='.' && $name!='..')
	             {
	                 $tmp = $path.DS.$name;
	                 if (is_dir($tmp))
	                 {
		                 if ($level==0)
				         {
				             $arr[$name] = self::brower($tmp,$ext,$level);      
				         }
				         else
				         {
				             if ($level!=1) $arr[$name] = self::brower($tmp,$ext,$level-1);            
				         }                 
	                 }
	                 else
	                 {
	                     if (self::ext($name)==$ext || empty($ext)) $arr[] = $name;
	                 }
	             }       
	        }
	        return $arr;  
	    }
	    else
	    {
	        return false;
	    }    
	}


	public static function copy()
	{
	    return false;
	}

	public static function move()
	{
            return false;
	}
    
    /**
     * 上传文件
     * 
     * @param string $name  FILE字段名称
     * @param string $path  上传的路径
     * @param string $ext   扩展名
     * @param boolean $rename 是否重新命名
     * @return array 
     */
    public static function upload($name,$path,$ext,$rename=true)
    {
	    if (!file_exists(dirname($path)))
		{
			folder::create(dirname($path));
		}
		$ext = explode(',',$ext);
		
		$files = $_FILES[$name];
		$attachments = array();
		//转换数组
		if(is_array($files['name']))
		{
                    foreach($files as $key => $var)
                    {
                            foreach($var as $id => $val)
                            {
                               $attachments[$id][$key] = $val;
                            }
                    }
		}
		else
		{
		    $attachments[] =$files; 
		}
        
		
		
		//上传
		$return = array();
		foreach ($attachments as $k=>$file)
		{
		    if (in_array(self::ext($file['name']),$ext))
		    {
		        $tmp = $path;
		        if ($rename)
		        {
		            $tmp .=DS.rand::string(10).self::ext($file['name']);
		        }
		        else
		        {
		            $tmp .=DS.$file['name'];
		        }
                        
		        @move_uploaded_file($file['name'],$tmp);
                        $return[] = $tmp;
                        @unlink($file['tmp_name']);
		    }
		    else
		    {
		        $return[] = false;
		    }
		}
		
		return $return ;
	}

	public static function find()
	{
	    
	}

}
?>