<?php
/**
 * title:头部文件
 * description:网站共用头部文件
*/
?>
<div id="header" class="clearfix">
  <a href="<?php echo zotop::url('site://');?>" title="网站首页"><div id="logo"></div></a>  
  <div id="topbar">
    <a href="javascript:void(0);" id="setDefault">设为首页</a>
    <a href="javascript:void(0);" id="setFavorate">加入收藏</a>
    <a href="javascript:void(0);">联系我们</a>
  </div>
  <div id="navbar" class="navbar">
    <ul>
      <li><a href="<?php echo zotop::url();?>">index</a></li>
      <li><a href="<?php echo zotop::url('blog');?>">blog</a></li>
    </ul>
  </div>
</div>
<div id="body" class="clearfix">