<?php
$this->header();
$this->top();
$this->navbar();
?>
<style type="text/css">
	div.icon-area{float:left;width:50px;text-align:center;}
	div.label-area{float:left;width:180px;}
	div.field-area{float:left;}
	div.field-area h5{clear:both;}
</style>
<?php

form::header();

	table::header('list sortable');

	foreach($configs as $config)
	{
		$type = $config['type'];
		$description = $config['description'];
		$settings = (array)json_decode($config['settings']);
		$attrs = array(
			'type' => $config['type'] == 'folder' ? 'hidden' : $config['type'],
			'name'=>$config['id'],        
			'value'=>$config['value'],
			'valid'=>$config['valid'],
		);
		$attrs = array_merge($attrs,$settings);
		
		
		$column = array();
		$input = '';
		if($type == 'folder')
		{
			$input .=  '<div class="icon-area"><div class="zotop-icon zotop-icon-file folder"></div></div>';
			$input .=  '<a href="'.zotop::url('system/config/index/'.$config['id']).'"><b>'.$config['title'].'</b></a><h5>'.$config['description'].'</h5>';
			$input .=  field::get($attrs);
		}
		else
		{
			$input .= '<div class="icon-area"><div class="zotop-icon zotop-icon-file txt"></div></div>';
			$input .= '<div class="label-area"><b>'.$config['title'].'</b><h5 class="red">'.$config['id'].'</h5></div>';
			$input .= '<div class="field-area">'.field::get($attrs).'<span class="field-valid"></span><h5>'.$config['description'].'</h5>'.'</div>';
		}
		$column['input'] = $input;
		$column['manage edit'] = '<a href="'.zotop::url('system/config/edit/'.$config['id']).'" class="dialog">修改</a>';
		$column['manage delete'] = '<a href="'.zotop::url('system/config/delete/'.$config['id']).'" class="confirm">删除</a>';
		table::row($column,'select field');
		
	}
	table::footer();

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