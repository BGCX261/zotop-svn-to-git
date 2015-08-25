<?php $this->header();?>
<div id="main">
<div id="main-inner">
<?php $this->top()?>
<?php $this->navbar()?>
<?php 
				form::header(array('class'=>'list'));
					$column = array();
					$column['select'] = html::checkbox(array('name'=>'id','class'=>'selectAll'));
					$column['name'] = '字段名称';
					$column['type w150'] = '字段类型';
					$column['manage pri'] = '主键';
					$column['manage index'] = '索引';
					$column['manage unique'] = '唯一';
					$column['manage fulltext'] = '全文';
					$column['manage edit'] = '修改';
					$column['manage delete'] = '删除';


					table::header('list',$column);
					foreach($fields as $field)
					{
						$column = array();
						$column['select'] = html::checkbox(array('name'=>'id[]','class'=>'select'));
						$column['name'] = '<a href="'.zotop::url('database/field/edit/'.$tablename.'/'.$field['name']).'" class="dialog" title="注释：'.$field['comment'].'&#13;默认：'.$field['default'].'&#13;空值：'.$field['null'].'&#13;整理：'.$field['collation'].'"><b class="'.$field['key'].'">'.$field['name'].'</b></a><h5>'.$field['comment'].'</h5>';
						$column['type w150'] = $field['type'].(empty($field['length']) ? '' : '('.$field['length'].')');
						$column['manage pri'] = '<a href="'.zotop::url('database/field/primaryKey/'.$tablename.'/'.$field['name']).'" class="confirm {content:\'<h2>确定要将该字段设置为主键？</h2>\'}">主键</a>';
						$column['manage index'] = '<a href="'.zotop::url('database/field/addIndex/'.$tablename.'/'.$field['name']).'" class="confirm {content:\'<h2>确定要索引该字段？</h2>\'}">索引</a>';
						$column['manage unique'] = '<a href="'.zotop::url('database/field/unique/'.$tablename.'/'.$field['name']).'" class="confirm {content:\'<h2>确定要将该字段设置为唯一？</h2>\'}">唯一</a>';

						if( stripos((string)$field['type'],'varchar')!==false || stripos((string)$field['type'],'text')!==false )
						{
							$column['manage fulltext'] = '<a href="'.zotop::url('database/field/fulltext/'.$tablename.'/'.$field['name']).'" class="confirm {content:\'<h2>确定要将该字段设置为全文索引？</h2>\'}">全文</a>';
						}
						else
						{
							$column['manage fulltext'] = '<a class="disabled">全文</a>';
						}
						$column['manage edit'] = '<a href="'.zotop::url('database/field/edit/'.$tablename.'/'.$field['name']).'" class="dialog">修改</a>';
						$column['manage delete'] = '<a href="'.zotop::url('database/field/delete/'.$tablename.'/'.$field['name']).'" class="confirm">删除</a>';
						table::row($column,'select');
					}
					table::footer();


				form::buttons(
				    array('type'=>'submit','value'=>'浏览选中项')
				);

				form::footer();

		$this->bottom();
?>
</div>
</div>
<div id="side">
<?php 

			box::header('数据表信息');
				table::header();
				table::row(array('w60 bold'=>'名称','2'=>''.$table['name'].''));
				table::row(array('w60 bold'=>'大小','2'=>''.format::byte($table['size']).''));
				table::row(array('w60 bold'=>'记录数','2'=>'<b>'.$table['rows'].'</b> '));
				table::row(array('w60 bold'=>'整理','2'=>''.$table['collation'].''));
				table::row(array('w60 bold'=>'创建时间','2'=>''.$table['createtime'].''));
				table::row(array('w60 bold'=>'更新时间','2'=>''.$table['updatetime'].''));
				//table::row(array('w60 bold'=>'注释','2'=>''.$table['comment'].''));
				table::footer();
			box::footer();

			box::header('索引信息');
				$column = array();
				//$column['i w10'] = '';
				$column['field'] = '字段';
				$column['type w50'] = '类型';
				$column['manage dropindex'] = '删除';
				table::header('list',$column);
				foreach($indexes as $index)
				{
					$column = array();
					//$column['i w10 center'] = '>';
					$column['field'] = '<b>'.$index['field'].'</b>';
					$column['type w50'] = $index['type'];
					$column['manage dropindex'] = '<a href="'.url::build('database/field/dropindex/'.$tablename.'/'.$index['name']).'" class="confirm">删除</a>';
					table::row($column,'select');
				}
				table::footer();
		box::footer();
?>
</div>
<?php $this->footer();?>