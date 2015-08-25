<?php
$this->header();
$this->top();
$this->navbar();
?>

<?php
//zotop::dump($configs);
if(empty($configs))
{
	echo('<div class="zotop-empty"><span>暂时没有数据</span></div>');
}
else
{
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
		);
		$attrs = array_merge($attrs,$settings);
		
		
		$column = array();
		$input = '';
		if($type == 'folder')
		{
			$input .=  '<div style="float:left;width:50px;height:100%;text-align:center;"><img src="'.url::decode('$theme/image/fileext/big/folder.gif').'" width="32px"></div>';
			$input .=  '<a href="'.zotop::url('zotop/config/index',array('parentid'=>$config['id'])).'"><b>'.$config['title'].'</b></a><h5>'.$config['description'].'</h5>';
			$input .=  field::get('hidden',$attrs);
		}
		else
		{
			$input .= '<div style="float:left;width:50px;height:100%;text-align:center;"><img src="'.url::decode('$theme/image/fileext/big/file.gif').'" width="32px"></div>';
			$input .= '<div style="float:left;width:160px;height:100%;"><b>'.$config['title'].'</b><h5 class="red">'.$config['id'].'</h5></div>';
			$input .= '<div style="float:left;">'.field::get($type,$attrs).'<h5>'.$config['description'].'</h5>'.'</div>';
		}
		$column['input'] = $input;
		$column['manage edit'] = '<a href="'.zotop::url('zotop/config/edit',array('id'=>$config['id'])).'" class="dialog">修改</a>';
		$column['manage delete'] = '<a href="'.zotop::url('zotop/config/delete',array('id'=>$config['id'])).'" class="confirm">删除</a>';
		table::row($column,'select');
		
	}
	table::footer();
	form::bottom('<span class="zotop-tip">拖动并保存，改变顺序</span>');
	form::footer(array(
		array('type'=>'submit','value'=>'保 存'),
		array('type'=>'back')
	));
}
?>

<?php
$this->bottom();
$this->footer();
?>