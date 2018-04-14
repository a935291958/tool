<?php

namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller
{
    //生产环境
    //protected $root = 'https://app.gzxsdyy.cn/';
    //测试环境
    private $root = 'http://127.0.0.1/';

    //小程序标识
    private $appid = 'wx032e97fdb52bbac3';
    //secret
    private $secret = '16e9a4c7a9696c228cc112dab01763ae';
    //商户的密钥
    private $key = 'anezFGAR9QuKz9bzOaQGl2ZMeFHKwFW6';
    //商家号
    private $mch_id = '1493522112';
    //下载对账单保存的地方
    //private $billFile =  'bill.csv';


    protected function _initialize()
    {
        //dump($_SERVER['HTTP_REFERER ']);
        //查询是否登录
        $user = session('adminData');
        //dump($user);
        if (!$user) {
            $this->redirect('/Admin/Login');
            die;
        }
        $this->assign(array(
            'adminData' => $user
        ));

    }

    /**用户访问空操作时跳转到index**/
    public function _empty()
    {

        $this->index();

    }


    //传入需要签名的数组,返回签名
    private function sign($postData)
    {
        //进行排序
        ksort($postData);

        if (isset($postData['sign'])) {
            unset($postData['sign']);
        }
        //拼接成要签名的字符串
        $str = '';
        $postLast = end($postData);
        foreach ($postData as $k => $v) {
            if (!$v) {
                continue;
            }
            if ($v !== $postLast) {
                $str .= $k . '=' . $v .= '&';
            } else {
                $str .= $k . '=' . $v;
            }

        }


        if (!isset($postData['key'])) {
            //拼接API密钥

            if (preg_match('/&$/', $str) > 0) {
                $str .= 'key=' . $this->key;
            } else {
                $str .= '&key=' . $this->key;
            }
        }
        //采用MD5的签名方式，并转为大写
        $sign = strtoupper(md5($str));
        return $sign;


    }

    //数组转成xml
    private function arrToXml($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $xmlStr = '<xml>';

        foreach ($array as $k => $v) {
            $xmlStr .= '<' . $k . '>' . $v . '</' . $k . '>';
        }

        $xmlStr .= '</xml>';
        return $xmlStr;

    }

    //访问HTTPS，传入URL和post数据,返回响应数据
    private function que($apiUrl, $post_data)
    {

        //发起请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        return $output;
    }

    //首页默认显示活动
    public function index()
    {

        $act = M('activity');
        $list = $act->order('usersort desc')->select();

        $this->assign(array(
            'list' => $list
        ));
        $this->display();
    }

    //ajax返回
    protected function aR($msg, $type = 1)
    {
        $aRT['res'] = true;
        $aRF['res'] = false;
        switch ($type) {
            case 1:
                $aRT['msg'] = $msg;
                $this->ajaxReturn($aRT);
                break;
            case 0:
                $aRF['msg'] = $msg;
                $this->ajaxReturn($aRF);
                break;
        }
        die;
    }

    //不是post的时候返回的数据
    protected function is_post()
    {
        if (!IS_POST) {
            $this->aR('请求错误', 0);
            die;
        }

    }


    // 上传单个文件
    protected function portrait()
    {
        if ($_FILES['img']['name']) { // 如果上传的头像
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Uploads_xsd/'; // 设置附件上传根目录
            $upload->subName = array('date', 'Y-m-d');//子目录创建规则
            // 上传单个文件
            $info = $upload->uploadOne($_FILES['img']);
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {// 上传成功 获取上传文件信息
                $portraitPath = 'Uploads_xsd/' . $info['savepath'] . $info['savename'];

                return array('imgPath' => $portraitPath);
            }
        }
    }

    //上传多文件
    private function upload($file)
    {
        $upload = new \Think\Upload($file);// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads_xsd/project/'; // 设置附件上传根目录
        //$upload->savePath  =     date('Y-m-d'); // 设置附件上传（子）目录
        //$upload->saveName = time().'_'.rand(10000,99999).'_'.mt_rand();

        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息

            return array('res' => false, 'msg' => $upload->getError());
        } else {// 上传成功
            //$this->success('上传成功！');
            return $info;
        }
    }

    //小程序预约挂号的banner图
    public function yyghbanner()
    {
        $yyghbanner = M('yyghbanner');
        $list = $yyghbanner->order('enable desc,xcxtype desc,usersort desc,id desc')->select();
        $this->assign(array(
            'list' => $list
        ));
        $this->display();

    }

    //小程序预约挂号添加banner图
    public function addyyghban()
    {
        if (!IS_POST) {
            $this->display();
            die;
        }

        $yygh = D('yyghbanner');

        if (!$yygh->create()) {
            $this->aR($yygh->getError(), 0);
        }
        $addRes = $yygh->add();
        if (!$addRes) {
            $this->aR('添加失败', 0);
        }

        $this->aR('添加成功,ID为' . $addRes);

    }

    //小程序预约挂号删除banner
    public function delyyghban()
    {
        $id = I('get.id', false, 'int');
        $yyghbanner = M('yyghbanner');
        $delRes = $yyghbanner->where(array('id' => $id))->delete();
        if ($delRes === 0) {
            $this->aR('没有删除任何数据', 0);
        } else if ($delRes === false) {
            $this->aR('SQL出错', 0);
        } else if ($delRes > 0) {
            $this->aR('成功删除' . $delRes . '条数据');
        }
    }

    //小程序预约挂号修改banner属性
    public function upyyghban()
    {
        $this->is_post();

        $yyghbanner = D('yyghbanner');

        if (!$yyghbanner->create($_POST,2)) {
            $this->aR($yyghbanner->getError(), 0);
        } else {
            $saveRes = $yyghbanner->save();
            if( $saveRes > 0){
               $this->aR('成功更新'.$saveRes.'条数据');
            }else{
                $this->aR('更新失败',0);
            }
        }

    }


    //添加活动
    public function add()
    {
        $aRT['res'] = true;
        $aRF['res'] = false;
        if (!IS_POST) {
            $this->display();
            die;
        }


        $activity = D('activity');

        $creatrRes = $activity->create();
        if (!$creatrRes) {
            exit($activity->getError());
        } else {
            $activity->addtime = time();
            $activity->uptime = time();
            $imgUpRes = $this->portrait();
            if (!$imgUpRes) {
                $this->error('图片上传失败');
            }
            $activity->img = $this->root . $imgUpRes['imgPath'];

            $id = $activity->add();
            if ($id > 0) {
                // dump($activity);die;
                $this->success('添加成功，ID为' . $id);
            } else {
                $this->error('添加失败');
            }
        }


    }

    //查询需要更新的活动
    public function upBlogView()
    {


        $id = I('get.id', null, 'int');
        if (!$id) {
            $this->error('SQL注入失败，请重新尝试！');
        }
        $activity = M('activity');
        $data = $activity->where(array('id' => $id))->find();
        if (!$data) {
            $this->error('暂无数据');
        }
        session('upBlogId', $id);
        $this->assign(array(
            'data' => $data
        ));

        $this->display('up');

    }

    //更新活动
    public function upBlog()
    {
        //有数据就更新
        if (IS_POST) {
            $activity = M('activity');

            $upPostData = I('post.');
            //文章ID存放在服务器防止客户端篡改ID
            if (session('id')) {
                $upPostData['id'] = session('id');
            }
            //原来的数据
            $oldData = $activity->where(array('id' => $upPostData['id']))->find();
            //数据差集，就是要更新的数据
            $saveData = array_diff_assoc($upPostData, $oldData);
            // dump($upPostData);
            // dump($saveData);
            // die;
            //判断有没有更新图片
            if ($_FILES['img']['name']) {

                $imgUpRes = $this->portrait();
                if (!$imgUpRes) {
                    $this->error('图片上传失败');
                }
                $saveData['img'] = $this->root . $imgUpRes['imgPath'];
            }

            if (!$saveData) {
                $this->error('没有修改任何数据！');
            }


            $saveData['uptime'] = time();

            $saveNum = $activity->where(array('id' => $upPostData['id']))->save($saveData);

            //die;
            if ($saveNum > 0) {
                // dump($saveData);
                $this->success('已更新记录数' . $saveNum . '条', __CONTROLLER__);
            } else if ($saveNum === 0) {
                $this->error('没有修改任何数据！');
            } else if ($saveNum === false) {
                $this->error('更新失败！');
            }
        }
    }

    //删除活动
    public function delBlog()
    {
        $aRT['res'] = true;
        $aRF['res'] = false;
        if (isset($_GET['id'])) {
            $data['id'] = I('get.id', -1, 'int');
            if ($data['id'] > 0) {
                $activity = M('activity');
                $delRes = $activity->where($data)->delete();
                if ($delRes > 0) {
                    $aRT['msg'] = '删除成功';
                    $this->ajaxReturn($aRT);
                } else {
                    $aRF['msg'] = '删除失败';
                    $this->ajaxReturn($aRF);
                }

            }
        }
        //非ajax直接报404
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");

    }

    //接收表单数据
    public function forms()
    {
        $page = I('get.page', NULL, 'int');
        $limit = I('get.limit', NULL, 'int');
        if ($page && $limit) {
            $form = M('form');
            $count = $form->limit($page, $limit)->count();

            if ($count <= 0) {
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg' => '暂无数据'
                ));
            }

            $data = $form->page($page, $limit)->select();
            $return = array(
                'code' => 0,
                'msg' => '成功',
                'count' => $count,
                'data' => $data
            );
            $this->ajaxReturn($return);

        }


        $this->display();
    }

    //打开增加项目视图和接收上传数据
    public function project()
    {
        if (!IS_POST) {
            $this->assign(array(
                'ajaxPicname' => __CONTROLLER__ . '/getPicname'
            ));
            $this->display();
            die;
        }


        //验证规则
        $validate = array(

            array('times', 'require', '请填写有效时间', 3),
            array('title', 'require', '请填写商品名称', 3),
            array('number', 'require', '请填写购买人数', 3),
            array('enable', 'require', '请填写是否启用', 3),
            array('oldprice', 'require', '请填写原价', 3),
            array('newprice', 'require', '请填写现价'),

            array('newprice', '/^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/', '现价必须为数字'),
            array('oldprice', '/^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/', '原价必须为数字'),
            array('number', 'number', '购买人数必须为数字'),
            array('enable', 'number', '是否启用填写不正确'),


        );


        $project = M('project');


        if (!$project->validate($validate)->create()) {
            $this->error($project->getError());
        } else {
            $data = I('post.');
            $data['uptime'] = time();
            $data['addtime'] = time();

            //判断有没有选择文件
            $getArrayValue = getArrayValue($_FILES['img']['name']);//得到全部值


            //有图的话就接收图片
            if (!isNull($getArrayValue)) {
                //接收活动文件
                $info = $this->upload($_FILES);


                //存在res表示上传失败了
                if (isset($info['res']) && $info['res'] === false) {
                    $this->error($info['msg']);
                }

                $uploadPath = 'Uploads_xsd/project/';
                $imgArray = '';
                foreach ($info as $k => $v) {
                    $imgArray[$k] = $this->root . $uploadPath . $v['savepath'] . $v['savename'];
                }
                $data['img'] = json_encode($imgArray);
            } else {
                //dump($_FILES);
                //echo "未发现上传文件！";
            }

            // dump($data);


            if ($project->add($data)) {
                //dump($project->getLastSql());die;
                // die;
                $this->success('添加成功', __CONTROLLER__ . '/proList');
            } else {
                $this->error('添加失败!');
            }
        }

    }

    //项目列表
    public function proList()
    {
        $page = I('get.page', 0, 'int');
        $limit = I('get.limit', 20, 'int');
        $limit = ($limit > 1000 || $limit < 10) ? 20 : $limit;


        $first = $limit * $page;

        $pro = M('project');

        $count = $pro->count();

        //$where['id'] = array('EGT', $first);
        $list = $pro->page($page, $limit)->order('usersort desc,id desc')->select();
        //dump($list);
        //die;
        //dump($pro->getLastSql());


        $this->assign(array(
            'count' => $count,
            'list' => $list,
            'page' => $page,
            'limit' => $limit,

        ));

        $this->display();
    }

    //删除项目
    public function delPro()
    {
        $aRT['res'] = true;
        $aRF['res'] = false;
        if (isset($_GET['proid'])) {
            $data['id'] = I('get.proid', -1, 'int');
            if ($data['id'] > 0) {
                $activity = M('project');
                $delRes = $activity->where($data)->delete();
                if ($delRes > 0) {
                    $aRT['msg'] = '删除成功';
                    $this->ajaxReturn($aRT);
                } else {
                    $aRF['msg'] = '删除失败';
                    $this->ajaxReturn($aRF);
                }

            }
        }
        //无参数 直接报404
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");

    }

    //查询需要更新的项目
    public function upProView()
    {

        $id = I('get.proid', null, 'int');
        if (!$id) {
            $this->error('SQL注入失败，请重新尝试！');
        }
        $activity = M('project');
        $data = $activity->where(array('id' => $id))->find();
        if (!$data) {
            $this->error('暂无数据');
        }
        $img = $data['img'];
        if ($img) {
            $data['img'] = json_decode($img, true);
        }

        session('upProId', $id);
        $this->assign(array(
            'data' => $data,
            'ajaxPicname' => __CONTROLLER__ . '/getPicname',
        ));
        //dump($data);
        $this->display('upPro');

    }

    //更新项目
    public function upPro()
    {
        //有数据就更新
        if (IS_POST) {
            $activity = M('project');

            $upPostData = I('post.');
            //文章ID存放在服务器防止客户端篡改ID
            if (session('upProId')) {
                $upPostData['id'] = session('upProId');
            }
            //原来的数据
            $oldData = $activity->where(array('id' => $upPostData['id']))->find();
            //数据差集，就是要更新的数据
            $saveData = array_diff_assoc($upPostData, $oldData);


            //判断有没有更新图片
            $getArrayValue = getArrayValue($_FILES['img']['name']);//得到全部值
            //dump($getArrayValue);
            //dump(isNull($getArrayValue));
            //判断值是不是空的
            if (!isNull($getArrayValue)) {

                //接受文件
                $info = $this->upload($_FILES);
                //存在res表示上传失败了
                if (isset($info['res']) && $info['res'] === false) {
                    $this->error($info['msg']);
                }

                $uploadPath = 'Uploads_xsd/project/';
                $imgArray = '';//新上传的图合集
                foreach ($info as $k => $v) {
                    $imgArray[$k] = $this->root . $uploadPath . $v['savepath'] . $v['savename'];
                }
                //$saveData['img'] = json_encode($imgArray);

                //读取原来的图片合集，追加进去
                //dump($oldData['img']);
                if ($oldData['img'] != "null" && $oldData['img']) {
                    $tempImgList = json_decode($oldData['img'], true);//此处为array
                    $newImgList = array_merge($tempImgList, $imgArray);
                    /*
                    echo '新上传的图片：';
                    dump($imgArray);
                    echo "原来的图片:";
                    dump($tempImgList);
                    echo "合并的数组：";
                    dump($newImgList);
                    */
                    //echo "原来有图";
                    $saveData['img'] = json_encode($newImgList);
                    //die;
                } else {
                    //echo "原来无图";
                    $saveData['img'] = json_encode($imgArray);
                }

            }
            //dump($saveData['img']);
            if (!$saveData) {
                $this->error('没有修改任何数据！');
            }


            $saveData['uptime'] = time();

            $saveNum = $activity->where(array('id' => $upPostData['id']))->save($saveData);

            //die;
            if ($saveNum > 0) {
                // dump($saveData);
                //dump($activity->getLastSql());
                $this->success('已更新记录数' . $saveNum . '条', __CONTROLLER__ . '/proList');
            } else if ($saveNum === 0) {
                $this->error('没有修改任何数据！');
            } else if ($saveNum === false) {
                $this->error('更新失败！');
            }
        }
    }

    //更新项目视图删除图片

    public function upViewDelImg()
    {
        // hjson();
        $aRT['res'] = true;
        $aRF['res'] = false;
        $id = I('get.proid', false, 'int');
        $imgName = I('get.src', false, 'string');

        if (!$id || !$imgName) {
            //参数不足
            $aRF['msg'] = '参数不足';
            $this->ajaxReturn($aRF);
        }
        $pro = M('project');
        $data = $pro->where(array('id' => $id))->field('img')->find();
        //判断有没有查到数据
        if (!$data) {
            $aRF['msg'] = '查无数据！';
            $this->ajaxReturn($aRF);
        }
        //查找要删除的数据在源数据的哪里，然后删掉那个值
        $imgList = json_decode($data['img']);
        $delRes = array();
        foreach ($imgList as $k => $v) {
            //echo $v;
            // echo $imgName;
            if ($v !== $imgName) {
                $delRes[] = $v;
            }
        }
        //dump($delRes);
        // die;
        //更新到数据库
        if ($pro->where(array('id' => $id))->save(array('img' => json_encode($delRes)))) {
            $aRT['msg'] = '更新成功';
            $this->ajaxReturn($aRT);
        } else {
            $aRF['msg'] = '更新失败！';
            $this->ajaxReturn($aRF);
        }


    }

    //接收单文件，用于封面等异步上传
    public function getPicname()
    {
        $aRF['res'] = false;
        $aRT['res'] = true;
        $info = $this->portrait($_FILES);
        if ($info) {
            $aRT['msg'] = '上传成功';
            $aRT['url'] = $this->root . $info['imgPath'];
            $this->ajaxReturn($aRT);
        } else {
            $aRF['msg'] = '上传失败';
            $this->ajaxReturn($aRF);
        }


    }

    //更新视图删除封面和banner
    public function delPicname()
    {
        $aRF['res'] = false;
        $aRT['res'] = true;
        $id = I('get.proid', false, 'int');
        $type = I('get.type', false, 'string');
        if (!$id || !$type) {
            $this->aR('参数错误', 0);
        }

        $pro = M('project');

        switch ($type) {
            //删除封面
            case  'delPicname':
                $saveRes = $pro->where(array('id' => $id))->save(array('picname' => ''));
                if ($saveRes > 0) {
                    $this->aR('删除成功');
                } else {
                    $this->aR('删除失败');
                }
                break;
            //删除banner
            case  'delBanner':
                $saveRes = $pro->where(array('id' => $id))->save(array('banner' => ''));
                if ($saveRes > 0) {
                    $this->aR('删除成功');
                } else {
                    $this->aR('删除失败', 0);
                }
                break;
            default:
                $this->aR('未知请求', 0);
        }


    }

    //商户订单
    public function order()
    {
        $page = I('get.page', NULL, 'int');
        $limit = I('get.limit', NULL, 'int');
        if ($page && $limit) {
            $order = M('order');
            $count = $order->limit($page, $limit)->count();

            if ($count <= 0) {
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg' => '暂无数据'
                ));
            }

            $data = $order->page($page, $limit)->order('id desc')->select();

            $pro = M('project');

            foreach ($data as $k => $v) {
                foreach ($v as $kk => $vv) {
                    switch ($kk) {
                        //查询商品名称
                        case "proid":
                            $titleRes = $pro->where(array('id' => $vv))->field('title')->order('id desc')->find();
                            $data[$k]['proname'] = $titleRes['title'];
                            //dump($titleRes);
                            break;
                        //转换支付状态
                        case "trade_state":
                            switch ($vv) {
                                case "DZF":
                                    $data[$k][$kk] = '待支付';
                                    break;
                                case "YQX":
                                    $data[$k][$kk] = '已取消';
                                    break;
                                case "YSY":
                                    $data[$k][$kk] = '已使用';
                                    break;
                                case "SUCCESS":
                                    $data[$k][$kk] = '已支付';
                                    break;
                            }
                            break;
                        //金额分换成元
                        case "total_fee":
                            $data[$k][$kk] = $vv / 100;
                            break;
                    }
                }

                //dump($k);
            }

            $return = array(
                'code' => 0,
                'msg' => '成功',
                'count' => $count,
                'data' => $data
            );
            $this->ajaxReturn($return);

        }


        $this->display();
    }

    //微信订单
    public function wxpay()
    {
        $page = I('get.page', NULL, 'int');
        $limit = I('get.limit', NULL, 'int');
        if ($page && $limit) {
            $wxpay = M('wxpay');
            $count = $wxpay->limit($page, $limit)->count();

            if ($count <= 0) {
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg' => '暂无数据'
                ));
            }

            $data = $wxpay->page($page, $limit)->order('id desc')->field('id,appid,openid,cash_fee,total_fee,attach,time_end,out_trade_no,transaction_id,addtime')->select();

            foreach ($data as $k => $v) {
                foreach ($v as $kk => $vv) {
                    switch ($kk) {
                        //现金支付金额
                        case "cash_fee":
                            $data[$k][$kk] = $vv / 100;
                            break;
                        //订单金额
                        case "total_fee":
                            $data[$k][$kk] = $vv / 100;
                            break;
                    }
                }

            }


            $return = array(
                'code' => 0,
                'msg' => '成功',
                'count' => $count,
                'data' => $data
            );
            $this->ajaxReturn($return);
        }

        $this->display();
    }

    //订单详情查询
    public function selectOrder()
    {
        $types = I('post.types', null, 'string');
        $number = I('post.numbers', null, 'string');
        if (!$types || !$number) {
            $this->assign(array(
                'display' => 'none'
            ));
            $this->display();
            die;
        }


        //正常查询数据

        switch ($types) {
            //微信订单
            case "wxdd":
                $post['transaction_id'] = $number;
                break;
            //商家订单
            case "sjdd":
                $post['out_trade_no'] = $number;
                break;
        }

        $post['appid'] = $this->appid;
        $post['mch_id'] = $this->mch_id;
        $post['sign_type'] = 'MD5';
        $post['nonce_str'] = generate_password(20);
        $post['sign'] = $this->sign($post);

        $postXml = $this->arrToXml($post);

        $queRes = $this->que('https://api.mch.weixin.qq.com/pay/orderquery', $postXml);

        $queArray = xmlToArray($queRes);

        if ($queArray['return_code'] !== 'SUCCESS') {
            die('通信失败');

        }
        if ($queArray['result_code'] === 'FAIL') {
            die($queArray['err_code_des']);

        }

        $selectorder = M('selectorder');
        $queArray['addtime'] = time();
        $selectorder->add($queArray);

        //dump($queArray);
        $this->assign(array(
            'prodata' => $queArray,
            'display' => 'block',
        ));
        $this->display();


    }

    //粉丝管理
    public function userList()
    {
        $u = M('user');
        $page = I('get.page', NULL, 'int');
        $limit = I('get.limit', NULL, 'int');
        if ($page && $limit) {
            $count = $u->limit($page, $limit)->count();

            if ($count <= 0) {
                $this->ajaxReturn(array(
                    'code' => -1,
                    'msg' => '暂无数据'
                ));
            }

            $data = $u->page($page, $limit)->field('id,openid,authopenid,nickName,avatarUrl,addtime,gender,model,ip')->select();
            $return = array(
                'code' => 0,
                'msg' => '成功',
                'count' => $count,
                'data' => $data
            );
            $this->ajaxReturn($return);

        }


        $this->display();


    }

    //下载对账单
    public function downBills()
    {


        if (!IS_POST) {
            $this->display();
            die;
        }

        $post['bill_type'] = I('post.bill_type', false);
        preg_match_all('/[0-9]+/', I('post.date', false), $res);

        foreach ($res[0] as $v) {
            $post['bill_date'] .= $v;
        }

        if (!$post['bill_type'] || !$post['bill_date']) {
            $this->error('参数不足');
        }


        //dump($_POST);

        $post['appid'] = $this->appid;
        $post['mch_id'] = $this->mch_id;
        $post['nonce_str'] = generate_password(20);
        //$post['tar_type'] = 'GZIP';
        $post['sign'] = $this->sign($post);

        $postXml = $this->arrToXml($post);

        //dump($post);

        $queRes = $this->que('https://api.mch.weixin.qq.com/pay/downloadbill', $postXml);

        $queFail = xmlToArray($queRes);//如果不是返回false，表示微信服务器返回了错误信息
        if ($queFail !== false) {
            // dump($queFail);
            $this->error('查询失败,错误信息如下：' . $queFail['return_msg'] . '<br/>错误代码：' . $queFail['error_code'], '', 10);

        }

        if (!$queRes) {
            $this->error('暂无数据');
        }


        $billFile = C('DOWN_FILE_DIR') . '/' . date('Y-m-d') . '_' . generate_password(10) . '.csv';
        if (file_put_contents($billFile, $queRes) > 0) {
            echo '<script>window.open("/' . $billFile . '")</script>';
            $this->success('数据生成成功,请允许弹窗', '', 8);


        }


    }

    //查询退款
    public function refundquery()
    {
        if (!IS_POST) {
            $this->assign(array(
                'resFail' => 'none',
                'resSuccess' => 'none'
            ));
            $this->display();
            die;
        }

        $types = I('post.types', null);
        $transaction = I('post.transaction', null);
        if (!$types || !$transaction) {
            $this->error('参数不足');
        }

        $post['transaction_id'] = '';
        $post['out_trade_no'] = '';
        $post['out_refund_no'] = '';
        $post['refund_id'] = '';

        switch ($types) {
            //微信订单号
            case "transaction_id":
                $post['transaction_id'] = $transaction;
                break;
            //微信订单号
            case "out_trade_no":
                $post['out_trade_no'] = $transaction;
                break;
            //商户订单号
            case "out_refund_no":
                $post['out_refund_no'] = $transaction;
                break;
            //商户退款单号
            case "refund_id":
                $post['refund_id'] = $transaction;
                break;
        }


        $post['appid'] = $this->appid;
        $post['mch_id'] = $this->mch_id;
        $post['nonce_str'] = generate_password(20);

        $post['sign'] = $this->sign($post);

        $postXml = $this->arrToXml($post);

        $queRes = $this->que('https://api.mch.weixin.qq.com/pay/refundquery', $postXml);

        $queResArray = xmlToArray($queRes);

        if ($queResArray['return_code'] !== 'SUCCESS') {
            //dump($queResArray);
            //dump($post);
            //dump($postXml);die;
            $this->error($queResArray['return_msg']);
        }

        if ($queResArray['result_code'] === 'FAIL') {
            $this->assign(array(
                'res' => $queResArray,
                'resFail' => 'block',
                'resSuccess' => 'none'
            ));
        } else {
            $this->assign(array(
                'res' => $queResArray,
                'resFail' => 'none',
                'resSuccess' => 'block'
            ));

        }

        //dump($queResArray);
        //dump($queResArray);
        $this->display();


    }


    /*
     * 管理员相关 START
     */

    //显示分组
    public function role()
    {

        $role = D('role');
        $all = $role->getAll();
        $this->assign(array(
            'list' => $all
        ));
        $this->display();
    }

    //添加分组
    public function addRole()
    {
        if (IS_POST) {
            $role = D('role');
            if (!$role->create()) {
                $this->aR($role->getError(), 0);
            } else {
                if ($role->add() > 0) {
                    $this->aR('添加成功');
                } else {
                    $this->aR('添加失败', 0);
                }
            }

        } else {
            $this->aR('请求格式错误', 0);
        }
    }

    //删除分组
    public function delRole()
    {
        if (!IS_POST) {
            $this->aR('请求格式错误', 0);
        }
        $id = I('post.id', -1, 'int');
        if ($id < 0) {
            $this->aR('参数错误', 0);
        }
        $role = D('role');
        if ($role->delOneRole($id)) {
            $this->aR('删除成功');
        } else {
            $this->aR('删除失败', 0);
        }

    }

    //查询单条分组数据
    public function selectRole()
    {
        $id = I('post.id', -1, 'int');
        $role = D('role');
        $data = $role->getOne($id);
        if ($data) {
            $this->aR($data);
        } else {
            $this->aR('暂无数据', 0);
        }


    }

    //更新分组
    public function upRole()
    {
        $role = D('role');
        if (!$role->create()) {
            $this->aR($role->getError(), 0);
        } else {
            $saveRes = $role->save();
            // dump($saveRes);
            if ($saveRes > 0) {
                $this->aR('更新成功');
            } else {
                $this->aR('更新失败', 0);
            }
        }

    }

    //管理员列表
    public function adminList()
    {
        $admin = M('admin');
        $all = $admin
            ->alias('A')
            ->field('A.id,A.user,A.pass,A.nickname,A.addtime,A.img,R.name')
            ->join('__ROLE__ R ON R.id = A.roleid')
            ->select();//select代入false，只返回SQL语句不执行

        //查询分组，赋值到添加表单
        $role = M('role');
        $roleAll = $role->field('name,id')->select();

        $this->assign(array(
            'list' => $all,
            'roleAll' => $roleAll
        ));


        $this->display();
    }

    //更新管理员
    public function upAdmin()
    {
        $pass = encrypting(I('post.pass', '', 'string'));
        $where['id'] = I('post.id', -1, 'int');
        $admin = M('admin');
        $saveRes = $admin->where($where)->save(array('pass' => $pass));
        if ($saveRes > 0) {
            $this->aR('已成功更新' . $saveRes . '条数据');

        } else {
            $this->aR('更新失败', 0);
        }

    }

    //设置单个权限,操作表，权限详情
    public function nodeList(){

        $this->display();
    }


    /*
     * 管理员相关 END
     */

}