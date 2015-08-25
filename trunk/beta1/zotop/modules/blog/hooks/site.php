<?php
zotop::add('blog.side','blog_side');

function blog_side($blog='')
{
	box::header(array(
		'title'=>'精彩评论',
		'action'=>'<a class="dialog" href="'.zotop::url('comment/add/blog/'.$blog->id).'">发表评论</a>|<a class="more" href="'.zotop::url('comment/list').'">更多</a>',
	));

	echo '<div style="height:200px;"></div>';

	box::footer();
}

?>