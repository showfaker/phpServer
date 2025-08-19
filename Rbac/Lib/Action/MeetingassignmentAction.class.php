<?php
class MeetingassignmentAction extends CommonAction {
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
		if($_REQUEST['group']){
			$map["group"]=array('like',"%".$_REQUEST['group']."%");
			$this->assign('group', $_REQUEST['group']);
		}
		if(!empty($_REQUEST['timebeginassign'])){
			$map['timebegin'] = array('like',"%".$_REQUEST['timebeginassign']."%");
		}
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
		}else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}
		$this->assign("timebeginassign",$_REQUEST['timebeginassign']);
		$this->assign("timeendassign",$_REQUEST['timeendassign']);
		$model = D("meetingplan");
		if (!empty($model)) {
			$this->_list($model, $map,"timebegin",true);
		}
		$where['status']=1;
		$where['more']=1;
		$group=M("group")->where($where)->order("sort asc")->select();
		$this->assign('groups',$group);
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
			foreach($volist as $k => $v){
				$meetingassignment = M("meetingassignment")->where("plan_id=".$v['id'])->order("begintime asc")->select();
				foreach($meetingassignment as $key => $val){
					$volist[$k]['meetingassigment'] .= $val['begintime'];
				}
			}
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
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);
        // $this->getAllcities(1);
		//查找所有项目
		$allprojects = M("project")->order("ctime desc")->select();
        $this->assign('allprojects', $allprojects);
		$where['status']=1;
		$where['more']=1;
		$group=M("group")->where($where)->order("sort asc")->select();
		$this->assign('group',$group);
		//查找
		$map['type'] = "1";
		$meetingset1 = M("meetingset")->where($map)->select();
		$map['type'] = "2";
		$meetingset2 = M("meetingset")->where($map)->select();
		$map['type'] = "3";
		$meetingset3 = M("meetingset")->where($map)->select();
		$map['type'] = "4";
		$meetingset4 = M("meetingset")->where($map)->select();
        $this->assign('meetingset1', $meetingset1);
        $this->assign('meetingset2', $meetingset2);
        $this->assign('meetingset3', $meetingset3);
        $this->assign('meetingset4', $meetingset4);
		$this->display(addoa);
    }

	function adds() {
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);
        // $this->getAllcities(1);
		//查找所有项目
		$allprojects = M("project")->order("ctime desc")->select();
        $this->assign('allprojects', $allprojects);
		//查找所有主项节点
		$where['status']=1;
		$where['more']=1;
		$group=M("group")->where($where)->order("sort asc")->select();
		$this->assign('group',$group);
		//查找
		$map['type'] = "1";
		$meetingset1 = M("meetingset")->where($map)->select();
		$map['type'] = "2";
		$meetingset2 = M("meetingset")->where($map)->select();
		$map['type'] = "3";
		$meetingset3 = M("meetingset")->where($map)->select();
		$map['type'] = "4";
		$meetingset4 = M("meetingset")->where($map)->select();
        $this->assign('meetingset1', $meetingset1);
        $this->assign('meetingset2', $meetingset2);
        $this->assign('meetingset3', $meetingset3);
        $this->assign('meetingset4', $meetingset4);
		$this->display(addsoa);
    }

	function find_meeting_mode(){
		$where['id'] = $_REQUEST['id'];
		$meetmode = M("meetingset")->where($where)->find();
		if($meetmode){
			return $this->send_success("获取成功",$meetmode);
		}else{
			return $this->send_error("获取失败");
		}
	}

    function edit() {
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);
        $model = M("meetingassignment");
        $id = $_REQUEST["id"];
        $vo = $model->getById($id);
		$vo['timebegin'] =$vo['timebegin']; 
        $this->assign('vo', $vo);
		//查找所有项目
		$allprojects = M("project")->order("ctime desc")->select();
        $this->assign('allprojects', $allprojects);
		//查找所有主项节点
		$where['status']=1;
		$where['more']=1;
		$group=M("group")->where($where)->order("sort asc")->select();
		$this->assign('group',$group);
		//查找
		$map['type'] = "1";
		$meetingset1 = M("meetingset")->where($map)->select();
		$map['type'] = "2";
		$meetingset2 = M("meetingset")->where($map)->select();
		$map['type'] = "3";
		$meetingset3 = M("meetingset")->where($map)->select();
		$map['type'] = "4";
		$meetingset4 = M("meetingset")->where($map)->select();
		$map['type'] = "5";
		$meetingset5 = M("meetingset")->where($map)->select();
        $this->assign('meetingset1', $meetingset1);
        $this->assign('meetingset2', $meetingset2);
        $this->assign('meetingset3', $meetingset3);
        $this->assign('meetingset4', $meetingset4);
        $this->assign('meetingset5', $meetingset5);
        if($_SESSION[skin]!=3){
        	$this->display(editoa);
        }else{
        	$this->display();
        }
    }


	function edits() {
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);
        $model = M("meetingplan");
        $id = $_REQUEST["id"];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
		//查找所有项目
		$allprojects = M("project")->order("ctime desc")->select();
        $this->assign('allprojects', $allprojects);
		//查找所有主项节点
		$where['status']=1;
		$where['more']=1;
		$group=M("group")->where($where)->order("sort asc")->select();
		$this->assign('group',$group);
		//查找
		$map['type'] = "1";
		$meetingset1 = M("meetingset")->where($map)->select();
		$map['type'] = "2";
		$meetingset2 = M("meetingset")->where($map)->select();
		$map['type'] = "3";
		$meetingset3 = M("meetingset")->where($map)->select();
		$map['type'] = "4";
		$meetingset4 = M("meetingset")->where($map)->select();
        $this->assign('meetingset1', $meetingset1);
        $this->assign('meetingset2', $meetingset2);
        $this->assign('meetingset3', $meetingset3);
        $this->assign('meetingset4', $meetingset4);
        $this->display(editsoa);
    }

    function info() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(infooa);
    	}else{
    		$this->display();
    	}
    }

	function infos() {
    	$model = M("meetingplan");
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	$this->display(infosoa);
    }
    
    function insert() {
    	$model = D("meetingassignment");
		$info['type1'] = $_REQUEST['type1'];
		$info['type2'] = $_REQUEST['type2'];
		$info['type3'] = $_REQUEST['type3'];
		$info['type4'] = $_REQUEST['type4'];
		$info['plmid'] = $_REQUEST['plmid'];
		$info['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$info['create_time'] = time();
		$info['group'] = $info['group'] = $_REQUEST['group'];
		$info['title'] = $_REQUEST['title'];
		$info['timebegin'] = str_replace("T"," ",$_REQUEST['timebegin']);
		$info['aim'] = $_REQUEST['aim'];
		$info['recorder'] = $_REQUEST['recorder'];
		$info['attendee'] = $_REQUEST['attendee'];
		$info['agenda'] = $_REQUEST['agenda'];
		$info['materials'] = $_REQUEST['materials'];
		$info['content'] = $_REQUEST['content'];
		$info['releaser'] = $_SESSION['loginUserName'];
		$res = M("meetingassignment")->add($info);
    	if ($res) {
    		$this->success('会议添加成功!');
    	} else {
    		$this->error('会议添加失败!');
    	}
    }

	function inserts() {
		//先添加会议计划
		$datainfo['title'] = $_REQUEST['title'];
		$datainfo['plmid'] = $_REQUEST['plmid'];
		$datainfo['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$datainfo['group'] = $_REQUEST['group'];
		$datainfo['type1'] = $_REQUEST['type1'];
		$datainfo['type2'] = $_REQUEST['type2'];
		$datainfo['type3'] = $_REQUEST['type3'];
		$datainfo['type4'] = $_REQUEST['type4'];
		$datainfo['attendee'] = $_REQUEST['attendee'];
		$datainfo['agenda'] = $_REQUEST['agenda'];
		$datainfo['materials'] = $_REQUEST['materials'];
		$datainfo['recorder'] = $_REQUEST['recorder'];
		$datainfo['aim'] = $_REQUEST['aim'];
		$datainfo['content'] = $_REQUEST['content'];
		$datainfo['timebegin'] = $_REQUEST['timebegin'];
		$datainfo['timeend'] = $_REQUEST['timeend'];
		$datainfo['releaser'] = $_SESSION['loginUserName'];
		$datainfo['create_time'] = time();
		$datainfo['circle'] = $_REQUEST['circle'];
		$datainfo['day'] = $_REQUEST['day'];
		$datainfo['week'] = $_REQUEST['week'];
		$datainfo['hour'] = $_REQUEST['hour'];
		$datainfo['minute'] = $_REQUEST['minute'];
		$data = M("meetingplan")->add($datainfo);

    	$model = D("meetingassignment");
		$info['plan_id'] = $data;
		$info['type1'] = $_REQUEST['type1'];
		$info['type2'] = $_REQUEST['type2'];
		$info['type3'] = $_REQUEST['type3'];
		$info['type4'] = $_REQUEST['type4'];
		$info['plmid'] = $_REQUEST['plmid'];
		$info['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$info['create_time'] = time();
		$info['group'] = $_REQUEST['group'];
		$info['title'] = $_REQUEST['title'];
		$info['aim'] = $_REQUEST['aim'];
		$info['recorder'] = $_REQUEST['recorder'];
		$info['attendee'] = $_REQUEST['attendee'];
		$info['agenda'] = $_REQUEST['agenda'];
		$info['materials'] = $_REQUEST['materials'];
		$info['content'] = $_REQUEST['content'];
		$info['releaser'] = $_SESSION['loginUserName'];
		//判断天 周 月
		if($_REQUEST['circle'] == "1"){
			//每天
			$i = strtotime($_REQUEST['timebegin']);
			while($i <= strtotime($_REQUEST['timeend'])){
				$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-d ".$_REQUEST['hour'].":".$_REQUEST['minute'],$i)));
				$i +=86400;
				$res = M("meetingassignment")->add($info);
			}
		}elseif($_REQUEST['circle'] == "2"){
			//每星期
			$i = strtotime($_REQUEST['week'], strtotime($_REQUEST['timebegin']));
			while($i <= strtotime($_REQUEST['timeend'])){
				$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-d ".$_REQUEST['hour'].":".$_REQUEST['minute'],$i)));
				$i += 86400*7;
				$res = M("meetingassignment")->add($info);
			}
		}else{
			//每月
			if(strtotime($_REQUEST['timebegin']) > strtotime(date("Y-m-".$_REQUEST['day'],strtotime($_REQUEST['timebegin'])))){
				$i = strtotime("+1 month",strtotime(date("Y-m-".$_REQUEST['day'],strtotime($_REQUEST['timebegin']))));
			}else{
				$i = strtotime(date("Y-m-".$_REQUEST['day'],strtotime($_REQUEST['timebegin'])));
			}
			while($i <= strtotime($_REQUEST['timeend'])){
				$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-".$_REQUEST['day']." ".$_REQUEST['hour'].":".$_REQUEST['minute'],$i)));
				$res = M("meetingassignment")->add($info);
				$i = strtotime("+1 month",strtotime(date("Y-m-".$_REQUEST['day'],$i)));
			}
		}
    	if($res){
    		$this->success('会议计划制作成功!');
    	} else {
    		$this->error('会议计划制作失败!');
    	}
    }

    function update() {
		$info['type1'] = $_REQUEST['type1'];
		$info['type2'] = $_REQUEST['type2'];
		$info['type3'] = $_REQUEST['type3'];
		$info['type4'] = $_REQUEST['type4'];
		$info['plmid'] = $_REQUEST['plmid'];
		$info['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$info['update_time'] = time();
		$info['group'] = $_REQUEST['group'];
		$info['title'] = $_REQUEST['title'];
		$info['timebegin'] = $_REQUEST['timebegin'];
		$info['aim'] = $_REQUEST['aim'];
		$info['recorder'] = $_REQUEST['recorder'];
		$info['attendee'] = $_REQUEST['attendee'];
		$info['agenda'] = $_REQUEST['agenda'];
		$info['materials'] = $_REQUEST['materials'];
		$info['content'] = $_REQUEST['content'];
    	$res = M("meetingassignment")->where("id=".$_REQUEST['id'])->save($info);
    	if ($res) {
    		$this->success('会议计划更新成功!');
    	} else {
    		$this->error('会议计划更新失败!');
    	}
    }

	function updates() {
		//更新会议计划
		$meetingplan = M("meetingplan")->where("id=".$_REQUEST['id'])->find();

		$datainfo['title'] = $_REQUEST['title'];
		$datainfo['plmid'] = $_REQUEST['plmid'];
		$datainfo['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$datainfo['group'] = $_REQUEST['group'];
		$datainfo['type1'] = $_REQUEST['type1'];
		$datainfo['type2'] = $_REQUEST['type2'];
		$datainfo['type3'] = $_REQUEST['type3'];
		$datainfo['type4'] = $_REQUEST['type4'];
		$datainfo['attendee'] = $_REQUEST['attendee'];
		$datainfo['agenda'] = $_REQUEST['agenda'];
		$datainfo['materials'] = $_REQUEST['materials'];
		$datainfo['recorder'] = $_REQUEST['recorder'];
		$datainfo['aim'] = $_REQUEST['aim'];
		$datainfo['content'] = $_REQUEST['content'];
		$datainfo['timebegin'] = $_REQUEST['timebegin'];
		$datainfo['timeend'] = $_REQUEST['timeend'];
		$datainfo['update_time'] = time();
		$datainfo['circle'] = $_REQUEST['circle'];
		$datainfo['day'] = $_REQUEST['day'];
		$datainfo['week'] = $_REQUEST['week'];
		$datainfo['hour'] = $_REQUEST['hour'];
		$datainfo['minute'] = $_REQUEST['minute'];
		$data = M("meetingplan")->where("id=".$_REQUEST['id'])->save($datainfo);

		//查找未开会计划
		$info['type1'] = $_REQUEST['type1'];
		$info['type2'] = $_REQUEST['type2'];
		$info['type3'] = $_REQUEST['type3'];
		$info['type4'] = $_REQUEST['type4'];
		$info['plmid'] = $_REQUEST['plmid'];
		$info['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$info['group'] = $_REQUEST['group'];
		$info['title'] = $_REQUEST['title'];
		$info['aim'] = $_REQUEST['aim'];
		$info['recorder'] = $_REQUEST['recorder'];
		$info['attendee'] = $_REQUEST['attendee'];
		$info['agenda'] = $_REQUEST['agenda'];
		$info['materials'] = $_REQUEST['materials'];
		$info['content'] = $_REQUEST['content'];
		$info['plan_id'] = $_REQUEST['id'];
		$info['update_time'] = time();
    	//判断天 周 月
		if($_REQUEST['circle'] != $meetingplan['circle'] || $_REQUEST['week'] != $meetingplan['circle'] || $_REQUEST['day'] != $meetingplan['circle'] || $_REQUEST['hour'] != $meetingplan['circle'] || $_REQUEST['minute'] != $meetingplan['circle']){
			//今天之前不做改动 今天之后做调整
			$info['create_time'] = time();
			$info['releaser'] = $_SESSION['loginUserName'];
			$where['timebegin'] = array("gt",date("Y-m-d H:i"));
			$where['plan_id'] = $_REQUEST['id'];
			M("meetingassignment")->where($where)->delete();
			
			if($_REQUEST['circle'] == "1"){
				//每天
				if( strtotime($_REQUEST['timebegin']) > time()){
					$i = strtotime($_REQUEST['timebegin']);
				}else{
					$i = time()+86400;
				}
				while($i <= strtotime($_REQUEST['timeend'])){
					$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-d ".$_REQUEST['hour'].":".$_REQUEST['minute'],$i)));
					$i +=86400;
					$res = M("meetingassignment")->add($info);
				}
			}elseif($_REQUEST['circle'] == "2"){
				//每星期
				if( strtotime($_REQUEST['timebegin']) > time()){
					$i = strtotime($_REQUEST['week'], strtotime($_REQUEST['timebegin']));
				}else{
					$i = strtotime($_REQUEST['week'], time()+86400);
				}
				while($i <= strtotime($_REQUEST['timeend'])){
					$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-d ".$_REQUEST['hour'].":".$_REQUEST['minute'],$i)));
					$i += 86400*7;
					$res = M("meetingassignment")->add($info);
				}
			}else{
				//每月
				if(strtotime($_REQUEST['timebegin']) > time()){
					if(strtotime($_REQUEST['timebegin']) > strtotime(date("Y-m-".$_REQUEST['day'],strtotime($_REQUEST['timebegin'])))){
						$i = strtotime("+1 month",strtotime(date("Y-m-".$_REQUEST['day'],strtotime($_REQUEST['timebegin']))));
					}else{
						$i = strtotime(date("Y-m-".$_REQUEST['day'],strtotime($_REQUEST['timebegin'])));
					}
				}else{
					if(date("j") >= $_REQUEST['day']){
						$i = strtotime("+1 month",strtotime(date("Y-m-".$_REQUEST['day'],time())));
					}else{
						$i = strtotime(date("Y-m-".$_REQUEST['day'],time()));
					}
				}
				while($i <= strtotime($_REQUEST['timeend'])){
					$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-".$_REQUEST['day']." ".$_REQUEST['hour'].":".$_REQUEST['minute'],$i)));
					$res = M("meetingassignment")->add($info);
					$i = strtotime("+1 month",strtotime(date("Y-m-".$_REQUEST['day'],$i)));
				}
			}
		}
    	if ($data) {
    		$this->success('会议计划更新成功!');
    	} else {
    		$this->error('会议计划更新失败!');
    	}
    }

	public function result(){
		$result = M("meetingassignment")->where("id=".$_REQUEST['id'])->find();
		$this->assign("result",$result);
        $this->display();
	}

	public function resultdetail(){
		$meetingassignment = M("meetingassignment")->where("id=".$_REQUEST['id'])->find();
		$this->assign("meetingassignment",$meetingassignment);
		$result = M("result")->where("meet_id=".$_REQUEST['id'])->order("ctime asc")->select();
		$this->assign("result",$result);

        $this->display();
	}

	public  function doresult(){
		foreach($_REQUEST['content'] as $k => $v){
			if($v){
				$info['meet_id'] = $_REQUEST['meet_id'];
				$info['result'] = $v;
				$info['ctime'] = time();
				$info['add_user'] = $_SESSION['loginUserName'];
				M("result")->add($info);
			}
		}
		$this->success('会议决议添加成功!');
	}
	
	public function foreverdelete() {
		$id=$_REQUEST['id'];
		$res=M('meetingplan')->where(array('id'=>$id))->delete();
		$res=M('meetingassignment')->where(array('plan_id'=>$id))->delete();
		if($res){
			//删除所有会议决议
			M("result")->where("meet_id=".$id)->delete();
			$this->success('会议计划删除成功!');
		}else{
			$this->error('会议计划删除失败!');
		}
	}

	public function toexcel(){
		$model=M("meetingplan");
		if (method_exists($this, '_filter')){
			$this->_filter($map);
		}
		if($_REQUEST['plm']){
			$map['plm'] = array('like',"%".$_REQUEST['plm']."%");
		}
		if($_REQUEST['plmgroup']){
			$mapforSecondgroup["name"]=array('like',"%".$_REQUEST['plmgroup']."%");
			$plmgrouparray=M("Secondgroup")->where($mapforSecondgroup)->field("id")->select();
			foreach($plmgrouparray as $key => $val){
				$plmgroupids.=$val["id"].",";
			}
			$plmgroupids= substr($plmgroupids,0,strlen($plmgroupids)-1);
			$map['groupid'] = array('in',$plmgroupids);
		}
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
		}else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}
		$volist=$model->where($map)->order('timebegin asc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++){
			$data[$i]['plm']=$volist[$i]['plm'];
			$data[$i]['group']=$volist[$i]['group'];
			$data[$i]['type1']=$volist[$i]['type1'];
			$data[$i]['type2']=$volist[$i]['type2'];
			$data[$i]['type3']=$volist[$i]['type3'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['timebegin']=$volist[$i]['timebegin'];
			$data[$i]['timeend']=$volist[$i]['timeend'];
			if($volist[$i]['circle'] == "1"){
				$data[$i]['circle'] = "每天";
				$data[$i]['week'] = "";
				$data[$i]['day'] = "";
				$data[$i]['hour'] = $volist[$i]['hour'];
				$data[$i]['minute'] = $volist[$i]['minute'];
			}elseif($volist[$i]['circle'] == "2"){
				$data[$i]['circle'] = "每星期";
				switch($volist[$i]['week']){
					case "Monday":
						$data[$i]['week'] = "周一";
					  	break;
					case "Tuesday":
						$data[$i]['week'] = "周二";
					  	break;
					case "Wednesday":
						$data[$i]['week'] = "周三";
					  	break;
					case "Thursday":
						$data[$i]['week'] = "周四";
					  	break;
					case "Friday":
						$data[$i]['week'] = "周五";
					  	break;
					case "Saturday":
						$data[$i]['week'] = "周六";
					  	break;
					case "Sunday":
						$data[$i]['week'] = "周日";
					  	break;
				}
				$data[$i]['day'] = "";
				$data[$i]['hour'] = $volist[$i]['hour'];
				$data[$i]['minute'] = $volist[$i]['minute'];
			}else{
				$data[$i]['circle'] = "每月";
				$data[$i]['week'] = "";
				$data[$i]['day'] = $volist[$i]['day'];
				$data[$i]['hour'] = $volist[$i]['hour'];
				$data[$i]['minute'] = $volist[$i]['minute'];
			}
			$data[$i]['aim']=$volist[$i]['aim'];
			$data[$i]['recorder']=$volist[$i]['recorder'];
			$data[$i]['attendee']=$volist[$i]['attendee'];
			$data[$i]['agenda']=$volist[$i]['agenda'];
			$data[$i]['materials']=$volist[$i]['materials'];
			$data[$i]['content']=$volist[$i]['content'];
			$data[$i]['releaser']=$volist[$i]['releaser'];
			$data[$i]['ctime']= date("Y-m-d H:i",$volist[$i]['create_time']);
			
		}
		
		$file="会议计划列表";
		$title="会议计划列表";
		$subtitle='会议计划列表';
		
		$th_array=array('所属项目','专项节点','会议分类','会议属性','会议级别','会议名称','计划开始时间','计划结束时间','计划周期',"星期",'天','小时','分钟','会议目标','记录人','参会人','议程','上会资料','会议内容',"发起人","发起时间");

		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}

	function createExel($file,$title,$subtitle,$array_th,$data,$excelname=""){
		Vendor ('Excel.PHPExcel');
		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_second.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		$objActSheet->setCellValue ( 'A1', $title );
		$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		$objActSheet->setCellValue ( 'F2', $subtitle);
		if($array_th==null){
			$array_th=array_keys($data[0]);
		}
		foreach($array_th as $key=>$value){
			$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
		}
		$baseRow = 5; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
		foreach ( $data as $r => $dataRow ){
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value){		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AQ'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
			}		 
		}
		$filename = $excelname."_".time();
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );
	}

	public function getexcel(){
		$ext=end(explode('.', $_FILES['file']['name']));
		$exts=array('xls','xlsx');
		$check=in_array($ext,$exts);
		if(empty($_FILES['file']) || (!$check)){
			$this->error('没有找到文件或请上传EXCEL文件(xls,xlsx).');
		}
		$filename=$_FILES['file']['name'];
		$savePath = '../Public/Uploads/meetplan/'; 
		if($filename!=null){
			$ext = strtolower(end(explode(".",basename($filename))));
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;	
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$file = $newname;
			$filerealname = $filename;
		}
		$filePath=$upload_file;

		Vendor('Excel.PHPExcel');
		$PHPExcel = new PHPExcel();

		//ok
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)){ 
				echo 'no Excel'; 
				return ; 
			} 
		} 
		$PHPExcel = $PHPReader->load($filePath); 
		/**读取excel文件中的第一个工作表*/ 
		$currentSheet = $PHPExcel->getSheet(0); 
		/**取得最大的列号*/ 
		$allColumn = $currentSheet->getHighestColumn(); 
		/**取得一共有多少行*/ 
		$allRow = $currentSheet->getHighestRow(); 
		
		/**从第二行开始输出，因为excel表中第一行为列名*/ 
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){ 
			/**从第A列开始输出*/ 
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				$val = (string)$currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/ 
				$data[$currentRow-2][$currentColumn]=$val;
			}
		}
		$count=count($data);

			for($k=0;$k<$count;$k++){
				$mapforProject["plm"] = $data[$k]['A'];
				$mapforProject["plmid"] = M("project")->where("title='".$data[$k]['A']."'")->getfield("id");
				$mapforProject["group"] = $data[$k]['B'];
				$mapforProject["type1"] = $data[$k]['C'];
				$mapforProject["type2"] = $data[$k]['D'];
				$mapforProject["type3"] = $data[$k]['E'];
				$mapforProject["title"] = $data[$k]['F'];
				$mapforProject["timebegin"] = $data[$k]['G'];
				$mapforProject["timeend"] = $data[$k]['H'];
				if($data[$k]['I'] == "每天"){
					$mapforProject["circle"] = "1";
				}elseif($data[$k]['I'] == "每星期"){
					$mapforProject["circle"] = "2";
				}else{
					$mapforProject["circle"] = "3";
				}
				switch($data[$k]['J']){
					case "周一":
						$mapforProject["week"] = "Monday";
					  	break;
					case "周二":
						$mapforProject["week"] = "Tuesday";
					  	break;
					case "周三":
						$mapforProject["week"] = "Wednesday";
					  	break;
					case "周四":
						$mapforProject["week"] = "Thursday";
					  	break;
					case "周五":
						$mapforProject["week"] = "Friday";
					  	break;
					case "周六":
						$mapforProject["week"] = "Saturday";
					  	break;
					case "周日":
						$mapforProject["week"] = "Sunday";
					  	break;
				}
				$mapforProject["day"] = $data[$k]['K'];
				$mapforProject["hour"] = $data[$k]['L'];
				$mapforProject["minute"] = $data[$k]['M'];
				$mapforProject["aim"] = $data[$k]['N'];
				$mapforProject["recorder"] = $data[$k]['O'];
				$mapforProject["attendee"] = $data[$k]['P'];
				$mapforProject["agenda"] = $data[$k]['Q'];
				$mapforProject["materials"] = $data[$k]['R'];
				$mapforProject["content"] = $data[$k]['S'];
				$mapforProject["releaser"] = $_SESSION['loginUserName'];
				$mapforProject["create_time"] = time();
				$x=M("meetingplan")->add($mapforProject);

				$info['plan_id'] = $x;
				$info["plm"] = $data[$k]['A'];
				$info["plmid"] = M("project")->where("title='".$data[$k]['A']."'")->getfield("id");
				$info["group"] = $data[$k]['B'];
				$info["type1"] = $data[$k]['C'];
				$info["type2"] = $data[$k]['D'];
				$info["type3"] = $data[$k]['E'];
				$info["title"] = $data[$k]['F'];
				$info["timebegin"] = $data[$k]['G'];
				$info["timeend"] = $data[$k]['H'];

				switch($data[$k]['J']){
					case "周一":
						$info["week"] = "Monday";
					  	break;
					case "周二":
						$info["week"] = "Tuesday";
					  	break;
					case "周三":
						$info["week"] = "Wednesday";
					  	break;
					case "周四":
						$info["week"] = "Thursday";
					  	break;
					case "周五":
						$info["week"] = "Friday";
					  	break;
					case "周六":
						$info["week"] = "Saturday";
					  	break;
					case "周日":
						$info["week"] = "Sunday";
					  	break;
				}
				$info["day"] = $data[$k]['K'];
				$info["hour"] = $data[$k]['L'];
				$info["minute"] = $data[$k]['M'];
				$info["aim"] = $data[$k]['N'];
				$info["recorder"] = $data[$k]['O'];
				$info["attendee"] = $data[$k]['P'];
				$info["agenda"] = $data[$k]['Q'];
				$info["materials"] = $data[$k]['R'];
				$info["content"] = $data[$k]['S'];
				$info["releaser"] = $_SESSION['loginUserName'];
				$info['create_time'] = time();
				//判断天 周 月
				if($data[$k]['I'] == "每天"){
					//每天
					$i = strtotime($data[$k]['G']);
					while($i <= strtotime($data[$k]['H'])){
						$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-d ".$data[$k]['L'].":".$data[$k]['M'],$i)));
						$i +=86400;
						$res = M("meetingassignment")->add($info);
					}
				}elseif($data[$k]['I'] == "每星期"){
					//每星期
					$i = strtotime($info['week'], strtotime($data[$k]['G']));
					while($i <= strtotime($data[$k]['H'])){
						$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-d ".$data[$k]['L'].":".$data[$k]['M'],$i)));
						$i += 86400*7;
						$res = M("meetingassignment")->add($info);
					}
				}else{
					//每月
					if(strtotime($data[$k]['G']) > strtotime(date("Y-m-".$data[$k]['K'],strtotime($data[$k]['G'])))){
						$i = strtotime("+1 month",strtotime(date("Y-m-".$data[$k]['K'],strtotime($data[$k]['G']))));
					}else{
						$i = strtotime(date("Y-m-".$data[$k]['K'],strtotime($data[$k]['G'])));
					}
					while($i <= strtotime($data[$k]['H'])){
						$info['timebegin'] = date("Y-m-d H:i",strtotime(date("Y-m-".$data[$k]['K']." ".$data[$k]['L'].":".$data[$k]['M'],$i)));
						$res = M("meetingassignment")->add($info);
						$i = strtotime("+1 month",strtotime(date("Y-m-".$data[$k]['K'],$i)));
					}
				}
			}

			$this->success('上传成功!');
	}
}
?>