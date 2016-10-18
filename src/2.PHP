<?php
$score_url = "http://ha.122.gov.cn/";
$capture_url = "http://ha.122.gov.cn/captcha?nocache=".md5(time().rand(100000,999999));
$cookie_str = "JSESSIONID-L=bae9ca87-6481-4554-b40a-7428336eb1a2;qt=716463";

$ch = curl_init($score_url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
curl_setopt($ch, CURLOPT_COOKIEJAR,  __DIR__."\\cookie.text");
curl_setopt($ch, CURLOPT_COOKIESESSION,  true);

$result = curl_exec ($ch);
curl_close($ch);

var_dump( __DIR__."\\cookie.text");
