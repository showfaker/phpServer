<?php
class WastebasketModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','必须填写标题',1),
		);
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('commit_time','0',self::MODEL_INSERT),
			array('update_time','0',self::MODEL_INSERT),
		);

}
?>