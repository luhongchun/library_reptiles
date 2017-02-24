##PHP多进程实现图书馆图书爬虫(限校园网访问)

####文件
library.php
####运行方式
$php -f library.php
####运行结果
爬取成功：图书信息存入数据库library_books表  
爬取失败：存入redeal_no表  
运行信息：存入library.log日志文件  
top：表示图书信息解析出错  
under：表示因为图书状态就判断没有该图书  
订购：表示因为处于订购原因没有爬取成功  
验收：表示因为处于验收原因没有爬取成功  
title is null：表示图书以存入数据库，但是没有名字  
auther is null：表示图书以存入数据库，但是没有著作  
press is null：表示图书以存入数据库，但是没有出版社  
time is null：表示图书以存入数据库，但是没有出版时间  
###后期处理
对于redeal_no表中timeout数据，运行redeal.php处理  
对于top、under、title、auther、press、time数据，尽可能排查并修改正则以兼容此类问题  
对于订购、验收数据，认定暂时图书信息有误或不存在，可以先略过。  
####文件
redeal.php
####运行方式
$php -f redeal.php
####运行输出
控制台监控输出
####运行结果
对数据库redeal_no表中timeout数据，再进行爬取处理，如果成功，就删除该条记录。并将图书信息存入数据库library_books表，反之存入redeal_no表，并且备注原因（timeout，top，under，订阅，验收等）。
####后期处理
尽可能排除所有timeout数据。
####文件
reptile.php
####类型
图书详情页爬虫处理
####文件
library_books.sql
####类型
数据库library_books表结构
####文件
timeout_no.sql
####类型
数据库timeout_no表结构
####文件
library.log
####类型
library.php程序运行日志文件
