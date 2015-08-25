<?php
class test_controller extends controller
{
    public function onDefault()
    {
       $header['title'] = '测试表单';

       page::header($header);
       page::top();

	   page::navbar(array(
		  array('id'=>'form','title'=>'测试表单','href'=>url::build('zotop/test')),
		  array('id'=>'info','title'=>'系统信息','href'=>url::build('zotop/index/info')),
	   ),'form');

           form::header();

           block::header(array('title'=>'基本信息','action'=>'more'));

           form::field(array(
               'type'=>'text',
               'label'=>'文本框',
			   'name'=>'test3',
               'value'=>'2的飞洒的发生地',
			   'valid'=>'{required:true,maxlength:5}',
               'description'=>'提示信息',
           ));
           form::field(array(
               'type'=>'checkbox',
               'label'=>zotop::t('多选框'),
			   'name'=>'test1',
               'options'=>array('1'=>'a1','2'=>'a2','3'=>'a1','4'=>'a2','5'=>'a1','6'=>'a2'),
               'value'=>array('1','2'),
			   'valid'=>'{required:true,minlength:3}',
			   'title'=>'请最少选择三项',
               //'class'=>'block',
               'description'=>'最少选择三项',
           ));
           form::field(array(
               'type'=>'checkbox',
               'label'=>zotop::t('多选框'),
			   'name'=>'test11',
               'options'=>array('1'=>'a1','2'=>'a2','3'=>'a3','4'=>'a4'),
               'value'=>array('1','2','4'),
               'class'=>'block',
			   'valid'=>'{required:true,maxlength:2}',
			   'title'=>'最多选择2项',
               'description'=>'最多只允许选择2项',
           ));

           form::field(array(
               'type'=>'select',
               'label'=>'下拉列表',
			   'name'=>'test22',
               'options'=>array('1'=>'a1','2'=>'a2','3'=>'a1','4'=>'a2','5'=>'a1','6'=>'a2'),
               'value'=>array('2','4'),
			   'valid'=>'{required:true,maxlength:1}',
			   'title'=>'最多允许选择三项',
               'description'=>'提示信息',
           ));
           form::field(array(
               'type'=>'select',
               'label'=>'下拉列表',
			   'name'=>'test2',
               'options'=>array(''=>'empty','1'=>'a1','2'=>'a2','3'=>'a1','4'=>'a2','5'=>'a1','6'=>'a2'),
               'value'=>'2',
			   'valid'=>'{required:true}',
			   'title'=>'最多允许选择三项',
               //'class'=>'block',
               'description'=>'提示信息',
           ));

           block::footer();
           block::header(array('title'=>'高级信息','action'=>'more'));
           form::field(array(
               'type'=>'image',
               'label'=>'上传图片',
			   'name'=>'test4',
               'value'=>'2的飞洒的发生地',
               'valid'=>'required:true',
               'description'=>'提示信息',
           ));
           form::field(array(
               'type'=>'textarea',
               'label'=>'文本框',
			   'name'=>'test32',
               'value'=>'2的飞洒的发生地',
               'valid'=>'required:true,maxlength:500',
               'description'=>'提示信息',
           ));

		   form::field(array(
				'label'=>'组合',
				'type'=>'group',
				'fields'=>array(
					array('label'=>'年','name'=>'year','type'=>'text'),
					array('label'=>'月','name'=>'month','type'=>'text'),
					array('label'=>'日','name'=>'day','type'=>'text'),
				),
				'description'=>'生成一个复合控件'
		   ));


           block::footer();

           form::buttons(
               array('type'=>'submit'),array('type'=>'reset')
           );

           form::footer();


       page::bottom();
       page::footer();
    }
}
?>