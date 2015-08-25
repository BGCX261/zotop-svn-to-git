<?php
class AboutController extends controller
{
   public function onDefault()
   {
        $header['title'] = '关于我们';

        dialog::header($header);
		   dialog::top();

			   echo '<div style="padding:4px 15px;">';
			   echo '<table class="list">';
			   echo '<tr><td class="list-side">程序版本：</td><td>'.zotop::config('zotop.version').'</td></tr>';
			   echo '<tr><td class="list-side">程序设计：</td><td>'.zotop::config('zotop.author').'</td></tr>';
			   echo '<tr><td class="list-side">程序开发：</td><td>'.zotop::config('zotop.authors').'</td></tr>';
			   echo '<tr><td class="list-side">官方网站：</td><td><a href="'.zotop::config('zotop.homepage').'" target="_blank">'.zotop::config('zotop.homepage').'</a></td></tr>';
			   echo '<tr><td class="list-side">安装时间：</td><td>'.zotop::config('zotop.install').'</td></tr>';

			   echo '</table>';
			   echo '</div>';

		   dialog::bottom();
       dialog::footer();
   }

}
?>