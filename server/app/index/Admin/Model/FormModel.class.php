<?php

namespace Admin\Model;
use Think\Model;

class FormModel extends Model {

	protected $_validate = array(
		array('name','require','请填写姓名',1),
		array('phone','require','请填写手机号码',1),
		array('ks','require','请选择科室',1)
	);
	


}

?>