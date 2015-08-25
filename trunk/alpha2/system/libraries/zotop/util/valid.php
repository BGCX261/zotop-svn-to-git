<?php
class zotop_valid
{
    public static function isNum($val)
    {
		return is_numeric($val);
	}

	public static function isInt($val)
	{
		return is_int($val);
	}

	public static function regex($val,$regex)
	{

	}
	/**
	 * 检查字符串是否是UTF8编码
	 * @param string $string 字符串
	 * @return Boolean
	 */
	function isUtf8($string)
	{
		return preg_match('%^(?:
			 [\x09\x0A\x0D\x20-\x7E]            # ASCII
		   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
		   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
		   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	   )*$%xs', $string);
	}

	public static function test($val,$valid)
	{
		//用于自定义的测试 $valid = {required:true,maxlength:5}
	}
}
?>