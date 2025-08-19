<?php
class SecondbankAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		
		$type=$_REQUEST["type"];
		if($type=="成交月度统计")
		{
			$this->index1();
			return;
		}
		
		if($_REQUEST['year'])
		{
			$year=$_REQUEST['year'];
			$this->assign("year",$_REQUEST['year']);
		}
		else
		{
			$year=date("Y");
		}
		
		$type=$_REQUEST["type"];
		if(empty($type))
		{
			$type="成交把握度";
		}
		$this->assign("type",$type);
		
		$mapforProject1['projecttype'] = array('eq',"工程项目");
		//$mapforProject2['projecttype'] = array('eq',"工程项目");
		//$mapforProject3['projecttype'] = array('eq',"工程项目");
		$mapforProject4['projecttype'] = array('eq',"工程项目");
		$mapforProject['projecttype'] = array('eq',"工程项目");
		
		if($_SESSION[account]!="admin")
		{
			$mapforProject1['_complex'] = $this->find5level($_SESSION[position],$mapforProject1);
			$mapforProject2['_complex'] = $this->find5level($_SESSION[position],$mapforProject2);
			$mapforProject3['_complex'] = $this->find5level($_SESSION[position],$mapforProject3);
			$mapforProject4['_complex'] = $this->find5level($_SESSION[position],$mapforProject4);
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		}
		
		
		
		$mapforProject1["estimate_signtime"]=array("like","$year%");
		$mapforProject1["status"]="成交";
		$data[0][1][0]=M("Project")->where($mapforProject1)->sum("quantities1");
		$data[0][1][1]=M("Project")->where($mapforProject1)->sum("estimate_total1");
		$data[0][1][2]=M("Project")->where($mapforProject1)->sum("quantities2");
		$data[0][1][3]=M("Project")->where($mapforProject1)->sum("estimate_total2");
		$data[0][1][4]=M("Project")->where($mapforProject1)->sum("estimate_total3");
		$data[0][1][5]=M("Project")->where($mapforProject1)->sum("estimate_total4");
		$data[0][1][6]=M("Project")->where($mapforProject1)->sum("estimate_total5");
		$data[0][1][7]=M("Project")->where($mapforProject1)->sum("estimate_total6");
		$data[0][1][8]=M("Project")->where($mapforProject1)->sum("other_estimate_total");
		$data[0][1][9]=$data[0][1][0]+$data[0][1][2];//+M("Project")->where($mapforProject1)->sum("quantities3")+M("Project")->where($mapforProject1)->sum("quantities4")+M("Project")->where($mapforProject1)->sum("quantities5")+M("Project")->where($mapforProject1)->sum("quantities6")
		$data[0][1][10]=M("Project")->where($mapforProject1)->sum("estimate_total");
		
		
		$mapforProject2["time"]=array("like","$year%");
		$data[0][2][0]=M("Plmcontract")->where($mapforProject2)->sum("quantities1");
		$data[0][2][1]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total1");
		$data[0][2][2]=M("Plmcontract")->where($mapforProject2)->sum("quantities2");
		$data[0][2][3]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total2");
		$data[0][2][4]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total3");
		$data[0][2][5]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total4");
		$data[0][2][6]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total5");
		$data[0][2][7]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total6");
		$data[0][2][8]=M("Plmcontract")->where($mapforProject2)->sum("para14");
		$data[0][2][9]=$data[0][2][0]+$data[0][2][2];
		//+M("Project")->where($mapforProject2)->sum("quantities3")+M("Project")->where($mapforProject2)->sum("quantities4")+M("Project")->where($mapforProject2)->sum("quantities5")+M("Project")->where($mapforProject2)->sum("quantities6")
		$data[0][2][10]=M("Plmcontract")->where($mapforProject2)->sum("para15");
		
	
		$mapforProject3["date"]=array("like","$year%");
		$mapforProject3["pworktype"]=array("like","%整形%");
		$data[0][3][0]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][1]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%复拌%");
		$data[0][3][2]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][3]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%地聚物注浆%");
		$data[0][3][4]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%高聚物注浆%");
		$data[0][3][5]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%大空隙灌浆%");
		$data[0][3][6]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%快速回填%");
		$data[0][3][7]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$data[0][3][8]="";
		$data[0][3][9]=$data[0][3][0]+$data[0][3][2];
		$data[0][3][10]=$data[0][3][1]+$data[0][3][3]+$data[0][3][4]+$data[0][3][5]+$data[0][3][6]+$data[0][3][7];
		
	
		//1月
		$mapforProject["status"]=array("eq","进行中");
		for($i=1;$i<=12;$i++)
		{
			if($i<10)$ii="0".$i;else $ii=$i;
			$mapforProject["estimate_signtime"]=array("like","$year-".$ii."%");
			if($type=="成交把握度")
			{
				$mapforProject["dealpercent"]=array("eq","80%以上");
			}
			else
			{
				$mapforProject["deallevel"]=array("eq","高");
			}
			$data[$i][1][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][1][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][1][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][1][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][1][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][1][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][1][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][1][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][1][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][1][9]=$data[$i][1][0]+$data[$i][1][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][1][10]=M("Project")->where($mapforProject)->sum("estimate_total");
			if($type=="成交把握度")
			{
				$mapforProject["dealpercent"]=array("eq","50%");
			}
			else
			{
				$mapforProject["deallevel"]=array("eq","中");
			}
			$data[$i][2][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][2][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][2][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][2][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][2][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][2][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][2][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][2][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][2][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][2][9]=$data[$i][2][0]+$data[$i][2][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][2][10]=M("Project")->where($mapforProject)->sum("estimate_total");
			if($type=="成交把握度")
			{
				$mapforProject["dealpercent"]=array("eq","30%");
			}
			else
			{
				$mapforProject["deallevel"]=array("eq","低");
			}
			$data[$i][3][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][3][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][3][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][3][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][3][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][3][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][3][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][3][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][3][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][3][9]=$data[$i][3][0]+$data[$i][3][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][3][10]=M("Project")->where($mapforProject)->sum("estimate_total");
			
			$mapforProject["dealpercent"]=array("like","%%");
			$mapforProject["deallevel"]=array("like","%%");
			$data[$i][4][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][4][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][4][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][4][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][4][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][4][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][4][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][4][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][4][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][4][9]=$data[$i][4][0]+$data[$i][4][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][4][10]=M("Project")->where($mapforProject)->sum("estimate_total");
		}
		
		
		
		$mapforProject4["estimate_signtime"]=array("like","$year%");
		$mapforProject4["status"]=array("eq","进行中");
		if($type=="成交把握度")
		{
			$mapforProject4["dealpercent"]=array("eq","80%以上");
		}
		else
		{
			$mapforProject4["deallevel"]=array("eq","高");
		}
		$data[13][1][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][1][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][1][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][1][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][1][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][1][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][1][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][1][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][1][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][1][9]=$data[13][1][0]+$data[13][1][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][1][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		
		if($type=="成交把握度")
		{
			$mapforProject4["dealpercent"]=array("eq","50%");
		}
		else
		{
			$mapforProject4["deallevel"]=array("eq","中");
		}
		$data[13][2][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][2][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][2][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][2][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][2][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][2][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][2][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][2][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][2][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][2][9]=$data[13][2][0]+$data[13][2][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][2][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		if($type=="成交把握度")
		{
			$mapforProject4["dealpercent"]=array("eq","30%");
		}
		else
		{
			$mapforProject4["deallevel"]=array("eq","低");
		}
		$data[13][3][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][3][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][3][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][3][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][3][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][3][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][3][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][3][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][3][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][3][9]=$data[13][3][0]+$data[13][3][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][3][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		
		$mapforProject4["dealpercent"]=array("like","%%");
		$mapforProject4["deallevel"]=array("like","%%");
		
		
		$data[13][4][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][4][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][4][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][4][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][4][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][4][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][4][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][4][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][4][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][4][9]=$data[13][4][0]+$data[13][4][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][4][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		
		
		for($i=0;$i<=13;$i++)
		{
			for($j=1;$j<=4;$j++)
			{
				for($k=0;$k<=10;$k++)
				{
					if(($k==0)||($k==2)||($k==9))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k]/10000,2);//万m2  m2
					}
					if(($k==1)||($k==3)||($k==4)||($k==5)||($k==6)||($k==7)||($k==8)||($k==10))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k],4);
					}
					if(empty($data[$i][$j][$k]))$data[$i][$j][$k]=0;
					//$data[$i][$j][$k]=round($data[$i][$j][$k],0);
				}
				
			}
		}
	
		$this->assign("data",$data);
		
		$this->display();
		return;
	}
	
	
	public function index1() {
		$type=$_REQUEST["type"];
		$this->assign("type",$type);
		if($_REQUEST['year'])
		{
			$year=$_REQUEST['year'];
			$this->assign("year",$_REQUEST['year']);
		}
		else
		{
			$year=date("Y");
		}
		
		
		
		
		
		
		
		$mapforProject1["estimate_signtime"]=array("like","$year%");
		$mapforProject1["status"]="成交";
		$data[0][1][0]=M("Project")->where($mapforProject1)->sum("quantities1");
		$data[0][1][1]=M("Project")->where($mapforProject1)->sum("estimate_total1");
		$data[0][1][2]=M("Project")->where($mapforProject1)->sum("quantities2");
		$data[0][1][3]=M("Project")->where($mapforProject1)->sum("estimate_total2");
		$data[0][1][4]=M("Project")->where($mapforProject1)->sum("estimate_total3");
		$data[0][1][5]=M("Project")->where($mapforProject1)->sum("estimate_total4");
		$data[0][1][6]=M("Project")->where($mapforProject1)->sum("estimate_total5");
		$data[0][1][7]=M("Project")->where($mapforProject1)->sum("estimate_total6");
		$data[0][1][8]=M("Project")->where($mapforProject1)->sum("other_estimate_total");
		$data[0][1][9]=$data[0][1][0]+$data[0][1][2];//+M("Project")->where($mapforProject1)->sum("quantities3")+M("Project")->where($mapforProject1)->sum("quantities4")+M("Project")->where($mapforProject1)->sum("quantities5")+M("Project")->where($mapforProject1)->sum("quantities6")
		$data[0][1][10]=M("Project")->where($mapforProject1)->sum("estimate_total");
		
		
		$mapforProject2["time"]=array("like","$year%");
		$data[0][2][0]=M("Plmcontract")->where($mapforProject2)->sum("quantities1");
		$data[0][2][1]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total1");
		$data[0][2][2]=M("Plmcontract")->where($mapforProject2)->sum("quantities2");
		$data[0][2][3]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total2");
		$data[0][2][4]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total3");
		$data[0][2][5]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total4");
		$data[0][2][6]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total5");
		$data[0][2][7]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total6");
		$data[0][2][8]=M("Plmcontract")->where($mapforProject2)->sum("para14");
		$data[0][2][9]=$data[0][2][0]+$data[0][2][2];
		//+M("Project")->where($mapforProject2)->sum("quantities3")+M("Project")->where($mapforProject2)->sum("quantities4")+M("Project")->where($mapforProject2)->sum("quantities5")+M("Project")->where($mapforProject2)->sum("quantities6")
		$data[0][2][10]=M("Plmcontract")->where($mapforProject2)->sum("para15");
		
	
		$mapforProject3["date"]=array("like","$year%");
		$mapforProject3["pworktype"]=array("like","%整形%");
		$data[0][3][0]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][1]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%复拌%");
		$data[0][3][2]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][3]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%地聚物注浆%");
		$data[0][3][4]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%高聚物注浆%");
		$data[0][3][5]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%大空隙灌浆%");
		$data[0][3][6]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%快速回填%");
		$data[0][3][7]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$data[0][3][8]="";
		$data[0][3][9]=$data[0][3][0]+$data[0][3][2];
		$data[0][3][10]=$data[0][3][1]+$data[0][3][3]+$data[0][3][4]+$data[0][3][5]+$data[0][3][6]+$data[0][3][7];
		
	
		//1月
		for($i=1;$i<=12;$i++)
		{
			if($i<10)$ii="0".$i;else $ii=$i;
			$mapforProject1["estimate_signtime"]=array("like","$year-".$ii."%");
			$mapforProject1["status"]="成交";
			$data[$i][1][0]=M("Project")->where($mapforProject1)->sum("quantities1");
			$data[$i][1][1]=M("Project")->where($mapforProject1)->sum("estimate_total1");
			$data[$i][1][2]=M("Project")->where($mapforProject1)->sum("quantities2");
			$data[$i][1][3]=M("Project")->where($mapforProject1)->sum("estimate_total2");
			$data[$i][1][4]=M("Project")->where($mapforProject1)->sum("estimate_total3");
			$data[$i][1][5]=M("Project")->where($mapforProject1)->sum("estimate_total4");
			$data[$i][1][6]=M("Project")->where($mapforProject1)->sum("estimate_total5");
			$data[$i][1][7]=M("Project")->where($mapforProject1)->sum("estimate_total6");
			$data[$i][1][8]=M("Project")->where($mapforProject1)->sum("other_estimate_total");
			$data[$i][1][9]=$data[$i][1][0]+$data[$i][1][2];//+M("Project")->where($mapforProject1)->sum("quantities3")+M("Project")->where($mapforProject1)->sum("quantities4")+M("Project")->where($mapforProject1)->sum("quantities5")+M("Project")->where($mapforProject1)->sum("quantities6")
			$data[$i][1][10]=M("Project")->where($mapforProject1)->sum("estimate_total");
			
			
			$mapforProject2["time"]=array("like","$year-".$ii."%");
			$data[$i][2][0]=M("Plmcontract")->where($mapforProject2)->sum("quantities1");
			$data[$i][2][1]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total1");
			$data[$i][2][2]=M("Plmcontract")->where($mapforProject2)->sum("quantities2");
			$data[$i][2][3]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total2");
			$data[$i][2][4]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total3");
			$data[$i][2][5]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total4");
			$data[$i][2][6]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total5");
			$data[$i][2][7]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total6");
			$data[$i][2][8]=M("Plmcontract")->where($mapforProject2)->sum("para14");
			$data[$i][2][9]=$data[$i][2][0]+$data[$i][2][2];
			//+M("Project")->where($mapforProject2)->sum("quantities3")+M("Project")->where($mapforProject2)->sum("quantities4")+M("Project")->where($mapforProject2)->sum("quantities5")+M("Project")->where($mapforProject2)->sum("quantities6")
			$data[$i][2][10]=M("Plmcontract")->where($mapforProject2)->sum("para15");
			
		
			$mapforProject3["date"]=array("like","$year-".$ii."%");
			$mapforProject3["pworktype"]=array("like","%整形%");
			$data[$i][3][0]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
			$data[$i][3][1]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%复拌%");
			$data[$i][3][2]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
			$data[$i][3][3]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%地聚物注浆%");
			$data[$i][3][4]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%高聚物注浆%");
			$data[$i][3][5]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%大空隙灌浆%");
			$data[$i][3][6]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%快速回填%");
			$data[$i][3][7]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$data[$i][3][8]="";
			$data[$i][3][9]=$data[$i][3][0]+$data[$i][3][2];
			$data[$i][3][10]=$data[$i][3][1]+$data[$i][3][3]+$data[$i][3][4]+$data[$i][3][5]+$data[$i][3][6]+$data[$i][3][7];
		}
		
		
		for($i=0;$i<=13;$i++)
		{
			for($j=1;$j<=4;$j++)
			{
				for($k=0;$k<=10;$k++)
				{
					if(($k==0)||($k==2)||($k==9))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k]/10000,2);//万m2  m2
					}
					if(($k==1)||($k==3)||($k==4)||($k==5)||($k==6)||($k==7)||($k==8)||($k==10))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k],4);
					}
					if(empty($data[$i][$j][$k]))$data[$i][$j][$k]=0;
					//$data[$i][$j][$k]=round($data[$i][$j][$k],0);
				}
				
			}
		}
	
		$this->assign("data",$data);
		
		$this->display("index1");
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
	
	function ajax1()
	{
		$titlerepeat["title"]=array("eq",$_REQUEST[title]);
		$ifrepeat=M("Project")->where($titlerepeat)->find();
		if(!empty($ifrepeat))
		{
			echo "0";
		}
		else
		{
			echo "1";
		}
	}
	
	function insert() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		
		if(false!=strpos($_REQUEST[address], "/"))
		{
			$this->error("项目地址不能含有特殊字符！");
		}
		if(false!=strpos($_REQUEST[address], " "))
		{
			$this->error("项目地址不能含有空格！");
		}
		if(false!=strpos($_REQUEST[address], "\\"))
		{
			$this->error("项目地址不能含有特殊字符！");
		}
		if(empty($_REQUEST[id]))
		{
			$titlerepeat["title"]=array("eq",$_REQUEST[title]);
			$ifrepeat=M("Project")->where($titlerepeat)->find();
			if(!empty($ifrepeat))
			{
				$this->error("项目名称已经存在！");	
			}
		}
			
		
		$model->user=$_SESSION['loginUserName'];
		//$model->charge=$_SESSION['loginUserName'];
		$model->create_time=time();
		$model->last_time=time();
		
		$model->addressfull=$_REQUEST['province'].$_REQUEST['city'].$_REQUEST['area'].$_REQUEST['address'];
		
		if(empty($_REQUEST[city]))
		{
			//$model->city=$_REQUEST[province];
		}
		
		$date=date('m-d H:i');
		$address=$model->title;
		
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
			$model->picture=$newnameall;
			$model->picturefilename=$filenameall;
		}
		if(!empty($_FILES['file2']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file2']['name'];
			$file_tmp=$_FILES['file2']['tmp_name'];
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
			$model->clientpicture=$newnameall;
			$model->clientpicturefilename=$filenameall;
		}
		//保存当前数据对象
		if(empty($_REQUEST[id]))
		{
			$model->handlehistory=$_SESSION['loginUserName']."于".$date."创建了项目立项</br>------------------</br>"; 
			$list = $model->add();
			
			$data['content']=$_SESSION['loginUserName']."于".$date."创建了《".$address."》项目立项，请您审核。";
			$data['href'] ="index.php?s=Jypg/index";
			$data['taskid'] =$list;
			$data['type'] ="Jypg";
			//$userschedule=$this->findUserByRole("营销部经理");
			$userschedule=$this->findUserByAccount("zhourong");
			$data['user']=$userschedule['nickname'].$userschedule['number'];
	    	$this->Addschedule($data);
		}
		else
		{
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			$address=$info[title];
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目立项</br>------------------</br>"; 
			$list = $model->save();
			
			/*********增加待办事项***********/
			$schedulemap[taskid]=$info[id];
			$schedulemap[type]="Jypg";		
			$scheduledata['content']=$_SESSION['loginUserName']."于".$date."修改《".$address."》项目立项，重新提交，请您审核。";
			//$userschedule=$this->findUserByRole("营销部经理");
			//$scheduledata['user']=$userschedule['nickname'].$userschedule['number'];
			$scheduledata['create_time']=time();	
			$scheduledata['status']=1;	
			M("Schedule")->where($schedulemap)->save($scheduledata);
		}
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功!');
			//$this->redirect('index');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function update() {

		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$taskid=$model->id;
		
		// 更新数据
		$model->secondcreate_time=time();
		$model->last_time=time();
		$date=date('Y-m-d H:i:s');
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目立项</br>------------------</br>"; 
		if($_REQUEST[waysub]!="")
		{
			$model->waysub=$_REQUEST[waysub];
		}
		if($_REQUEST[activity]!=""){
			$model->activity=$_REQUEST["activity"];
		}

		$address=$model->title;
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			
			$date=date('m-d H:i');
			$data['content']=$_SESSION['loginUserName']."于".$date."修改了《".$address."》项目立项，请您审核。";
			$data['href'] ="index.php?s=Jypg/index";
			$data['taskid'] =$taskid;
			$data['type'] ="Jypg";
			//$userschedule=$this->findUserByRole("营销部经理");
			//英达热再生再生这里不会走到
			$userschedule=$this->findUserByAccount("zhourong");
			$data['user']=$userschedule['nickname'].$userschedule['number'];
	    	$this->Addschedule($data);
			
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('项目立项成功!');
		} else {
			//错误提示
			$this->error('项目立项失败!');
		}
	}
	
	function change() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('list', $vo);
		
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		$this->display();
	}
	
	public function changestatus()
	{
		$id=$_REQUEST ['ids'];
		$model=M("Project");
	
		$info = $model->where("id='" . $id . "'")->find();
		if(empty($info))
		{
			$this->error('选项出错!');
		}
		$date=date('Y-m-d H:i:s');
		//$info['clientstatus']=$_REQUEST["clientstatus"];
		if($_REQUEST["clientstatus"]!=$info["clientstatus"])
		{
			$info['clientstatus']=$_REQUEST["clientstatus"];
			$info['handlehistory'].=$_SESSION['loginUserName']."于".$date."修改客户状态为：".$info['clientstatus'].'，备注：'.$_REQUEST['remark']."</br>------------------</br>";   //经办人记录
			
			
			if($info['clientstatus']=="死单客户")
			{
				$mapforcharge[nickname]=array("eq",$info[charge]);
				$chargeposition=M("User")->where($mapforcharge)->getField("position");
				$chargedepartment=M("User")->where($mapforcharge)->getField("department");
				$mapforparentrole[id]=$chargeposition;
				$parentrole=M("Role")->where($mapforparentrole)->select();
				$pline="";
				foreach($parentrole as $pkey=>$pval)
				{
					$pline.=$pval[pid].",";
				}
				$mapuser['position']=array("in",$pline);
				$mapuser['department']=$chargedepartment;
				
				$user=M("User")->where($mapuser)->find();
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."在《".$info['title']."》修改客户状态为死单客户。";
				$data['receiver']=$user['nickname'].$user['number'].",";
				$data['sender']="系统通知";
				$data['title']  =$_SESSION['loginUserName']."于".$date."在《".$info['title']."》修改客户状态为死单客户。";
				$this->Sendmail($data);
			}
			
			
		}
		else
		{
			//$info['approvestatus']=$_REQUEST["approvestatus"];
			$info['handlehistory'].=$_SESSION['loginUserName']."于".$date."添加备注：".$_REQUEST['remark']."</br>------------------</br>";   //经办人记录
		}
		$info[last_time]=time();
		$model->where("id='" . $id . "'")->save($info);
	
		if($_REQUEST["approvestatus"])
			$this->success('备案状态修改为'.$info['approvestatus']."!");
		else
			$this->success('添加备注成功'."!");	
	}
	
	function draftfirst() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
	
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->display();
	}
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Project";
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
                    //echo $model->getlastsql();
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
	
	function draft() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$pos=strpos($vo["way"],"-");		
		if($pos)
		{	
		
			$vo["waysub"]=substr($vo["way"],$pos+1);
			$vo["way"]=substr($vo["way"],0,$pos);
		}
			$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->assign('huodong',$huodong);
		$this->assign('vo', $vo);
		$this->findRelativePersons();
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		
		$this->display();
	}
	/*
	function find5level($roleid)
	{
		//$roleids=$roleid.",";
		$map['pid']=$roleid;
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}	
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$mapusers[position] = array("in",$roleids);
		$users=M("User")->where($mapusers)->field("nickname")->select();
		foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].",";
		}
		$subordinates.=$_SESSION[name];
		$where["charge"]=array("in",$subordinates);
		$where["user"]=array("in",$subordinates);
		$where['_logic'] = 'or';
		return $where;
	}	
	*/
	function detail() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
	
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		$this->assign('huodong',$huodong);
		if(!(($_SESSION[account]=="zhourong")||($_SESSION[account]=="chenxiaohua")||($_SESSION[account]=="taojianhua")||($_SESSION[account]=="chongfazhan")||($_SESSION[account]=="admin")))
		{
			if($vo[design_status]=="完成验收")
			{
				echo "</br>您无权查看此项目</br></br>";
				return;
			}
		}
		$this->display();
	}
	
	public function toexcel()
	{
		$type=$_REQUEST["type"];
		if($type=="成交月度统计")
		{
			$this->toexcel1();
			return;
		}
		
		if($_REQUEST['year'])
		{
			$year=filter_var(htmlspecialchars($_REQUEST['year']), FILTER_CALLBACK, array("options"=>"convertSpace"));
			$this->assign("year",$_REQUEST['year']);
		}
		else
		{
			$year=date("Y");
		}
		$type=$_REQUEST["type"];
		if(empty($type))
		{
			$type="成交把握度";
		}
		$this->assign("type",$type);
		
		
		$mapforProject1['projecttype'] = array('eq',"工程项目");
		//$mapforProject2['projecttype'] = array('eq',"工程项目");
		//$mapforProject3['projecttype'] = array('eq',"工程项目");
		$mapforProject4['projecttype'] = array('eq',"工程项目");
		$mapforProject['projecttype'] = array('eq',"工程项目");
		
		if($_SESSION[account]!="admin")
		{
			$mapforProject1['_complex'] = $this->find5level($_SESSION[position],$mapforProject1);
			$mapforProject2['_complex'] = $this->find5level($_SESSION[position],$mapforProject2);
			$mapforProject3['_complex'] = $this->find5level($_SESSION[position],$mapforProject3);
			$mapforProject4['_complex'] = $this->find5level($_SESSION[position],$mapforProject4);
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		}
		
		
		$mapforProject1["estimate_signtime"]=array("like","$year%");
		$mapforProject1["status"]="成交";
		$data[0][1][0]=M("Project")->where($mapforProject1)->sum("quantities1");
		$data[0][1][1]=M("Project")->where($mapforProject1)->sum("estimate_total1");
		$data[0][1][2]=M("Project")->where($mapforProject1)->sum("quantities2");
		$data[0][1][3]=M("Project")->where($mapforProject1)->sum("estimate_total2");
		$data[0][1][4]=M("Project")->where($mapforProject1)->sum("estimate_total3");
		$data[0][1][5]=M("Project")->where($mapforProject1)->sum("estimate_total4");
		$data[0][1][6]=M("Project")->where($mapforProject1)->sum("estimate_total5");
		$data[0][1][7]=M("Project")->where($mapforProject1)->sum("estimate_total6");
		$data[0][1][8]=M("Project")->where($mapforProject1)->sum("other_estimate_total");
		$data[0][1][9]=$data[0][1][0]+$data[0][1][2];//+M("Project")->where($mapforProject1)->sum("quantities3")+M("Project")->where($mapforProject1)->sum("quantities4")+M("Project")->where($mapforProject1)->sum("quantities5")+M("Project")->where($mapforProject1)->sum("quantities6")
		$data[0][1][10]=M("Project")->where($mapforProject1)->sum("estimate_total");
		
		
		$mapforProject2["time"]=array("like","$year%");
		$data[0][2][0]=M("Plmcontract")->where($mapforProject2)->sum("quantities1");
		$data[0][2][1]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total1");
		$data[0][2][2]=M("Plmcontract")->where($mapforProject2)->sum("quantities2");
		$data[0][2][3]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total2");
		$data[0][2][4]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total3");
		$data[0][2][5]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total4");
		$data[0][2][6]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total5");
		$data[0][2][7]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total6");
		$data[0][2][8]=M("Plmcontract")->where($mapforProject2)->sum("para14");
		$data[0][2][9]=$data[0][2][0]+$data[0][2][2];
		//+M("Project")->where($mapforProject2)->sum("quantities3")+M("Project")->where($mapforProject2)->sum("quantities4")+M("Project")->where($mapforProject2)->sum("quantities5")+M("Project")->where($mapforProject2)->sum("quantities6")
		$data[0][2][10]=M("Plmcontract")->where($mapforProject2)->sum("para15");
		
	
		$mapforProject3["date"]=array("like","$year%");
		$mapforProject3["pworktype"]=array("like","%整形%");
		$data[0][3][0]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][1]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%复拌%");
		$data[0][3][2]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][3]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%地聚物注浆%");
		$data[0][3][4]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%高聚物注浆%");
		$data[0][3][5]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%大空隙灌浆%");
		$data[0][3][6]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%快速回填%");
		$data[0][3][7]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$data[0][3][8]="";
		$data[0][3][9]=$data[0][3][0]+$data[0][3][2];
		$data[0][3][10]=$data[0][3][1]+$data[0][3][3]+$data[0][3][4]+$data[0][3][5]+$data[0][3][6]+$data[0][3][7];
		
	
		//1月
		$mapforProject["status"]=array("eq","进行中");
		for($i=1;$i<=12;$i++)
		{
			if($i<10)$ii="0".$i;else $ii=$i;
			$mapforProject["estimate_signtime"]=array("like","$year-".$ii."%");
			if($type=="成交把握度")
			{
				$mapforProject["dealpercent"]=array("eq","80%以上");
			}
			else
			{
				$mapforProject["deallevel"]=array("eq","高");
			}
			$data[$i][1][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][1][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][1][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][1][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][1][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][1][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][1][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][1][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][1][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][1][9]=$data[$i][1][0]+$data[$i][1][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][1][10]=M("Project")->where($mapforProject)->sum("estimate_total");
			if($type=="成交把握度")
			{
				$mapforProject["dealpercent"]=array("eq","50%");
			}
			else
			{
				$mapforProject["deallevel"]=array("eq","中");
			}
			$data[$i][2][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][2][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][2][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][2][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][2][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][2][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][2][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][2][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][2][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][2][9]=$data[$i][2][0]+$data[$i][2][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][2][10]=M("Project")->where($mapforProject)->sum("estimate_total");
			if($type=="成交把握度")
			{
				$mapforProject["dealpercent"]=array("eq","30%");
			}
			else
			{
				$mapforProject["deallevel"]=array("eq","低");
			}
			$data[$i][3][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][3][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][3][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][3][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][3][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][3][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][3][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][3][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][3][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][3][9]=$data[$i][3][0]+$data[$i][3][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][3][10]=M("Project")->where($mapforProject)->sum("estimate_total");
			
			$mapforProject["dealpercent"]=array("like","%%");
			$mapforProject["deallevel"]=array("like","%%");
			$data[$i][4][0]=M("Project")->where($mapforProject)->sum("quantities1");
			$data[$i][4][1]=M("Project")->where($mapforProject)->sum("estimate_total1");
			$data[$i][4][2]=M("Project")->where($mapforProject)->sum("quantities2");
			$data[$i][4][3]=M("Project")->where($mapforProject)->sum("estimate_total2");
			$data[$i][4][4]=M("Project")->where($mapforProject)->sum("estimate_total3");
			$data[$i][4][5]=M("Project")->where($mapforProject)->sum("estimate_total4");
			$data[$i][4][6]=M("Project")->where($mapforProject)->sum("estimate_total5");
			$data[$i][4][7]=M("Project")->where($mapforProject)->sum("estimate_total6");
			$data[$i][4][8]=M("Project")->where($mapforProject)->sum("other_estimate_total");
			$data[$i][4][9]=$data[$i][4][0]+$data[$i][4][2]+M("Project")->where($mapforProject)->sum("quantities3")+M("Project")->where($mapforProject)->sum("quantities4")+M("Project")->where($mapforProject)->sum("quantities5")+M("Project")->where($mapforProject)->sum("quantities6");
			$data[$i][4][10]=M("Project")->where($mapforProject)->sum("estimate_total");
		}
		
		$mapforProject4["estimate_signtime"]=array("like","$year%");
		$mapforProject4["status"]=array("eq","进行中");
		if($type=="成交把握度")
		{
			$mapforProject4["dealpercent"]=array("eq","80%以上");
		}
		else
		{
			$mapforProject4["deallevel"]=array("eq","高");
		}
		$data[13][1][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][1][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][1][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][1][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][1][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][1][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][1][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][1][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][1][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][1][9]=$data[13][1][0]+$data[13][1][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][1][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		
		if($type=="成交把握度")
		{
			$mapforProject4["dealpercent"]=array("eq","50%");
		}
		else
		{
			$mapforProject4["deallevel"]=array("eq","中");
		}
		$data[13][2][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][2][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][2][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][2][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][2][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][2][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][2][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][2][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][2][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][2][9]=$data[13][2][0]+$data[13][2][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][2][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		if($type=="成交把握度")
		{
			$mapforProject4["dealpercent"]=array("eq","30%");
		}
		else
		{
			$mapforProject4["deallevel"]=array("eq","低");
		}
		$data[13][3][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][3][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][3][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][3][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][3][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][3][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][3][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][3][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][3][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][3][9]=$data[13][3][0]+$data[13][3][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][3][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		
		$mapforProject4["dealpercent"]=array("like","%%");
		$mapforProject4["deallevel"]=array("like","%%");
		$data[13][4][0]=M("Project")->where($mapforProject4)->sum("quantities1");
		$data[13][4][1]=M("Project")->where($mapforProject4)->sum("estimate_total1");
		$data[13][4][2]=M("Project")->where($mapforProject4)->sum("quantities2");
		$data[13][4][3]=M("Project")->where($mapforProject4)->sum("estimate_total2");
		$data[13][4][4]=M("Project")->where($mapforProject4)->sum("estimate_total3");
		$data[13][4][5]=M("Project")->where($mapforProject4)->sum("estimate_total4");
		$data[13][4][6]=M("Project")->where($mapforProject4)->sum("estimate_total5");
		$data[13][4][7]=M("Project")->where($mapforProject4)->sum("estimate_total6");
		$data[13][4][8]=M("Project")->where($mapforProject4)->sum("other_estimate_total");
		$data[13][4][9]=$data[13][4][0]+$data[13][4][2]+M("Project")->where($mapforProject4)->sum("quantities3")+M("Project")->where($mapforProject4)->sum("quantities4")+M("Project")->where($mapforProject4)->sum("quantities5")+M("Project")->where($mapforProject4)->sum("quantities6");
		$data[13][4][10]=M("Project")->where($mapforProject4)->sum("estimate_total");
		
	
		
		for($i=0;$i<=13;$i++)
		{
			for($j=1;$j<=4;$j++)
			{
				for($k=0;$k<=10;$k++)
				{
					if(($k==0)||($k==2)||($k==9))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k]/10000,2);//万m2  m2
					}
					if(($k==1)||($k==3)||($k==4)||($k==5)||($k==6)||($k==7)||($k==8)||($k==10))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k],4);
					}
					if(empty($data[$i][$j][$k]))$data[$i][$j][$k]=0;
					//$data[$i][$j][$k]=round($data[$i][$j][$k],0);
				}
			
			}
		}
		
		$x=0;
		for($i=0;$i<=13;$i++)
		{
			if($i==0)
				$temp=3;
			else
				$temp=4;
			for($j=1;$j<=$temp;$j++)
			{
				for($k=0;$k<=10;$k++)
				{
					$data1[$x][$k]=$data[$i][$j][$k];
				}
				$x++;
			}
		}
		
		
		$file="销售报表";
		$title="销售团队".$year."年各月预计成交情况汇总";
		$subtitle='';
		
		$th_array=array('','','','','','');
		$this->createExel($title,$th_array,$data1);
	}
	
	public function toexcel1()
	{
		$type=$_REQUEST["type"];
		$this->assign("type",$type);
		if($_REQUEST['year'])
		{
			$year=filter_var(htmlspecialchars($_REQUEST['year']), FILTER_CALLBACK, array("options"=>"convertSpace"));
			$this->assign("year",$_REQUEST['year']);
		}
		else
		{
			$year=date("Y");
		}
		
		
		
		
		
		
		
		$mapforProject1["estimate_signtime"]=array("like","$year%");
		$mapforProject1["status"]="成交";
		$data[0][1][0]=M("Project")->where($mapforProject1)->sum("quantities1");
		$data[0][1][1]=M("Project")->where($mapforProject1)->sum("estimate_total1");
		$data[0][1][2]=M("Project")->where($mapforProject1)->sum("quantities2");
		$data[0][1][3]=M("Project")->where($mapforProject1)->sum("estimate_total2");
		$data[0][1][4]=M("Project")->where($mapforProject1)->sum("estimate_total3");
		$data[0][1][5]=M("Project")->where($mapforProject1)->sum("estimate_total4");
		$data[0][1][6]=M("Project")->where($mapforProject1)->sum("estimate_total5");
		$data[0][1][7]=M("Project")->where($mapforProject1)->sum("estimate_total6");
		$data[0][1][8]=M("Project")->where($mapforProject1)->sum("other_estimate_total");
		$data[0][1][9]=$data[0][1][0]+$data[0][1][2];//+M("Project")->where($mapforProject1)->sum("quantities3")+M("Project")->where($mapforProject1)->sum("quantities4")+M("Project")->where($mapforProject1)->sum("quantities5")+M("Project")->where($mapforProject1)->sum("quantities6")
		$data[0][1][10]=M("Project")->where($mapforProject1)->sum("estimate_total");
		
		
		$mapforProject2["time"]=array("like","$year%");
		$data[0][2][0]=M("Plmcontract")->where($mapforProject2)->sum("quantities1");
		$data[0][2][1]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total1");
		$data[0][2][2]=M("Plmcontract")->where($mapforProject2)->sum("quantities2");
		$data[0][2][3]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total2");
		$data[0][2][4]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total3");
		$data[0][2][5]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total4");
		$data[0][2][6]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total5");
		$data[0][2][7]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total6");
		$data[0][2][8]=M("Plmcontract")->where($mapforProject2)->sum("para14");
		$data[0][2][9]=$data[0][2][0]+$data[0][2][2];
		//+M("Project")->where($mapforProject2)->sum("quantities3")+M("Project")->where($mapforProject2)->sum("quantities4")+M("Project")->where($mapforProject2)->sum("quantities5")+M("Project")->where($mapforProject2)->sum("quantities6")
		$data[0][2][10]=M("Plmcontract")->where($mapforProject2)->sum("para15");
		
	
		$mapforProject3["date"]=array("like","$year%");
		$mapforProject3["pworktype"]=array("like","%整形%");
		$data[0][3][0]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][1]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%复拌%");
		$data[0][3][2]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
		$data[0][3][3]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%地聚物注浆%");
		$data[0][3][4]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%高聚物注浆%");
		$data[0][3][5]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%大空隙灌浆%");
		$data[0][3][6]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$mapforProject3["pworktype"]=array("like","%快速回填%");
		$data[0][3][7]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
		$data[0][3][8]="";
		$data[0][3][9]=$data[0][3][0]+$data[0][3][2];
		$data[0][3][10]=$data[0][3][1]+$data[0][3][3]+$data[0][3][4]+$data[0][3][5]+$data[0][3][6]+$data[0][3][7];
		
	
		//1月
		for($i=1;$i<=12;$i++)
		{
			if($i<10)$ii="0".$i;else $ii=$i;
			$mapforProject1["estimate_signtime"]=array("like","$year-".$ii."%");
			$mapforProject1["status"]="成交";
			$data[$i][1][0]=M("Project")->where($mapforProject1)->sum("quantities1");
			$data[$i][1][1]=M("Project")->where($mapforProject1)->sum("estimate_total1");
			$data[$i][1][2]=M("Project")->where($mapforProject1)->sum("quantities2");
			$data[$i][1][3]=M("Project")->where($mapforProject1)->sum("estimate_total2");
			$data[$i][1][4]=M("Project")->where($mapforProject1)->sum("estimate_total3");
			$data[$i][1][5]=M("Project")->where($mapforProject1)->sum("estimate_total4");
			$data[$i][1][6]=M("Project")->where($mapforProject1)->sum("estimate_total5");
			$data[$i][1][7]=M("Project")->where($mapforProject1)->sum("estimate_total6");
			$data[$i][1][8]=M("Project")->where($mapforProject1)->sum("other_estimate_total");
			$data[$i][1][9]=$data[$i][1][0]+$data[$i][1][2];//+M("Project")->where($mapforProject1)->sum("quantities3")+M("Project")->where($mapforProject1)->sum("quantities4")+M("Project")->where($mapforProject1)->sum("quantities5")+M("Project")->where($mapforProject1)->sum("quantities6")
			$data[$i][1][10]=M("Project")->where($mapforProject1)->sum("estimate_total");
			
			
			$mapforProject2["time"]=array("like","$year-".$ii."%");
			$data[$i][2][0]=M("Plmcontract")->where($mapforProject2)->sum("quantities1");
			$data[$i][2][1]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total1");
			$data[$i][2][2]=M("Plmcontract")->where($mapforProject2)->sum("quantities2");
			$data[$i][2][3]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total2");
			$data[$i][2][4]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total3");
			$data[$i][2][5]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total4");
			$data[$i][2][6]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total5");
			$data[$i][2][7]=M("Plmcontract")->where($mapforProject2)->sum("estimate_total6");
			$data[$i][2][8]=M("Plmcontract")->where($mapforProject2)->sum("para14");
			$data[$i][2][9]=$data[$i][2][0]+$data[$i][2][2];
			//+M("Project")->where($mapforProject2)->sum("quantities3")+M("Project")->where($mapforProject2)->sum("quantities4")+M("Project")->where($mapforProject2)->sum("quantities5")+M("Project")->where($mapforProject2)->sum("quantities6")
			$data[$i][2][10]=M("Plmcontract")->where($mapforProject2)->sum("para15");
			
		
			$mapforProject3["date"]=array("like","$year-".$ii."%");
			$mapforProject3["pworktype"]=array("like","%整形%");
			$data[$i][3][0]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
			$data[$i][3][1]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%复拌%");
			$data[$i][3][2]=M("Plmoutputdaily")->where($mapforProject3)->sum("value");
			$data[$i][3][3]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%地聚物注浆%");
			$data[$i][3][4]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%高聚物注浆%");
			$data[$i][3][5]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%大空隙灌浆%");
			$data[$i][3][6]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$mapforProject3["pworktype"]=array("like","%快速回填%");
			$data[$i][3][7]=M("Plmoutputdaily")->where($mapforProject3)->sum("money")/10000;
			$data[$i][3][8]="";
			$data[$i][3][9]=$data[$i][3][0]+$data[$i][3][2];
			$data[$i][3][10]=$data[$i][3][1]+$data[$i][3][3]+$data[$i][3][4]+$data[$i][3][5]+$data[$i][3][6]+$data[$i][3][7];
		}
		
		
		for($i=0;$i<=12;$i++)
		{
			for($j=1;$j<=4;$j++)
			{
				for($k=0;$k<=10;$k++)
				{
					if(($k==0)||($k==2)||($k==9))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k]/10000,2);//万m2  m2
					}
					if(($k==1)||($k==3)||($k==4)||($k==5)||($k==6)||($k==7)||($k==8)||($k==10))
					{
						$data[$i][$j][$k]=round($data[$i][$j][$k],4);
					}
					if(empty($data[$i][$j][$k]))$data[$i][$j][$k]=0;
					//$data[$i][$j][$k]=round($data[$i][$j][$k],0);
				}
				
			}
		}
		
		$x=0;
		for($i=0;$i<=12;$i++)
		{
			if($i==0)
				$temp=3;
			else
				$temp=3;
			for($j=1;$j<=$temp;$j++)
			{
				for($k=0;$k<=10;$k++)
				{
					$data1[$x][$k]=$data[$i][$j][$k];
				}
				$x++;
			}
		}
		
		
		$file="成交情况汇总";
		$title=$year."年各月成交情况汇总";
		$subtitle='';
		
		$th_array=array('','','','','','');
		$this->createExel($title,$th_array,$data1);
	}
	
	function createExel($title,$array_th,$data) 
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		if($_REQUEST["type"]=="成交把握度")
		{
			$objPHPExcel = $objReader->load ("../Public/template/template_secondbank.xls" );
		}
		else if($_REQUEST["type"]=="成交月度统计")
		{
			$objPHPExcel = $objReader->load ("../Public/template/template_secondbank2.xls" );
		}
		else
		{
			$objPHPExcel = $objReader->load ("../Public/template/template_secondbank1.xls" );
		}
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		
		$objActSheet->setCellValue ( 'B2', $title );
		//$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		//$objActSheet->setCellValue ( 'F2', $subtitle);
		
		if($array_th==null)
		{
			$array_th=array_keys($data[0]);
		}
	
		foreach($array_th as $key=>$value)
		{
			$objActSheet->getCellByColumnAndRow($key,3)->setValue($value);		
		}

		$baseRow = 6; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
		foreach ( $data as $r => $dataRow ) 
	    {
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value)
			{		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key+3,$row)->setValue($dataRow [$value]);
			}		 
		}
		$curdate=date('Ymd',time());
		$filename = $curdate."_".$title;
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );

	}
}
?>