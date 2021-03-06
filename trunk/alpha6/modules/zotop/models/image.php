<?php
class zotop_model_image extends model
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
		$this->upload->savepath = trim(zotop::config('upload.dir'),'/').'/'.trim(zotop::config('upload.filepath'),'/');
		$this->upload->alowexts = 'jpg|jpeg|gif|bmp|png';
		$this->upload->maxsize = 0;
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
				if( !$this->isExist($this->_key, $file['id']) )
				{
					$image = $this->info($file['path']);

					$file['id'] = $file['id'];
					$file['parentid'] = $file['id'];
					$file['globalid'] = $this->globalid;
					$file['folderid'] = $this->folderid;
					$file['field'] = $this->field;
					$file['type'] = 'image';
					$file['width'] = $image['width'];
					$file['height'] = $image['height'];
					$file['description'] = empty($description[$key]) ? $file['description'] : $description[$key];
					$file['userid'] = $userid;
					$file['createip'] = $ip;
					$file['createtime'] = TIME;
					
					$this->insert($file);
				}			
			}

			return $this->files;
		}

		return array();
	}

	public function info($image)
	{
		$info = @getimagesize(ZOTOP_PATH_ROOT.DS.$image);
		
		if ( is_array($info) )
		{
			return array('width' => $info[1],'height' => $info[0]);
		}
		return array('width' => 0,'height' => 0);
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

		$images = $this->db()->select('id,path')->where($where)->getAll();
		
		if( is_array($images) )
		{
		
			foreach($images as $image)
			{
				file::delete(ZOTOP_PATH_ROOT.DS.$image['path']);
			}

			return  $this->db()->where($where)->delete();  

		}
		
		return false;           
    }

}
?>