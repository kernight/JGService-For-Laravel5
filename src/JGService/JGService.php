<?php
namespace steveLiuxu\JGService;

use Illuminate\Support\Facades\Log;

class JGService{
    /***
     * 交管服务平台会话Cookie
     * @var string
     */
    protected $JG_cookies = "";

    /***
     * 交管服务平台地址
     * @var string
     */
    protected $JG_base_url = "";

    /**
     * 交管服务平台验证码获取地址
     * @var string
     */
    protected $JG_captcha_url = "";

    /**
     * 交管服务平台驾照查分地址
     * @var string
     */
    protected $JG_score_url = "";

    /***
     * 易源数据平台接口数据
     *
     * @var array
     */
    protected $show_api_param = [
        "app_id"=>"",
        "app_secret"=>""
    ];
    /**
     * 图片的识别代码
     * 参数详情:https://www.showapi.com/api/lookPoint/184
     *
     * @var string
     */
    protected $typeId = "3040";

    /***
     * 数据平台验证码识别接口地址
     *
     * @var string
     */
    protected $showapi_url = "";

    /**
     *设置查询区域
     * todo:根据不同区域设置不同的查询接口
     * @param string $CityCode  查询区域代码
     * @return $this
     */
    public function SetQueryArea($CityCode)
    {
        $this->SetJGBaseUrl("http://ha.122.gov.cn/");
        return $this;
    }

    /**
     * 设置易源接口的信息
     *
     * @param string $app_id 易源接口app_id 在官网的"我的应用"中找到相关值
     * @param string $app_secret 易源接口app密钥 在官网的"我的应用"中找到相关值
     * @param string $url 易源验证码识别接口地址
     * @return $this
     */
    public function SetShowApiParam($app_id,$app_secret,$url)
    {
        $this->show_api_param["app_id"] = $app_id;
        $this->show_api_param["app_secret"] = $app_secret;
        $this->showapi_url = $url;

        return $this;
    }

    /**
     * 设置验证码识别的类型
     *
     * @param int $typeId 识别类型代码
     * @return $this
     */
    public function SetTypeId($typeId){
        $this->typeId = $typeId;

        return $this;
    }

    /***
     * 获取驾驶证扣分情况
     *
     * @param string $license_number 驾驶证号码
     * @param string $file_number 驾驶证档案编号
     * @param string $area_code 驾驶证所在区域
     *
     * @return array
     */
    public function GetScore($license_number,$file_number,$area_code)
    {
        $query_str = "jszh=$license_number&dabh=$file_number&qm=wf&page=1&captcha=".$this->GetCaptchaChar();

        $res = $this->SetQueryArea($area_code)->GetHttpResponse($this->JG_score_url,$query_str);

        return $res;
    }

    /***
     * 创建参数(包括签名的处理)
     *
     * @param array $paramArr 参数列表
     * @return string 返回查询字符串
     */
    protected function CreateParam ($paramArr) {
        $paraStr = "";
        $signStr = "";
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $signStr .= $key.$val;
                $paraStr .= $key.'='.urlencode($val).'&';
            }
        }
        #排好序的参数加上secret,进行md5
        $signStr .= $this->show_api_param["app_secret"];
        $sign = strtolower(md5($signStr));
        #将md5后的值作为参数,便于服务器的效验
        $paraStr .= 'showapi_sign='.$sign;

        return $paraStr;
    }

    /***
     * 设置交管服务平台验证码获取地址
     *
     * @param string $url 交管服务平台验证码获取地址
     * @return $this
     */
    protected function SetJGBaseUrl($url)
    {
        $this->JG_base_url = $url;
        $this->JG_captcha_url = $url."captcha";
        $this->JG_score_url = $url."m/publicquery/scores";
        return $this;
    }

    /***
     * 设置交管服务平台会话Cookie
     *
     * @return $this
     */
    protected function SetJGCookies()
    {
        $header = $this->GetHttpResponse($this->JG_base_url,"",1);
        #正则提取cookie信息
        if(preg_match('/JSESSIONID[^;]*/',$header,$match)){
            $this->JG_cookies = $match[0];
        }

        return $this;
    }

    /***
     * 获取并识别交管服务平台验证码
     *
     * @return false|string 成功返回验证码图片的识别结果，失败返回false
     */
    protected function GetCaptchaChar(){
        $img_data =  $this->SetJGCookies()->GetHttpResponse($this->JG_captcha_url,"nocache=".md5(time().rand(11111,99999)));

        return $this->RecognitionCaptcha($img_data);
    }

    /***
     * 识别验证码
     *
     * @param string $img_data 验证码图片字节流
     * @return bool|string 成功返回识别结果，失败返回false
     */
    protected function RecognitionCaptcha($img_data){
        $paramArr = array(
            'showapi_appid'=> $this->show_api_param["app_id"],
            'typeId'=> $this->typeId,
            'convert_to_jpg'=> "1",
            "img_base64"=>base64_encode($img_data),
        );

        $query_str = $this->CreateParam($paramArr);

        #获取识别数据
        try{
            $res = $this->GetHttpResponse($this->showapi_url,$query_str);
            if(!$res){
                return false;
            }
            $res = json_decode($res);
            if(0 != $res->showapi_res_code){
                throw new \Exception("JGService error: SHOWAPI Exception.\n message:$$res->showapi_res_error");
            }

            return $res->showapi_res_body->Result;
        }catch (\Exception $e){
            Log::error($e);
            return false;
        }
    }

    /***
     * 获取HTTP响应信息
     *
     * @param string $url 请求地址
     * @param string $query_str 请求参数
     * @param int $need_header 0 返回响应主体 1 返回响应头
     *
     * @return bool|mixed 成功返回响应文本，失败返回false
     */
    protected function GetHttpResponse($url,$query_str=null,$need_header = 0)
    {
        $ch = curl_init($url);
        $output = false;
        try{
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.59 Safari/537.36');
            #设置请求来源，不然会屏蔽掉
            curl_setopt($ch, CURLOPT_REFERER, $this->JG_base_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_COOKIE, $this->JG_cookies);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$query_str);
            curl_setopt($ch, CURLOPT_POST,1);
            $output = curl_exec($ch);
            if("200" != curl_getinfo($ch,CURLINFO_HTTP_CODE)){
                throw new \Exception("JGService error: Curl HTTP CODE Exception.\n url:$url \n query_str:$query_str");
            }

            if(false == $output){
                throw new \Exception('JGService error: Curl' . curl_error($ch));
            }

            if($need_header){
                return substr($output, 0, curl_getinfo($ch,CURLINFO_HEADER_SIZE));
            }

            return substr($output, curl_getinfo($ch,CURLINFO_HEADER_SIZE));
        }catch (\Exception $e){
            Log::error($e);
            return false;
        }finally{
            curl_close($ch);
        }
    }
}
