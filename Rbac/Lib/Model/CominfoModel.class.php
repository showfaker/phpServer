<?php
class CominfoModel extends CommonModel {
	// �Զ���֤����
	protected $_validate	 =	 array(
		array('name','require','名称必须',1),
			array('address','require','地址必须',1),
			array('email','email','邮箱格式错误',2),
		);
	// �Զ��������
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>