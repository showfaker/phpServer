<?php
class Sgjh1Action extends CommonAction {
	
	//过滤查询字段
	function _filter(&$map){
		if($_POST['address'])
		{
			$map['title'] = array('like',"%".$_POST['address']."%");
			$this->assign("address",$_POST['address']);
		}
		if($_POST['keyword'])
		{
			$map['title'] = array('like',"%".$_POST['keyword']."%");
			$this->assign("keyword",$_POST['keyword']);
		}
	}
	
	public function index() {
		
		
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		
		
        if(!empty($_REQUEST['tab']))
		{
			$_SESSION[tab]=$_REQUEST['tab'];
		}
		else
		{
			$_SESSION[tab]=1;
		}
		$this->assign('tab',$_SESSION['tab']);		
		if($_REQUEST['plmgroup'])
		{
			$mapforSecondgroup["name"]=array('like',"%".$_REQUEST['plmgroup']."%");
			$plmgrouparray=M("Secondgroup")->where($mapforSecondgroup)->field("id")->select();
			foreach($plmgrouparray as $key => $val)
			{
				$plmgroupids.=$val["id"].",";
			}
			$plmgroupids= substr($plmgroupids,0,strlen($plmgroupids)-1);
			$map['groupid'] = array('in',$plmgroupids);
			$this->assign('plmgroup', $_REQUEST['plmgroup']);
		}
		
		//变更历史记录
		if($_SESSION['tab']=="4")
		{
			
			if($_POST['keyword'])
			{
				$mapforproject['title'] = array('like',"%".$_POST['keyword']."%");
				$this->assign("keyword",$_POST['keyword']);
				$projects=M("Project")->where($mapforproject)->field("id")->select();
				foreach($projects as $key => $val)
				{
					$plmids.=$val[id];
				}
				$mapforPlmschedulechange[plmid]=array("in",$plmids);
			}
		
			$mapforPlmschedulechange[change_time]=array("neq","");
			$sgjh=M("Plmschedule")->where($mapforPlmschedulechange)->group("plmid")->select();
			foreach($sgjh as $key => $val)
			{
				$sgjh[$key]['plminfo']=M("Project")->where("id=".$val[plmid])->find();
			}
			$this->assign('list', $sgjh);
			$this->display();	
			return;
		}
		
		
		
		if(!empty($_REQUEST['type']))
		{
			$map['approvestatus'] = $_REQUEST['type'];
			$this->assign('type',$_REQUEST['type']);	
		}
		if(!empty($_REQUEST['way'])){
			$map['way'] = $_REQUEST['way'];
			$this->assign('way',$_REQUEST['way']);	
		}
		if(!empty($_REQUEST['waysub'])){
			$map['waysub'] = $_REQUEST['waysub'];
			$this->assign('waysub',$_REQUEST['waysub']);	
		}
		if(!empty($_REQUEST['activity']))
		{
			$map['activity'] = array("like","%".$_REQUEST['activity']."%");
			$this->assign('activity',$_REQUEST['activity']);	
		}
		/*
		if(!empty($_REQUEST['clientstatus']))
		{
			$map['clientstatus'] = $_REQUEST['clientstatus'];
			$this->assign('clientstatus',$_REQUEST['clientstatus']);	
		}
		else
		{
			$map['clientstatus'] = array("neq","死单客户");
		}
		*/
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		//$map[design_status]=array("in","可研评审报告审批通过,合同审核完成,设计审核通过,施工计划待审核,施工计划审核退回,施工计划审核通过,待施工,施工中,完成施工,暂停中");//新加的 可研评审报告审批通过
		$map['projecttype'] = array("eq","承揽项目");
		
		$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities();
		
		$this->display();
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
            $p = new Page($count, $listRows);
            //分页查询数据
			if($_SESSION['curpage']!=null)
			{
				$p->nowPage=$_SESSION['curpage'];		
				$p->firstRow=($_SESSION['curpage']-1)*($p->listRows);
				unset($_SESSION['curpage']);
			}
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			foreach($voList as $key => $val)
			{
				$voList[$key]['drawings']=explode(',',$val['drawing']);
				$voList[$key]['drawingsfilename']=explode(',',$val['drawingfilename']);
				
				$voList[$key]['illustrations']=explode(',',$val['illustration']);
				$voList[$key]['clientillustrations']=explode(',',$val['clientillustration']);
				
				$voList[$key]['budgets']=explode(',',$val['budget']);
				$voList[$key]['budgetsfilename']=explode(',',$val['budgetfilename']);
				
				$voList[$key]['worktype']=M("Plmworktype")->where("plmid=".$val[id])->order("id asc")->select();
				
				
				$mapforPlmschedule[plmid]=$val[id];
				$mapforPlmschedule[status]=1;
				//$schedules=M("Plmschedule")->where($mapforPlmschedule)->select();
				//$this->assign('schedules', $schedules);
				$schedulesuser=M("Plmschedule")->where($mapforPlmschedule)->group("user")->field("user")->select();
				foreach($schedulesuser as $key1 => $val1)
				{
					$voList[$key]['alluser'].=$val1[user].",";
				}
				
				$voList[$key]['alluser']= substr($voList[$key]['alluser'], 0, -1);
				
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
            $this->assign("totalCount", $p->totalRows);
            $this->assign("numPerPage", $p->listRows);
            $this->assign("currentPage", $p->nowPage);
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('countnumber', $count);
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
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
			
			$mapforPlmschedulex[subworktype]=$val['title'];
			$mapforPlmschedulex[plmid]=$val['plmid'];
			$mapforPlmschedulex[status]=1;
			$vo['worktype'][$key][schedule]=M("Plmschedule")->where($mapforPlmschedulex)->find();
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		
		$this->assign('type', $_REQUEST[type]);
		$this->assign('change', $_REQUEST[change]);
		$this->assign('flag', $_REQUEST[flag]);
		$this->display("add");
	}	
	function add_auto() {
		$plminfo=M("Project")->where("id=".htmlspecialchars($_REQUEST[id]))->find();
		$mapforPlmschedule[plmid]=htmlspecialchars($_REQUEST[id]);
		$schedules=M("Plmworktype")->where($mapforPlmschedule)->order("id asc")->select();//sort asc
		$date=htmlspecialchars($_REQUEST["date"]);
		foreach($schedules as $key => $val)
		{
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			if($plminfo["invest6"]<=200)
			{
				$length=$worktypeinfo["period1"];
			}
			else
			{
				$length=$worktypeinfo["period2"];
			}
			$schedules[$key]["timebegin"]=$date;
			//$date=date("Y-m-d",strtotime($date)+($length-1)*24*60*60);
			$date=$this->getendday($date,($length-1));
			$schedules[$key]["timeend"]=$date;
			//$date=date("Y-m-d",strtotime($date)+1*24*60*60);
			$date=$this->getendday($date,1);
		}
		
		
		$bjsz=M("Bjsz")->find();
		if($plminfo["projecttype"]=="充电建设")
		{
			$time0=$bjsz["subtitle100"];
			$time1=$bjsz["subtitle101"];
			$time2=$bjsz["subtitle102"];
		}
		if($plminfo["projecttype"]=="换电建设")
		{
			$time0=$bjsz["subtitle110"];
			$time1=$bjsz["subtitle111"];
			$time2=$bjsz["subtitle112"];
		}
		if($plminfo["projecttype"]=="低速车建设")
		{
			$time0=$bjsz["subtitle120"];
			$time1=$bjsz["subtitle121"];
			$time2=$bjsz["subtitle122"];
		}
		
		$date0=$date;
		//$date1=date("Y-m-d",strtotime($date0)+($time0-1)*24*60*60);
		//$date2=date("Y-m-d",strtotime($date1)+($time1)*24*60*60);
		//$date3=date("Y-m-d",strtotime($date2)+($time2)*24*60*60);
		
		$date1=$this->getendday($date0,($time0-1));
		$date2=$this->getendday($date1,($time1));
		$date3=$this->getendday($date2,($time2));
		
		$predate["100"]=$date1;
		$predate["101"]=$date2;
		$predate["102"]=$date3;
		
		$data["schedules"]=$schedules;
		$data["predate"]=$predate;
		
		echo json_encode($data);
	}
	function insert() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$handlehistory.=$_SESSION['loginUserName']."于".$date."设置施工计划</br>------------------</br>"; 
		$model->plan_time=time();
		$model->handlehistory=$handlehistory;
		$model->jhuser=$_SESSION['loginUserName'];
		
		
		
		
		//施工计划待审核
		if(empty($_REQUEST[change]))
		{
			//$model->design_status="施工计划待审核";
			//$model->design_status="施工计划审核通过";
			$model->predate100=$_REQUEST['predate100'];
			$model->predate101=$_REQUEST['predate101'];
			$model->predate102=$_REQUEST['predate102'];
			$model->design_status="待施工";
		}
		else
		{
			$model->plan_status="施工计划变更待审核";
			
			$model->predate100=$info['predate100'];
			$model->predate101=$info['predate101'];
			$model->predate102=$info['predate102'];
			
			
			$model->predate100temp=$_REQUEST['predate100'];
			$model->predate101temp=$_REQUEST['predate101'];
			$model->predate102temp=$_REQUEST['predate102'];
		
		}
		$list = $model->save();
		$time=time();
		
		
		
		
		
		
		$plantimebegin=$_REQUEST[plantimebegin];
		$plantimeend=$_REQUEST[plantimeend];
		
		$worktype=$_REQUEST[worktype];
		foreach($plantimeend as $key => $val)
		{
			
			
			if((($val>$info[dateend])&&(!empty($info[dateend])))&&($worktype[$key]!="甲方验收")&&($worktype[$key]!="装修保证金退还"))
			{
				$this->error("计划完成时间不能晚于项目开业计划结束时间：".$info[dateend]);
			}
			
			
			if(empty($val)||empty($plantimebegin[$key]))
			{
				$this->error("请填写完整的施工计划");
			}
			
		}
		
		
		
		
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		$scheduleexist=M("Schedule")->where($schedulemap)->getField("id");
		
		
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		if($_REQUEST[change])
		{
			//上面挪到下面2021-08-03
			if(empty($scheduleexist))
			{	
				$data['content']=$_SESSION['loginUserName']."于".$date."变更了《".$address."》施工计划，请您审核。";
				$data['href'] ="index.php?s=Sgjh/index";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$userschedule=$this->findleader($info['projecttype'],$info['city']);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			//施工计划变更
			/*
			$mapforPlmschedule[status]=1;
			$scheduledata=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			$pworktype=$_REQUEST[pworktype];
			$worktype=$_REQUEST[worktype];
			$plantimebegin=$_REQUEST[plantimebegin];
			$plantimeend=$_REQUEST[plantimeend];
			foreach($scheduledata as $key => $val)
			{
				$data[id]=$val[id];
				$data[plantimebegintmp]=$plantimebegin[$key];
				$data[plantimeendtmp]=$plantimeend[$key];
				$data[reason]=$_REQUEST[reason];
				M("Plmschedule")->save($data);
			}
			*/
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			M("Plmscheduletemp")->where($mapforPlmschedule)->delete();
			
			$data[plmid]=$_REQUEST[id];
			$data[user]=$_SESSION[name];
			$data[create_time]=$time;
			$data[status]=1;
			$pworktype=$_REQUEST[pworktype];
			$worktype=$_REQUEST[worktype];
			$plantimebegin=$_REQUEST[plantimebegin];
			$plantimeend=$_REQUEST[plantimeend];
			foreach($pworktype as $key => $val)
			{
				$data[worktype]=$val;
				$data[subworktype]=$worktype[$key];
				$data[plantimebegintmp]=$plantimebegin[$key];
				$data[plantimeendtmp]=$plantimeend[$key];
				$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegintmp],$data[plantimeendtmp]);
				$data["sort"]=$key+1;
				$timeform[$val]+=$data[plantimelength];
				
				
				//判断是否有改变
				$mapforold[worktype]=$val;
				$mapforold[subworktype]=$worktype[$key];
				$mapforold[plmid]=$_REQUEST[id];
				$mapforold[status]=1;
				$old=M("Plmschedule")->where($mapforold)->find();
				//if(($old[plantimebegin]!=$data[plantimebegin])||($old[plantimeend]!=$data[plantimeend]))
				//{
					$data[plantimebegin]=$old[plantimebegin];
					$data[plantimeend]=$old[plantimeend];
				//}
				$data[reason]=$_REQUEST[reason];
				M("Plmscheduletemp")->add($data);
			}
			
			$mapforschedule[plmid]=$_REQUEST[id];
			$mapforschedule[status]=1;
			$scheduledata=M("Plmscheduletemp")->where($mapforschedule)->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmscheduletemp")->where("id=".$val[id])->setField("weight",$weight);
			}
			
		}
		else
		{
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			M("Plmschedule")->where($mapforPlmschedule)->delete();
			
			//plmschedule
			$data[plmid]=$_REQUEST[id];
			$data[user]=$_SESSION[name];
			$data[user]=$_SESSION[name];
			$data[create_time]=$time;
			$data[status]=1;
			$pworktype=$_REQUEST[pworktype];
			$worktype=$_REQUEST[worktype];
			$plantimebegin=$_REQUEST[plantimebegin];
			$plantimeend=$_REQUEST[plantimeend];
			foreach($pworktype as $key => $val)
			{
				$data[worktype]=$val;
				$data[subworktype]=$worktype[$key];
				$data[plantimebegin]=$plantimebegin[$key];
				$data[plantimeend]=$plantimeend[$key];
				$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegin],$data[plantimeend]);
				$data["sort"]=$key+1;
				$timeform[$val]+=$data[plantimelength];
				M("Plmschedule")->add($data);
			}
			
			$mapforschedule[plmid]=$_REQUEST[id];
			$mapforschedule[status]=1;
			$scheduledata=M("Plmschedule")->where($mapforschedule)->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmschedule")->where("id=".$val[id])->setField("weight",$weight);
			}
		
		}
		
		
		if($_SESSION[app])
		{
			$this->redirect('../App/jhsb');
		}
		else
		{
			$this->redirect('index');
		}
	}
	
	
	
	function add1() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		
		$this->display("add1");
	}	
	//施工计划审核
	function insert1() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="通过"))/*同意*/
		{
			$model->handlehistory=$info['handlehistory']."施工计划审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			//$model->design_status="施工计划审核通过";
			$model->design_status="待施工";//施工中
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行施工计划审核，结果：同意。";
			$data['receiver']=$info['jhuser'].$this->findNumberByNameAndRole($info['jhuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行施工计划审核，结果：同意。";
			$this->Sendmail($data);
		}
		else
		{	//拒绝流程
			$model->handlehistory=$info['handlehistory']."施工计划审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
			$model->design_status="施工计划审核退回";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行施工计划审核，结果：拒绝。";
			$data['receiver']=$info['jhuser'].$this->findNumberByNameAndRole($info['jhuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行施工计划审核，结果：拒绝。";
			$this->Sendmail($data);
		}
		$model->plan_approveuser=$_SESSION["name"];
		$model->plan_approve_time=time();
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist5'));
			}
			else
			{
				$this->redirect('index');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	function add11() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$schedules=M("Plmscheduletemp")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		
		$this->display("add11");
	}	
	//施工计划变更审核
	function insert11() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		M("Schedule")->where($schedulemap)->setField("status",0);
		$date=date("Y-m-d");
		if(($_REQUEST[result]=="通过"))/*同意*/
		{
			/*
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			//施工计划变更
			$mapforPlmschedule[status]=1;
			$scheduledata=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			foreach($scheduledata as $key => $val)
			{
				$data[id]=$val[id];
				$data[plantimebegin]=$val[plantimebegintmp];
				$data[plantimeend]=$val[plantimeendtmp];
				$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegin],$data[plantimeend]);
				$timeform[$val[worktype]]+=$data[plantimelength];
				$data[change_time]=time();
				
				$data[plantimebeginold]=$val[plantimebegin];
				$data[plantimeendold]=$val[plantimeend];
				
				M("Plmschedule")->save($data);
			}
			$scheduledata=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmschedule")->where("id=".$val[id])->setField("weight",$weight);
			}
			*/
			
			M("Project")->where("id='" . $model->id . "'")->setField("predate100",$info["predate100temp"]);
			M("Project")->where("id='" . $model->id . "'")->setField("predate101",$info["predate101temp"]);
			M("Project")->where("id='" . $model->id . "'")->setField("predate102",$info["predate102temp"]);
			
			M("Project")->where("id='" . $model->id . "'")->setField("predate100temp",$info["predate100"]);
			M("Project")->where("id='" . $model->id . "'")->setField("predate101temp",$info["predate101"]);
			M("Project")->where("id='" . $model->id . "'")->setField("predate102temp",$info["predate102"]);
			
			
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			$scheduledata=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			foreach($scheduledata as $key => $val)
			{
				$dataold[id]=$val[id];
				$dataold[status]=0;
				M("Plmschedule")->save($dataold);
			}
			
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			//施工计划变更
			$mapforPlmschedule[status]=1;
			$scheduledata=M("Plmscheduletemp")->where($mapforPlmschedule)->order("sort asc")->select();
			foreach($scheduledata as $key => $val)
			{
				
				$mapforold[worktype]=$val[worktype];
				$mapforold[subworktype]=$val[subworktype];
				$mapforold[plmid]=$val[plmid];
				$old=M("Plmschedule")->where($mapforold)->find();
				if(!empty($old))
				{
					$old["sort"]=$val["sort"];
					$old[plantimebegin]=$val[plantimebegintmp];
					$old[plantimeend]=$val[plantimeendtmp];
					$old[plantimelength]=$val[plantimelength];
					$old[change_time]=$val[create_time];
					$old[plantimebeginold]=$val[plantimebegin];
					$old[plantimeendold]=$val[plantimeend];
					$old[status]=$val[status];
					$old[reason]=$val[reason];
					$old[weight]=$val[weight];
					$old[user]=$val[user];
					M("Plmschedule")->save($old);
				}
				else
				{
					$data[plantimebegin]=$val[plantimebegintmp];
					$data[plantimeend]=$val[plantimeendtmp];
					$data[plantimelength]=$val[plantimelength];
					$data[change_time]=$val[create_time];
					
					$data[plantimebeginold]=$val[plantimebegin];
					$data[plantimeendold]=$val[plantimeend];
					
					$data[plmid]=$val[plmid];
					$data[create_time]=$val[create_time];
					$data[status]=$val[status];
					$data[worktype]=$val[worktype];
					$data[subworktype]=$val[subworktype];
					$data["sort"]=$val["sort"];
					$data[reason]=$val[reason];
					$data[weight]=$val[weight];
					$data[user]=$val[user];
					M("Plmschedule")->add($data);
				}
			}
			/*
			$scheduledata=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmschedule")->where("id=".$val[id])->setField("weight",$weight);
			}
			*/
			$model->handlehistory=$info['handlehistory']."施工计划变更审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$model->plan_status="施工计划变更审核通过";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行施工计划变更审核，结果：同意。";
			$data['receiver']=$info['sguser'].$this->findNumberByNameAndRole($info['sguser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行施工计划变更审核，结果：同意。";
			$this->Sendmail($data);
		}
		else
		{	//拒绝流程
			$model->handlehistory=$info['handlehistory']."施工计划变更审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
			$model->plan_status="施工计划变更审核退回";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行施工计划变更审核，结果：拒绝。";
			$data['receiver']=$info['sguser'].$this->findNumberByNameAndRole($info['sguser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行施工计划变更审核，结果：拒绝。";
			$this->Sendmail($data);
		}
		$model->plan_approveuser=$_SESSION["name"];
		$model->plan_approve_time=time();
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist5'));
			}
			else
			{
				$this->redirect('index');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function add2() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$vo['worktype']=M("Plmworktype")->where("plmid=".$vo[id])->order("id asc")->select();
		$this->assign('orgdata', $vo);
		
	
		$mapforWorktype[type]=1;
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		foreach ($worktypes as $key => $val) {
			$worktypes[$key][subworktypes]=M("Worktype")->where("pid=".$val[id])->order("sort asc")->select();
		}
		$this->assign('worktypes', $worktypes);
		$this->display("add2");
	}
	
	function insert2() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$handlehistory.=$_SESSION['loginUserName']."于".$date."设置专项节点</br>------------------</br>"; 
		$model->handlehistory=$handlehistory;
		
		M("Plmworktype")->where("plmid=".$_REQUEST["id"])->delete();
		
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		$scheduleexist=M("Schedule")->where($schedulemap)->getField("id");
		
		if(empty($scheduleexist))
		{	
			$data['content']=$_SESSION['loginUserName']."于".$date."变更了《".$address."》施工计划，请您审核。";
			$data['href'] ="index.php?s=Sgjh/index";
			$data['taskid'] =$info[id];
			$data['type'] ="Sgjh";
			$userschedule=$this->findleader($info['projecttype'],$info['city']);
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
		}
		
		
		$dataplmworktype["plmid"]=$_REQUEST["id"];
		$postdata=$_POST;
		foreach($postdata as $key => $val)
		{
			if($key!="id")
			{
				$dataplmworktype["title"]=$val;
				$worktypedetail=M("Worktype")->where("id=".$key)->find();
				$dataplmworktype["sort"]=$worktypedetail["sort"];
				$dataplmworktype["pid"]=$worktypedetail["pid"];
				$dataplmworktype["type"]=$worktypedetail["type"];
				$dataplmworktype["parallel"]=$worktypedetail["parallel"];
				$dataplmworktype["user_id"]=$_SESSION["number"];
				$dataplmworktype["create_time"]=time();
				$dataplmworktype["pworktype"]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
				M("Plmworktype")->add($dataplmworktype);
			}
			
		}
		
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			//$this->success('新增成功!');
			$this->redirect('index');
			//dump($_POST);
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function detail2() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$mapforPlmworktype[plmid]=$vo[id];
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		//dump($schedules);
		$this->assign('schedules', $schedules);
		/*
		$vo['worktype']=M("Plmworktype")->where("plmid=".$vo[id])->order("id asc")->select();
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedulex[id]=$_REQUEST[id];
		$schedule=M("Plmschedule")->where($mapforPlmschedulex)->find();
		
		$mapforPlmschedule[create_time]=$schedule[create_time];
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		
		
		foreach($schedules as $key => $val)
		{
			if($schedules[$key][pworktype]!=$schedules[$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$schedules[$key][pworktype];
				$schedules[$key][block]=1;
				$schedules[$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
		}
		
		$mapforproject[id]=$schedule[plmid];
		$projectinfo=M("Project")->where($mapforproject)->find();
		$this->assign('orgdata', $projectinfo);
		*/
		$this->display("detail2");
	}	
	
	
	
}
?>