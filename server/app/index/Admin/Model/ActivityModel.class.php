<?php

namespace Admin\Model;
use Think\Model;

class ActivityModel extends Model {

	protected $_validate = array(
		array('content  ','require','请填写活动内容',3),
		array('times','require','请填写活动时间',3),
		array('title','require','请填写标题',3),
		array('number','require','请填写报名人数',3),
		array('enable','require','请填写是否启用',3),
	);






}

?>