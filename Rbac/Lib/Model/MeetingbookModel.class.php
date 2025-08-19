<?php
class MeetingbookModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		array('name','require','您还没有选择会议室！',1),
		array('begin_time','require','您还没有选择会议开始时间！',1),
		array('end_time','require','您还没有选择会议结束时间！',1),
		array('join','require','您还没有邀请参加会议人员！',1),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>