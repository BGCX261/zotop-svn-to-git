<?php
class setting_controller extends controller
{
	public function navbar()
	{
		return array(
			array('id'=>'main','title'=>'基本设置','href'=>url::build('zotop/index/main')),
			array('id'=>'info','title'=>'系统信息','href'=>url::build('zotop/index/info')),
		);
	}
    public function onDefault()
    {
		if(form::isPostBack())
		{
			msg::error('开发中','数据保存开发中，请稍后……');
		}
		$header['title'] = '系统设置';

		page::header($header);
		page::top();
		page::navbar($this->navbar(),'main');

			form::header();

			block::header('网站基本信息');

			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('网站名称'),
			   'name'=>'zotop.site.title',
			   'value'=>zotop::config('zotop.site.title'),
			   'description'=>zotop::t('网站名称，将显示在标题和导航中'),
			));
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('网站域名'),
			   'name'=>'zotop.site.domain',
			   'value'=>zotop::config('zotop.site.domain'),
			   'description'=>zotop::t('网站域名地址，不包含http://，如：www.zotop.com'),
			));
			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('备案信息'),
			   'name'=>'zotop.site.icp',
			   'value'=>zotop::config('zotop.site.icp'),
			   'description'=>zotop::t('页面底部可以显示 ICP 备案信息，如果网站已备案，在此输入您的授权码，它将显示在页面底部，如果没有请留空'),
			));
			form::field(array(
			   'type'=>'select',
			   'options'=>array('0'=>'不显示','1'=>'显示'),
			   'label'=>zotop::t('授权信息'),
			   'name'=>'zotop.site.license',
			   'value'=>zotop::config('zotop.site.license'),
			   'description'=>zotop::t('页脚部位显示程序官方网站链接'),
			));
			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('网站简介'),
			   'name'=>'zotop.site.about',
			   'value'=>zotop::config('zotop.site.about'),
			));

			block::footer();

			block::header('联系信息设置');

			form::field(array(
			   'type'=>'text',
			   'label'=>zotop::t('公司名称'),
			   'name'=>'zotop.site.title',
			   'value'=>'',
			   'description'=>zotop::t('网站隶属的公司或者组织名称'),
			));

			form::field(array(
			   'type'=>'textarea',
			   'label'=>zotop::t('网站简介'),
			   'name'=>'zotop.site.about',
			   'value'=>'',
			));

			block::footer();

			form::buttons(
			   array('type'=>'submit'),
			   array('type'=>'back' )
			);
			form::footer();

		page::bottom();
		page::footer();
	}


	public function onInfo()
	{
        $header['title'] = '控制中心';

        page::header($header);
		page::top();
		page::navbar($this->navbar());


		page::bottom();
        page::footer();
	}
}
?>