-- phpMyAdmin SQL Dump
-- version 2.11.9.2
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1:3306
-- 生成日期: 2010 年 03 月 10 日 07:24
-- 服务器版本: 5.1.28
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `zotopcms`
--

-- --------------------------------------------------------

--
-- 表的结构 `zotop_content`
--

DROP TABLE IF EXISTS `zotop_content`;
CREATE TABLE IF NOT EXISTS `zotop_content` (
  `id` int(10) NOT NULL COMMENT '内容编号',
  `title` varchar(100) NOT NULL COMMENT '内容标题'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容模块主表';

--
-- 导出表中的数据 `zotop_content`
--

