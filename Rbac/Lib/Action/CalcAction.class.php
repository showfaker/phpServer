<?php
class CalcAction extends Action {
	function calc()
	{
	
	
		$mapforproject[design_status]=array("in","施工完成,完成验收");
		$projects=M("Project")->where($mapforproject)->select();
		foreach($projects as $key => $val)
		{
			$schedulemap[taskid]=$val[id];
			$schedulemap[status]=1;
			M("Schedule")->where($schedulemap)->setField("status",0);
		}
		
		$h=date("H",time());
		$i=date("i",time());
		if(($h!="08")&&($h!="14")&&($h!="18")&&($i!="00"))
		{
			//dump($h);
			//return;
		}
		set_time_limit(0);
		$warningdata=M("Plmwarning")->select();
		foreach($warningdata as $key => $val)
		{
			M("Plmwarning")->where("id=".$val[id])->delete();
			//M("Plmwarning")->where("id=".$val[id])->setField("status",0);
		}
		
	
		$mapforproject[design_status]=array("in","施工中");
		//$mapforproject[activity]=array("neq","投入使用");
		$projects=M("Project")->where($mapforproject)->select();
		
		
		$warningsetting=M("Bjsz")->where("id=1")->find();
		
		$date=date("Y-m-d");
		foreach($projects as $key => $val)
		{
			dump("---------------------".$val[title]."---------------------");
			$map[plmid]=$val[id];
			$map[status]=1;
			$map[subworktype]=array("neq","付款申请");
			$plmschedules=M("Plmschedule")->where($map)->order("classify asc,sort asc")->select();
			foreach($plmschedules as $key1 => $val1)
			{
				$worktype=$val1["worktype"]."-".$val1["subworktype"];
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
				
				$todayplanpercent=round($todayplanpercent,2);
				$realpercent=str_replace("%","",$val1["realpercent"]);
				if($realpercent=="")$realpercent=0;
				$diff=round($todayplanpercent-$realpercent,2);
				
				
				/*
				$datatesttest[title]=$val1[worktype]."-".$val2[subworktype]."：昨日应当完成".$todayplanpercent."，昨日实际完成".$realpercent."，完成进度差".$diffpercent."，所占比重".$val2[weight];
				$datatesttest[content]=$val[id].$val['title'];
				$datatesttest[create_time]=time();
				$datatesttest[update_time]=date("Y-m-d H:i:s",time());
				M("Testtest")->add($datatesttest);
				*/
					
				//if(($todayplanpercent-$realpercent)>$mastpercent)
				//if($diff>$mastpercent)
				if(($todayplanpercent>$realpercent)&&($todayplanpercent=="100"))
				{
					//报警
					$data[plmid]=$val[id];
					$data[number]=$val[number];
					$data[address]=$val['title'];
					$data[city]=$val['city'];
					$data[create_time]=time();
					$data[status]=1;
					$data[worktype]=$worktype;
					$data[classify]=$val1['classify'];
					$data[plantimebegin]=$plantimebegin;
					$data[plantimeend]=$plantimeend;
					$data[planpercent]=$todayplanpercent."%";
					$data[realpercent]=$realpercent."%";
					//$data[d]=($todayplanpercent-$realpercent)."%";
					$data[d]=$diff."%";
					
					$data[prewarning]="";
					$data[prewarning_time]="";
					$data[warning]="";
					$data[warning_time]="";
					
					$data[warning]=1;
					$data[warning_time]=time();
					
					
					$mapforwarningRepeat["plmid"]=$val["id"];
					$mapforwarningRepeat["warning"]=1;
					$mapforwarningRepeat["worktype"]=$worktype;
					$repeatinfo=M("Plmwarning")->where($mapforwarningRepeat)->find();
					if(!empty($repeatinfo))
					{
						$data["id"]=$repeatinfo["id"];
						M("Plmwarning")->save($data);
						$result=$repeatinfo["id"];
						$data["id"]="";
					}
					else
					{
						$result=M("Plmwarning")->add($data);
					}
					
					/*
					$datatest[title]=$result;
					$datatest[content]=$val[id].$val['title'];
					$datatest[create_time]=time();
					$datatest[update_time]=date("Y-m-d H:i:s",time());
					$datatest[photo]="1";
					M("Test")->add($datatest);
					*/
					//报警发送
					$content=$val['title']."项目的".$worktype."专项发生报警，请您关注。";
					
					
					
					$pushinfo = M("Bjsz")->where("id=1")->find();
					if(1)
					{
						//推送给设置的推送人
						$subtitle=explode(",",$pushinfo[subtitle]);
						foreach($subtitle as $key5 => $val5)
						{
							if(!empty($val5))
							{
								$mapforuserinfo[nickname]=array("eq",$val5);
								$userinfoaccount=M("User")->where($mapforuserinfo)->getField("account");
								if(!empty($userinfoaccount))
								{
									$this->push($userinfoaccount,$content);
								}
							}
						}
						$this->push($val[projectmanager],$content);
					}
					else if(false!==strpos($worktype,"竣工验收工程"))
					{
						//$this->push("chongfazhan",$content);
						//$this->push($val[supervisor],$content);
					}
					else
					{
						$this->push($val[engineeringmanage],$content);
					}
					
					
					$this->push("admin",$content);
					
				}
				//else if($diff>($mastpercent-30))//($todayplanpercent-$realpercent)
				if(false!==strstr($val1["classify"],"开发"))
				{
					$warningset=$warningsetting["subtitle1"];
				}
				if(false!==strstr($val1["classify"],"设计"))
				{
					$warningset=$warningsetting["subtitle2"];
				}
				if(false!==strstr($val1["classify"],"采购"))
				{
					$warningset=$warningsetting["subtitle3"];
				}
				if(false!==strstr($val1["classify"],"施工"))
				{
					$warningset=$warningsetting["subtitle4"];
				}
				if(false!==strstr($val1["classify"],"主项"))
				{
					$warningset=$warningsetting["subtitle4"];
				}
				if(($diff>$warningset)&&($todayplanpercent!="100"))
				{
					//预警
					$data[plmid]=$val[id];
					$data[number]=$val[number];
					$data[address]=$val['title'];
					$data[city]=$val['city'];
					$data[create_time]=time();
					$data[status]=1;
					$data[worktype]=$worktype;
					$data[classify]=$val1['classify'];
					$data[plantimebegin]=$plantimebegin;
					$data[plantimeend]=$plantimeend;
					$data[planpercent]=$todayplanpercent."%";
					$data[realpercent]=$realpercent."%";
					//$data[d]=($todayplanpercent-$realpercent)."%";
					$data[d]=$diff."%";
					
					$data[prewarning]="";
					$data[prewarning_time]="";
					$data[warning]="";
					$data[warning_time]="";
					
					$data[prewarning]=1;
					$data[prewarning_time]=time();
					
					$mapforwarningRepeat["plmid"]=$val["id"];
					$mapforwarningRepeat["prewarning"]=1;
					$mapforwarningRepeat["worktype"]=$worktype;
					$repeatinfo=M("Plmwarning")->where($mapforwarningRepeat)->find();
					if(!empty($repeatinfo))
					{
						$data["id"]=$repeatinfo["id"];
						M("Plmwarning")->save($data);
						$result=$repeatinfo["id"];
						$data["id"]="";
					}
					else
					{
						$result=M("Plmwarning")->add($data);
					}
					/*
					$datatest[title]=$result;
					$datatest[content]=$val[id].$val['title'];
					$datatest[create_time]=time();
					$datatest[update_time]=date("Y-m-d H:i:s",time());
					$datatest[photo]="2";
					M("Test")->add($datatest);
					*/
					//预警发送
					$content=$val['title']."项目的".$worktype."专项发生预警，请您关注。";
					
					if(1)
					{
						//$this->push("chongfazhan",$content);
						//$this->push($val[areadetail],$content);//带班
						$this->push($val[projectmanager],$content);//项目经理
						//$this->push($val[supervisor],$content);//工程责任人
					}
					else if(false!==strpos($worktype,"弱电"))
					{
						//$this->push("wangzhichao",$content);
						//$this->push("taojianhua",$content);
					}
					else
					{
						$this->push($val[supervisor],$content);
					}
					$this->push("admin",$content);
				}
				else
				{
					$data[plmid]=$val[id];
					$data[number]=$val[number];
					$data[address]=$val['title'];
					$data[city]=$val['city'];
					$data[create_time]=time();
					$data[status]=1;
					$data[worktype]=$worktype;
					$data[classify]=$val1['classify'];
					$data[plantimebegin]=$plantimebegin;
					$data[plantimeend]=$plantimeend;
					$data[planpercent]=$todayplanpercent."%";
					$data[realpercent]=$realpercent."%";
					//$data[d]=($todayplanpercent-$realpercent)."%";
					$data[d]=$diff."%";
					
					$data[prewarning]="";
					$data[prewarning_time]="";
					$data[warning]="";
					$data[warning_time]="";
					
					$result=M("Plmwarning")->add($data);
					
					/*
					$datatest[title]=$result;
					$datatest[content]=$val[id].$val['title'];
					$datatest[create_time]=time();
					$datatest[update_time]=date("Y-m-d H:i:s",time());
					$datatest[photo]="3";
					M("Test")->add($datatest);
					*/
					
				}
			}
		}
		
		foreach($projects as $key => $val)
		{
			
			$map[plmid]=$val[id];
			$map[status]=1;
			$plmschedules=M("Plmschedule")->where($map)->order("sort asc")->select();//->group("worktype")
			$workweight=0;
			$planworkweight=0;
			$length=0;
			foreach($plmschedules as $key1 => $val1)
			{
				
				$workweight+=$val1["plantimelength"]*$val1["percent"];
				$planworkweight+=$val1["plantimelength"]*$val1["planpercent"];
				$length+=$val1["plantimelength"];
				
				
				
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
				$planpercent=$todayplanpercent."%";
				M("Plmschedule")->where("id=".$val1["id"])->setField("planpercent",$planpercent);
				
				
				
				
				
			}
			$percent=$workweight/$length;
			dump("@@@@@@@@@@@@@@@@@@".$val[title]."@@@@@@@@@@@@@@@@@".$percent);
			M("Project")->where("id=".$val["id"])->setField("image_progress",round($percent,0));
			
			$planpercent=$planworkweight/$length;
			dump("@@@@@@@@@@@@@@@@@@".$val[title]."@@@@@@@@@@@@@@@@@".$percent);
			M("Project")->where("id=".$val["id"])->setField("plan_image_progress",round($planpercent,0));
		}
		/*
		$mapforprewarning[prewarning]=1;
		$mapforprewarning[status]=1;
		$prewarningcount=M("Plmwarning")->where($mapforprewarning)->count();
		$mapforwarning[warning]=1;
		$mapforwarning[status]=1;
		$warningcount=M("Plmwarning")->where($mapforwarning)->count();
		$content="今日数据：当前项目有".$prewarningcount."项预警，".$warningcount."项报警，请进入系统或APP查看";
		$users=M("User")->select();
		foreach($users as $key => $val)
		{
			$mapforuser[number]=preg_replace('/[^0-9]/','',$val[number]);
			$appinfo=M("User")->where($mapforuser)->field("devicetype,clientid")->find();
			//$mapforschedule[user]=$data[user];
			//$mapforschedule[status]=1;
			//$badge=M("Schedule")->where($mapforschedule)->count();
			if(!empty($appinfo[devicetype])&&(!empty($appinfo[clientid])))
			{
				$aapush=new AapushAction();
				if($appinfo[devicetype]=="1")
				{
					$aapush->pushMessageToSingle($appinfo[clientid],$content,0,1);
				}
				else
				{
					$aapush->pushMessageToSingle($appinfo[clientid],$content,0,0);
				}
			}
		}
		*/
	}
	
