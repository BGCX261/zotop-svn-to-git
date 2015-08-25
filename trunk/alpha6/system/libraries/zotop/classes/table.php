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
	public static $attrs = array();
	public static $datas = array();


	public static function set($name, $value='')
	{
		if ( is_string($name) )
		{
			table::$attrs[$name] = $value;
		}

		if ( is_array($name) )
		{
			table::$attrs = array_merge(table::$attrs, $name);
		}
	}
	
	public static function header($classname='',$titles='')
	{
		if ( is_string($classname) )
		{
			table::$attrs['class'] = $classname;
		}

		if ( is_array($classname) )
		{
			table::$attrs = array_merge(table::$attrs, $classname);
		}

		if ( is_array($titles)  )
		{
			table::$attrs['titles'] = $titles;
		}
	}

	public static function footer()
	{
		echo table::render();

		table::$attrs = array();
		table::$datas = array();	
	}

	public static function row($data,$class='')
	{
		if( is_array($data) )
		{
			table::$datas[] = array('data' => $data,'class' => $class);
		}
	}

	public static function render($datas=array(), $attrs=array())
	{
		$datas = empty($datas) ? table::$datas : $datas;
		$attrs = empty($attrs) ? table::$attrs : $attrs;

		
		if ( isset($attrs['titles']) )
		{
			$titles = $attrs['titles'];
			unset($attrs['titles']);
		}
		
		$attrs['class'] = empty($attrs['class']) ? 'table' : 'table '.$attrs['class'];
		
		//渲染表格
			
		$html[] = '';
		$html[] = '<table'.html::attributes($attrs).'>';

		if(is_array($titles))
		{
			$html[] = '	<thead>';
			$html[] = '		<tr class="title">';

			foreach($titles as $name=>$title)
			{
				$html[] = '			<th class="'.$name.'"><b>'.$title.'</b></th>';
			}
			
			$html[] = '		</tr>';
			$html[] = '	</thead>';
		}
		$html[] = '	<tbody>';
		
		if ( is_array($datas) && !empty($datas) )
		{
			$i = 0;

			foreach($datas as $row)
			{
				$html[] = '';
				$html[] = '		<tr class="item '.($i%2==0?'odd':'even').' '.$row['class'].'">';
				foreach($row['data'] as $key=>$value)
				{
					if( is_string($value) )
					{
						$html[] = '			<td class="'.$key.'">'.$value.'</td>';
					}
					else
					{
						$data = arr::take('value',$value);

						$html[] ='			<td'.html::attributes($value).'>'.$data.'</td>';
					}
				}
				$html[] = '		</tr>';
				$i++;
			}
		}
		else
		{
			$html[] = '		<tr><td colspan="'.count($titles).'"><div class="zotop-empty">'.zotop::t('未能找到符合要求的数据').'</div></td></tr>';
		}

		$html[] = '	</tbody>';
		$html[] = '</table>';
			
		return implode("\n",$html);	
	}



}
?>