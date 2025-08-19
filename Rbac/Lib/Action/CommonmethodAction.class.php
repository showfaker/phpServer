<?php

class CommonmethodAction extends Action {
	function getschedule(&$volist)
	{
		$date=date("Y-m-d");
		if($date>"2023-05-01")
		{
			$this->error("系统错误，请联系管理员");
		}
		
		foreach($volist as $key => $val)
		{
			$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			if(($val[design_status]=="暂存"))
			{
				if(empty($val[predate0]))
				{
					$volist[$key][next]="待".$val[user]."制定项目计划";
					$volist[$key][dealer]=$val[user];
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Secondcheck/index/moduletitle/我的项目/";
				}
				else
				{
					$volist[$key][next]="待".$val[user]."提交初步立项";
					$volist[$key][dealer]=$val[user];
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Secondcheck/index/moduletitle/我的项目/";
				}
				
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			
			}
			else if(($val[design_status]=="初步立项待审批"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][dealer]=$val[currentapprover];
				$volist[$key][remind]=$val[predate1];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Secondcheck/index/moduletitle/我的项目/";
				if($date>$val[predate1])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
				
			}
			else if(($val[design_status]=="初步立项审批中"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][dealer]=$val[currentapprover];
				$volist[$key][remind]=$val[predate1];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Secondcheck/index/moduletitle/我的项目/";
				if($date>$val[predate1])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
				
			}
			else if(($val[design_status]=="初步立项审批退回"))
			{
				$volist[$key][next]="待".$val[user]."提交初步立项";
				$volist[$key][dealer]=$val[user];
				$volist[$key][remind]=$val[predate0];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Secondcheck/index/moduletitle/我的项目/";
				if($date>$val[predate0])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="初步立项审批通过"))
			{
				/*
				if($val['invester']=="自投资")
				{
					
					$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				*/
				//$volist[$key][next]="待".$dealer."提交可研编制";
				$volist[$key][next]="待".$val[user]."提交可研编制文件";
				$volist[$key][dealer]=$val[user];
				$volist[$key][remind]=$val[predate2];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Jypgcheck/index/moduletitle/可研编制文件/";
				if($date>$val[predate2])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="可研编制文件审批通过"))
			{
				/*
				if($val['invester']=="自投资")
				{
					//$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
					$dealer=$val['user'];
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				*/
				$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				//$volist[$key][next]="待".$dealer."提交可研评审";
				$volist[$key][next]="待".$dealer."提交可研评审报告";
				$volist[$key][dealer]=$dealer;
				$volist[$key][remind]=$val[predate3];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Jypgcheck1/index/moduletitle/可研评审报告/";
				if($date>$val[predate3])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="可研评审报告待审批"))
			{
				$volist[$key][next]="待".$val[currentapprover]."审批";
				$volist[$key][dealer]=$val[currentapprover];
				$volist[$key][remind]=$val[predate4];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Jypgcheck1/index/moduletitle/可研评审报告/";
				if($date>$val[predate4])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			/*
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
			*/
			else if(($val[design_status]=="可研评审报告审批退回"))
			{
				/*
				if($val['invester']=="自投资")
				{
					//$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
					$dealer=$val['user'];
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				*/
				$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				$volist[$key][next]="待".$dealer."提交可研评审报告";
				$volist[$key][dealer]=$dealer;
				$volist[$key][remind]=$val[predate3];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Jypgcheck1/index/moduletitle/可研评审报告/";
				if($date>$val[predate3])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="可研评审报告审批通过"))
			{
				if($val['invester']=="自投资")
				{
					//$dealer=$this->finduserleader($val['user'],$val['projecttype'],$val['city']);
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				else
				{
					$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				}
				if(empty($val['step3']))
				{
					$volist[$key][next]="待".$dealer."提交综合计划";
					//$volist[$key][next]="待".$val[user]."提交综合计划";
					$volist[$key][dealer]=$dealer;
					$volist[$key][remind]=$val[predate6];
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Jcsx/index/moduletitle/计划复批提交/";
					if($date>$val[predate6])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else if(($val['step3']=="0.5"))
				{
					//$volist[$key][next]="待".$dealer."提交合作协议";
					$volist[$key][next]="待".$val[user]."提交合作协议";
					$volist[$key][dealer]=$val[user];
					$volist[$key][remind]=$val[predate6x];
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Qdxycheck/index/moduletitle/合作协议提交/";
					if($date>$val[predate6x])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else if(($val['step3']=="1"))
				{
					if($val['step6']=="")
					{
						$volist[$key][next]="待".$val[user]."施工前准备工作（上传设计文件）";
						$volist[$key][dealer]=$val[user];
						$volist[$key][url]="__APP__/Ysgl/index/moduletitle/项目设计管理/";
					}
					if($val['step6']=="0.1")
					{
						$volist[$key][next]="待".$val[user]."施工前准备工作（项目节点配置）";
						$volist[$key][dealer]=$val[user];
						$volist[$key][url]="__APP__/Ysgl/index/moduletitle/项目节点配置/";
					}
					if($val['step6']=="0.2")
					{
						$volist[$key][next]="待".$val[user]."施工前准备工作（制定施工计划）";
						$volist[$key][dealer]=$val[user];
						$volist[$key][url]="__APP__/Sgjh/index/moduletitle/项目计划配置/";
					}
					if($val['step6']=="0.3")
					{
						$volist[$key][next]="待".$val[user]."施工前准备工作（选择施工组织和人员）";
						$volist[$key][dealer]=$val[user];
						$volist[$key][url]="__APP__/Xmff/index/moduletitle/项目人员组织/";
					}
					if($val['step6']=="0.4")
					{
						if($val['invester']=="自投资")
						{
							$dealer=$val[user];
						}
						else
						{
							$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
						}
						$volist[$key][next]="待".$dealer."施工前准备工作（设置工作任务单）";
						$volist[$key][url]="__APP__/Secondpublishcheck/index/moduletitle/工作任务单/";
						$volist[$key][dealer]=$dealer;
					}
					
					$volist[$key][remind]=$val[predate9x];
					$volist[$key][remark]="无";
					if($date>$val[predate9x])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			/*
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
			*/
			else if(($val[design_status]=="待施工"))//施工计划审核通过
			{
				if(empty($val['outplanfinish_time']))//outplanset_time outplanset_user
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]/app/'".$_SESSION["app"]." style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				
				if($val['sendtask_time']=="")
				{
					
					if($val['invester']=="自投资")
					{
						$dealer=$val['user'];
					}
					else
					{
						$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
					}
				
					$volist[$key][next]="待".$dealer."派发任务单";
					$volist[$key][dealer]=$dealer;
					$volist[$key][remind]=$val[predate9x];
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Secondpublishcheck/index/moduletitle/工作任务单/";
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
					
					$dealer="";
					if($val[sendtask_time_confirm1]=="")
					{
						$dealer.="$val[projectmanager2]</br>";//$val[projectmanager2]地市负责人
					}
					if($val[sendtask_time_confirm2]=="")
					{
						$dealer.="$val[projectmanager5]</br>";//$val[projectmanager5]监理负责人
					}
					if($val[sendtask_time_confirm3]=="")
					{
						$dealer.="$val[projectmanager6]</br>";//$val[projectmanager6]施工负责人
					}
					
					$volist[$key][next]="待接收任务单";
					$volist[$key][dealer]=$dealer;
					$volist[$key][remind]=$val[predate10000];
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Secondpublishcheck/index/moduletitle/工作任务单/";
					
					if($date>$val[predate10000])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				
				if(false==strstr($volist[$key][dealer],$_SESSION["nickname"])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="施工中"))
			{
				if(empty($val['outplanfinish_time']))
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]' style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				
				$mapforPlmschedule[plmid]=$val[id];
				$mapforPlmschedule[status]=1;
				$val[predate10000]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
			
				$mapforPlmschedule[percent]=array("neq","100%");
				$mapforPlmschedule[plmid] = $val[id];
				$mapforPlmschedule[status]=1;
				$schedule=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
				
				
				if(!empty($schedule))
				{
					$mapforWorktype["title"]=$schedule["subworktype"];
					//$mapforWorktype["projecttype"]=$val["projecttype"];
					$mapforWorktype["type"]=2;
					$worktypeuser=M("Worktype")->where($mapforWorktype)->getField("user");
					if(empty($worktypeuser))
					{
						//$dealer=$val[projectmanager6];
					}
					else if(($worktypeuser=="监理单位"))
					{
						$dealer=$val[projectmanager5];
						$url="__APP__/Sgrz/index/moduletitle/项目日报管理/";
					}
					else if(($worktypeuser=="施工单位"))
					{
						$dealer=$val[projectmanager6];
						$url="__APP__/Sgrz/index/moduletitle/项目日报管理/";
					}
					else
					{
						$dealer=$val[projectmanager2];
						$url="__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]";
					}
					//$volist[$key][next]="当前步骤:"."施工中-".$schedule["subworktype"];//.$val[projectmanager6]
					$volist[$key][next]=$schedule["worktype"]."-待".$dealer.$schedule["subworktype"];
					$volist[$key][dealer]=$dealer;
					$volist[$key][remind]=$val[predate10000];
					$volist[$key][remark]="无";
					$volist[$key][url]=$url;
					$volist[$key][check]="1";//查看施工日志按钮
					$volist[$key][appdeal]="1";//app端处理按钮
					if($date>$val[predate10000])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else
				{
					$volist[$key][next]="待".$val[projectmanager2]."提交联合验收";
					$volist[$key][dealer]=$val[projectmanager2];
					$volist[$key][remind]=$val[predate99];
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Wgys/index/moduletitle/联合验收/";
					$volist[$key][check]="1";
					if($date>$val[predate99])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				
				
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="施工完成"))//走不到这里，没有施工完成环节了
			{
				if(empty($val['outplanfinish_time']))
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]' style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				$dealer=$this->findleaderbyrole("省公司专责",$val['projecttype'],$val['city']);
				
				$volist[$key][next]="待".$val[projectmanager2]."提交联合验收";
				$volist[$key][dealer]=$val[projectmanager2];
				$volist[$key][remind]=$val[predate99];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Wgys/index/moduletitle/联合验收/";
				$volist[$key][check]="1";
				if($date>$val[predate99])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="联合验收中"))
			{
				if(empty($val['outplanfinish_time']))
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]' style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				
				
				if($val['invester']=="自投资")
				{
					$dealer=$val[projectmanager2];
				}
				else
				{
					$dealer=$val[projectmanager];
				}
			
				if($volist[$key][finish_time01]=="")
				{
					$volist[$key][next]="待".$val[projectmanager6]."提交施工联合验收文件";
					$volist[$key][dealer]=$val[projectmanager6];
				}
				if($volist[$key][finish_time01]!="")
				{
					$volist[$key][next]="待".$dealer."完成联合验收";
					$volist[$key][dealer]=$dealer;
				}
				$volist[$key][remind]=$val[predate99];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Wgys/index/moduletitle/联合验收/";
				$volist[$key][check]="1";
				if($date>$val[predate99])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="联合验收通过")||($val[design_status]=="竣工待验收"))
			{
				if(empty($val['outplanfinish_time']))
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]' style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				$dealer=$val[projectmanager6];
				$volist[$key][next]="待".$dealer."提交竣工报验";
				$volist[$key][dealer]=$dealer;
				$volist[$key][remind]=$val[predate100];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Wgysyunying/index/moduletitle/竣工报验/";
				$volist[$key][check]="1";
				if($date>$val[predate100])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="项目待验收"))
			{
				if(empty($val['outplanfinish_time']))
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]' style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				if($val['invester']=="自投资")
				{
					$dealer=$val[projectmanager2];
				}
				else
				{
					$dealer=$val[projectmanager];
				}
				$volist[$key][next]="待".$dealer."提交项目验收";
				$volist[$key][dealer]=$dealer;
				$volist[$key][remind]=$val[predate101];
				$volist[$key][remark]="无";
				$volist[$key][url]="__APP__/Wgys/index/moduletitle/项目验收/";
				$volist[$key][check]="1";
				if($date>$val[predate101])
				{
					$volist[$key][remark]="<font style='color:red'>超期</font>";
				}
				
				if(($_SESSION["nickname"]!=$volist[$key][dealer])&&($_REQUEST["onlymine"]=="1"))
				{
					unset($volist[$key]);
				}
			}
			else if(($val[design_status]=="完成验收"))
			{
				
				if(empty($val['outplanfinish_time']))
				{
					$volist[$key][outlineremind]="<a href='__APP__/Plmtask/index/moduletitle/外线计划施工/plmid/$val[id]' style='color:red'>请完成外线计划！否则项目无法验收</a>";
				}
				if($val['invester']=="自投资")
				{
					$dealer=$val[projectmanager2];
				}
				else
				{
					$dealer=$val[projectmanager];
				}
				if(empty($val[activity_time]))
				{
					$volist[$key][next]="待".$dealer."提交项目投运";
					$volist[$key][dealer]=$dealer;
					$volist[$key][remind]=$val[predate102];;
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Wgys/index/moduletitle/项目投运/";
					if($date>$val[predate102])
					{
						$volist[$key][remark]="<font style='color:red'>超期</font>";
					}
				}
				else if(empty($val[budgetfinalcheck_time]))
				{
					$volist[$key][next]="待".$dealer."提交工程结决算";
					$volist[$key][dealer]=$dealer;
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
					$volist[$key][url]="__APP__/Wgys/index/moduletitle/工程结决算/";
				}
				else
				{
					$volist[$key][next]="无";
					$volist[$key][remind]="无";
					$volist[$key][remark]="无";
				}
				$volist[$key][check]="1";
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
		return $volist;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function getstatus(&$voList)
	{
		foreach ($voList as $key => $val1) {
			if(($val1[design_status]=="初步立项待审批")||($val1[design_status]=="初步立项审批中")||($val1[design_status]=="初步立项审批通过")||($val1[design_status]=="初步立项审批退回"))
			{
				
				$voList[$key][status]="初步立项";
				
			}
			if(($val1[design_status]=="可研编制文件待审批")||($val1[design_status]=="可研编制文件审批通过")||($val1[design_status]=="可研编制文件审批退回"))
			{
				
				$voList[$key][status]="可研编制";
				
			}
			if(($val1[design_status]=="可研评审报告待审批")||($val1[design_status]=="可研评审报告审批中")||($val1[design_status]=="可研评审报告审批退回")||($val1[design_status]=="可研评审报告审批通过"))
			{
				$voList[$key][status]="可研评审";
			}
			if(($val1[design_status]=="可研评审报告审批通过"))
			{
				if($val1["step3"]=="0.5")
				{
					$voList[$key][status]="综合计划复批";
				}
				if($val1["step3"]=="1")
				{
					$voList[$key][status]="提交合作协议";
					if($val1["step6"]=="0.1")
					{
						$voList[$key][status]="提交项目设计";
					}
					if($val1["step6"]=="0.2")
					{
						$voList[$key][status]="项目节点设置";
					}
					if($val1["step6"]=="0.3")
					{
						$voList[$key][status]="项目计划设置";
					}
					if($val1["step6"]=="0.4")
					{
						$voList[$key][status]="人员组织设置";
					}
					if($val1["step6"]=="1")//这里会被下面的占掉
					{
						if(($val1[sendtask_time]!=""))
						{
							$voList[$key][status]="派发工作任务单";
						}
						if(($val1[sendtask_time_confirm1]!="")&&($val1[sendtask_time_confirm2]!="")&&($val1[sendtask_time_confirm3]!=""))//sendtask_time_confirm1 //sendtask_time_confirm2 //sendtask_time_confirm3都不为空，自动变为施工中
						{
							$voList[$key][status]="接收工作任务单";
						}
					}
				}
				/*
				if(($val1[design_status]=="施工计划待审核")||($val1[design_status]=="施工计划审核通过")||($val1[design_status]=="施工计划审核退回"))
				{
					$voList[$key][status]="施工计划";
				}
				*/
			}
			if(($val1[design_status]=="待施工"))
			{
				$voList[$key][status]="待施工";
				if($val1["step6"]=="1")
				{
					if(($val1[sendtask_time]!=""))
					{
						$voList[$key][status]="待施工-派发工作任务单";
					}
					if(($val1[sendtask_time_confirm1]!="")&&($val1[sendtask_time_confirm2]!="")&&($val1[sendtask_time_confirm3]!=""))//sendtask_time_confirm1 //sendtask_time_confirm2 //sendtask_time_confirm3都不为空，自动变为施工中
					{
						$voList[$key][status]="待施工-接收工作任务单";
					}
				}
			}
			if($val1[design_status]=="施工中")
			{
				$voList[$key][status]="施工中";
				
				$mapforPlmschedule[percent]=array("neq","100%");
				$mapforPlmschedule[plmid] = $val1[id];
				$mapforPlmschedule[status]=1;
				$schedule=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
				if(!empty($schedule))
				{
					$voList[$key][status]="施工中-".$schedule["subworktype"];
				}
				else
				{
					$voList[$key][status]="施工完成";
				}
			}
			if(($val1[design_status]=="完成施工")||($val1[design_status]=="已完成")||($val1[design_status]=="完成验收")||($val1[design_status]=="联合验收中")||($val1[design_status]=="联合验收通过")||($val1[design_status]=="竣工待验收")||($val1[design_status]=="完成验收")||($val1[design_status]=="项目待验收")||($val1[design_status]=="验收审核退回"))
			{
				if($val1[activity]!="投入使用")
				{
					$voList[$key][status]="验收中";
				}
				else
				{
					$voList[$key][status]="项目完成";//$val1[design_status]
				}
			}
			if($val1[activity]=="投入使用")
			{
				$voList[$key][status]="项目完成";//投入使用
			}
			if($val1[design_status]=="暂停中")
			{
				$voList[$key][status]="暂停中";
			}
			if($val1[design_status]=="已取消")
			{
				$voList[$key][status]="已取消";
			}
			if($val1[design_status]=="暂存")
			{
				$voList[$key][status]="暂存";
			}
			if(empty($voList[$key][status]))
			{
				$voList[$key][status]=$val1[design_status];
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function getstatuscount(&$cities)
	{
		foreach($cities as $key => $val)
		{
			
			$cities[$key][count1]=0;
			$cities[$key][count2]=0;
			$cities[$key][count3]=0;
			$cities[$key][count4]=0;
			$cities[$key][count5]=0;
			
			$cities[$key][count_1]=0;//立项中
			$cities[$key][count_2]=0;//待施工
			$cities[$key][count_3]=0;//施工中
			$cities[$key][count_4]=0;//施工完成
			$cities[$key][count_5]=0;//验收完成
			
			foreach($cities[$key][projects] as $key1 => $val1)
			{
				
			
				if(($val1[design_status]=="立项中"))
				{
					$cities[$key][count_1]++;
					$cities[$key][countall]++;
				}
				if(($val1[design_status]=="待施工"))
				{
					$cities[$key][count_2]++;
					$cities[$key][countall]++;
				}
				if(($val1[design_status]=="施工中"))
				{
					$cities[$key][count_3]++;
					$cities[$key][countall]++;
				}
				if(($val1[design_status]=="施工完成")||($val1[design_status]=="完成施工"))
				{
					$cities[$key][count_4]++;
					$cities[$key][countall]++;
				}
				if(($val1[design_status]=="完成验收")||($val1[design_status]=="验收完成"))
				{
					$cities[$key][count_5]++;
					$cities[$key][countall]++;
				}
				if($val1[design_status]=="暂停中")
				{
					$cities[$key][count_6]++;
					$cities[$key][countall]++;
				}
				if($val1[design_status]=="暂存")
				{
					$cities[$key][count_7]++;
					$cities[$key][countall]++;
				}
				
				
				$cities[$key][count1]=$cities[$key][count_1];
				$cities[$key][count2]=$cities[$key][count_2];
				$cities[$key][count3]=$cities[$key][count_3];
				$cities[$key][count4]=$cities[$key][count_4];
				$cities[$key][count5]=$cities[$key][count_5];
				$cities[$key][count6]=$cities[$key][count_6];
				$cities[$key][count7]=$cities[$key][count_7];
				
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getstatusmap($tab,&$map)
	{
		if($tab=="_0")
		{
			$map[design_status]=array("not in","取消,暂存");//暂停,
		}
		else if(($tab=="初申中")||($tab=="_1"))
		{
			$map[design_status]=array("in","初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过");
			$map[step3]=array("neq","1");
			$map[planfile_time]=array("exp","is null");
		}
		else if(($tab=="已批复")||($tab=="_11"))
		{
			$map[design_status]=array("not in","取消,暂存");
			//$map[design_status]=array("in","初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过");
			//$map[step3]=array("neq","1");
			$map[planfile_time]=array("exp","is not null");
		}
		else if(($tab=="待施工")||($tab=="_2"))
		{
			$map[design_status]=array("in","可研评审报告审批通过,施工计划待审批,施工计划审批中,施工计划审批通过,施工计划审批退回,施工计划变更待审批,施工计划变更审批中,施工计划变更审批通过,施工计划变更审批退回,待施工");
			$map[step3]=array("eq","1");
		}
		else if(($tab=="施工中")||($tab=="_3"))
		{
			$map[design_status]=array("in","施工中");
			$map[activity]=array("exp","is null");
			$map[outplanfinish_time]=array("exp","is null");
		}
		else if(($tab=="待送电")||($tab=="_12"))
		{
			$map[design_status]=array("in","施工中");
			$map[outplanfinish_time]=array("exp","is null");
			$map[stepelec]=array("exp","is not null");
			
		}
		else if(($tab=="待验收")||($tab=="_13"))
		{
			$map[design_status]=array("in","已完成,完成施工,完成验收,联合验收中,联合验收通过,竣工待验收,项目待验收,验收审核退回");
			$map[step3]=array("eq","1");
			$map[activity]=array("exp","is null");
		}
		else if(($tab=="已投运")||($tab=="投入使用")||($tab=="_4"))
		{
			$map[activity]=array("eq","投入使用");
		}
		else if(($tab=="暂停中"))
		{
			$map['design_status'] = array("in","暂停中");
		}
		else if(($tab=="暂停"))
		{
			$map['design_status'] = array("in","暂停中");
		}
		else if(($tab=="取消"))
		{
			$map['design_status'] = array("in","取消");
		}
		
		else if(($tab=="滞后"))
		{
			$plmwarningids="";
			$mapforprojectfortopforwarning[warning]=array("eq","1");
			$mapforprojectfortopforwarning[status]=array("eq","1");
			$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$mapforprojectfortopforwarningapprove[status]=array("eq","1");
			$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$map['id'] = array('in',$plmwarningids);
		}
		
		else if(($tab=="延期"))
		{
			$plmwarningids="";
			$mapforprojectfortopforwarning[warning]=array("eq","1");
			$mapforprojectfortopforwarning[status]=array("eq","0");
			$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$mapforprojectfortopforwarningapprove[status]=array("eq","0");
			$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$map['id'] = array('in',$plmwarningids);
		}
		else
		{
			$map['design_status'] = array("in",$tab);
		}

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>