<?php
$score_url = "http://ha.122.gov.cn/m/publicquery/scores";
$capture_url = "http://ha.122.gov.cn/captcha?nocache=".md5(time().rand(100000,999999));
$cookie_str = "JSESSIONID-L=bae9ca87-6481-4554-b40a-7428336eb1a2;qt=716463";

$ch = curl_init($capture_url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36');
curl_setopt($ch, CURLOPT_REFERER, 'http://ha.122.gov.cn/views/inquiry.html?q=j');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_COOKIE,  $cookie_str);
curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
$output = curl_exec ($ch);
curl_close($ch);

file_put_contents("asd.png",$output);

header("Content-Type:text/html;charset=UTF-8");
date_default_timezone_set("PRC");
$showapi_appid = '25733';  //替换此值,在官网的"我的应用"中找到相关值
$showapi_secret = '5fb49c950bb94643bc22814bdab76a24';  //替换此值,在官网的"我的应用"中找到相关值
$paramArr = array(
    'showapi_appid'=> $showapi_appid,
    'typeId'=> "3040",
    'convert_to_jpg'=> "0",
    "img_base64"=>base64_encode(file_get_contents("asd.png")),
);

//创建参数(包括签名的处理)
function createParam ($paramArr,$showapi_secret) {
    $paraStr = "";
    $signStr = "";
    ksort($paramArr);
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $signStr .= $key.$val;
            $paraStr .= $key.'='.urlencode($val).'&';
        }
    }
    $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
    $sign = strtolower(md5($signStr));
    $paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
    return $paraStr;
}

$param = createParam($paramArr,$showapi_secret);
$url = 'http://route.showapi.com/184-2?'.$param;
$result = file_get_contents($url);
$result = json_decode($result);
var_dump($result);
$code = $result->showapi_res_body->Result;
print_r($code);
echo "<br>\r\n";
echo "<img src='asd.png'/>";
echo "<br>\r\n";

$jszh = "410511198407251231";
$dabh = "410500798165";
$jszh = "410503198104030511";
$dabh = "410500712909";

$query_str = "jszh=$jszh&dabh=$dabh&qm=wf&page=1&captcha=$code";
$ch = curl_init($score_url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36');
curl_setopt($ch, CURLOPT_REFERER, 'http://ha.122.gov.cn/views/inquiry.html');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie_str); //存储cookies
curl_setopt($ch, CURLOPT_POSTFIELDS,$query_str);
curl_setopt($ch, CURLOPT_POST,1);
$output = curl_exec ($ch);
curl_close($ch);
echo "<br>\r\n";
var_dump($output);
echo "<br>\r\n";