<?php

namespace Admin\Model;
use Think\Model;

class YyghbannerModel extends Model {

	protected $_validate  = array(

		array('img','require','图片不能为空',0,'require',1),
		array('usersort','require','排序不能为空',0),
        array('usersort',array(1,9999),'排序应该为数字且范围[1-9999]',0,'between'),
        array('enable',array(0,1),'只能是显示或者不显示',0,'in'),
		array('enable','require','是否前台显示不能为空',0),
		array('id','require','ID不能为空',0,'',2),


	);

	protected $_auto = array(
        array('addtime','time',1,'function'), // 对update_time字段在更新的时候写入当前时间戳

    );



}

?>