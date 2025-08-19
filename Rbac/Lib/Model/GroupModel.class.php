<?php
// 配置类型模型
class GroupModel extends CommonModel {
	protected $_validate = array(
		array('name','require','名称必须'),
		array('title','require','说明必须'),
		array('name','','名称已经存在',0,'unique',self::MODEL_INSERT),
		array('title','','说明已经存在',0,'unique',self::MODEL_INSERT),
		);

	protected $_auto		=	array(
        array('status',1,self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>