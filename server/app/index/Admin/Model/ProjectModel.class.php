<?php

namespace Admin\Model;
use Think\Model;

class ProjectModel extends Model {

	protected $_validate  = array(

		array('times','require','请填写有效时间',3),
		array('title','require','请填写商品名称',3),
		array('number','require','请填写购买人数',3),
		array('enable','require','请填写是否启用',3),
        array('oldprice','require','请填写原价',3),
        array('newprice','require','请填写现价')


	);






}

?>