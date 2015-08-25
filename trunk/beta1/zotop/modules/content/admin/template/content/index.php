<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<style type="text/css">
table.list td.title span.title{
	display:block;
	width:80%;
}
table.list td.title span.zotop-icon{
	float:right;
}
span.zotop-icon-status0{background-position:-198px -6px;}
span.zotop-icon-status-1{background-position:-102px -6px;}
span.zotop-icon-status-50{background-position:-230px -294px;}
</style>
<script type="text/javascript">
$(function(){
	$('#operation').change(function(){
		var value = $(this).val();
		//显示权重
		if(value=='order'){
			$('#order').show();
		}else{
			$('#order').hide();
		}
		//显示栏目
		if(value=='move'){
			$('#selectcategory').show();
		}else{
			$('#selectcategory').hide();
		}
	});
});

$(function(){
	$('#selectcategory').click(function(){
		//弹出窗口
		var handle = '<?php echo zotop::url("content/category/select/__ID__")?>';
			handle = handle.replace(/__ID__/i, $('input[name=categoryid]').val());	
		var dialog = zotop.dialog.open({
			id:'select',
			title:'选择栏目…',
			url:handle,
			callback:function(id,title){
				$('input[name=categoryid]').val(id);
				$('input[name=selectcategory]').val(title);
				return true;
			}
		});
	});
})
</script>
<div class="subnavbar" style="position:relative;">
	<ul>
	<li><span>全部</span></a></li>
	</ul>
	<form class="smallsearch"  target="mainIframe" method="get" action="<?echo zotop::url("content/content/index/$categoryid")?>">
		<input type="text" name="keywords" class="text" value="<?php echo zotop::get('keywords') ?>" title="请输入关键词进行搜索"/>
		<button type="submit"><span class="zotop-icon zotop-icon-search button-icon"></span></button>
	</form>
</div>
<?php
form::header(array('valid'=>'false','class'=>'list','action'=>zotop::url('blog/index/operation')));
	
	$column = array();
	
	$column['select'] = html::checkbox(array('name'=>'table','class'=>'selectAll')); 
	$column['order w30 center'] = '权重';
	$column['status w30 center'] = '状态';
	$column['title'] = '名称';
	$column['model w60 center'] = '类型';
	$column['category w100 center'] = '栏目名称';
	$column['creator w150'] = '发布者/发布时间';
	$column['manage edit'] = '编辑';
	$column['manage delete'] = '删除';

	table::header('list',$column);

	foreach($contents as $row)
	{
		$column = array();
		$column['select'] = html::checkbox(array('name'=>'id[]','value'=>$row['id'],'class'=>'select'));
		$column['order w30 center'] = $row['order'] ? '<span class="red important">'.$row['order'].'</span>' : '0';
		$column['status w30 center'] = '<span class="zotop-icon zotop-icon-status'.(int)$row['status'].'"></span>';
		$column['title'] .= $row['link'] ? '<span class="zotop-icon zotop-icon-link" title="'.zotop::t('链接').'"></span>' : '';
		$column['title'] .= $row['image'] ? '<span class="zotop-icon zotop-icon-image" title="'.zotop::t('图片').'"></span>' : '';
		$column['title'] .= '<span class="title textflow"><a href="'.zotop::url('content/content/preview/'.$row['id']).'" '.html::attributes('style',$row['style']).' target="_blank">'.$row['title'].'</a></span>';
		$column['model w60 center'] = isset($models[$row['modelid']]) ? '<div class="w60 textflow">'.$models[$row['modelid']]['title'].'</div>' : '--';
		$column['category w100 center'] = isset($categories[$row['categoryid']]) ? '<div class="w100 textflow"><a>'.$categories[$row['categoryid']]['title'].'</a></div>' : '--';
		$column['creator w150'] = '<b>'.$row['username'].'</b>'.'<h5>'.time::format($row['createtime']).'</h5>';
		$column['manage edit'] = '<a href="'.zotop::url('content/content/edit/'.$row['id']).'">编辑</a>';
		$column['manage delete'] = (int)$row['system'] ? '<span class="disabled">删除</span>' : '<a href="'.zotop::url('content/content/delete/'.$row['id']).'" class="confirm1">删除</a>';
		table::row($column);
	}

	table::footer('选择：<a href="javascript:void(0);" class="selectAll" flag="true">全部</a> - <a href="javascript:void(0);" class="selectAll" flag="false">无</a>');

form::buttons(
	array('type'=>'select','name'=>'operation','id'=>'operation','class'=>'short','options'=>array(
		'status100'=>$statuses['100'],
		'status0'=>$statuses['0'],
		'status-1'=>$statuses['-1'],
		'status-50'=>$statuses['-50'],
		'order'=>'权重->'
		,'move'=>'移动->',
		'delete'=>'永久删除'
	)),
	array('type'=>'text','name'=>'order','id'=>'order','value'=>'50','title'=>'权重参数，必须是数字','class'=>'small','style'=>'width:30px;padding:4px;display:none;'),
	array('type'=>'text','name'=>'categoryid','id'=>'categoryid','value'=>$categoryid,'class'=>'small','style'=>'width:30px;padding:4px;display:none;'),
	array('type'=>'text','name'=>'selectcategory','id'=>'selectcategory','value'=>'选择栏目…','title'=>'选择栏目…','readonly'=>'readonly','class'=>'small','style'=>'width:120px;padding:4px;display:none;cursor:pointer;'),
	array('type'=>'submit','value'=>'执行操作')
);
form::footer($pagination);
?>
<?php $this->bottom()?>
<?php $this->footer();?>