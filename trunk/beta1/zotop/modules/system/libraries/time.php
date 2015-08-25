<?php
class time_base
{
    /**
     * 返回当前时间或者当前时间戳
     * 
     * @param bool $format 是否格式化时间
     *
     * @return string
     */
    public static function current($format=false)
    {
        static $time ='';
        if( empty($time) )
        {
            $time = time();
        }
        $time = $format ? time::format($time) : $time;
        return $time;
    }
    
    /**
     * 等同于 time::current() 函数
     *
     */
    public static function now($format=false)
    {
        return time::current($format);
    }
    
    /**
     * 对时间进行格式化，支持多种格式化方式
     * 
     * @param string 待格式化的时间戳或者时间标准串
     * @param string 时间格式
     * 
     * @return string 格式化后的时间
     */
    public static function format($time=NULL, $format='{YYYY}-{MM}-{DD} [HH]:[MM]:[SS]')
    {
		if( is_null($time) )
		{
			$time = time();
		}

        $format = strtoupper($format);
        $formatTime = strtr($format,array(
            '{YYYY}' => date('Y',$time),//2009
            '{YY}' => date('y',$time),//09
        	'{MM}' => date('m',$time),//01
            '{M}' => date('n',$time),//1
            '{DD}' => date('d',$time),//03
            '{D}' => date('j',$time),//3
            '[HH]' => date('H',$time),//12
            '[H]' => date('G',$time),//5
            '[MM]' => date('i',$time),//00
            '[M]' => date('i',$time),//00
            '[SS]' => date('s',$time),//00
            '[S]' => date('s',$time), //00               
        ));
        return $formatTime;
    }


    /**
     * 两个时间的间隔，如 1天3小时56分钟25秒
     * 
     * @param string 时间1
     * @param string 时间2
     * 
     * @return string 格式化后的时间
     */
	public static function span($t1, $t2='')
	{
	
	}

	public static function zone()
	{
	
	}
}
?>