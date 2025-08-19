<?php
// 配置类型模型
class DeptModel extends CommonModel {
	protected $_validate = array(
		array('name','require','名称必须'),
		array('name','','名称已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),
		);

	protected $_auto		=	array(
        array('status',1,self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>