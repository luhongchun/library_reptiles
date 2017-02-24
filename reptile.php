<?php

/*
 * 图书爬取主入口
 * @param $minno最小值
 * @param $maxno最大值
 */
function main_action($minno, $maxno){
    file_out("$minno=================$maxno\n");
    print "$minno=================$maxno\n";
    for($no=$minno; $no<$maxno; $no++){
        $noStr = str_pad($no, 10, "0", STR_PAD_LEFT);
        $bookmes = check_books($noStr);    //固定10字节长度，用0填充
        if($bookmes['res'] == 201 || $bookmes['res'] == 501){
            save_no($no,$bookmes['mes']);
            $mes = "no=> $no ,no book for $bookmes[mes]\n";
            file_out($mes);
            print $mes;
        } else{ 
            $insert = save_book($no, $bookmes);
            if($insert['result'] === FALSE || $insert['result'] == 0){    //如果存储失败。存储到错误表
                save_no($no, 'error');            
                $mes = "no=> $no ,no book for mysql error\n";
                file_out($mes);
                print $mes;
            }else{
                if($bookmes['title'] == ''){
                    $mes = "no=> $no ,line $insert[lineId] but title is null\n";
                }else if($bookmes['auther'] == ''){
                    $mes = "no=> $no ,line $insert[lineId] but auther is null\n";
                }else if($bookmes['press'] == ''){
                    $mes = "no=> $no ,line $insert[lineId] but press is null\n";
                }else if($bookmes['time'] == ''){
                    $mes = "no=> $no ,line $insert[lineId] but time is null\n";
                }else{
                    $mes = "no=> $no ,line $insert[lineId]\n";
                }
                file_out($mes);
                print $mes;
            }
        }
    }
}

/* main_test(); */

/*
 * 单条数据测试使用，测试时，将开启进程关闭
 */
function main_test(){
    $res = check_books($_GET['id']);
    print_r($res);
}

/*
 * 将图书信息保存到数据库library_books表
 */
function save_book($no, $bookmes){
    $pdo=new PDO('mysql:host=localhost;dbname=cityuit','root','q123456');
    $pdo->query('set names utf8');//设置字符集
    $strSql = "INSERT INTO `library_books`(`no`, `title`, `auther`, `press`, `time`, `search`, `place`, `state`) VALUES ('$no', '$bookmes[title]','$bookmes[auther]','$bookmes[press]','$bookmes[time]','$bookmes[search]','$bookmes[place]','$bookmes[state]')";
    $result = $pdo->exec($strSql);//返回影响了多少行数据
    $lineId = $pdo->lastInsertId();//返回刚插入的id(的自增id)
    return array("result"=>$result, "lineId"=>$lineId);
}

/*
 * 将不成功信息保存到数据库timeout_no表
 */
function save_no($no, $mes){
    $pdo=new PDO('mysql:host=localhost;dbname=cityuit','root','q123456');
    $pdo->query('set names utf8');//设置字符集
    $strSql = "INSERT INTO `redeal_no`(`no`, `remark`) VALUES ('$no','$mes')";
    $pdo->exec($strSql);//返回影响了多少行数据
}

/*
 * 将控制台信息保存到日志文件，定期清理
 */
function file_out($mes){
    file_put_contents("library.log" , $mes , FILE_APPEND);
}

/*
 * 对图书详情页进行数据正则匹配，爬取
 */
