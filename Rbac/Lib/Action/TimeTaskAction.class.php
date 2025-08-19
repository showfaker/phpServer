<?php
class TimeTaskAction extends CommonAction {		

	
	public function autoDownload() {
		
		$date=date("Y-m-d");
		$month=date("Y-m");
		$year=date("Y");
		
		//截至月末在建
		$map["design_status"]=array("eq","施工中");
		$count1=M("Project")->where($map)->count();
		$capacity1=M("Project")->where($map)->sum("capacity");
		if(empty($capacity1))$capacity1=0;else $capacity1=round($capacity1,4);
		
		//本月并网
		$mapforProject2["design_status"]=array("in","施工完成,完成验收");
		$mapforProject2["finish_time"]=array("like","%".$month."%");
		$count2=M("Project")->where($mapforProject2)->count();
		$capacity2=M("Project")->where($mapforProject2)->sum("capacity");
		if(empty($capacity2))$capacity2=0;else $capacity2=round($capacity2,4);
		
		//本年度并网
		$mapforProject3["design_status"]=array("in","施工完成,完成验收");
		$mapforProject3["finish_time"]=array("like",$year."%");
		$count3=M("Project")->where($mapforProject3)->count();
		$capacity3=M("Project")->where($mapforProject3)->sum("capacity");
		if(empty($capacity3))$capacity3=0;else $capacity3=round($capacity3,4);
		
		//其中风电
		$mapforProject3["projecttype"]=array("like","%风%");
		$count4=M("Project")->where($mapforProject3)->count();
		$capacity4=M("Project")->where($mapforProject3)->sum("capacity");
		if(empty($capacity4))$capacity4=0;else $capacity4=round($capacity4,4);
		
		//其中光伏
		$mapforProject3["projecttype"]=array("like","%光伏%");
		$count5=M("Project")->where($mapforProject3)->count();
		$capacity5=M("Project")->where($mapforProject3)->sum("capacity");
		if(empty($capacity5))$capacity5=0;else $capacity5=round($capacity5,4);
		
		//累计并网
		$mapforProject4["design_status"]=array("in","施工完成,完成验收");
		$count6=M("Project")->where($mapforProject4)->count();
		$capacity6=M("Project")->where($mapforProject4)->sum("capacity");
		if(empty($capacity6))$capacity6=0;else $capacity6=round($capacity6,4);
		
		//其中风电
		$map1=$mapforProject4;
		$map1["projecttype"]=array("like","%风%");
		$count7=M("Project")->where($map1)->count();
		$capacity7=M("Project")->where($map1)->sum("capacity");
		if(empty($capacity7))$capacity7=0;else $capacity7=round($capacity7,4);
		
		//其中光伏
		$map1["projecttype"]=array("like","%光伏%");
		$count8=M("Project")->where($map1)->count();
		$capacity8=M("Project")->where($map1)->sum("capacity");
		if(empty($capacity8))$capacity8=0;else $capacity8=round($capacity8,4);
		
		//项目管理类
		$map2["design_status"]=array("eq","施工中");
		$map2["operatetype"]=array("like","%项目管理类%");
		$count9=M("Project")->where($map2)->count();
		$list9=M("Project")->where($map2)->select();
		$count9_1=0;
		foreach($list9 as $key => $val)
		{
			if($val["image_progress"]<$val["plan_image_progress"])
			{
				$count9_1++;
				$list9[$key]["warning"]=1;
			}
		}
		
		
		//全流程项目
		$map2["operatetype"]=array("in","全流程项目,全部");
		$count10=M("Project")->where($map2)->count();
		$list10=M("Project")->where($map2)->select();
		$count10_1=0;
		foreach($list10 as $key => $val)
		{
			if($val["image_progress"]<$val["plan_image_progress"])
			{
				$count10_1++;
				$list10[$key]["warning"]=1;
			}
		}
		
		//纯EPC
		$map2["operatetype"]=array("like","%纯EPC%");
		$count11=M("Project")->where($map2)->count();
		$list11=M("Project")->where($map2)->select();
		$count11_1=0;
		foreach($list11 as $key => $val)
		{
			if($val["image_progress"]<$val["plan_image_progress"])
			{
				$count11_1++;
				$list11[$key]["warning"]=1;
			}
		}
		
		
	
		
		$map3[design_status]=array("in","施工中");
		$map3[operatetype]=array("in","全流程项目,非全流程项目-纯EPC,非全流程项目-设计类,非全流程项目-采购类,非全流程项目-施工类");
		$list12=M("Project")->where($map3)->select();
		foreach($list12 as $key => $val)
		{
			$mapforPlmnode["realtimeend"]=array("like","%%");
			$mapforPlmnode["warning"]=array("like","%%");
			$mapforPlmnode1["realtimeend"]=array("like","%%");
			$mapforPlmnode1["warning"]=array("like","%%");
			
			//本月到期节点
			$mapforPlmnode["plmid"]=array("eq",$val["id"]);
			$mapforPlmnode["plantimeend"]=array("like","%".$month."%");
			$list12[$key][nodecount1]=M("Plmschedule")->where($mapforPlmnode)->count();
			//按时完成
			$mapforPlmnode["realtimeend"]=array("neq","");
			$mapforPlmnode["warning"]=array("neq","1");
			$list12[$key][nodecount2]=M("Plmschedule")->where($mapforPlmnode)->count();
			//超期完成
			$mapforPlmnode["realtimeend"]=array("neq","");
			$mapforPlmnode["warning"]=array("eq","1");
			$list12[$key][nodecount3]=M("Plmschedule")->where($mapforPlmnode)->count();
			//未完成
			$mapforPlmnode["realtimeend"]=array("eq","");
			$mapforPlmnode["warning"]=array("like","%%");
			$list12[$key][nodecount4]=M("Plmschedule")->where($mapforPlmnode)->count();
			
			//累计到期节点
			$mapforPlmnode1["plmid"]=array("eq",$val["id"]);
			$mapforPlmnode1["plantimeend"]=array("lt",$month."-31");
			$list12[$key][nodecount5]=M("Plmschedule")->where($mapforPlmnode1)->count();
			
			//按时完成
			$mapforPlmnode1["realtimeend"]=array("neq","");
			$mapforPlmnode1["warning"]=array("neq","1");
			$list12[$key][nodecount6]=M("Plmschedule")->where($mapforPlmnode1)->count();
			
			//超期完成
			$mapforPlmnode1["realtimeend"]=array("neq","");
			$mapforPlmnode1["warning"]=array("eq","1");
			$list12[$key][nodecount7]=M("Plmschedule")->where($mapforPlmnode1)->count();
			
			//未完成
			$mapforPlmnode1["realtimeend"]=array("eq","");
			$mapforPlmnode1["warning"]=array("like","%%");
			$list12[$key][nodecount8]=M("Plmschedule")->where($mapforPlmnode1)->count();
			
			
			
			$mapforPlmschedule["plmid"]=array("eq",$val["id"]);
			$mapforPlmschedule["classify"]=array("like","%主项%");
			$list12[$key][planfinishtime]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
			$list12[$key][schedules0]=M("Plmschedule")->where($mapforPlmschedule)->order("plantimebegin asc")->select();
			
			
			//主项大节点
			$list12[$key][schedules1]=M("Plmschedule")->where($mapforPlmschedule)->order("plantimebegin asc")->group("worktype")->select();
			
			foreach($list12[$key]["schedules1"] as $key1 => $val1)
			{
				$mapforPlmschedule0["plmid"]=array("eq",$val["id"]);
				$mapforPlmschedule0["classify"]=array("like","%主项%");
				$mapforPlmschedule0["worktype"]=array("eq",$val1["worktype"]);
				$list12[$key]["schedules1"][$key1][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule0)->min("plantimebegin");
				$list12[$key]["schedules1"][$key1][plantimeend]=M("Plmschedule")->where($mapforPlmschedule0)->max("plantimeend");
				
				$mapforPlmschedule0temp=$mapforPlmschedule0;
				$mapforPlmschedule0temp[realtimebegin]=array("neq","");
				$list12[$key]["schedules1"][$key1][realtimebegin]=M("Plmschedule")->where($mapforPlmschedule0temp)->min("realtimebegin");
				
				$mapforPlmschedule0temp[realtimeend]=array("eq","");
				$realtimeendid=M("Plmschedule")->where($mapforPlmschedule0temp)->getField("id");//判断是否所有工序不是完全结束
				if(empty($realtimeendid))
				{
					$list12[$key]["schedules1"][$key1][realtimeend]=M("Plmschedule")->where($mapforPlmschedule0temp)->max("realtimeend");
				}
			}
			
			foreach($list12[$key]["schedules1"] as $key1 => $val1)
			{
				$mapforPlmschedule11["plmid"]=array("eq",$val["id"]);
				$mapforPlmschedule11["classify"]=array("like","%主项%");
				$mapforPlmschedule11["worktype"]=array("eq",$val1["worktype"]);
				$subschedules=M("Plmschedule")->where($mapforPlmschedule11)->field("plantimelength,percent")->select();
				$workweight=0;
				$planworkweight=0;
				$length=0;
				foreach($subschedules as $key2 => $val2)
				{
					$workweight+=$val2["plantimelength"]*$val2["percent"];
					$planworkweight+=$val2["plantimelength"]*$val2["planpercent"];
					$length+=$val2["plantimelength"];
				}
				$percent=$workweight/$length;
				if(!empty($percent))
				{
					$percent=round($percent,0);
					$list12[$key]["schedules1"][$key1][realpercent]=$percent."%";
				}
				
			}
			
			foreach($list12[$key][schedules1] as $key1 => $val1)
			{
				if($key1==0)
				{
					$list12[$key][schedules1][$key1][block]=1;
				}
				
				
				$plantimebegin=$val1["plantimebegin"];
				$plantimeend=$val1["plantimeend"];
				if(empty($plantimebegin))
				{
					continue;
				}
				
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
				$list12[$key][schedules1][$key1][planpercent]=$todayplanpercent."%";
				
				
				
				if(($list12[$key][schedules1][$key1][planpercent]!="100%")&&($list12[$key][schedules1][$key1][realpercent]<$list12[$key][schedules1][$key1][planpercent]))
				{
					$list12[$key][schedules1][$key1][deviate]= "是";
				}
				else
				{
					$list12[$key][schedules1][$key1][deviate]= "";
				}
			
			
				if(($list12[$key][schedules1][$key1][planpercent]=="100%")&&($list12[$key][schedules1][$key1][realpercent]!="100%"))
				{
					$list12[$key][schedules1][$key1][delaydays]= $this->diffBetweenTwoDays($val1["plantimeend"], $date);
				}
				else
				{
					$list12[$key][schedules1][$key1][delaydays]= "";
				}
			}
			
			
			
			//主项小节点
			//$mapforPlmschedule["classify"]=array("like","%主项%");
			$mapforPlmschedule1["plmid"]=array("eq",$val["id"]);
			$mapforPlmschedule1["plantimeend"]=array("like","%".$month."%");//本月到期节点
			$list12[$key][schedules2]=M("Plmschedule")->where($mapforPlmschedule1)->order("classify asc,sort asc")->select();
			foreach($list12[$key]["schedules2"] as $key1 => $val1)
			{
				
				$list12[$key]["schedules2"][$key1][plantimebegin]=$val1["plantimebegin"];
				$list12[$key]["schedules2"][$key1][plantimeend]=$val1["plantimeend"];
				$list12[$key]["schedules2"][$key1][realtimebegin]=$val1["realtimebegin"];
				$list12[$key]["schedules2"][$key1][realtimeend]=$val1["realtimeend"];
				$list12[$key]["schedules2"][$key1][realpercent]=$val1["percent"];
				
				if(false!==strstr($val1["classify"],"主项"))
				{
					$list12[$key]["schedules2"][$key1][dept]="工程部";
				}
				if(false!==strstr($val1["classify"],"施工"))
				{
					$list12[$key]["schedules2"][$key1][dept]="工程部";
				}
				if(false!==strstr($val1["classify"],"开发"))
				{
					$list12[$key]["schedules2"][$key1][dept]="开发部";
				}
				if(false!==strstr($val1["classify"],"设计"))
				{
					$list12[$key]["schedules2"][$key1][dept]="设计部";
				}
				if(false!==strstr($val1["classify"],"采购"))
				{
					$list12[$key]["schedules2"][$key1][dept]="采购部";
				}
			}
			
			foreach($list12[$key][schedules2] as $key1 => $val1)
			{
				if($val1["classify"]!=$list12[$key][schedules2][$key1-1]["classify"])
				{
					$list12[$key][schedules2][$key1][block]=1;
					$x=$key1;
					$list12[$key][schedules2][$x]["rowscount"]=1;
				}
				if($val1["classify"]==$list12[$key][schedules2][$key1-1]["classify"])
				{
					$list12[$key][schedules2][$x]["rowscount"]++;
				}
				
				$plantimebegin=$val1["plantimebegin"];
				$plantimeend=$val1["plantimeend"];
				if(empty($plantimebegin))
				{
					continue;
				}
				
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
				$list12[$key][schedules2][$key1][planpercent]=$todayplanpercent."%";
				
				
				if(($list12[$key][schedules2][$key1][planpercent]!="100%")&&($list12[$key][schedules2][$key1][planpercent]!="0%")&&($list12[$key][schedules2][$key1][realpercent]/1<$list12[$key][schedules2][$key1][planpercent]/1))
				{
					$list12[$key][schedules2][$key1][deviate]= "是";
				}
				else
				{
					$list12[$key][schedules2][$key1][deviate]= "";
				}
				
				
				if(($list12[$key][schedules2][$key1][planpercent]=="100%")&&($list12[$key][schedules2][$key1][realpercent]!="100%"))
				{
					$list12[$key][schedules2][$key1][delaydays]= $this->diffBetweenTwoDays($val1["plantimeend"], $date);
				}
				else
				{
					$list12[$key][schedules2][$key1][delaydays]= "";
				}
			}
				
			foreach($list12[$key][schedules2] as $key1 => $val1)
			{
				$list12[$key][schedules2][$key1][classify]=str_replace("节点库","",$val1["classify"]);
			}
			$list12[$key][schedules2count]=count($list12[$key][schedules2]);
			
			
			/*
			//专项
			$list12[$key]["schedules3"][0]["classify"]="施工";
			$list12[$key]["schedules3"][1]["classify"]="设计";
			$list12[$key]["schedules3"][2]["classify"]="采购";
			$list12[$key]["schedules3"][3]["classify"]="开发";
			$list12[$key]["schedules3"][0]["dept"]="工程部";
			$list12[$key]["schedules3"][1]["dept"]="设计部";
			$list12[$key]["schedules3"][2]["dept"]="采购部";
			$list12[$key]["schedules3"][3]["dept"]="开发部";
			foreach($list12[$key]["schedules3"] as $key1 => $val1)
			{
				
				$mapforPlmschedule3["plmid"]=array("eq",$val["id"]);
				$mapforPlmschedule3["classify"]=array("like","%".$val1["classify"]."%");
				$list12[$key]["schedules3"][$key1][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule3)->min("plantimebegin");
				$list12[$key]["schedules3"][$key1][plantimeend]=M("Plmschedule")->where($mapforPlmschedule3)->max("plantimeend");
				
				$mapforPlmschedule0temp=$mapforPlmschedule3;
				$mapforPlmschedule0temp[realtimebegin]=array("neq","");
				$list12[$key]["schedules3"][$key1][realtimebegin]=M("Plmschedule")->where($mapforPlmschedule0temp)->min("realtimebegin");
				
				$mapforPlmschedule0temp[realtimeend]=array("eq","");
				$realtimeendid=M("Plmschedule")->where($mapforPlmschedule0temp)->getField("id");//判断是否所有工序不是完全结束
				if(empty($realtimeendid))
				{
					$list12[$key]["schedules3"][$key1][realtimeend]=M("Plmschedule")->where($mapforPlmschedule0temp)->max("realtimeend");
				}
			}
			foreach($list12[$key]["schedules3"] as $key1 => $val1)
			{
				$mapforPlmschedule33["plmid"]=array("eq",$val["id"]);
				$mapforPlmschedule33["classify"]=array("like","%".$val1["classify"]."%");
				$subschedules=M("Plmschedule")->where($mapforPlmschedule33)->field("plantimelength,percent")->select();
				$workweight=0;
				$planworkweight=0;
				$length=0;
				foreach($subschedules as $key2 => $val2)
				{
					$workweight+=$val2["plantimelength"]*$val2["percent"];
					$planworkweight+=$val2["plantimelength"]*$val2["planpercent"];
					$length+=$val2["plantimelength"];
				}
				
				
				$percent=$workweight/$length;
				if(!empty($percent))
				{
					$percent=round($percent,0);
					$list12[$key]["schedules3"][$key1][realpercent]=$percent."%";
				}
				
			}
			
			foreach($list12[$key][schedules3] as $key1 => $val1)
			{
				if($key1==0)
				{
					$list12[$key][schedules3][$key1][block]=1;
				}
				
				$plantimebegin=$val1["plantimebegin"];
				$plantimeend=$val1["plantimeend"];
				if(empty($plantimebegin))
				{
					continue;
				}
				
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
				$list12[$key][schedules3][$key1][planpercent]=$todayplanpercent."%";
				
				
				if(($list12[$key][schedules3][$key1][planpercent]!="100%")&&($list12[$key][schedules3][$key1][realpercent]<$list12[$key][schedules3][$key1][planpercent]))
				{
					$list12[$key][schedules3][$key1][deviate]= "是";
				}
				else
				{
					$list12[$key][schedules3][$key1][deviate]= "";
				}
				
				
				if(($list12[$key][schedules3][$key1][planpercent]=="100%")&&($list12[$key][schedules3][$key1][realpercent]!="100%"))
				{
					$list12[$key][schedules3][$key1][delaydays]= $this->diffBetweenTwoDays($val1["plantimeend"], $date);
				}
				else
				{
					$list12[$key][schedules3][$key1][delaydays]= "";
				}
			}
			*/
			
			
		}
		
		
		
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/monthreport.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		//$objActSheet->setTitle ($title);
		
		//$objActSheet->setCellValue ( 'A1', $title );
		//$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		//$objActSheet->setCellValue ( 'F2', $subtitle);
		
		$objActSheet->setCellValue ('M2',date('Y-m-d'));
		
		$objActSheet->setCellValue ('A6',$count1);
		$objActSheet->setCellValue ('B6',$capacity1."mw");
		$objActSheet->setCellValue ('C6',$count2);
		$objActSheet->setCellValue ('D6',$capacity2."mw");
		$objActSheet->setCellValue ('E6',$count3);
		$objActSheet->setCellValue ('F6',$capacity3."mw");
		$objActSheet->setCellValue ('G6',$count4);
		$objActSheet->setCellValue ('H6',$capacity4."mw");
		$objActSheet->setCellValue ('I6',$count5);
		$objActSheet->setCellValue ('J6',$capacity5."mw");
		$objActSheet->setCellValue ('K6',$count6);
		$objActSheet->setCellValue ('L6',$capacity6."mw");
		$objActSheet->setCellValue ('M6',$count7);
		$objActSheet->setCellValue ('N6',$capacity7."mw");
		$objActSheet->setCellValue ('O6',$count8);
		$objActSheet->setCellValue ('P6',$capacity8."mw");
		
		$a7="截至月末在建项目共".$count1."个，其中：\r\n";
		$a7.="■ 项目管理类项目".$count9."个，分别为";
		foreach($list9 as $key=>$val)
		{
			$a7.="$val[title]；";
		}
		$a7.="\r\n■ 全流程项目".$count10."个，整体进度偏离$count10_1";
		if($count10_1>0)
		{
			$a7.="，分别为";
		}
		foreach($list10 as $key=>$val)
		{
			if($val["warning"]=="1")
			{
				$a7.="$val[title]；";
			}
		}
		$a7.="\r\n■ 纯EPC项目".$count11."个，整体进度偏离$count11_1";
		if($count11_1>0)
		{
			$a7.="，分别为";
		}
		foreach($list11 as $key=>$val)
		{
			if($val["warning"]=="1")
			{
				$a7.="$val[title]；";
			}
		}
		
		$objActSheet->setCellValue ('A7',$a7);
		
		/*
		if($array_th==null)
		{
			$array_th=array_keys($data[0]);
		}
		foreach($array_th as $key=>$value)
		{
			$objActSheet->getCellByColumnAndRow($key,3)->setValue($value);
		}
		*/
		
		$currentrow=10;
		foreach ($list12 as $key => $vo ) 
		{
			$temp="（".($key+1)."）".$vo[title]."(计划".$vo["planfinishtime"]."并网，计划总体进度".$vo["plan_image_progress"]."%，实际总体进度".$vo["image_progress"]."%）";
			$objActSheet->mergeCells('A'.$currentrow.":".'P'.$currentrow);
			$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue($temp);
			$objActSheet->getStyle('A'.$currentrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objActSheet->getStyle('A'.$currentrow)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'AAC2D7')));
			$currentrow++;
			
