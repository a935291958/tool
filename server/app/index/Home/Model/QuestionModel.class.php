<?php

namespace Home\Model;

use Think\Model;

class QuestionModel extends Model
{


    protected $_validate = array(
        array('content', 'require', '内容不能为空'),
        array('content', '3,254', '留言应3-254个字', 0, 'length', 1),
        array('content', 'sene', '请勿含敏感字', 0, 'function', 1),

        array('phone', 'number', '手机应为纯数字'),
        array('phone', 'isPhone', '手机格式错误', 0, 'function', 1),
        array('email', 'email', '邮箱格式错误'),

        array('qq', 'number', 'QQ格式错误'),


    );
}

?>