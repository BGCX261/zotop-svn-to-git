<?php
class form extends BaseForm
{
	public static function template()
	{
        $html[] = '';
	    $html[] = '<table class="field"><tr>';
	    $html[] = '	<td class="field-side">';
	    $html[] = '		{$field:label}{$field:required}';

	    $html[] = '	</td>';
	    $html[] = '	<td class="field-main">';
	    $html[] = '	{$field:controller}';
	    $html[] = '	{$field:description}';
	    $html[] = '	</td>';
	    $html[] = '</tr></table>';

	    return implode("\n",$html);
	}


}
?>