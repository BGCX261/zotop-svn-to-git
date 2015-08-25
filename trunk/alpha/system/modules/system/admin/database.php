<?php
class DatabaseController extends controller
{

    public function navbar()
	{
		return array(
		  array('id'=>'table','title'=>'数据表管理','href'=>url::build('system/database')),
		  array('id'=>'info','title'=>'数据表创建','href'=>url::build('system/db/create'),'class'=>'dialog'),
	   );
	}

	public function onDefault()
    {

	   $tables = $this->db->table()->get(true);

       $header['title'] = '数据库管理';
       page::header($header);

	   page::add('<div id="page" class="clearfix">');
	   page::add('<div id="side">');

		   block::header('数据库基本信息');
				table::header();
				table::row(array('w60'=>'数据库名称','2'=>''.$this->db->config['database'].''));
				table::row(array('w60'=>'数据库版本','2'=>''.$this->db->version(true).''));
				table::row(array('w60'=>'数据库大小','2'=>'<b>'.$this->db->size().'</b> '));
				table::row(array('w60'=>'数据表个数','2'=>'<b>'.count($tables).'</b> 个'));
				table::footer();
		   block::footer();

	   page::add('</div>');
	   page::add('<div id="main">');

	   page::top();
	   page::navbar($this->navbar(),'table');

		//zotop::dump($tables);

				form::header(array('class'=>'ajax'));
					$column['select'] = '<input name="id" class="selectAll" type="checkbox"/>';
					$column['name'] = '数据表名称';
					$column['size  w60'] = '大小';
					$column['Rows  w60'] = '记录数';
					$column['Engine  w60'] = '类型';
					$column['Collation  w100'] = '整理';
					$column['manage view w60'] = '浏览';
					$column['manage delete'] = '删除';

					table::header('list',$column);
					foreach($tables as $table)
					{
						$size   =  $table['Data_length'] + $table['Index_length'];

						$column = array();
						$column['select'] = '<input name="id[]" class="select" type="checkbox"/>';
						$column['name'] = '<b>'.$table['Name'].'</b><h5>'.$table['Comment'].'</h5>';
						$column['size w60'] = (string)format::size($size);
						$column['Rows  w60'] = $table['Rows'];
						$column['Engine  w60'] = $table['Engine'];
						$column['collation  w100'] = $table['Collation'];
						$column['manage view w60'] = '<a href="'.url::build('system/database/table/record').'">浏览</a>';
						$column['manage delete'] = '<a href="'.url::build('system/database/table/delete').'" class="confirm">删除</a>';
						table::row($column);
					}
					table::footer();
					page::add('<div style="height:200px;"></div>');
					form::buttons(
						array('type'=>'select','style'=>'width:180px','options'=>array('check'=>'优化','delete'=>'删除')),
						array('type'=>'submit','value'=>'执行操作')
						);
				form::footer();

       page::bottom();
	   page::add('</div>');
	   page::add('</div>');
       page::footer();
    }

	public function table()
	{}
}
?>