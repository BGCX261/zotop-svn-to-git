<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 页面组件
 *
 * @copyright  (c)2009 zotop team
 * @package    zotop.ui
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class zotop_pagination
{
	protected $config = array();
    
    /**
     * 初始化控制器
     * 
     */
    public function __construct($config=array())
    {
        $this->config = array_merge(array(
			'first'=>zotop::t('首页'),
			'prev'=>zotop::t('上页'),
			'next'=>zotop::t('下页'),
			'end'=>zotop::t('末页'),
			'page'=>isset($_GET['page']) ? $_GET['page'] : 1,
			'total'=>0,
			'pagesize'=>30,
			'param'=>'page',
			'template'=>zotop::t('<div class="pagination"><ul><li class="total">共 $total 条记录</li> <li class="page">$page页/$totalpages页</li> $first $prev $pages $next $end</ul></div>')
		),$config);
    }   
	
    /**
     * 设置数据对象的值
     * 
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name,$value)
    {
        $this->config[$name]  =   $value;
    }

    /**
     * 获取数据对象的值
     * 
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->config[$name])?$this->config[$name]:null;
    }	

	public function config($name=null, $value=null)
	{
        
		if( !isset($name) )
		{
			return $this->config;
		}

		if( !isset($value) )
		{
			return $this->config[$name];
		}
		
		if( is_array($name) )
        {
            $this->config = array_merge($this->config,$name);

			 return $this->config;
        }
        
        if( is_string($name) )
        {
            $this->config[$name] = $value;

			return $this->config;
        }	
	}

	public function url($page=1)
	{
		$param = empty($this->param) ? 'page' : $this->param;

		return url::join(array($param=>$page));
	}

	public function render()
	{
		$total = (int)$this->total; //总条数
		$pagesize = (int)$this->pagesize; //每页显示条数
		$page = (int)$this->page; //当前页码
		$showpage = (int)$this->showpage; //显示页码数,如值为10的时候，一共显示10个页码
		$showpage = 10;
		$maxpages = (int)$this->maxpages; //最多显示页数
		$offset = 2;
		
		if (  $total == 0 ||  $pagesize == 0 ) return '';

		//计算全部页数
		$totalpages = @ceil($total / $pagesize);
		$totalpages = $maxpages && $maxpages < $totalpages ? $maxpages : $totalpages; //最多显示页数计算

		//if ( $totalpages == 1 ) return '';

		//当前页码
		$page = $page <=0 ? 1 : $page;
		$page = $page > $totalpages ? $totalpages : $page;

		
		
		
		if ( $showpage > $totalpages )
		{
			$from = 1;
			$to = $totalpages;
		}
		else
		{
			$from = $page - $offset;
			$to = $from + $showpage -1;

			if ( $from < 1 )
			{
				$from = 1;
				$to = $page + 1 - $from;
				if ( $to - $from < $showpage )
				{
					$to = $showpage;
				}
			}
			elseif ( $to > $totalpages )
			{
				$from = $totalpages - $showpage + 1;
                $to = $totalpages;
			}

		}

		for($i = $from; $i <= $to; $i++) {
			if ( $i == $page )
			{
				$pages .= ' <li class="active">'.$i.'</li> ';
			}
			else
			{
				$pages .= ' <li><a href="'.$this->url($i).'">'.$i.'</a></li> ';
			}
        }



		
		//上下翻页
		$prev = $page - 1;
		$next = $page + 1;

		$prevPage = $prev > 0 ? '<li class="previous"><a href="'.$this->url($prev).'">'.$this->prev.'</a></li>' : '<li class="previous-off">'.$this->prev.'</li>';

		$nextPage = $next <= $totalpages ? '<li class="next"><a href="'.$this->url($next).'">'.$this->next.'</a></li>' : '<li class="next-off">'.$this->next.'</li>';

		$firstPage = $page == 1 ? '<li class="first-off">'.$this->first.'</li>' : '<li class="first"><a href="'.$this->url(1).'">'.$this->first.'</a></li>';

		$endPage = $page == $totalpages ? '<li class="end-off">'.$this->end.'</li>' : '<li class="end"><a href="'.$this->url($totalpages).'">'.$this->end.'</a></li>';


		$str = $this->template;
		$str = str_ireplace(
			array('$totalpages','$total','$pages','$page','$first','$prev','$next','$end'),
			array($totalpages,$total,$pages,$page,$firstPage,$prevPage,$nextPage,$endPage),
			$str
			);

		return $str;
	}
	

}
?>