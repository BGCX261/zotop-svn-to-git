<?php
//基类Base
abstract class base
{
	private function __set($name,$value)
	{
		if(property_exists($this,$name)){
			$this->$name=$value;
		}
	}

	private function __get($name)
	{
		if(isset($this->$name)){
			return $this->$name;
		}else{
			return null;
		}
	}
}
?>