function check_books($no){
    $content = http_get("http://210.30.108.79/opac/item.php?marc_no=$no");    //图书详情页接口

    if(!$content){
        return array("res"=>501,"mes"=>"timeout");   //表示没有这本，因为超时
    }

    $contents = preg_replace("/([\r\n|\n|\t| ]+)/",'',$content);  //为更好地避开换行符和空格等不定因素的阻碍，有必要先清除采集到的源码中的换行符、空格符和制表符
    $contents = html_entity_decode($contents);     //将&#x0020;字符转中文
    $contents = preg_replace('/<\/a>/','',$contents);   //先提前将</a>给删了，免去判断
    /* echo $contents; */

    //先确定有没有这本书，然后去解析书的信息

    $preg1 = '/此书刊可能正在订购中或者处理中/';
    $preg1_1 = '/正常验收/';
    if(preg_match($preg1, $contents)){
        return array("res"=>201,"mes"=>"订购");   //表示没有这本，并且是因为订购，返回就直接终止
    }else if(preg_match($preg1_1, $contents)){
        return array("res"=>201,"mes"=>"验收");   //表示没有这本，并且是因为验收，返回就直接终止
    }

    $bookarr = array();        
    $preg2 = '/<table.*索书号.*<\/table>/U';
    if(preg_match($preg2, $contents, $out2)){
        $preg3 = '/<tr.*>(.*)<\/tr>/U';
        if(preg_match_all($preg3, $out2[0], $out3)){
            $preg4 = '/<td.*>(.*)<\/td>/U';
            for($i=1;$i<count($out3[1]);$i++){
                if(preg_match_all($preg4,$out3[1][$i],$out4)){
                    $bookarr[] = $out4[1];    //将每项最后结果放入数组
                }
            }
        }
    }
    if(!empty($bookarr)){
        $state = '已借出';
        for($i=0;$i<count($bookarr);$i++){
            $search = $bookarr[$i][0];                     //变量表示索书号

            $preg5 = '/>.*库.*库(.*)</U';
            preg_match($preg5, $bookarr[$i][3], $out5);
            $place = $out5[1];                        //变量表示馆藏位置

            $preg6 = '/>(.*)</U';
            preg_match($preg6, $bookarr[$i][4], $out6);
            if($out6[1] == '可借'){
                $state = '可借';
            }
            /* $state = $out6[1] == '可借' ? '可借' : '已借出';           //变量表示是否可借 */
        }
    }else{
        return array("res"=>201,"mes"=>"under");   //表示没有这本，并且是因为下面匹配为空导致，返回就直接终止
    }

    $preg7 = '/题名\/责任者:<\/dt><dd><a.*>(.*)<\/dd>.*出版发行项:<\/dt><dd>(.*)<\/dd>/U';
    $preg7_1 = '/题名\/责任者:<\/dt><dd><a.*>(.*)<\/dd>/U';      //存在没有出版社和出版时间的图书

    if(preg_match($preg7, $contents, $out7)){
        $bookarr = bookmes_top($out7, $contents);
        $bookarr['search'] = $search;
        $bookarr['place'] = $place;
        $bookarr['state'] = $state;
        return $bookarr;
    }else if(preg_match($preg7_1, $contents, $out7)){
        $bookarr = bookmes_top($out7, $contents);
        $bookarr['search'] = $search;
        $bookarr['place'] = $place;
        $bookarr['state'] = $state;
        return $bookarr;
    }else{
        return array("res"=>201,"mes"=>"top");   //表示没有这本，并且是因为上面匹配为空导致，返回就直接终止
    }
}

/*
 * 对图书信息的解析
 */
function bookmes_top($out, $contents){
    if(preg_match('/\//',$out[1])){             //某些存在多个/字符的问题解决
        $title = substr($out[1],0,strrpos($out[1],'/'));     //截取字符串开头到最后一个/字符的字符串，一般为书名信息
        $auther = substr($out[1],strrpos($out[1],'/')+1, strlen($out[1]));    //截取最后一个/字符到结尾的字符串，一般为著者信息
    }else{//表示第一行没有著者的情况
        $title = $out[1];
        $preg8_1 = '/个人责任者.*<a.*>(.*)<\/dd>/U';    //特殊处理，将主编取出
        $preg8_2 = '/团体责任者.*<a.*>(.*)<\/dd>/U';
        if(preg_match($preg8_1, $contents, $auth)){
            $auther = $auth[1];
        }else if(preg_match($preg8_2, $contents, $auth)){
            $auther = $auth[1];
        }
    }

    if($out[2] == NULL){      //如果是匹配的7_1，$out[2]就是空的了,但是不想对这些书的出版社和出版时间全部放弃
        $preg8_3 = '/载体形态项.*<dd>(.*)<\/dd>/U';   //只能对个别问题特殊处理
        if(preg_match($preg8_3, $contents, $auth)){
            $real = $auth[1];
            if(preg_match('/:/',$real)){   //有没有：字符，如果有应该是地点名词，舍去
                $real = substr($auth[1],strpos($auth[1],':')+1, strlen($auth[1]));    //截取最后一个,字符到结尾的字符串，一般为出版时间信息
            }
            if(preg_match('/;/',$real)){             //某些存在没有,字符的问题解决
                $press = substr($real,0,strrpos($real,';'));        //截取字符串开头到最后一个,字符的字符串，一般为出版社信息
                $time = substr($real,strrpos($real,';')+1, strlen($real));    //截取最后一个,字符到结尾的字符串，一般为出版时间信息
            }else{//表示没有出版时间信息
                $press = $real;
            }
        }
    }else if(preg_match('/:/',$out[2])){   //有没有：字符，如果有应该是地点名词，舍去
        $real = substr($out[2],strpos($out[2],':')+1, strlen($out[2]));    //截取最后一个,字符到结尾的字符串，一般为出版时间信息
        if(preg_match('/,/',$real)){             //某些存在没有,字符的问题解决
            $press = substr($real,0,strrpos($real,','));        //截取字符串开头到最后一个,字符的字符串，一般为出版社信息
            $time = substr($real,strrpos($real,',')+1, strlen($real));    //截取最后一个,字符到结尾的字符串，一般为出版时间信息
        }else{//表示没有出版时间信息
            $press = $real;
        }
    }else{
        $press = $out[2];
    }

    return array("res"=>200, "title"=>$title, "auther"=>$auther, "press"=>$press, "time"=>$time);
}


/**
 * GET 请求
 * @param string $url
 */
function http_get($url){
    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_TIMEOUT,30);   //只需要设置一个秒的数量就可以  
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}
?>
