<?php
class ProductModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','产品名称必须！',1),
		array('email','email','邮箱格式错误！',2),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>