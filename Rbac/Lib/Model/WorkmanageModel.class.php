<?php
class WorkmanageModel extends CommonModel {
	// �Զ���֤����
	protected $_validate	 =	 array(
		array('title','require','标题必须填写',1),
		array('email','email','邮箱格式错误',2),
		);
	// �Զ��������
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>