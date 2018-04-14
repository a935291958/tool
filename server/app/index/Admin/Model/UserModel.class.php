<?php

namespace Admin\Model;

use Think\Model;

class UserModel extends Model
{
    /* @fn 判断是不是已存在的用户
     * @param  $authOpenid string 用户的authOpenid
     * @return bool 已存在返回true，不存在返回false
     */
    public function isUser(string $authOpenid): bool
    {
        if ($this->where(array('authOpenid' => $authOpenid))->count() >= 1) {
            return true;
        }
        return false;
    }

    /* @fn 返回一个用户的全部数据
     * @param  $authOpenid string 用户的authOpenid
     * @return 成功返回全部数据，失败返回false
     */
    public function getOneUser(string $authOpenid)
    {
        $data = $this->where(array('authOpenid' => $authOpenid))->find();
        if ($data) {
            return $data;
        }
        return false;
    }

}

?>