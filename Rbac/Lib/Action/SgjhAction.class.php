<?php
class SgjhAction extends CommonAction {
	
	//过滤查询字段
	function _filter(&$map){
		
		//$map['projecttype'] = array("neq","承揽项目");
		//$map['step3'] = array("eq","1");
		//$map['step6'] = array("egt","0.2");
		if($_REQUEST['title'])
		{
			$map['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['number'])
		{
			$map['number'] = array('like',"%".$_REQUEST['number']."%");
			$this->assign("number",$_REQUEST['number']);
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
			$map['groupid'] = array('in',$plmgroupids);
			$this->assign('plmgroup', $_REQUEST['plmgroup']);
		}
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
	}
	
	public function index() {
		
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		/*
		$allprojects=M("Project")->select();
		foreach($allprojects as $key => $val)
		{
			for($i=1;$i<=5;$i++)
			{
				if($i=="1")$classify="主项";
				if($i=="2")$classify="开发";
				if($i=="3")$classify="设计";
				if($i=="4")$classify="采购";
				if($i=="5")$classify="施工";
				
				$pos=strrpos($val["handlehistory"],"设置".$classify."计划");
				if($pos)
				{
					$jhuser="jhuser".$i;
					$plan_time="plan_time".$i;
					$str=substr($val["handlehistory"],$pos-31, 9);
					M("Project")->where("id=".$val["id"])->setField($jhuser,str_replace("br>","",$str));
					$str=substr($val["handlehistory"],$pos-19, 10);
					M("Project")->where("id=".$val["id"])->setField($plan_time,$str);
				}
				
				$pos=strrpos($val["handlehistory"],$classify."计划审核");
				if($pos)
				{
					$plan_approveuser="plan_approveuser".$i;
					$plan_approve_time="plan_approve_time".$i;
					$str=substr($val["handlehistory"],$pos+21, 9);
					M("Project")->where("id=".$val["id"])->setField($plan_approveuser,str_replace("</b","",$str));
				}
				
				$pos=strrpos($val["handlehistory"],$classify."计划变更审核");
				if($pos)
				{
					$planchange_approveuser="planchange_approveuser".$i;
					$planchange_approve_time="planchange_approve_time".$i;
					$str=substr($val["handlehistory"],$pos+27, 9);
					M("Project")->where("id=".$val["id"])->setField($planchange_approveuser,str_replace("</b","",$str));
				}
			}
		}
		*/
		
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
			
			if(($_POST['title'])||($_POST['number']))
			{
				if($_POST['title'])
				{
					$mapforproject['title'] = array('like',"%".$_POST['title']."%");
					$this->assign("title",$_POST['title']);
				}
				if($_POST['number'])
				{
					$mapforproject['number'] = array('like',"%".$_POST['number']."%");
					$this->assign("number",$_POST['number']);
				}
				$projects=M("Project")->where($mapforproject)->field("id")->select();
				foreach($projects as $key => $val)
				{
					$plmids.=$val[id].",";
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
			if($_SESSION["app"]=="1")
			{
				$this->display("indexapp");
			}
			else
			{
				$this->display();	
			}
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
		if(!empty($_REQUEST['moduletitle']))
		{
			$this->assign('moduletitle',$_REQUEST['moduletitle']);	
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
		//$map[design_status]=array("in","可研评审报告审批通过,合同审核完成,设计审核通过,施工计划待审核,施工计划审核退回,施工计划审核通过,待施工,施工中,完成施工");//新加的 可研评审报告审批通过
		$map[design_status]=array("not in","取消,暂停中,暂存");
		$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		$changepower=M("User")->where("id=".$_SESSION["id"])->getField("main");
		$this->assign('changepower', $changepower);
		$this->getAllcities();
		if($_SESSION["app"]=="1")
		{
			$this->display("indexapp");
		}
		else
		{
			$this->display();	
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
				$schedulesuser=M("Plmschedule")->where($mapforPlmschedule)->group("user")->field("user")->select();
				foreach($schedulesuser as $key1 => $val1)
				{
					$voList[$key]['alluser'].=$val1[user].",";
				}
				
				$voList[$key]['alluser']= substr($voList[$key]['alluser'], 0, -1);
				
				
				if($_REQUEST["moduletitle"]=="主项计划")$classify="主项";
				if($_REQUEST["moduletitle"]=="开发专项计划")$classify="开发";
				if($_REQUEST["moduletitle"]=="设计专项计划")$classify="设计";
				if($_REQUEST["moduletitle"]=="采购专项计划")$classify="采购";
				if($_REQUEST["moduletitle"]=="设备到货计划")$classify="采购";
				if($_REQUEST["moduletitle"]=="施工专项计划")$classify="施工";
				
				
				
				
				
		
				if($_REQUEST["moduletitle"]=="主项计划")
				{
					$voList[$key]['plan_status']=$voList[$key]['plan_status1'];
					$voList[$key]['jhuser']=$voList[$key]['jhuser1'];
					$voList[$key]['plan_approveuser']=$voList[$key]['plan_approveuser1'];
					$voList[$key]['planchange_approveuser']=$voList[$key]['planchange_approveuser1'];
					
					
					$mapforPlmschedule_temp[plmid]=$val[id];
					$mapforPlmschedule_temp[classify]=array("like","%".$classify."%");
					$ifsetplmschedule=M("Plmschedule")->where($mapforPlmschedule_temp)->getField("id");
					
					$voList[$key]['worktype_status']=$voList[$key]['worktype_status1'];
				}
				if($_REQUEST["moduletitle"]=="开发专项计划")
				{
					$voList[$key]['plan_status']=$voList[$key]['plan_status2'];
					$voList[$key]['jhuser']=$voList[$key]['jhuser2'];
					$voList[$key]['plan_approveuser']=$voList[$key]['plan_approveuser2'];
					$voList[$key]['planchange_approveuser']=$voList[$key]['planchange_approveuser2'];
					
					$mapforPlmschedule_temp[plmid]=$val[id];
					$mapforPlmschedule_temp[classify]=array("like","%".$classify."%");
					$ifsetplmschedule=M("Plmschedule")->where($mapforPlmschedule_temp)->getField("id");
					
					$voList[$key]['worktype_status']=$voList[$key]['worktype_status2'];
				}
				if($_REQUEST["moduletitle"]=="设计专项计划")
				{
					$voList[$key]['plan_status']=$voList[$key]['plan_status3'];
					$voList[$key]['jhuser']=$voList[$key]['jhuser3'];
					$voList[$key]['plan_approveuser']=$voList[$key]['plan_approveuser3'];
					$voList[$key]['planchange_approveuser']=$voList[$key]['planchange_approveuser3'];
					
					$mapforPlmschedule_temp[plmid]=$val[id];
					$mapforPlmschedule_temp[classify]=array("like","%".$classify."%");
					$ifsetplmschedule=M("Plmschedule")->where($mapforPlmschedule_temp)->getField("id");
					
					$voList[$key]['worktype_status']=$voList[$key]['worktype_status3'];
				}
				if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
				{
					$voList[$key]['plan_status']=$voList[$key]['plan_status4'];
					$voList[$key]['jhuser']=$voList[$key]['jhuser4'];
					$voList[$key]['plan_approveuser']=$voList[$key]['plan_approveuser4'];
					$voList[$key]['planchange_approveuser']=$voList[$key]['planchange_approveuser4'];
					
					$mapforPlmschedule_temp[plmid]=$val[id];
					$mapforPlmschedule_temp[classify]=array("like","%".$classify."%");
					$ifsetplmschedule=M("Plmschedule")->where($mapforPlmschedule_temp)->getField("id");
					
					$voList[$key]['worktype_status']=$voList[$key]['worktype_status4'];
				}
				if($_REQUEST["moduletitle"]=="施工专项计划")
				{
					$voList[$key]['plan_status']=$voList[$key]['plan_status5'];
					$voList[$key]['jhuser']=$voList[$key]['jhuser5'];
					$voList[$key]['plan_approveuser']=$voList[$key]['plan_approveuser5'];
					$voList[$key]['planchange_approveuser']=$voList[$key]['planchange_approveuser5'];
					
					$mapforPlmschedule_temp[plmid]=$val[id];
					$mapforPlmschedule_temp[classify]=array("like","%".$classify."%");
					$ifsetplmschedule=M("Plmschedule")->where($mapforPlmschedule_temp)->getField("id");
					
					$voList[$key]['worktype_status']=$voList[$key]['worktype_status5'];
				}
					
				if((empty($voList[$key]['plan_status']))&&(!empty($ifsetplmschedule)))
				{
					$voList[$key]['plan_status']="<div style='color:orange'>保存状态</div>";
				}
				
				if((false!==strpos($val["plan_status"],"审核通过")))
				{
					$voList[$key]['ifsetplan']=1;
				}
					
				
				if(false!==strstr($voList[$key]['plan_status'],"待审核"))
				{
					$current=$this->findProjectleader($val["id"],$classify);
					$voList[$key]['current']=$current["nickname"];
				}
		
				
			}
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
				else
				{
					
					$p->parameter .= "$key=" . $_REQUEST[$key] . "&";
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
        Cookie::set('_currentUrl_', __SELF__.$p->parameter);
		
        return;
    }
	
	function add() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmworktype[classify]="施工专项节点库";
		
		//if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[title]="到货情况";
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
			
			$mapforPlmschedulex[worktype]=$val['pworktype'];
			$mapforPlmschedulex[subworktype]=$val['title'];
			$mapforPlmschedulex[plmid]=$val['plmid'];
			$mapforPlmschedulex[status]=1;
			if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
			{
				$mapforPlmschedulex[branch]=$val['branch'];
			}
			$vo['worktype'][$key][schedule]=M("Plmschedule")->where($mapforPlmschedulex)->find();
			
			
			
	
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			$mapforWorktypeperiod["begin"]=array("elt",$vo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$vo["capacity"]);
			$mapforWorktypeperiod["pid"]=array("eq",$worktypeinfo["id"]);
			$vo['worktype'][$key][period]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
			
			
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
		
		//if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[subworktype]="到货情况";
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		$this->assign('type', $_REQUEST[type]);
		$this->assign('change', $_REQUEST[change]);
		$this->assign('flag', $_REQUEST[flag]);
		$this->display("add");
	}	
	function add_auto() {
		$plminfo=M("Project")->where("id=".htmlspecialchars($_REQUEST[id]))->find();
		$mapforPlmschedule[plmid]=htmlspecialchars($_REQUEST[id]);
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")
		{
			$mapforPlmschedule[classify]="施工专项节点库";
			$weekend="1";
		}
		$schedules=M("Plmworktype")->where($mapforPlmschedule)->order("id asc")->select();//sort asc
		$date=htmlspecialchars($_REQUEST["date"]);
		foreach($schedules as $key => $val)
		{
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			
			$mapforWorktypeperiod["begin"]=array("elt",$plminfo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$plminfo["capacity"]);
			$mapforWorktypeperiod["pid"]=array("eq",$worktypeinfo["id"]);
			
			/*
			if($plminfo["invest6"]<=200)
			{
				$length=$worktypeinfo["period1"];
			}
			else
			{
				$length=$worktypeinfo["period2"];
			}
			*/
			$length=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
			if(empty($length))
			{
				$length=3;//测试
			}
			
			$schedules[$key]["timebegin"]=$date;
			//$date=date("Y-m-d",strtotime($date)+($length-1)*24*60*60);
			$dateend=$this->getendday($date,($length-1),'','',$weekend);
			$schedules[$key]["timeend"]=$dateend;
			//$date=date("Y-m-d",strtotime($date)+1*24*60*60);
			
			
			
			if($worktypeinfo["parallel"]!="是")
			{
				$date=$this->getendday($dateend,1,'','',$weekend);
			}
			if((($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))&&($key%4==3)&&($key!=0))
			{
				$date=$_REQUEST["date"];
			}
			
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
		
		$date1=$this->getendday($date0,($time0-1),'','',$weekend);
		$date2=$this->getendday($date1,($time1),'','',$weekend);
		$date3=$this->getendday($date2,($time2),'','',$weekend);
		
		$predate["100"]=$date1;
		$predate["101"]=$date2;
		$predate["102"]=$date3;
		
		$data["schedules"]=$schedules;
		$data["predate"]=$predate;
		
		echo json_encode($data);
	}
	function gettimeend() {
		$plminfo=M("Project")->where("id=".htmlspecialchars($_REQUEST[id]))->find();
		$mapforPlmschedule[id]=htmlspecialchars($_REQUEST[worktypeid]);
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")
		{
			$mapforPlmschedule[classify]="施工专项节点库";
			$weekend="1";
		}
		$scheduleinfo=M("Plmworktype")->where($mapforPlmschedule)->order("id asc")->find();
		$date=htmlspecialchars($_REQUEST["date"]);
	
		$mapforWorktype["pid"]=$scheduleinfo["pid"];
		$mapforWorktype["title"]=$scheduleinfo["title"];
		$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
		if($plminfo["invest6"]<=200)
		{
			$length=$worktypeinfo["period1"];
		}
		else
		{
			$length=$worktypeinfo["period2"];
		}
		if(empty($length))
		{
			$length=3;//测试
		}
		
		$dateend=$this->getendday($date,($length-1),'','',$weekend);
		//$datatest["title"]=$dateend;
		//M("Test")->add($datatest);
		echo json_encode($dateend);
	}
	
	function gettimeend1() {
		$plminfo=M("Project")->where("id=".htmlspecialchars($_REQUEST[id]))->find();
	
		$date=htmlspecialchars($_REQUEST["date"]);
		$mapforWorktype["pid"]=htmlspecialchars($_REQUEST[worktypeid]);
		$mapforWorktype["title"]="到货情况";
		$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
		if($plminfo["invest6"]<=200)
		{
			$length=$worktypeinfo["period1"];
		}
		else
		{
			$length=$worktypeinfo["period2"];
		}
		if(empty($length))
		{
			$length=3;//测试
		}
		
		$dateend=$this->getendday($date,($length-1),'','',$weekend);
		//$datatest["title"]=$dateend;
		//M("Test")->add($datatest);
		echo json_encode($dateend);
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
		
		if($_REQUEST["moduletitle"]=="主项计划")
		{
			$classify="主项";
			$plan_status="plan_status1";
			$jhuser="jhuser1";
			$plan_time="plan_time1";
		}
		if($_REQUEST["moduletitle"]=="开发专项计划")
		{
			$classify="开发";
			$plan_status="plan_status2";
			$jhuser="jhuser2";
			$plan_time="plan_time2";
		}
		if($_REQUEST["moduletitle"]=="设计专项计划")
		{
			$classify="设计";
			$plan_status="plan_status3";
			$jhuser="jhuser3";
			$plan_time="plan_time3";
		}
		if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
		{
			$classify="采购";
			$plan_status="plan_status4";
			$jhuser="jhuser4";
			$plan_time="plan_time4";
		}
		if($_REQUEST["moduletitle"]=="施工专项计划")
		{
			$classify="施工";
			$plan_status="plan_status5";
			$jhuser="jhuser5";
			$plan_time="plan_time5";
		}
			
		
		
		$plantimebegin=$_REQUEST[plantimebegin];
		$plantimeend=$_REQUEST[plantimeend];
		
		$worktype=$_REQUEST[worktype];
		foreach($plantimeend as $key => $val)
		{
			
			if(($val<$plantimebegin[$key]))
			{
				$this->error($worktype[$key]."计划开始时间不能超出计划完成时间");
			}
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				if(empty($val)||empty($plantimebegin[$key]))
				{
					$this->error("请填写完整的施工计划");
				}
			}
			else
			{
				if($worktype[$key]=="到货情况")
				{
					if(empty($val)||empty($plantimebegin[$key]))
					{
						$this->error("请填写完整的施工计划");
					}
				}
			}
			
		}
		
		if($_REQUEST["sgjhissave"]!="1")
		{
			$handlehistory.=$_SESSION['loginUserName']."于".$date."设置".$classify."计划</br>------------------</br>"; 
			$model->plan_time=time();
			$model->handlehistory=$handlehistory;
			$model->jhuser=$_SESSION['loginUserName'];
			$model->$jhuser=$_SESSION['loginUserName'];
			$model->$plan_time=date("Y-m-d");
			if($info["step6"]=="0.2")
			{
				M("Project")->where("id=".$info[id])->setField("step6","0.3");
			}
			
			//施工计划待审核
			if(empty($_REQUEST[change]))
			{
				if($_REQUEST["moduletitle"]!="设备到货计划")
				{
					$model->$plan_status=$classify."计划待审核";
				}
				else
				{
					$model->$plan_status=$classify."计划（设备到货需求）待审核";
				}
				$model->predate100=$_REQUEST['predate100'];
				$model->predate101=$_REQUEST['predate101'];
				$model->predate102=$_REQUEST['predate102'];
			}
			else
			{
				if($_REQUEST["moduletitle"]!="设备到货计划")
				{
					$model->$plan_status=$classify."计划变更待审核";
				}
				else
				{
					$model->$plan_status=$classify."计划（设备到货需求）变更待审核";
				}
				$model->predate100=$info['predate100'];
				$model->predate101=$info['predate101'];
				$model->predate102=$info['predate102'];
				$model->predate100temp=$_REQUEST['predate100'];
				$model->predate101temp=$_REQUEST['predate101'];
				$model->predate102temp=$_REQUEST['predate102'];
			
			}
		}
		
		$list = $model->save();
		$time=time();
		
		if($_REQUEST["sgjhissave"]!="1")
		{
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Sgjh";
			$schedulemap[classify]=$classify;
			M("Schedule")->where($schedulemap)->setField("status",0);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		if($_REQUEST[change])
		{
			//上面挪到下面2021-08-03
			
			
			$data['content']=$_SESSION['loginUserName']."于".$date."变更了《".$address."》".$classify."计划，请您审核。";
			$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/5/";
			$data['taskid'] =$info[id];
			$data['type'] ="Sgjh";
			$data['classify']=$classify;
			$userschedule=$this->findProjectleader($info['id'],$classify);
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
		
			/*
			$datamail['content']=$_SESSION["name"]."于".$date."变更了《".$address."》".$classify."计划，请您审核";
			$datamail['receiver']=$this->findleader();
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."变更了《".$address."》".$classify."计划，请您审核";
			$this->Sendmail($datamail);
			*/
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
			if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
			M("Plmscheduletemp")->where($mapforPlmschedule)->delete();
			
			$data[plmid]=$_REQUEST[id];
			$data[user]=$_SESSION[name];
			$data[create_time]=$time;
			$data[status]=1;
			$classify=$_REQUEST[classify];
			$attribute=$_REQUEST[attribute];
			$qualityunit=$_REQUEST[qualityunit];
			$pworktype=$_REQUEST[pworktype];
			$worktype=$_REQUEST[worktype];
			$plantimebegin=$_REQUEST[plantimebegin];
			$plantimeend=$_REQUEST[plantimeend];
			$plancount=$_REQUEST[plancount];
			$branch=$_REQUEST[branch];
			$planquality=$_REQUEST[planquality];
			$sort=1;
			foreach($pworktype as $key => $val)
			{
				$data[worktype]=$val;
				$data[classify]=$classify[$key];
				$data[attribute]=$attribute[$key];
				$data[qualityunit]=$qualityunit[$key];
				$data[subworktype]=$worktype[$key];
				$data[plantimebegintmp]=$plantimebegin[$key];
				$data[plantimeendtmp]=$plantimeend[$key];
				$data[plancounttmp]=$plancount[$key];
				$data[planqualitytmp]=$planquality[$key];
				$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegintmp],$data[plantimeendtmp]);
				$data[branch]=$branch[$key];
				$data["sort"]=$sort;
				$sort++;
				$timeform[$val]+=$data[plantimelength];
				
				
				//判断是否有改变
				$mapforold[worktype]=$val;
				$mapforold[subworktype]=$worktype[$key];
				$mapforold[plmid]=$_REQUEST[id];
				$mapforold[status]=1;
				if(($_REQUEST["moduletitle"]=="设备到货计划")||($_REQUEST["moduletitle"]=="采购专项计划"))
				{
					$mapforold[branch]=$branch[$key];
				}
				$old=M("Plmschedule")->where($mapforold)->find();
				//if(($old[plantimebegin]!=$data[plantimebegin])||($old[plantimeend]!=$data[plantimeend]))
				//{
					$data[plantimebegin]=$old[plantimebegin];
					$data[plantimeend]=$old[plantimeend];
					$data[plancount]=$old[plancount];
					$data[planquality]=$old[planquality];
				//}
				$data[reason]=$_REQUEST[reason];
				M("Plmscheduletemp")->add($data);
			}
			
			
			$mapforschedule[plmid]=$_REQUEST[id];
			$mapforschedule[status]=1;
			if($_REQUEST["moduletitle"]=="主项计划")$mapforschedule[classify]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发专项计划")$mapforschedule[classify]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计专项计划")$mapforschedule[classify]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购专项计划")$mapforschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="设备到货计划")$mapforschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工专项计划")$mapforschedule[classify]="施工专项节点库";
			$scheduledata=M("Plmscheduletemp")->where($mapforschedule)->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmscheduletemp")->where("id=".$val[id])->setField("weight",$weight);
			}
			
		}
		else
		{
			
			/*
			$datamail['content']=$_SESSION["name"]."于".$date."设置了《".$address."》".$classify."计划，请您审核";
			$datamail['receiver']=$this->findleader();
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."设置了《".$address."》".$classify."计划，请您审核";
			$this->Sendmail($datamail);
			*/
			if($_REQUEST["sgjhissave"]!="1")
			{
				$data['content']=$_SESSION['loginUserName']."于".$date."设置《".$address."》".$classify."计划，请您审核。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/2/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify']=$classify;
				$userschedule=$this->findProjectleader($info['id'],$classify);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			
			
			
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
			M("Plmschedule")->where($mapforPlmschedule)->delete();
			
			//plmschedule
			$data[plmid]=$_REQUEST[id];
			$data[user]=$_SESSION[name];
			$data[user]=$_SESSION[name];
			$data[create_time]=$time;
			$data[status]=1;
			$classify=$_REQUEST[classify];
			$attribute=$_REQUEST[attribute];
			$qualityunit=$_REQUEST[qualityunit];
			$pworktype=$_REQUEST[pworktype];
			$worktype=$_REQUEST[worktype];
			$plantimebegin=$_REQUEST[plantimebegin];
			$plantimeend=$_REQUEST[plantimeend];
			$plancount=$_REQUEST[plancount];
			$planquality=$_REQUEST[planquality];
			$branch=$_REQUEST[branch];
			$sort=1;
			foreach($pworktype as $key => $val)
			{
				$data[worktype]=$val;
				$data[classify]=$classify[$key];
				$data[attribute]=$attribute[$key];
				$data[qualityunit]=$qualityunit[$key];
				$data[subworktype]=$worktype[$key];
				$data[plantimebegin]=$plantimebegin[$key];
				$data[plantimeend]=$plantimeend[$key];
				$data[plancount]=$plancount[$key];
				$data[planquality]=$planquality[$key];
				$data[branch]=$branch[$key];
				$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegin],$data[plantimeend]);
				$data["sort"]=$sort;
				$sort++;
				$timeform[$val]+=$data[plantimelength];
				M("Plmschedule")->add($data);
			}
			
			$mapforschedule[plmid]=$_REQUEST[id];
			$mapforschedule[status]=1;
			if($_REQUEST["moduletitle"]=="主项计划")$mapforschedule[classify]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发专项计划")$mapforschedule[classify]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计专项计划")$mapforschedule[classify]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购专项计划")$mapforschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="设备到货计划")$mapforschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工专项计划")$mapforschedule[classify]="施工专项节点库";
			$scheduledata=M("Plmschedule")->where($mapforschedule)->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmschedule")->where("id=".$val[id])->setField("weight",$weight);
			}
			
			
			
		}
		
		
		
		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success('操作成功!');
		
	}
	
	function draftsubmit() {
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
		
		$classify="采购";
		$plan_status="plan_status4";
		$jhuser="jhuser4";
		$plan_time="plan_time4";
		
		$worktypeid=$_REQUEST[worktypeid];
		$worktype=$_REQUEST[worktype];
		
		foreach($worktypeid as $k => $v)
		{
			$plantimebegin=$_REQUEST["plantimebegin".$k];
			$plantimeend=$_REQUEST["plantimeend".$k];
			foreach($plantimeend as $key => $val)
			{
				if(($val<$plantimebegin[$key]))
				{
					$this->error($worktype[$key]."计划开始时间不能超出计划完成时间");
				}
				if(empty($val)||empty($plantimebegin[$key]))
				{
					$this->error("请填写完整的设备到货计划");
				}
			}
		}
		
		
		
		if($_REQUEST["sgjhissave"]!="1")
		{
			$handlehistory.=$_SESSION['loginUserName']."于".$date."设置".$classify."（设备到货需求）计划</br>------------------</br>"; 
			$model->plan_time=time();
			$model->handlehistory=$handlehistory;
			$model->jhuser=$_SESSION['loginUserName'];
			$model->$jhuser=$_SESSION['loginUserName'];
			$model->$plan_time=date("Y-m-d");
			if($info["step6"]=="0.2")
			{
				M("Project")->where("id=".$info[id])->setField("step6","0.3");
			}
			
			//施工计划待审核
			if(empty($_REQUEST[change]))
			{
				$model->$plan_status=$classify."计划（设备到货需求）待审核";
			}
			else
			{
				$model->$plan_status=$classify."计划（设备到货需求）变更待审核";
			
			}
		}
		$model->worktype_status4="采购节点审核通过";
		$list = $model->save();
		
		
		$time=time();
		if($_REQUEST["sgjhissave"]!="1")
		{
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Sgjh";
			$schedulemap[classify]=$classify;
			M("Schedule")->where($schedulemap)->setField("status",0);
		}
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		if(empty($_REQUEST[change]))
		{
			
			if($_REQUEST["sgjhissave"]!="1")
			{
				$data['content']=$_SESSION['loginUserName']."于".$date."设置《".$address."》".$classify."（设备到货需求），请您审核。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/2/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify']=$classify;
				$userschedule=$this->findProjectleader($info['id'],"施工");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			
			
			$mapforPlmworktype["plmid"]=$_REQUEST["id"];
			$mapforPlmworktype[classify]="采购专项节点库";
			M("Plmworktype")->where($mapforPlmworktype)->delete();
			$dataplmworktype["plmid"]=$_REQUEST["id"];
			foreach($worktypeid as $k => $v)
			{
				
				$branch=$_REQUEST["branch".$k];
				foreach($branch as $key => $val)
				{
					if(!empty($val))
					{
						$worktypearray=M("Worktype")->where("pid=".$v)->order("sort asc")->select();
						foreach($worktypearray as $i => $worktypedetail)
						{
							$dataplmworktype["title"]=$worktypedetail["title"];
							$dataplmworktype["classify"]=$worktypedetail["classify"];
							$dataplmworktype["attribute"]=$worktypedetail["attribute"];
							$dataplmworktype["sort"]=$worktypedetail["sort"];
							$dataplmworktype["qualityunit"]=$worktypedetail["qualityunit"];
							$dataplmworktype["pid"]=$worktypedetail["pid"];
							$dataplmworktype["type"]=$worktypedetail["type"];
							$dataplmworktype["parallel"]=$worktypedetail["parallel"];
							$dataplmworktype["user_id"]=$_SESSION["number"];
							$dataplmworktype["create_time"]=time();
							$dataplmworktype["branch"]=$branch[$key];
							$dataplmworktype["pworktype"]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
							$plmworktypes[]=M("Plmworktype")->add($dataplmworktype);
						}
					}
				}
			}
			
	
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			$mapforPlmschedule[classify]="采购专项节点库";
			M("Plmschedule")->where($mapforPlmschedule)->delete();
			$data[plmid]=$_REQUEST[id];
			$data[user]=$_SESSION[name];
			$data[user]=$_SESSION[name];
			$data[create_time]=$time;
			$data[status]=1;
			$sort=1;
			foreach($worktypeid as $k => $v)
			{
				$branch=$_REQUEST["branch".$k];
				$plantimebegin=$_REQUEST["plantimebegin".$k];
				$plantimeend=$_REQUEST["plantimeend".$k];
				$plancount=$_REQUEST["plancount".$k];
				$planquality=$_REQUEST["planquality".$k];
				foreach($branch as $key => $val)
				{
					if(!empty($val))
					{
						$worktypearray=M("Worktype")->where("pid=".$v)->order("sort asc")->select();
						foreach($worktypearray as $i => $worktypedetail)
						{
							$data[worktype]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
							$data[classify]=$worktypedetail["classify"];
							$data[attribute]=$worktypedetail["attribute"];
							$data[qualityunit]=$worktypedetail["qualityunit"];
							$data[subworktype]=$worktypedetail["title"];
							if($worktypedetail["title"]=="到货情况")
							{
								$data[plantimebegin]=$plantimebegin[$key];
								$data[plantimeend]=$plantimeend[$key];
							}
							else
							{
								$data[plantimebegin]="";
								$data[plantimeend]="";
							}
							
							$data[plancount]=$plancount[$key];
							$data[planquality]=$planquality[$key];
							$data[branch]=$branch[$key];
							$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegin],$data[plantimeend]);
							$data["sort"]=$sort;
							$sort++;
							$timeform[$data[worktype]]+=$data[plantimelength];
							M("Plmschedule")->add($data);
						}
					}
				}
			}
			
			$mapforschedule[plmid]=$_REQUEST[id];
			$mapforschedule[status]=1;
			$mapforschedule[classify]="采购专项节点库";
			$scheduledata=M("Plmschedule")->where($mapforschedule)->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmschedule")->where("id=".$val[id])->setField("weight",$weight);
			}
			
		}
		else
		{
			
			$data['content']=$_SESSION['loginUserName']."于".$date."变更了《".$address."》".$classify."（设备到货需求），请您审核。";
			$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/5/";
			$data['taskid'] =$info[id];
			$data['type'] ="Sgjh";
			$data['classify']=$classify;
			$userschedule=$this->findProjectleader($info['id'],"施工");
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
			
			
			
			
			$mapforPlmworktype["plmid"]=$_REQUEST["id"];
			$mapforPlmworktype[classify]="采购专项节点库";
			M("Plmworktypetemp")->where($mapforPlmworktype)->delete();
			$dataplmworktype["plmid"]=$_REQUEST["id"];
			foreach($worktypeid as $k => $v)
			{
				
				$branch=$_REQUEST["branch".$k];
				foreach($branch as $key => $val)
				{
					if(!empty($val))
					{
						$worktypearray=M("Worktype")->where("pid=".$v)->order("sort asc")->select();
						foreach($worktypearray as $i => $worktypedetail)
						{
							$dataplmworktype["title"]=$worktypedetail["title"];
							$dataplmworktype["classify"]=$worktypedetail["classify"];
							$dataplmworktype["attribute"]=$worktypedetail["attribute"];
							$dataplmworktype["sort"]=$worktypedetail["sort"];
							$dataplmworktype["qualityunit"]=$worktypedetail["qualityunit"];
							$dataplmworktype["pid"]=$worktypedetail["pid"];
							$dataplmworktype["type"]=$worktypedetail["type"];
							$dataplmworktype["parallel"]=$worktypedetail["parallel"];
							$dataplmworktype["user_id"]=$_SESSION["number"];
							$dataplmworktype["branch"]=$branch[$key];
							$dataplmworktype["create_time"]=time();
							$dataplmworktype["pworktype"]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
							$plmworktypes[]=M("Plmworktypetemp")->add($dataplmworktype);
						}
					}
				}
			}
			
	
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			$mapforPlmschedule[classify]="采购专项节点库";
			M("Plmscheduletemp")->where($mapforPlmschedule)->delete();
			$data[plmid]=$_REQUEST[id];
			$data[user]=$_SESSION[name];
			$data[user]=$_SESSION[name];
			$data[create_time]=$time;
			$data[status]=1;
			$sort=1;
			foreach($worktypeid as $k => $v)
			{
				$branch=$_REQUEST["branch".$k];
				$plantimebegin=$_REQUEST["plantimebegin".$k];
				$plantimeend=$_REQUEST["plantimeend".$k];
				$plancount=$_REQUEST["plancount".$k];
				$planquality=$_REQUEST["planquality".$k];
				foreach($branch as $key => $val)
				{
					if(!empty($val))
					{
						$worktypearray=M("Worktype")->where("pid=".$v)->order("sort asc")->select();
						foreach($worktypearray as $i => $worktypedetail)
						{
							$data[worktype]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
							$data[classify]=$worktypedetail["classify"];
							$data[attribute]=$worktypedetail["attribute"];
							$data[qualityunit]=$worktypedetail["qualityunit"];
							$data[subworktype]=$worktypedetail["title"];
							
							if($worktypedetail["title"]=="到货情况")
							{
								$data[plantimebegintmp]=$plantimebegin[$key];
								$data[plantimeendtmp]=$plantimeend[$key];
							}
							
							
							$data[plancounttmp]=$plancount[$key];
							$data[planqualitytmp]=$planquality[$key];
							$data[branch]=$branch[$key];
							$data[plantimelength]=$this->diffBetweenTwoDays($data[plantimebegin],$data[plantimeend]);
							$data["sort"]=$sort;
							$sort++;
							$timeform[$data[worktype]]+=$data[plantimelength];
							
							//判断是否有改变
							$mapforold[worktype]=$data[worktype];
							$mapforold[subworktype]=$worktypedetail["title"];
							$mapforold[plmid]=$_REQUEST[id];
							$mapforold[branch]=$branch[$key];
							$mapforold[status]=1;
							$old=M("Plmschedule")->where($mapforold)->find();
							//if(($old[plantimebegin]!=$data[plantimebegin])||($old[plantimeend]!=$data[plantimeend]))
							//{
								$data[plantimebegin]=$old[plantimebegin];
								$data[plantimeend]=$old[plantimeend];
								$data[plancount]=$old[plancount];
								$data[planquality]=$old[planquality];
								if($worktypedetail["title"]!="到货情况")
								{
									$data[plantimebegintmp]=$old[plantimebegin];
									$data[plantimeendtmp]=$old[plantimeend];
								}
							//}
							$data[reason]=$_REQUEST[reason];
				
							M("Plmscheduletemp")->add($data);
						}
					}
				}
			}
			
			$mapforschedule[plmid]=$_REQUEST[id];
			$mapforschedule[status]=1;
			$mapforschedule[classify]="采购专项节点库";
			$scheduledata=M("Plmscheduletemp")->where($mapforschedule)->select();
			foreach($scheduledata as $key => $val)
			{
				$weight=round((100*$val[plantimelength])/$timeform[$val[worktype]],2)."%";
				M("Plmscheduletemp")->where("id=".$val[id])->setField("weight",$weight);
			}
			
	
			
		}
		
		
		
		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success('操作成功!');
		
	}
	
	function add1() {
		
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmworktype[classify]="施工专项节点库";
		
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[title]="到货情况";
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
			
			$mapforPlmschedulex[worktype]=$val['pworktype'];
			$mapforPlmschedulex[subworktype]=$val['title'];
			$mapforPlmschedulex[plmid]=$val['plmid'];
			$mapforPlmschedulex[status]=1;
			if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
			{
				$mapforPlmschedulex[branch]=$val['branch'];
			}
			$vo['worktype'][$key][schedule]=M("Plmschedule")->where($mapforPlmschedulex)->find();
			
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			$mapforWorktypeperiod["begin"]=array("elt",$vo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$vo["capacity"]);
			$mapforWorktypeperiod["pid"]=array("eq",$worktypeinfo["id"]);
			$vo['worktype'][$key][period]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
		}
		$this->assign('orgdata', $vo);
		
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
		
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[subworktype]="到货情况";
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
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
		
		
		
		if($_REQUEST["moduletitle"]=="主项计划")
		{
			$classify="主项";
			$plan_status="plan_status1";
			
			$plan_approveuser="plan_approveuser1";
			$plan_approve_time="plan_approve_time1";
		}
		if($_REQUEST["moduletitle"]=="开发专项计划")
		{
			$classify="开发";
			$plan_status="plan_status2";
			
			$plan_approveuser="plan_approveuser2";
			$plan_approve_time="plan_approve_time2";
		}
		if($_REQUEST["moduletitle"]=="设计专项计划")
		{
			$classify="设计";
			$plan_status="plan_status3";
			
			$plan_approveuser="plan_approveuser3";
			$plan_approve_time="plan_approve_time3";
		}
		if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
		{
			$classify="采购";
			$plan_status="plan_status4";
			
			$plan_approveuser="plan_approveuser4";
			$plan_approve_time="plan_approve_time4";
		}
		if($_REQUEST["moduletitle"]=="施工专项计划")
		{
			$classify="施工";
			$plan_status="plan_status5";
			
			$plan_approveuser="plan_approveuser5";
			$plan_approve_time="plan_approve_time5";
		}
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		$schedulemap[classify]=$classify;
		M("Schedule")->where($schedulemap)->setField("status",0);		
		$date=date("Y-m-d");		
		if(($_REQUEST[result]=="通过"))/*同意*/
		{
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->$plan_status=$classify."计划审核通过";
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划审核，结果：同意。";
				$data['receiver']=$this->findProjectusers($info[id],$classify);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行".$classify."计划审核，结果：同意。";
				$this->Sendmail($data);
			}
			else
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划（设备到货需求）审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->$plan_status=$classify."计划（设备到货需求）审核通过";
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划（设备到货需求）审核，结果：同意。";
				$data['receiver']=$this->findProjectusers($info[id],"施工");
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行".$classify."计划（设备到货需求）审核，结果：同意。";
				$this->Sendmail($data);
				
				
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."审核通过《".$info['title']."》".$classify."计划（设备到货需求），请您设置采购计划。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/采购专项计划/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] ="采购";
				$userschedule=$this->findProjectuser($info['id'],"采购");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
			}
		
			
			
		}
		else
		{	//退回流程
	
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划审核：".$_SESSION['loginUserName']."</br>结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->$plan_status=$classify."计划审核退回";
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."计划审核，请您修改后提交。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectuser($info['id'],$classify);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			else
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划（设备到货需求）审核：".$_SESSION['loginUserName']."</br>结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->$plan_status=$classify."计划（设备到货需求）审核退回";
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."计划（设备到货需求）审核，请您修改后提交。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectuser($info['id'],"施工");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
				
		}
		$model->plan_approveuser=$_SESSION["name"];
		$model->plan_approve_time=time();
		
		$model->$plan_approveuser=$_SESSION["name"];
		$model->$plan_approve_time=date("Y-m-d");
			
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->success('操作成功');
			}
			else
			{
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('操作成功!');
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
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmworktype[classify]="施工专项节点库";
		
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[title]="到货情况";
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
			
			$mapforPlmschedulex[worktype]=$val['pworktype'];
			$mapforPlmschedulex[subworktype]=$val['title'];
			$mapforPlmschedulex[plmid]=$val['plmid'];
			$mapforPlmschedulex[status]=1;
			if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
			{
				$mapforPlmschedulex[branch]=$val['branch'];
			}
			$vo['worktype'][$key][schedule]=M("Plmscheduletemp")->where($mapforPlmschedulex)->find();
			$vo['worktype'][$key][oldschedule]=M("Plmschedule")->where($mapforPlmschedulex)->find();
			
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			$mapforWorktypeperiod["begin"]=array("elt",$vo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$vo["capacity"]);
			$mapforWorktypeperiod["pid"]=array("eq",$worktypeinfo["id"]);
			$vo['worktype'][$key][period]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
			
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
		$schedules=M("Plmscheduletemp")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
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
		
		
		$date=date("Y-m-d");
		
		
		if($_REQUEST["moduletitle"]=="主项计划")
		{
			$classify="主项";
			$plan_status="plan_status1";
			$planchange_approveuser="planchange_approveuser1";
			$planchange_approve_time="planchange_approve_time1";
		}
		if($_REQUEST["moduletitle"]=="开发专项计划")
		{
			$classify="开发";
			$plan_status="plan_status2";
			$planchange_approveuser="planchange_approveuser2";
			$planchange_approve_time="planchange_approve_time2";
		}
		if($_REQUEST["moduletitle"]=="设计专项计划")
		{
			$classify="设计";
			$plan_status="plan_status3";
			$planchange_approveuser="planchange_approveuser3";
			$planchange_approve_time="planchange_approve_time3";
		}
		if(($_REQUEST["moduletitle"]=="采购专项计划")||($_REQUEST["moduletitle"]=="设备到货计划"))
		{
			$classify="采购";
			$plan_status="plan_status4";
			$planchange_approveuser="planchange_approveuser4";
			$planchange_approve_time="planchange_approve_time4";
		}
		if($_REQUEST["moduletitle"]=="施工专项计划")
		{
			$classify="施工";
			$plan_status="plan_status5";
			$planchange_approveuser="planchange_approveuser5";
			$planchange_approve_time="planchange_approve_time5";
		}
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		$schedulemap[classify]=$classify;
		M("Schedule")->where($schedulemap)->setField("status",0);
		
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
			if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
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
				if(($_REQUEST["moduletitle"]=="设备到货计划")||($_REQUEST["moduletitle"]=="采购专项计划"))
				{
					$mapforold[branch]=$val[branch];
				}
				$old=M("Plmschedule")->where($mapforold)->find();
				if(!empty($old))
				{
					$old["classify"]=$val["classify"];
					$old["attribute"]=$val["attribute"];
					$old["sort"]=$val["sort"];
					$old["qualityunit"]=$val["qualityunit"];
					$old[plantimebegin]=$val[plantimebegintmp];
					$old[plantimeend]=$val[plantimeendtmp];
					$old[plantimelength]=$val[plantimelength];
					$old[plancount]=$val[plancounttmp];
					$old[planquality]=$val[planqualitytmp];
					$old[change_time]=$val[create_time];
					$old[plantimebeginold]=$val[plantimebegin];
					$old[plantimeendold]=$val[plantimeend];
					$old[plancountold]=$val[plancount];
					$old[planqualityold]=$val[planquality];
					$old[status]=$val[status];
					$old[reason]=$val[reason];
					$old[weight]=$val[weight];
					$old[user]=$val[user];
					M("Plmschedule")->save($old);
				}
				else
				{
					$data["classify"]=$val["classify"];
					$data["attribute"]=$val["attribute"];
					$data["qualityunit"]=$val["qualityunit"];
					$data[plantimebegin]=$val[plantimebegintmp];
					$data[plantimeend]=$val[plantimeendtmp];
					$data[plancount]=$val[plancounttmp];
					$data[planquality]=$val[planqualitytmp];
					$data[plantimelength]=$val[plantimelength];
					$data[branch]=$val[branch];
					$data[change_time]=$val[create_time];
					
					$data[plantimebeginold]=$val[plantimebegin];
					$data[plantimeendold]=$val[plantimeend];
					$data[plancountold]=$val[plancount];
					$data[planqualityold]=$val[planquality];
					
					
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
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划变更审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->$plan_status=$classify."计划变更审核通过";
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划变更审核，结果：同意。";
				$data['receiver']=$this->findProjectusers($info[id],$classify);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行".$classify."计划变更审核，结果：同意。";
				$this->Sendmail($data);
			}
			else
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划（设备到货需求）变更审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->$plan_status=$classify."计划（设备到货需求）变更审核通过";
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划（设备到货需求）变更审核，结果：同意。";
				$data['receiver']=$this->findProjectusers($info[id],$classify);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行".$classify."计划（设备到货需求）变更审核，结果：同意。";
				$this->Sendmail($data);
			}
		}
		else
		{	//退回流程
	
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划变更审核：".$_SESSION['loginUserName']."</br>结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->$plan_status=$classify."计划变更审核退回";
				
				/*
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划变更审核，结果：退回。";
				$data['receiver']=$this->findProjectusers($info[id]);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划变更审核，结果：退回。";
				$this->Sendmail($data);
				*/
				
				$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."计划变更审核，请您修改后提交。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/3/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectuser($info['id'],$classify);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			else
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划（设备到货需求）变更审核：".$_SESSION['loginUserName']."</br>结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->$plan_status=$classify."计划（设备到货需求）变更审核退回";
				
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."计划（设备到货需求）变更审核，请您修改后提交。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/3/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectuser($info['id'],"施工");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
			}
			
		}
		//$model->plan_approveuser=$_SESSION["name"];
		//$model->plan_approve_time=time();
		
		$model->$planchange_approveuser=$_SESSION["name"];
		$model->$planchange_approve_time=date("Y-m-d");
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if(0)//$_SESSION[app]
			{
				$this->success('操作成功');
			}
			else
			{
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('操作成功!');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	
	function modifyapprove() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmworktype[classify]="施工专项节点库";
		
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[title]="到货情况";
		$vo['worktype']=M("Plmworktypetemp")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktypetemp")->where($mapforPlmworktype)->count();
			}
			
			$mapforPlmschedulex[worktype]=$val['pworktype'];
			$mapforPlmschedulex[subworktype]=$val['title'];
			$mapforPlmschedulex[plmid]=$val['plmid'];
			$mapforPlmschedulex[status]=1;
			if($_REQUEST["moduletitle"]=="设备到货计划")
			{
				$mapforPlmschedulex[branch]=$val['branch'];
			}
			$vo['worktype'][$key][schedule]=M("Plmscheduletemp")->where($mapforPlmschedulex)->find();
			
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			$mapforWorktypeperiod["begin"]=array("elt",$vo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$vo["capacity"]);
			$mapforWorktypeperiod["pid"]=array("eq",$worktypeinfo["id"]);
			$vo['worktype'][$key][period]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
			
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")
		{
			$mapforPlmschedule[subworktype]="到货情况";
		}
		$schedules=M("Plmscheduletemp")->where($mapforPlmschedule)->order("sort asc")->select();
		$this->assign('schedules', $schedules);
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		$this->display();
	}	
	//施工计划变更审核
	function modifyapprovesubmit() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date("Y-m-d");
		$classify="采购";
		$plan_status="plan_status4";
		$planchange_approveuser="planchange_approveuser4";
		$planchange_approve_time="planchange_approve_time4";
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		$schedulemap[classify]=$classify;
		M("Schedule")->where($schedulemap)->setField("status",0);
		$time=time();
		if(($_REQUEST[result]=="通过"))/*同意*/
		{
			
			$mapforPlmworktype[plmid]=$_REQUEST[id];
			$mapforPlmworktype[classify]="采购专项节点库";
			M("Plmworktype")->where($mapforPlmworktype)->delete();
			$worktypedata=M("Plmworktypetemp")->where($mapforPlmworktype)->order("id asc")->select();
			foreach($worktypedata as $key => $val)
			{
				$val["id"]="";
				M("Plmworktype")->add($val);
			}
			
			
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			$mapforPlmschedule[classify]="采购专项节点库";
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
				$mapforold[branch]=$val[branch];
				$old=M("Plmschedule")->where($mapforold)->find();
				if(!empty($old))
				{
					$old["classify"]=$val["classify"];
					$old["attribute"]=$val["attribute"];
					$old["sort"]=$val["sort"];
					$old["qualityunit"]=$val["qualityunit"];
					$old[plantimebegin]=$val[plantimebegintmp];
					$old[plantimeend]=$val[plantimeendtmp];
					$old[plantimelength]=$val[plantimelength];
					$old[plancount]=$val[plancounttmp];
					$old[planquality]=$val[planqualitytmp];
					$old[change_time]=$val[create_time];
					$old[plantimebeginold]=$val[plantimebegin];
					$old[plantimeendold]=$val[plantimeend];
					$old[plancountold]=$val[plancount];
					$old[planqualityold]=$val[planquality];
					$old[status]=$val[status];
					$old[reason]=$val[reason];
					$old[weight]=$val[weight];
					$old[user]=$val[user];
					$old[update_time]=$time;
					M("Plmschedule")->save($old);
				}
				else
				{
					$data["classify"]=$val["classify"];
					$data["attribute"]=$val["attribute"];
					$data["qualityunit"]=$val["qualityunit"];
					$data["branch"]=$val["branch"];
					$data[plantimebegin]=$val[plantimebegintmp];
					$data[plantimeend]=$val[plantimeendtmp];
					$data[plancount]=$val[plancounttmp];
					$data[planquality]=$val[planqualitytmp];
					$data[plantimelength]=$val[plantimelength];
					$data[change_time]=$val[create_time];
					
					$data[plantimebeginold]=$val[plantimebegin];
					$data[plantimeendold]=$val[plantimeend];
					$data[plancountold]=$val[plancount];
					$data[planqualityold]=$val[planquality];
					
					
					$data[plmid]=$val[plmid];
					$data[create_time]=$val[create_time];
					$data[status]=$val[status];
					$data[worktype]=$val[worktype];
					$data[subworktype]=$val[subworktype];
					$data["sort"]=$val["sort"];
					$data[reason]=$val[reason];
					$data[weight]=$val[weight];
					$data[user]=$val[user];
					$data[update_time]=$time;
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
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划变更审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->$plan_status=$classify."计划变更审核通过";
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划变更审核，结果：同意。";
				$data['receiver']=$this->findProjectusers($info[id],$classify);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行".$classify."计划变更审核，结果：同意。";
				$this->Sendmail($data);
			}
			else
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划（设备到货需求）变更审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->$plan_status=$classify."计划（设备到货需求）变更审核通过";
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划（设备到货需求）变更审核，结果：同意。";
				$data['receiver']=$this->findProjectusers($info[id],$classify);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行".$classify."计划（设备到货需求）变更审核，结果：同意。";
				$this->Sendmail($data);
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."审核通过《".$info['title']."》".$classify."计划（设备到货需求）变更，请您设置采购计划变更。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/采购专项计划/tab/3/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] ="采购";
				$userschedule=$this->findProjectuser($info['id'],"采购");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
		}
		else
		{	//退回流程
	
			if($_REQUEST["moduletitle"]!="设备到货计划")
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划变更审核：".$_SESSION['loginUserName']."</br>结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->$plan_status=$classify."计划变更审核退回";
				
				/*
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划变更审核，结果：退回。";
				$data['receiver']=$this->findProjectusers($info[id]);
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."计划变更审核，结果：退回。";
				$this->Sendmail($data);
				*/
				
				$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."计划变更审核，请您修改后提交。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/3/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectuser($info['id'],$classify);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			else
			{
				$model->handlehistory=$info['handlehistory'].$classify."计划（设备到货需求）变更审核：".$_SESSION['loginUserName']."</br>结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->$plan_status=$classify."计划（设备到货需求）变更审核退回";
				
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."计划（设备到货需求）变更审核，请您修改后提交。";
				$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/3/";
				$data['taskid'] =$info[id];
				$data['type'] ="Sgjh";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectuser($info['id'],"施工");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
			}
			
		}
		//$model->plan_approveuser=$_SESSION["name"];
		//$model->plan_approve_time=time();
		
		$model->$planchange_approveuser=$_SESSION["name"];
		$model->$planchange_approve_time=date("Y-m-d");
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if(0)//$_SESSION[app]
			{
				$this->success('操作成功');
			}
			else
			{
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('操作成功!');
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
	
	
	function draft() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		
		
		
		
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$mapforWorktype[classify]="采购专项节点库";
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		
		$mapforWorktype[type]=1;
		$mapforWorktype[projecttype]=$vo["projecttype"];
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		foreach ($worktypes as $key => $val) {
			$worktypes[$key][subworktypes]=M("Worktype")->where("pid=".$val[id])->order("sort asc")->select();
			foreach ($worktypes[$key][subworktypes] as $key1 => $val1) {
				$worktypes[$key][subworktypes][$key1]["title1"]=$val[title].$val1[title];
			}
			
			
			$mapforPlmschedulex[worktype]=$val['title'];
			$mapforPlmschedulex[subworktype]="到货情况";
			$mapforPlmschedulex[plmid]=$vo['id'];
			$mapforPlmschedulex[status]=1;
			
			$worktypes[$key][schedules]=M("Plmschedule")->where($mapforPlmschedulex)->order("sort asc")->select();
			
			$worktypes[$key][schedulecount]=count($worktypes[$key][schedules]);
			
			$mapforWorktypeperiod["begin"]=array("elt",$vo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$vo["capacity"]);
			$mapforWorktypeperiod["worktype"]=array("eq",$val["title"]);
			$mapforWorktypeperiod["subworktype"]=array("eq","到货情况");
			$worktypes[$key][period]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
			
		}
		$this->assign('worktypes', $worktypes);
		
		
		$mapforPlmworktype[plmid]=$vo[id];
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach ($vo['worktype'] as $key => $val) {
			$checkedworktype.=$val[pworktype].$val[title].",";
			$pcheckedworktype.=$val[pworktype].",";
		}
		$this->assign('checkedworktype', $checkedworktype);
		$this->assign('pcheckedworktype', $pcheckedworktype);
		
		
		
		$this->assign('change', $_REQUEST["change"]);
		
		
		$this->display();
	}
	
	function choice() {
		
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		
		$mapforWorktype[projecttype]=$vo["projecttype"];
		$mapforWorktype[type]=1;
		$mapforWorktype[classify]="采购专项节点库";
	
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		foreach ($worktypes as $key => $val) {

			$mapforWorktype1[pid]=array("eq",$val["id"]);
			$mapforWorktype1[type]=2;
			$mapforWorktype1[title]="到货情况";
			$subworktype=M("Worktype")->where($mapforWorktype1)->field("id,period1")->find();
			$worktypes[$key]["period"]=$subworktype["period1"];
			$worktypes[$key]["worktypeid"]=$subworktype["id"];
		}
		
		
		
		$this->assign('materials', $worktypes);
		$this->assign('trkey', $_REQUEST["key"]);
		
		$this->display();
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
			$data['href'] ="index.php?s=Sgjh/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/5/";
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
			if(0)//$_SESSION[app]
			{
				$this->success('操作成功');
			}
			else
			{
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('操作成功!');
			}
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
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmworktype[classify]="施工专项节点库";
		
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmworktype[title]="到货情况";
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
			
			$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$worktypeinfo=M("Worktype")->where($mapforWorktype)->find();
			$mapforWorktypeperiod["begin"]=array("elt",$vo["capacity"]);
			$mapforWorktypeperiod["end"]=array("egt",$vo["capacity"]);
			$mapforWorktypeperiod["pid"]=array("eq",$worktypeinfo["id"]);
			$vo['worktype'][$key][period]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
		}
		$this->assign('orgdata', $vo);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		if($_REQUEST["moduletitle"]=="主项计划")$mapforPlmschedule[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发专项计划")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计专项计划")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购专项计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工专项计划")$mapforPlmschedule[classify]="施工专项节点库";
		
		if($_REQUEST["moduletitle"]=="设备到货计划")$mapforPlmschedule[subworktype]="到货情况";
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		//dump($schedules);
		$this->assign('schedules', $schedules);
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
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