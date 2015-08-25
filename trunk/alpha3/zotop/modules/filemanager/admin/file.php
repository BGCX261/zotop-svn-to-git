<?php
class file_controller extends controller
{
    public function __init()
    {
        field::set('source',array($this,'source'));
    }

	public static function source($attrs)
	{
	   $html = array();
	   $html[] = '';
	   $html[] = '<div style="height:460px;overflow:hidden;">';	   
	   $html[] = '<div id="SourceEditorPannel">正在加载编辑器，请稍后……</div>';
	   $html[] = html::script(url::common().'/js/swfobject.js');
	   $html[] = html::script(url::module().'/admin/js/file.js');
	   $html[] = field::textarea($attrs);
	   $html[] = '</div>';
	   return implode("\n",$html);
	}
    
    public function navbar()
    {
        return array(
			array('id'=>'edit','title'=>'文件编辑','href'=>url::build('filemanager/file/edit')),
		);
    }
    
    public function onEdit($file)
    {
        if(form::isPostBack())
        {
            
            $content = request::post('source');
            
            msg::success('保存测试','测试，继续编辑或者返回'.zotop::dump($content,true),'reload');
        }
        
        $source = file::read(ROOT.$file);
        
        $page['title'] = '文件编辑器';
        

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header(array('class'=>'sourceEditor'));
			
			form::field(array(
			   'type'=>'label',
			   'label'=>zotop::t('文件名称'),
			   'name'=>'filename',
			   'value'=>$file,
			   'valid'=>'',
			   'description'=>zotop::t(''),
			));			

			form::field(array(
			   'type'=>'source',
			   'label'=>zotop::t('文件内容'),
			   'name'=>'source',
			   'value'=>$source,
			   'valid'=>'required:true',
			   'description'=>zotop::t(''),
			));
			
			
			form::buttons(
			   array('type'=>'submit','value'=>'保存文件'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();
    }
}
?>