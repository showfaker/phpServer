<?php
class MyworkModel extends CommonModel {
	protected $_validate	 =	 array(
		/*array('title','require','标题必须',1),
		array('email','email','邮箱格式不正确',2),
		array('content','require','内容必须'),
		array('title','','标题已存在',0,'unique',self::MODEL_INSERT),*/
		array('current','require','没有选择人员',1),
		);
	protected $_auto	 =	 array(
		/*array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),*/
		);

}
?>