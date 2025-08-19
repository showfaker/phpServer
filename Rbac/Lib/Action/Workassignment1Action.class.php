<?php
class Workassignment1Action extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
		$this->assign('name', $_POST['name']);
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		
		if($_REQUEST['plm']){
			$map['plm'] = array('like',"%".$_REQUEST['plm']."%");
			$this->assign("plm",$_REQUEST['plm']);
		}
		
		
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign']))){
			$map['create_time'] = array('gt',strtotime($_REQUEST['timebeginassign']));
		}else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['create_time'] = array('lt',strtotime($_REQUEST['timeendassign']));
		}else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['create_time'] = array(array('gt',strtotime($_REQUEST['timebeginassign'])),array('lt',strtotime($_REQUEST['timeendassign'])),'and');
		}
		$this->assign("timebeginassign",$_REQUEST['timebeginassign']);
		$this->assign("timeendassign",$_REQUEST['timeendassign']);
		$name = "Workassignment";
		$model = D($name);
		$map['current'] = array("exp","like '%".$_SESSION['loginUserName']."%' or `releaser` = '".$_SESSION['loginUserName']."' or `current` like '%".$_SESSION['dept']."%' or `current` = '公司'");
		$map["type"]="1";
		$voList = $model->where($map)->select();
		foreach ($voList as $key=>$value){
			if(($voList[$key]['timeend']<date('Y-m-d',time())) && ($value["status"]!=1 && $value["status"]!=2)){
				//&($value[status]==0)//如果已经完成了，就已经超期了
				$voList[$key]['status']=3;
				$condition['id'] = $voList[$key]['id'];
				$model->where($condition)->save($voList[$key]);
			}
		}
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		if($_SESSION[app]){
			$this->display(indexapp);
		}else{
			$this->display(indexoa);
		}
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
			//$p = new Page($count, $listRows);
			$p = new Page($count, 20);
			$this->assign("totalCount", $p->totalRows);
			$this->assign("numPerPage", $p->listRows);
			$this->assign("currentPage", $p->nowPage);
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
		}
		Cookie::set('_currentUrl_', __SELF__);
		return;
	}

	function add() {
        $this->getAllcities(1);
		$this->display(addoa);
    }
	
      function edit() {
		$allprojects = M("project")->order("ctime desc")->select();
		$this->assign('allprojects', $allprojects);
		//查找所有部门
		$section = M("dept")->select();
		$this->assign('section', $section);

        $name = "Workassignment";
        $model = M($name);
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3){
        	$this->display(editoa);
        }else
        {
        	$this->display();
        }
    }
    
    function info() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(infooa);
    	}
    	else
    	{
    		$this->display();
    	}
    }
    
    function insert() {
    	//B('FilterString');
    	$name = "Workassignment";
    	$model = D($name);
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	if($model->timebegin=="")
    	{
    		$this->error("请填写起始时间!");
    	}
    	if($model->timeend=="")
    	{
    		$this->error("请填写完成时间!");
    	}
    	if($model->current=="")
    	{
    		$this->error("请填写责任人!");
    	}
    	
    	$date=date('Y-m-d H:i');
		$model->plm =M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
    	$data['content']=$_SESSION['loginUserName']."于".$date."委派给您一项任务，任务名称：".$model->title."，请及时处理。";
    	$data['href'] ="index.php?s=Mywork/index";
		$data['taskid'] =time();
    	$scheduleuser=explode(',',$model->current);
    	$model->process="任务过程：\n由".$_SESSION['loginUserName'].$_SESSION['number']."下达任务,";
		$model->taskid=$data['taskid'];
		$model->all=$model->current;
    	$list = $model->add();
    	if ($list !== false) { //保存成功
    		/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		foreach ($scheduleuser as $key=>$value)
    		{
    			if($value!=null)
    			{
	    			$data['user']=$value;
	    			$this->Addschedule($data);
    			}
    		}
			
			$emaildata['content'] =$_SESSION['loginUserName']."于".$date."发布了一项任务，任务名称：".$_REQUEST[title]."，您是查看人，请关注。";
			$emaildata['receiver']=$_REQUEST[check];
			$emaildata['status']=1;
			$emaildata['sender']="系统通知";
			$emaildata['create_time']=time();
			$emaildata['title'] =$_SESSION['loginUserName']."于".$date."发布了一项任务，任务名称：".$_REQUEST[title]."，您是查看人，请关注。";
			$this->Sendmail($emaildata);
			
    		$this->success('委派任务成功!');
    	} else {
    		//失败提示
    		$this->error('委派任务失败!');
    	}
    }

	public function pursue(){
		$name = "Workassignment";
        $model = M($name);
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        $this->display();
	}

	public function dopursue(){
		$info['status'] = $_REQUEST['status'];
		$info['complete'] = $_SESSION['loginUserName'];
		$info['feedback'] = $_REQUEST['feedback'];
		$list = M("workassignment")->where("id=".$_REQUEST['id'])->save($info);
		if (false !== $list) {
			//查看所有的项目是否完成 全部完成 总项目完成
			$plan_id = M("workassignment")->where("id=".$_REQUEST['id'])->getfield("plan_id");
			$where['status'] = array("in","0,2");
			$where['plan_id'] = $plan_id;
			$res = M("workassignment")->where($where)->find();
			if(!$res){//全部完成
				$data['status'] = "1";
				M("workplan")->where("id=".$plan_id)->save($data);
			}else{
				$data['status'] = "0";
				M("workplan")->where("id=".$plan_id)->save($data);
			}
			$this->success('职能计划成功!');
		} else {
			//错误提示
			$this->error('职能计划失败!');
		}
	}

    function update() {
		$info['content'] = $_REQUEST['content'];
    	$list = M("workassignment")->where("id=".$_REQUEST['id'])->save($info);
    	if (false !== $list) {
    		$this->success('职能计划更新成功!');
    	} else {
    		//错误提示
    		$this->error('职能计划更新失败!');
    	}
    }
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Workassignment";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            //$id = $_REQUEST [$pk];
            if(!empty($_REQUEST [$pk]))
            {
            	$id = $_REQUEST [$pk];
				
				$mapschedule[taskid]=$model->where("id='" . $_REQUEST['id'] . "'")->getField('taskid');
				M("Schedule")->where($mapschedule)->setField("status",0);
            }
            else
            {
            	$id = $_REQUEST ["ids"];
            }
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete())
				{
                    //echo $model->getlastsql();
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }

}
?>