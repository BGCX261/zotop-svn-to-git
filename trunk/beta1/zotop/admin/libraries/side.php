<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 侧边页面类
 *
 * @package		zotop
 * @class		side
 * @author		zotop team
 * @copyright	(c)2009 zotop team 
 * @license		http://zotop.com/license.html
 */
class side extends page
{
    public function header()
    {
        $this->body = array_merge((array)$this->body, array('class'=>'side'));
        $this->addScript('$common/js/zotop.side.js');
        return parent::header();
    }

	public function footer()
	{
        return parent::footer();		
	}

	public function top()
	{
		return '';
	}

	public function bottom()
	{
		return '';
	}
    
	public function navlist($data,$current='')
	{
        $html = array();        
		if(is_array($data))
		{
            $html[] = '';
			$html[] = '	<ul class="list">';
			foreach($data as $item)
			{
				if(is_array($item))
				{
					$class=($current==$item['id'])?'current':(empty($item['href'])?'hidden':'normal');
					$href = empty($item['href']) ? '#' : $item['href'];
					$html[]='		<li class="'.$item['class'].' '.$class.'"><a href="'.$href.'"  id="'.$item['id'].'" target="mainIframe"><span>'.$item['title'].'</span></a></li>';
				}
				else
				{
					$html[] = '		<li class="'.$item['class'].' '.$class.'">'.$item.'</li>';
				}
			}
			$html[] = '	</ul>';
		}
		return implode("\n",$html);        
	}
	
}
?>