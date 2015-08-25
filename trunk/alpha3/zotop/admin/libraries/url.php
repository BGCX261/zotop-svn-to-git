<?php
class url extends zotop_url
{
    public static function root()
    {
        $application = url::application();
        $dir = explode('/',$application);
        array_pop($dir);//默认情况下，admin位于zotop下面
        array_pop($dir);
        $root = implode('/',$dir);
        return $root;
    }

    public static function theme()
    {
        $theme = zotop::config('zotop.theme');
        $theme = empty($theme) ? 'blue' : $theme ;
        return url::application().'/themes/'.$theme;
    }

}
?>