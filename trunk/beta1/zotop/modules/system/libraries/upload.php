<?php
class upload_base
{
	protected $files = array();
	protected $uploads = 0;
	protected $error = 0;
	protected $data = array(
		'field' => 'file',
		'maxsize' => 0,
		'savepath' => 'uploads/',
		'overwrite' => true,
		'allowexts' => 'jpg|jpeg|gif|bmp|png|doc|docx|xls|ppt|pdf|txt|rar|zip',
		'filename' => 'md5',
	);

    /**
     * 初始化控制器
     * 
     */
    public function __construct($config=array())
    {
        //设置上传参数
		$this->data = array_merge($this->data, (array)$config);		
    } 


    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->data[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 上传保存过程
     * 
     * @param string $field 上传文件字段名称
     * @return mixed
     */
	public function save($field='', $file='')
	{
		$uploadfiles = array();
		
		//字段名称
		$field = empty($field) ? $this->field : $field;



		//初始化上传数据
		$this->uploads = count($_FILES[$field]['name']);


		if ( 0 == $this->uploads )
		{
			$this->error = 5;//没有文件被上传，上传文件字段错误
			return false;
		}

		if ( 1 == $this->uploads )
		{
			if ( $_FILES[$field]['error'] === 0 )
			{
				$uploadfiles[0] = array(
					'id' => md5_file($_FILES[$field]['tmp_name']),
					'name' => $_FILES[$field]['name'],
					'tmp_name' => $_FILES[$field]['tmp_name'],
					'type' => $_FILES[$field]['type'],
					'size' => $_FILES[$field]['size'],
					'ext' => $this->getFileExt($_FILES[$field]['name']),
					'description' => $this->getFileDescription($_FILES[$field]['name']),
					'error' => $_FILES[$field]['error']				
				);
			}
		}
		else
		{
			foreach( $_FILES[$field]['name'] as $key => $value )
			{
				if ( $_FILES[$field]['error'][$key] === 0 )
				{
					$uploadfiles[$key] = array(
						'id' => md5_file($_FILES[$field]['tmp_name'][$key]),
						'name' => $_FILES[$field]['name'][$key],
						'tmp_name' => $_FILES[$field]['tmp_name'][$key],
						'type' => $_FILES[$field]['type'][$key],
						'size' => $_FILES[$field]['size'][$key],
						'ext' => $this->getFileExt($_FILES[$field]['name'][$key]),
						'description' => $this->getFileDescription($_FILES[$field]['name'][$key]),
						'error' => $_FILES[$field]['error'][$key]				
					);
				}
			}
		}		
		
		if ( empty($uploadfiles) )
		{
			$this->error = 4;//没有选择上传文件
			return false;
		}

		//上传
		foreach($uploadfiles as $key=>$file)
		{
	
			//格式检查
			if ( !$this->isAllowedFile($file) )
			{
				$this->error = 10; //上传格式错误
				return false;
			}
			
			//文件大小检查
			if ( $this->maxsize() && $file['size'] > $this->maxsize() )
			{
				$this->error = 11; //不被允许的格式
				return false;
			}

			
			//文件检查
			if(!$this->isUploadedFile($file['tmp_name']))
			{
				$this->error = 12;
				return false;
			}

			
			
			$savepath = $this->savepath($file);
			$filename = $this->filename($file);
						
			$filepath = $savepath.$filename; // uploads/2010/0307/dsfsdfasdfsdfsd.jpg

		
			//获取存储的实际文件
			$savefile = $this->savefile($savepath,$filename); // E://wwwroot/zotopcms/uploads/dsfsdfasdfsdfsd.jpg
			//$savefile = preg_replace("/(php|phtml|php3|php4|jsp|exe|dll|asp|cer|asa|shtml|shtm|aspx|asax|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2", $savefile);

			//不允许覆写则调过该过程
			if ( !$this->overwrite && file_exists($savefile) )
			{
				$this->error = 13;
				return false;
			};

			//移动上传文件
			if( move_uploaded_file($file['tmp_name'], $savefile) || @copy($file['tmp_name'], $savefile) )
			{
				@chmod($savefile, 0644);
				@unlink($file['tmp_name']);
				$this->files[] = array(
					'id'=>md5($filepath.time()),
					'guid'=>$file['id'],
					'name'=>$file['name'],
					'path'=>$filepath,
					'type'=>$file['type'],
					'size'=>$file['size'],
					'ext'=>$file['ext'],
					'description'=>$file['description'],
					'url'=>$filepath
				);
			}
			
		} // foreach
		
		return (array)$this->files;
	}

	public function maxsize()
	{
		$maxsize = $this->maxsize; //单位kb
		$maxsize = $maxsize * 1024;
		return $maxsize;
	}

    /**
     * 上传保存位置
     * 
     * @return mixed
     */
	public function savepath($file='')
	{	

		$savepath = $this->savepath;
		$savepath = $this->parsePath($savepath); //替换特殊变量		
		return $savepath;
	}

	public function savefile($savepath, $filename)
	{
		//返回实际目录
		$dir = ZOTOP_PATH_ROOT.DS.$savepath;

		//目录检测
		if( !is_dir($dir) && !folder::create($dir, 0777) )
		{
			$this->error = 8; //目录不存在且无法自动创建
			return false;
		}

		@chmod($dir, 0777);
		
		if(!is_writeable($dir) && ($dir != '/'))
		{
			$this->error = 9; //不可写
			return false;
		}
		
		$savefile = $dir.$filename;

		return $savefile;
	}

	public function filename($file)
	{
		$ext =  //获取原格式名称
		
		$filename = $this->filename; //获取文件命名方式	
		
		if ( $filename == 'time' )
		{
			$newfilename = date('Ymdhis').rand(1000, 9999).'.'.$file['ext'];
		}
		elseif ( $filename == 'md5' ||  $filename == 'id' )
		{
			$newfilename = $file['id'].'.'.$file['ext'];
		}
		else
		{
			$newfilename = $this->cleanFileName($file['name']);
		}
		
		return $newfilename;
	}


    /**
     * 获取文件扩展名
     * 
     * @return mixed
     */
	public function getFileExt($filename)
	{
		$x = explode('.', $filename);

		return strtolower(end($x));
	}

	public function getFileDescription($filename)
	{
		$x = explode('.', $filename);

		return current($x);
	}
	
	public function cleanFileName($filename)
	{
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						"%20",
						"%22",
						"%3c",		// <
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (
						"%29", 		// )
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);
					
		$filename = str_replace($bad, '', $filename);

		return stripslashes($filename);		
	}

	/*
	 * 接续文件路径，支持变量，如：$upload = 上传目录 , $type = 文件类型 ，$year = 年 ，$month = 月 ，$day = 日
	 * 
	 * @param string $filename，
	 */
	public function parsePath($savepath)
	{
		$p = array(
			'$year' => date("Y"),
			'$month' => date("m"),
			'$day' => date("d"),
			'$Y' => date("Y"),
			'$m' => date("m"),
			'$d' => date("d"),
	    );

	    $path = strtr($savepath, $p);

		$path = str_replace("\\", "/", $path);
		$path = str_replace("///", "/", $path);
		$path = str_replace("//", "/", $path);
	    $path = rtrim($path, '/').'/';

		return $path;
	}



	public function isUploadedFile($file)
	{
		return is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file));
	}

	public function isAllowedFile($file)
	{
		$allowexts = $this->allowexts;

		if ( is_array($allowexts) )
		{
			$allowexts = implode('|', $allowexts);
		}
		else
		{
			$allowexts = str_replace(array(',','/','\\'), '|', $allowexts);
		}

		$fileext = $file['ext'];
		
		return preg_match("/^(".strtolower($allowexts).")$/", $fileext);
	}

	public function error()
	{
		return $this->error;
	}

	function msg()
	{
		$messages = array(
			0 => zotop::t('文件上传成功'),
			1 => zotop::t('上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值'),
			2 => zotop::t('上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值'),
			3 => zotop::t('文件只有部分被上传'),
			4 => zotop::t('请选择要上传文件'),
			5 => zotop::t('没有文件被上传'),
			6 => zotop::t('找不到临时文件夹'),
			7 => zotop::t('文件写入临时文件夹失败'),
			8 => zotop::t('目录不存在且无法自动创建'),
			9 => zotop::t('目录没有写入权限'),
			10 => zotop::t('不允许上传该类型文件'),
			11 => zotop::t('文件超过了管理员限定的大小'),
			12 => zotop::t('非法上传文件'),
			13 => zotop::t('文件已经存在，且系统不允许覆盖已有文件'),
		);
		return $messages[$this->error];
	}


	

}
?>