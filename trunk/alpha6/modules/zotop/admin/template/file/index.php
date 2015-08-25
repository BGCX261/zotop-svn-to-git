<?php $this->header();?>
<?php $this->mainHeader();?>
<?php $this->top()?>
<?php $this->navbar()?>
<script type="text/javascript">
$(function(){
	$('.image').zoomImage(40,36);
	
	$('.image').each(function(){
		var image = $(this).find('img').attr('src');
		if ( image.length > 4 ){
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

form::header(array('valid'=>'false','class'=>'list','action'=>zotop::url('zotop/file/action')));


        $column = array();
		$column['select'] = html::checkbox(array('name'=>'table','class'=>'selectAll'));
    	$column['w40 center'] = '图标';
    	$column['name'] = '名称';
    	$column['type'] = '类型';
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
			$column['name'] .= '<a href="'.zotop::url('zotop/file/view',array('id'=>$file['id'])).'" class="dialog">查看</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('zotop/file/down',array('id'=>$file['id'])).'">下载</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('zotop/file/edit',array('id'=>$file['id'])).'" class="dialog">编辑</a>';
			$column['name'] .= '&nbsp;&nbsp;<a href="'.zotop::url('zotop/file/delete',array('id'=>$file['id'])).'" class="confirm">删除</a>';
        	$column['name'] .= '</h5>';
			$column['type w60'] = ''.$file['type'].'';
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
<?php $this->mainFooter();?>
<?php $this->sideHeader();?>

<?php block::header('统计信息');?>
<?php
	table::header();
	table::row(array('w60'=>zotop::t('文件大小'),'2'=>format::byte($totalsize)));
	table::row(array('w60'=>zotop::t('文件数量'),'2'=>$totalcount .zotop::t(' 个')));
	table::footer();
?>
<?php block::footer();?>


<?php block::header('按文件类型查看');?>
<div class="navbarlist">
	<ul class="list2">
		<li><a href="<?php echo zotop::url('zotop/file/index')?>"><span class="zotop-icon zotop-icon-file all"></span>全部文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'document'))?>"><span class="zotop-icon zotop-icon-file document"></span>文档文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'text'))?>"><span class="zotop-icon zotop-icon-file text"></span>文本文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'image'))?>"><span class="zotop-icon zotop-icon-file image"></span>图片文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'flash'))?>"><span class="zotop-icon zotop-icon-file flash"></span>flash文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'video'))?>"><span class="zotop-icon zotop-icon-file video"></span>视频文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'audio'))?>"><span class="zotop-icon zotop-icon-file audio"></span>音频文件</a></li>
		<li><a href="<?php echo zotop::url('zotop/file/index',array('type'=>'unknown'))?>"><span class="zotop-icon zotop-icon-file unknown"></span>其它</a></li>
	</ul>
</div>
<?php block::footer();?>

<?php $this->sideFooter();?>
<?php $this->footer();?>