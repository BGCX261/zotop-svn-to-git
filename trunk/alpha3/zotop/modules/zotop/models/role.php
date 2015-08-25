<?php
defined('ZOTOP') OR die('No direct access allowed.');
/**
 * 系统的角色模型，完成对角色的基本操作
 *
 * @copyright  (c)2009 zotop team
 * @package    core
 * @author     zotop team
 * @license    http://zotop.com/license.html
 */
class role_model extends model
{
	protected $primaryKey = 'id';
	protected $tableName = 'role';

	public function isExist()
	{
	    return false;
	}
}
?>