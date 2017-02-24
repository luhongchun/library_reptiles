<?php
include "reptile.php";

error_reporting(E_ALL ^ E_NOTICE);

$trouble = 'timeout';    //可以根据情况选要再处理的条件

$mysql=new PDO('mysql:host=localhost;dbname=cityuit','root','q123456');
$mysql->query('set names utf8');//设置字符集
/* $res = $mysql->query("select no from redeal_no where remark != '$trouble'"); */
$res = $mysql->query("select no from redeal_no where remark = '$trouble'");
foreach($res->fetchAll() as $row){
    $noStr = str_pad($row[0], 10, "0", STR_PAD_LEFT);
    $bookmes = check_books($noStr);
    if($bookmes['res'] == 201){
        $sql = "update redeal_no set remark = '$bookmes[mes]' where no = $row[0]";    //更改状态
        $mysql->exec($sql);
        $mes = "no=> $row[0] ,no book for $bookmes[mes]\n";
        print $mes;
    }else if($bookmes['res'] == 501){                //如果超时了，就干脆不做任何修改
        $mes = "no=> $row[0] ,no book for $bookmes[mes]\n";
        print $mes;
    } else{ 
        $insert = save_book($row[0], $bookmes);
        if($insert['result'] === FALSE || $insert['result'] == 0){    //如果存储失败。不做保存，不删除
            $mes = "no=> $no ,no book for mysql error\n";
            print $mes;
        }else{
            $sql = "delete from redeal_no where no = $row[no]";   //如果成功了，就将数据删除
            $mysql->exec($sql);
            if($bookmes['title'] == ''){
                $mes = "no=> $row[0] ,line $insert[lineId] but title is null\n";
            }else if($bookmes['auther'] == ''){
                $mes = "no=> $row[0] ,line $insert[lineId] but auther is null\n";
            }else if($bookmes['press'] == ''){
                $mes = "no=> $row[0] ,line $insert[lineId] but press is null\n";
            }else if($bookmes['time'] == ''){
                $mes = "no=> $row[0] ,line $insert[lineId] but time is null\n";
            }else{
                $mes = "no=> $row[0] ,line $insert[lineId]\n";
            }
            print $mes;
        }
    }
}

