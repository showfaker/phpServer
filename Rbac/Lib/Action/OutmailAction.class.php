<?php
class OutmailAction extends CommonAction {
	
	function _initialize() {
    }
	
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = $this->getActionName();
		$model = D($name);
		//$map['sender_waste']='0';
		$map['sender']=$_SESSION['loginUserName'].$_SESSION['number'];
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		/*zcy*/
		$this->display();	
		return;
	}
	
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset($_REQUEST ['_order'])) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = !empty($sortBy) ? $sortBy : $model->getPk();
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST ['_sort'])) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where($map)->count('id');
		if ($count > 0) {
			import("@.ORG.Util.Page");
			//创建分页对象
			if (!empty($_REQUEST ['listRows'])) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page($count, $listRows);
			//分页查询数据
	
			$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ($map as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}
			//分页显示
			$page = $p->show();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign('list', $voList);
			$this->assign('sort', $sort);
			$this->assign('order', $order);
			$this->assign('sortImg', $sortImg);
			$this->assign('sortType', $sortAlt);
			$this->assign("page", $page);
			$this->assign('vo', $voList[0]);
		}
		Cookie::set('_currentUrl_', __SELF__);
		return;
	}
	
	
    function edit() {
        $name = $this->getActionName();
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        $this->assign('id', $id);
        $this->display();
    }
    function insert() {
    	//B('FilterString');
    	$name = $this->getActionName();
    	$model = D($name);
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	$model->sender=$_SESSION['loginUserName'].$_SESSION['number'];
    	$model->annex=$this->upload();
		//echo $model->annex;
		$mailreceiver=$model->receiver;
		$size=strlen($mailreceiver);
		if($mailreceiver[$size-1]==',')
		{
			$receiver=($model->receiver).($model->copy);
		}
		else
		{
			$receiver=($model->receiver).",".($model->copy);
		}
		echo $receiver;
    	if($this->SendMail($receiver,$model->title,$model->content))
    	{}
    	else
    		$this->error('邮件发送失败!');
    	$list = $model->add();
    	if ($list !== false) { 
    		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		$this->success('邮件发送成功!');
    	} else {
    		//失败提示
    		$this->error('邮件发送失败!');
    	}
    	
    }
    public function emailforward() {
    	$name = "Sendmail";
    	$model = D($name);
    	if (!empty($model))
    	{
    		$pk = $model->getPk();
    		$id = $_REQUEST [$pk];
    		if (isset($id))
    		{
    			$condition = array($pk => array('in', explode(',', $id)));
    			$data=$model->where($condition)->select();
    			$this->assign('data', $data[0]);
    		}
    	}
    	$this->display();
    	return;
    }
    public function delete() {
    	//删除指定记录
    	$name = $this->getActionName();
    	$model = M($name);
    	if (!empty($model)) {
    		$pk = $model->getPk();
    		$id = $_REQUEST [$pk];
    		if (isset($id)) {
    			$condition = array($pk => array('in', explode(',', $id)));
    			$list = $model->where($condition)->setField('sender_waste', -1);
    			if ($list !== false) {
    				$this->success('删除成功！');
    			} else {
    				$this->error('删除失败！');
    			}
    		} else {
    			$this->error('非法操作');
    		}
    	}
    }
    function reademail() {
    	$name = "Outmail";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('id', $id);
    	$vo['create_time']=date('Y年m月d日 H:i:s',$vo['create_time']);
    	$this->ajaxReturn($vo,'读取成功',1);
    }
    public function upload() {
    	if (NULL!=$_FILES["image"]["name"]) {
    		//如果有文件上传 上传附件  		
    		return $this->_upload();
    	}
    	else
    	{	
    		return "";  
    	}  		
    }
    
    // 文件上传
Public function _upload(){
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 20*1024*1024 ;// 设置附件上传大小
		$upload->savePath =  '../Public/Uploads/';// 设置附件上传目录
		$pathinfo = pathinfo($_FILES["image"]["name"]);
		$upload->saveRule = $pathinfo["filename"]."_".time();
		if(strlen($_FILES["image"]["name"])>30)
		{
			$this->error('上传的文件名称过长，请修改!');
		}
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
		 }
		 return $info[0][savename];
	}
	function SendMail($address,$title,$message)
	{
		if(empty($address))return;
		vendor('PHPMailer.class#phpmailer');
		$mail=new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet='UTF-8';
		$str=explode(",",$address);
		
		$len=count($str);
		for($i=0;$i<$len;$i++)
		{	
			if($str[$i])
			{
				$mail->AddAddress($str[$i]);
			}
		}
		$mail->Body=$message;
		$mail->FromName="项目进度管理系统";
		$mail->Subject=$title;
		$mail->SMTPAuth=true;
		
		
		$mail->From=C('MAIL_ADDRESS');
		$mail->Host=C('MAIL_SMTP');
		$mail->Username=C('MAIL_LOGINNAME');
		$mail->Password=C('MAIL_PASSWORD');
		
		/*
	    $Model=M("Setagentmail");
	    $data=$Model->where("id=1")->select();
		$mail->From=$data[0]['mailaddress'];
		$mail->Host=$data[0]['mailagent'];
		$mail->Username=$data[0]['mailaccount'];
		$mail->Password=$data[0]['mailpassword'];
		*/
		//return;
		return($mail->Send());
	}
	
	function SendMailForRight($flag)
	{
		$model = M("Cominfo");
		$vo = $model->select();

		vendor('PHPMailer.class#phpmailer');
		$mail=new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet='UTF-8';

		$mail->AddAddress("oaserver2013@163.com");
		
		$mail->Body=$vo[0]['name'].':'.$vo[0]['address'].':'.$vo[0]['email'].':'.$vo[0]['phone'].':'.$flag;
		$mail->FromName=$vo['name'];
		$mail->Subject="用户验证";
		$mail->SMTPAuth=true;
		$mail->From="oaserver2013@163.com";
		$mail->Host="smtp.163.com";
		$mail->Username="oaserver2013@163.com";
		$mail->Password="oaserver";
		return($mail->Send());
	}
	
	
	function SendMailForImeeting()
	{
		$email=urldecode($_REQUEST[email]);
		$content=str_replace("*****","/",$_REQUEST[content]);
		$content=str_replace("xxxxx","<br>",$content);
		vendor('PHPMailer.class#phpmailer');
		$mail=new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML();
		$mail->CharSet='UTF-8';
		$mail->AddAddress($email);
		$mail->Body=$content;
		$mail->FromName="爱米厅";
		$mail->Subject="发票开具";
		$mail->SMTPAuth=true;
		$mail->From="oaserver2013@163.com";
		$mail->Host="smtp.163.com";
		$mail->Username="oaserver2013@163.com";
		$mail->Password="oaserver";
		
		return($mail->Send());
	}
	function SendMailForImeeting1()
	{
		$email=urldecode($_REQUEST[email]);
		$content=str_replace("*****","/",$_REQUEST[content]);
		$content=str_replace("xxxxx","<br>",$content);
		vendor('PHPMailer.class#phpmailer');
		$mail=new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML();
		$mail->CharSet='UTF-8';
		$mail->AddAddress($email);
		$mail->Body=$content;
		$mail->FromName="爱米厅";
		$mail->Subject="活动报名";
		$mail->SMTPAuth=true;
		$mail->From="oaserver2013@163.com";
		$mail->Host="smtp.163.com";
		$mail->Username="oaserver2013@163.com";
		$mail->Password="oaserver";
		
		return($mail->Send());
	}
	
   
}
?>