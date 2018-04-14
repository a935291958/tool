<?php

namespace Home\Model;

use Think\Model;

class CommentModel extends Model
{
    protected $_validate = array(
        array('proid', 'require', '项目ID不能为空'),
        array('score', 'require', '分数不能为空'),
        array('authOpenid', 'require', '用户标识符不能为空'),
        array('msg', 'require', '评论不能为空'),

        array('proid', 'number', '项目类型错误'),
        array('score', 'number', '分数类型错误'),
        array('msg', '3,254', '留言应3-254个字', 0, 'length', 1),
        array('msg', 'sene', '请勿含敏感字', 0, 'function', 1),

    );

    /**
     * @fn 获取某一项目的评论
     * @param $proid int 项目的ID
     * @return array 成功返回数组数据，失败返回空数组
     */
    public function getOneCom(int $proid): array
    {
        $data = $this->field('id,msg,score,authOpenid')->where(array('proid' => $proid, 'show' => 1))->select();
        if (!$data) {
            return array();
        }
        return $data;

    }


}

?>