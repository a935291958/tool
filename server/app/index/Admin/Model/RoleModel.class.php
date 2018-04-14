<?php
/**
 * Created by PhpStorm.
 * User: Annie
 * Date: 2017/12/12
 * Time: 13:44
 */



namespace Admin\Model;
use Think\Model;

class RoleModel extends Model {

    protected $_validate  = array(
        array('name','require','请填写分组名称',3),
        array('name','','分组已经存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('name','','分组已经存在',0,'unique',2), // 在修改的时候验证name字段是否唯一
        array('id','require','缺少ID',2), // 在修改的时候，验证ID是否存在
    );

    protected $_auto = array(
        array('uptime','time',2,'function'), // 对update_time字段在更新的时候写入当前时间戳
        array('addtime','time',1,'function'),


    );
    //获取所有数据
    public function getAll(){
        $data = $this->select();
        return $data;
    }

    /**
     * 删除条记录数
     * @access public
     * @param int $id
     * @return bool || false
     */
    public function delOneRole($id){
        $id = (int)$id;
        $where['id'] = $id;
        $delRes = $this->where($where)->delete();
        if( $delRes  === false ){
            return false;
        }else if ($delRes>0){
            return $delRes;
        }
    }

    /**
     * 更新单条记录数
     * @access public
     * @param int $id
     * @param array $post 需要更新的数据
     * @return bool || false
     */
    public function upOneRole($id,$post){
        $saveRes = $this->where(array('id'=>(int)$id))->save($post);

        if( $saveRes === false ){
            return false;
        }else if ($saveRes>0){
            return $saveRes;
        }


    }

    /**
     * 获取单条记录数
     * @access public
     * @param int $id
     * @return array
     */
    public function getOne($id){
        return $this->where(array('id'=>$id))->find();
         //$this->getLastSql();
    }



}

?>