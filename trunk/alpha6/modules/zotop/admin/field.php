<?php
class zotop_controller_field extends controller
{
    
    public function actionIndex()
    {
      
    }

	public function actionSelect($type='text')
	{    
        $types = array(
			'text'=>'单行文本输入框',
			'textarea'=>'多行文本输入框',
			'select'=>'单选下拉选择框',
			'radio'=>'单项选择框',
			'checkbox'=>'多项选择框',
			'editor'=>'富文本编辑器',
			'image'=>'图片上传控件',
			'date'=>'日期选择控件',

		);
		$attrs = array(
			'text'=>'width,style,class',	
			'textarea'=>'width,height,style,class',
			'select'=>'options,style,width',
			'editor'=>'width,height',
		);

		$controls = array(
			'width'=>array('type'=>'text','name'=>'width','label'=>'控件宽度','value'=>'','valid'=>'','description'=>'控件的宽度，单位为<b>px</b>'),
			'height'=>array('type'=>'text','name'=>'height','label'=>'控件高度','value'=>'','valid'=>'','description'=>'控件的高度，单位为<b>px</b>'),
			'style'=>array('type'=>'text','name'=>'style','label'=>'控件样式','value'=>'','valid'=>'','description'=>'控件的style属性'),
			'class'=>array('type'=>'text','name'=>'class','label'=>'控件风格','value'=>'','valid'=>'','description'=>'控件的class属性'),
			'options'=>array('type'=>'textarea','name'=>'options','label'=>'控件选项','value'=>'','valid'=>'','description'=>'每行一个，值和数据使用<b>::</b>隔开'),
			
		);

		$page = new dialog();
        $page->set('title','选择控件');
		$page->set('type',$type);
		$page->set('types',$types);
		$page->set('controls',$controls);
		$page->set('attrs',$attrs[$type]);
        $page->display();   		
	}
}
?>