	function push($nameoraccount,$content)
	{
		//return;
		$datapushlog[title]=$nameoraccount;
		$datapushlog[content]=$content;
		$datapushlog[create_time]=time();
		$datapushlog[ctime]=date("Y-m-d H:i:s",time());
		M("Pushlog")->add($datapushlog);
		
		
		
		return;
		
		if($nameoraccount!="admin")
		{
			//return;
		}
		$where['account']  = array('eq',$nameoraccount);
		$where['nickname']  = array('eq',$nameoraccount);
		$where['_logic'] = 'or';
		$mapforuser['_complex'] = $where;
		$appinfo=M("User")->where($mapforuser)->field("devicetype,clientid")->find();
		if(!empty($appinfo[devicetype])&&(!empty($appinfo[clientid])))
		{
			$aapush=new AapushAction();
			if($appinfo[devicetype]=="1")
			{
				$aapush->pushMessageToSingle($appinfo[clientid],$content,0,1);//1是苹果 0是安卓
			}
			else
			{
				$aapush->pushMessageToSingle($appinfo[clientid],$content,0,0);
			}
		}
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
	  return 1+($second1 - $second2) / 86400;
	}
	
	function test ()
	{
	  $second1 = strtotime("2018-06-28");
	  $second2 = strtotime("2018-06-29");
		
	  if ($second1 < $second2) {
		$tmp = $second2;
		$second2 = $second1;
		$second1 = $tmp;
	  }
	  dump( 1+($second1 - $second2) / 86400);
	}
}
?>