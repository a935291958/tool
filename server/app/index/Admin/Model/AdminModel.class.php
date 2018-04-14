<?php
/**
 * Created by PhpStorm.
 * User: Annie
 * Date: 2017/12/12
 * Time: 13:44
 */



namespace Admin\Model;
use Think\Model;

class AdminModel extends Model {

    protected $_validate  = array(
        //登录和添加验证非空
        array('user','require','请填写账号',3),
        array('pass','require','请填写密码',3),

        //添加和修改的时候验证唯一性
        array('user','','该用户已存在',0,'unique',1),
        array('user','','该用户已存在',0,'unique',2),

    );

    protected $_auto = array(

    );

    /**
     * 返回所有数据
     *  @access public
     * @return array
     *
     */
    public function getAll(){
        return $this->select();
    }










}

?>