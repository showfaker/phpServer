<?php
class MeetingcheckAction extends CommonAction {
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if($_REQUEST['plm']){
			$map['plm'] = array('like',"%".$_REQUEST['plm']."%");
			$search['plm'] = $_REQUEST['plm'];
			$this->assign("plm",$_REQUEST['plm']);
		}
		if($_REQUEST['group']){
			$map["group"]=array('like',"%".$_REQUEST['group']."%");
			$search['group'] = $_REQUEST['group'];
			$this->assign('group', $_REQUEST['group']);
		}

		if($_REQUEST['timebeginassign']){
			$search['timebeginassign'] = $_REQUEST['timebeginassign'];
			$map['timebegin'] = array('like',"%".$_REQUEST['timebeginassign']."%");
		}
		$this->assign("timebeginassign",$_REQUEST['timebeginassign']);
		$name = "Meetingassignment";
		$model = D($name);
		$map["releaser"]=$_SESSION['loginUserName'];
		$search['releaser'] = $_SESSION['loginUserName'];

		
		if (!empty($model)) {
			$this->_list($model, $map,"timebegin",true,$search);
		}
		if($_SESSION[app]){
			$this->display(indexapp);
		}else{
			$this->display(indexoa);
		}
		return;
	}
	
	protected function _list($model, $map, $sortBy = '', $asc = false,$search) {
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
			foreach($voList as $k => $v){
				if( $v['timebegin'] < date('Y-m-d H:i',time()) && $v["status"] == 0){
					$model->where('id='.$v['id'])->setfield("status","2");
				}
				$voList[$k]['result'] = M("result")->where("meet_id=".$v['id'])->select();
			}
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			
			foreach ($search as $key => $val) {
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
        $vo = M("result")->where("id=".$_REQUEST['id'])->find();    
        $this->assign('vo', $vo);
		$meetingassignment = M("meetingassignment")->where("id=".$vo['meet_id'])->find();
        $this->assign('meetingassignment', $meetingassignment);
        if($_SESSION[skin]!=3){
        	$this->display(editoa);
        }else{
        	$this->display();
        }
    }
	
	public function conduct(){
		$meetingassignment = M("meetingassignment")->where("id=".$_REQUEST['id'])->find();
        $this->assign('meetingassignment', $meetingassignment);
        if($_SESSION[skin]!=3){
        	$this->display(conduct);
        }else{
        	$this->display();
        }
	}

	public function doconduct(){
		$info['status'] = $_REQUEST['status'];
		$info['explain'] = $_REQUEST['explain'];
		$info['partake'] = $_REQUEST['partake'];
		$info['complete_time'] = time();
    	$list = M("meetingassignment")->where("id=".$_REQUEST['id'])->save($info);
    	if (false !== $list) {
    		$this->success('决议执行成功!');
    	} else {
    		//错误提示
    		$this->error('决议执行失败!');
    	}
	}

	public function execute(){
		$vo = M("result")->where("id=".$_REQUEST['id'])->find();    
        $this->assign('vo', $vo);
		$meetingassignment = M("meetingassignment")->where("id=".$vo['meet_id'])->find();
        $this->assign('meetingassignment', $meetingassignment);
        if($_SESSION[skin]!=3){
        	$this->display(execute);
        }else{
        	$this->display();
        }
	}

	public function doexecute(){
		$info['status'] = $_REQUEST['status'];
		$info['explain'] = $_REQUEST['explain'];
		$info['complete_time'] = time();
    	$list = M("result")->where("id=".$_REQUEST['id'])->save($info);
    	if (false !== $list) {
    		$this->success('决议执行成功!');
    	} else {
    		//错误提示
    		$this->error('决议执行失败!');
    	}
	}
    
    function info() {
    	$name = "Meetingassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	if($_SESSION[skin]!=3){
    		$this->display(infooa);
    	}else{
    		$this->display();
    	}
    }
    
    function insert() {
    	//B('FilterString');
    	$name = "Meetingassignment";
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
    
    function update() {
    	// 更新数据
    	$info['result'] = $_REQUEST['content'];
    	$list = M("result")->where("id=".$_REQUEST['id'])->save($info);
    	if (false !== $list) {
    		$this->success('决议更新成功!');
    	} else {
    		//错误提示
    		$this->error('决议更新失败!');
    	}
    }
	
	public function foreverdelete() {
        //删除指定记录
        $res = M("result")->where("id=".$_REQUEST['id'])->delete();
		if($res){
			$this->success('删除成功！');
		}else{
			$this->error('删除失败！');
		}
    }

}
?>