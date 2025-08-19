<?php
// 用户模型
class PayrollModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('belong','require','必须填写所属期！',1),
		array('real','require','必须填写实际发放期！',1),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>