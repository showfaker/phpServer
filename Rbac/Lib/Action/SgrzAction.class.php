<?php
class SgrzAction extends CommonAction {
	
	
	function _filter(&$map){
		if($_REQUEST['city'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['city']."%");
			$this->assign("city",$_REQUEST['city']);
		}
		if($_REQUEST['address'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		if($_REQUEST['keyword'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['keyword']."%");
			$this->assign("keyword",$_REQUEST['keyword']);
		}
		if($_REQUEST['worktype'])
		{
			$map['worktype'] = array('like',"%".$_REQUEST['worktype']."%");
			$this->assign("worktypetitle",$_REQUEST['worktype']);
		}
		if($_REQUEST['subworktype'])
		{
			$map['subworktype'] = array('like',"%".$_REQUEST['subworktype']."%");
			$this->assign("subworktypetitle",$_REQUEST['subworktype']);
		}
		
		
	}
	
	public function getAllworktypes() {
		
		$map["type"]=1;
		$worktypes=M("Worktype")->where($map)->group("title")->order("pid asc,sort asc")->select();
		$this->assign('worktypes', $worktypes);
		
		if($_REQUEST["worktype"])
		{
				$map["type"]=1;
				$map["title"]=$_REQUEST["worktype"];
				$worktypes=M("Worktype")->where($map)->group("title")->order("pid asc,sort asc")->select();
				foreach($worktypes as $key => $val)
				{
					$pids.=$val["id"].",";
				}
				
				
				$map["type"]=2;
				$map["pid"]=array("in",$pids);
				$subworktypes=M("Worktype")->where($map)->group("title")->order("pid asc,sort asc")->select();
				$this->assign('subworktypes', $subworktypes);
		}
		
	}
	public function getsubworktype() {
		
		$map["type"]=1;
		$map["title"]=$_REQUEST["worktype"];
		$worktypes=M("Worktype")->where($map)->group("title")->order("pid asc,sort asc")->select();
		foreach($worktypes as $key => $val)
		{
			$pids.=$val["id"].",";
		}
		
		
		$map["type"]=2;
		$map["pid"]=array("in",$pids);
		$subworktypes=M("Worktype")->where($map)->group("title")->order("pid asc,sort asc")->select();
		echo json_encode($subworktypes);
		
	}
	
	
	
	
	public function index() {
		
		$this->getAllcities();
		$this->getAllworktypes();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if($_REQUEST['title'])
		{
			$mapforproject['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['number'])
		{
			$mapforproject['number'] = array('like',"%".$_REQUEST['number']."%");
			$this->assign("number",$_REQUEST['number']);
		}
		
        if(!empty($_REQUEST['tab']))
		{
			$this->assign('tab',$_REQUEST['tab']);	
		}		
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position]);
		
		if(!empty($_REQUEST['projecttype']))
		{
			$map['projecttype'] = array("in",$_REQUEST['projecttype']);
			$this->assign('projecttype',$_REQUEST['projecttype']);	
		}
		
		if($_REQUEST['tab']==4)
		{
			/*
			$mapforplmschedule['warning'] = 1;
			$mapforPlmschedule[status]=1;
			$warningschedules=M("Plmschedule")->where($mapforplmschedule)->select();
			foreach($warningschedules as $key => $val)
			{
				$scheduleidstr.=$val[id].",";
			}
			$map[scheduleid]=array("in",$scheduleidstr);
			*/
			$map[warning]=array("neq","");
		}
		if($_REQUEST['tab']==7)
		{
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			$projects=M("Project")->where($mapforproject)->field("id")->select();
			foreach($projects as $key => $val)
			{
				$plmids.=$val[id].",";
			}
			$mapforplmschedule[plmid]=array("in",$plmids);
			
			if($_REQUEST['city2'])
			{
				$mapforplmschedule['city'] = array('like',"%".$_REQUEST['city2']."%");
				$this->assign("city2",$_REQUEST['city2']);
			}
			if($_REQUEST['address2'])
			{
				$mapforplmschedule['address'] = array('like',"%".$_REQUEST['address2']."%");
				$this->assign("address2",$_REQUEST['address2']);
			}
			
			$date=date("Y-m-d");
			$mapforplmschedule['prewarning'] = 1;
			
			if($_REQUEST['worktype'])
			{
				$mapforplmschedule['worktype'] = array('like',"%".$_REQUEST['worktype']."%");
				$this->assign("worktypetitle",$_REQUEST['worktype']);
			}
			
			$warningschedules=M("Plmwarning")->where($mapforplmschedule)->select();
			foreach($warningschedules as $key => $val)
			{
				$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
			}
			$this->assign("list", $warningschedules);
			if(empty($warningschedules))
				$this->assign("warningschedulescount", 0);
			else
				$this->assign("warningschedulescount", count($warningschedules));
			if($_SESSION["app"])
			{
				$this->display("indexapp");
			}
			else
			{
				$this->display();
			}
			return;
		}
		if($_REQUEST['tab']==8)
		{
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			$projects=M("Project")->where($mapforproject)->field("id")->select();
			foreach($projects as $key => $val)
			{
				$plmids.=$val[id].",";
			}
			$mapforplmschedule[plmid]=array("in",$plmids);
			
			if($_REQUEST['city2'])
			{
				$mapforplmschedule['city'] = array('like',"%".$_REQUEST['city2']."%");
				$this->assign("city2",$_REQUEST['city2']);
			}
			if($_REQUEST['address2'])
			{
				$mapforplmschedule['address'] = array('like',"%".$_REQUEST['address2']."%");
				$this->assign("address2",$_REQUEST['address2']);
			}
			
			$date=date("Y-m-d");
			$mapforplmschedule['warning'] = 1;
			
			if($_REQUEST['worktype'])
			{
				$mapforplmschedule['worktype'] = array('like',"%".$_REQUEST['worktype']."%");
				$this->assign("worktypetitle",$_REQUEST['worktype']);
			}
			
			$warningschedules=M("Plmwarning")->where($mapforplmschedule)->select();
			foreach($warningschedules as $key => $val)
			{
				$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
			}
			$this->assign("list", $warningschedules);
			if(empty($warningschedules))
				$this->assign("warningschedulescount", 0);
			else
				$this->assign("warningschedulescount", count($warningschedules));
			if($_SESSION["app"])
			{
				$this->display("indexapp");
			}
			else
			{
				$this->display();
			}
			return;
		}
		
		if((empty($_REQUEST['tab']))||($_REQUEST['tab']==1))
		{
			//$map['user'] = array("in",$this->find5levelusers($_SESSION[position]));
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			//$mapforproject["engineeringmanage|supervisor|drawing_user|budget_user|designer|projectmanager|way|waysub|areatype|areadetail|draw_user|waysubother"]=array("eq",$_SESSION[name]);
			
			if($_REQUEST['plmid'])
			{
				$mapforproject['id'] = array('eq',$_REQUEST['plmid']);
				$this->assign("plmid",$_REQUEST['plmid']);
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
				$mapforproject['groupid'] = array('in',$plmgroupids);
				$this->assign('plmgroup', $_REQUEST['plmgroup']);
			}
			if(($_REQUEST["moduletitle"])&&(($_REQUEST["tab"]=="")||($_REQUEST["tab"]=="1")))
			{
				$map['classify'] = $_REQUEST["moduletitle"];
			}
			
			$projects=M("Project")->where($mapforproject)->field("id")->select();
			$arrstr="";
			foreach($projects as $k=>$v){
				$arrstr.=$v['id'].",";
			}
			//$where['user']  = array("in",$this->find5levelusers($_SESSION[position]));
			$where['plmid']  = array('in',$arrstr);
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
			$name = "Plmdaily";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'create_time',false);
			}
		
		}
		
		
		if($_REQUEST['tab']==5)
		{
			if($_REQUEST['title'])
			{
				$mapforproject['title'] = array('like',"%".$_REQUEST['title']."%");
				$this->assign("title",$_REQUEST['title']);
			}
			if($_REQUEST['number'])
			{
				$mapforproject['number'] = array('like',"%".$_REQUEST['number']."%");
				$this->assign("number",$_REQUEST['number']);
			}
		
			//$mapforproject['design_status'] = array("in","施工中");
			$mapforproject['design_status'] = array("in","施工中,竣工待验收,项目待验收,完成验收");
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			//$mapforproject[user]=array("neq","");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $mapforproject,'create_time',false);
			}
		
		}
		if($_REQUEST['tab']==6)
		{
			
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
		
		
			$map['design_status'] = array("in","施工中");
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$map[user]=array("neq","");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'create_time',false);
			}
		
		}
		$this->assign("date",date("Y-m-d"));
		if($_SESSION["app"])
		{
			$this->display("indexapp");
		}
		else
		{
			$this->display();
		}
		
		return;
	}
	function diffBetweenTwoDays ($day1, $day2)
	{
	  $second1 = strtotime($day1);
	  $second2 = strtotime($day2);
		
	  if ($second1 < $second2) {
		$tmp = $second2;
		$second2 = $second1;
		$second1 = $tmp;
	  }
	  return ($second1 - $second2) / 86400;
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
			if($_REQUEST[tab]=="6")
			{
				$p = new Page($count,1000);
			}
			else
			{
				$p = new Page($count, $listRows);
			}
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
				$voList[$key]['photos']=explode(',',$val['photo']);
				$voList[$key]['photosrealname']=explode(',',$val['photorealname']);
				
				foreach($voList[$key]['photos'] as $key1 => $val1)
				{
					$ext = strtolower(end(explode(".",basename($val1)))); 
					if(($ext=="png")||($ext=="jpg")||($ext=="jpeg")||($ext=="bmp")||($ext=="gif"))
					{
						$voList[$key]['photostype'][$key1]="image";
					}
					else
					{
						$voList[$key]['photostype'][$key1]="other";
					}
				}
				
			}
			if($_REQUEST[tab]=="5")
			{
				$date=date("Y-m-d");
				foreach($voList as $key => $val)
				{
					$mapforPlmschedule[classify]=array("eq","施工专项节点库");
					$mapforPlmschedule[plmid] = $val[id];
					$mapforPlmschedule[status] = 1;
					//$voList[$key][daily]=M("Plmschedule")->where($mapforPlmschedule)->order("create_time desc,id desc")->find();
					$voList[$key][daily]=M("Plmschedule")->where($mapforPlmschedule)->order("create_time asc,id asc")->select();
					foreach($voList[$key][daily] as $key1 => $val1)
					{
						if($val1)
						{
							$voList[$key][daily][$key1]['files']=explode(',',$val1['file']);
							$voList[$key][daily][$key1]['filesrealname']=explode(',',$val1['filerealname']);
						}
						
						
						$plantimebegin=$val1["plantimebegin"];
						$plantimeend=$val1["plantimeend"];
						
						//计算今天的需要完成的任务项
						$day1 = $plantimebegin;
						$day2 = $plantimeend;
						$diff = $this->diffBetweenTwoDays($day1, $day2);
						$timeplanlenth=$diff;
						//每天所占的比例
						$percentperday=100/$timeplanlenth;
						
						if($date<$plantimebegin)
						{
							$todayplanpercent=0;
						}	
						else if($date>$plantimeend)
						{
							$todayplanpercent=100;
						}	
						else
						{
							//今天与计划日之间天数差
							$diffreal = $this->diffBetweenTwoDays($day1, $date);
							//今天应该完成的比例
							$todayplanpercent=$percentperday*$diffreal;
						}
						
						$todayplanpercent=round($todayplanpercent,0);
						$voList[$key][daily][$key1][planpercent]=$todayplanpercent."%";
						if(empty($voList[$key][daily][$key1][percent]))$voList[$key][daily][$key1][percent]="0%";
					
					}
					
					
					
					
					
					
					
				}
			}
			if($_REQUEST[tab]=="6")
			{
				foreach($voList as $key => $val)
				{
					//$mapforPlmschedule[percent]=array("neq","100%");
					$mapforPlmschedule[plmid] = $val[id];
					$voList[$key][daily]=M("Plmdaily")->where($mapforPlmschedule)->order("create_time desc,id desc")->find();
					if(!empty($voList[$key][daily][warning]))
					{
						//$mapforPlmwarning[id] = $voList[$key][daily][warning];
						$mapforPlmwarning[plmid] = $voList[$key][daily][plmid];
						$mapforPlmwarning[worktype] = $voList[$key][daily][worktype];
						$voList[$key][warning]=M("Plmwarning")->where($mapforPlmwarning)->find();
					}
					else
					{
						unset($voList[$key]);
					}
				}
			}
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
			$p->parameter="&";
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
			if($_REQUEST['worktype'])
			{
				$p->parameter .= "worktype=" . urlencode($_REQUEST['worktype']) . "&";
			}
			if($_REQUEST['city'])
			{
				$p->parameter .= "city=" . urlencode($_REQUEST['city']) . "&";
			}
			if($_REQUEST['address'])
			{
				$p->parameter .= "address=" . urlencode($_REQUEST['address']) . "&";
			}
			if($_REQUEST['timebegin'])
			{
				$p->parameter .= "timebegin=" . urlencode($_REQUEST['timebegin']) . "&";
			}
			if($_REQUEST['timeend'])
			{
				$p->parameter .= "timeend=" . urlencode($_REQUEST['timeend']) . "&";
			}
			if($_REQUEST['tab'])
			{
				$p->parameter .= "tab=" . urlencode($_REQUEST['tab']) . "&";
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
		
        return;
    }
	public function foreverdelete() {
        //删除指定记录
        $name = "Plmdaily";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            //$id = $_REQUEST [$pk];
            if(!empty($_REQUEST [$pk]))
            {
            	$id = $_REQUEST [$pk];
            }
            else
            {
            	$id = $_REQUEST ["ids"];
            }
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete())
				{
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
	
	function piliangdailyadd()
	{
		$this->assign("moduletitle", $_REQUEST["moduletitle"]);
		$this->assign("plmid", $_REQUEST["plmid"]);
		
		$this->display();
	}
	
	function piliangdailyadd2()
	{
		$plmid=$_REQUEST["plmid"];
		$mapforPlmschedule["plmid"]=$plmid;
		$mapforPlmschedule["status"]=1;
		if($_REQUEST["moduletitle"]=="主项进度管理")$mapforPlmschedule[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发进度管理")$mapforPlmschedule[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计进度管理")$mapforPlmschedule[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购进度管理")$mapforPlmschedule[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工进度管理")$mapforPlmschedule[classify]="施工专项节点库";
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("id asc")->select();
		
		foreach($schedules as $key => $val)
		{
			if($schedules[$key][worktype]!=$schedules[$key-1][worktype])
			{
				$mapforPlmschedule[worktype]=$val[worktype];
				$schedules[$key][block]=1;
				$schedules[$key][rowspan]=M("Plmschedule")->where($mapforPlmschedule)->count();
			}
		}
		$date=date("Y-m-d");
		foreach($schedules as $key => $val)
		{
			if($date<$val[plantimebegin])
			{
				$todayplanpercent=0;
			}	
			else if($date>=$val[plantimeend])
			{
				$todayplanpercent=100;
			}	
			else
			{
				$diff = $val[plantimelength];
				$timeplanlenth=$diff;
				$percentperday=100/$timeplanlenth;
				//今天与计划日之间天数差
				$diffreal = $this->diffBetweenTwoDays($val[plantimebegin], $date);
				//今天应该完成的比例
				$todayplanpercent=round($percentperday*$diffreal,2);
				
				
			}
			
			$schedules[$key][planpercent]=$todayplanpercent."%";
			
			$schedules[$key][currentpercent]=str_replace("","",$val[percent]);
			$schedules[$key][plantimeend]=$val[plantimeend];
			$schedules[$key][plancount]=$val[plancount];
			$schedules[$key][realcount]=$val[realcount];
			
			$schedules[$key][percentdigit]=str_replace("%","",$val[percent]);
			if(empty($schedules[$key][percentdigit]))$schedules[$key][percentdigit]=0;
		}
		
		
		
		foreach($schedules as $key => $val)
		{
			$schedules[$key]['photos']=explode(',',$val['file']);
			$schedules[$key]['photosrealname']=explode(',',$val['filerealname']);
			foreach($schedules[$key]['photos'] as $key1 => $val1)
			{
				$ext = strtolower(end(explode(".",basename($val1)))); 
				if(($ext=="png")||($ext=="jpg")||($ext=="jpeg")||($ext=="bmp")||($ext=="gif"))
				{
					$schedules[$key]['photostype'][$key1]="image";
				}
				else
				{
					$schedules[$key]['photostype'][$key1]="other";
				}
			}
		}
		
		$this->assign("moduletitle", $_REQUEST["moduletitle"]);
		$this->assign("plmid", $_REQUEST["plmid"]);
		$this->assign("schedules", $schedules);
		
		$mapforUser["status"]=1;
		$users=M("User")->where($mapforUser)->field("nickname")->select();
		$this->assign("users", $users);
		
		$this->display();
	}
	
	public function uploadfile() {
		
		set_time_limit(0);
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		if($_REQUEST["moduletitle"]=="主项进度管理"){$worktype_status=$plminfo["worktype_status1"];$plan_status=$plminfo["plan_status1"];}
		if($_REQUEST["moduletitle"]=="开发进度管理"){$worktype_status=$plminfo["worktype_status2"];$plan_status=$plminfo["plan_status2"];}
		if($_REQUEST["moduletitle"]=="设计进度管理"){$worktype_status=$plminfo["worktype_status3"];$plan_status=$plminfo["plan_status3"];}
		if($_REQUEST["moduletitle"]=="采购进度管理"){$worktype_status=$plminfo["worktype_status4"];$plan_status=$plminfo["plan_status4"];}
		if($_REQUEST["moduletitle"]=="施工进度管理"){$worktype_status=$plminfo["worktype_status5"];$plan_status=$plminfo["plan_status5"];}
		if($worktype_status=="")$worktype_status="节点未设置";
		if($plan_status=="")$plan_status="计划未设置";
		
		if((false!==strpos($worktype_status,"待审核"))||(false!==strpos($worktype_status,"退回")||($worktype_status=="节点未设置")))
		{
			$this->error($worktype_status."，无法上传进度");
		}
		if((false!==strpos($plan_status,"计划待审核"))||(false!==strpos($plan_status,"计划审核退回")||($plan_status=="计划未设置")))
		{
			$this->error($plan_status."，无法上传进度");
		}
		$scheduleidarray=$_REQUEST["scheduleid"];
		
		$date=date("Y-m-d");
		$handlehistory=$plminfo["handlehistory"].$_SESSION['loginUserName']."于".$date."上传文件</br>------------------</br>"; 
		M("Project")->where("id=".$plminfo[id])->setField("handlehistory",$handlehistory);
	
	
		foreach($scheduleidarray as $key =>$val)
		{
			
			$mapforPlmschedule[id]=$val;
			$mapforPlmschedule[status]=1;
			$scheduleinfo=M("Plmschedule")->where($mapforPlmschedule)->find();
			
			$savePath = '../Public/Uploads/';
			
			
			$newnameall=$scheduleinfo["file"];
			$filenameall=$scheduleinfo["filerealname"];
			
			
			$i=0;
			if(!empty($_FILES['file'.$key]['name'][0]))
			{
				
				$file=$_FILES['file'.$key]['name'];
				$file_tmp=$_FILES['file'.$key]['tmp_name'];
				foreach($file as $key=>$val)
				{
					if(!empty($val))
					{
						$filename=$val;
						$ext = strtolower(end(explode(".",basename($filename)))); 
						$uuid=uniqid(rand(), false);
						$newname = $uuid.'.'.$ext;
						$upload_file = $savePath.$newname;
						
						if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
						{
							$this->error("文件名不能含有特殊字符！");
						}
						if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
						{
							//$this->error("非法文件类型！");
						}
						move_uploaded_file($file_tmp[$key],$upload_file);
						
					
						$datafile[plmNumber]=$plminfo[id];
						$datafile[loadPerson]=$_SESSION["name"];
						$datafile[loadtime]=time();
						$datafile[plm]=$plminfo[title];
						$datafile[type]=$scheduleinfo[worktype];
						$datafile[classify]=$scheduleinfo[classify];
						$datafile[attribute]=$scheduleinfo[attribute];
						$datafile[worktype]=$scheduleinfo[worktype];
						$datafile[subworktype]=$scheduleinfo[subworktype];
						$datafile[newname] = $newname;
						$datafile[filename] = $filename;
						$datafile[title]=$filename;
						M("Plmfile")->add($datafile);
					
						$newnameall.=$newname.',';
						$filenameall.=$filename.',';
						
						
						$filearray[$i]=$newname;
						$i++;
					}
				}
				$scheduleinfo[file]=$newnameall;
				$scheduleinfo[filerealname]=$filenameall;
				M("Plmschedule")->save($scheduleinfo);
			}
		}	
		$this->success(json_encode($filearray));		
	}
	
	
	public function deleteFile() {
		
		
		$filenewname=$_REQUEST["filenewname"];
		$mapforPlmschedule[file]=array("like","%".$filenewname.",%");
		$mapforPlmschedule[status]=1;
		$scheduleinfo=M("Plmschedule")->where($mapforPlmschedule)->find();
		

		$mapforPlmfile["newname"]=$filenewname;
		$plmfileid=M("Plmfile")->where($mapforPlmfile)->getField("id");
		$mapforPlmfile["id"]=$plmfileid;
		M("Plmfile")->where($mapforPlmfile)->delete();
		
		$savePath = '../Public/Uploads/';
		//unlink("$savePath/$filenewname");
		
		$newnameall=$scheduleinfo["file"];
		$filenameall=$scheduleinfo["filerealname"];
		
		$newnameallarray=explode(",",$scheduleinfo["file"]);
		$filenameallarray=explode(",",$scheduleinfo["filerealname"]);
		
		$newnameall="";
		$filenameall="";
		foreach($newnameallarray as $key => $val)
		{
			if($val!=$filenewname)
			{
				$newnameall.=$newnameallarray[$key].",";
				$filenameall.=$filenameallarray[$key].",";
			}
			else
			{
				
			}
		}
		
		$scheduleinfo["file"]=$newnameall;
		$scheduleinfo["filerealname"]=$filenameall;
		M("Plmschedule")->save($scheduleinfo);
		
		$this->success($filenewname);		
	}
	
	public function dailysubmit() {
		set_time_limit(0);
		/*
		if(($oldscheduleinfo[$key]!="100%")&&($percentarray[$key]=="100%")&&(empty($_FILES['file'.$key]['name'][0]))&&(false==strstr($scheduleinfo["classify"],"采购")))
		{
			$result[result]="进度完成时，请上传附件或图片";
			echo json_encode($result);
			return;
		}	
		*/
		if(empty($_REQUEST[plmid]))
		{
			$this->error("请选择项目");	
			return;
		}
		
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		if($_REQUEST["moduletitle"]=="主项进度管理"){$worktype_status=$plminfo["worktype_status1"];$plan_status=$plminfo["plan_status1"];}
		if($_REQUEST["moduletitle"]=="开发进度管理"){$worktype_status=$plminfo["worktype_status2"];$plan_status=$plminfo["plan_status2"];}
		if($_REQUEST["moduletitle"]=="设计进度管理"){$worktype_status=$plminfo["worktype_status3"];$plan_status=$plminfo["plan_status3"];}
		if($_REQUEST["moduletitle"]=="采购进度管理"){$worktype_status=$plminfo["worktype_status4"];$plan_status=$plminfo["plan_status4"];}
		if($_REQUEST["moduletitle"]=="施工进度管理"){$worktype_status=$plminfo["worktype_status5"];$plan_status=$plminfo["plan_status5"];}
		if($worktype_status=="")$worktype_status="节点未设置";
		if($plan_status=="")$plan_status="计划未设置";
		
		if((false!==strpos($worktype_status,"待审核"))||(false!==strpos($worktype_status,"退回")||($worktype_status=="节点未设置")))
		{
			$this->error($worktype_status."，无法上传进度");
		}
		if((false!==strpos($plan_status,"计划待审核"))||(false!==strpos($plan_status,"计划审核退回")||($plan_status=="计划未设置")))
		{
			$this->error($plan_status."，无法上传进度");
		}
		
		
		
		$scheduleidarray=$_REQUEST["scheduleid"];
		$planpercentarray=$_REQUEST["planpercent"];
		$percentarray=$_REQUEST["percent"];
		$remarkarray=$_REQUEST["remark"];
		$countarray=$_REQUEST["count"];
		//$jihuashuliangarray=$_REQUEST["jihuashuliang"];
		$daohuoshuliangarray=$_REQUEST["daohuoshuliang"];
		$brancharray=$_REQUEST["branch"];
		$keeperarray=$_REQUEST["keeper"];
		$remarkarray=$_REQUEST["remark"];
		
		
		
		
		foreach($scheduleidarray as $key =>$val)
		{
			$mapforPlmschedule[id]=$val;
			$mapforPlmschedule[status]=1;
			$scheduleinfo=M("Plmschedule")->where($mapforPlmschedule)->find();
			if($daohuoshuliangarray[$key])
			{
				$realcount=$daohuoshuliangarray[$key];
				if($realcount==$scheduleinfo["plancount"])
				{
					$percent="100%";
				}
				else
				{
					$percent=round(100*$daohuoshuliangarray[$key]/$scheduleinfo["plancount"],0)."%";
				}
				$percentarray[$key]=$percent;
			}
			
			$oldpercent=str_replace("%","",$scheduleinfo["percent"]);
			$newpercent=str_replace("%","",$percentarray[$key]);
			
			if(($oldpercent>$newpercent)&&($_SESSION["account"]!="admin"))
			{
				$this->error($scheduleinfo["subworktype"]."节点进度小于历史进度，请检查今日实际进度比例是否正确");
			}
			
			
		}
		
		
		
		M("Project")->where("id=".$_REQUEST[plmid])->setField("design_status","施工中");
		
		
		
		if($info["step6"]=="0.2")
		{
			M("Project")->where("id=".$info[id])->setField("step6","0.3");
		}
		
		
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		
		
		
		
		$date=date("Y-m-d");
		$handlehistory=$plminfo["handlehistory"].$_SESSION['loginUserName']."于".$date."上传进度</br>------------------</br>"; 
		M("Project")->where("id=".$plminfo[id])->setField("handlehistory",$handlehistory);
	
		
		
		$data[plmid]=$_REQUEST[plmid];
		$data[plm]=$plminfo['title'];
		$data[classify]=$_REQUEST[moduletitle];
		$data[user_id]=$_SESSION[number];
		$data[user]=$_SESSION['loginUserName'];
		$data[role]=$_SESSION[role];
		$data[type]="2";
		$data[create_time]=time();
		$data["date"]=date("Y-m-d");
		$data[title]=$plminfo['title'];
		$dailyid=M("Plmdaily")->add($data);
		foreach($scheduleidarray as $key =>$val)
		{
			$mapforPlmschedule[id]=$val;
			$mapforPlmschedule[status]=1;
			$scheduleinfo=M("Plmschedule")->where($mapforPlmschedule)->find();
			$oldscheduleinfo=$scheduleinfo;
			if($daohuoshuliangarray[$key])
			{
				//$scheduleinfo[plancount]=$jihuashuliangarray[$key];
				$scheduleinfo[realcount]=$daohuoshuliangarray[$key];
				if($scheduleinfo[realcount]==$scheduleinfo["plancount"])
				{
					$scheduleinfo[percent]="100%";
				}
				else
				{
					$scheduleinfo[percent]=round(100*$daohuoshuliangarray[$key]/$scheduleinfo["plancount"],0)."%";
				}
				$percentarray[$key]=$scheduleinfo[percent];
			}
			
			if($planpercentarray[$key]>$percentarray[$key])
			{
				$scheduleinfo[warning]="1";
			}
			$scheduleinfo[realquality]=round(($percentarray[$key]*$scheduleinfo["planquality"]/100),1);
			$scheduleinfo[qualityunity]=$scheduleinfo["qualityunity"];
			
		
		
			$date=date("Y-m-d");
			//今日应当进度
			if($date<$scheduleinfo[plantimebegin])
			{
				$todayplanpercent=0;
			}	
			else if($date>=$scheduleinfo[plantimeend])
			{
				$todayplanpercent=100;
			}	
			else
			{
				$diff = $scheduleinfo[plantimelength];
				$timeplanlenth=$diff;
				$percentperday=100/$timeplanlenth;
				//今天与计划日之间天数差
				$diffreal = $this->diffBetweenTwoDays($scheduleinfo[plantimebegin], $date);
				//今天应该完成的比例
				$todayplanpercent=round($percentperday*$diffreal,0);
			}
			$scheduleinfo[percent]=$percentarray[$key];
			if($remarkarray[$key])
			{
				$scheduleinfo[remark]=$remarkarray[$key];
			}
			$scheduleinfo[keeper]=$keeperarray[$key];
			
			if(($oldscheduleinfo[realtimebegin]=="")&&($scheduleinfo["percent"]!="0%"))
			{
				$scheduleinfo[realtimebegin]=$date;
			}
			if(($oldscheduleinfo["realtimeend"]=="")&&($scheduleinfo["percent"]=="100%"))
			{
				$scheduleinfo[realtimeend]=date("Y-m-d");
				$scheduleinfo[realtimelength]=$this->diffBetweenTwoDays($scheduleinfo[realtimebegin],$scheduleinfo[realtimeend])+1;
				
				$datanews["content"]=$_SESSION["name"]."于".$date."完成项目节点".$scheduleinfo["worktype"]."-".$scheduleinfo["subworktype"];
				$datanews["user"]=$_SESSION["name"];
				$datanews["ctime"]=time();
				$datanews["time"]=date("Y-m-d H:i:s");
				$datanews["plmid"]=$_REQUEST[plmid];
				M("Plmnews")->add($datanews);
				
				
				$datamail['content']=$_SESSION["name"]."于".$date."完成项目《".$plminfo["title"]."》节点".$scheduleinfo["worktype"]."-".$scheduleinfo["subworktype"];
				$datamail['receiver']=$this->findProjectusers($_REQUEST[plmid]);
				$datamail['sender']="系统通知";
				$datamail['title'] =$_SESSION["name"]."于".$date."完成项目《".$plminfo["title"]."》节点".$scheduleinfo["worktype"]."-".$scheduleinfo["subworktype"];
				$this->Sendmail($datamail,"no");
			
			}
			
			
			if(($oldscheduleinfo["keeper"]!=$scheduleinfo["keeper"])&&(!empty($scheduleinfo["keeper"])))
			{
				$mapforuser[nickname]=$scheduleinfo["keeper"];
				$appinfo=M("User")->where($mapforuser)->field("devicetype,clientid,email")->find();
				OutmailAction::SendMail($appinfo["email"],"项目进度管理系统","【文件接收】"."【".$plminfo["title"]."】的".$scheduleinfo['worktype']."-".$scheduleinfo['subworktype']."（节点文件）需要您操作接收");
				
				
				$scheduleinfo["update_time"]=time();
				$scheduleinfo["filereceivetime"]="";
			}
			
			M("Plmschedule")->save($scheduleinfo);
			
			
			
			
			
			
			/*自动完成*/
			if($_REQUEST["moduletitle"]=="主项进度管理"){$mapforWorktype1["classify"]="主项节点库";$mapforWorktype2["classify"]="主项节点库";}
			if($_REQUEST["moduletitle"]=="开发进度管理"){$mapforWorktype1["classify"]="开发专项节点库";$mapforWorktype2["classify"]="开发专项节点库";}
			if($_REQUEST["moduletitle"]=="设计进度管理"){$mapforWorktype1["classify"]="设计专项节点库";$mapforWorktype2["classify"]="设计专项节点库";}
			if($_REQUEST["moduletitle"]=="采购进度管理"){$mapforWorktype1["classify"]="采购专项节点库";$mapforWorktype2["classify"]="采购专项节点库";}
			if($_REQUEST["moduletitle"]=="施工进度管理"){$mapforWorktype1["classify"]="施工专项节点库";$mapforWorktype2["classify"]="施工专项节点库";}
			$mapforWorktype1["type"]="1";
			$mapforWorktype1["projecttype"]=$plminfo[projecttype];
			$mapforWorktype1["title"]=$scheduleinfo['worktype'];
			$worktypeid=M("Worktype")->where($mapforWorktype1)->getField("id");
			
			$mapforWorktype2["type"]="2";
			$mapforWorktype2["pid"]=$worktypeid;
			$mapforWorktype2["title"]=$scheduleinfo['subworktype'];
			$worktypeid=M("Worktype")->where($mapforWorktype2)->getField("id");
			
			$mapforWorktype3["type"]="2";
			$mapforWorktype3["pid"]=array("like","%%");
			$mapforWorktype3["title"]=array("like","%%");
			$mapforWorktype3["autocompleteid"]=array("like","%".$worktypeid.",%");
			
			$autocompleteworktypearray=M("Worktype")->where($mapforWorktype3)->select();
			
			if(!empty($autocompleteworktypearray))
			{
				//dump($autocompleteworktypearray);
				//本项决定的主项列表
				foreach($autocompleteworktypearray as $key => $val)
				{
					$autocompleteid=$val[autocompleteid];
					$autocompletearray=explode(",",$autocompleteid);
					$autocomplete=$val[autocomplete];
					$autocompletearray=explode("</br>",$autocomplete);
					$isautocomplete=1;
					//该主项的所有完成项
					$alldatelength=0;
					$percentdays=0;
					foreach($autocompletearray as $key1 => $val1)
					{
						$autocompletedetail=explode("-",$val1);
						
						$mapforPlmscheduley[plmid]=$_REQUEST[plmid];
						$mapforPlmscheduley[classify]=$autocompletedetail[0];
						$mapforPlmscheduley[worktype]=$autocompletedetail[1];
						$mapforPlmscheduley[subworktype]=$autocompletedetail[2];
						$mapforPlmscheduley[status]=1;
						
						$autocompletescheduledata=M("Plmschedule")->where($mapforPlmscheduley)->field("percent,plantimelength")->find();
						//并不是所有依赖的自动完成项，该项目里都有
						if(!empty($autocompletescheduledata))
						{
							$alldatelength+=$autocompletescheduledata["plantimelength"];
							$percentdays+=str_replace("%","",$autocompletescheduledata["percent"])*$autocompletescheduledata["plantimelength"]/100;
						}
						/*
						if($percent!="100%")
						{
							$isautocomplete=0;
						}
						*/
					}
					$realpercent=round(100*$percentdays/$alldatelength,0)."%";
					
					$mapforPlmschedulez[plmid]=$_REQUEST[plmid];
					$mapforPlmschedulez[classify]=$val["classify"];
					$mapforPlmschedulez[worktype]=M("Worktype")->where("id=".$val["pid"])->getField("title");
					$mapforPlmschedulez[subworktype]=$val["title"];
					$mapforPlmschedulez[status]=1;
					$scheduleinfo=M("Plmschedule")->where($mapforPlmschedulez)->find();
					
				
					if($realpercent!=$scheduleinfo["percent"])//(!empty($dependencearray))&&($isautocomplete==1)
					{
						//今日应当进度
						/*
						if($date<$scheduleinfo[plantimebegin])
						{
							$todayplanpercent=0;
						}	
						else if($date>=$scheduleinfo[plantimeend])
						{
							$todayplanpercent=100;
						}	
						else
						{
							$diff = $scheduleinfo[plantimelength];
							$timeplanlenth=$diff;
							$percentperday=100/$timeplanlenth;
							//今天与计划日之间天数差
							$diffreal = $this->diffBetweenTwoDays($scheduleinfo[plantimebegin], $date);
							//今天应该完成的比例
							$todayplanpercent=round($percentperday*$diffreal,0);
						}
						
						$data[planpercent]=$todayplanpercent."%";
						if($data[planpercent]>"100%")
						{
							$data[warning]="1";
						}
						
						if($val["classify"]=="主项节点库")$data[classify]="主项进度管理";
						if($val["classify"]=="开发专项节点库")$data[classify]="开发进度管理";
						if($val["classify"]=="设计专项节点库")$data[classify]="设计进度管理";
						if($val["classify"]=="采购专项节点库")$data[classify]="采购进度管理";
						if($val["classify"]=="施工专项节点库")$data[classify]="施工进度管理";
						
						$data[plmid]=$_REQUEST[plmid];
						$data[plm]=$plminfo['title'];
						$data[user_id]="0";
						$data[user]="系统";
						
						$data[role]="系统";
						$data[type]="进度汇报";
						
						$data[create_time]=time();
						$data["date"]=date("Y-m-d");
						
						$data[title]=$plminfo['title'];
						$data[content]="系统自动生成";
						$data[voice]="";
						$data[worktype]=$scheduleinfo['worktype'];
						$data[subworktype]=$scheduleinfo['subworktype'];
						
						$data[percent]=$realpercent;
						$data[scheduleid]=$scheduleinfo["id"];
						M("Plmdaily")->add($data);
						*/
						
						$schedule=$scheduleinfo;
						if(($schedule[realtimebegin]==""))
						{
							$schedule[realtimebegin]=$data["date"];
						}
						if(1)
						{
							$schedule[realtimeend]=$data["date"];
						}
						
						$schedule[percent]=$realpercent;
						M("Plmschedule")->save($schedule);
			
					}
				}
			}
			
		
		
		
		}
		
		
		
		
		if($_REQUEST["moduletitle"]=="主项进度管理")$mapforPlmschedule1["classify"]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发进度管理")$mapforPlmschedule1["classify"]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计进度管理")$mapforPlmschedule1["classify"]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购进度管理")$mapforPlmschedule1["classify"]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工进度管理")$mapforPlmschedule1["classify"]="施工专项节点库";
		$mapforPlmschedule1[id]=array("in",$_REQUEST["scheduleid"]);
		$mapforPlmschedule1[status]=1;
		$schedulearray=M("Plmschedule")->where($mapforPlmschedule1)->select();
		foreach($schedulearray as $key =>$val)
		{
			$schedulearray[$key]["id"]="";
			$schedulearray[$key]["dailyid"]=$dailyid;
		}
		$result=M("Plmscheduledaily")->addAll($schedulearray);
	
		$this->success("操作成功");	
		//$result[result]="操作成功";
		//echo json_encode($result);
	}
	
	function piliangdailycheck()
	{
		if(!empty($_REQUEST["dailyid"]))
		{
			$dailyid=$_REQUEST["dailyid"];
		}
		else
		{
			$mapforPlmscheduletemp["plmid"]=$_REQUEST["plmid"];
			$mapforPlmscheduletemp["status"]=1;
			if(false!==strstr($_REQUEST["moduletitle"],"主项"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%主项%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"开发"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%开发%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"设计"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%设计%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"采购"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%采购%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"施工"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%施工%");
			}
			$dailyid=M("Plmscheduledaily")->where($mapforPlmscheduletemp)->max("dailyid");
		}
		$mapforPlmschedule["dailyid"]=$dailyid;
		$mapforPlmschedule["status"]=1;
		$schedules=M("Plmscheduledaily")->where($mapforPlmschedule)->order("sort asc")->select();

		foreach($schedules as $key => $val)
		{
			if($schedules[$key][worktype]!=$schedules[$key-1][worktype])
			{
				$mapforPlmschedule[worktype]=$val[worktype];
				$schedules[$key][block]=1;
				$schedules[$key][rowspan]=M("Plmscheduledaily")->where($mapforPlmschedule)->count();
			}
			
			$schedules[$key]['photos']=explode(',',$val['file']);
			$schedules[$key]['photosrealname']=explode(',',$val['filerealname']);
			foreach($schedules[$key]['photos'] as $key1 => $val1)
			{
				$ext = strtolower(end(explode(".",basename($val1)))); 
				if(($ext=="png")||($ext=="jpg")||($ext=="jpeg")||($ext=="bmp")||($ext=="gif"))
				{
					$schedules[$key]['photostype'][$key1]="image";
				}
				else
				{
					$schedules[$key]['photostype'][$key1]="other";
				}
			}
		}
		$date=date("Y-m-d");
		$this->assign("moduletitle", $_REQUEST["moduletitle"]);
		$this->assign("plmid", $_REQUEST["plmid"]);
		$this->assign("schedules", $schedules);
		$this->display();
	}
	
	
	function piliangdailycheck_new()
	{
		if(!empty($_REQUEST["dailyid"]))
		{
			$dailyid=$_REQUEST["dailyid"];
		}
		else
		{
			$mapforPlmscheduletemp["plmid"]=$_REQUEST["plmid"];
			$mapforPlmscheduletemp["status"]=1;
			if(false!==strstr($_REQUEST["moduletitle"],"主项"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%主项%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"开发"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%开发%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"设计"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%设计%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"采购"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%采购%");
			}
			if(false!==strstr($_REQUEST["moduletitle"],"施工"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%施工%");
			}
			
			if(false!==strstr($_REQUEST["moduletitle1"],"主项"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%主项%");
			}
			if(false!==strstr($_REQUEST["moduletitle1"],"开发"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%开发%");
			}
			if(false!==strstr($_REQUEST["moduletitle1"],"设计"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%设计%");
			}
			if(false!==strstr($_REQUEST["moduletitle1"],"采购"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%采购%");
			}
			if(false!==strstr($_REQUEST["moduletitle1"],"施工"))
			{
				$mapforPlmscheduletemp["classify"]=array("like","%施工%");
			}
			$dailyid=M("Plmscheduledaily")->where($mapforPlmscheduletemp)->max("dailyid");
		}
		$mapforPlmschedule["dailyid"]=$dailyid;
		$mapforPlmschedule["status"]=1;
		$schedules=M("Plmscheduledaily")->where($mapforPlmschedule)->order("sort asc")->select();

		foreach($schedules as $key => $val)
		{
			
			$schedules[$key]['percentdigit']=str_replace('%','',$val['percent']);
			$schedules[$key]['worktypesubworktype']=$val['worktype'].$val['subworktype'];
			if($schedules[$key][worktype]!=$schedules[$key-1][worktype])
			{
				$mapforPlmschedule[worktype]=$val[worktype];
				$schedules[$key][block]=1;
				$schedules[$key][rowspan]=M("Plmscheduledaily")->where($mapforPlmschedule)->count();
			}
			
			$schedules[$key]['photos']=explode(',',$val['file']);
			$schedules[$key]['photosrealname']=explode(',',$val['filerealname']);
			foreach($schedules[$key]['photos'] as $key1 => $val1)
			{
				$ext = strtolower(end(explode(".",basename($val1)))); 
				if(($ext=="png")||($ext=="jpg")||($ext=="jpeg")||($ext=="bmp")||($ext=="gif"))
				{
					$schedules[$key]['photostype'][$key1]="image";
				}
				else
				{
					$schedules[$key]['photostype'][$key1]="other";
				}
			}
		}
		$date=date("Y-m-d");
		$this->assign("moduletitle", $_REQUEST["moduletitle"]);
		$this->assign("subworktype", $_REQUEST["subworktype"]);
		$this->assign("worktype", $_REQUEST["worktype"]);
		$this->assign("worktypesubworktype", $_REQUEST["worktype"].$_REQUEST["subworktype"]);
		$this->assign("plmid", $_REQUEST["plmid"]);
		$this->assign("dailyid", $dailyid);
		
		$this->assign("schedules", $schedules);
		
		$mapforUser["status"]=1;
		$mapforUser["nickname"]=array("neq","展示账号");
		$users=M("User")->where($mapforUser)->field("nickname")->select();
		$this->assign("users", $users);
		
		$this->display();
	}
	
	
	public function dailyupdate() {
		set_time_limit(0);
	
	
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		$mapforPlmschedule["plmid"]=$_REQUEST["plmid"];
		$mapforPlmschedule["worktype"]=$_REQUEST["worktype"];
		$mapforPlmschedule["subworktype"]=$_REQUEST["subworktype"];
		$plmscheduleid=M("Plmschedule")->where($mapforPlmschedule)->max("id");
		$scheduleinfo=M("Plmschedule")->where("id=".$plmscheduleid)->find();	
		
		$newnameall=$scheduleinfo["file"];
		$filenameall=$scheduleinfo["filerealname"];
		
		$newnameallarray=explode(",",$newnameall);
		$filenameallarray=explode(",",$filenameall);
		
		$scheduleidarray=$_REQUEST["scheduleid"];
		$planpercentarray=$_REQUEST["planpercent"];
		$percentarray=$_REQUEST["percent"];
		$remarkarray=$_REQUEST["remark"];
		$countarray=$_REQUEST["count"];
		$daohuoshuliangarray=$_REQUEST["daohuoshuliang"];
		$keeperarray=$_REQUEST["keeper"];
		$remarkarray=$_REQUEST["remark"];
		$delboxarray=$_REQUEST["delbox"];
		
		
		$newnameall="";
		$filenameall="";
		$testtest["title"]=json_encode($delboxarray);
		M("Testtest")->add($testtest);
		foreach($newnameallarray as $key => $val)
		{
			if($_REQUEST["delbox"][$key]!="1")
			{
				$newnameall.=$newnameallarray[$key].",";
				$filenameall.=$filenameallarray[$key].",";
			}
			else
			{
				$mapforPlmfile["newname"]=$newnameallarray[$key];
				$plmfileid=M("Plmfile")->where($mapforPlmfile)->getField("id");
				$mapforPlmfile["id"]=$plmfileid;
				M("Plmfile")->where($mapforPlmfile)->delete();
			}
		}
		
		
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		
		$date=date("Y-m-d");
		$handlehistory=$plminfo["handlehistory"].$_SESSION['loginUserName']."于".$date."编辑进度</br>------------------</br>"; 
		M("Project")->where("id=".$plminfo[id])->setField("handlehistory",$handlehistory);
	
		

		
			
			
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file']['name'][0]))
		{
			
			$file=$_FILES['file']['name'];
			$file_tmp=$_FILES['file']['tmp_name'];
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						//$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					
				
					$datafile[plmNumber]=$plminfo[id];
					$datafile[loadPerson]=$_SESSION["name"];
					$datafile[loadtime]=time();
					$datafile[plm]=$plminfo[title];
					$datafile[type]=$scheduleinfo[worktype];
					$datafile[classify]=$scheduleinfo[classify];
					$datafile[attribute]=$scheduleinfo[attribute];
					$datafile[worktype]=$scheduleinfo[worktype];
					$datafile[subworktype]=$scheduleinfo[subworktype];
					$datafile[newname] = $newname;
					$datafile[filename] = $filename;
					$datafile[title]=$filename;
					M("Plmfile")->add($datafile);
				
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			
		}
		$scheduleinfonew[file]=$newnameall;
		$scheduleinfonew[filerealname]=$filenameall;
		$plmscheduledaily[file]=$newnameall;
		$plmscheduledaily[filerealname]=$filenameall;
		
			
			
		$mapforPlmscheduledaily["dailyid"]=$_REQUEST["dailyid"];
		$mapforPlmscheduledaily["worktype"]=$_REQUEST["worktype"];
		$mapforPlmscheduledaily["subworktype"]=$_REQUEST["subworktype"];
		$plmscheduledailyid=M("Plmscheduledaily")->where($mapforPlmscheduledaily)->getField("id");
		
		
		
		
		
			
		$plmscheduledaily["id"]=$plmscheduledailyid;
		$plmscheduledaily["remark"]=$remarkarray;
		$plmscheduledaily["keeper"]=$keeperarray;
			
		M("Plmscheduledaily")->save($plmscheduledaily);
		
		
		$scheduleinfonew[id]=$plmscheduleid;
		$scheduleinfonew[remark]=$remarkarray;
		$scheduleinfonew[keeper]=$keeperarray;
		
		M("Plmschedule")->save($scheduleinfonew);	
			
			
		
			
		
		
		
		
	
		$this->success("操作成功");	
	}
	
	function dailytoexcel()
	{
		$dailyid=$_REQUEST["dailyid"];
		$mapforPlmschedule["dailyid"]=$dailyid;
		$mapforPlmschedule["status"]=1;
		$schedules=M("Plmscheduledaily")->where($mapforPlmschedule)->order("sort asc")->select();

		foreach($schedules as $key => $val)
		{
			if($schedules[$key][worktype]!=$schedules[$key-1][worktype])
			{
				$mapforPlmschedule[worktype]=$val[worktype];
				$schedules[$key][block]=1;
				$schedules[$key][rowspan]=M("Plmscheduledaily")->where($mapforPlmschedule)->count();
			}
			
			$schedules[$key]['photos']=explode(',',$val['file']);
			$schedules[$key]['photosrealname']=explode(',',$val['filerealname']);
			foreach($schedules[$key]['photos'] as $key1 => $val1)
			{
				$ext = strtolower(end(explode(".",basename($val1)))); 
				if(($ext=="png")||($ext=="jpg")||($ext=="jpeg")||($ext=="bmp")||($ext=="gif"))
				{
					$schedules[$key]['photostype'][$key1]="image";
				}
				else
				{
					$schedules[$key]['photostype'][$key1]="other";
				}
			}
		}
		$date=date("Y-m-d");
		
		
		
		
		$number=count($schedules);
		for($i=0;$i<$number;$i++)
		{
			if($schedules[$i]['worktype']!=$schedules[$i-1]['worktype'])
			{
				$data[$i]['worktype']=$schedules[$i]['worktype'];
			}
			else
			{
				$data[$i]['worktype']="";
			}
			
			$data[$i]['subworktype']=$schedules[$i]['subworktype'];
			$data[$i]['plantimebegin']=$schedules[$i]['plantimebegin'];
			$data[$i]['plantimeend']=$schedules[$i]['plantimeend'];
			$data[$i]['planquality']=$schedules[$i]['planquality'].$schedules[$i]['qualityunit'];
			$data[$i]['percent']=$schedules[$i]['percent'];
			$data[$i]['remark']=$schedules[$i]['remark'];	
		}
		
		$file="施工日报_".$schedules[0]["user"]."_".date("Y-m-d",$schedules[0]["create_time"]);
		$title="施工日报_".$schedules[0]["user"]."_".date("Y-m-d",$schedules[0]["create_time"]);
		$subtitle="施工日报_".$schedules[0]["user"]."_".date("Y-m-d",$schedules[0]["create_time"]);
		
		$th_array=array('专项','节点','计划开始','计划完成','工程量','今日实际','备注');
		
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	
	
	
	
	
	function progresstoexcel()
	{
		if($_REQUEST['title'])
		{
			$mapforproject['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['number'])
		{
			$mapforproject['number'] = array('like',"%".$_REQUEST['number']."%");
			$this->assign("number",$_REQUEST['number']);
		}
		$mapforproject['design_status'] = array("in","施工中,竣工待验收,项目待验收,完成验收");
		$mapforproject['_complex'] = $this->find5level($_SESSION[position],$map);
		
		$name = "Project";
		$model = D($name);
		
		
		$voList = $model->where($map)->order("create_time desc")->select();
		foreach($voList as $key => $val)
		{
			if($_REQUEST["moduletitle"]=="主项进度管理")$mapforPlmschedule[classify]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发进度管理")$mapforPlmschedule[classify]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计进度管理")$mapforPlmschedule[classify]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购进度管理")$mapforPlmschedule[classify]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工进度管理")$mapforPlmschedule[classify]="施工专项节点库";
			//$mapforPlmschedule[percent]=array("neq","100%");
			$mapforPlmschedule[plmid] = $val[id];
			$mapforPlmschedule[status] = 1;
			//$voList[$key][daily]=M("Plmschedule")->where($mapforPlmschedule)->order("create_time desc,id desc")->find();
			$voList[$key][daily]=M("Plmschedule")->where($mapforPlmschedule)->order("create_time asc,id asc")->select();
		}
		
	
		
		
		$number=count($voList);
		$x=0;
		for($i=0;$i<$number;$i++)
		{
			$data[$x]['title']=$voList[$i]['title']."（".$voList[$i]['design_status']."）";
			$x++;
			foreach($voList[$i][daily] as $key => $val)
			{
				$data[$x]['title']="";
				$data[$x]['worktype']=$val['worktype'];
				$data[$x]['subworktype']=$val['subworktype'];
				$data[$x]['plantimebegin']=$val['plantimebegin'];
				$data[$x]['plantimeend']=$val['plantimeend'];
				$data[$x]['planquality']=$val['planquality'].$val['qualityunit'].$val['plancount'];
				
				$data[$x]['realtimebegin']=$val['realtimebegin'];
				$data[$x]['realtimeend']=$val['realtimeend'];
				
				$data[$x]['percent']=$val['percent'];
				$data[$x]['realquality']=$val['realquality'].$val['qualityunit'].$val['realcount'];
				
				$data[$x]['remark']=$val['remark'];
				$x++;
			}
		}
		
		$file=$_REQUEST["moduletitle"]."_"."_".date("Y-m-d");
		$title=$_REQUEST["moduletitle"]."_"."_".date("Y-m-d");
		$subtitle=$_REQUEST["moduletitle"]."_"."_".date("Y-m-d");
		
		$th_array=array('项目名称','专项','节点','计划开始','计划完成','计划工程量','实际开始','实际完成','完成百分比','完成工程量','备注');
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>