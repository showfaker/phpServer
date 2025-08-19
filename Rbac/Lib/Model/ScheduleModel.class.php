<?php
class ScheduleModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('content','require','内容必须'),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>