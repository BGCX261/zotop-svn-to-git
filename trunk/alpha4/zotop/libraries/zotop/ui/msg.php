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
    public static function show($msg=array())
    {
		$header['title'] = '系统提示';
		$header['body']['class']="msg";

		page::header($header);


			page::add('<div id="msg" class="'.$msg['type'].' clearfix">');
			page::add('	<div id="msg-type">'.$msg['type'].'</div>');
			page::add('	<div id="msg-life">'.$msg['life'].'</div>');
			page::add('	<div class="zotop-msg zotop-msg-'.$msg['type'].'">');
			page::add('		<div class="zotop-msg-icon"></div>');
			page::add('		<div class="zotop-msg-content clearfix">');
			page::add('			<div id="msg-title">'.$msg['title'].'</div>');
			page::add('			<div id="msg-content">'.$msg['content'].'</div>');
			page::add('			<a href="'.$msg['url'].'" id="msg-url">'.$msg['url'].'</a>');
			page::add('			<div id="msg-extra">'.$msg['extra'].'</div>');
			page::add('		</div>');
			page::add('	</div>');			
			page::add('</div>');
            page::add('<div id="powered">'.zotop::config('zotop.name').' '.zotop::config('zotop.version').'</div>');
			

		page::footer();
		exit;
    }

    public static function error($content='', $life=9)
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

    public static function success($title, $content='', $url='', $life=5, $extra='')
    {
        $msg = array();
        $msg['type'] = 'success';
        $msg['title'] = empty($content) ? 'success' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['extra'] = $extra.'';
        $msg['url'] = $url;
        $msg['life'] = $life;
        msg::show($msg);
    }
    public static function alert($title,$content='',$life=0)
    {
        $msg = array();
        $msg['type'] = 'alert';
        $msg['title'] = empty($content) ? 'alert' : $title;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['content'] = empty($content) ? $title : $content;
        $msg['life'] = $life;
        msg::show($msg);
    }

    public static function template()
    {
    }
}
?>