-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-05-07 10:27:28
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
-- 表的结构 `redeal_no`
--

CREATE TABLE IF NOT EXISTS `redeal_no` (
          `id` int(11) NOT NULL COMMENT '自增量',
          `no` varchar(10) NOT NULL COMMENT '超时编号',
          `remark` varchar(10) CHARACTER SET ucs2 NOT NULL COMMENT '备注信息'

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `redeal_no`
--
ALTER TABLE `redeal_no`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `redeal_no`
--
ALTER TABLE `redeal_no`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增量';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
