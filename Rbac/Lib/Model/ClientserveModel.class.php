<?php
class ClientserveModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('title','require','客户服务主题必须！',1),
		array('email','email','邮箱格式错误！',2),
		array('title','','客户服务主题已经存在',0,'unique',1),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		//array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>