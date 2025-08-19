<?php
class SetagentmailModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('mailaddress','require','外部邮箱地址必须填写！',1),
			array('mailaccount','require','外部邮箱账号必须填写！',1),
			array('mailpassword','require','外部邮箱密码必须填写！',1),
			array('mailagent','require','外部邮箱服务必须填写！',1),
			array('mailaddress','email','邮箱格式错误！',2),
			array('mailaccount','email','邮箱格式错误！',2),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>