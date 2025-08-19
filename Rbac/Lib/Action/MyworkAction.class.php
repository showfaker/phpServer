<?php
class MyworkAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
		
	}
	public function index1() {
		$this->display();
	}
	public function index() {
		//date('Y年m月d日 H:i',time())
		if($_REQUEST["plm"])
		{
			$map['plm'] = array('like',"%".$_REQUEST['plm']."%");
			$this->assign("plm",$_REQUEST['plm']);
		}

		if($_REQUEST['calendar']){
			$this->assign("calendar",$_REQUEST['calendar']);
		}else{
			$this->assign("calendar","1");
		}

		if($_REQUEST["year"]){
			$year=$_REQUEST["year"];
		}else{
			$year=date('Y',time());
		}
		if($_REQUEST["month"]){
			$month=$_REQUEST["month"];
		}else{
			$month=date('m',time());
		}
		if(strlen($month)==1){
			$month="0".$month;
		}
		$days = date('t', strtotime($year."-".$month."-"."01"));
		$weeknum=date("w",strtotime($year."-".$month."-"."01"));//如果是2，那么前面还有2个数据需要整合
		
		$long=$weeknum;
		$lastmonth=$month-1;
		if($lastmonth==0){
			$lastmonth=12;
			$yearoflastmonth=$year-1;
		}else{
			$yearoflastmonth=$year;
		}
		if(strlen($lastmonth)==1){
			$lastmonth="0".$lastmonth;
		}
		//上个月有几天
		$daysoflastmonth = date('t', strtotime($yearoflastmonth."-".$lastmonth."-"."01"));
		for($i=1;$i<=$long;$i++)
		{	
			//前面$i天是几号
			$data[0][$i-1]["d"]=$daysoflastmonth-$long+$i;
			if(strlen($data[0][$i-1]["d"])==1)
				$datatemp="0".$data[0][$i-1]["d"];
			else
				$datatemp=$data[0][$i-1]["d"];
			$data[0][$i-1]["date"]=$yearoflastmonth."-".$lastmonth."-".$datatemp;
			
		}
		
		$weeknumi=$weeknum;
		for($dayi=1;$dayi<=$days;$dayi++)//从第一周到第六周
		{   
			$data[($dayi+$weeknum-1)/7][$weeknumi]["d"]=$dayi;
			if(strlen($dayi)==1)
				$datatemp="0".$dayi;
			else
				$datatemp=$dayi;
			$data[($dayi+$weeknum-1)/7][$weeknumi]["date"]=$year."-".$month."-".$datatemp;
			$weeknumi++;
			if($weeknumi==7)
			{
				$weeknumi=0;
			}
		}
		
		$nextmonth=$month+1;
		if($nextmonth==13)
		{
			$nextmonth=1;
			$yearofnextmonth=$year+1;
		}
		else
		{
			$yearofnextmonth=$year;
		}
		if(strlen($nextmonth)==1)
		{
			$nextmonth="0".$nextmonth;
		}
		//最后一天是第几周
		$finalweek=floor(($days+$weeknum-1)/7);
		//最后一天是星期几
		$weeknum=$weeknumi-1;
		
		if($weeknum!=-1)//如果本月最后一天正好占满航，下个月一号轮到了周日，则不走了
		{
			
			$dayi=1;
			for($i=0;$i<=14;$i++)
			{
				$weeknum++;
				$data[$finalweek][$weeknum]["d"]=$dayi;
				if(strlen($dayi)==1)
					$datatemp="0".$dayi;
				else
					$datatemp=$dayi;
				$data[$finalweek][$weeknum]["date"]=$yearofnextmonth."-".$nextmonth."-".$datatemp;
				$dayi++;
				if($weeknum==6)
				{
					break;
				}
			}
			
		}
		$timebegin=$data[0][0]["date"];
		$timeend=$data[$finalweek][6]["date"];
		// $map['user_id|current'] = array('like','%'.$_SESSION['loginUserName'].'%');
		if($_REQUEST['calendar'] == 2){
			$map['timebegin']=array("like","%".$year."-".$month."%");
		}else{
			$map['timebegin']=array(array('egt',$timebegin),array('elt',$timeend),'and');
		}
		$map['current'] = array("exp","like '%".$_SESSION['loginUserName']."%' or `releaser` = '".$_SESSION['loginUserName']."' or `current` like '%".$_SESSION['dept']."%' or `current` = '公司'");
		$worklist=M("Workassignment")->where($map)->order("create_time asc")->select();
		// dump($worklist);die();
		$thisday=date("Y-m-d");
		foreach ($data as $key => $val){
			foreach ($val as $key1 => $val1){
				if($val1["date"]==$thisday){
					$data[$key][$key1][ifthisday]=1;
				}
				$key3=0;
				foreach ($worklist as $key2 => $val2){

					if(($val2["timebegin"]<=$val1["date"]) && ($val2["timeend"]>=$val1["date"])){
						$data[$key][$key1][work][$key3]=$val2;
						$key3++;
					}
				}
			}
		}
		$this->assign("data",$data);
		$this->assign("lastmonth",$lastmonth);
		$this->assign("yearoflastmonth",$yearoflastmonth);
		$this->assign("nextmonth",$nextmonth);
		$this->assign("yearofnextmonth",$yearofnextmonth);
		$this->assign("namenumber",$_SESSION[namenumber]);
		$this->assign("currentmonth",date('m',time()));
		$this->assign("currentyear",date('Y',time()));
		$this->assign("month",$month);
		$this->assign("year",$year);
		$this->assign("worklist",$worklist);
		if($_SESSION[app]){
			$this->display(indexapp);
		}else{
			$this->display(indexoa);
		}
		return;
	}
	
	protected function _list($model, $map, $sortBy = '', $asc = false) {

		if (isset($_REQUEST ['_order'])) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = !empty($sortBy) ? $sortBy : $model->getPk();
		}
	
		if (isset($_REQUEST ['_sort'])) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
	
		$count = $model->where($map)->count('id');
		if ($count > 0) {
			import("@.ORG.Util.Page");
			
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
			
			$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();

			foreach ($map as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}

			$page = $p->show();

			$sortImg = $sort; 
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; 
			$sort = $sort == 'desc' ? 1 : 0; 
			
			/*foreach ($voList as $key=>$value)
			{
				$voList[$key][timebegin]=$value[timebeginyear].'-'.$value[timebeginmonth].'-'.$value[timebeginday];
				$voList[$key][timeend]=$value[timeendyear].'-'.$value[timeendmonth].'-'.$value[timeendday];
			}*/
			foreach ($voList as $key=>$value)
			{
				if(0<substr_count($voList[$key]['current'],$_SESSION['loginUserName'].$_SESSION['number']))
				{
					$voList[$key]['ifcurrent']=1;
				}
				else
				{
					$voList[$key]['ifcurrent']=0;
				}
				$data.="
				{
				id:".$value[id].",
				title: '".$value[title]."',
				start: '".$value[timebegin]."',
				end: '".$value[timeend]."',
				allDay: false,
				url: 'index.php/Mywork/info/id/".$value[id]."',
				},";
				
			}
			//echo $data;
			$this->assign('list', $voList);
			$this->assign('sort', $sort);
			$this->assign('order', $order);
			$this->assign('sortImg', $sortImg);
			$this->assign('sortType', $sortAlt);
			$this->assign("page", $page);
			$this->assign('data', $data);
		}
		Cookie::set('_currentUrl_', __SELF__);
		return;
	}
	
	function info() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
		
		if(null==strpos('1'.$vo[current],$_SESSION['loginUserName'].$_SESSION['number']))
		{
			$vo["ifcurrent"]=0;
		}
		else
		{
			$vo["ifcurrent"]=1;
		}
		
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
	
    public function forevercommit() {
        $model = D("Workassignment");
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            $view=$_REQUEST ['view'];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $currentcommit=$_SESSION['loginUserName'].$_SESSION['number'].',';
                
                $data['current']=$model->where($condition)->getField("current");
                
                if(null==strpos('1'.$data[current],$_SESSION['loginUserName'].$_SESSION['number']))
                $this->error('您无权处理');
                
                $data['current']=str_replace($currentcommit,"",$data['current']);

                $data['user_id']=$model->where($condition)->getField("user_id");
                $data['user_id'].=$currentcommit;
                
                if(($data['current']==null)||($data['current']==""))
                {	
	                $data['commit_time'] = time();
	                $data['status'] = 0;
                }
                
                $data['process']=$model->where($condition)->getField("process");
                $data['process'].="\n".$_SESSION['loginUserName'].$_SESSION['number']."于".date('Y年m月d日H:i:s',time())."完成了该任务。";
                
                $data['view']= $model->where($condition)->getfield('view');
                if(""!=$view)
                	$data['view'] .=$_SESSION['loginUserName'].$_SESSION['number'].'完成任务意见:'.$view.';';
                if (false !== $model->where($condition)->save($data)) 
                {
					$mapschedule[taskid]=$model->where($condition)->getField("taskid");
					$mapschedule[user]=$_SESSION['loginUserName'].$_SESSION['number'];
					M("Schedule")->where($mapschedule)->setField("status",0);
                    $this->success('完成任务');
                }
                else
                {
                    $this->error('操作失败');
                }
            } else {
                $this->error('操作失败');
            }
        }
        $this->forward();
    }
    
    public function refuse() {
    	$name = $this->getActionName();
    	$model = D("Workassignment");
    	if (!empty($model)) {
    		$pk = $model->getPk();
    		$id = $_REQUEST [$pk];
    		$view=$_REQUEST ['view'];
    		if (isset($id)) {
    			$condition = array($pk => array('in', explode(',', $id)));
    			$data=$model->where($condition)->find();
    			$str = $data['user_id'];
    			$str1 = $data['current'];
    			$user = explode(",",$str1);
    			$len=count($user);
    			if($len==2)
    			{
	    			$this->error('拒绝任务失败,您是最后一个处理人');
    			}
    			$newdata['current']=str_replace($_SESSION['loginUserName'].$_SESSION['number'].',',"",$str1);
    			$newdata['user_id']=$str.$_SESSION['loginUserName'].$_SESSION['number'].',';
    			
    			
    			$newdata['process'] = $data['process'];
    			$newdata['process'].="\n".$_SESSION['loginUserName'].$_SESSION['number']."于".date('Y年m月d日H:i:s',time())."拒绝了该任务。";
    			$newdata['view']= $model->where($condition)->getfield('view');
    			if(""!=$view)
    				$newdata['view'] .=$_SESSION['loginUserName'].$_SESSION['number'].'拒绝任务意见:'.$view.';';
    			
    			if (false !== $model->where($condition)->save($newdata))
    			{
					$mapschedule[taskid]=$data[taskid];
					$mapschedule[user]=$_SESSION['loginUserName'].$_SESSION['number'];
					M("Schedule")->where($mapschedule)->setField("status",0);
    				$this->success('拒绝任务成功');
    			}
    			else
    			{
    				$this->error('拒绝任务失败了');
    			}
    		} else {
    			$this->error('拒绝任务失败');
    		}
    	}
    	$this->forward();
    }
    
    function edit() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	if(null!=strpos('1'.$vo[current],$_SESSION['loginUserName'].$_SESSION['number']))
    	{
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
    	else
    	{
    		$this->error('您无权处理');
    	}
    }
    function fill() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	if(null!=strpos('1'.$vo[current],$_SESSION['loginUserName'].$_SESSION['number']))
    	{
    		$this->assign('vo', $vo);
    		if($_SESSION[skin]!=3)
    		{
    			$this->display(fill);
    		}
    		else
    		{
    			$this->display();
    		}
    	}
    	else
    	{
    		$this->error('您无权处理');
    	}
    }
	function fillupdate() {
    	//B('FilterString');
    	$name = "Workassignment";
    	$model = D($name);
    	$map['id']= $_REQUEST[id];
    	$process = $model->where($map)->getfield('process');
    	$process .="\n由".$_SESSION['loginUserName'].$_SESSION['number']."于".date('Y年m月d日H:i:s',time())."填写任务纪要：".$_REQUEST[process].",";
    	$model-> where($map)->setField("process",$process);

    	$this->success('任务纪要填写成功');
    }
    function commit() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
		$this->assign('vo', $vo);
		if(null==strpos('1'.$vo[current],$_SESSION['loginUserName'].$_SESSION['number']))
			$this->error('您无权处理');
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(commitoa);
    	}
    	else
    	{
    		$this->display();
    	}
    }
    
    function refusework() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	
    	if(null==strpos('1'.$vo[current],$_SESSION['loginUserName'].$_SESSION['number']))
    		$this->error('您无权处理');
    	
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(refuseworkoa);
    	}
    	else
    	{
    		$this->display();
    	}
    }
    
    function update() {
    	//B('FilterString');
    	$name = "Workassignment";
    	$model = D($name);
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}

    	$map['id']= $model->id;
		$taskid= $model->taskid;
    	
    	$data['content']="您收到一条任务委派：".$model->title."，请及时处理。";
    	//$data['user']=$model->user_id;
    	$data['href'] ="index.php?s=Mywork/index";
    	$scheduleuser=explode(',',$model->current);
    	
    	$newuser['user_id'] = $model->where($map)->getfield('user_id');
    	//$newuser['user_id'] .=($model->user_id);
    	$newuser['user_id'] .=$_SESSION['loginUserName'].$_SESSION['number'].',';
    	$newuser['view']= $model->where($map)->getfield('view');
    	if(""!=$model->view)
    		$newuser['view'] .=$_SESSION['loginUserName'].$_SESSION['number'].'委派任务意见:'.($model->view).';';
    	$str = $model->where($map)->getfield('current');
    	$current['current']=str_replace($_SESSION['loginUserName'].$_SESSION['number'].',',"",$str);
    	$current['current'].=($model->current);
    	$process['process'] = $model->where($map)->getfield('process');
    	$process['process'].="\n由".$_SESSION['loginUserName'].$_SESSION['number']."于".date('Y年m月d日H:i:s',time())."委派任务至".$model->current;
    	$list=$model-> where($map)->setField($newuser);
    	$list=$model-> where($map)->setField($current);
    	$list=$model-> where($map)->setField($process);
    	
    	//$list = $model->save();
    	if (false !== $list) {
    		/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		foreach ($scheduleuser as $key=>$value)
    		{
    			if($value!=null)
    			{
    				$data['user']=$value;
					$data['taskid']=$taskid;
    				$this->Addschedule($data);
    			}
    		}
			$mapschedule[taskid]=$taskid;
			$mapschedule[user]=$_SESSION['loginUserName'].$_SESSION['number'];
			M("Schedule")->where($mapschedule)->setField("status",0);
			
    		$this->success('委派任务成功');
    	} else {
    		$this->error('委派任务失败');
    	}
    }
    function insert() {
    	//B('FilterString');
    	$name = "Workassignment";
    	$model = D($name);
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	//保存当前数据对象
    	$user = D("User");
    	$userdata=$user->select();
    	$count = count($userdata);
    	for($i=0;$i<$count;$i++)
    	{
    	if($model->user_id ==$userdata[$i][nickname].$userdata[$i][number])
    		break;
    		if($i==$count-1)
    		{
    		$this->error('查无此员工!');
    			return;
    		}
    	}
        	$model->user_id.=',';
        	$model->current =$userdata[$i][nickname].$userdata[$i][number];
        	$list = $model->add();
        	if ($list !== false) { //保存成功
        	/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
        			$this->success('新增成功!');
        	} else {
        	//失败提示
        			$this->error('新增失败!');
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
                if (false !== $model->where($condition)->delete()){
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