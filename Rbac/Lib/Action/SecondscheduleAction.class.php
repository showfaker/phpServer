<?php
class SecondscheduleAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		//$map[step1]=1;
		
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
		if($_REQUEST['owner'])
		{
			$map['owner'] = array('like',"%".$_REQUEST['owner']."%");
			$this->assign("owner",$_REQUEST['owner']);
		}
		if($_REQUEST['owner2'])
		{
			$map['owner2'] = array('like',"%".$_REQUEST['owner2']."%");
			$this->assign("owner2",$_REQUEST['owner2']);
		}
		if($_REQUEST['invester'])
		{
			$map['invester'] = array('like',"%".$_REQUEST['invester']."%");
			$this->assign("invester",$_REQUEST['invester']);
		}
		
		if($_REQUEST['type'])
		{
			$map['type'] = array('like',"%".$_REQUEST['type']."%");
			$this->assign("type",$_REQUEST['type']);
		}
		if($_REQUEST['taketype'])
		{
			$map['taketype'] = array('like',"%".$_REQUEST['taketype']."%");
			$this->assign("taketype",$_REQUEST['taketype']);
		}
		if($_REQUEST['currentapprover'])
		{
			$map['currentapprover'] = array('like',"%".$_REQUEST['currentapprover']."%");
			$this->assign("currentapprover",$_REQUEST['currentapprover']);
		}
		
		if($_REQUEST['status'])
		{
			if(($_REQUEST['status']=="储备"))
			{
				//$map['design_status'] = array("in","储备");
				$map[design_status]=array("in","储备,暂存,初步申报待审批,初步申报审批中,初步申报审批通过,初步申报审批退回,项目计划待审批,项目计划审批中,项目计划审批通过,项目计划审批退回,初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回");
			}
			else if(($_REQUEST['status']=="待施工"))
			{
				$map['design_status'] = array("in","待施工");
			}
			else if(($_REQUEST['status']=="施工中"))
			{
				$map['design_status'] = array("in","施工中");
			}
			else if(($_REQUEST['status']=="已完成"))
			{
				$map['design_status'] = array('in',"完成施工,已完成,竣工待验收,项目待验收,验收审核退回,完成验收");
			}
			else if(($_REQUEST['status']=="暂停中"))
			{
				$map['design_status'] = array("in","暂停中");
			}
			else if(($_REQUEST['status']=="暂停"))
			{
				$map['design_status'] = array("in","暂停中");
			}
			else if(($_REQUEST['status']=="取消"))
			{
				$map['design_status'] = array("in","取消");
			}
			$this->assign("status",$_REQUEST['status']);
		}
		else
		{
			//$map['status'] = array('neq',"取消");
		}
		if($_REQUEST['address'])
		{
			$map['province|city|area|address'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		if($_REQUEST['charge'])
		{
			$map['charge'] = array('like',"%".$_REQUEST['charge']."%");
			$this->assign("charge",$_REQUEST['charge']);
		}
		if($_REQUEST['director'])
		{
			$map['director'] = array('like',"%".$_REQUEST['director']."%");
			$this->assign("director",$_REQUEST['director']);
		}
		if($_REQUEST['technology'])
		{
			$map['technology'] = array('like',"%".$_REQUEST['technology']."%");
			$this->assign("technology",$_REQUEST['technology']);
		}
		
		
		if((!empty($_REQUEST['estimate_signtimebegin']))&&(empty($_REQUEST['estimate_signtimeend'])))
		$map['estimate_signtime'] = array('egt',($_REQUEST['estimate_signtimebegin']));
		else if((empty($_REQUEST['estimate_signtimebegin']))&&(!empty($_REQUEST['estimate_signtimeend'])))
		$map['estimate_signtime'] = array('elt',($_REQUEST['estimate_signtimeend']));
		else if((!empty($_REQUEST['estimate_signtimebegin']))&&(!empty($_REQUEST['estimate_signtimeend'])))
		$map['estimate_signtime'] = array(array('egt',($_REQUEST['estimate_signtimebegin'])),array('elt',($_REQUEST['estimate_signtimeend'])),'and');
		$this->assign('estimate_signtimebegin', $_REQUEST['estimate_signtimebegin']);
		$this->assign('estimate_signtimeend', $_REQUEST['estimate_signtimeend']);
		
		if((!empty($_REQUEST['estimate_intimebegin']))&&(empty($_REQUEST['estimate_intimeend'])))
		$map['estimate_intime'] = array('egt',($_REQUEST['estimate_intimebegin']));
		else if((empty($_REQUEST['estimate_intimebegin']))&&(!empty($_REQUEST['estimate_intimeend'])))
		$map['estimate_intime'] = array('elt',($_REQUEST['estimate_intimeend']));
		else if((!empty($_REQUEST['estimate_intimebegin']))&&(!empty($_REQUEST['estimate_intimeend'])))
		$map['estimate_intime'] = array(array('egt',($_REQUEST['estimate_intimebegin'])),array('elt',($_REQUEST['estimate_signtimeend'])),'and');
		$this->assign('estimate_intimebegin', $_REQUEST['estimate_intimebegin']);
		$this->assign('estimate_intimeend', $_REQUEST['estimate_intimeend']);
		
		
		if($_REQUEST['dealpercent'])
		{
			$map['dealpercent'] = array('like',"%".$_REQUEST['dealpercent']."%");
			$this->assign("dealpercent",$_REQUEST['dealpercent']);
		}
		if($_REQUEST['bid'])
		{
			$map['bid'] = array('like',"%".$_REQUEST['bid']."%");
			$this->assign("bid",$_REQUEST['bid']);
		}
		
		if((!empty($_REQUEST['dealtimebegin']))&&(empty($_REQUEST['dealtimeend'])))
		$map['estimate_intime'] = array('egt',($_REQUEST['dealtimebegin']));
		else if((empty($_REQUEST['dealtimebegin']))&&(!empty($_REQUEST['dealtimeend'])))
		$map['estimate_intime'] = array('elt',($_REQUEST['dealtimeend']));
		else if((!empty($_REQUEST['dealtimebegin']))&&(!empty($_REQUEST['dealtimeend'])))
		$map['estimate_intime'] = array(array('egt',($_REQUEST['dealtimebegin'])),array('elt',($_REQUEST['estimate_signtimeend'])),'and');
		$this->assign('dealtimebegin', $_REQUEST['dealtimebegin']);
		$this->assign('dealtimeend', $_REQUEST['dealtimeend']);
		
		
		if($_REQUEST['progress'])
		{
			$map['progress'] = array('like',"%".$_REQUEST['progress']."%");
			$this->assign("progress",$_REQUEST['progress']);
		}
		if($_REQUEST['keyman'])
		{
			$map['keyman'] = array('like',"%".$_REQUEST['keyman']."%");
			$this->assign("keyman",$_REQUEST['keyman']);
		}
		if($_REQUEST['qualifications'])
		{
			$map['qualifications'] = array('like',"%".$_REQUEST['qualifications']."%");
			$this->assign("qualifications",$_REQUEST['qualifications']);
		}
		if($_REQUEST['bidmeans'])
		{
			$map['bidmeans'] = array('like',"%".$_REQUEST['bidmeans']."%");
			$this->assign("bidmeans",$_REQUEST['bidmeans']);
		}
		if($_REQUEST['design_institute'])
		{
			$map['design_institute'] = array('like',"%".$_REQUEST['design_institute']."%");
			$this->assign("design_institute",$_REQUEST['design_institute']);
		}
		if($_REQUEST['designer'])
		{
			$map['designer'] = array('like',"%".$_REQUEST['designer']."%");
			$this->assign("designer",$_REQUEST['designer']);
		}
		if($_REQUEST['fundsource'])
		{
			$map['fundsource'] = array('like',"%".$_REQUEST['fundsource']."%");
			$this->assign("fundsource",$_REQUEST['fundsource']);
		}
		if($_REQUEST['hardness'])
		{
			$map['hardness'] = array('like',"%".$_REQUEST['hardness']."%");
			$this->assign("hardness",$_REQUEST['hardness']);
		}
		if($_REQUEST['role'])
		{
			$mapforRole["name"]=array("like","%".$_REQUEST['role']."%");
			$roles=M("Role")->where($mapforRole)->select();
			foreach ($roles as $key => $val) 
			{
				$roleids.=$val["id"].",";
			}
			$mapforUser["position"]=array("in",$roleids);
		
			$userarray=M("User")->where($mapforUser)->select();
			foreach ($userarray as $key => $val) 
			{
				$users.=$val["nickname"].",";
			}
			$map['xiaoshouuser'] = array('in',$users);
			$this->assign("role",$_REQUEST['role']);
		}
		if($_REQUEST['xiaoshouuser'])
		{
			$map['xiaoshouuser'] = array('like',"%".$_REQUEST['xiaoshouuser']."%");
			$this->assign("xiaoshouuser",$_REQUEST['xiaoshouuser']);
		}
		
		
		
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		
		
		$allstatus=M("Project")->where($map)->order("id desc")->group("design_status")->field("design_status")->select();
		$this->assign('allstatus',$allstatus);
		/*
		$alldata=M("Project")->select();
		foreach($alldata as $key => $val)
		{
			M("Project")->where("id=". $val["id"])->setField("ctime",$val["time"]);
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
		
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}
		
		if(1)//$_REQUEST["onlymine"]=="1"
		{
			$map['design_status'] = array("not in","暂停,取消,立项中,施工完成,验收完成,完成验收");
		}
		if($_REQUEST["design_status"])//$_REQUEST["onlymine"]=="1"
		{
			$map['design_status'] = array("eq",$_REQUEST["design_status"]);
			$this->assign('design_status', $_REQUEST['design_status']);
		}
		$this->assign('onlymine', $_REQUEST['onlymine']);
		//$map[design_status]=array("in","销售中心,经营评估退回,研究中心,工程评估退回,报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,竣工待验收,项目待验收,验收审核退回,暂停中");//完成验收
		//$map['three'] = array("neq",1);
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'id',false);
		}
		
		
		$map1['_complex'] = $this->find5level($_SESSION[position],$map1);
		
		$allprojects=M("Project")->where($map1)->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
		
		
		
		$mapforRole["name"]=array("like","%组长%");
		$roles=M("Role")->where($mapforRole)->select();
		foreach ($roles as $key => $val) 
		{
			$roles[$key]["subname"]=str_replace("组长","",$val["name"]);
		}
		$this->assign('roles', $roles);
		
		if($_SESSION["app"])
		{
			$this->display("indexapp");
			return;
		}
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
		$date=date("Y-m-d");
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
			if($_REQUEST["onlymine"]==1)
			{
				 $listRows = 2000;
			}
            $p = new Page($count, $listRows);
            //分页查询数据
			
			if($_SESSION['curpage']!=null)
			{
				$p->nowPage=$_SESSION['curpage'];		
				$p->firstRow=($_SESSION['curpage']-1)*($p->listRows);
				unset($_SESSION['curpage']);
			}
			
            $volist = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			foreach($volist as $key => $val)
			{
				$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
				if(($val[design_status]=="暂存"))
				{
					$volist[$key][next]="无";
					$volist[$key][dealer]="";
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
				}
				else if(($val[design_status]=="立项中"))
				{
					$volist[$key][next]="待设置专项负责人";
					$volist[$key][dealer]="管理员";
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Xmff/index/moduletitle/项目组织/";
					
					
					
					if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
					{
						unset($volist[$key]);
					}
				}
				else if(($val[design_status]=="待施工"))
				{
					$volist[$key][next]="无";
					$volist[$key][dealer]="";
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
				}
				else if(($val[design_status]=="施工中"))
				{
					
					
					$mapforPlmschedule[percent]=array("neq","100%");
					$mapforPlmschedule[plmid] = $val[id];
					$mapforPlmschedule[status]=1;
					$mapforPlmschedule[classify]=array("like","%主项%");
					$schedule1=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
					$mapforPlmschedule[classify]=array("like","%开发%");
					$schedule2=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
					$mapforPlmschedule[classify]=array("like","%设计%");
					$schedule3=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
					$mapforPlmschedule[classify]=array("like","%采购%");
					$schedule4=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
					$mapforPlmschedule[classify]=array("like","%施工%");
					$schedule5=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
					
					$mapforPlmoperatetype["id"]=$val["operatetypeid"];
					$operatetypecontent=M("Plmoperatetype")->where($mapforPlmoperatetype)->getField("content");
					
					
					
					
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Sgrz/index/moduletitle/项目日报管理/";
					$volist[$key][check]="1";
					if(($date>$schedule1[plantimeend])&&(!empty($schedule1[plantimeend]))&&(false!==strstr($operatetypecontent,"工程"))&&(false==strstr($val[plan_status1],"计划审核退回")))
					{
						$volist[$key][remark]="<font style='color:red'>超期</font></br>";
						$volist[$key][remark1]="<font style='color:red'>超期</font></br>";
					}
					else 
					{
						$volist[$key][remark]="<font style='color:green'></font></br>";
						$volist[$key][remark1]="<font style='color:green'></font></br>";
					}
					if(($date>$schedule2[plantimeend])&&(!empty($schedule2[plantimeend]))&&(false!==strstr($operatetypecontent,"开发"))&&(false==strstr($val[plan_status2],"计划审核退回")))
					{
						$volist[$key][remark].="<font style='color:red'>超期</font></br>";
						$volist[$key][remark2]="<font style='color:red'>超期</font></br>";
					}
					else 
					{
						$volist[$key][remark].="<font style='color:green'></font></br>";
						$volist[$key][remark2]="<font style='color:green'></font></br>";
					}
					if(($date>$schedule3[plantimeend])&&(!empty($schedule3[plantimeend]))&&(false!==strstr($operatetypecontent,"设计"))&&(false==strstr($val[plan_status3],"计划审核退回")))
					{
						$volist[$key][remark].="<font style='color:red'>超期</font></br>";
						$volist[$key][remark3]="<font style='color:red'>超期</font></br>";
					}
					else 
					{
						$volist[$key][remark].="<font style='color:green'></font></br>";
						$volist[$key][remark3]="<font style='color:green'></font></br>";
					}
					if(($date>$schedule4[plantimeend])&&(!empty($schedule4[plantimeend]))&&(false!==strstr($operatetypecontent,"采购"))&&(false==strstr($val[plan_status4],"计划审核退回")))
					{
						$volist[$key][remark].="<font style='color:red'>超期</font></br>";
						$volist[$key][remark4]="<font style='color:red'>超期</font></br>";
					}
					else 
					{
						$volist[$key][remark].="<font style='color:green'></font></br>";
						$volist[$key][remark4]="<font style='color:green'></font></br>";
					}
					if(($date>$schedule5[plantimeend])&&(!empty($schedule5[plantimeend]))&&(false!==strstr($operatetypecontent,"工程"))&&(false==strstr($val[plan_status5],"计划审核退回")))
					{
						$volist[$key][remark].="<font style='color:red'>超期</font></br>";
						$volist[$key][remark5]="<font style='color:red'>超期</font></br>";
					}
					else 
					{
						$volist[$key][remark].="<font style='color:green'></font></br>";
						$volist[$key][remark5]="<font style='color:green'></font></br>";
					}
					
					//1主项
					if((false!==strstr($operatetypecontent,"工程")))
					{
						if(empty($val[gongcheng]))
						{
							$volist[$key][next1]="待设置负责人";
							$volist[$key][dealer1]="管理员";
							$volist[$key][url1]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime1]=$val["time"];
						}
						else if(empty($val[gongchenguser]))
						{
							$volist[$key][next1]="待设置执行人";
							$volist[$key][dealer1]=$val[gongcheng];
							$volist[$key][url1]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime1]=$val[set_leader_time1];
						}
						else if(empty($val[worktype_status1]))
						{
							$volist[$key][next1]="待设置节点";
							$volist[$key][dealer1]=$val[gongchenguser];
							$volist[$key][url1]="__APP__/Ysgl/index/moduletitle/主项节点设置/";
							$volist[$key][arrivetime1]=$val[set_user_time1];
						}
						else if(empty($val[plan_status1])||(false!==strstr($val[plan_status1],"计划审核退回")))
						{
							$volist[$key][next1]="待设置计划";
							$volist[$key][dealer1]=$val[gongchenguser];
							$volist[$key][url1]="__APP__/Sgjh/index/moduletitle/主项计划/";
							$volist[$key][arrivetime1]=$val[set_node_time1];
						}
						else if((false!==strstr($val[plan_status1],"计划待审核")))
						{
							$volist[$key][next1]="待审批计划";
							$volist[$key][dealer1]=$val[gongcheng];
							$volist[$key][url1]="__APP__/Sgjh/index/moduletitle/主项计划/tab/2/";
							$volist[$key][arrivetime1]=$val[plan_time1];
						}
						else
						{
							if(!empty($schedule1["subworktype"]))
							{
								$volist[$key][next1]="待上传".$schedule1["worktype"]."-".$schedule1["subworktype"]."节点";
								$volist[$key][dealer1]=$val[gongchenguser];
								$volist[$key][url1]="__APP__/Bproject/index/moduletitle/主项进度管理/";
								
								
								$mapforPlmschedule1[percent]=array("eq","100%");
								$mapforPlmschedule1[plmid] = $val[id];
								$mapforPlmschedule1[status]=1;
								$mapforPlmschedule1[classify]=array("like","%主项%");
								$tempschedule=M("Plmschedule")->where($mapforPlmschedule1)->order("realtimeend desc")->find();
								if(empty($tempschedule))
								{
									$volist[$key][arrivetime1]=$val[plan_approve_time1];
								}
								else
								{
									$volist[$key][arrivetime1]=$tempschedule[realtimeend];
								}
							}
							else
							{
								$volist[$key][next1]="当前无节点";
							}
							
						}
					}
					
					//2开发
					if((false!==strstr($operatetypecontent,"开发")))
					{
						if(empty($val[kaifa]))
						{
							$volist[$key][next2]="待设置负责人";
							$volist[$key][dealer2]="管理员";
							$volist[$key][url2]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime2]=$val["time"];
						}
						else if(empty($val[kaifauser]))
						{
							$volist[$key][next2]="待设置执行人";
							$volist[$key][dealer2]=$val[kaifa];
							$volist[$key][url2]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime2]=$val[set_leader_time2];
						}
						else if(empty($val[worktype_status2]))
						{
							$volist[$key][next2]="待设置节点";
							$volist[$key][dealer2]=$val[kaifauser];
							$volist[$key][url2]="__APP__/Ysgl/index/moduletitle/开发节点设置/";
							$volist[$key][arrivetime2]=$val[set_user_time2];
						}
						else if(empty($val[plan_status2])||(false!==strstr($val[plan_status2],"计划审核退回")))
						{
							$volist[$key][next2]="待设置计划";
							$volist[$key][dealer2]=$val[kaifauser];
							$volist[$key][url2]="__APP__/Sgjh/index/moduletitle/开发专项计划/";
							$volist[$key][arrivetime2]=$val[set_node_time2];
						}
						else if((false!==strstr($val[plan_status2],"计划待审核")))
						{
							$volist[$key][next2]="待审批计划";
							$volist[$key][dealer2]=$val[kaifa];
							$volist[$key][url2]="__APP__/Sgjh/index/moduletitle/开发专项计划/tab/2/";
							$volist[$key][arrivetime2]=$val[plan_time2];
						}
						else
						{
							if(!empty($schedule2["subworktype"]))
							{
								$volist[$key][next2]="待上传".$schedule2["worktype"]."-".$schedule2["subworktype"]."节点";
								$volist[$key][dealer2]=$val[kaifauser];
								$volist[$key][url2]="__APP__/Bproject/index/moduletitle/开发进度管理/";
								
								$mapforPlmschedule1[percent]=array("eq","100%");
								$mapforPlmschedule1[plmid] = $val[id];
								$mapforPlmschedule1[status]=1;
								$mapforPlmschedule1[classify]=array("like","%开发%");
								$tempschedule=M("Plmschedule")->where($mapforPlmschedule1)->order("realtimeend desc")->find();
								if(empty($tempschedule))
								{
									$volist[$key][arrivetime2]=$val[plan_approve_time2];
								}
								else
								{
									$volist[$key][arrivetime2]=$tempschedule[realtimeend];
								}
							}
							else
							{
								$volist[$key][next2]="当前无节点";
							}
						}
					}
					
					//3设计
					if((false!==strstr($operatetypecontent,"设计")))
					{
						if(empty($val[sheji]))
						{
							$volist[$key][next3]="待设置负责人";
							$volist[$key][dealer3]="管理员";
							$volist[$key][url3]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime3]=$val["time"];
							
						}
						else if(empty($val[shejiuser]))
						{
							$volist[$key][next3]="待设置执行人";
							$volist[$key][dealer3]=$val[sheji];
							$volist[$key][url3]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime3]=$val[set_leader_time3];
						}
						else if(empty($val[worktype_status3]))
						{
							$volist[$key][next3]="待设置节点";
							$volist[$key][dealer3]=$val[shejiuser];
							$volist[$key][url3]="__APP__/Ysgl/index/moduletitle/设计节点设置/";
							$volist[$key][arrivetime3]=$val[set_user_time3];
						}
						else if(empty($val[plan_status3])||(false!==strstr($val[plan_status3],"计划审核退回")))
						{
							$volist[$key][next3]="待设置计划";
							$volist[$key][dealer3]=$val[shejiuser];
							$volist[$key][url3]="__APP__/Sgjh/index/moduletitle/设计专项计划/";
							$volist[$key][arrivetime3]=$val[set_node_time3];
						}
						else if((false!==strstr($val[plan_status3],"计划待审核")))
						{
							$volist[$key][next3]="待审批计划";
							$volist[$key][dealer3]=$val[sheji];
							$volist[$key][url3]="__APP__/Sgjh/index/moduletitle/设计专项计划/tab/2/";
							$volist[$key][arrivetime3]=$val[plan_time3];
						}
						else
						{
							if(!empty($schedule3["subworktype"]))
							{
								$volist[$key][next3]="待上传".$schedule3["worktype"]."-".$schedule3["subworktype"]."节点";
								$volist[$key][dealer3]=$val[shejiuser];
								$volist[$key][url3]="__APP__/Bproject/index/moduletitle/设计进度管理/";
								
								$mapforPlmschedule1[percent]=array("eq","100%");
								$mapforPlmschedule1[plmid] = $val[id];
								$mapforPlmschedule1[status]=1;
								$mapforPlmschedule1[classify]=array("like","%设计%");
								$tempschedule=M("Plmschedule")->where($mapforPlmschedule1)->order("realtimeend desc")->find();
								//dump($tempschedule);
								if(empty($tempschedule))
								{
									$volist[$key][arrivetime3]=$val[plan_approve_time3];
								}
								else
								{
									$volist[$key][arrivetime3]=$tempschedule[realtimeend];
								}
							}
							else
							{
								$volist[$key][next3]="当前无节点";
							}
						}
					}
					
					//4采购
					if((false!==strstr($operatetypecontent,"采购")))
					{
						if(empty($val[caigou]))
						{
							$volist[$key][next4]="待设置负责人";
							$volist[$key][dealer4]="管理员";
							$volist[$key][url4]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime4]=$val["time"];
						}
						else if(empty($val[caigouuser]))
						{
							$volist[$key][next4]="待设置执行人";
							$volist[$key][dealer4]=$val[caigou];
							$volist[$key][url4]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime4]=$val[set_leader_time4];
						}
						else if(empty($val[worktype_status4]))
						{
							$volist[$key][next4]="待设置节点";
							$volist[$key][dealer4]=$val[caigouuser];
							$volist[$key][url4]="__APP__/Ysgl/index/moduletitle/采购节点设置/";
							$volist[$key][arrivetime4]=$val[set_user_time4];
						}
						else if(empty($val[plan_status4])||(false!==strstr($val[plan_status4],"计划审核退回")))
						{
							$volist[$key][next4]="待设置计划";
							$volist[$key][dealer4]=$val[caigouuser];
							$volist[$key][url4]="__APP__/Sgjh/index/moduletitle/采购专项计划/";
							$volist[$key][arrivetime4]=$val[set_node_time4];
						}
						else if((false!==strstr($val[plan_status4],"计划待审核")))
						{
							$volist[$key][next4]="待审批计划";
							$volist[$key][dealer4]=$val[caigou];
							$volist[$key][url4]="__APP__/Sgjh/index/moduletitle/采购专项计划/tab/2/";
							$volist[$key][arrivetime4]=$val[plan_time4];
						}
						else
						{
							if(!empty($schedule4["subworktype"]))
							{
								$volist[$key][next4]="待上传".$schedule4["worktype"]."-".$schedule4["subworktype"]."节点";
								$volist[$key][dealer4]=$val[caigouuser];
								$volist[$key][url4]="__APP__/Bproject/index/moduletitle/采购进度管理/";
								
								$mapforPlmschedule1[percent]=array("eq","100%");
								$mapforPlmschedule1[plmid] = $val[id];
								$mapforPlmschedule1[status]=1;
								$mapforPlmschedule1[classify]=array("like","%采购%");
								$tempschedule=M("Plmschedule")->where($mapforPlmschedule1)->order("realtimeend desc")->find();
								if(empty($tempschedule))
								{
									$volist[$key][arrivetime4]=$val[plan_approve_time4];
								}
								else
								{
									$volist[$key][arrivetime4]=$tempschedule[realtimeend];
								}
							}
							else
							{
								$volist[$key][next4]="当前无节点";
							}
						}
					}
					
					//5施工
					if((false!==strstr($operatetypecontent,"工程")))
					{
						if(empty($val[gongcheng]))
						{
							$volist[$key][next5]="待设置负责人";
							$volist[$key][dealer5]="管理员";
							$volist[$key][url5]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime5]=$val["time"];
						}
						else if(empty($val[gongchenguser]))
						{
							$volist[$key][next5]="待设置执行人";
							$volist[$key][dealer5]=$val[gongcheng];
							$volist[$key][url5]="__APP__/Xmff/index/moduletitle/项目组织/";
							$volist[$key][arrivetime5]=$val[set_leader_time1];
						}
						else if(empty($val[worktype_status5]))
						{
							$volist[$key][next5]="待设置节点";
							$volist[$key][dealer5]=$val[gongchenguser];
							$volist[$key][url5]="__APP__/Ysgl/index/moduletitle/施工节点设置/";
							$volist[$key][arrivetime5]=$val[set_user_time1];
						}
						else if(empty($val[plan_status5])||(false!==strstr($val[plan_status5],"计划审核退回")))
						{
							$volist[$key][next5]="待设置计划";
							$volist[$key][dealer5]=$val[gongchenguser];
							$volist[$key][url5]="__APP__/Sgjh/index/moduletitle/施工专项计划/";
							$volist[$key][arrivetime5]=$val[set_node_time5];
						}
						else if((false!==strstr($val[plan_status5],"计划待审核")))
						{
							$volist[$key][next5]="待审批计划";
							$volist[$key][dealer5]=$val[gongcheng];
							$volist[$key][url5]="__APP__/Sgjh/index/moduletitle/施工专项计划/tab/2/";
							$volist[$key][arrivetime5]=$val[plan_time5];
						}
						else
						{
							if(!empty($schedule5["subworktype"]))
							{
								$volist[$key][next5]="待上传".$schedule5["worktype"]."-".$schedule5["subworktype"]."节点";
								$volist[$key][dealer5]=$val[gongchenguser];
								$volist[$key][url5]="__APP__/Sgrz/index/moduletitle/施工进度管理/";
								
								$mapforPlmschedule1[percent]=array("eq","100%");
								$mapforPlmschedule1[plmid] = $val[id];
								$mapforPlmschedule1[status]=1;
								$mapforPlmschedule1[classify]=array("like","%施工%");
								$tempschedule=M("Plmschedule")->where($mapforPlmschedule1)->order("realtimeend desc")->find();
								if(empty($tempschedule))
								{
									$volist[$key][arrivetime5]=$val[plan_approve_time5];
								}
								else
								{
									$volist[$key][arrivetime5]=$tempschedule[realtimeend];
								}
							}
							else
							{
								$volist[$key][next5]="当前无节点";
							}
						}
					}
					
					if($volist[$key][dealer1]==$_SESSION["nickname"])
					{
						$volist[$key][dealer1]='<font style="color:">我</font>';
					}
					else
					{
						$volist[$key][url1]="javascript:alert('您不是负责人')";
					}
					if($volist[$key][dealer2]==$_SESSION["nickname"])
					{
						$volist[$key][dealer2]='<font style="color:">我</font>';
					}
					else
					{
						$volist[$key][url2]="javascript:alert('您不是负责人')";
					}
					if($volist[$key][dealer3]==$_SESSION["nickname"])
					{
						$volist[$key][dealer3]='<font style="color:">我</font>';
					}
					else
					{
						$volist[$key][url3]="javascript:alert('您不是负责人')";
					}
					if($volist[$key][dealer4]==$_SESSION["nickname"])
					{
						$volist[$key][dealer4]='<font style="color:">我</font>';
					}
					else
					{
						$volist[$key][url4]="javascript:alert('您不是负责人')";
					}
					if($volist[$key][dealer5]==$_SESSION["nickname"])
					{
						$volist[$key][dealer5]='<font style="color:">我</font>';
					}
					else
					{
						$volist[$key][url5]="javascript:alert('您不是负责人')";
					}
					
					
					$volist[$key][next]=$volist[$key][next1]."</br>".$volist[$key][next2]."</br>".$volist[$key][next3]."</br>".$volist[$key][next4]."</br>".$volist[$key][next5]."</br>";
					$volist[$key][dealer]=$volist[$key][dealer1]."</br>".$volist[$key][dealer2]."</br>".$volist[$key][dealer3]."</br>".$volist[$key][dealer4]."</br>".$volist[$key][dealer5];
					
					$volist[$key][remind1]=!empty($schedule1[plantimeend])?$schedule1[plantimeend]:"无";
					$volist[$key][remind2]=!empty($schedule2[plantimeend])?$schedule2[plantimeend]:"无";
					$volist[$key][remind3]=!empty($schedule3[plantimeend])?$schedule3[plantimeend]:"无";
					$volist[$key][remind4]=!empty($schedule4[plantimeend])?$schedule4[plantimeend]:"无";
					$volist[$key][remind5]=!empty($schedule5[plantimeend])?$schedule5[plantimeend]:"无";
					
					$volist[$key][remind]=$volist[$key][remind1]."</br>".$volist[$key][remind2]."</br>".$volist[$key][remind3]."</br>".$volist[$key][remind4]."</br>".$volist[$key][remind5];
					
					if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
					{
						unset($volist[$key]);
					}
				}
				else if(($val[design_status]=="施工完成"))
				{
					$volist[$key][next]="待验收";
					$volist[$key][dealer]=$val[gongcheng];
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Wgys/index/moduletitle/项目验收/";
					
					if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
					{
						unset($volist[$key]);
					}
				}
				else if(($val[design_status]=="完成验收"))
				{
					
					if(0)//empty($val[activity_time])
					{
						$volist[$key][next]="待投入运营";
						$volist[$key][dealer]=$dealer;
						$volist[$key][remind]="无";
						$volist[$key][remark]="无";
						$volist[$key][url]="__APP__/Wgys/index/moduletitle/项目投运/";
						if($date>$val[predate102])
						{
							$volist[$key][remark]="<font style='color:red'>超期</font>";
						}
					}
					else
					{
						$volist[$key][next]="无";
						$volist[$key][remind]="无";
						$volist[$key][remark]="无";
					}
					
					if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
					{
						unset($volist[$key]);
					}
				}
				else
				{
					$volist[$key][next]="无";
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
					if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
					{
						unset($volist[$key]);
					}
				}
			}
		
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
				else
				{
					$p->parameter .= "$key=" . $_REQUEST[$key] . "&";
				}
            }
			if($_REQUEST["flag1"])
			{
				$p->parameter .= "flag1=" . $_REQUEST["flag1"] . "&";
			}
			if($_REQUEST["flag2"])
			{
				$p->parameter .= "flag2=" . $_REQUEST["flag2"] . "&";
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
            $this->assign('list', $volist);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
		Cookie::set('_currentUrl_', __SELF__.$p->parameter);
        return;
    }
	
	public function toexcel()
	{
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		/*
		$alldata=M("Project")->select();
		foreach($alldata as $key => $val)
		{
			M("Project")->where("id=". $val["id"])->setField("ctime",$val["time"]);
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
		
		
		//$map[design_status]=array("in","销售中心,经营评估退回,研究中心,工程评估退回,报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,竣工待验收,项目待验收,验收审核退回,暂停中");//完成验收
		//$map['three'] = array("neq",1);
		
		$name = "Project";
		$model = D($name);
		$volist = $model->where($map)->order("id desc")->select();
			
		foreach($volist as $key => $val)
		{
			$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			if(($val[design_status]=="暂存"))
			{
				$volist[$key][next]="无";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="初步申报待审批"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="初步申报审批中"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="初步申报审批通过"))
			{
				$volist[$key][next]="待".$val[user]."设置关键节点时间";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="初步申报审批退回"))
			{
				$volist[$key][next]="待".$val[user]."提交初步申报";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="项目计划待审批"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="项目计划审批中"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="项目计划审批退回"))
			{
				$volist[$key][next]="待".$val[user]."设置关键节点时间";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
			else if(($val[design_status]=="项目计划审批通过"))
			{
				$volist[$key][next]="待".$val[user]."提交初步立项";
				$volist[$key][remind]=$val[predate0];
				$volist[$key][remark]="无";
				if($date>$val[predate0])
				{
				
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="初步立项待审批"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]=$val[predate1];
				$volist[$key][remark]="无";
				if($date>$val[predate1])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="初步立项审批退回"))
			{
				$volist[$key][next]="待".$val[user]."提交初步立项";
				$volist[$key][remind]=$val[predate0];
				$volist[$key][remark]="无";
				if($date>$val[predate0])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="初步立项审批通过"))
			{
				if($val['invester']=="自投资")
				{
					
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				//$volist[$key][next]="待".$dealer."提交可研编制";
				$volist[$key][next]="待".$val[user]."提交可研编制文件";
				$volist[$key][remind]=$val[predate2];
				$volist[$key][remark]="无";
				if($date>$val[predate2])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="可研编制文件审批通过"))
			{
				if($val['invester']=="自投资")
				{
					//$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
					$dealer=$val['user'];
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				//$volist[$key][next]="待".$dealer."提交可研评审";
				$volist[$key][next]="待".$dealer."提交可研评审报告";
				$volist[$key][remind]=$val[predate3];
				$volist[$key][remark]="无";
				if($date>$val[predate3])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="可研评审报告待审批"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]=$val[predate4];
				$volist[$key][remark]="无";
				if($date>$val[predate4])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="可研评审报告审批中"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][remind]=$val[predate5];
				$volist[$key][remark]="无";
				if($date>$val[predate5])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="可研评审报告审批退回"))
			{
				if($val['invester']=="自投资")
				{
					//$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
					$dealer=$val['user'];
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				$volist[$key][next]="待".$dealer."提交可研评审报告";
				$volist[$key][remind]=$val[predate3];
				$volist[$key][remark]="无";
				if($date>$val[predate3])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="可研评审报告审批通过"))
			{
				if($val['invester']=="自投资")
				{
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				if(empty($val['step3']))
				{
					//$volist[$key][next]="待".$dealer."提交合作协议";
					$volist[$key][next]="待".$val[user]."提交合作协议";
					$volist[$key][remind]=$val[predate6];
					$volist[$key][remark]="无";
					if($date>$val[predate6])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				}
				else if(($val['step3']=="0.5"))
				{
					//$volist[$key][next]="待".$dealer."提交综合计划";
					$volist[$key][next]="待".$val[user]."提交综合计划";
					$volist[$key][remind]=$val[predate6x];
					$volist[$key][remark]="无";
					if($date>$val[predate6x])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else if(($val['step3']=="1"))
				{
					//$volist[$key][next]="待".$dealer."提交招标信息";
					$volist[$key][next]="待".$val[user]."提交招标信息";
					$volist[$key][remind]=$val[predate7];
					$volist[$key][remark]="无";
					if($date>$val[predate7])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
			}
			else if(($val[design_status]=="招标审核通过"))
			{
				if($val['invester']=="自投资")
				{
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				
				//$volist[$key][next]="待".$dealer."提交合同信息";
				$volist[$key][next]="待".$val[user]."提交合同信息";
				$volist[$key][remind]=$val[predate8];
				$volist[$key][remark]="无";
				if($date>$val[predate8])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				
			}
			else if(($val[design_status]=="合同审核完成"))
			{
				if($val['invester']=="自投资")
				{
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				
				//$volist[$key][next]="待".$dealer."提交施工计划";
				$volist[$key][next]="待".$val[user]."提交施工计划";
				$volist[$key][remind]=$val[predate9];
				$volist[$key][remark]="无";
				if($date>$val[predate9])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				
			}
			else if(($val[design_status]=="设计审核通过"))
			{
				if($val['invester']=="自投资")
				{
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				
				//$volist[$key][next]="待".$dealer."提交施工计划";
				$volist[$key][next]="待".$val[user]."提交施工计划";
				$volist[$key][remind]=$val[predate9];
				$volist[$key][remark]="无";
				if($date>$val[predate9])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="待施工"))//施工计划审核通过
			{
				if($val['invester']=="自投资")
				{
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				
				
				if(empty($val['outplanset_user']))
				{
					//$volist[$key][outlineremind]="未设置外线计划";
				}
				
				if($val['sendtask_time']=="")
				{
					//$volist[$key][next]="待".$dealer."派发任务单";
					$volist[$key][next]="待".$val[user]."派发任务单";
					$volist[$key][remind]=$val[predate9x];
					$volist[$key][remark]="无";
					if($date>$val[predate9x])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else
				{
					$mapforPlmschedule[plmid]=$val[id];
					$mapforPlmschedule[status]=1;
					$val[predate10000]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
					
					
					
					$volist[$key][next]="待".$val[projectmanager4]."施工";
					$volist[$key][remind]=$val[predate10000];
					$volist[$key][remark]="无";
					if($date>$val[predate10000])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
			}
			else if(($val[design_status]=="施工中"))
			{
				if(empty($val['outplanset_user']))
				{
					//$volist[$key][outlineremind]="未设置外线计划";
				}
				
				$mapforPlmschedule[plmid]=$val[id];
				$mapforPlmschedule[status]=1;
				$val[predate10000]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
					
				$volist[$key][next]="当前步骤:".$val[projectmanager4]."施工中";
				$volist[$key][remind]=$val[predate10000];
				$volist[$key][remark]="无";
				if($date>$val[predate10000])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="竣工待验收"))
			{
				if(empty($val['outplanset_user']))
				{
					//$volist[$key][outlineremind]="未设置外线计划";
				}
				
				$volist[$key][next]="竣工待验收";
				$volist[$key][remind]=$val[predate100];
				$volist[$key][remark]="无";
				if($date>$val[predate100])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="项目待验收"))
			{
				if(empty($val['outplanset_user']))
				{
					//$volist[$key][outlineremind]="未设置外线计划";
				}
				
				$volist[$key][next]="项目待验收";
				$volist[$key][remind]=$val[predate101];
				$volist[$key][remark]="无";
				if($date>$val[predate101])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="验收审核退回"))
			{
				if(empty($val['outplanset_user']))
				{
					//$volist[$key][outlineremind]="未设置外线计划";
				}
				
				$volist[$key][next]="竣工待验收";
				$volist[$key][remind]=$val[predate100];
				$volist[$key][remark]="无";
				if($date>$val[predate100])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
			}
			else if(($val[design_status]=="完成验收"))
			{
				if(empty($val['outplanset_user']))
				{
					//$volist[$key][outlineremind]="未设置外线计划";
				}
				
				if(empty($val[activity_time]))
				{
					$volist[$key][next]="待投入运营";
					$volist[$key][remind]=$val[predate102];;
					$volist[$key][remark]="无";
					if($date>$val[predate102])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else
				{
					$volist[$key][next]="无";
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
				}
			}
			else
			{
				$volist[$key][next]="无";
				$volist[$key][remind]="无";
				$volist[$key][remark]="无";
			}
		}
		
	
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['number']=$volist[$i]['number'];
			
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['invester']=$volist[$i]['invester'];
			$data[$i]['user']=$volist[$i]['user'];
			$data[$i]['ctime']=$volist[$i]['ctime'];
			$data[$i]['design_status']=$volist[$i]['design_status'];
			
			$data[$i]['next']=$volist[$i]['next']." ".$volist[$i]['outlineremind'];
			$data[$i]['remind']=$volist[$i]['remind'];
			$data[$i]['remark']=$volist[$i]['remark'];
		}	
		
		$file="流程查看";
		$title="流程查看";
		$subtitle='流程查看';
		
		$th_array=array('项目编号','项目名称','投资方','报备人员','报备时间','项目状态','下一步操作','时间限制','是否超期');
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	
}
?>