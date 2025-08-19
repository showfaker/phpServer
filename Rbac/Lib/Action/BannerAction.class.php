<?php
class BannerAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
		$this->assign('name',$_POST['name']);
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
			$this->_list($model, $map,'title',asc);
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
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
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
		$filename=$_FILES['file']['name'];
		if($filename!=null)
		{
			$savePath = '../Public/login/';     //设置附件上传目录
			$ext = strtolower(end(explode(".",basename($filename)))); 
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;	
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$model->file = $newname;
			$model->filerealname = $filename;
		}
		$model->create_time=time();
		$model->user_id=$_SESSION['loginUserName'];
		//保存当前数据对象
		$list = $model->add();
		if ($list !== false) { //保存成功
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
			$savePath = '../Public/login/';     //设置附件上传目录
			$ext = strtolower(end(explode(".",basename($filename)))); 
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;	
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$model->file = $newname;
			$model->filerealname = $filename;
		}
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			$this->success('编辑成功!');
		} else {
			//错误提示
			$this->error('编辑失败!');
		}
	}
	function news() {
		$name = $this->getActionName();
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		if (!empty($model)) {
			$this->_list($model, null,'create_time',false);
		}
		$this->assign('vo', $vo);
		
		
		$readhistory=$model->where("id='" . $id . "'")->getField("readhistory"); 
		$readhistory.=$_SESSION['loginUserName']."于".date('Y年m月d日H:i:s',time())."阅读了该条信息。</br>";
		$model->where("id='" . $id . "'")->setField("readhistory",$readhistory); 
		
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