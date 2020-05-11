<?php 
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
if(!empty($_GET['id'])&&!empty($_GET['n'])) {
    $sum = 3;
    $data = array();
    $list = array(8,3,5,17,6,7,13,12);
    $name = array('DM','YBD','ZP','ZM','YZ','OM','YM','WM');
    if(empty($list[$_GET['n']-1])) { exit('采集完成'); }
    if((($sum+1)-intval($_GET['id']))>0) { 
        $caiji = getVideo($list[intval($_GET['n'])-1], (($sum+1)-intval($_GET['id'])));
    }
    if(!empty($caiji)&&is_array($caiji)) {
        foreach($caiji['data'] as $key => $value) {
            if(strtotime($value['vod_addtime'])<(strtotime(date("Y-m-d")))){continue;}
            preg_match_all('/([a-zA-Z0-9\:\_\/\-\.]+jpg)/u', $value['vod_pic'], $vod_pic);
            if(empty($vod_pic[1])){continue;}
            $vod_pic = $vod_pic[1][0];
            preg_match_all('/([a-zA-Z0-9\_\～\-\.\s]+|[\x{4e00}-\x{9fff}]+|[\x{0800}-\x{4e00}]+|[\x{AC00}-\x{D7A3}]+|[\x{4e00}-\x{9fa5}]+)/u', str_replace(array('骗','下','迷','药','强','奸'),array('带'),$value['vod_name']), $vod_name);
            if(empty($vod_name[0])){continue;}
            $vod_all = explode("番号", implode('',$vod_name[1]));
            $vod_name = (empty($vod_all[1])?array($vod_all[0],$name[$_GET['n']-1].'_'.strtoupper(shortUrl(md5($vod_all[0])))):array($vod_all[0],strtoupper(shortUrl(md5($vod_all[1])))));
            preg_match_all('/([a-zA-Z0-9\:\_\/\-\.]+m3u8)/u', $value['vod_url'], $vod_url);
            if(empty($vod_url[1])){continue;}
            $vod_url = $vod_url[1][0];
            preg_match_all('/([\x{4e00}-\x{9fff}]+)/u', $value['vod_area'], $vod_area);
            if(empty($vod_area[1])){continue;}
            $vod_area = $vod_area[1][0];
            if(empty($value['vod_addtime'])){continue;}
            $vod_addtime = strtotime(date('Y-m-d h:i:s',strtotime($value['vod_addtime'])));
            preg_match_all('/([\x{4e00}-\x{9fff}]+)/u', trim($value['vod_name']), $getPY);
            $getPY = !empty($getPY[1])?getPinyin(implode('',$getPY[1])):'G';
            $data[] = '('.(intval($_GET['n'])+1).', \''.htmlspecialchars(trim($vod_name[0]),ENT_QUOTES).'\', \''.htmlspecialchars(trim($vod_name[1]),ENT_QUOTES).'\', \'\', \'\', \'\', \'\', \''.htmlspecialchars(trim($vod_name[0]),ENT_QUOTES).'\', \''.trim($vod_pic).'\', \''.$vod_area.'\', \'\', 0, \'0\', \'\', 1, \''.$vod_addtime.'\', '.mt_rand(60500,96080).', '.mt_rand(5080,8090).', '.mt_rand(500,900).', '.mt_rand(50,90).', \''.$vod_addtime.'\', 1, 1, '.mt_rand(20800,40900).', '.mt_rand(108,809).', \'m3u8\', \'\', \''.$vod_url.'\', \'admin\', \'\', \'\', \''.$getPY.'\', \'\', \''.mt_rand(6,9).'.'.mt_rand(2,5).'\', '.mt_rand(3050,7608).', 1, 0, 0, 0)';
        }
        if(!empty($data)&&is_array($data)) {
            shuffle($data);
            $data = 'INSERT INTO `ff_vod` (`vod_cid`, `vod_name`, `vod_title`, `vod_keywords`, `vod_color`, `vod_actor`, `vod_director`, `vod_content`, `vod_pic`, `vod_area`, `vod_language`, `vod_year`, `vod_continu`, `vod_total`, `vod_isend`, `vod_addtime`, `vod_hits`, `vod_hits_day`, `vod_hits_week`, `vod_hits_month`, `vod_hits_lasttime`, `vod_stars`, `vod_status`, `vod_up`, `vod_down`, `vod_play`, `vod_server`, `vod_url`, `vod_inputer`, `vod_reurl`, `vod_jumpurl`, `vod_letter`, `vod_skin`, `vod_gold`, `vod_golder`, `vod_isfilm`, `vod_filmtime`, `vod_length`, `vod_weekday`) VALUES '.implode(',',$data).';';
            file_put_contents('video/video'.intval($_GET['n']).'.txt', $data.PHP_EOL, FILE_APPEND|LOCK_EX);
            // 配置数据库连接
            getInsert('127.0.0.1','root','root','root',$data);
        }
    }
    $pid = (($sum+1)-intval($_GET['id']))>0?array((intval($_GET['id'])+1),intval($_GET['n'])):array(1,(intval($_GET['n'])+1));
    echo '第 '.intval($_GET['id']).' 页 栏目 '.intval($_GET['n']).PHP_EOL.'<script>window.location.href="/caiji.php?id='.$pid[0].'&n='.$pid[1].'";</script>';
}

function getVideo($cid = 1, $p = 1) {
    $jsonVideo = getJson('http://api.iixxzyapi.com/inc/feifei3/?a=json&cid='.intval($cid).'&g=plus&m=api&p='.intval($p),getRandIP());
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

function code62($x) {
    $show = '';
    while($x > 0) {
        $s = $x % 62;
        if ($s > 35) {
            $s = chr($s+61);
        } elseif ($s > 9 && $s <=35) {
            $s = chr($s + 55);
        }
        $show .= $s;
        $x = floor($x/62);
    }
    return $show;
}

function shortUrl($url) {
    $url = md5($url);
    $url = crc32($url);
    $result = sprintf("%u", $url);
    return code62($result);
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