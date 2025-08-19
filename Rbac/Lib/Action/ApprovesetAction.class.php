<?php
class ApprovesetAction extends CommonAction {
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$list=M("Bjsz")->where($map)->order("id asc")->select();
		$this->assign('list', $list);
		
		$map1["status"]=1;
		$roles = M("Role")->where($map1)->order("id asc")->select();
		$this->assign('roles', $roles);
		
		
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
	//过滤查询字段	
    function insert() {
        //B('FilterString');
        $name = "Bjsz";
        $model = D($name);
        $list = $model->where("id=1")->setField("subtitle1",$_REQUEST[subtitle1]);
		$list = $model->where("id=1")->setField("subtitle2",$_REQUEST[subtitle2]);
		$list = $model->where("id=1")->setField("subtitle3",$_REQUEST[subtitle3]);
		$list = $model->where("id=1")->setField("subtitle4",$_REQUEST[subtitle4]);
		$list = $model->where("id=1")->setField("subtitle5",$_REQUEST[subtitle5]);
		$list = $model->where("id=1")->setField("subtitle6",$_REQUEST[subtitle6]);
		
		
		
       $this->success('设置成功!');
    }
    function insert1() {
        
        $name = "Bjsz";
        $model = D($name);
		
		$titlearraay=$_REQUEST["title"];
		$approverarraay=$_REQUEST["approver"];
		$approver2arraay=$_REQUEST["approver2"];
		foreach($titlearraay as $key => $val)
		{
			$model->where("id=".($key+1))->setField("title",$val);
			$model->where("id=".($key+1))->setField("approver",$approverarraay[$key]);
			$model->where("id=".($key+1))->setField("approver2",$approver2arraay[$key]);
		}
		
		
		
		
       $this->success('设置成功!');
    }
   
   
}
?>