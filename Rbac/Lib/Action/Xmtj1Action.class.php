<?php
class Xmtj1Action extends CommonAction {		

	/*
	function _initialize() {
        import('@.ORG.Util.Cookie');
    }
	*/
	function plmdetail() {
        
		$id=$_REQUEST["id"];
		$mapforPlmschedule["plmid"]=$id;
		$mapforPlmschedule["status"]=1;
		$maxplantimeend=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
		$this->assign('maxplantimeend',date("Y-m-d",strtotime($maxplantimeend)+24*60*60));
		
		$maxrealtimeend=M("Plmschedule")->where($mapforPlmschedule)->max("realtimeend");
		$this->assign('maxrealtimeend',date("Y-m-d",strtotime($maxrealtimeend)+24*60*60));
		
		
		
		$this->assign('noclose',$_REQUEST['noclose']);	
		
		
		$overduereasonarray=M("Plmoverduereason")->where("plmid=".$id)->select();
		foreach($overduereasonarray as $key => $val)
		{
			$overduereasons[$val["node"]]=$val["reason"];
		}
		$this->assign('overduereasons',$overduereasons);
		
		$today=date("Y-m-d");
		$this->assign('today',$today);	
		
		CommonAction::plmdetail();
		
		
	
    }
	
	public function index() {
		
		$_REQUEST['tab']=1;
		$this->assign('tab',$_REQUEST['tab']);	
	
		if(($_REQUEST['tab']=="")||($_REQUEST['tab']=="1")||($_REQUEST['tab']=="11")||($_REQUEST['tab']=="13")||($_REQUEST['tab']=="14"))
		{
			if(empty($_REQUEST["classify"]))
			{
				$classify="开发";
			}
			else
			{
				$classify=$_REQUEST["classify"];
			}
			$mapforWorktype["classify"]=array("like","%".$classify."%");
			$this->assign('classify',$classify);	
		
			$mapforWorktype["type"]=array("eq","1");
			$worktypes=M("Worktype")->where($mapforWorktype)->group("title")->order("sort asc")->field("title")->select();
			//查找项目所有城市
			if(($_REQUEST['tab']=="")||($_REQUEST['tab']=="1"))
			{
				$cities=M("Project")->group("city")->field("city")->select();
				$para="city";
				$title="城市";
			}
			if($_REQUEST['tab']=="11")
			{	
				if($classify=="开发")
				{
					$cities=M("Project")->group("kaifauser")->field("kaifauser")->select();
					$para="kaifauser";
				}
				if($classify=="设计")
				{
					$cities=M("Project")->group("shejiuser")->field("shejiuser")->select();
					$para="shejiuser";
				}
				if($classify=="采购")
				{
					$cities=M("Project")->group("caigouuser")->field("caigouuser")->select();
					$para="caigouuser";
				}
				if($classify=="施工")
				{
					$cities=M("Project")->group("gongchenguser")->field("gongchenguser")->select();
					$para="gongchenguser";
				}
				if($classify=="主项")
				{
					$cities=M("Project")->group("gongchenguser")->field("gongchenguser")->select();
					$para="gongchenguser";
				}
				$title="人员";
			}
			if($_REQUEST['tab']=="13")
			{
				$cities=M("Project")->group("invester")->field("invester")->select();
				$para="invester";
				$title="合作模式";
			}
			if($_REQUEST['tab']=="14")
			{
				$cities=M("Project")->group("capacity")->field("capacity")->select();
				$para="capacity";
				$title="装机容量";
			}
			foreach($cities as $key => $val)
			{
				$cities[$key]["title"] = $val[$para];
				$mapforProject[$para] = $val[$para];
				$cities[$key][projects]=M("Project")->where($mapforProject)->field("id")->select();
				$plmids="";
				foreach($cities[$key][projects] as $key1 => $val1)
				{
					$plmids.=$val1["id"].",";
				}
				foreach($worktypes as $key1 => $val1)
				{
					$mapforPlmschedule["plmid"]=array("in",$plmids);
					$mapforPlmschedule["classify"]=array("like","%".$classify."%");
					$mapforPlmschedule["worktype"]=array("eq",$val1["title"]);
					$mapforPlmschedule["percent"]=array("eq","100%");
					
					$cities[$key]["detail"][$key1]["title"]=$val1["title"];
					$cities[$key]["detail"][$key1]["realtimelength"]=M("Plmschedule")->where($mapforPlmschedule)->avg("realtimelength");
					if(empty($cities[$key]["detail"][$key1]["realtimelength"]))
					{
						$mapforPlmschedule1["city"]=array("eq",$val["city"]);
						$mapforPlmschedule1["title"]=array("eq",$val1["title"]);
						$status=M("Worktypebook")->where($mapforPlmschedule1)->getField("status");
						if($status=="1")
						{
							$cities[$key]["detail"][$key1]["realtimelength"]="0.1";
						}
					}
				}
				
			}
			$this->assign('title',$title);
			$this->assign('cities',$cities);
			$this->assign('worktypes',$worktypes);
			if($_SESSION["app"])
			{
				$this->display("indexapp");
			}
			else
			{
				$this->display("index");
			}
			return;
		}
		if(($_REQUEST['tab']=="6")||($_REQUEST['tab']=="7")||($_REQUEST['tab']=="8")||($_REQUEST['tab']=="9")||($_REQUEST['tab']=="10"))
		{
			//$mapforSettingreason["type"]=array("eq","1");
			$reasons=M("Settingreason")->where($mapforSettingreason)->group("name")->field("name")->select();
			foreach($reasons as $key => $val)
			{
				if($_REQUEST['tab']=="6")
				{
					$mapforPlmoverduereason["classify"] = array("like","%开发%");
				}
				if($_REQUEST['tab']=="7")
				{
					$mapforPlmoverduereason["classify"] = array("like","%设计%");
				}
				if($_REQUEST['tab']=="8")
				{
					$mapforPlmoverduereason["classify"] = array("like","%采购%");
				}
				if($_REQUEST['tab']=="9")
				{
					$mapforPlmoverduereason["classify"] = array("like","%施工%");
				}
				if($_REQUEST['tab']=="10")
				{
					$mapforPlmoverduereason["classify"] = array("like","%主项%");
				}
				$mapforPlmoverduereason["reason"] = $val["name"];
				$reasons[$key][reasoncount]=M("Plmoverduereason")->where($mapforPlmoverduereason)->count();
			}
			$this->assign('reasons',$reasons);
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="16")||($_REQUEST['tab']=="17")||($_REQUEST['tab']=="18")||($_REQUEST['tab']=="19")||($_REQUEST['tab']=="20"))
		{
			
			if($_REQUEST['tab']=="16")
			{
				$classify="开发";
			}
			if($_REQUEST['tab']=="17")
			{
				$classify="设计";
			}
			if($_REQUEST['tab']=="18")
			{
				$classify="采购";
			}
			if($_REQUEST['tab']=="19")
			{
				$classify="施工";
			}
			if($_REQUEST['tab']=="20")
			{
				$classify="主项";
			}
			$mapforWorktype["classify"] = array("like","%".$classify."%");
			$mapforWorktype["type"]=array("eq","1");
			$worktypes=M("Worktype")->where($mapforWorktype)->group("title")->order("sort asc")->field("title")->select();
			foreach($worktypes as $key => $val)
			{
				$mapforPlmschedule["classify"]=array("like","%".$classify."%");
				$mapforPlmschedule["worktype"]=array("eq",$val["title"]);
				$mapforPlmschedule["percent"]=array("eq","100%");
				$worktypes[$key]["realtimelength"]=M("Plmschedule")->where($mapforPlmschedule)->avg("realtimelength");
			}
			$this->assign('worktypes',$worktypes);
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="26")||($_REQUEST['tab']=="27")||($_REQUEST['tab']=="28")||($_REQUEST['tab']=="29")||($_REQUEST['tab']=="30"))
		{
			if($_REQUEST['tab']=="26")
			{
				$classify="开发";
			}
			if($_REQUEST['tab']=="27")
			{
				$classify="设计";
			}
			if($_REQUEST['tab']=="28")
			{
				$classify="采购";
			}
			if($_REQUEST['tab']=="29")
			{
				$classify="施工";
			}
			if($_REQUEST['tab']=="30")
			{
				$classify="主项";
			}
			$mapforWorktype["classify"] = array("like","%".$classify."%");
			$mapforWorktype["type"]=array("eq","1");
			$worktypes=M("Worktype")->where($mapforWorktype)->group("title")->order("sort asc")->field("title")->select();
			foreach($worktypes as $key => $val)
			{
				$mapforPlmschedule["classify"]=array("like","%".$classify."%");
				$mapforPlmschedule["worktype"]=array("eq",$val["title"]);
				$mapforPlmschedule["percent"]=array("eq","100%");
				$mapforPlmschedule["warning"]=array("eq","");
				$worktypes[$key]["nowarningcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
				$mapforPlmschedule["warning"]=array("eq","1");
				$worktypes[$key]["warningcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
				$worktypes[$key]["nowarningpercent"]=round(100*$worktypes[$key]["nowarningcount"]/($worktypes[$key]["nowarningcount"]+$worktypes[$key]["warningcount"]),0);
			}
			$this->assign('worktypes',$worktypes);
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="2"))
		{
			//预警 专项
			$mapforPlmprewarning1[prewarning]=1;
			$worktypes=M("Plmwarning")->where($mapforPlmprewarning1)->group("worktype")->field("worktype")->select();
			foreach($worktypes as $key => $val)
			{
				$mapforPlmprewarning1[prewarning]=1;
				$mapforPlmprewarning1[worktype] = $val[worktype];
				$worktypes[$key][count1]=M("Plmwarning")->where($mapforPlmprewarning1)->count();
				
				$plmarray=M("Plmwarning")->where($mapforPlmprewarning1)->group("plmid")->field("plmid")->select();
				foreach($plmarray as $key1 => $val1)
				{
					$plmids.=$val1[plmid].",";
				}
				$worktypes[$key][plmids]=$plmids;
			}
			$this->assign('worktypes',$worktypes);	
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="3"))
		{
			//预警 项目
			$mapforPlmprewarning2[prewarning]=1;
			$projects=M("Plmwarning")->where($mapforPlmprewarning2)->group("plmid")->field("plmid")->select();
			foreach($projects as $key => $val)
			{
				$mapforPlmprewarning2[prewarning]=1;
				$mapforPlmprewarning2[plmid] = $val[plmid];
				$projects[$key][count1]=M("Plmwarning")->where($mapforPlmprewarning2)->count();
				$mapforProject[id] = $val[plmid];
				$projects[$key][plminfo]=M("Project")->where($mapforProject)->find();
			}
			$this->assign('projects',$projects);	
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="4"))
		{
			//报警 专项
			$mapforPlmwarning1[warning]=1;
			$worktypes=M("Plmwarning")->where($mapforPlmwarning1)->group("worktype")->field("worktype")->select();
			foreach($worktypes as $key => $val)
			{
				$mapforPlmwarning1[warning]=1;
				$mapforPlmwarning1[worktype] = $val[worktype];
				$worktypes[$key][count1]=M("Plmwarning")->where($mapforPlmwarning1)->count();
				
				$plmarray=M("Plmwarning")->where($mapforPlmwarning1)->group("plmid")->field("plmid")->select();
				foreach($plmarray as $key1 => $val1)
				{
					$plmids.=$val1[plmid].",";
				}
				$worktypes[$key][plmids]=$plmids;
				
			}
			$this->assign('worktypes',$worktypes);	
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="5"))
		{
			//报警 项目
			$mapforPlmwarning2[warning]=1;
			$projects=M("Plmwarning")->where($mapforPlmwarning2)->group("plmid")->field("plmid")->select();
			foreach($projects as $key => $val)
			{
				$mapforPlmwarning2[warning]=1;
				$mapforPlmwarning2[plmid] = $val[plmid];
				$projects[$key][count1]=M("Plmwarning")->where($mapforPlmwarning2)->count();
				$mapforProject[id] = $val[plmid];
				$projects[$key][plminfo]=M("Project")->where($mapforProject)->find();
			}
			$this->assign('projects',$projects);	
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="7"))
		{
			if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
			$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
			else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
			else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
			$this->assign('timebegin', $_REQUEST['timebegin']);
			$this->assign('timeend', $_REQUEST['timeend']);
			
			if($_REQUEST[keyword])
			{
				$map['title'] = array('like',"%".$_REQUEST['keyword']."%");
				$this->assign('keyword',$_REQUEST['keyword']);	
			}
			
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$map[design_status]=array("in","完成施工,完成验收");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'last_time',false);
			}
		
			$this->display();
			return;
		}
		if(0)//$_REQUEST['tab']=="10"
		{
			$mapforWorktype[type]=1;
			$worktypes=M("Worktype")->where($mapforWorktype)->group("title")->field("title")->select();
			foreach($worktypes as $key => $val)
			{
				$mapforPlmwarning[worktype] = $val[title];
				//$mapforPlmwarning[percent] = array(array("neq","0%"),array("neq","100%"),array("neq",""),"and");
				//$mapforPlmwarning[plmid]=array("in",$ingplmidstr);
				$mapforPlmwarning[status]=1;
				$temp=M("Plmschedule")->where($mapforPlmwarning)->group("plmid")->select();
				$worktypes[$key][count1]=0;
				foreach($temp as $key1 => $val1)
				{
					$mapforPlmwarning1[worktype] = $val[title];
					$mapforPlmwarning1[plmid]=array("eq",$val1[plmid]);
					$mapforPlmwarning1[status]=1;
					$mapforPlmwarning1[percent] = array("like","%%");
					$temp1=M("Plmschedule")->where($mapforPlmwarning1)->count();
					$mapforPlmwarning1[percent]=array("eq","100%");
					$temp2=M("Plmschedule")->where($mapforPlmwarning1)->count();
					$mapforPlmwarning1[percent]=array("eq","");
					$temp3=M("Plmschedule")->where($mapforPlmwarning1)->count();
					if(($temp1!=$temp2)&&($temp1!=$temp3))
					{
						$worktypes[$key][count1]++;
					}
				}
			}
			$this->assign('worktypes',$worktypes);	
			$this->display();
			return;
		}
	}
	public function indexproject() 
	{
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}
		$name = "Project";
		$model = D($name);
		$voList = $model->where($map)->select();
		if(!$_REQUEST["plmid"])
		{
			$_REQUEST["plmid"]=$voList[0]["id"];
		}
		
		$plminfo=M("Project")->where("id=".$_REQUEST["plmid"])->find();
		$plminfo["discusscount"]=M("Plmdiscuss")->where("plmid=".$_REQUEST["plmid"])->count();
		$this->assign("plminfo", $plminfo);
		$this->assign("plmid",$_REQUEST["plmid"]);
		
		if(empty($_REQUEST['classify']))
		{
			$_REQUEST['classify'] = "开发";
		}
		if(false!==strstr($_REQUEST["classify"],"开发")) $classify = "开发";
		if(false!==strstr($_REQUEST["classify"],"施工")) $classify = "施工"; 
		if(false!==strstr($_REQUEST["classify"],"设计")) $classify = "设计";
		if(false!==strstr($_REQUEST["classify"],"采购")) $classify = "采购";
		if(false!==strstr($_REQUEST["classify"],"主项")) $classify = "主项";
		$this->assign("classify",$_REQUEST["classify"]);
		
		$mapforPlmschedule["plmid"] = array("eq",$plminfo["id"]);
		$mapforPlmschedule["classify"] = array("like","%".$classify."%");
		$worktypes=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		
		foreach($worktypes as $key => $val)
		{
			$worktypes[$key]["title"]=$val["worktype"];
			if($val["realtimeend"]>$val["plantimeend"])
			{
				$worktypes[$key]["warning"]=1;
			}
			if(empty($val["plantimeend"])&&(date("Y-m-d")>$val["plantimeend"]))
			{
				$worktypes[$key]["warning"]=1;
			}
		}
		$this->assign("worktypes", $worktypes);
		
		
		
		
		
		
		
		$this->assign('list', $voList);
		$this->display("indexproject");
		
	}
	public function indexdept() 
	{
		$map['name'] = array(array("like","%工程一部%"),array("like","%工程二部%"),array("like","%开发%"),array("like","%供应链%"),array("like","%设计%"),"or");
		$map['status'] = 1;
		$name = "Dept";
		$model = D($name);
		$voList = $model->where($map)->select();
		if(!$_REQUEST["deptid"])
		{
			$_REQUEST["deptid"]=$voList[0]["id"];
		}
		
		$deptinfo=M("Dept")->where("id=".$_REQUEST["deptid"])->find();
		$this->assign("deptinfo", $deptinfo);
		$this->assign("deptid",$_REQUEST["deptid"]);
			
		if(false!==strstr($deptinfo["name"],"工程")) $classify = "施工"; 
		if(false!==strstr($deptinfo["name"],"开发")) $classify = "开发";
		if(false!==strstr($deptinfo["name"],"设计")) $classify = "设计";
		if(false!==strstr($deptinfo["name"],"供应")) $classify = "采购";
		if(false!==strstr($deptinfo["name"],"主项")) $classify = "主项";
		$mapforWorktype["classify"] = array("like","%".$classify."%");
		$mapforWorktype["type"]=array("eq","1");
		$worktypes=M("Worktype")->where($mapforWorktype)->group("title")->order("sort asc")->field("title")->select();
		
		
		$userarray=M("User")->where("department=".$_REQUEST["deptid"])->field("nickname")->select();
		foreach($userarray as $key => $val)
		{
			$subordinates.=$val["nickname"].",";
		}
		$where["kaifa"]=array("in",$subordinates);
		$where["kaifauser"]=array("in",$subordinates);
		$where["sheji"]=array("in",$subordinates);
		$where["shejiuser"]=array("in",$subordinates);
		$where["caigou"]=array("in",$subordinates);
		$where["caigouuser"]=array("in",$subordinates);
		$where["gongcheng"]=array("in",$subordinates);
		$where["gongchenguser"]=array("in",$subordinates);
		$where["shangwu"]=array("in",$subordinates);
		$where["shangwuuser"]=array("in",$subordinates);
		$where['_logic'] = 'or';
		$mapforProject['_complex'] = $where;
		$plmarray=M("Project")->where($mapforProject)->field("id")->select();
		foreach($plmarray as $key => $val)
		{
			$plmids.=$val["id"].",";
		}
		
		foreach($worktypes as $key => $val)
		{
			$mapforPlmschedule["plmid"]=array("in",$plmids);
			$mapforPlmschedule["classify"]=array("like","%".$classify."%");
			$mapforPlmschedule["worktype"]=array("eq",$val["title"]);
			$mapforPlmschedule["percent"]=array("neq","100%");
			$worktypes[$key]["ingcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$mapforPlmschedule["percent"]=array("eq","100%");
			$worktypes[$key]["finishedcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$mapforPlmschedule["warning"]=array("eq","");
			$worktypes[$key]["nowarningcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$mapforPlmschedule["warning"]=array("eq","1");
			$worktypes[$key]["warningcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$worktypes[$key]["nowarningpercent"]=round(100*$worktypes[$key]["nowarningcount"]/($worktypes[$key]["nowarningcount"]+$worktypes[$key]["warningcount"]),0);
		}
		$this->assign("worktypes", $worktypes);
			
		
		foreach($voList as $key => $val)
		{
			$voList[$key]['usercount']=M("User")->where("department=".$val["id"])->count();
		}		
				
				
		$this->assign('list', $voList);
		$this->display("indexdept");
		
	}
	
	public function indexyear() 
	{
		if(empty($_REQUEST["classify"]))
		{
			$_REQUEST["classify"]="开发";
		}
		$classify=$_REQUEST["classify"];
		$this->assign("classify", $classify);
		$mapforWorktype["classify"] = array("like","%".$classify."%");
		$mapforWorktype["type"]=array("eq","1");
		$worktypes=M("Worktype")->where($mapforWorktype)->group("title")->order("sort asc")->field("title")->select();
		
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$mapforProject['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$mapforProject['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$mapforProject['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		$plmarray=M("Project")->where($mapforProject)->field("id")->select();
		foreach($plmarray as $key => $val)
		{
			$plmids.=$val["id"].",";
		}
		
		foreach($worktypes as $key => $val)
		{
			$mapforPlmschedule["plmid"]=array("in",$plmids);
			$mapforPlmschedule["classify"]=array("like","%".$classify."%");
			$mapforPlmschedule["worktype"]=array("eq",$val["title"]);
			$mapforPlmschedule["percent"]=array("neq","100%");
			$worktypes[$key]["ingcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$mapforPlmschedule["percent"]=array("eq","100%");
			$worktypes[$key]["finishedcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$mapforPlmschedule["warning"]=array("eq","");
			$worktypes[$key]["nowarningcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$mapforPlmschedule["warning"]=array("eq","1");
			$worktypes[$key]["warningcount"]=M("Plmschedule")->where($mapforPlmschedule)->count();
			$worktypes[$key]["nowarningpercent"]=round(100*$worktypes[$key]["nowarningcount"]/($worktypes[$key]["nowarningcount"]+$worktypes[$key]["warningcount"]),0);
		}
		$this->assign("worktypes", $worktypes);
			
		
		foreach($voList as $key => $val)
		{
			$voList[$key]['usercount']=M("User")->where("department=".$val["id"])->count();
		}		
				
				
		$this->assign('list', $voList);
		$this->display("indexyear");
		
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
            $p = new Page($count, 200);//$listRows
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
				$voList[$key]['finishs']=explode(',',$val['finish']);
				$voList[$key]['finishsfilename']=explode(',',$val['finishfilename']);
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
	
	
	function add1() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->display("add1");
	}	
	
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
		$date=date('Y-m-d H:i:s');
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
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
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->finish=$newnameall;
			$model->finishfilename=$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了验收报告</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$model->finish_time=time();
		$model->design_status="完成验收";
		$list = $model->save();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			//$this->success('新增成功!');
			$this->redirect('index',"tab=7");
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
		$this->assign('orgdata', $vo);
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
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
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
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->budgetfinal=$newnameall;
			$model->budgetfinalfilename=$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了决算单</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$list = $model->save();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			//$this->success('新增成功!');
			$this->redirect('index',"tab=8");
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}	
	
	function prewarning()
	{
		//预警 专项
		$mapforPlmprewarning1[prewarning]=1;
		if($_REQUEST[id])
		{
			$mapforPlmprewarning1[plmid]=$_REQUEST[id];
		}
		if($_REQUEST[ids])
		{
			$mapforPlmprewarning1[plmid]=array("in",$_REQUEST[ids]);
		}
		if($_REQUEST[worktype])
		{
			$mapforPlmprewarning1[worktype]=array("in",$_REQUEST[worktype]);
		}
		$volist=M("Plmwarning")->where($mapforPlmprewarning1)->select();
		foreach($volist as $key => $val)
		{
			$volist[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
		}
		$this->assign('list', $volist);
		$this->display();
	}
	
	function warning()
	{
		//预警 专项
		$mapforPlmprewarning1[warning]=1;
		if($_REQUEST[id])
		{
			$mapforPlmprewarning1[plmid]=$_REQUEST[id];
		}
		if($_REQUEST[ids])
		{
			$mapforPlmprewarning1[plmid]=array("in",$_REQUEST[ids]);
		}
		if($_REQUEST[worktype])
		{
			$mapforPlmprewarning1[worktype]=array("in",$_REQUEST[worktype]);
		}
		$volist=M("Plmwarning")->where($mapforPlmprewarning1)->select();
		foreach($volist as $key => $val)
		{
			$volist[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
		}
		$this->assign('list', $volist);
		$this->display();
	}
	public function findposition() 
	{	$lat=json_encode($_REQUEST[lat]);
	    $lng=json_encode($_REQUEST[lng]);
		$this->assign('lat', $lat);
		$this->assign('lng', $lng);
		$this->display();
	}
	
	
	function overdueedit() 
	{
        $name = "Plmoverduereason";
        $model = M($name);
		$plmid = $_REQUEST ["plmid"];
        $node = $_REQUEST ["node"];
		$worktype = $_REQUEST ["worktype"];
		$classify = $_REQUEST ["classify"];
		$map["plmid"]=$plmid;
		$map["node"]=$node;
		$map["worktype"]=$worktype;
        $vo = $model->where($map)->find();
        $this->assign('vo', $vo);
		
		$this->assign('plmid', $plmid);
		$this->assign('node', $node);
		$this->assign('worktype', $worktype);
		$this->assign('classify', $classify);
		
		$reasons=M('Settingreason')->select();
		$this->assign('reasons', $reasons);
		
        $this->display();
    }
	function overdueupdate() 
	{
        //B('FilterString');
        $name = "Plmoverduereason";
        $model = D($name);
		$plmid = $_REQUEST ["plmid"];
        $node = $_REQUEST ["node"];
		$worktype = $_REQUEST ["worktype"];
		$map["plmid"]=$plmid;
		$map["node"]=$node;
		$map["worktype"]=$worktype;
        $vo = $model->where($map)->find();
		
		if(empty($vo))
		{
			$data["plmid"] = $_REQUEST ["plmid"];
			$data["classify"] = $_REQUEST ["classify"];
			$data["worktype"] = $_REQUEST ["worktype"];
			$data["node"] = $_REQUEST ["node"];
			$data["reasonid"] = $_REQUEST ["reasonid"];
			$data["reason"] = M("Settingreason")->where("id=".$_REQUEST ["reasonid"])->getField("name");
			$data["remark"] = $_REQUEST ["remark"];
			$data["user"] = $_SESSION ["nickname"];
			$data["create_time"] = time();
			$data["ctime"] = date("Y-m-d");
			M("Plmoverduereason")->add($data);
		}
        else
		{
			$data["id"] = $vo ["id"];
			$data["classify"] = $_REQUEST ["classify"];
			$data["reasonid"] = $_REQUEST ["reasonid"];
			$data["reason"] =  M("Settingreason")->where("id=".$_REQUEST ["reasonid"])->getField("name");
			$data["remark"] = $_REQUEST ["remark"];
			$data["user"] = $_SESSION ["nickname"];
			$data["update_time"] = time();
			M("Plmoverduereason")->save($data);
		}
		$this->success('编辑成功');
    }
	
	
	
	//取消
	public function setcancel() {
        $name = "Worktypebook";
        $model = M($name);
     
		$id = $_REQUEST ["id"];
		
		$arr=explode("_",$id);
		
		$condition["city"] = $arr[0];
		$condition["title"] = $arr[1];
		$data=M("Worktypebook")->where($condition)->find();
		if(empty($data))
		{
			
		}
		else
		{
			$condition["id"] = $data["id"];
			$model->where($condition)->setField("status","0");
		}
		
		$this->success('取消成功！');
            
     
        $this->forward();
    }
	public function setsubmit() {
		
	
        $name = "Worktypebook";
        $model = D($name);
        
          
		$id = $_REQUEST ["id"];
		
		$arr=explode("_",$id);
		
		$condition["city"] = $arr[0];
		$condition["title"] = $arr[1];
		$data=M("Worktypebook")->where($condition)->find();
		if(empty($data))
		{
			$data["city"] = $arr[0];
			$data["title"] = $arr[1];
			$data["status"] = 1;
			M("Worktypebook")->add($data);
		}
		else
		{
			$model->where($condition)->setField("status","1");
		}
		
		$this->success('取消成功！');
            
     
        $this->forward();
    }
}
?>