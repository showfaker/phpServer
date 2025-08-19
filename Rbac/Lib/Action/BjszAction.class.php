<?php
class BjszAction extends CommonAction {
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = "Bjsz";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,"id",true);
		}
		
		$map1["status"]=1;
		$roles = M("Role")->where($map1)->order("id asc")->select();
		$this->assign('roles', $roles);
		
		
		$nodes=M("Settingformproject")->order("sort asc")->select();
		$this->assign('nodes', $nodes);
		
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
         $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
		$model->id=1;
		
        $list = $model->save();
        $this->success('设置成功!');
    }
    function insert1() {
        //B('FilterString');
        $name = "Bjsz";
        $model = D($name);
		
		$list = $model->where("id=1")->setField("subtitle11",$_REQUEST[subtitle11]);
		$list = $model->where("id=1")->setField("subtitle12",$_REQUEST[subtitle12]);
		$list = $model->where("id=1")->setField("subtitle13",$_REQUEST[subtitle13]);
		$list = $model->where("id=1")->setField("subtitle14",$_REQUEST[subtitle14]);
		
		
       $this->success('设置成功!');
    }
   
   
}
?>