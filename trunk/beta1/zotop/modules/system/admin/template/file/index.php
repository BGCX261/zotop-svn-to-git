<?php $this->header();?>
<?php $this->top()?>
<?php $this->navbar()?>
<div class="subnavbar">
	<ul>
		<li<?php echo(empty($type) || $type=='all' ? ' class="current"' : '')?>><a href="<?php echo zotop::url("system/file/index/all/$folderid")?>"><span><span class="zotop-icon zotop-icon-file all"></span>全部</a></span></li>
		<?php foreach($types as $k=>$t):?>
		<li<?php echo($type == $k ? ' class="current"' : '')?>><a href="<?php echo zotop::url('system/file/index/'.$k.'/'.$folderid)?>"><span><span class="zotop-icon zotop-icon-file <?php echo $k?>"></span><?php echo $t?></a></span></li>
		<?php endforeach;?>
	</ul>
</div>
<script type="text/javascript">
$(function(){
	$('.image').zoomImage(40,40);
	
	$('.image').each(function(){
		var image = $(this).find('img').attr('src');
		if ( image && image.length > 4 ){
			$(this).simpletip({
				content:'<img src="'+image+'"/>',
				onBeforeShow:function(){
					$('.tooltip').zoomImage(400,300,{valign:false,onerror:false});
				},
				offset:[10,0]
			});
		}
	});
})
</script>
<?php

form::header(array('valid'=>'false','class'=>'list','action'=>zotop::url('system/file/action')));

        $column = array();
		$column['select'] = html::checkbox(array('name'=>'table','class'=>'selectAll'));
    	$column['w40 center'] = '图标';
    	$column['name'] = '名称';
		$column['user_name'] = '用户名';
    	//$column['type'] = '类型';		
    	$column['size w60'] = '大小';
    	$column['atime w120'] = '创建时间';

        table::header('list',$column);		
		
        foreach($files as $file)
        {
            $column = array();
			$column['select'] = html::checkbox(array('name'=>'id[]','value'=>$file['id'],'class'=>'select'));
            $column['center w40'] = $file['type']=='image' ? '<div class="image">'.html::image($file['path'],array('style'=>'display:none;')).'</div>' : '<div class="zotop-icon zotop-icon-file '.file::ext($file['path']).'"></div>';
            $column['name'] = '<div><b>'.$file['name'].'</b></div>';
			$column['name'] .= '<h5>';
			$column['name'] .= '<a href="'.zotop::url('system/file/down/'.$file['id']).'">下载</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('system/file/edit/'.$file['id']).'" class="dialog">编辑</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('system/file/delete/'.$file['id']).'" class="confirm">删除</a>';
        	$column['name'] .= '</h5>';
			$column['user_name w100'] = '<a><b>'.$file['user_username'].'</b></a><div class="textflow w100">'.$file['user_name'].'</div>';
			//$column['type w60'] = ''.$file['type'].'';
        	$column['size w60'] = ''.format::byte($file['size']).'';
        	$column['atime w120'] = ''.time::format($file['createtime']).'';
            table::row($column);
        }

        table::footer();

	form::buttons(
		array('type'=>'submit','value'=>'永久删除')
	);

form::footer($pagination);
?>
<?php $this->bottom()?>
<?php $this->footer();?>