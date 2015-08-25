<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<?php
form::header();
	
	block::header('选择数据表');

	echo '';
	echo '<table class="table">';
	echo '<tr><td class="w30 center"><input name="tables" class="selectAll select" type="checkbox" id="tables" checked="checked"/></td><td><label for="tables"><b>选择全部数据表</b></span></td><td colspan="4"></td></tr>';
	echo '<tr class="item">';

	$i=0;

	foreach($tables as $name=>$table)
	{
		if ( $i%3==0 )
		{
			echo '</tr><tr class="item">';
		}
		echo '<td class="w30 center"><input type="checkbox" name="tables[]" value="'.$table['name'].'" class="select" id="table'.$i.'" checked="checked"/></td><td><label for="table'.$i.'"><b>'.$table['name'].'</b><h5>'.$table['comment'].'</h5></label></td>';
		
		$i++;
	}
	echo '</tr>';
	echo '</table>';

	block::footer();

	block::header(array('title'=>'备份选项','class'=>'collapsed'));
		
		form::field(array(
			'name' => 'sizelimit',
			'type' => 'text',
			'value' => '2048',
			'valid' => 'required:true,number:true,min:100',
			'label' => '分卷长度',			
			'description' => '使用分卷备份时每个分卷文件的长度，单位 <b>KB</b>',
		));
		form::field(array(
			'name' => 'sqlcompat',
			'type' => 'radio',
			'options' => array(''=>'默认(MySQL5)', 'MYSQL40'=>'MySQL 3.23/4.0.x', 'MYSQL41'=>'MySQL 4.1.x/5.x',),
			'value' => '',
			'valid' => '',
			'label' => '语句格式',			
		));

	block::footer();
	
	form::buttons(
		array('type'=>'submit','value'=>'备 份')
	);
		
form::footer();
?>
<?php $this->bottom()?>
<?php $this->footer();?>