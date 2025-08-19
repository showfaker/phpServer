<?php

if (!function_exists('array_column')) {
	function array_column($arr2, $column_key) {
		foreach ($arr2 as $key => $value) {
			$data[] = $value[$column_key];
		}
		return $data;
	}
}

class DapingAction extends CommonAction {
	function _initialize() {
		 
		 
	}
	function index(){
		
		
		//$mapforcalc["activity"]="投入使用";
		//$mapforcalc["workdaycount"]=array("exp","is null");
		$calcprojects=M("Project")->where($mapforcalc)->select();
		foreach($calcprojects as $key => $val)
		{
			
		
			if(empty($val["timebegin"]))
			{
				$workdaycount=0;
			}
			else
			{
				$worktimebegin=$val["timebegin"];
				if(empty($val["finish_time"]))
				{
					$worktimeend=date("Y-m-d");
				}
				else
				{
					$worktimeend=$val["finish_time"];
				}
				$workdaycount=$this->diffBetweenTwoDays($worktimebegin,$worktimeend);
			}
			
			
			$mapforPlmschedule_3["plmid"]=$val["id"];
			$mapforPlmschedule_3["realtimebegin"]=array("neq","");
			
			$worktimebegin=M("Plmschedule")->where($mapforPlmschedule_3)->min("realtimebegin");
			$worktimeend=M("Plmschedule")->where("plmid=".$val["id"])->max("realtimeend");
			$elecdaycount=$this->diffBetweenTwoDays($worktimebegin,$worktimeend);
			
			
			M("Project")->where("id=".$val["id"])->setField("workdaycount",$workdaycount);
			M("Project")->where("id=".$val["id"])->setField("elecdaycount",$elecdaycount);
			
			M("Project")->where("id=".$val["id"])->setField("capacity_temp",$val["capacity"]);
			
			
			//M("Project")->where("id=".$val["id"])->setField("timebegin",$worktimebegin);
			//M("Project")->where("id=".$val["id"])->setField("timeend",$worktimeend);
		}
		
		
		
		
        $totNumOfItems=M("Project")->count();//项目总数
		$mapforprojectfortop["projecttype"]="分布式光伏发电";
		$numOfChargingStationItems = M("Project")->where($mapforprojectfortop)->count(); //充电站项目数
		$mapforprojectfortop["projecttype"]="集中式光伏发电";
		$numOfSubstationProjects =  M("Project")->where($mapforprojectfortop)->count(); //换电站项目数
		$mapforprojectfortop["projecttype"]="风力发电";
		$numOfLowspeedVehicles =  M("Project")->where($mapforprojectfortop)->count(); //低速车项目数
		
		
		$totNumOfItems_capacity=M("Project")->sum("capacity");//项目总数
		$totNumOfItems_capacity=round($totNumOfItems_capacity,0);
		$mapforprojectfortop["projecttype"]="分布式光伏发电";
		$numOfChargingStationItems_capacity = M("Project")->where($mapforprojectfortop)->sum("capacity"); //分布式光伏发电
		$numOfChargingStationItems_capacity=round($numOfChargingStationItems_capacity,0);
		$mapforprojectfortop["projecttype"]="集中式光伏发电";
		$numOfSubstationProjects_capacity =  M("Project")->where($mapforprojectfortop)->sum("capacity"); //集中式光伏发电
		$numOfSubstationProjects_capacity=round($numOfSubstationProjects_capacity,0);
		$mapforprojectfortop["projecttype"]="风力发电";
		$numOfLowspeedVehicles_capacity =  M("Project")->where($mapforprojectfortop)->sum("capacity"); //风力发电
		$numOfLowspeedVehicles_capacity=round($numOfLowspeedVehicles_capacity,0);
		
		
		$printdata.="var totNumOfItems=".$this->toJson($totNumOfItems).";";
		$printdata.="var numOfChargingStationItems=".$this->toJson($numOfChargingStationItems).";";
		$printdata.="var numOfSubstationProjects=".$this->toJson($numOfSubstationProjects).";";
		$printdata.="var numOfLowspeedVehicles=".$this->toJson($numOfLowspeedVehicles).";";
		
		
		$printdata.="var totNumOfItems_capacity=".$this->toJson($totNumOfItems_capacity).";";
		$printdata.="var numOfChargingStationItems_capacity=".$this->toJson($numOfChargingStationItems_capacity).";";
		$printdata.="var numOfSubstationProjects_capacity=".$this->toJson($numOfSubstationProjects_capacity).";";
		$printdata.="var numOfLowspeedVehicles_capacity=".$this->toJson($numOfLowspeedVehicles_capacity).";";
		
		$mapforprojectfortopforwarning[warning]=array("eq","1");
		$mapforprojectfortopforwarning[status]=array("eq","1");
		$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->group("plmid")->select();
		foreach($plmwarnings as $key => $val)
		{
			$plmwarningids.=",".$val["plmid"].",";
		}
		$mapforprojectfortopforwarningapprove[status]=array("eq","1");
		$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->group("plmid")->select();
		foreach($plmwarnings as $key => $val)
		{
			$plmwarningids.=",".$val["plmid"].",";
		}
		$mapforplmwarnings['id'] = array('in',$plmwarningids);
		//if(empty($plmwarnings))$projectcount6=0;
		//else $projectcount6=count($plmwarnings);
		$projectcount6=M("Project")->where($mapforplmwarnings)->count();
		if(empty($projectcount6))$projectcount6=0;
		
		
		$mapforProject["city"]=array("neq","");
		$cities=M("Project")->where($mapforProject)->group("province")->field("province")->select();
		foreach($cities as $key => $val)
		{
			$cities[$key]["city"]=str_replace("省","",$val["province"]);
			$cities[$key]["city"]=str_replace("壮族自治区","",$cities[$key]["city"]);
			$cities[$key]["city"]=str_replace("市","",$cities[$key]["city"]);
		}
		foreach($cities as $key => $val)
		{
			$cities[$key][numOfChargingStationItems]=0;
			$cities[$key][numOfSubstationProjects]=0;
			$cities[$key][numOfLowspeedVehicles]=0;
			
				
			$mapforProject[province] = array("like","%".$val[city]."%");
			$mapforProject[projecttype] = array("like","%%");
			for($i=0;$i<=2;$i++)
			{
				if($i==0)
				{
					//$projecttype="分布式光伏发电";
					$para="chaData";
				}
				if($i==1)
				{
					//$projecttype="集中式光伏发电";
					$para="repData";
				}
				if($i==2)
				{
					//$projecttype="风力发电";
					$para="lowData";
				}
				
				$cities[$key][$para][totNumOfItems]=0;//项目总数
				$cities[$key][$para][toBeConstructed]=0;//待施工
				$cities[$key][$para][underConstruction]=0;//施工中
				$cities[$key][$para][hysteresis]=0;
				$cities[$key][$para][completed]=0; //已完成
			
				//$mapforProject[projecttype] = $projecttype;
				//$mapforProject["design_status"]=array("not in","取消,暂停中,暂停");
				$projectList=M("Project")->where($mapforProject)->order("step1 desc")->limit(11)->select();//->field("title,city,design_status,construction_status,activity")
				foreach($projectList as $key1 => $val1)
				{
					$cities[$key][$para][projectList][$key1]["id"]=$val1["id"];
					$cities[$key][$para][projectList][$key1]["name"]=$val1["title"];
					$cities[$key][$para][projectList][$key1]["projecttype"]=$val1["projecttype"];
					$cities[$key][$para][projectList][$key1]["capacity"]=$val1["capacity"];
					$cities[$key][$para][projectList][$key1]["province"]=$val1["province"];
					$cities[$key][$para][projectList][$key1]["city"]=$val1["city"];
					
					$cities[$key][$para][projectList][$key1]["area"]=$val1["area"];
					$cities[$key][$para][projectList][$key1]["totAmt"]=$val1["invest6"];
					$cities[$key][$para][projectList][$key1]["prvnRatioAmt"]=$val1["investpercent1"]."%"."-".$val1["invest6"]*$val1["investpercent1"];
					$cities[$key][$para][projectList][$key1]["selfproportionAmt"]=$val1["investpercent2"]."%"."-".$val1["invest6"]*$val1["investpercent2"];
					
					$plantimebegin=M("Plmschedule")->where("plmid=".$val1["id"])->min("plantimebegin");
					$plantimeend=M("Plmschedule")->where("plmid=".$val1["id"])->max("plantimeend");
					
					$mapforPlmschedule_3["realtimebegin"]=array("neq","");
					$mapforPlmschedule_3["plmid"]=array("eq",$val1["id"]);
					$worktimebegin=M("Plmschedule")->where($mapforPlmschedule_3)->min("realtimebegin");
					$worktimeend=M("Plmschedule")->where("plmid=".$val1["id"])->max("realtimeend");
					
					if(empty($worktimebegin))$worktimebegin=date("Y-m-d");
					if(empty($worktimeend))$worktimeend=date("Y-m-d");
						
					$plandaycount=$this->diffBetweenTwoDays($plantimebegin,$plantimeend);
					$workdaycount=$this->diffBetweenTwoDays($worktimebegin,$worktimeend);
						
				
					
					
					$cities[$key][$para][projectList][$key1]["planCycle"]=$plandaycount;
					$cities[$key][$para][projectList][$key1]["actlCycle"]=$workdaycount;
					$cities[$key][$para][projectList][$key1]["status"]=$val1["design_status"];
					$cities[$key][$para][projectList][$key1]["projectSchedule"]=array();
					
					
					$mapforPlmschedule0[plmid]=$val1["id"];
					$mapforPlmschedule0[status]=1;
					$projectSchedule_0[0]["classify"]="开发";
					$projectSchedule_0[1]["classify"]="设计";
					$projectSchedule_0[2]["classify"]="采购";
					foreach($projectSchedule_0 as $key2 => $val2)
					{
						$mapforPlmschedule0[classify]=array("like","%".$val2["classify"]."%");
						$projectSchedule_0[$key2][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule0)->min("plantimebegin");
						$projectSchedule_0[$key2][plantimeend]=M("Plmschedule")->where($mapforPlmschedule0)->max("plantimeend");
						
						$mapforPlmschedule0temp=$mapforPlmschedule0;
						$mapforPlmschedule0temp[realtimebegin]=array("neq","");
						$projectSchedule_0[$key2][realtimebegin]=M("Plmschedule")->where($mapforPlmschedule0temp)->min("realtimebegin");
						
						$mapforPlmschedule0temp[realtimeend]=array("eq","");
						$realtimeendid=M("Plmschedule")->where($mapforPlmschedule0temp)->getField("id");//判断是否所有工序不是完全结束
						if(empty($realtimeendid))
						{
							$projectSchedule_0[$key2][realtimeend]=M("Plmschedule")->where($mapforPlmschedule0temp)->max("realtimeend");
						}
						
						
						//if(empty($projectSchedule_0[$key2][realtimebegin]))$projectSchedule_0[$key2][realtimebegin]=date("Y-m-d");
						//if(!empty($projectSchedule_1[$key2][realtimebegin])&&empty($projectSchedule_0[$key2][realtimeend]))$projectSchedule_0[$key2][realtimeend]=date("Y-m-d");
					}
					$mapforPlmschedule0[classify]=array("like","%施工%");
					$projectSchedule_1=M("Plmschedule")->where($mapforPlmschedule0)->group("worktype")->select();
					foreach($projectSchedule_1 as $key2 => $val2)
					{
						$mapforPlmschedule0_temp=$mapforPlmschedule0;
						$mapforPlmschedule0_temp[worktype]=array("like","%".$val2["worktype"]."%");
						$projectSchedule_1[$key2][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule0_temp)->min("plantimebegin");
						$projectSchedule_1[$key2][plantimeend]=M("Plmschedule")->where($mapforPlmschedule0_temp)->max("plantimeend");
						
						$mapforPlmschedule0_temptemp=$mapforPlmschedule0_temp;
						$mapforPlmschedule0_temptemp[realtimebegin]=array("neq","");
						$projectSchedule_1[$key2][realtimebegin]=M("Plmschedule")->where($mapforPlmschedule0_temptemp)->min("realtimebegin");
						
						$mapforPlmschedule0_temptemp[realtimeend]=array("eq","");
						$realtimeendid=M("Plmschedule")->where($mapforPlmschedule0_temptemp)->getField("id");//判断是否所有工序不是完全结束
						if(empty($realtimeendid))
						{
							$projectSchedule_1[$key2][realtimeend]=M("Plmschedule")->where($mapforPlmschedule0_temptemp)->max("realtimeend");
						}
						//if(empty($projectSchedule_1[$key2][realtimebegin]))$projectSchedule_1[$key2][realtimebegin]=date("Y-m-d");
						//if(!empty($projectSchedule_1[$key2][realtimebegin])&&empty($projectSchedule_1[$key2][realtimeend]))$projectSchedule_1[$key2][realtimeend]=date("Y-m-d");
					}
					
					$xx=0;
					foreach($projectSchedule_0 as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][name]=$val2["classify"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][startTime]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][endTime]=$val2["realtimeend"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][estimatedTm]=$val2["plantimeend"];
						
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][plantimebegin]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][plantimeend]=$val2["plantimeend"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][realtimebegin]=$val2["realtimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][realtimeend]=$val2["realtimeend"];
						
						if(!empty($val2["plantimebegin"]))
						{
							$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][cycle]=$this->diffBetweenTwoDays($val2["plantimebegin"],$val2["plantimeend"]);//计划周期
						}
						if(!empty($val2["realtimeend"]))
						{
							$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][actlCycle]=$this->diffBetweenTwoDays($val2["realtimebegin"],$val2["realtimeend"]);//实际周期
						}
						$xx++;
					}
					foreach($projectSchedule_1 as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][name]=str_replace("设备及安装","",$val2["worktype"]);
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][startTime]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][endTime]=$val2["realtimeend"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][estimatedTm]=$val2["plantimeend"];
						
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][plantimebegin]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][plantimeend]=$val2["plantimeend"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][realtimebegin]=$val2["realtimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][realtimeend]=$val2["realtimeend"];
						
						if(!empty($val2["plantimebegin"]))
						{
							$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][cycle]=$this->diffBetweenTwoDays($val2["plantimebegin"],$val2["plantimeend"]);//计划周期
						}
						if(!empty($val2["realtimeend"]))
						{
							$cities[$key][$para][projectList][$key1]["projectSchedule"][$xx][actlCycle]=$this->diffBetweenTwoDays($val2["realtimebegin"],$val2["realtimeend"]);//实际周期
						}
						$xx++;
						if($xx>=8)break;
					}
					
					$date=date("Y-m-d");
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					//轨道图0
					
					$mapforPlmschedule0[plmid]=$val1["id"];
					$mapforPlmschedule0[status]=1;
				
					
					$plantimebegin=M("Plmschedule")->where($mapforPlmschedule0)->min("plantimebegin");
					$plantimeend=M("Plmschedule")->where($mapforPlmschedule0)->max("plantimeend");
						
					$mapforPlmschedule0temp=$mapforPlmschedule0;
					$mapforPlmschedule0temp[realtimebegin]=array("neq","");
					$realtimebegin=M("Plmschedule")->where($mapforPlmschedule0temp)->min("realtimebegin");
						
					$mapforPlmschedule0temp[realtimeend]=array("eq","");
					$realtimeendid=M("Plmschedule")->where($mapforPlmschedule0temp)->getField("id");//判断是否所有工序不是完全结束
					if(empty($realtimeendid))
					{
						$realtimeend=M("Plmschedule")->where($mapforPlmschedule0temp)->max("realtimeend");
					}
					
						
			
					$percent=$val1["image_progress"];
					$realpercent=round($percent,0)."%";
					
					$planpercent=$val1["plan_image_progress"];
					$planpercent=round($planpercent,0)."%";
					
					$cities[$key][$para][projectList][$key1]["projectnode0"]=array();
					
					$xx=0;
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][name]="总进度";
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][plantimebegin]=$plantimebegin;
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][plantimeend]=$plantimeend;
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][realtimebegin]=$realtimebegin;
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][realtimeend]=$realtimeend;
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][status]=1;
					
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][planpercent]=$planpercent;
					$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][realpercent]=$realpercent;
					
					
					
					if(($planpercent=="100%")&&($realpercent!="100%"))
					{
						$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][delaydays]= $this->diffBetweenTwoDays($plantimeend, $date);
					}
					else
					{
						$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][delaydays]= "";
					}
					
					
					$planpercent=str_replace("%","",$planpercent);
					$realpercent=str_replace("%","",$realpercent);
					if($planpercent>$realpercent)
					{
						$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][delay]= "2";
					}
					else
					{
						$cities[$key][$para][projectList][$key1]["projectnode0"][$xx][delay]= "1";
					}
					
					
					
					
					
					
					
					//轨道图1
					
					$mapforPlmschedule0[plmid]=$val1["id"];
					$mapforPlmschedule0[status]=1;
					$projectSchedule_0[0]["classify"]="主项";
					$projectSchedule_0[1]["classify"]="施工";
					$projectSchedule_0[2]["classify"]="开发";
					$projectSchedule_0[3]["classify"]="设计";
					$projectSchedule_0[4]["classify"]="采购";
					foreach($projectSchedule_0 as $key2 => $val2)
					{
						$mapforPlmschedule0[classify]=array("like","%".$val2["classify"]."%");
						$projectSchedule_0[$key2][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule0)->min("plantimebegin");
						$projectSchedule_0[$key2][plantimeend]=M("Plmschedule")->where($mapforPlmschedule0)->max("plantimeend");
						
						$mapforPlmschedule0temp=$mapforPlmschedule0;
						$mapforPlmschedule0temp[realtimebegin]=array("neq","");
						$projectSchedule_0[$key2][realtimebegin]=M("Plmschedule")->where($mapforPlmschedule0temp)->min("realtimebegin");
						
						$mapforPlmschedule0temp[realtimeend]=array("eq","");
						$realtimeendid=M("Plmschedule")->where($mapforPlmschedule0temp)->getField("id");//判断是否所有工序不是完全结束
						if(empty($realtimeendid))
						{
							$projectSchedule_0[$key2][realtimeend]=M("Plmschedule")->where($mapforPlmschedule0temp)->max("realtimeend");
						}
						
						
						
						
						$mapforPlmschedule0_1[plmid]=$val1[id];
						$mapforPlmschedule0_1[status]=1;
						$mapforPlmschedule0_1[classify]=array("like","%".$val2["classify"]."%");
						$plmschedules=M("Plmschedule")->where($mapforPlmschedule0_1)->order("sort asc")->select();
						$workweight=0;
						$planworkweight=0;
						$length=0;
						foreach($plmschedules as $key3 => $val3)
						{
							
							$workweight+=$val3["plantimelength"]*$val3["percent"];
							$planworkweight+=$val3["plantimelength"]*$val3["planpercent"];
							$length+=$val3["plantimelength"];
							$plantimebegin=$val3["plantimebegin"];
							$plantimeend=$val3["plantimeend"];
							if(empty($plantimebegin))
							{
								continue;
							}
							
							if(($val1["id"]=="62299")&&($key2=="1"))
							{
								//dump($val3["id"]);
								//dump($val3["plantimelength"]);
								//dump($val3["planpercent"]);
							}
							
						}
						
						if(($val1["id"]=="62299")&&($key2=="1"))
						{
							//dump($planworkweight);
						}
						
						$percent=$workweight/$length;
						$projectSchedule_0[$key2][realpercent]=round($percent,0)."%";
						
						$planpercent=$planworkweight/$length;
						$projectSchedule_0[$key2][planpercent]=round($planpercent,0)."%";
					}
					
					$cities[$key][$para][projectList][$key1]["projectnode"]=array();
					$xx=0;
					
					foreach($projectSchedule_0 as $key2 => $val2)
					{
						if(empty($val2["plantimebegin"]))
						{
							continue;
						}
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][name]=$val2["classify"];
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][plantimebegin]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][plantimeend]=$val2["plantimeend"];
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][realtimebegin]=$val2["realtimebegin"];
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][realtimeend]=$val2["realtimeend"];
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][status]=1;
						
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][planpercent]=$val2["planpercent"];
						$cities[$key][$para][projectList][$key1]["projectnode"][$xx][realpercent]=$val2["realpercent"];
						
						
						
						if(($val2[planpercent]=="100%")&&($val2[realpercent]!="100%"))
						{
							$cities[$key][$para][projectList][$key1]["projectnode"][$xx][delaydays]= $this->diffBetweenTwoDays($val2["plantimeend"], $date);
						}
						else
						{
							$cities[$key][$para][projectList][$key1]["projectnode"][$xx][delaydays]= "";
						}
						
						
						$planpercent=str_replace("%","",$val2[planpercent]);
						$realpercent=str_replace("%","",$val2[realpercent]);
						if($planpercent>$realpercent)
						{
							$cities[$key][$para][projectList][$key1]["projectnode"][$xx][delay]= "2";
						}
						else
						{
							$cities[$key][$para][projectList][$key1]["projectnode"][$xx][delay]= "1";
						}
						
						
						$xx++;
					}
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					//轨道图2
					$mapforPlmschedule_1[plmid]=array("eq",$val1["id"]);
					//$mapforPlmschedule_1[classify]=array("like","%主项%");
					$projectSchedule=M("Plmschedule")->where($mapforPlmschedule_1)->order("classify asc,id asc")->select();
					
					$cities[$key][$para][projectList][$key1]["projectnode1"]=array();
					foreach($projectSchedule as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][name]=str_replace("节点库","",$val2["classify"])."-".$val2["subworktype"];
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][name]=str_replace("专项","",$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][name]);
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][plantimebegin]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][plantimeend]=$val2["plantimeend"];
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realtimebegin]=$val2["realtimebegin"];
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realtimeend]=$val2["realtimeend"];
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][reason]=$val2["reason"];
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][status]=1;
						if(($val2["percent"]=="")||($val2["percent"]=="0%"))
						{
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realtimebegin]="";
						}
						if(($val2["percent"]!="100%"))
						{
							if($val2["percent"]=="")$val2["percent"]="0%";
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realpercent]=$val2["percent"];
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][status]=0;
						}
						else
						{
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realpercent]=$val2["percent"];
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realtimeend]=$val2["realtimeend"];
						}
						
						
						
						$plantimebegin=$val2["plantimebegin"];
						$plantimeend=$val2["plantimeend"];
						
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
						$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][planpercent]=$todayplanpercent."%";
		
						if(($cities[$key][$para][projectList][$key1]["projectnode1"][$key2][planpercent]=="100%")&&($cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realpercent]!="100%"))
						{
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][delaydays]= $this->diffBetweenTwoDays($val2["plantimeend"], $date);
						}
						else
						{
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][delaydays]= "";
						}
						
						
						$planpercent=str_replace("%","",$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][planpercent]);
						$realpercent=str_replace("%","",$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][realpercent]);
						if($planpercent>$realpercent)
						{
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][delay]= "2";
						}
						else
						{
							$cities[$key][$para][projectList][$key1]["projectnode1"][$key2][delay]= "1";
						}
					}
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					/*
					foreach($projectSchedule as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$key2][name]=$val2["subworktype"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$key2][startTime]=$val2["plantimebegin"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$key2][endTime]=$val2["plantimeend"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$key2][estimatedTm]=$val2["plantimeend"];
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$key2][cycle]=$this->diffBetweenTwoDays($val2["plantimebegin"],$val2["plantimeend"]);
						$cities[$key][$para][projectList][$key1]["projectSchedule"][$key2][actlCycle]=$this->diffBetweenTwoDays($val2["realtimebegin"],$val2["realtimeend"]);
					}
					*/
					
					
					$cities[$key][$para][projectList][$key1]["constructionProcess"]=array();
					$mapforPlmscheduledaily["plmid"]=$val1["id"];
					$mapforPlmscheduledaily["classify"]="施工专项节点库";
					$mapforPlmscheduledaily["file"]=array("exp","is not null");
					$projectdailys=M("Plmscheduledaily")->where($mapforPlmscheduledaily)->order("id desc")->limit(30)->select();
					foreach($projectdailys as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["constructionProcess"][$key2][name]=$val2["subworktype"];
						$photos=explode(",",$val2["file"]);
						$imageList=array();
						$x4=0;
						foreach($photos as $key3 => $val3)
						{
							if(!empty($val3)&&((false!==strpos($val3,"png"))||(false!==strpos($val3,"jpg"))||(false!==strpos($val3,"jpeg"))||(false!==strpos($val3,"bmp"))))
							{
								$imageList[$x4]="http://".$_SERVER['HTTP_HOST']."/projecttest/Public/Uploads/".$val3;
								$x4++;
							}
						}
						$cities[$key][$para][projectList][$key1]["constructionProcess"][$key2][imageList]=$imageList;
						$cities[$key][$para][projectList][$key1]["constructionProcess"][$key2]["time"]=$val2["subworktype"];
						$cities[$key][$para][projectList][$key1]["constructionProcess"][$key2][schedule]="1";
						$cities[$key][$para][projectList][$key1]["constructionProcess"][$key2][status]="1";
						$cities[$key][$para][projectList][$key1]["constructionProcess"][$key2][content]=$val2["subworktype"];
					}
					
					//形象进度
					$cities[$key][$para][projectList][$key1]["graphicProcess"]=array();
					$plmfileimage=M("Plmfilediaodu")->where("plmNumber=".$val1["id"])->order("id desc")->select();
					foreach($plmfileimage as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["graphicProcess"][$key2][name]=$val2["title"];
						$photos=explode(",",$val2["newname"]);
						foreach($photos as $key3 => $val3)
						{
							if(!empty($val3))
							{
								$imageList[$key3]="http://".$_SERVER['HTTP_HOST']."/projecttest/Public/Uploads/".$val3;
							}
						}
						$cities[$key][$para][projectList][$key1]["graphicProcess"][$key2][imageList]=$imageList;
						$cities[$key][$para][projectList][$key1]["graphicProcess"][$key2]["time"]=$val2["title"];
						$cities[$key][$para][projectList][$key1]["graphicProcess"][$key2][content]=$val2["title"];
					}
					
					
					$cities[$key][$para][projectList][$key1]["projectdiscussion"]=array();
					$plmdiscuss=M("Plmdiscuss")->where("plmid=".$val1["id"])->order("id desc")->select();//->where("plmid=".$val1["id"])
					foreach($plmdiscuss as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["projectdiscussion"][$key2]["publisher"]=$val2["user"];
						$cities[$key][$para][projectList][$key1]["projectdiscussion"][$key2]["publish"]=$val2["title"];
						$cities[$key][$para][projectList][$key1]["projectdiscussion"][$key2]["publishTime"]=date("Y-m-d H:i",$val2["create_time"]);
						
						$mapforPlmdiscussreply["discussid"]=$val2["id"];
						$reply=M("Plmdiscussreply")->where($mapforPlmdiscussreply)->find();
						$cities[$key][$para][projectList][$key1]["projectdiscussion"][$key2]["reply"]=$reply["user"];
						$cities[$key][$para][projectList][$key1]["projectdiscussion"][$key2]["replycontent"]=$reply["content"];
						$cities[$key][$para][projectList][$key1]["projectdiscussion"][$key2]["replyTime"]=date("Y-m-d H:i",$reply["create_time"]);
					}
					
					$cities[$key][$para][projectList][$key1]["latestinformation"]=array();
					$latestinformation=M("Plmnews")->where("plmid=".$val1["id"])->order("id desc")->select();
					foreach($latestinformation as $key2 => $val2)
					{
						$cities[$key][$para][projectList][$key1]["latestinformation"][$key2]["content"]=$val2["content"];
						$cities[$key][$para][projectList][$key1]["latestinformation"][$key2]["time"]=$val2["time"];
					}
					
					$cities[$key][$para][projectList][$key1]["livevideo"]=array();
					for($key2=0;$key2<=2;$key2++)
					{
						$cities[$key][$para][projectList][$key1]["livevideo"][$key2]["title"]="视频".($key2+1);
						$cities[$key][$para][projectList][$key1]["livevideo"][$key2]["video"]="https://i.loli.net/2021/10/12/1hlIOuTASL3dZB9.png";
					}
					
					
				}
				$cities[$key][$para]["eachAreaCondition"]=array();
				$mapforProject1=$mapforProject;
				$mapforProject1["area"]=array("neq","");
				$areas=M("Project")->where($mapforProject1)->group("area")->field("area")->select();
				foreach($areas as $key1 => $val1)
				{
					$mapforProject1[area] = $val1[area];
					$projects=M("Project")->where($mapforProject1)->field("area,title,address,city,design_status,design_status,activity,projecttype")->select();
				
					$cities[$key][$para][eachAreaCondition][$key1]["area"]=$val1["area"];
					$cities[$key][$para][eachAreaCondition][$key1]["toBeConstructed"]=0;
					$cities[$key][$para][eachAreaCondition][$key1]["underConstruction"]=0;
					$cities[$key][$para][eachAreaCondition][$key1]["completed"]=0;
					$cities[$key][$para][eachAreaCondition][$key1]["hysteresis"]=0;
					foreach($projects as $key2 => $val2)
					{
						if(($val2[design_status]=="立项中")||($val2[design_status]=="待施工")||($val2[design_status]=="取消")||($val2[design_status]=="暂停")||($val2[design_status]=="暂停中"))//待施工
						{
							$cities[$key][$para][eachAreaCondition][$key1]["toBeConstructed"]++;
						}
						if($val2[design_status]=="施工中")//施工中
						{
							$cities[$key][$para][eachAreaCondition][$key1]["underConstruction"]++;
						}
						if($val2[activity]=="完成验收")//完成验收
						{
							$cities[$key][$para][eachAreaCondition][$key1]["completed"]++;
						}
						if($val2[design_status]=="施工完成")
						{
							$cities[$key][$para][eachAreaCondition][$key1]["hysteresis"]++;
						}
					}
				}
				
				//场站分类
				$cities[$key][$para]["stationClassification"]=array();
				//$mapforProject2=$mapforProject;
				$mapforProject2["projecttype"]=array("neq","");
				$taketypes=M("Project")->where($mapforProject2)->group("projecttype")->field("projecttype")->select();
				foreach($taketypes as $key1 => $val1)
				{
					$cities[$key][$para][stationClassification][$key1]["name"]=$val1[projecttype];
					$mapforProject2[projecttype] = $val1[projecttype];
					$cities[$key][$para][stationClassification][$key1]["value"]=M("Project")->where($mapforProject2)->count();
				}
				
				
				$projects=M("Project")->where($mapforProject)->field("city,design_status,construction_status,activity,projecttype")->select();
				foreach($projects as $key1 => $val1)
				{
					/*
					if(false!==strstr($plmwarningids,",".$val1["id"].","))
					{
						$cities[$key][$para][hysteresis]++;//滞后
					}
					*/
					if($val1[projecttype]=="分布式光伏发电")
					{
						$cities[$key][numOfChargingStationItems]++;
					}
					if($val1[projecttype]=="集中式光伏发电")
					{
						$cities[$key][numOfSubstationProjects]++;
					}
					if($val1[projecttype]=="风力发电")
					{
						$cities[$key][numOfLowspeedVehicles]++;
					}				
					
					if(($val1[design_status]=="储备")||($val1[design_status]=="暂存")||($val1[design_status]=="初步申报待审批")||($val1[design_status]=="初步申报审批中")||($val1[design_status]=="初步申报审批通过")||($val1[design_status]=="初步申报审批退回")||($val1[design_status]=="项目计划待审批")||($val1[design_status]=="项目计划审批中")||($val1[design_status]=="项目计划审批退回"))//初申中
					{
						$cities[$key][$para][count1]++;
						$cities[$key][$para][totNumOfItems]++;
					}
					if(($val1[design_status]=="立项中")||($val1[design_status]=="待施工")||($val1[design_status]=="取消")||($val1[design_status]=="暂停")||($val1[design_status]=="暂停中"))//待施工
					{
						$cities[$key][$para][toBeConstructed]++;
						$cities[$key][$para][totNumOfItems]++;
					}
					if(($val1[design_status]=="施工中"))//施工中
					{
						$cities[$key][$para][underConstruction]++;
						$cities[$key][$para][totNumOfItems]++;
					}
					if(($val1[design_status]=="施工完成"))//施工完成
					{
						$cities[$key][$para][hysteresis]++;
						$cities[$key][$para][totNumOfItems]++;
					}
					if($val1[activity]=="完成验收")//完成验收
					{
						$cities[$key][$para][completed]++;
						$cities[$key][$para][totNumOfItems]++;
					}
					if($val1[design_status]=="暂停中")//暂停中
					{
						$cities[$key][$para][count5]++;
						$cities[$key][$para][totNumOfItems]++;
					}
				}
			}
		}
		
		
		$sort = array(  
			'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
			'field'     => 'count2',       //排序字段  
		);  
		$arrSort = array();  
		foreach($cities AS $uniqid => $row){  
			foreach($row AS $key=>$value){  
				$arrSort[$key][$uniqid] = $value;  
			}  
		}  
		if($sort['direction']){  
			array_multisort($arrSort[$sort['field']], constant($sort['direction']), $cities);  
		}
		$cities=array_slice($cities,0,100);
		
		$printdata.="var cityData=".$this->toJson($cities).";";
		
		
		$allprojects=M("Project")->field("title,address,city,design_status,capacity,activity,projecttype,image_progress")->select();
		$tempData[totNumOfItems]=0;
		$tempData[toBeConstructed]=0;
		$tempData[underConstruction]=0;
		$tempData[numOfProjectsUnderConstruction]=0;
		$tempData[hysteresis]=0;
		$tempData[completed]=0;
		$tempData[numOfProjectsInOperation]=0;
		
		
		$tempData[numOfItemsInStg]=0;
		$tempData[numOfProjectsInFeasibilityStdy]=0;
		$tempData[numOfProjectsInBidding]=0;
		$tempData[numOfProjectsUnderContract]=0;
		$tempData[numOfItemsInAcceptance]=0;
		$tempData["percent10count"]=0;
		$tempData["percent30count"]=0;
		$tempData["percent50count"]=0;
		$tempData["percent70count"]=0;
		$tempData["percent90count"]=0;
		$tempData["percent100count"]=0;
		
		$tempData[capacityOfItemsInStg]=0;
		$tempData["percent10capacity"]=0;
		$tempData["percent30capacity"]=0;
		$tempData["percent50capacity"]=0;
		$tempData["percent70capacity"]=0;
		$tempData["percent90capacity"]=0;
		$tempData["percent100capacity"]=0;
		
		//for($x=1;$x<=3;$x++)
		if(1)
		{
			/*
			if($x==1)
			{
				$projecttype="分布式光伏发电";
			}
			if($x==2)
			{
				$projecttype="集中式光伏发电";
			}
			if($x==3)
			{
				$projecttype="风力发电";
			}
			*/
			foreach($allprojects as $key1 => $val1)
			{
				
				if(1)//$val1[projecttype]==$projecttype
				{
					$tempData[totNumOfItems]++;
					if(($val1[image_progress]>=100)||($val1[design_status]=="施工完成")||($val1[design_status]=="完成验收"))
					{
						$tempData["percent100count"]++;
						$tempData["percent100capacity"]+=$val1["capacity"];
					}
					else if(($val1[image_progress]>=90))
					{
						$tempData["percent90count"]++;
						$tempData["percent90capacity"]+=$val1["capacity"];
					}
					else if(($val1[image_progress]>=70))
					{
						$tempData["percent70count"]++;
						$tempData["percent70capacity"]+=$val1["capacity"];
					}
					else if(($val1[image_progress]>=50))
					{
						$tempData["percent50count"]++;
						$tempData["percent50capacity"]+=$val1["capacity"];
					}
					else if(($val1[image_progress]>=30))
					{
						$tempData["percent30count"]++;
						$tempData["percent30capacity"]+=$val1["capacity"];
					}
					else if(($val1[image_progress]>=10))
					{
						$tempData["percent10count"]++;
						$tempData["percent10capacity"]+=$val1["capacity"];
					}
				}
				if(($val1[design_status]=="待施工")||($val1[design_status]=="立项中")||($val1[design_status]=="取消")||($val1[design_status]=="暂停")||($val1[design_status]=="暂停中"))
				{
					$tempData["toBeConstructed"]++;
				}
				if($val1[design_status]=="施工中")//施工中
				{
					$tempData["numOfItemsInStg"]++;
					$tempData["capacityOfItemsInStg"]+=$val1["capacity"];
					$tempData['underConstruction']++;
				}
				if($val1[design_status]=="施工完成")//施工完成
				{
					$tempData['hysteresis']++;
				}
				if($val1[design_status]=="完成验收")//完成验收
				{
					$tempData['completed']++;
				}
				
			}
			
			
			$tempData[capacityOfItemsInStg]=round($tempData[capacityOfItemsInStg],0);
			$tempData["percent10capacity"]=round($tempData[percent10capacity],0);
			$tempData["percent30capacity"]=round($tempData[percent30capacity],0);
			$tempData["percent50capacity"]=round($tempData[percent50capacity],0);
			$tempData["percent70capacity"]=round($tempData[percent70capacity],0);
			$tempData["percent90capacity"]=round($tempData[percent90capacity],0);
			$tempData["percent100capacity"]=round($tempData[percent100capacity],0);
			
			//$mapforCha["projecttype"]=$projecttype;
			$mapforCha1["workdaycount"]=array("gt","0");
			$tempdata=M("Project")->where($mapforCha1)->order("workdaycount desc")->limit(10)->select();
			foreach($tempdata as $key => $val)//建设周期最长Top10
			{
				$tempData[top10WithTheLongestConstructionPd][$key][name]=$val["title"];
				$tempData[top10WithTheLongestConstructionPd][$key][area]=$val["area"];
				$tempData[top10WithTheLongestConstructionPd][$key][cycle]=$val["workdaycount"];
			}
			$tempdata=M("Project")->where($mapforCha1)->order("workdaycount asc")->limit(10)->select();
			foreach($tempdata as $key => $val)//建设周期最短Top10
			{
				$tempData[top10WithTheShortestConstructionPd][$key][name]=$val["title"];
				$tempData[top10WithTheShortestConstructionPd][$key][area]=$val["area"];
				$tempData[top10WithTheShortestConstructionPd][$key][cycle]=$val["workdaycount"];
			}
			
			//$mapforCha["design_status"]=array("neq","取消");
			$tempdata=M("Project")->where($mapforCha)->order("capacity_temp desc")->limit(10)->select();
			foreach($tempdata as $key => $val)//装机容量最大Top10
			{
				$tempData[theLongestPowerCycleTop10][$key][name]=$val["title"];
				$tempData[theLongestPowerCycleTop10][$key][area]=$val["area"];
				$tempData[theLongestPowerCycleTop10][$key][capacity]=$val["capacity"];
			}
			$tempdata=M("Project")->where($mapforCha)->order("capacity_temp asc")->limit(10)->select();
			foreach($tempdata as $key => $val)//装机容量最小Top10
			{
				$tempData[top10WithTheShortestPowerCycle][$key][name]=$val["title"];
				$tempData[top10WithTheShortestPowerCycle][$key][area]=$val["area"];
				$tempData[top10WithTheShortestPowerCycle][$key][capacity]=$val["capacity"];
			}
			
			$mapforCha2=$mapforCha;
			$mapforCha2["province"]=array(array("neq",""),array("exp","is not null"),"and");
			$provinces=M("Project")->where($mapforCha2)->group("province")->field("province")->select();
		
			foreach($provinces as $key => $val)//装机容量最大Top10省份
			{
				$mapfortheLongestPowerProvinceTop10[design_status] = array("neq","取消");
				$mapfortheLongestPowerProvinceTop10[province] = $val[province];
				$theLongestPowerProvinceTop10[$key][name] = $val[province];
				$theLongestPowerProvinceTop10[$key][capacity]=M("Project")->where($mapfortheLongestPowerProvinceTop10)->sum("capacity_temp");
			}
			
			

			$sort = array_column($theLongestPowerProvinceTop10, 'capacity');
			array_multisort($sort, SORT_DESC, $theLongestPowerProvinceTop10);
			$iii=1;
			foreach($theLongestPowerProvinceTop10 as $key => $val)//装机容量最大Top10省份
			{
				if($iii>10)
				{
					break;
				}
				$tempData[theLongestPowerProvinceTop10][$key][name] = $val[name];
				$tempData[theLongestPowerProvinceTop10][$key][capacity] = round($val[capacity],0);
				$tempData[theLongestPowerProvinceTop10][$key][rank] = $iii;
				$tempData[theLongestPowerProvinceTop10][$key][radio] = round(100*$val[capacity]/$totNumOfItems_capacity,0)."%";
				$iii++;
			}
			
			$mapforProject_2["design_status"]="施工中";
			$mapforProject_2["projecttype"]="分布式光伏发电";
			$tempdata=M("Project")->where($mapforProject_2)->order("id asc")->select();//timebegin desc ->limit(10)
			foreach($tempdata as $key => $val)//最近新建项目Top10
			{
				$tempData[top10NewlyBuiltProjects][$key][name]=$val["title"];
				$tempData[top10NewlyBuiltProjects][$key][area]=str_replace("省","",$val["province"]);
				$tempData[top10NewlyBuiltProjects][$key][capacity]=$val["capacity"];
				//$map1_1["plmid"]=$val["id"];
				//$map1_1["realtimebegin"]=array("neq","");
				//$tempData[top10NewlyBuiltProjects][$key][startTime]=$val["timebegin"];
				//$tempData[top10NewlyBuiltProjects][$key][endTime]=$val["finish_time"];
			}
			
			$mapforProject_2["projecttype"]="集中式光伏发电";
			$tempdata=M("Project")->where($mapforProject_2)->order("id asc")->select();//finish_time desc ->limit(10)
			foreach($tempdata as $key => $val)//最近完成项目Top10
			{
				$tempData[top10RecentlyCompletedProjects][$key][name]=$val["title"];
				$tempData[top10RecentlyCompletedProjects][$key][area]=str_replace("省","",$val["province"]);
				$tempData[top10RecentlyCompletedProjects][$key][capacity]=$val["capacity"];
				//$map1_1["plmid"]=$val["id"];
				//$map1_1["realtimebegin"]=array("neq","");
				//$tempData[top10RecentlyCompletedProjects][$key][startTime]=$val["timebegin"];
				//$tempData[top10RecentlyCompletedProjects][$key][endTime]=$val["finish_time"];
			}
			
			$mapforProject_2["projecttype"]="风力发电";
			$tempdata=M("Project")->where($mapforProject_2)->order("id asc")->select();
			foreach($tempdata as $key => $val)//最近新建项目Top10
			{
				$tempData[top10NewlyBuiltProjects1][$key][name]=$val["title"];
				$tempData[top10NewlyBuiltProjects1][$key][area]=str_replace("省","",$val["province"]);
				$tempData[top10NewlyBuiltProjects1][$key][capacity]=$val["capacity"];
			}
			
			
			
			foreach($cities as $key => $val)//地图项目数
			{
				$tempData[numOfMapProjects][$key]['city']=$val["city"];
				$tempData[numOfMapProjects][$key]['value']=0;
				
				$mapforProject5["projecttype"]=array("like","%%");
				$mapforProject5["province"]=array("like","%".$val["city"]."%");
				$tempData[numOfMapProjects][$key]['value']=M("Project")->where($mapforProject5)->count();
				$tempData[numOfMapProjects][$key]['value0']=$tempData[numOfMapProjects][$key]['value'];
				
				$mapforProject5["projecttype"]="分布式光伏发电";
				$tempData[numOfMapProjects][$key]['value1']=M("Project")->where($mapforProject5)->count();
				$mapforProject5["projecttype"]="集中式光伏发电";
				$tempData[numOfMapProjects][$key]['value2']=M("Project")->where($mapforProject5)->count();
				$mapforProject5["projecttype"]="风力发电";
				$tempData[numOfMapProjects][$key]['value3']=M("Project")->where($mapforProject5)->count();
				
			}
			
			
			//13个城市投资项目数总计
			$tempData[totNumOfInvestProjectsIn13Cities]['within2Million']=0;
			$tempData[totNumOfInvestProjectsIn13Cities]['moreThan2Million']=0;
			$tempData[totNumOfInvestProjectsIn13Cities]['provincialCoWithin2Million']=0;
			$tempData[totNumOfInvestProjectsIn13Cities]['provincialCoOvr2Million']=0;
			
			
			$mapforProject_1["city"]=array("neq","");
			$mapforProject_1["design_status"]=array("eq","施工中");
			$cities1=M("Project")->where($mapforProject_1)->group("province")->field("province")->select();
			foreach($cities1 as $key => $val)//13个城市投资项目数
			{
				$tempData[numOfInvestProjectsIn13Cities][$key]['city']=str_replace("省","",$val["province"]);
				$tempData[numOfInvestProjectsIn13Cities][$key]['within2Million']=0;//地市自投资200万以内
				$tempData[numOfInvestProjectsIn13Cities][$key]['moreThan2Million']=0;//地市自投资200万以上
				$tempData[numOfInvestProjectsIn13Cities][$key]['provincialCoWithin2Million']=0;//省公司投资200万以内
				$tempData[numOfInvestProjectsIn13Cities][$key]['provincialCoOvr2Million']=0;//省公司投资200万以上
			
				$mapforProjectfornumOfInvestProjectsIn13Cities[province] = array("like","%".$val[province]."%");
				$mapforProjectfornumOfInvestProjectsIn13Cities["design_status"]="施工中";
				$projectList=M("Project")->where($mapforProjectfornumOfInvestProjectsIn13Cities)->field("projecttype,capacity")->select();
				
				
				foreach($projectList as $key1 => $val1)//13个城市投资项目数
				{
					if($val1["projecttype"]=="分布式光伏发电")
					{
						$tempData[numOfInvestProjectsIn13Cities][$key]['within2Million']++;
						$tempData[totNumOfInvestProjectsIn13Cities]['within2Million']++;
						
						$tempData[numOfInvestProjectsIn13Cities][$key]['moreThan2Million']++;
						$tempData[totNumOfInvestProjectsIn13Cities]['moreThan2Million']++;
						
						$tempData[numOfInvestProjectsIn13Cities][$key]['capacity1']+=$val1["capacity"];
						$tempData[numOfInvestProjectsIn13Cities][$key]['capacity']+=$val1["capacity"];
						$tempData[totNumOfInvestProjectsIn13Cities]['capacity']+=$val1["capacity"];
						$tempData[totNumOfInvestProjectsIn13Cities]['capacity1']+=$val1["capacity"];
					}
					if($val1["projecttype"]=="集中式光伏发电")
					{
						$tempData[numOfInvestProjectsIn13Cities][$key]['within2Million']++;
						$tempData[totNumOfInvestProjectsIn13Cities]['within2Million']++;
						
						$tempData[numOfInvestProjectsIn13Cities][$key]['provincialCoWithin2Million']++;
						$tempData[totNumOfInvestProjectsIn13Cities]['provincialCoWithin2Million']++;
						
						$tempData[numOfInvestProjectsIn13Cities][$key]['capacity2']+=$val1["capacity"];
						$tempData[numOfInvestProjectsIn13Cities][$key]['capacity']+=$val1["capacity"];
						$tempData[totNumOfInvestProjectsIn13Cities]['capacity']+=$val1["capacity"];
						$tempData[totNumOfInvestProjectsIn13Cities]['capacity2']+=$val1["capacity"];
					}
					if($val1["projecttype"]=="风力发电")
					{
						$tempData[numOfInvestProjectsIn13Cities][$key]['within2Million']++;
						$tempData[totNumOfInvestProjectsIn13Cities]['within2Million']++;
						
						$tempData[numOfInvestProjectsIn13Cities][$key]['provincialCoOvr2Million']++;
						$tempData[totNumOfInvestProjectsIn13Cities]['provincialCoOvr2Million']++;
						
						$tempData[numOfInvestProjectsIn13Cities][$key]['capacity3']+=$val1["capacity"];
						$tempData[numOfInvestProjectsIn13Cities][$key]['capacity']+=$val1["capacity"];
						$tempData[totNumOfInvestProjectsIn13Cities]['capacity']+=$val1["capacity"];
						$tempData[totNumOfInvestProjectsIn13Cities]['capacity3']+=$val1["capacity"];
					}
				}
				
				
				$tempData[numOfInvestProjectsIn13Cities][$key]['capacity']=round($tempData[numOfInvestProjectsIn13Cities][$key]['capacity'],0);
				$tempData[numOfInvestProjectsIn13Cities][$key]['capacity1']=round($tempData[numOfInvestProjectsIn13Cities][$key]['capacity1'],0);
				$tempData[numOfInvestProjectsIn13Cities][$key]['capacity2']=round($tempData[numOfInvestProjectsIn13Cities][$key]['capacity2'],0);
				$tempData[numOfInvestProjectsIn13Cities][$key]['capacity3']=round($tempData[numOfInvestProjectsIn13Cities][$key]['capacity3'],0);
			}
			$tempData[totNumOfInvestProjectsIn13Cities]['capacity']=round($tempData[totNumOfInvestProjectsIn13Cities]['capacity'],0);
			$tempData[totNumOfInvestProjectsIn13Cities]['capacity1']=round($tempData[totNumOfInvestProjectsIn13Cities]['capacity1'],0);
			$tempData[totNumOfInvestProjectsIn13Cities]['capacity2']=round($tempData[totNumOfInvestProjectsIn13Cities]['capacity2'],0);
			$tempData[totNumOfInvestProjectsIn13Cities]['capacity3']=round($tempData[totNumOfInvestProjectsIn13Cities]['capacity3'],0);
			
			//近6个月项目新建/完成情况
			$tempData[projectStsInThePast6Months]=array();
			$ii=0;
			for($i=6;$i>=1;$i--)//13个城市投资项目数
			{
				$month=date("Y-m", strtotime("-$i month"));
				$days=date('t', strtotime("-$i month"));
				$tempData[projectStsInThePast6Months][$ii]['date']=date("m月", strtotime("-$i month"));
				
				//$mapfornumOfNewProjects["projecttype"]=$projecttype;
				$mapfornumOfNewProjects["time"]=array("like","%".$month."%");
				$tempData[projectStsInThePast6Months][$ii]['numOfNewProjects']=M("Project")->where($mapfornumOfNewProjects)->count();
				//$mapfornumOfProjectsCompleted["projecttype"]=$projecttype;
				$mapfornumOfProjectsCompleted["finish_time"]=array("between",strtotime($month."-01"),strtotime($month."-$days"));
				$tempData[projectStsInThePast6Months][$ii]['numOfProjectsCompleted']=M("Project")->where($mapfornumOfProjectsCompleted)->count();
				$ii++;
				
			}
			//场站分类
			$tempData[stationClassification]=array();
			$tempData[totStationClassification]=0;
			//$mapforProjectforstationClassification["projecttype"]=$projecttype;
			$taketypes=M("Project")->where($mapforProjectforstationClassification)->group("projecttype")->field("projecttype")->select();
		
			foreach($taketypes as $key => $val)
			{
				$tempData[stationClassification][$key][name]=$val[projecttype];
				
				$mapforProjectforstationClassification[projecttype] = $val[projecttype];
				$tempData[stationClassification][$key][value]=M("Project")->where($mapforProjectforstationClassification)->count();
				$tempData[totStationClassification]+=$tempData[stationClassification][$key][value];//场站分类(充电)饼图合计
				
			}
		
			$tempData[proportionOfProjectsIn13Cities]=array();
			foreach($cities as $key => $val)//13地市项目占比情况
			{
				$tempData[proportionOfProjectsIn13Cities][$key]['city']=$val["city"];
				$tempData[proportionOfProjectsIn13Cities][$key]['province']=$val["city"];
				$tempData[proportionOfProjectsIn13Cities][$key]['totNumOfItems']=0;//项目总数
				$tempData[proportionOfProjectsIn13Cities][$key]['toBeConstructed']=0;//待施工
				$tempData[proportionOfProjectsIn13Cities][$key]['underConstruction']=0;//施工中
				$tempData[proportionOfProjectsIn13Cities][$key]['completed']=0;//已完成
				
				$mapforproportionOfProjectsIn13Cities["province"]=array("like","%".$val["city"]."%");
				//$mapforproportionOfProjectsIn13Cities["projecttype"]=$projecttype;
				$allprojects1=M("Project")->where($mapforproportionOfProjectsIn13Cities)->field("title,address,city,design_status,construction_status,activity,projecttype")->select();
				foreach($allprojects1 as $key1 => $val1)
				{
					$tempData[proportionOfProjectsIn13Cities][$key]['totNumOfItems']++;
					if($val1[design_status]=="施工中")//施工中
					{
						$tempData[proportionOfProjectsIn13Cities][$key]['underConstruction']++;
					}
					if($val1[design_status]=="施工完成")//投入使用
					{
						$tempData[proportionOfProjectsIn13Cities][$key]['completed']++;
					}
					if(($val2[design_status]=="待施工")||($val2[design_status]=="立项中")||($val2[design_status]=="取消")||($val2[design_status]=="暂停")||($val2[design_status]=="暂停中"))//待施工
					{
						$tempData[proportionOfProjectsIn13Cities][$key]["toBeConstructed"]++;
					}
				}
			}
			
			
			if($x==1)
			{
				//$chaData=$tempData;
			}
			if($x==2)
			{
				//$repData=$tempData;
			}
			if($x==3)
			{
				//$lowData=$tempData;
			}
		}
		$chaData=$tempData;
		$repData=$tempData;
		$lowData=$tempData;
		
		header('Content-Type:application/json; charset=utf-8');
		
		$banners=M("Banner")->order("title asc")->select();
		$x=0;
		$y=0;
		foreach($banners as $key => $val)//13地市项目占比情况
		{
			if($val["classify"]=="竖图")
			{
				$bannerarray1[$x]["imgUrl"]="http://".$_SERVER['HTTP_HOST']."/projecttest/Public/login/".$val["file"];
				$x++;
			}
			else
			{
				$bannerarray2[$y]["imgUrl"]="http://".$_SERVER['HTTP_HOST']."/projecttest/Public/login/".$val["file"];
				$y++;
			}
		}
		
		$printdata.="var chaData=".$this->toJson($chaData).";";
		$printdata.="var repData=".$this->toJson($repData).";";
		$printdata.="var lowData=".$this->toJson($lowData).";";
		
		$printdata.="var imgList1=".$this->toJson($bannerarray1).";";
		$printdata.="var imgList2=".$this->toJson($bannerarray2).";";
	
		$printdata=str_replace(";",";\r\n",$printdata);
		$printdata=str_replace(",",",\r\n",$printdata);
		$printdata=str_replace("{","{\r\n",$printdata);
		$printdata=str_replace("}","\r\n}",$printdata);
		$d = file_put_contents('../bigScreen/js/data.js',$printdata);//,FILE_APPEND
	}
	
	/**
	 * 浏览器友好的变量输出
	 * @param mixed $var 变量
	 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
	 * @param string $label 标签 默认为空
	 * @param boolean $strict 是否严谨 默认为true
	 * @return void|string
	 */
	function dump1($var, $echo=true, $label=null, $strict=true) {
		$label = ($label === null) ? '' : rtrim($label) . ' ';
		if (!$strict) {
			if (ini_get('html_errors')) {
				$output = print_r($var, true);
				$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
			} else {
				$output = $label . print_r($var, true);
			}
		} else {
			ob_start();
			var_dump($var);
			$output = ob_get_clean();
			if (!extension_loaded('xdebug')) {
				$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
				$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
			}
		}
		if ($echo) {
			echo($output);
			return null;
		}else
			return $output;
	}
	
	/**
	* 使用 json_encode() 处理数组时，
	* 不对数组里面的中文字串进行转义
	*
	* @param  array   $arr 待处理数组
	* @return string       Json格式的字符串
	*/
	 
	function toJson($arr) {
		$ajax = $this->ToUrlencode($arr);    
		$str_json = json_encode($ajax);
		return urldecode($str_json);     
	}
	 
	/**
	* 将数组里面key字串和value字串用urlencode转换格式后返回
	*
	* @param  array $arr 数组
	* @return array
	*/
	function ToUrlencode($arr) {
		$temp = array();
		if (is_array($arr)) {
			foreach ($arr AS $key => $row) {
				//若key为中文，也需要进行urlencode处理
				$key = urlencode($key);
				if (is_array($row)) {
					$temp[$key] = $this->ToUrlencode($row);
				} else {
					$temp[$key] = urlencode($row);
				}
			}
		} else {
			$temp = $arr;
		}
	 
		return $temp;
	}
	 
	

}

?>