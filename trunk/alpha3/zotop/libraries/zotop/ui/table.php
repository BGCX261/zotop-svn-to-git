<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 简化表格输出
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_table
{
	public static function header($classname='',$titles='')
	{
		$html[] = '';
		$html[] = '<table class="table '.$classname.'">';
		if(is_array($titles))
		{
			$html[] = '<tr class="title">';
			foreach($titles as $name=>$title)
			{
				$html[] = '<th class="'.$name.'"><b>'.$title.'</b></th>';
			}

			$html[] = '</tr>';
		}
		$html[] = '	<tbody>';
		$html[] = '';
		echo implode("\n",$html);
	}
	public static function footer()
	{
		$html[] = '';
		$html[] = '	</tbody>';
		$html[] = '</table>';
		echo implode("\n",$html);
	}

	public static function row($rows,$classname='')
	{
		static $i=0;
		if(is_array($rows))
		{
			$html[] = '';
			$html[] = '		<tr class="item '.($i%2==0?'odd':'even').' '.$classname.'">';
			foreach($rows as $key=>$value)
			{
				if( is_string($value) )
				{
					$html[] = '			<td class="'.$key.'">'.$value.'</td>';
				}
				else
				{
					$inner = arr::take('inner',$value);

					$html[] ='			<td class="'.$key.'" '.html::attributes($value).'>'.$inner.'</td>';
				}
			}
			$html[] = '		</tr>';
			$i++;
		}
		echo implode("\n",$html);
	}



}
?>