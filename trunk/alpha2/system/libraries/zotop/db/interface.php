<?php
interface zotop_database_interface{

	/**
	 * 查询数据库
	 *
	 * @param string $sql
	 */
	public function query($sql);

	/**
	 * 查找最后一次插入的id号
	 * @return int
	 */
	public function insertId();

	/**
	 * 连接数据库
	 *
	 * @param array $config
	 * @return boolean
	 */
	public function connect();

	public function size();



}