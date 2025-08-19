<?php
// 用户模型
class SalarysettingModel extends CommonModel {
	protected $_validate	 =	 array(
			array('content','require','条目必须填写！',1),
	);
	// 自动填充设置
	protected $_auto	 =	 array(
			array('status','1',self::MODEL_INSERT),
	);
}
?>