<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php

form::header();

	table::header('list sortable');
	foreach($configs as $config)
	{
		$type = $config['type'];
		$description = $config['description'];
		$settings = (array)json_decode($config['settings']);
		$attrs = array(
			'name'=>$config['id'],        
			'value'=>$config['value'],
			'valid'=>$config['valid'],
		);
		$attrs = array_merge($attrs,$settings);
		
		
		$column = array();
		$input = '';
		if($type == 'folder')
		{
			$input .=  '<div style="float:left;width:50px;height:100%;text-align:center;"><div class="zotop-icon zotop-icon-file folder"></div></div>';
			$input .=  '<a href="'.zotop::url('zotop/config/index',array('parentid'=>$config['id'])).'"><b>'.$config['title'].'</b></a><h5>'.$config['description'].'</h5>';
			$input .=  field::get('hidden',$attrs);
		}
		else
		{
			$input .= '<div style="float:left;width:50px;height:100%;text-align:center;"><div class="zotop-icon zotop-icon-file txt"></div></div>';
			$input .= '<div style="float:left;width:160px;height:100%;"><b>'.$config['title'].'</b><h5 class="red">'.$config['id'].'</h5></div>';
			$input .= '<div style="float:left;">'.field::get($type,$attrs).'<span class="field-valid"></span><h5>'.$config['description'].'</h5>'.'</div>';
		}
		$column['input'] = $input;
		$column['manage edit'] = '<a href="'.zotop::url('zotop/config/edit',array('id'=>$config['id'])).'" class="dialog">修改</a>';
		$column['manage delete'] = '<a href="'.zotop::url('zotop/config/delete',array('id'=>$config['id'])).'" class="confirm">删除</a>';
		table::row($column,'select field');
		
	}
	table::footer();

form::bottom('<span class="zotop-icon zotop-icon-notice"></span>拖动并保存，改变排序');	
form::buttons(
	array('type'=>'submit','value'=>'保 存'),
	array('type'=>'back')
);
form::footer();

?>

<?php
$this->bottom();
$this->footer();
?>