<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 消息提示
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class msg_base
{
    public static function show(array $msg)
    {

		$page = new page();
		$page->set('msg',$msg);

		$message = $page->render('msg');
		
		if ( $message !== false )
		{
			echo $message;
		}
		else
		{
			$page->title = $msg['type'];
			$page->body = array('class'=>'msg');
			$page->header();
			
				$page->add('');
				$page->add('<div id="icon"><div class="zotop-icon zotop-icon-'.$msg['type'].'"></div></div>');
				$page->add('<div id="msg" class="'.$msg['type'].' clearfix">');
				$page->add('	<div id="msg-type">'.$msg['type'].'</div>');
				$page->add('	<div id="msg-life">'.(int)$msg['life'].'</div>');
				$page->add('	<div id="msg-title">'.$msg['title'].'</div>');
				$page->add('	<div id="msg-content">'.$msg['content'].'</div>');
				$page->add('	<div id="msg-detail">'.$msg['detail'].'</div>');
				$page->add('	<div id="msg-action">'.$msg['action'].'</div>');
				$page->add('	<div id="msg-file">'.$msg['file'].'</div>');
				$page->add('	<div id="msg-line">'.$msg['line'].'</div>');

				if( !empty($msg['url']) )
				{
					$page->add('	<div>');
					$page->add('		<div><b>如果页面没有自动跳转，请点击以下链接</b></div>');
					$page->add('		<a href="'.$msg['url'].'" id="msg-url">'.$msg['url'].'</a>');
					$page->add('	</div>');
				}
				$page->add('</div>');
				$page->add('<div id="powered">'.zotop::config('zotop.name').' '.zotop::config('zotop.version').'</div>');
				
			$page->footer();
		}

		exit;
    }
    
    /**
     * 显示错误消息
     *
     */
    public static function error($content='', $life=3)
    {
        $msg = array('type'=>'error','title'=>zotop::t('error'),'content'=>'','life'=>$life,'file'=>'','line'=>'');
        
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content,array('type'=>'error'));
        }
        else
        {
           $msg['content'] =  $content;
        }
		
		//action提示	
		$html[] = '<div class="msg-title"><b>如果问题未能解决，请尝试以下操作：</b></div>';
		$html[] = '<ul class="list">';
		if( is_string($msg['action']) )
		{
			$html[] = '<li>'.$msg['action'].'</li>';	
		}
		if( is_array($msg['action']) )
		{
			foreach($msg['action'] as $action)
			{
				$html[] = '<li>'.$action.'</li>';
			}
		}
		$html[] = '<li>点击<a href="javascript:document.location.reload();"> 刷新 </a>重试，或者以后再试</li>';
		$html[] = '<li>或者尝试点击<a href="javascript:window.history.go(-1);"> 返回前页 </a>后再试</li>';
		$html[] = '</ul>';

		$msg['action'] = implode("\n",$html);

        msg::show($msg);
    }

    /**
     * 显示成功消息
     *
     */
    public static function success($content='', $url='', $life=2)
    {
        $msg = array('type'=>'success','title'=>zotop::t('success'),'content'=>'','detail'=>'','url'=>$url,'life'=>$life,'action'=>'');
		
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content,array('type'=>'success'));
        }
        else
        {
           $msg['content'] =  $content;
        }
		       
        msg::show($msg);
    }    
}