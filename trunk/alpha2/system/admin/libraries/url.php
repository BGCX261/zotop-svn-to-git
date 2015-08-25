<?php
class url extends Zotop_Url
{
    public static function root()
    {
        $application = url::application();
        $dir = explode('/',$application);        
        array_pop($dir);//默认情况下，admin位于system下面
        array_pop($dir);
        $root = implode('/',$dir);
        return $root;
    }

}
?>