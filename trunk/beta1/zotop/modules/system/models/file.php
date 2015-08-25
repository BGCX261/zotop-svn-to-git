<?php
class system_model_file extends model
{
	protected $_key = 'id';
	protected $_table = 'file';

	public $upload ='';
	public $files = array();

	

	public function __construct()
	{
		//调用父类的初始化函数
		parent::__construct();
		
		//初始化上传对象
		$this->upload = new upload();		
		$this->upload->files = array();
		$this->upload->savepath = trim(zotop::config('system.upload.dir'),'/').'/'.trim(zotop::config('system.upload.filepath'),'/');
		$this->upload->allowexts = 'jpg|jpeg|gif|bmp|png|doc|docx|xls|ppt|pdf|txt|rar|zip';
		$this->upload->maxsize = (int)zotop::config('system.upload.maxsize');
		$this->upload->overwrite = true;
		$this->upload->filename = 'md5';
		$this->upload->field = 'file';
		$this->upload->error = 0;
		$this->upload->msg = '';
	}

	public function upload()
	{		
		//上传文件
		$this->files = $this->upload->save();

		//设置错误
		if ( $this->upload->error() )
		{
			$this->error($this->upload->error(), $this->upload->msg());
		}

		if ( is_array($this->files) )
		{
			//保存文件		
			$ip = ip::current();
			$userid = $this->_user['id'];
			$description =  (array) $this->description;

			foreach($this->files as $key=>$file)
			{

					$file['id'] = $file['id'];
					$file['parentid'] = $file['id'];;
					$file['globalid'] = $this->globalid;
					$file['groupid'] = $this->folderid;
					$file['field'] = $this->field;
					$file['type'] = file::type($file['path']);
					$file['description'] = empty($description[$key]) ? $file['description'] : $description[$key];
					$file['userid'] = $userid;
					$file['status'] = (int)$this->status;
					$file['createip'] = $ip;
					$file['createtime'] = TIME;

					if ( $file['type'] == 'image' || preg_match('/^(jpeg|jpeg|png|gif|bmp|ico|tif|tiff|psd|xbm|xcf)$/', $file['ext']) )
					{
						$info = image::info($file['path']);

						$file['width'] = (int)$info['width'];
						$file['height'] = (int)$info['height'];
					}
					
					$this->insert($file);

			}

			return $this->files;
		}

		return array();	
	}


	public function getList($where, $page=1, $pagesize=20)
	{
		zotop::dump($this->_user);
	}

	public function types()
	{
		return (array)file::types();
	}


    public function delete($where)
    {
        if( !is_array($where) )
        {
            $key = $this->key();
            
            if( empty($where) )
            {
               $where = array($key,'=',$this->$key); 
            }
            
            if( is_numeric($where) || is_string($where) )
            {
               $where = array($key,'=',$where);
            }            
        }

		$files = $this->db()->select('id,path')->where($where)->getAll();

		foreach($files as $file)
		{
			if ( $this->count(array('path','=',$file['path'])) == 1 )
			{
				file::delete(ZOTOP_PATH_ROOT.DS.$file['path']);
			}

			$this->db()->where(array('id','=',$file['id']))->delete(); 
		}               
        return  true;    
    }


	public function totalsize($where='')
	{
		$totalsize = $this->db()->select('sum(size) as num')->where($where)->getOne();

		return $totalsize;
	}

	//change globalid
	public function refresh($globalid,$new_globalid='')
	{
		if ( empty($new_globalid) || $globalid==$new_globalid )
		{
			$this->update(array('status'=>1),array('globalid','=',$globalid));
		}
		else
		{
			$this->update(array('globalid'=>$new_globalid,'status'=>1),array('globalid','=',$globalid));			
		}
	}


}
?>