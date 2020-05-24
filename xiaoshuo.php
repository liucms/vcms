<?php 
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
if(!empty($_GET['id'])&&!empty($_GET['n'])) {
    $sum = 3;
    $data = array();
    $list = array(19,18,21,22,23,24);
    if(empty($list[$_GET['n']-1])) { exit('采集完成'); }
    if((($sum+1)-intval($_GET['id']))>0) { 
        $caiji = getNews($list[intval($_GET['n'])-1], (($sum+1)-intval($_GET['id'])));
    }
    if(!empty($caiji)&&is_array($caiji)) {
        foreach($caiji['list'] as $key => $value) {
            if(strtotime($value['art_time'])<(strtotime(date("Y-m-d")))){continue;}
            preg_match_all('/([a-zA-Z0-9\_\～\-\.\s]+|[\x{4e00}-\x{9fff}]+|[\x{0800}-\x{4e00}]+|[\x{AC00}-\x{D7A3}]+|[\x{4e00}-\x{9fa5}]+)/u', str_replace(array('骗','下','迷','药','强','奸'),array('带'),$value['art_title']), $art_title);
            if(empty($art_title[0])){continue;}
            $art_name = implode('',$art_title[1]);
            preg_match_all('/([a-zA-Z0-9]+|\<p\>+|\<\/p\>+|\<br\/\>+|\<br\>+|\<br \>+|\<br \/\>+|[\x{4e00}-\x{9fff}]+|[\x{0800}-\x{4e00}]+|[\x{AC00}-\x{D7A3}]+|[\x{4e00}-\x{9fa5}]+)/u', $value['art_content'], $art_content);
            if(empty($art_content[1])){continue;}
            $art_contents = (mb_strlen($art_contents,'utf-8')>12048?mb_strcut($art_contents,0,12048,'utf-8'):$art_contents);
            $art_contents = preg_replace(array('/<br\/><br\/>/u','/\s/u','/nbspnbsp/u','/<p><br\/><\/p>/u','/<br\/>/u'),array('</p><p>','',''),implode('',$art_content[1]));
            if(empty($value['art_time'])){continue;}
            $art_addtime = strtotime(date('Y-m-d h:i:s',strtotime($value['art_time'])));
            preg_match_all('/([\x{4e00}-\x{9fff}]+)/u', trim($value['art_name']), $getPY);
            $getPY = !empty($getPY[1])?getPinyin(implode('',$getPY[1])):'G';
            $data[] = '('.(intval($_GET['n'])+10).', \''.htmlspecialchars(trim($art_name),ENT_QUOTES).'\', \''.htmlspecialchars(trim($art_name),ENT_QUOTES).'\', \'\', \'\', \'\', \'\', \''.htmlspecialchars(trim($art_name),ENT_QUOTES).'\', \''.trim(fixHtml($art_contents)).'\', '.mt_rand(6050,9608).', '.mt_rand(5,9).', '.mt_rand(50,90).', '.mt_rand(500,900).', '.$art_addtime.', 1, 1, '.mt_rand(2080,4090).', '.mt_rand(10,40).', \'\', \''.$getPY.'\', '.$art_addtime.', \'\', \''.mt_rand(6,9).'.'.mt_rand(2,5).'\', '.mt_rand(3050,7608).')';
        }
        if(!empty($data)&&is_array($data)) {
            $data = 'INSERT INTO `ff_news` (`news_cid`, `news_name`, `news_keywords`, `news_color`, `news_pic`, `news_inputer`, `news_reurl`, `news_remark`, `news_content`, `news_hits`, `news_hits_day`, `news_hits_week`, `news_hits_month`, `news_hits_lasttime`, `news_stars`, `news_status`, `news_up`, `news_down`, `news_jumpurl`, `news_letter`, `news_addtime`, `news_skin`, `news_gold`, `news_golder`) VALUES '.implode(',',$data).';';
            //file_put_contents('xiaoshuo/xiaoshuo'.intval($_GET['n']).'.txt', $data.PHP_EOL, FILE_APPEND|LOCK_EX);
            // 配置数据库连接
            getInsert('127.0.0.1','root','root','root',$data);
        }
    }
    $pid = (($sum+1)-intval($_GET['id']))>0?array((intval($_GET['id'])+1),intval($_GET['n'])):array(1,(intval($_GET['n'])+1));
    echo '第 '.intval($_GET['id']).' 页 栏目 '.intval($_GET['n']).PHP_EOL.'<script>window.location.href="/xiaoshuo.php?id='.$pid[0].'&n='.$pid[1].'";</script>';
}

