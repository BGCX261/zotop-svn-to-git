<?php $this->header();?>
<?php $this->top();?>
<?php $this->navbar();?>
<script type="text/javascript">	
	//设置按钮
	dialog.setTip("点击选择模板文件").setButtons([{text:'确 定',callback:select},{text:'关 闭'}]);
	dialog.args.template = "<?php echo $field;?>" || dialog.args.template;
	var field = dialog.args.template;
	var $field = dialog.opener.$('input[name='+field+']');
	var value = $field.val();

	//选择函数
	function select(){
		var value = $('input[name=return]').val();
		if ( value ){
			$field.focus().val(value);
			dialog.close();
			return true;
		}
		zotop.msg.error('请选择模板文件');
		return false;
	}

	//表格
	$(function(){
		$('tr.file').click(function(){
			$('tr.file').removeClass('selected');
			$(this).addClass('selected');
			var value = $(this).find('input').val();
			$('input[name=return]').val(value);
		});
	})
</script>
<style type="text/css">
	#toolbar {position:absolute;top:10px;right:10px;}
	#toolbar a{margin:0px;margin-left:3px;}
	#position {border:solid 1px #eee;background:#fff;margin:5px;padding:5px;}
</style>
<input type="hidden" name="return" id="return" value=""/>
<div id="toolbar">
	<a class="button dialog {width:500,height:120}" href="<?php echo zotop::url("system/template/newfile",array('dir'=>$dir))?>">
		<span class="zotop-icon zotop-icon-file zotop-icon-newfile button-icon"></span><span class="button-text"><?php echo zotop::t('新建模板')?></span>
	</a>
	<a class="button" href="javascript:location.reload();">
		<span class="zotop-icon zotop-icon-refresh button-icon"></span><span class="button-text"><?php echo zotop::t('刷新')?></span>
	</a>
</div>
<div id="position">
<?php echo zotop::t("现在位置：").$position; ?>
</div>
<div style="border:solid 1px #eee;background:#fff;margin:5px;padding:3px;height:300px;overflow:auto;">
<table class="table list">
<?php foreach( $folders as $folder ) :?>
<tr class="item">
	<td class="w20 center"><span class="zotop-icon zotop-icon-folder"></span></td>
	<td><a href="<?php echo zotop::url("system/template/select/$field",array('dir'=>$dir.'/'.$folder))?>"><?php echo $folder;?></a></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<?php endforeach;?>
<?php foreach( $files as $file ) :?>
<?php 
	$filepath = site::template($dir.'/'.$file);
	$filedata = file::data($filepath);
	$filesize = file::size($filepath,true);
?>
<tr class="item file">
	<td class="w20 center"><span class="zotop-icon zotop-icon-file"></span></td>
	<td class="w120"><input type="hidden" value="<?php echo empty($dir) ? $file : $dir.'/'.$file;?>"/><?php echo $file;?></td>
	<td><?php echo $filedata['description'] ? $filedata['description'] : $filedata['title'];?></td>
	<td class="w60"><?php echo $filesize;?></td>
	<td class="manage"><?php echo '<a href="'.zotop::url('system/template/rename',array('file'=>$dir.'/'.$file)).'" class="dialog {width:500,height:120}">'.zotop::t('重命名').'</a>'?></td>
	<td class="manage"><?php echo '<a href="'.zotop::url('system/template/editor',array('file'=>$dir.'/'.$file)).'" class="dialog {width:750,height:400}">'.zotop::t('编辑').'</a>'?></td>
	<td class="manage"><?php echo '<a href="'.zotop::url('system/template/delete',array('file'=>$dir.'/'.$file)).'" class="confirm">'.zotop::t('删除').'</a>'?></td>
</tr>
<?php endforeach;?>
</table>
</div>
<?php $this->bottom(); ?>
<?php $this->footer(); ?>