<?php $this->header();?>
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
			$('#categoryid').show();
		}else{
			$('#categoryid').hide();
		}
	});
});
</script>
<?php $this->mainHeader();?>
<?php $this->top()?>
<?php $this->navbar()?>
<div class="subnavbar">
	<ul>
	<li<?php echo !isset($status) ? ' class="current"' : '';?>><a href="<?php echo zotop::url('blog/index/index',array('categoryid'=>$categoryid))?>"><span>全部</span></a></li>
	<?php foreach($blogstatus as $s=>$t){?>	
	<b>|</b>
	<li<?php echo (isset($status) && $status==$s) ? ' class="current"' : '';?>><a href="<?php echo zotop::url('blog/index/index',array('categoryid'=>$categoryid,'status'=>$s))?>"><span><?php echo $t?></span></a></li>
	<?php };?>

	</ul>
</div>
<?php

form::header(array('valid'=>'false','class'=>'list','action'=>zotop::url('blog/index/operation')));


        $column = array();
		$column['select'] = html::checkbox(array('name'=>'table','class'=>'selectAll'));    	
		$column['status w30 center'] = '状态';
		$column['order w30 center'] = '权重';
    	$column['name'] = '名称';
		$column['categoryid w50'] = '分类';
		$column['comment w30 center'] = '评论';
    	$column['atime w120'] = '最后修改时间';

        table::header('list',$column);		
		
        foreach($blogs['data'] as $blog)
        {
            $column = array();
			$column['select'] = html::checkbox(array('name'=>'id[]','value'=>$blog['id'],'class'=>'select'));
			$column['w30 center'] = '<span title="'.$blogstatus[$blog['status']].'" class="zotop-icon zotop-icon-status'.$blog['status'].'"></span>';
            $column['order w30 center'] = empty($blog['order']) ? '0' : '<b class="red">'.$blog['order'].'</b>';
			$column['title'] = '<b'.(empty($blog['style']) ? '' : ' style="'.$blog['style'].'"').'>'.$blog['title'].'</b>';
			$column['title'] .= '<h5>';
			$column['title'] .= '<a href="'.zotop::url('site://blog/index/view',array('id'=>$blog['id'])).'" target="_blank">查看</a>';
			$column['title'] .= '&nbsp;<a href="'.zotop::url('blog/index/edit',array('id'=>$blog['id'])).'">编辑</a>';
			$column['title'] .= '&nbsp;<a href="'.zotop::url('blog/index/delete',array('id'=>$blog['id'])).'" class="confirm">删除</a>';
        	$column['title'] .= '</h5>';
			$column['categoryid w50'] = '<div class="w60 textflow" title="'.$categorys[$blog['categoryid']]['title'].'">'.$categorys[$blog['categoryid']]['title'].'</div>';
			$column['comment w30 center'] = empty($blog['comment']) ? '0' : '<b class="red">'.$blog['comment'].'</b>';
        	$column['atime w120'] = ''.time::format($blog['updatetime']).'';
            table::row($column);
        }

        table::footer();

	form::buttons(
		array('type'=>'select','name'=>'operation','id'=>'operation','class'=>'short','options'=>array('status100'=>$blogstatus['100'],'status0'=>$blogstatus['0'],'status-1'=>$blogstatus['-1'],'status-50'=>$blogstatus['-50'],'order'=>'权重->','move'=>'移动->','delete'=>'永久删除')),
		array('type'=>'text','name'=>'order','id'=>'order','value'=>'50','title'=>'权重参数，必须是数字','style'=>'width:30px;padding:4px;display:none;'),
		array('type'=>'select','options'=>arr::hashmap($categorys,'id','title'),'name'=>'categoryid','id'=>'categoryid','value'=>$categoryid,'class'=>'short','style'=>'display:none;'),
		array('type'=>'submit','value'=>'执行操作')
	);

form::footer($pagination);
?>
<?php $this->bottom()?>
<?php $this->mainFooter();?>
<?php $this->sideHeader();?>

<?php block::header(array('title'=>'日志分类','action'=>'<a href="'.zotop::url('blog/category/index').'" class="dialog">管理</a><a href="'.zotop::url('blog/category/add',array('referer'=>url::encode(url::location()))).'" class="dialog">添加</a>'));?>
<div class="navbarlist">
	<ul>
		<li<?php echo empty($categoryid) ? ' class="selected"' : '';?>><a href="<?php echo zotop::url('blog/index/index')?>"><span class="zotop-icon zotop-icon-category"></span>全部日志</a></li>
		<?php foreach($categorys as $c){?>
		<li<?php echo $categoryid==$c['id'] ? ' class="selected"' : '';?>><a class="textflow" href="<?php echo zotop::url('blog/index/index',array('categoryid'=>$c['id']))?>"><span class="zotop-icon zotop-icon-category"></span><?php echo $c['title']?></a></li>
		<?php }?>
	</ul>
</div>
<?php block::footer();?>

<?php block::header(array('title'=>zotop::modules('blog','name')));?>
<table class="table">	
	<tr><td><?php echo zotop::modules('blog','description')?></td></tr>
	<tr><td>版本：<?php echo zotop::modules('blog','version')?></td></tr>
	<tr><td>作者：<?php echo zotop::modules('blog','author')?></td></tr>
	<tr><td>主页：<?php echo html::a(zotop::modules('blog','homepage'))?></td></tr>
</table>
<?php block::footer();?>

<?php $this->sideFooter();?>
<?php $this->footer();?>