function getNews($cid = 1, $p = 1) {
    $jsonVideo = getJson('https://lbapi9.com/api.php/provide/art/at/json/?ac=detail&t='.intval($cid).'&pg='.intval($p).'&h=&ids=&wd=',getRandIP());
    if(!empty($jsonVideo[1])&&$jsonVideo[1] == 200) {
        return json_decode($jsonVideo[0], true);
    }
    return '404';
}

function getInsert($host,$user,$passwd,$db,$sql){
    try {
        $conn = new PDO('mysql:host='.$host.';dbname='.$db, $user, $passwd);
        $conn->query("set names utf8");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec($sql);
        echo "新记录插入成功";
    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();exit();
    }
    $conn = null;
}

function getJson($url, $randIP) {
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
        CURLOPT_COOKIEFILE => __DIR__.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cookie.txt',
        CURLOPT_HTTPHEADER => array('Content-Type: text/plain', 'X-FORWARDED-FOR:' . $randIP, 'CLIENT-IP:' . $randIP),
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec ($ch);
    $Code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($result, $Code);
}

function getRandIP() {
    $ip2id= round(rand(600000, 2550000) / 10000);
    $ip3id= round(rand(600000, 2550000) / 10000);
    $ip4id= round(rand(600000, 2550000) / 10000);
    $_array = array('218','218','66','66','218','218','60','60','202','204','66','66','66','59','61','60','222','221','66','59','60','60','66','218','218','62','63','64','66','66','122','211');
    $randarr= mt_rand(0,count($_array)-1);
    $ip1id = $_array[$randarr];
    return $ip1id.'.'.$ip2id.'.'.$ip3id.'.'.$ip4id;
}

//生成字母前缀
function getPinyin($s0){
	$firstchar_ord=ord(strtoupper(mb_substr($s0,0,1))); 
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return mb_substr($s0,0,1); 
	$s=@iconv("UTF-8","GBK//IGNORE", $s0); 
	$asc=ord(mb_substr($s,0,1))*256+ord(mb_substr($s,1,1))-65536; 
	if($asc>=-20319 and $asc<=-20284)return "A";
	if($asc>=-20283 and $asc<=-19776)return "B";
	if($asc>=-19775 and $asc<=-19219)return "C";
	if($asc>=-19218 and $asc<=-18711)return "D";
	if($asc>=-18710 and $asc<=-18527)return "E";
	if($asc>=-18526 and $asc<=-18240)return "F";
	if($asc>=-18239 and $asc<=-17923)return "G";
	if($asc>=-17922 and $asc<=-17418)return "H";
	if($asc>=-17417 and $asc<=-16475)return "J";
	if($asc>=-16474 and $asc<=-16213)return "K";
	if($asc>=-16212 and $asc<=-15641)return "L";
	if($asc>=-15640 and $asc<=-15166)return "M";
	if($asc>=-15165 and $asc<=-14923)return "N";
	if($asc>=-14922 and $asc<=-14915)return "O";
	if($asc>=-14914 and $asc<=-14631)return "P";
	if($asc>=-14630 and $asc<=-14150)return "Q";
	if($asc>=-14149 and $asc<=-14091)return "R";
	if($asc>=-14090 and $asc<=-13319)return "S";
	if($asc>=-13318 and $asc<=-12839)return "T";
	if($asc>=-12838 and $asc<=-12557)return "W";
	if($asc>=-12556 and $asc<=-11848)return "X";
	if($asc>=-11847 and $asc<=-11056)return "Y";
	if($asc>=-11055 and $asc<=-10247)return "Z";
	return 0;//null
}

function fixHtml($srt){
    $srt = preg_replace('/<[^>]*$/','',$srt);
    preg_match_all('/<([a-z]+)(?: .*)?(?<![/|/ ])>/iU', $srt, $result);
    if($result){
        $opentags = $result[1];
        preg_match_all('/</([a-z]+)>/iU', $srt, $result);
        if($result){
            $closetags = $result[1];
            $len_opened = count($opentags);
            if (count($closetags) == $len_opened) {
                return $srt;  //没有未关闭标签
            }
            $opentags = array_reverse($opentags);
            $sc = array('br','input','img','hr','meta','link');  //跳过这些标签
            for ($i=0; $i < $len_opened; $i++) {
                $ot = strtolower($opentags[$i]);
                if (!in_array($opentags[$i], $closetags) && !in_array($ot,$sc)) {
                    $srt .= '</'.$opentags[$i].'>';  //补齐标签
                } else {
                    unset($closetags[array_search($opentags[$i], $closetags)]);
                }
            }
        }
    }
    return $srt;
}
