<?php
class LogAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['name'] = array('like',"%".$_POST['name']."%");
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
			$this->_list($model, $map,'time',false);
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