<?php
$this->header();
$this->top();
$this->navbar();
?>
<script type="text/javascript">
	function setValid(){
		var required = $('select[name=required]').val();
			required = required==1 ? true : false;
			valid('required',required);
		
		var maxlength = $('input[name=maxlength]').val();
			maxlength = parseInt(maxlength)>0 ? maxlength : false;
			valid('maxlength',maxlength);

		var field = $('select[name=type]').val() || $('input[name=type]').val() || '';
			field = field.toUpperCase();
			field = field == 'INT' || field=='MEDIUMINT' || field=='SMALLINT' || field=='TINYINT' ? true : false;
			valid('number',field);

			
	}

	function valid(name,value){
		var valid = $('input[name=valid]').val();
		var data = valid ? valid.split(',') : [];			
		var valid = [];
		for(var key in data){
			var val = data[key];
			var arr = val.split(':');
			valid[arr[0]] = arr[1];
		}			
		valid[name] = value;			
		var arr= [];
		for(var key in valid)
		{
			var val = valid[key];
			if ( val !== false ){
				arr.push(key + ':' + val);
			}
		}
		$('input[name=valid]').val(arr.join(','));
		
	}

	$(function(){
		//setValid();
	})
	
	$(function(){
		$('select[name=field]').change(function(){
			var type = $(this).val();
			var href = "<?php echo zotop::url('content/field/attrs/__type__');?>";
				href = href.replace(/__type__/i, type);			
				$('#settings').load(href,function(){
					setValid();
				});		
		});
	});

	$(function(){

		$('select[name=type]').live('change',function(){
			var type = $(this).val();
			var $maxlength = $('input[name=maxlength]');
			switch(type.toLowerCase())
			{
				case 'char':
					$maxlength.val(10).focus();
					$maxlength.rules("add",{max:255});
					break;
				case 'varchar':
					$maxlength.val(255).focus();
					$maxlength.rules("add",{max:255});
					break;
				case 'int':
					$maxlength.val(11).focus();
					$maxlength.rules("add",{max:11});
					break;
				case 'mediumint':
					$maxlength.val(7).focus();
					$maxlength.rules("add",{max:7});
					break;				
				case 'smallint':
					$maxlength.val(5).focus();
					$maxlength.rules("add",{max:5});
					break;
				case 'tinyint':
					$maxlength.val(3).focus();
					$maxlength.rules("add",{max:3});
					break;
				case 'mediumtext':
				case 'text':
					$maxlength.val('').focus();
					$maxlength.rules("remove","max");
					break;			
			}
			
		});

		$('input[name=maxlength]').live('change',function(){
			setValid();
		});

		$('select[name=required]').bind('change',function(){
			setValid();
		});

	});
</script>
<style type="text/css">
</style>
<?php
form::header();

	form::field(array(
		'type'=>'hidden',
		'name'=>'modelid',
		'label'=>'模型编号',
		'value'=>$data['modelid'],
		'valid'=>'required:true',
		'description'=>''
	));

	if ( $data['system'] ) :

	form::field(array(
		'type'=>'disabled',
		'name'=>'name',
		'label'=>'字段名称',
		'value'=>$data['name'],
		//'valid'=>'{required:true,maxlength:64,minlength:3}',
		//'description'=>'只能由英文字母、数字和下划线组成，并且仅能字母开头，不以下划线结尾，如： <b>title</b>'
	));

	else:

	form::field(array(
		'type'=>'text',
		'name'=>'name',
		'label'=>'字段名称',
		'value'=>$data['name'],
		'valid'=>'{required:true,maxlength:64,minlength:3}',
		'description'=>'只能由英文字母、数字和下划线组成，并且仅能字母开头，不以下划线结尾，如： <b>title</b>'
	));
	
	form::field(array(
		'type'=>'hidden',
		'name'=>'oname',
		'label'=>'字段名称',
		'value'=>$data['name'],
		'valid'=>'{required:true,maxlength:64,minlength:3}',
		'description'=>'只能由英文字母、数字和下划线组成，并且仅能字母开头，不以下划线结尾，如： <b>title</b>'
	));

	endif;

	form::field(array(
		'type'=>'text',
		'name'=>'label',
		'label'=>'控件标签',
		'value'=>$data['label'],
		'valid'=>'{required:true,maxlength:64}',
		'description'=>'请输入字段的标签，如：<b>新闻标题</b>',
	));

	if ( !$data['system'] ) :

	form::field(array(
		'type'=>'select',
		'options'=>$data['types'],
		'name'=>'field',
		'label'=>'控件类型',
		'value'=>$data['field'],
		'valid'=>'{required:true}',
	));

	form::field('<div id="settings">');
	
	foreach($attrs as $t=>$attr)
	{
		form::field($attr);
	}

	form::field('</div>');

	endif;


	form::field(array(
		'type'=>'css',
		'name'=>'settings[class]',
		'label'=>'控件CSS',
		'value'=>$data['settings']['class'],
		'valid'=>'',
		'description'=>'定义表单的CSS样式名',
	));

	form::field(array(
		'type'=>'css',
		'name'=>'settings[style]',
		'label'=>'控件样式',
		'value'=>$data['settings']['style'],
		'valid'=>'',
		'description'=>'定义表单的style样式，如：width:200px;',
	));

	form::field(array(
		'type'=>'text',
		'name'=>'value',
		'label'=>'默认数值',
		'value'=>$data['value'],
		'valid'=>'',
		'description'=>'',
	));

	form::field(array(
		'type'=>'select',
		'options'=>array(true=>'不允许空值(NOT NULL)',false=>'允许空值(NULL)'),
		'name'=>'required',
		'label'=>'是否空值',
		'value'=>(int)$data['required'],
		'valid'=>'',
		'description'=>'',
	));

	form::field(array(
		'type'=>'hidden',
		'name'=>'system',
		'label'=>'系统字段',
		'value'=>(int)$data['system'],
		'valid'=>'',
		'description'=>'',
	));
	
	form::field(array(
		'type'=>'valid',
		'name'=>'valid',
		'label'=>'验证规则',
		'value'=>$data['valid'],
		'valid'=>'',
		'description'=>'字段值有效性验证规则，多个规则使用<b>英文逗号</b>隔开，如：<b>required:true,maxlength:200,email:true,url:true等</b>',
	));

	form::field(array(
		'type'=>'text',
		'name'=>'description',
		'label'=>'提示说明',
		'value'=>$data['description'],
		'valid'=>'',
		'description'=>'显示在控件下方作为表单输入提示',
	));


	form::buttons(
		array('type'=>'submit'),
		array('type'=>'back')	
	);
	
	form::footer();

$this->bottom();
$this->footer();
?>