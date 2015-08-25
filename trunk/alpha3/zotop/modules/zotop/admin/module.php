<?php
class module_controller extends controller
{
   
    public function navbar()
    {
        return array(
			array('id'=>'default','title'=>'模块列表','href'=>url::build('zotop/module')),
			array('id'=>'install','title'=>'安装模块','href'=>url::build('zotop/module/install')),
			array('id'=>'add','title'=>'新建模块','href'=>url::build('zotop/module/add')),
			array('id'=>'edit','title'=>'编辑模块'),
		);
    }
    
    public function onDefault()
    {
		$module = zotop::model('zotop.module');
		$list = $module->getAll(array(
		    'select'=>'*',
		    'orderby'=>'updatetime desc'
		));
		
		$module->getStructure();
                
        $page['title'] = '模块管理';

		page::header($page);
		page::top();
		page::navbar($this->navbar());
				
		form::header();
		
        $column = array();
    	$column['status w30 center'] = '状态';
    	$column['modulename'] = '模块ID';
    	$column['name'] = '模块名称';
    	$column['path'] = '安装目录';
    	$column['varsion w60 center'] = '版本号';
    	$column['updatetime w150'] = '更新时间';
    	$column['manage lock'] = '锁定';
    	$column['manage rename'] = '设置';
    	$column['manage edit'] = '权限';
    	$column['manage delete'] = '删除';

        table::header('list',$column);
        foreach($list as $module)
        {
            $module['status-icon'] = $module['status'] == -1 ? html::image(url::theme().'/image/icon/lock.gif') : html::image(url::theme().'/image/icon/ok.gif');
            
            $column = array();
            $column['status w30 center'] = $module['status-icon'];
            $column['name w80'] = $module['id'];
            $column['modulename'] = '<a><b>'.$module['name'].'</b></a><h5>'.$module['description'].'</h5>';
        	
        	$column['loginnum w60'] = $module['path'];
        	$column['loginip w60 center'] = $module['version'];        	
        	$column['logintime w150'] = time::format($module['updatetime']);
        	if( $module['status'] == -1 )
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/module/lock',array('id'=>$module['id'],'status'=>0)).'">启用</a>';
        	}
        	else
        	{
        	    $column['manage lock'] = '<a class="confirm" href="'.zotop::url('zotop/module/lock',array('id'=>$module['id'])).'">禁用</a>';
        	}
        	$column['manage setting'] = '<a href="'.zotop::url('zotop/module/setting',array('id'=>$module['id'])).'">设置</a>';
        	$column['manage priv'] = '<a href="'.zotop::url('zotop/module/priv',array('id'=>$module['id'])).'">权限</a>';
        	$column['manage delete'] = '<a>卸载</a>';
            table::row($column);
        }
        table::footer();
		form::buttons();
        form::footer();
		
