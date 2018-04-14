<?php


namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{


    public function index()
    {


        //读取缓存文件的Access Token
        $bdAccess = S('bdAccess') ?? '';

        //没有缓存Access Token的话再请求服务器获取
        if (!$bdAccess) {
            //为获取百度Access Token读取数据的配置
            $cfg = M('config');
            $bdCfg['grant_type'] = ($cfg->field('value')->where(array('key' => 'grantType'))->find())['value'];
            $bdCfg['client_id'] = ($cfg->field('value')->where(array('key' => 'clientId'))->find())['value'];
            $bdCfg['client_secret'] = ($cfg->field('value')->where(array('key' => 'clientSecret'))->find())['value'];
            $bdapi = ($cfg->field('value')->where(array('key' => 'bdapi'))->find())['value'];

            //格式化post数据
            $post_data = fieldUrl($bdCfg);

            //发起请求,以获取Access Token

            $getAccess = request_post($bdapi, $post_data);
            if (!$getAccess) {
                #$email$#
                aR('请求百度服务器错误,获取Access Token失败', 0);
            }

            //将返回的数据转换成数组
            $getAccessArray = json_decode($getAccess, true);

            //判断有没有出错，包含error表示百度返回了错误信息

            if (isset($getAccessArray['error'])) {
                #$email$#
                aR($getAccessArray['error_description'], 0);
            }

            //echo getMillisecond();//获取毫秒级时间戳，文件读写性能测试

            // 缓存百度access_token
            S('bdAccess', $getAccessArray['access_token'], $getAccessArray['expires_in']);

            $bdAccess = $getAccessArray;
        }

        // dump($bdAccess);

        $api = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/animal?access_token=' . $bdAccess;


        $img = file_get_contents('d:/d.jpg');
        $img = base64_encode($img);
        $bodys = array(
            'image' => $img
        );
        $res = request_post($api, $bodys);

        var_dump($res);


    }
    #添加用户
    public function addUser()
    {
        $postData = I('post.');

        //当前时间戳比提交的时间戳大于60秒视为非法数据
        if (time() - (int)$postData['timestamp'] > 60) {
            aR('时间差过长，数据无效', false, -3);
        }

        if (!$postData['code']) {
            aR('缺少code', false, -1);
        }

        //获取小程序的配置
        $cfg = D('config');
        $appId = $cfg->getOne('appId');
        $appSecret = $cfg->getOne('appSecret');

        //获取用户密钥和IV
        $userKey = $cfg->getOne('userKey');
        $userIv = $cfg->getOne('userIv');


        //获取openID
        $wxCom = new \Common\Wechat\common($appId, $appSecret);
        $wxCom->code = $postData['code'];
        $openid = $wxCom->getOpenId();

        if (!$openid) {
            aR('登陆失败', false, -2);
        }

        //查询authOpenid
        $u = D('user');
        $authOpenid = $wxCom->setAuthOpenid($openid);


        //下载头像到本地
        if($postData['avatarUrl']){
            $avatarDir = $cfg->getOne('avatarDir');//获取根目录
            $fileName = getAvatarUrl($avatarDir, $postData['avatarUrl']) ?? '';
            $postData['localAvatarUrl'] = $fileName;
        }


        //获取IP地址
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

        $postData['ip'] = ip2long($ip);

        $postData['authOpenid'] = $authOpenid;

        $postData['openid'] = $openid;


        //查询有没有存在该用户,如果存在就更新数据
        if ($u->isUser($authOpenid)) {
            // dump($postData);
            $oldUserData = $u->getOneUser($authOpenid);
            //删除不需要更新的字段
            unset($postData['code']);
            unset($postData['timestamp']);
            unset($oldUserData['addtime']);
            //找出差集
            $newUserData = array_diff($postData, $oldUserData);

            //加密用户信息返回给小程序
            $userInfo = array(
                'authOpenid' => $authOpenid,
                'timestamp' => time()
            );

            //用户的session
            $userSession = encrypted(json_encode($userInfo), $userKey, $userIv);

            //更新数据
            if (sizeof($newUserData) >= 1 && $u->where(array('id' => $oldUserData['id']))->data($newUserData)->save() >= 1) {


                aR(array('msg' => '已存在的用户,已更新数据', 'authOpenid' => $authOpenid, 'userSession' => $userSession), true, 1);
            }

            aR(array('msg' => '已存在的用户,未更新数据', 'authOpenid' => $authOpenid, 'userSession' => $userSession), true, 2);
        }


        //写入数据库
        if ($u->add($postData) >= 1) {
            //返回数据给客户端
            aR(array('msg' => '登录成功', 'authOpenid' => $authOpenid));
        } else {
            aR('用户数据写入数据库失败', false, -4);
        }


    }

    //返回配置信息给小程序
    public function getXcxCfg()
    {
        $type = I('get.type', null, 'int');

        if (!$type) {
            aR('缺少参数', false, -999);
        }

        $cfg = D('config');
        $data = '';
        switch ($type) {
            case 1:
                $data = $cfg->getOne('xcxConfig');
                break;
        }

        if (!$data) {
            aR('查无数据', false, -998);
        }

        aR(array('msg' => '查询成功', 'data' => $data, 'key' => 'saveUserTime'));

    }

    //小程序API接口
    public function api()
    {
        $type = I('get.type', null, 'string');//获取查询的类型
        if (!$type) {
            aR('未输入API查询类型', false, -997);
        }
        //不需要写入数据库的查询类型
        $notWrite = array('pid', 'getHot');

        $cfg = D('config');//小程序配置表

        //写入用户查询日志
        $userlog = D('userlog');
        $authOpenid = I('get.authOpenid', false, 'string');
        $getData = I('get.');

        //dump(in_array($type,$notWrite));
        if (!$authOpenid && !in_array($type, $notWrite)) {
            aR('缺少authOpenid', false, -996);
        }


        //如果有设置IP参数，获取IP，参数为空时默认为客户端IP
        if (isset($getData['ip'])) {
            //$getData['ip'] = $getData['ip'] ??  $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']; //这样写会变成空字符串
            $getData['ip'] = $getData['ip'] == '' ? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']) : $getData['ip'];

        }


        //要写入日志的数据
        $logAddData = $getData;
        unset($logAddData['authOpenid']);
        unset($logAddData['type']);

        //如果不是pid获取分类就写入数据库
        if (!in_array($type, $notWrite)) {
            $logAdd['authOpenid'] = $authOpenid;
            $logAdd['type'] = $type;
            $logAdd['data'] = json_encode($logAddData);
            $userlog->add($logAdd);

        }


        //获取阿里API appcode
        $appcode = $cfg->getOne('appcode');


        switch ($type) {
            //查询分类
            case 'pid':

                $pid = I('get.pid', null, 'int');
                if ($pid !== null) {
                    $column = D('column');
                    $data = $column->order('sort asc,hot desc,id desc')->getColumn($pid);
                    if (!$data) {
                        aR('暂无分类数据', false, -998);
                    }
                    aR(array('msg' => 'OK', 'data' => $data));
                }
                break;
            //获取热门项目
            case 'getHot':
                $column = D('column');
                $hotPro = $column->getHot();
                if (!$hotPro) {
                    aR('暂无数据', false, -200);
                }
                aR(array('msg' => 'OK', 'data' => $hotPro));

                break;
            //查询手机归属地
            case 'gsd':

                $phone = I('get.phone', null, '/^[1][3,4,5,6,7,8][0-9]{9}$/');

                if (!$phone) {
                    aR('参数phone有误', false, -999);
                }

                $host = "http://jshmgsdmfb.market.alicloudapi.com/shouji/query?shouji=" . $phone;

                $res = aliApi($host, $appcode, array('status' => "0"));

                if ($res) {
                    aR(array('msg' => 'OK', 'data' => $res['result']));
                } else {
                    aR('查询失败', false, -201);
                }
                break;
            //查询天气
            case 'cxtq':

                $host = '';
                if ($getData['latitude'] && $getData['longitude']) {
                    $host = 'http://jisutqybmf.market.alicloudapi.com/weather/query?location=' . $getData['latitude'] . ',' . $getData['longitude'];
                    //加入数据库
                    $l = M('location');
                    $l->add($getData);
                } elseif ($getData['keyword']) {
                    $host = 'http://jisutqybmf.market.alicloudapi.com/weather/query?city=' . $getData['keyword'];
                } else {
                    aR('请输入location,city任意一种，location优先', false, -995);
                }
                $res = aliApi($host, $appcode, array('status' => "0"));
                if ($res) {
                    aR(array('msg' => 'OK', 'data' => $res['result']));
                } else {
                    aR('查询失败', false, -201);
                }

                break;
            //IP地址查询
            case 'ipdz':

                $ip = $getData['ip'];


                if (!$ip) {
                    aR('缺少IP参数', false, -994);
                }

                $host = 'https://dm-81.data.aliyun.com/rest/160601/ip/getIpInfo.json?ip=' . $ip;
                // dump($ip);
                $res = aliApi($host, $appcode, array('code' => 0));
                if ($res) {
                    aR(array('msg' => 'OK', 'data' => $res['data']));
                } else {
                    aR('查询失败', false, -201);
                }

                break;
            //IP定位
            case 'ipdw':

                $ip = $getData['ip'];


                if (!$ip) {
                    aR('缺少IP参数', false, -994);
                }
                $host = 'http://iploc.market.alicloudapi.com/v3/ip?ip=' . $ip;
                // dump($ip);
                $res = aliApi($host, $appcode, array('status' => '1'));
                if ($res) {
                    aR(array('msg' => 'OK', 'data' => $res));
                } else {
                    aR('查询失败', false, -201);
                }

                break;
            //添加评论
            case 'comment':
                #验证时间戳
                if (!$getData['t']) {
                    aR('缺少时间戳', false, -503);
                }


                $u = D('user');
                if (!$u->isUser($getData['authOpenid'])) {
                    aR('查询不到该用户', false, -500);
                }

                $comment = D('comment');
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                if (!$comment->create($getData)) {
                    aR($comment->getError(), false, -501);
                }


                if ($comment->add($getData) < 1) {
                    aR('评论失败', false, -502);
                }

                aR('评论成功');

                break;
            //获取相应项目的评论
            case 'getComments':

                if (!$getData['proid']) {
                    aR('缺少proid参数', false, -999);
                }
                $comment = D('comment');
                $u = D('user');
                #获取评论列表
                $data = $comment->getOneCom((int)$getData['proid']);

                #查询评论人的头像
                foreach ($data as $k => $v) {
                    $userData = $u->getUserImg($v['authOpenid']);
                    $data[$k]['localAvatarUrl'] = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $userData['localAvatarUrl'];
                    $data[$k]['nickName'] = $userData['nickName'];
                }
                aR(array('msg' => 'OK', 'data' => $data));
                break;

            default:
                aR('缺少type参数', false, -999);
        }


    }

    //小程序使用建议提交
    public function queUrl()
    {
        $getData = I('get.');
        foreach ($getData as $k => $v) {
            if (!$v) {
                unset($getData[$k]);
            }

        }
        //dump($getData);
        $q = D('question');
        if (!$q->create($getData)) {
            aR($q->getError(), false, -503);
        }
        if ($q->add() > 0) {
            aR('OK');
        }
    }


}