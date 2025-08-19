<?php
class PlmworkassignmentAction extends CommonAction {
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
		//$map['releaser']=$_SESSION['loginUserName'].$_SESSION['number'];
		if($_REQUEST[searchtype]==1)
		{
			$map[timeend]=array('egt',date('Y-n-j',time()));
		}
		if($_REQUEST[searchtype]==2)
		{
			$map[timeend]=array('lt',date('Y-n-j',time()));
		}
		
		if($_REQUEST['address'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		if($_REQUEST['plmgroup'])
		{
			$mapforSecondgroup["name"]=array('like',"%".$_REQUEST['plmgroup']."%");
			$plmgrouparray=M("Secondgroup")->where($mapforSecondgroup)->field("id")->select();
			foreach($plmgrouparray as $key => $val)
			{
				$plmgroupids.=$val["id"].",";
			}
			$plmgroupids= substr($plmgroupids,0,strlen($plmgroupids)-1);
			$mapforProject['groupid'] = array('in',$plmgroupids);
			$plmarray=M("Project")->where($mapforProject)->field("id")->select();
			foreach($plmarray as $key => $val)
			{
				$plmids.=$val["id"].",";
			}
			$plmpids= substr($plmids,0,strlen($plmids)-1);
			$mapforProject['plmid'] = array('in',$plmpids);//验证
			
			$this->assign('plmgroup', $_REQUEST['plmgroup']);
		}
		
		
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign'])))
		{
			$map['create_time'] = array('gt',strtotime($_REQUEST['timebeginassign']));
		}
		else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign'])))
		{
			$map['create_time'] = array('lt',strtotime($_REQUEST['timeendassign']));
		}
		else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign'])))
		{
			$map['create_time'] = array(array('gt',strtotime($_REQUEST['timebeginassign'])),array('lt',strtotime($_REQUEST['timeendassign'])),'and');
		}
		$this->assign("timebeginassign",$_REQUEST['timebeginassign']);
		$this->assign("timeendassign",$_REQUEST['timeendassign']);
		//dump($map);
		$name = $this->getActionName();
		$model = D($name);
		$map[releaser]=$_SESSION['loginUserName'].$_SESSION['number'];
		$voList = $model->select();
		foreach ($voList as $key=>$value)
		{
			//if($voList[$key]['timeend']<date('Y-n-j',time()))
			if(($voList[$key]['timeend']<date('Y-m-d',time()))&&($value[status]!=0))//&($value[status]==0)//如果已经完成了，就已经超期了
			{
				$voList[$key]['status']=2;
				$condition['id'] = $voList[$key]['id'];
				$model->where($condition)->save($voList[$key]);
			}
		}
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		if($_SESSION[app])
		{
			$this->display(indexapp);
		}
		else
		{
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
        $name = $this->getActionName();
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3)
        {
        	$this->display(editoa);
        }
        else
        {
        	$this->display();
        }
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
    	}
    	else
    	{
    		$this->display();
    	}
    }
    
    function insert() {
    	//B('FilterString');
    	$name = $this->getActionName();
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
    	//B('FilterString');
    	$name = $this->getActionName();
    	$model = D($name);
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}


    	$date=date('Y-m-d H:i');
		$emaildata['content'] =$_SESSION['loginUserName']."于".$date."对项目任务《".$model->title."》进行任务更新";
		$emaildata['receiver']=$_REQUEST[current].$_REQUEST[check];
		$emaildata['status']=1;
		$emaildata['sender']="系统通知";
		$emaildata['create_time']=time();
		$emaildata['title'] =$_SESSION['loginUserName']."于".$date."对项目任务《".$model->title."》进行任务更新，您是查看人，请关注。";
		$this->Sendmail($emaildata);

		$mapschedule[taskid]= $model->taskid;
		
    	$data['content']="任务更新:".$model->title;
    	$data['href'] ="index.php?s=Mywork/index";
		$data['create_time']=time();
    	$scheduleuser=explode(',',$model->current);
    	
 		$map[id]=$model->id;
 		$model->process =D($name)->where($map)->getField("process");
    	$model->process.="\n".$_SESSION['loginUserName'].$_SESSION['number']."于".date('Y年m月d日H:i:s',time())."更新了该任务,";
    	$list = $model->save();
    	if (false !== $list) {
    		//成功提示
    		/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		
    		foreach ($scheduleuser as $key=>$value)
    		{
    			if($value!=null)
    			{
    				$data['user']=$value;
					$data['status']=1;
					$mapschedule['user']= $value;
					$data['id']=M("Schedule")->where($mapschedule)->getField("id");
					$ret=M("Schedule")->save($data);
    			}
    		}
    		
    		$this->success('任务更新成功!');
    	} else {
    		//错误提示
    		$this->error('任务更新失败!');
    	}
    }
	
	    public function foreverdelete() {
        //删除指定记录
        $name = $this->getActionName();
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