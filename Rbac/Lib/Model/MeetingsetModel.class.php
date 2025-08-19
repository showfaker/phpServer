<?php
class MeetingsetModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','必须填写会议室名称！',1),
		array('name','','会议室名称已经存在',0,'unique',self::MODEL_INSERT),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>