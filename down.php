<?php 
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
if(!empty($_GET['id'])&&!empty($_GET['n'])) {
    $sum = 2;
    $data = array();
    $list = array(5,7,8,11,12,13,17);
    if(empty($list[intval($_GET['n'])-1])) { exit('采集完成'); }
    if(intval($_GET['id'])<($sum+1)) {
        $caiji = getVideo($list[intval($_GET['n'])-1], (($sum+1)-intval($_GET['id'])));
    }
    if(!empty($caiji)&&is_array($caiji)) {
        foreach($caiji['data'] as $key => $value) {
            preg_match_all('/([a-zA-Z0-9\:\_\/\-\.]+[png|jpg])/u', $value['vod_pic'], $vod_pic);
            if(empty($vod_pic[1])){continue;}
            $vod_pic = $vod_pic[1][0];
            preg_match_all('/([a-zA-Z0-9\_\～\-\.\s]+|[\x{4e00}-\x{9fff}]+|[\x{0800}-\x{4e00}]+|[\x{AC00}-\x{D7A3}]+|[\x{4e00}-\x{9fa5}]+)/u', str_replace(array('骗','下','迷','药','强','奸'),array('带'),$value['vod_name']), $vod_name);
            if(empty($vod_name[0])){continue;}
            $vod_name = implode('',$vod_name[1]);
            preg_match_all('/(http.*?\.mp4)/u', $value['vod_url'], $vod_url);
            if(empty($vod_url[1])){continue;}
            $vod_url = $vod_url[1][0];
            $data['img'][] = 'bitsadmin /transfer '.$key.' /download /priority normal "'.$vod_pic.'" "D:\xiazai\\'.intval($list[intval($_GET['n'])-1]).'\\'.$vod_name.'.jpg"';
            $data['vod'][] = 'bitsadmin /transfer '.$key.' /download /priority normal "'.$vod_url.'" "D:\xiazai\\'.intval($list[intval($_GET['n'])-1]).'\\'.$vod_name.'.mp4"';
        }
        if(!empty($data)&&is_array($data)) {
            $f = fopen('video/'.$list[intval($_GET['n'])-1].'i.bat', "a+");
            $text = iconv("UTF-8","GBK//TRANSLIT",implode(PHP_EOL,$data['img']).PHP_EOL);
            fputs($f, $text);
            fclose($f);
            $f = fopen('video/'.$list[intval($_GET['n'])-1].'m.bat', "a+");
            $text = iconv("UTF-8","GBK//TRANSLIT",implode(PHP_EOL,$data['vod']).PHP_EOL);
            fputs($f, $text);
            fclose($f);
        }
    }
    $pid = ((intval($_GET['id'])+1)>$sum)?array(1,intval($_GET['n'])+1):array((intval($_GET['id'])+1),intval($_GET['n']));
    echo '第 '.intval($_GET['id']).' 页 栏目 '.intval($_GET['n']).PHP_EOL.'<script>window.location.href="/down.php?id='.$pid[0].'&n='.$pid[1].'";</script>';
}

function getVideo($cid = 1, $p = 1) {
    $jsonVideo = getJson('http://api.iixxzyapi.com/inc/feifei3down/?a=json&cid='.intval($cid).'&g=plus&m=api&p='.intval($p));
    if(!empty($jsonVideo[1])&&$jsonVideo[1] == 200) {
        return json_decode($jsonVideo[0], true);
    }
    return '404';
}

function getJson($url) {
    $ch = curl_init();
    $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:75.0) Gecko/20100101 Firefox/75.0';
    $options =  array(
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPGET => TRUE,
        CURLOPT_NOBODY => FALSE,
        CURLOPT_HEADER => FALSE,
        CURLOPT_REFERER => $url,
        CURLOPT_USERAGENT => $user_agent,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec ($ch);
    $Code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($result, $Code);
}