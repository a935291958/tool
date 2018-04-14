<?php

namespace Home\Model;

use Think\Model;

class ColumnModel extends Model
{


    /* @fn 获取单项配置信息
     * @param $pid int 父栏目的ID
     * @return  array 没有查询到数据时返回空数组
     */
    public function getColumn(int $pid): array
    {
        return $this->where(array('pid' => $pid, 'show' => 1))->select();
    }

    /* @fn 获取单项配置信息
     * @return  array 没有查询到数据时返回空数组
     */
    public function getHot(): array
    {
        return $this->order('sort desc')->where(array('show' => 1, 'hot' => 1))->select();
    }


}

?>