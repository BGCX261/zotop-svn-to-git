<?php
class url extends zotop_url
{
    public static function theme()
    {
        $theme = zotop::config('zotop.theme');
        $theme = empty($theme) ? 'blue' : $theme ;
        return url::application().'/themes/'.$theme;
    }

}
?>