			$objActSheet->mergeCells('A'.$currentrow.":".'A'.$currentrow);
			$objActSheet->mergeCells('B'.$currentrow.":".'E'.$currentrow);
			$objActSheet->mergeCells('F'.$currentrow.":".'G'.$currentrow);
			$objActSheet->mergeCells('H'.$currentrow.":".'I'.$currentrow);
			$objActSheet->mergeCells('J'.$currentrow.":".'K'.$currentrow);
			$objActSheet->mergeCells('L'.$currentrow.":".'L'.$currentrow);
			$objActSheet->mergeCells('M'.$currentrow.":".'P'.$currentrow);
			
			$objActSheet->getStyle('A'.$currentrow.":".'P'.$currentrow)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'DDEBF7')));
			
			$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue("序号");
			$objActSheet->getCellByColumnAndRow(1,$currentrow)->setValue("一级节点");
			$objActSheet->getCellByColumnAndRow(5,$currentrow)->setValue("计划完成时间");
			$objActSheet->getCellByColumnAndRow(7,$currentrow)->setValue("计划进度");
			$objActSheet->getCellByColumnAndRow(9,$currentrow)->setValue("实际进度");
			$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("状态");
			$objActSheet->getCellByColumnAndRow(12,$currentrow)->setValue("原因说明");
			$currentrow++;
			
			foreach ($vo["schedules1"] as $key1 => $schedule1)
			{
				
				$objActSheet->mergeCells('A'.$currentrow.":".'A'.$currentrow);
				$objActSheet->mergeCells('B'.$currentrow.":".'E'.$currentrow);
				$objActSheet->mergeCells('F'.$currentrow.":".'G'.$currentrow);
				$objActSheet->mergeCells('H'.$currentrow.":".'I'.$currentrow);
				$objActSheet->mergeCells('J'.$currentrow.":".'K'.$currentrow);
				$objActSheet->mergeCells('L'.$currentrow.":".'L'.$currentrow);
				$objActSheet->mergeCells('M'.$currentrow.":".'P'.$currentrow);
			
				$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue($key+1);
				$objActSheet->getCellByColumnAndRow(1,$currentrow)->setValue($schedule1["worktype"]);
				$objActSheet->getCellByColumnAndRow(5,$currentrow)->setValue($schedule1["plantimeend"]);//$schedule1["plantimebegin"]."~".
				$objActSheet->getCellByColumnAndRow(7,$currentrow)->setValue($schedule1["planpercent"]);
				$objActSheet->getCellByColumnAndRow(9,$currentrow)->setValue($schedule1["realpercent"]);
				
				if($schedule1["deviate"]=="是")
				{
					$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("偏离");
				}
				if($schedule1["delaydays"]>0)
				{
					$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("超期".$schedule1["delaydays"]."天");
				}
				$objActSheet->getCellByColumnAndRow(12,$currentrow)->setValue($schedule1["reason"]);
				
				$currentrow++;
			}
			
			$temp="项目累计到期节点".$vo["nodecount5"]."个，其中按时完成".$vo["nodecount6"]."个，超期完成".$vo["nodecount7"]."个，未完成".$vo["nodecount8"]."个。\r\n项目本月到期节点".$vo["nodecount1"]."个，其中按时完成".$vo["nodecount2"]."个，超期完成".$vo["nodecount3"]."个，未完成".$vo["nodecount4"]."个。具体情况如下：";
			$objActSheet->mergeCells('A'.$currentrow.":".'P'.$currentrow);
			$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue($temp);
			$objActSheet->getStyle('A'.$currentrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
			$currentrow++;
			
			
			$objActSheet->mergeCells('A'.$currentrow.":".'A'.$currentrow);
			$objActSheet->mergeCells('B'.$currentrow.":".'C'.$currentrow);
			$objActSheet->mergeCells('D'.$currentrow.":".'E'.$currentrow);
			$objActSheet->mergeCells('F'.$currentrow.":".'G'.$currentrow);
			$objActSheet->mergeCells('H'.$currentrow.":".'I'.$currentrow);
			$objActSheet->mergeCells('J'.$currentrow.":".'K'.$currentrow);
			$objActSheet->mergeCells('L'.$currentrow.":".'L'.$currentrow);
			$objActSheet->mergeCells('M'.$currentrow.":".'N'.$currentrow);
			$objActSheet->mergeCells('O'.$currentrow.":".'P'.$currentrow);
			
			$objActSheet->getStyle('A'.$currentrow.":".'P'.$currentrow)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => 'DDEBF7')));
			$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue("序号");
			$objActSheet->getCellByColumnAndRow(1,$currentrow)->setValue("计划分类");
			$objActSheet->getCellByColumnAndRow(3,$currentrow)->setValue("节点名称");
			$objActSheet->getCellByColumnAndRow(5,$currentrow)->setValue("计划完成时间");
			$objActSheet->getCellByColumnAndRow(7,$currentrow)->setValue("计划进度");
			$objActSheet->getCellByColumnAndRow(9,$currentrow)->setValue("实际进度");
			$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("状态");
			$objActSheet->getCellByColumnAndRow(12,$currentrow)->setValue("归属部门");
			$objActSheet->getCellByColumnAndRow(14,$currentrow)->setValue("原因说明");
			
			$currentrow++;
			foreach ($vo["schedules2"] as $key2 => $schedule2)
			{			
				
				$objActSheet->mergeCells('A'.$currentrow.":".'A'.$currentrow);
				$objActSheet->mergeCells('B'.$currentrow.":".'C'.$currentrow);
				$objActSheet->mergeCells('D'.$currentrow.":".'E'.$currentrow);
				$objActSheet->mergeCells('F'.$currentrow.":".'G'.$currentrow);
				$objActSheet->mergeCells('H'.$currentrow.":".'I'.$currentrow);
				$objActSheet->mergeCells('J'.$currentrow.":".'K'.$currentrow);
				$objActSheet->mergeCells('L'.$currentrow.":".'L'.$currentrow);
				$objActSheet->mergeCells('M'.$currentrow.":".'N'.$currentrow);
				$objActSheet->mergeCells('O'.$currentrow.":".'P'.$currentrow);
				
				$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue($key2+1);
				$objActSheet->getCellByColumnAndRow(1,$currentrow)->setValue($schedule2["classify"]);
				$objActSheet->getCellByColumnAndRow(3,$currentrow)->setValue($schedule2["worktype"]."-".$schedule2["subworktype"]);
				$objActSheet->getCellByColumnAndRow(5,$currentrow)->setValue($schedule2["plantimeend"]);//$schedule2["plantimebegin"]."~".
				$objActSheet->getCellByColumnAndRow(7,$currentrow)->setValue($schedule2["planpercent"]);
				$objActSheet->getCellByColumnAndRow(9,$currentrow)->setValue($schedule2["realpercent"]);
				
				if($schedule2["deviate"]=="是")
				{
					$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("偏离");
				}
				if($schedule2["delaydays"]>0)
				{
					$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("超期".$schedule2["delaydays"]."天");
				}
				
				$objActSheet->getCellByColumnAndRow(12,$currentrow)->setValue($schedule2["dept"]);
				$objActSheet->getCellByColumnAndRow(14,$currentrow)->setValue($schedule2["reason"]);
				$currentrow++;
				
			}

			/*
			foreach ($vo["schedules3"] as $key3 => $schedule3)
			{			
				
				$objActSheet->mergeCells('A'.$currentrow.":".'A'.$currentrow);
				$objActSheet->mergeCells('B'.$currentrow.":".'C'.$currentrow);
				$objActSheet->mergeCells('D'.$currentrow.":".'E'.$currentrow);
				$objActSheet->mergeCells('F'.$currentrow.":".'G'.$currentrow);
				$objActSheet->mergeCells('H'.$currentrow.":".'I'.$currentrow);
				$objActSheet->mergeCells('J'.$currentrow.":".'J'.$currentrow);
				$objActSheet->mergeCells('K'.$currentrow.":".'L'.$currentrow);
				$objActSheet->mergeCells('M'.$currentrow.":".'N'.$currentrow);
				$objActSheet->mergeCells('O'.$currentrow.":".'P'.$currentrow);
			
				$objActSheet->getCellByColumnAndRow(0,$currentrow)->setValue($key3+1);
				$objActSheet->getCellByColumnAndRow(1,$currentrow)->setValue($schedule3["classify"]."专项计划");
				$objActSheet->getCellByColumnAndRow(3,$currentrow)->setValue("-");
				$objActSheet->getCellByColumnAndRow(5,$currentrow)->setValue($schedule3["plantimebegin"]."~".$schedule3["plantimeend"]);
				$objActSheet->getCellByColumnAndRow(7,$currentrow)->setValue($schedule3["planpercent"]);
				$objActSheet->getCellByColumnAndRow(9,$currentrow)->setValue($schedule3["realpercent"]);
				
				if($schedule3["deviate"]=="是")
				{
					$objActSheet->getCellByColumnAndRow(11,$currentrow)->setValue("偏离");
				}
				if($schedule3["delaydays"]>0)
				{
					$objActSheet->getCellByColumnAndRow(10,$currentrow)->setValue("超期".$schedule3["delaydays"]."天");
				}
				
				$objActSheet->getCellByColumnAndRow(12,$currentrow)->setValue($schedule3["dept"]);
				$objActSheet->getCellByColumnAndRow(14,$currentrow)->setValue($schedule3["reason"]);
				$currentrow++;
			}
			*/
		}				
		/*
		foreach ( $data as $r => $dataRow ) 
		{
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value)
			{		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
			}		 
		}
		*/
		//$filename = $file;
		$excelname=$month."月度报告"."_".date("Y-m-d-H-i-s").".xls";
		$filename = iconv('utf-8','gb2312',$excelname);

		$savePath = '../Public/Uploads/autoDownload/';

		//判断目录存在否，存在给出提示，不存在则创建目录
        if (!is_dir($savePath)){  
            mkdir($savePath,0777,true); 
        }

        $insert_data = array(
        	'name'=>$excelname,
        	'create_time'=>time()
        	);
        M('autodownload')->add($insert_data);
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( $savePath.$filename );
		
		echo 1;
		return;
	
		
	}

}
?>