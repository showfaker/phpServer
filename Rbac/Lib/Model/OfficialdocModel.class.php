<?php
class OfficialdocModel extends CommonModel {
	// 自动验证设置
	protected $_validate	 =	 array(
		//array('number','require','必须填写发文字号！',1),
		array('title','require','必须填写发文标题！',1),
		//array('keywords','require','必须填写主题词！',1),
		//array('senddept','require','必须填写主送机关！',1),
		//array('content','require','必须填写内容必须'),
		//array('title','','标题已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),
		);
	// 自动填充设置
	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);

}
?>