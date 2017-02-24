<?php

include "reptile.php";

error_reporting(E_ALL ^ E_NOTICE);

$max = 170000;      //最大数据估计值170000
$workers = 10;      //进程数量
//因为多进程进行爬虫，如果出现timeout量过多，可以分多次进行，以免一次性过多的timeout，或在只能选择全部重新开始的情况。
$times = 1;       //分几次进行
$now = 1;          //当前第几次

/*
 * 开启多进程方式调用爬虫主入口函数
 */
$pids = array();
for($i = 0; $i < $workers; $i++){
    $pids[$i] = pcntl_fork();
    switch ($pids[$i]) {
    case -1:
        print "fork error----------------------------------------------------------\n";
        exit;
    case 0:
        $minno = ($max/$times) / $workers * $i + ($max/$times*($now-1));
        $maxno = ($max/$times) / $workers * ($i+1) + ($max/$times*($now-1));
        main_action($minno, $maxno);
        exit;
    default:
        break;
    }
}

foreach ($pids as $i => $pid) {     //主进程等待所有子进程都结束了才退出
    if($pid) {
        pcntl_waitpid($pid, $status);
    }
}

