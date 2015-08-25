<?php
/*系统函数补丁以及部分函数简写*/


/**
 * 数组arr::take的重写,从数组中弹出键，类似于array_pop,但是是根据键名弹出
 *
 * @param string $key 弹出的键名
 * @param array $array 目标数组
 * @param boolean $bool 是否区分大小写
 * @return $mix	被弹出 的数据
 */
function array_take($key,&$array,$bool=TRUE)
{
	return arr::take($key,$array,$bool);
}
//string::format的简写，并含有语言功能,尚未完成，先占个位置
function t($str,$args=array())
{
    return $str;
}
?>