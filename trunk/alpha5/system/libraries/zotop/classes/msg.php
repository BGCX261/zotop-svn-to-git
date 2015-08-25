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
class zotop_msg
{
    public static function show(array $msg)
    {

		$page = new page;
		$page->title = $msg['type'];
		$page->body = array('class'=>'msg');
		$page->header();
				
			$page->add('<div id="msg" class="'.$msg['type'].' clearfix">');
			$page->add('	<div id="msg-type">'.$msg['type'].'</div>');
			$page->add('	<div id="msg-life">'.$msg['life'].'</div>');
		    $page->add('	<div id="msg-title">'.$msg['title'].'</div>');
			$page->add('	<div id="msg-content">'.$msg['content'].'</div>');
  			$page->add('	<div id="msg-extra">'.$msg['extra'].'</div>');

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
		exit;
    }
    
    /**
     * 显示错误消息
     *
     */
    public static function error($content='', $life=3)
    {
        $msg = array('type'=>'error','title'=>'error','content'=>'','life'=>0);
        
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content);
        }
        else
        {
           $msg['content'] =  $content;
           $msg['life'] =  $life; 
        }        
		$msg['type'] = 'error';
        $msg['extra'] = $msg['extra'].'<div class="msg-title"><b>如果问题未能解决，请尝试以下操作：</b></div><ul class="list"><li>点击<a href="javascript:location.reload();"> 刷新 </a>重试，或者以后再试</li><li>或者尝试点击<a href="javascript:history.go(-1);"> 返回前页 </a>后再试</li></ul>';
        msg::show($msg);
    }

    /**
     * 显示成功消息
     *
     */
    public static function success($content='', $url='', $life=3, $extra='')
    {
        $msg = array('type'=>'success','title'=>'success','content'=>'','life'=>0);
        if( is_array($content) )
        {
            $msg = array_merge($msg,$content);
        }
        else
        {
           $msg['content'] =  $content;
           $msg['url'] =  $url;
           $msg['extra'] =  $extra;
           $msg['life'] =  $life;
        }          
        $msg['type'] = 'success';
        msg::show($msg);
    }    
}
?>