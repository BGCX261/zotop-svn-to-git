<?php
class zotop_ip
{
    /**
     * 获取当前的ip地址     * 
     *
     */
    public static function current()
    {
        return ip::get();
    }

    /**
     * 获取当前的ip地址     * 
     *
     */
    public static function location($ip='')
    {
        return ip::get();
    }

	public static function get()
	{
		$ip = '';
		
		if ( !empty($_SERVER["HTTP_CLIENT_IP"]) )
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif ( !empty($_SERVER["HTTP_X_FORWARDED_FOR"]) )
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif ( !empty($_SERVER["REMOTE_ADDR"]) )
		{
			$ip = $_SERVER["REMOTE_ADDR"];
		}

		preg_match("/[\d\.]{7,15}/", $ip, $matches);

		$ip = isset($matches[0]) ? $matches[0] : 'unknown';

		unset($matches);
		
		return $ip;
	}
    

}
?>