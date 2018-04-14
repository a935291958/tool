<?php

namespace Home\Model;
use Think\Model;

class ConfigModel extends Model {


    /* @fn 获取单项配置信息
     * @return string ,没有查询到数据时返回空字符串
     */
    function getOne(string $key):string
    {
        $appId = ($this->field('value')->where(array('key' => $key))->find())['value'];
        $appId = $appId ?? '';
        return $appId;
    }


}

?>