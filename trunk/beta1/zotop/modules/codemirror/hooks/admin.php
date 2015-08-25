<?php
zotop::add('system.ready','field_codemirror');

function field_codemirror()
{
	field::set('code','codemirror');

	function codemirror($attrs)
	{		
			$url = zotop::module('codemirror','url');
		
			$options = new stdClass();
			$options->path = $url.'/codemirror/js/';
			$options->parserfile = array('parsexml.js');
			$options->stylesheet = array($url.'/codemirror/css/xmlcolors.css');
			$options->height = is_numeric($attrs['height']) ? $attrs['height'].'px' : $attrs['height'];
			$options->width = is_numeric($attrs['width']) ? $width.'px' : $attrs['width'];
			$options->continuousScanning = 500;
			$options->autoMatchParens = true;
			if ( $attrs['linenumbers'] !== false ) {
				$options->lineNumbers = true;
				$options->textWrapping = false;
			}
			if ( $attrs['tabmode'] == '' )
			{
				$options->tabMode = 'shift';
			}
			
			$html = array();
			$html[] = html::script($url.'/codemirror/js/codemirror.js');
			$html[] = html::stylesheet($url.'/codemirror/css/codemirror.css');
			$html[] = '	'.field::textarea($attrs);
			$html[] = '<script type="text/javascript">';
			$html[] = '	var editor = CodeMirror.fromTextArea("'.$attrs['name'].'", '.json_encode($options).');';
			$html[] = '$(function(){';
			$html[] = '	$("form").submit(function(){';
			$html[] = '		$("textarea[name=+'.$attrs['name'].'+]").val(editor.getCode());';
			$html[] = '	});';
			$html[] = '})';
			$html[] = '</script>';
		 
			return implode("\n",$html);
	}

	field::set('templateeditor',templateeditor);

	function templateeditor($attrs)
	{
		return codemirror($attrs);
	}
}


?>