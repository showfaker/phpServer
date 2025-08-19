<?php
class FormAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
	}
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = $this->getActionName();
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		$namesetting = "Setting";
		$modelsetting = D($namesetting);
		$data=$modelsetting->where('id=1')->select();
		$this->assign('amount',$data[0]);
		if($_SESSION[skin]!=3)
		{
			$this->display(indexoa);
		}
		else
		{
			$this->display();
		}
		return;
	}
	
	function setamount() {
		$name = "Setting";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		$list = $model->where('id=1')->save();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('设置成功!');
		} else {
			//失败提示
			$this->error('设置失败!');
		}
	}
	
	function insert() {
		//B('FilterString');
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->user_id=$_SESSION['loginUserName'];
		//保存当前数据对象
		
		$filename=$_FILES['file']['name'];
		if($filename!=null)
		{
			$MAXIMUM_FILESIZE= 1024 * 1024 * M("Configure")->getField("filesize");

			$size = $_FILES['file']['size']; //文件大小
			if($size>$MAXIMUM_FILESIZE)
			{
				$this->error('上传的文件大小超过限制');
			}
			
			$savePath = '../Public/Uploads/';     //设置附件上传目录		
			if (!file_exists($savePath)) //判断是否存在目录
			{
				 mkdir($savePath);//创建目录
			}
			
			$ext = strtolower(end(explode(".",basename($filename)))); 
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;
			
			if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
			{
				$this->error("文件名不能含有特殊字符！");
			}
			if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
			{
				$this->error("非法文件类型！");
			}
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$model->photo = $newname;
			
			$imagesize=getimagesize($savePath.$newname);
			$imgagewidth=$imagesize[0];
			$imgageheight=$imagesize[1];
			$proportion1=220/$imgagewidth;
			$proportion2=200/$imgageheight;
			$proportion=$proportion2>=$proportion1?$proportion2:$proportion1;
			$this->img2thumb($savePath.$newname,$savePath.'thumb_'.$uuid.'.'.$ext,0,0,0,$proportion);
		}
		
		$list = $model->add();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function update() {
		//B('FilterString');
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		// 更新数据
		$model->create_time=time();
		$model->user_id=$_SESSION['loginUserName'];
		
		$filename=$_FILES['file']['name'];
		if($filename!=null)
		{
			$MAXIMUM_FILESIZE= 1024 * 1024 * M("Configure")->getField("filesize");

			$size = $_FILES['file']['size']; //文件大小
			if($size>$MAXIMUM_FILESIZE)
			{
				$this->error('上传的文件大小超过限制');
			}
			
			$savePath = '../Public/Uploads/';     //设置附件上传目录		
			if (!file_exists($savePath)) //判断是否存在目录
			{
				 mkdir($savePath);//创建目录
			}
			
			$ext = strtolower(end(explode(".",basename($filename)))); 
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;
			
		
			if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
			{
				$this->error("文件名不能含有特殊字符！");
			}
			if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
			{
				$this->error("非法文件类型！");
			}
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$model->photo = $newname;
			
			$imagesize=getimagesize($savePath.$newname);
			$imgagewidth=$imagesize[0];
			$imgageheight=$imagesize[1];
			$proportion1=220/$imgagewidth;
			$proportion2=200/$imgageheight;
			$proportion=$proportion2>=$proportion1?$proportion2:$proportion1;
			$this->img2thumb($savePath.$newname,$savePath.'thumb_'.$uuid.'.'.$ext,0,0,0,$proportion);
		}
		
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('编辑成功!');
		} else {
			//错误提示
			$this->error('编辑失败!');
		}
	}
	/*
	function news() {
		$name = $this->getActionName();
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		if (!empty($model)) {
			$this->_list($model, null,'create_time',false);
		}
		$this->assign('vo', $vo);
		if($_SESSION[skin]!=3)
		{
			$this->display(newsoa);
		}
		else
		{
			$this->display();
		}
	}*/
	
	function news() {
		$name = "Publicity";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$map[status]=1;
		if (!empty($model)) {
			$this->_list($model, null,'ctime',false);
		}
		$this->assign('vo', $vo);
		if($_SESSION[skin]!=3)
		{
			$this->display(newsoa);
		}
		else
		{
			$this->display();
		}
	}
}
?>