<?php
/**
 * Created by PhpStorm.
 * User: Annie
 * Date: 2018/2/24
 * Time: 21:28
 */

namespace Common\Wechat;


class common
{
    public $code;
    public $secret;
    public $appid;

    /* @fn PHP CURL HTTPS POST
     * @param  $url string 请求的地址
     * @param  $param  post的数据
     * @return boolean 失败返回false，成功返回string
     */
    static public function request_post(string $url = '', $param = '')
    {
//        if (empty($url) || empty($param)) {
//            return false;
//        }

        $postUrl = $url;
        $curlPost = $param;
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($curl);//运行curl
        curl_close($curl);

        return $data;
    }
    //自动设置appid,secret
    public function __construct( string $appid='', string $secret='')
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    //获取openID
    public function getOpenId()
    {
        $appid = $this->appid;
        $secret = $this->secret;
        $code = $this->code;
        if(!$appid || !$secret || !$code ){
            return false;
        }
        $apiUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid
            . '&secret=' . $secret
            . '&js_code=' . $code
            . '&grant_type=authorization_code';

        $res = json_decode($this->request_post($apiUrl), true);
        //有这些表示登陆失败了
        if (isset($res['errcode']) || isset($res['errmsg'])) {
            return false;
        }
        return $res['openid'];
    }

    //生成加密的AuthOpenid,用来代替session_key存在客户端
    static public function setAuthOpenid($openid)
    {
        return md5($openid);
    }
}