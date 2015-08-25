<?php
class BaseTable extends Base
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
				$html[] = '<td class="'.$name.'"><b>'.$title.'</b></td>';
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
					$inner = array_take('inner',$value);

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