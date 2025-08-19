<?php
class WorkreportModel extends CommonModel {
	// �Զ���֤����
	protected $_validate	 =	 array(
		array('title','require','标题必须填写',1),
		array('receiver','require','接收人必须填写',1),
		array('email','email','邮箱格式不正确',2),
		/*array('content','require','内容必须填写'),
		array('title','','标题必须唯一',0,'unique',self::MODEL_INSERT),*/
		);
	// �Զ��������
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>