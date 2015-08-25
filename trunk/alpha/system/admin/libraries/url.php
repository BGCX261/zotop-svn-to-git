<?php
class url extends BaseUrl
{
    public static function root()
    {
        $app = url::app();
        $dir = explode('/',$app);
        //默认情况下，admin位于system下面
        array_pop($dir);
        //默认情况下，system位于admin下面
        array_pop($dir);

        $root = implode('/',$dir);

        return $root;
    }


}
?>