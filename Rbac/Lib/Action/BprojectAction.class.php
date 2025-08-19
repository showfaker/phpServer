<?php
class BprojectAction extends CommonAction {
	
	
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
		if($_REQUEST['title'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
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
		
		
        if(!empty($_REQUEST['tab']))
		{
			$this->assign('tab',$_REQUEST['tab']);	
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
			//$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			$mapforproject['kaifauser|shejiuser|caigouuser|shangwuuser|gongchenguser'] = $_SESSION["nickname"];
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
			if($_REQUEST['moduletitle'])
			{
				$mapforplmschedule['classify'] = array('like',"%".str_replace("进度管理","",$_REQUEST['moduletitle'])."%");
				$this->assign("moduletitle",$_REQUEST['moduletitle']);
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
			if($_REQUEST['moduletitle'])
			{
				$mapforplmschedule['classify'] = array('like',"%".str_replace("进度管理","",$_REQUEST['moduletitle'])."%");
				$this->assign("moduletitle",$_REQUEST['moduletitle']);
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
			if(($_REQUEST["moduletitle"])&&(($_REQUEST["tab"]=="")||($_REQUEST["tab"]=="1")))
			{
				$map['classify'] = $_REQUEST["moduletitle"];
			}
			//$map['user'] = array("in",$this->find5levelusers($_SESSION[position]));
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			//$mapforproject["engineeringmanage|supervisor|drawing_user|budget_user|designer|projectmanager|way|waysub|areatype|areadetail|draw_user|waysubother"]=array("eq",$_SESSION[name]);
			
			if($_REQUEST['plmid'])
			{
				$mapforproject['id'] = array('eq',$_REQUEST['plmid']);
				$this->assign("plmid",$_REQUEST['plmid']);
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
		
		if(($_REQUEST['tab']==5)||($_REQUEST['tab']==2))
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
			}
			if($_REQUEST[tab]=="5")
			{
				
				$date=date("Y-m-d");
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
			if($_REQUEST[tab]=="2")
			{
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
					$mapforPlmschedule[subworktype] = "到货情况";
					//$voList[$key][daily]=M("Plmschedule")->where($mapforPlmschedule)->order("create_time desc,id desc")->find();
					$voList[$key][daily]=M("Plmschedule")->where($mapforPlmschedule)->order("create_time asc,id asc")->select();
					/*
					foreach($voList as $key => $val)
					{
						$voList[$key]['enters']=explode(',',$val['enter']);
						$voList[$key]['entersfilename']=explode(',',$val['enterfilename']);
					}
					*/
				}
			}
			if($_REQUEST[tab]=="6")
			{
				foreach($voList as $key => $val)
				{
					//$mapforPlmschedule[percent]=array("neq","100%");
					if($_REQUEST["moduletitle"])
					{
						$mapforPlmschedule['classify'] = $_REQUEST["moduletitle"];
					}
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
	
	
	
	
	
	
	
	public function dailyadd() 
	{
		$this->assign("moduletitle",$_REQUEST['moduletitle']);
		$this->display();
	}
	
	
	public function dailyadd1() 
	{
		$this->assign("moduletitle",$_REQUEST['moduletitle']);
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
	
	public function plmlist() {
		$model=M("Project");
		if($_REQUEST[webid]=="dailyadd")//待施工
		{
			$mapforproject[design_status]=array("in","待施工,施工中,施工完成,完成验收");//,施工完成,完成验收
			$mapforproject[advance]=array("exp","is null");
		}
	
		
		
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$mapforproject["city|area"]=array("like","%".urldecode($_REQUEST[city])."%");
		}
		
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$mapforproject['user|projectmanager']=array("like","%".urldecode($_REQUEST[user])."%");
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		}
		else
		{
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		}
		
		

		
		//programlistdaily
		if(($_REQUEST["webid"]=="programlistdaily")||($_REQUEST["webid"]=="programlist8"))
		{
			$mapforproject["projecttype"]=array("like","%%");
		}
		if((!empty($_REQUEST[projecttypecurrentchoice]))&&($_REQUEST[projecttypecurrentchoice]!=null)&&($_REQUEST[projecttypecurrentchoice]!="null")&&($_REQUEST[projecttypecurrentchoice]!="undefined"))
		{
			$mapforproject["projecttype"]=$_REQUEST["projecttypecurrentchoice"];
		}
		
		
		$date=date("Y-m-d");
		
		
		$volist=$model->where($mapforproject)->order('create_time desc')->limit(1000)->select();//->limit(10*$page. ',10')
		foreach($volist as $key => $val)
		{
			$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			$volist[$key]["worktype_status"]="";
			$volist[$key]["plan_status"]="";
			
			if($_REQUEST["moduletitle"]=="主项进度管理"){$worktype_status=$val["worktype_status1"];$plan_status=$val["plan_status1"];}
			if($_REQUEST["moduletitle"]=="开发进度管理"){$worktype_status=$val["worktype_status2"];$plan_status=$val["plan_status2"];}
			if($_REQUEST["moduletitle"]=="设计进度管理"){$worktype_status=$val["worktype_status3"];$plan_status=$val["plan_status3"];}
			if($_REQUEST["moduletitle"]=="采购进度管理"){$worktype_status=$val["worktype_status4"];$plan_status=$val["plan_status4"];}
			if($_REQUEST["moduletitle"]=="施工进度管理"){$worktype_status=$val["worktype_status5"];$plan_status=$val["plan_status5"];}
			if($worktype_status=="")$worktype_status="节点未设置";
			if($plan_status=="")$plan_status="计划未设置";
			
			
			if((false!==strpos($worktype_status,"待审核"))||(false!==strpos($worktype_status,"退回")||($worktype_status=="节点未设置")))
			{
				$volist[$key]["worktype_status"]=" [".$worktype_status."，无法上传进度]";
			}
			if((false!==strpos($plan_status,"计划待审核"))||(false!==strpos($plan_status,"计划审核退回")||($plan_status=="计划未设置")))
			{
				$volist[$key]["plan_status"]=" [".$plan_status."，无法上传进度]";
			}
			
			
			if($val[design_status]=="施工中")
			{
				$mapforPlmdaily["plmid"]=$val["id"];
				$mapforPlmdaily["subworktype"]=array("neq","工作汇报");
				$status=M("Plmdaily")->where($mapforPlmdaily)->order("id desc")->getField("subworktype");
				if($status)
				{
					$volist[$key][design_status1]=$volist[$key][design_status]."-".$status;
				}
			}
		}
		if(1)
		{
			foreach($volist as $key => $val)
			{
				$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			}
		}
		/*
		项目名称：某某项目（点击当前项目直接跳转项目日志明细页面）
		区域：南通+具体区域
		施工中---进场前准备（完成30%）如果当前环节完成100%，则显示待土建施工
		时间限制：当前环节日期
		是否超期：否
		*/
		if(($_REQUEST[webid]=="programlistdaily"))
		{
			foreach($volist as $key => $val)
			{
				$volist[$key][invester]=$val[invester];
				$volist[$key][ctime]="区域:".$val[area];
				
				$mapforPlmschedule[percent]=array("neq","100%");
				$mapforPlmschedule[plmid] = $val[id];
				$mapforPlmschedule[status]=1;
				$voList[$key][schedule]=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
				if(!empty($voList[$key][schedule]))
				{
					if(empty($voList[$key][schedule][percent]))
					{
						$voList[$key][schedule][percent]="0%";
					}
					
					
					
					$mapforPlmschedule_1[plmid] = $val[id];
					$mapforPlmschedule_1[status]=1;
					$schedules=M("Plmschedule")->where($mapforPlmschedule_1)->order("sort asc")->field("plantimelength,percent,weight")->select();
					$alldaycount=0;
					$percent=0;
					foreach($schedules as $key1 => $val1)
					{
						$alldaycount+=$val1["plantimelength"];
						$percent+=$val1["plantimelength"]*$val1["percent"]/100;
					}
					$progressbyall=round(100*$percent/$alldaycount,2)."%";
					
					$mapforPlmschedule_2[plmid] = $val[id];
					$mapforPlmschedule_2[status]=1;
					$mapforPlmschedule_2[worktype]=$voList[$key][schedule][worktype];
					$schedules=M("Plmschedule")->where($mapforPlmschedule_2)->order("sort asc")->field("plantimelength,percent,weight")->select();
					$alldaycount=0;
					$percent=0;
					foreach($schedules as $key1 => $val1)
					{
						$alldaycount+=$val1["plantimelength"];
						$percent+=$val1["plantimelength"]*$val1["percent"]/100;
					}
					$progressbyworktype=round(100*$percent/$alldaycount,2)."%";
					
					$volist[$key][design_status1]="完成情况：总体完成".$progressbyall;
					$volist[$key][design_status2]="环节：".$voList[$key][schedule][worktype]."（完成".$progressbyworktype."）";
					$volist[$key][design_status]="节点：".$voList[$key][schedule][subworktype]."（完成".$voList[$key][schedule][percent]."）";
					
					
					$volist[$key][user]="时间限制：".$voList[$key][schedule][plantimeend];
					$volist[$key][remind]="时间限制：".$voList[$key][schedule][plantimeend];
					if(date("Y-m-d")>$voList[$key][schedule][plantimeend])
					{
						$volist[$key][remark]="是否超期：<font style='color:red'>是</font>";
					}
				}
				else
				{
					$volist[$key][design_status]="施工完成";
					$volist[$key][user]="时间限制：无";
					$volist[$key][remind]="时间限制：无";
					$volist[$key][remark]="是否超期：无";
				}
				
				
			}
		}
		echo json_encode($volist);
	}
	
	public function worktypelist() {
		$plmid=$_REQUEST[plmid];
		$plminfo=M("Project")->where("id=$plmid")->find();
		
		if($_REQUEST["moduletitle"]=="主项进度管理")$map[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发进度管理")$map[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计进度管理")$map[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购进度管理")$map[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工进度管理")$map[classify]="施工专项节点库";
		if($_REQUEST[account]!="admin")
		{
			$map[plmid]=$plmid;
			$map[status]=1;
			$map[percent]=array("neq","100%");
			if(false!==strstr($_SESSION['role'],"负责人"))
			{
				$volist1[0][id]="1";
				$volist1[0][title]="工作汇报";
				$volist1[0][planpercent]="不需填写完成量";
				$volist1[0][currentpercent]="";
				$volist1[$i][plantimeend]="";
				$volist1[$i][plancount]="";
				$volist1[$i][realcount]="";
				$i=1;
			}
			else
			{
				/*
				$map[worktype]=array("eq","xxx");
				$volist1[0][id]="1";
				$volist1[0][title]="工作汇报";
				$volist1[0][planpercent]="不需填写完成量";
				$volist1[0][currentpercent]="";
				$i=1;
				*/
				$i=0;
			}
			$schedule=M("Plmschedule")->where($map)->group("worktype")->order("sort asc")->limit(1)->select();//新增->limit(1)
			/*
			$volist1[0][id]="1";
			$volist1[0][title]="工作汇报";
			$volist1[0][planpercent]="不需填写完成量";
			$i=1;
			*/
			$date=date("Y-m-d");
			foreach($schedule as $key1 => $val1)
			{
				$map[worktype]=$val1[worktype];
				$volist=M("Plmschedule")->where($map)->order("sort asc")->select();
				foreach($volist as $key => $val)
				{
					$mapforworktype[title]=$val[subworktype];
					$mapforworktype[type]=2;
					//$mapforworktype[projecttype]=$plminfo[projecttype];
					$worktypeinfo=M("Worktype")->where($mapforworktype)->find();
					$ifpingxing=$worktypeinfo["parallel"];
					$user=$worktypeinfo["user"];
					
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
					
					
					if(($key==0)&&($ifpingxing!="是")&&($val[subworktype]!="墙体拆除")&&($val[worktype]!="相关配套工程"))
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						
						$volist1[$i][plantimeend]=$val[plantimeend];
						$volist1[$i][plancount]=$val[plancount];
						$volist1[$i][realcount]=$val[realcount];
						
						/*
						if($user!=$_SESSION["role"])
						{
							//新加的
							$volist1[0][id]="1";
							$volist1[0][title]="工作汇报";
							$volist1[0][planpercent]="不需填写完成量";
							$volist1[0][currentpercent]="";
						}
						*/
						
						$i++;
						break;
					}
					else if(($ifpingxing!="是")&&($val[subworktype]!="墙体拆除")&&($val[worktype]!="相关配套工程"))
					{
						break;
					}
					else
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						
						$volist1[$i][plantimeend]=$val[plantimeend];
						$volist1[$i][plancount]=$val[plancount];
						$volist1[$i][realcount]=$val[realcount];
						$i++;
					}
				}
			}
			echo json_encode($volist1);
			return;
		}
		else
		{
			$map[plmid]=$plmid;
			$map[status]=1;
			$map[percent]=array("neq","100%");
			$schedule=M("Plmschedule")->where($map)->group("worktype")->order("sort asc")->select();
			
			/*
			$volist1[0][id]="1";
			$volist1[0][title]="工作汇报";
			$volist1[0][planpercent]="不需填写完成量";
			$volist1[0][currentpercent]="";
			$i=1;
			*/
			$i=0;
			
			$date=date("Y-m-d");
			foreach($schedule as $key1 => $val1)
			{
				$map[worktype]=$val1[worktype];
				$volist=M("Plmschedule")->where($map)->order("sort asc")->select();
				foreach($volist as $key => $val)
				{
					$mapforworktype[title]=$val[subworktype];
					$ifpingxing=M("Worktype")->where($mapforworktype)->getField("parallel");
					
					
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
					
					
					if(($key==0)&&($ifpingxing!="是"))
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$volist1[$i][plantimeend]=$val[plantimeend];
						$volist1[$i][plancount]=$val[plancount];
						$volist1[$i][realcount]=$val[realcount];
						$i++;
					}
					else if($ifpingxing!="是")
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$volist1[$i][plantimeend]=$val[plantimeend];
						$volist1[$i][plancount]=$val[plancount];
						$volist1[$i][realcount]=$val[realcount];
						$i++;
					}
					else
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$volist1[$i][plantimeend]=$val[plantimeend];
						$volist1[$i][plancount]=$val[plancount];
						$volist1[$i][realcount]=$val[realcount];
						$i++;
					}
				}
			}
			echo json_encode($volist1);
			return;
		}
		
		
	}
	
	
	public function dailysubmit() {
		
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		
		
		
			
			
		$mapforPlmschedule[id]=$_REQUEST['worktypeid'];
		$mapforPlmschedule[status]=1;
		$worktype=M("Plmschedule")->where($mapforPlmschedule)->find();
		/*上传图片*/
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		header('Content-Type:text/html;charset=UTF-8');
		set_time_limit(0);
		$savePath = '../Public/Uploads/';
		
		if(($_REQUEST["percent"]=="100%")&&(empty($_POST['base641']))&&(false==strstr($worktype["classify"],"采购")))
		{
			$result[result]="进度完成时，请上传附件或图片";
			echo json_encode($result);
			return;
		}
		
		for($i=1;$i<=10;$i++)
		{
			$img = $_POST['base64'.$i];
			if (!empty($img)) {
				$uuid=uniqid(rand(), false);
				if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
					$ext = strtolower(end(explode(".",basename($_REQUEST["imgNameList"][$i-1])))); 
					$target = $savePath.$uuid.".".$ext;
					$img = base64_decode($matches[2]);
					file_put_contents($target, $img);
				} else {
					echo 'error'; 
				}
				$filename=$uuid.".".$ext;
				$newname=$filename;
				$data[photo].=$newname.",";
				$data[photorealname].=$_REQUEST["imgNameList"][$i-1].",";
				
				
				
				$datafile[plmNumber]=$plminfo[id];
				$datafile[loadPerson]=$_SESSION["name"];
				$datafile[loadtime]=time();
				$datafile[plm]=$plminfo[title];
				$datafile[type]=$worktype[worktype];
				$datafile[classify]=$worktype[classify];
				$datafile[attribute]=$worktype[attribute];
				$datafile[worktype]=$worktype[worktype];
				$datafile[subworktype]=$worktype[subworktype];
				$datafile[newname] = $newname;
				$datafile[filename] = $_REQUEST["imgNameList"][$i-1];
				$datafile[title]=$_REQUEST["imgNameList"][$i-1];
				M("Plmfile")->add($datafile);
			}
		}
		
		
		
		
		
		$audio = $_POST['audio'];
		if (!empty($audio)) {
			$savePath = '../Public/Uploads/';
			$uuid=uniqid(rand(), false);
			$target = $savePath.$uuid.'.amr';
			if (preg_match('/data:([^;]*);base64,(.*)/', $audio, $matches)) {
				$audio = base64_decode($matches[2]);
				$data[voicedata]=str_replace("data:audio/amr;base64,","",$_POST['audio']);
				file_put_contents($target, $audio);
			} else {
				echo 'error'; 
			}
		} else {
			
		}
		if (!empty($audio))
		{
			$filename=$uuid.'.amr';
			$newname=$filename;
			$data[voice]=$newname;
			$data[voice]=$filename;
		}
		
		
		$model=M("Plmdaily");
		M("Project")->where("id=".$_REQUEST[plmid])->setField("design_status","施工中");
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		$date=date("Y-m-d");
		for($i=0;$i<=4;$i++)
		{
			if($i==0) 
				$j="";
			else 
				$j=$i;
			
			if((empty($_REQUEST['worktypeid'.$j]))||($_REQUEST['worktypeid'.$j]=="worktypeid1")||($_REQUEST['worktypeid'.$j]=="worktypeid2")||($_REQUEST['worktypeid'.$j]=="worktypeid3")||($_REQUEST['worktypeid'.$j]=="worktypeid4"))
			{
				continue;
			}
			//注意  这里是调用的Plmschedule的专项节点
			$mapforPlmschedule[id]=$_REQUEST['worktypeid'.$j];
			$mapforPlmschedule[status]=1;
			$worktype=M("Plmschedule")->where($mapforPlmschedule)->find();
			
			//判断是否超期
			//$mapforplmwarning[plmid]=$_REQUEST[plmid];
			//$mapforplmwarning[worktype]=$worktype[worktype];
			//$mapforplmwarning[warning]=1;
			//$warningid=M("Plmwarning")->where($mapforplmwarning)->getField("id");
			//$data[warning]=$warningid;
			//今日应当进度
			if($date<$worktype[plantimebegin])
			{
				$todayplanpercent=0;
			}	
			else if($date>=$worktype[plantimeend])
			{
				$todayplanpercent=100;
			}	
			else
			{
				$diff = $worktype[plantimelength];
				$timeplanlenth=$diff;
				$percentperday=100/$timeplanlenth;
				//今天与计划日之间天数差
				$diffreal = $this->diffBetweenTwoDays($worktype[plantimebegin], $date);
				//今天应该完成的比例
				$todayplanpercent=round($percentperday*$diffreal,0);
			}
			$data[planpercent]=$todayplanpercent."%";
			
			$data[plmid]=$_REQUEST[plmid];
			$data[plm]=$plminfo['title'];
			$data[classify]=$_REQUEST[moduletitle];
			$data[user_id]=$userinfo[number];
			$data[user]=$_SESSION['loginUserName'];
			
			$data[role]=$_SESSION[role];
			$data[type]=1;
			
			$data[create_time]=time();
			$data["date"]=date("Y-m-d");
			
			$data[title]=$plminfo['title'];
			$data[content]=$_REQUEST["rizhi"];
			$data[voice]="";
			$data[attribute]=$worktype[attribute];
			$data[worktype]=$worktype['worktype'];
			$data[subworktype]=$worktype['subworktype'];
			
			if($_REQUEST["type"]!="节点验收")
			{
				$data[percent]=$_REQUEST["percent".$j];//zcy 20220725
			}
			
			$data[scheduleid]=$_REQUEST["worktypeid".$j];
			
			
			
			if($_REQUEST["daohuoshuliang"])
			{
				$data[plancount]=$_REQUEST['jihuashuliang'];
				$data[realcount]=$_REQUEST['daohuoshuliang'];
				if($data[realcount]==$worktype["plancount"])
				{
					$data[percent]="100%";
				}
				else
				{
					$data[percent]=round(100*$_REQUEST['daohuoshuliang']/$worktype["plancount"],0)."%";
				}
			}
			
			if($data[planpercent]>$data["percent"])
			{
				$data[warning]="1";
			}
			$data[realquality]=round(($data[percent]*$worktype["planquality"]/100),1);
			$data[qualityunity]=$worktype["qualityunity"];
			
			if($_REQUEST['worktypeid'.$j]=="1")
			{
				$data[worktype]="工作汇报";
				$data[subworktype]="工作汇报";
				$data[planpercent]="";
				$data[percent]="";
				$data[warning]="";
				M("Plmdaily")->add($data);
				continue;
			}
			
			M("Plmdaily")->add($data);
			$mapforPlmschedulex[plmid]=$_REQUEST[plmid];
			$mapforPlmschedulex[worktype]=$worktype['worktype'];
			$mapforPlmschedulex[subworktype]=$worktype['subworktype'];
			$mapforPlmschedulex[status]=1;
			$schedule=M("Plmschedule")->where($mapforPlmschedulex)->find();
			if(($schedule[realtimebegin]==""))
			{
				$schedule[realtimebegin]=$data["date"];
			}
			if($_REQUEST["percent".$j]=="100%")
			{
				$schedule[realtimeend]=$data["date"];
				$schedule[realtimelength]=$this->diffBetweenTwoDays($schedule[realtimebegin],$schedule[realtimeend])+1;
			}
			$schedule[realcount]=$data['realcount'];
			$schedule[percent]=$data[percent];
			$schedule[realquality]=$data[realquality];
			
			
			M("Plmschedule")->save($schedule);
			
			
			
			
			
			
			
			
			
			/*自动完成*/
			if($_REQUEST["moduletitle"]=="主项进度管理")$mapforWorktype1["classify"]="主项节点库";
			if($_REQUEST["moduletitle"]=="开发进度管理")$mapforWorktype1["classify"]="开发专项节点库";
			if($_REQUEST["moduletitle"]=="设计进度管理")$mapforWorktype1["classify"]="设计专项节点库";
			if($_REQUEST["moduletitle"]=="采购进度管理")$mapforWorktype1["classify"]="采购专项节点库";
			if($_REQUEST["moduletitle"]=="施工进度管理")$mapforWorktype1["classify"]="施工专项节点库";
			$mapforWorktype1["type"]="1";
			$mapforWorktype1["projecttype"]=$plminfo[projecttype];
			$mapforWorktype1["title"]=$worktype['worktype'];
			$worktypeid=M("Worktype")->where($mapforWorktype1)->getField("id");
			
			$mapforWorktype1["type"]="2";
			$mapforWorktype1["pid"]=$worktypeid;
			$mapforWorktype1["title"]=$worktype['subworktype'];
			$worktypeid=M("Worktype")->where($mapforWorktype1)->getField("id");
			
			$mapforWorktype1["type"]="2";
			$mapforWorktype1["pid"]=array("like","%%");
			$mapforWorktype1["title"]=array("like","%%");
			$mapforWorktype1["autocompleteid"]=array("like","%".$worktypeid.",%");
			
			$autocompleteworktypearray=M("Worktype")->where($mapforWorktype1)->select();
			if(!empty($autocompleteworktypearray))
			{
				foreach($autocompleteworktypearray as $key => $val)
				{
					$autocompleteid=$val[autocompleteid];
					$autocompletearray=explode(",",$autocompleteid);
					
					$autocomplete=$val[autocomplete];
					$autocompletearray=explode("</br>",$autocomplete);
					
					$isautocomplete=1;
					foreach($autocompletearray as $key1 => $val1)
					{
						$autocompletedetail=explode("-",$val1);
						
						$mapforPlmscheduley[plmid]=$_REQUEST[plmid];
						$mapforPlmscheduley[classify]=$autocompletedetail[0];
						$mapforPlmscheduley[projecttype]=$plminfo["projecttype"];
						$mapforPlmscheduley[worktype]=$autocompletedetail[1];
						$mapforPlmscheduley[subworktype]=$autocompletedetail[2];
						$mapforPlmscheduley[status]=1;
						
						$percent=M("Plmschedule")->where($mapforPlmscheduley)->getField("percent");
						if($percent!="100%")
						{
							$isautocomplete=0;
						}
					}
					if((!empty($dependencearray))&&($isautocomplete==1))
					{
						$mapforPlmschedulez[plmid]=$_REQUEST[plmid];
						$mapforPlmschedulez[classify]=$val["classify"];
						$mapforPlmschedulez[projecttype]=$plminfo["projecttype"];
						$mapforPlmschedulez[worktype]=M("Worktype")->where("id=".$val["pid"])->getField("title");
						$mapforPlmschedulez[subworktype]=$val["title"];
						$mapforPlmschedulez[status]=1;
						$scheduleinfo=M("Plmschedule")->where($mapforPlmschedulez)->find();
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
						$data[planpercent]=$todayplanpercent."%";
						if($data[planpercent]>"100%")
						{
							$data[warning]="1";
						}
						
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
						
						$data[percent]="100%";
						$data[scheduleid]=$scheduleinfo["id"];
						M("Plmdaily")->add($data);
						
						$schedule=$worktype;
						if(($schedule[realtimebegin]==""))
						{
							$schedule[realtimebegin]=$data["date"];
						}
						if(1)
						{
							$schedule[realtimeend]=$data["date"];
						}
						
						$schedule[percent]=$data[percent];
						M("Plmschedule")->save($schedule);
			
					}
				}
			}
			
			
			
			
			
			
			
			
			
			
			
		
		}
		
		$result[result]="操作成功";
		echo json_encode($result);
	}
	
	
	
	
	
	
	
	
	
	function dailytoexcel()
	{
		$dailyid=$_REQUEST["dailyid"];
		$mapforPlmschedule["dailyid"]=$dailyid;
		$mapforPlmschedule["status"]=1;
		$schedules=M("Plmscheduledaily")->where($mapforPlmschedule)->order("id asc")->select();

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