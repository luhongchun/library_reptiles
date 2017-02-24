-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-05-07 10:26:20
-- 服务器版本： 5.5.47-MariaDB
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cityuit`
--

-- --------------------------------------------------------

--
-- 表的结构 `library_books`
--

CREATE TABLE IF NOT EXISTS `library_books` (
          `id` int(11) NOT NULL COMMENT '自增量',
          `no` int(11) NOT NULL COMMENT 'url编号',
          `title` varchar(80) NOT NULL COMMENT '书名',
          `auther` varchar(80) NOT NULL COMMENT '作者',
          `press` varchar(80) NOT NULL COMMENT '出版社',
          `time` varchar(8) NOT NULL COMMENT '出版日期',
          `search` varchar(20) NOT NULL COMMENT '索书号',
          `place` varchar(120) NOT NULL COMMENT '馆藏地',
          `state` varchar(8) NOT NULL COMMENT '状态'

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `library_books`
--
ALTER TABLE `library_books`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `library_books`
--
ALTER TABLE `library_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增量';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
