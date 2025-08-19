<?php
class FormModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('typename','require','审核定义必须！',1),
		//array('content','require','内容必须'),
		array('typename','','审核定义已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>