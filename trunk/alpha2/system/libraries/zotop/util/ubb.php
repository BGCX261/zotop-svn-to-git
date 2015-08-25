<?php
class zotop_ubb
{
	public static function decode($str) {
	  $str =trim($Text);
	  $str =preg_replace("/\\t/is","  ",$str);
	  $str =preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$str);
	  $str =preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$str);
	  $str =preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$str);
	  $str =preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$str);
	  $str =preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$str);
	  $str =preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$str);
	  $str =preg_replace("/\[separator\]/is","",$str);
	  $str =preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$str);
	  $str =preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$str);
	  $str =preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$str);
	  $str =preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\1</a>",$str);
	  $str =preg_replace("/\[url\]([^\[]*)\[\/url\]/is","<a href=\"\\1\" target=_blank>\\1</a>",$str);
	  $str =preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$str);
	  $str =preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$str);
	  $str =preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$str);
	  $str =preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$str);
	  $str =preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$str);
	  $str =preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$str);
	  $str =preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$str);
	  $str =preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis","color_txt('\\1')",$str);
	  $str =preg_replace("/\[emot\](.+?)\[\/emot\]/eis","emot('\\1')",$str);
	  $str =preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$str);
	  $str =preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$str);
	  $str =preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$str);
	  $str =preg_replace("/\[quote\](.+?)\[\/quote\]/is"," <div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $str);
	  $str =preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $str);
	  $str =preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $str);
	  $str =preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div class='sign'>\\1</div>", $str);
	  $str =preg_replace("/\\n/is","<br/>",$str);
	  return $str;
	}

	public static function encode($str)
	{

	}


}
?>