		page::bottom();
		page::footer();        
    }
    
    public function onCheckID()
    {
        header("Cache-Control","no-store");
        header("Pragma","no-cache");
        header("Expires", "0");  
        
        $module = zotop::model('zotop.module');
        $module->id = $_GET['id'];
        
        if( $module->isExist() )
        {
            echo 'true';
        }
        else
        {
            echo 'false';
        }
        exit();
    }
    
    public function onInstall($id='')
    {
        $module = zotop::model('zotop.module');
        $modules = $module->notInstalled();
        
        //模块安装
        if( !empty($id) )
        {
            $install = $module->install($id);
            if( $install )
            {
                msg::success('操作成功','模块安装成功，请稍候',zotop::url('zotop/module/install'));
            }
            msg::error('操作失败','模块安装失败，请检查模块是否已经存在！');
        }        
        
        $page['title'] = '模块安装';

		page::header($page);
		page::top();
		page::navbar($this->navbar());
        
		//block::header('操作说明');
		
		echo '<ul class="notice">';
		echo '	<li>安装模块前，请确认将该模块文件夹上传至服务器上的模块目录下面目录下面（/zotop/modules）</li>';
		echo '	<li>上传完成后，刷新页面，模块将会出现在下面的待安装模块列表中</li>';
		echo '</ul>';
		
		//block::footer();
		
		block::header('待安装模块');
		$column = array();
		$column['logo w30'] = '';
		$column['name'] = '名称';
		$column['version w50'] = '版本';
    	$column['manage edit'] = '安装';
    	$column['manage delete'] = '删除';		
		
		table::header('list',$column);
		foreach($modules as $module)
		{
		    
		    $column = array();
    		$column['logo w30'] = html::image($module['icon'],array('width'=>'32px'));
    		$column['name'] = '<a><b>'.$module['name'].'</b></a><h5>'.$module['description'].'</h5>';
    		$column['version'] = $module['version'];
        	$column['manage edit'] = html::a(zotop::url('zotop/module/install',array('id'=>$module['id'])),'安装',array('class'=>'confirm'));
        	$column['manage delete'] = '删除';    		
    		
    		
		    table::row($column);
		}
		table::footer();
		block::footer();
        
		page::bottom();
		page::footer();         
        
    }
    
    public function onAdd()
    {		
		if( form::isPostBack() )
		{
		    $module = zotop::model('zotop.module');
		    $post = form::post();		    
		    $result = $module->insert($post);
		    if( $result )
		    {
		        msg::success('保存成功','添加成功，正在刷新页面，请稍后……',zotop::url('zotop/module'));   
		    }		    
		}
		    
        $page['title'] = '新建模块';

		page::header($page);
		page::top();
		page::navbar($this->navbar());

			form::header();
            
			block::header('基本信息');

    			form::field(array(
    			   'type'=>'text',
    			   'label'=>'模块ID',
    			   'name'=>'id',
    			   'value'=>$data['id'],
    			   'valid'=>'required:true,minlength:3,maxlength:32,remote:"'.zotop::url('zotop/module/checkid').'"',
    			   'description'=>'允许使用数字、英文字符(不区分大小写)或者下划线，不允许使用其它特殊字符，3~32位',
    			));
    								
    			form::field(array(
    			   'type'=>'text',
    			   'label'=>'模块名称',
    			   'name'=>'name',
    			   'value'=>$data['name'],
    			   'valid'=>'required:true',
    			   'description'=>'',
    			));
    			
			    form::field(array(
    			   'type'=>'radio',
			       'options'=>array('system'=>'核心模块','plugin'=>'插件模块'),
    			   'label'=>'模块类型',
    			   'name'=>'type',
    			   'value'=>$data['type'],
    			   'valid'=>'required:true',
    			   'description'=>'',
    			));

    			form::field(array(
    			   'type'=>'text',
    			   'label'=>'访问地址',
    			   'name'=>'url',
    			   'value'=>$data['url'],
    			   'valid'=>'url:true',
    			   'description'=>'可以为模块绑定访问域名，如：http://bbs.***.com/ 如果不绑定域名，请留空',
    			));    			
			
    			form::field(array(
    			   'type'=>'textarea',
    			   'label'=>'模块说明',
    			   'name'=>'description',
    			   'value'=>$data['description'],
    			   'valid'=>'',
    			   'description'=>'',
    			));			
			
			block::footer();
			
			block::header('开发者信息');
    			
			    form::field(array(
    			   'type'=>'text',
    			   'label'=>'开发者',
    			   'name'=>'author',
    			   'value'=>$data['author'],
    			   'valid'=>'required:true',
    			   'description'=>'',
    			));

			    form::field(array(
    			   'type'=>'text',
    			   'label'=>'电子邮件',
    			   'name'=>'email',
    			   'value'=>$data['email'],
    			   'valid'=>'required:true,email:true',
    			   'description'=>'',
    			));
    				    			
			    form::field(array(
    			   'type'=>'text',
    			   'label'=>'官方网站',
    			   'name'=>'site',
    			   'value'=>$data['site'],
    			   'valid'=>'required:true',
    			   'description'=>'',
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
    

    

    
    public function onLock($id,$status=-1)
    {
        $module = zotop::model('zotop.module');
        
        $post = array(
            'id'=>$id,
            'status'=>$status,
        );
        
        $update = $module->update($post);
        if( $update )
        {
            msg::success('操作成功','正在刷新页面，请稍后……',zotop::url('zotop/module'));   
        }        
    }
}
?>