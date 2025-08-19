<?php
class IndexAction extends CommonAction {
	// 框架首页
	public function index() {
		ini_set("memory_limit","-1");
		set_time_limit(120);
		$_SESSION["nn"]=$_SESSION["name"].$_SESSION["number"];
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
		$name = "Group";
		$model = M($name);
		$map[name]='Talk';
		$status=$model->where($map)->getField('status');
		$this->assign('status', $status);
		
		$company = "Cominfo";
		$commodel = D($company);
		$comname=$commodel->getField("name");
		$this->assign('comname',$comname);
		
		$Userskin=M("User");
		$mapskin['number']=$_SESSION['number'];
		$userinfo=$Userskin->where($mapskin)->find();
		//$skin=3;
		$_SESSION['skin']	=	$userinfo["skin"];
		$_SESSION['projecttype1']	=	$userinfo["projecttype1"];
		
		
		/*加入新模板之后移过来的，开始*/
		if($_SESSION[skin]!=3)
		{
			C('SHOW_RUN_TIME',false);			// 运行时间显示
			C('SHOW_PAGE_TRACE',false);
			$model	=	M("Group");
			$map['name']  = array('neq','Talk');
			$map['id']  = array('neq','18');
			$map['status']  = 1;
			
			$checkid=$_REQUEST ['id'];
			if($checkid==1)/*配置*/
			{
				//$map['id']  = 1;
				$map['id']  = array('in','2,17');
			}
			if($checkid==2)/*应用*/
			{
				$map['id']  = array('not in','2,17,18');
			}

			$grouplist	=	$model->where($map)->field('id,title,fa')->order('sort')->select();
			
			$node    =   M("Node");
		
			if(isset($_SESSION[C('USER_AUTH_KEY')])) {
				//显示菜单项
				$menu  = array();
				if(0)//isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]) 
				{
					//如果已经缓存，直接读取缓存
					$menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
				}
				else
				{
					//读取数据库模块列表生成菜单项                
					$id	=	$node->getField("id");
					$where['level']=2;
					$where['status']=1;
					$where['pid']=$id;
					$list =	$node->where($where)->order('sort asc')->select();
					$accessList = $_SESSION['_ACCESS_LIST'];
					foreach($list as $key=>$module) 
					{
						//if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator'])
						if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['id'])]) || $_SESSION['administrator'])
						{
							//设置模块访问权限
							$module['access'] =   1;
							$menu[$key]  = $module;
						}
					}
					//缓存菜单访问
					$_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
				}
				
				foreach($grouplist as $groupkey=>$groupvalue) 
				{
				   foreach($menu as $menukey=>$menuvalue)
				   {
					   if($menuvalue[group_id]==$groupvalue[id])
					   {
							$grouplist[$groupkey][flag]=1;
							break;
					   }
				   }
				}
	
				$this->assign('nodeGroupList',$grouplist);
				/*************3级子菜单*****************/
				$map3['status'] = 1;
				$map3['level']  = 3;
				$threelist=$node->where($map3)->order('sort')->select();
				foreach($threelist as $threekey=>$threevalue)
				{
					$maproleuser["user_id"]=$_SESSION["id"];
					$role_ids=M("Role_user")->where($maproleuser)->field("role_id")->select();
					foreach($role_ids as $role_idskey=>$role_idsvalue)
					{
						$role_id[$role_idskey]=$role_idsvalue["role_id"];
					}
					$mapaccess["node_id"]=$threevalue["id"];
					$mapaccess["role_id"]=array("in",$role_id);
					
					if(M("Access")->where($mapaccess)->find())
						$threelist[$threekey]["access"]=1;
					
				}
				
				$this->assign('threelist',$threelist);
				
				$three_number=count($threelist);				
				foreach($menu as $menukeys=>$menulistx)
				{
					for($n=0;$n<$three_number;$n++)
					{
						if($menulistx['id']==$threelist[$n]['pid'])
						{
							$menu[$menukeys]['threeexist']=1;
							break;
						}
					}
				}
							
				if($_SESSION['nopower']!=1)
				{
					$this->assign('menu',$menu);
				}
				
				foreach($menu as $menukey=>$menuvaluex)
				{
					if(($menuvaluex['group_id']==101)||($menuvaluex['group_id']==102)||($menuvaluex['group_id']==103)
					  ||($menuvaluex['group_id']==104)||($menuvaluex['group_id']==105)||($menuvaluex['group_id']==106)
					  ||($menuvaluex['group_id']==107)||($menuvaluex['group_id']==108)||($menuvaluex['group_id']==132)
					  ||($menuvaluex['group_id']==133)||($menuvaluex['group_id']==135)||($menuvaluex['group_id']==136)
					  ||($menuvaluex['group_id']==139))
						$this->assign('casemenulist',1);			   
						
					if(($menuvaluex['group_id']==111)||($menuvaluex['group_id']==112)||($menuvaluex['group_id']==113)||($menuvaluex['group_id']==114)||($menuvaluex['group_id']==115))	
						$this->assign('materialmenulist',1);					
						
					if(($menuvaluex['group_id']==3)||($menuvaluex['group_id']==122)||($menuvaluex['group_id']==123)||($menuvaluex['group_id']==124))
						$this->assign('dothingmenulist',1);			   
						
					if($menuvaluex['group_id']==134)
						$this->assign('budgetmenulist',1);			   
						
					if(($menuvaluex['group_id']==1)||($menuvaluex['group_id']==2)||($menuvaluex['group_id']==200))
						$this->assign('configmenulist',1);
				}
			}
			$Userskin    =   M("User");
			$skinmap['number']=$_SESSION['number'];
			$skin    =	 $Userskin->where($skinmap)->getField("skin");
			//$skin = 3 ;
			$this->assign('skin',$skin);
			$_SESSION['skin']	=	$skin;
			
			$workstr   =   M("Workstr");
			$workmap['status']=1;
			$works    =	 $workstr->order("id")->where($workmap)->select();
			$this->assign('works',$works);
			
			$myfworkmap[id]=18;
			$myfwork	=	$model->where($myfworkmap)->getField('status');
			$this->assign('myfwork',$myfwork);
	    }
		
		
		
    	$nodes=M('classify')->group("topname")->field("topname")->select();
		$this->assign('nodes',$nodes);
		
		
		$dept=M("dept");
		$mapsearch['id']=$_SESSION['department'];
		$searchret=$dept->where($mapsearch)->find();
		if($searchret['name']=='总裁办')
		{
			$this->assign('searchstatus',1);
		}
		
		/*加入新模板之后移过来的，结束*/
		if($skin==3)
		{
			$this->display('classicindex');
		}
		else	
		{
			$company = "Cominfo";
			$commodel = D($company);
			$comname=$commodel->getField("name");
			$comsubname=$commodel->getField("subname");
			$title=$commodel->getField("title");
			$subtitle=$commodel->getField("subtitle");
			$this->assign('comname',$comname);
			$this->assign('comsubname',$comsubname);
			$_SESSION[title]=$title;
			$_SESSION[subtitle]=$subtitle;
			//PublicAction::main();
			$this->top();
			
			$this->getAllcities();
		
			$this->display(indexoa);
		}
	}
	
	public function top()
	{
		
        $name = "Sendmail";
        $model = D($name);
        
		if($_SESSION[account]!="admin")
		{
			$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
		." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		}
		
		
        $count = $model->where($condition)->order('create_time desc')->count('id');
        $datamail1 = $model->where($condition)->order('create_time desc')->select();
        $this->assign('count',$count);
         
        for($i=0;$i<10;$i++)
        {
	        if($datamail1[$i][title]!=null)
	        {
	        	if($datamail1[$i][create_time]>time()-1*24*60*60)
	        	{	
		        	$datamail[$i][title1]=PublicAction::g_substr_mail($datamail1[$i][title]).'&nbsp;<img src="__PUBLIC__/Images/icons/new.gif"/></img>';
	        	}
	        	else
	        	{
					$datamail[$i][title1]=PublicAction::g_substr_mail($datamail1[$i][title]);
	        	}	
		        $datamail[$i][create_time]=$datamail1[$i][create_time];
				$datamail[$i][id]=$datamail1[$i][id];
		        
	        }
	        else
	        {
	        	$datamail[$i][title1]="&nbsp;";
	        }
        }

        $this->assign('datamail',$datamail);

        $nameform = "Form";
        $modelform = D($nameform);
        $mapform[status]=1;
		$mapform[photo]=array("neq","");
        $dataform=$modelform->where($mapform)->order('create_time desc')->select();
        $mapform[photo]=array("eq","");
        $dataform1=$modelform->where($mapform)->order('create_time desc')->select();	
        $this->assign('dataform',$dataform);      
		$this->assign('dataform1',$dataform1);      

        
		
		//查找同级别所有人
		$mapForUser["position"]=$_SESSION["position"];
		$users_on_mylevel_array=M("User")->where($mapForUser)->field("nickname,number")->select();
		foreach ($users_on_mylevel_array as $key=>$val)
		{
			$users_on_mylevel.=$val["nickname"].$val["number"].",";
		}
		if($_SESSION["projecttype"]=="")
		{
			
		}
		else
		{
			$mapsch["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		
        $namesch = "Schedule";
        $modelsch = D($namesch);
        $mapsch[status]=1;
        $mapsch[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
		//$mapsch[user]=array('in',$users_on_mylevel);
        $datasch1=$modelsch->where($mapsch)->order('create_time desc')->select();
        for($i=0;$i<10;$i++)
        {
			if($datasch1[$i][create_time]>time()-2*24*60*60)
			{	
				$datasch[$i][title1]=PublicAction::g_substr($datasch1[$i][content]).'&nbsp;<img src="__PUBLIC__/Images/icons/new.gif"/></img>';
			}
			else
			{
				$datasch[$i][title1]=PublicAction::g_substr($datasch1[$i][content]);
			}
			//$datasch[$i][title1]=$datasch1[$i][content];
			$datasch[$i][create_time]=$datasch1[$i][create_time];
			$datasch[$i][href]=$datasch1[$i][href];
			$datasch[$i][taskid]=$datasch1[$i][taskid];
			$slash=strpos($datasch[$i]["href"],"/");
			$datasch[$i]["rel"]=substr($datasch[$i]["href"],12,$slash-12);
        }
		
        $this->assign('schcount',count($datasch1));
        $this->assign('datasch',$datasch);
		
		$mapsch[status]=0;
        $dataschfinish1=$modelsch->where($mapsch)->order('create_time desc')->select();
        for($i=0;$i<5;$i++)
        {
	        if($dataschfinish1[$i][content]!=null)
	        {
	        	if($dataschfinish1[$i][create_time]>time()-2*24*60*60)
	        	{	
	       	    	$dataschfinish1[$i][title1]=PublicAction::g_substr($dataschfinish1[$i][content]).'&nbsp;<img src="__PUBLIC__/Images/icons/new.gif"/></img>';
	        	}
	        	else
	        	{
					$dataschfinish[$i][title1]=PublicAction::g_substr($dataschfinish1[$i][content]);
	        	}	
	       	    $dataschfinish[$i][create_time]=$dataschfinish1[$i][create_time];
	       	    $dataschfinish[$i][href]=$dataschfinish1[$i][href];
				$slash=strpos($dataschfinish[$i]["href"],"/");
				$dataschfinish[$i]["rel"]=substr($dataschfinish[$i]["href"],12,$slash-12);
	        }
	        else
	        {
	       	    $dataschfinish[$i][title1]="&nbsp;";
	        }
        }
        $this->assign('schcountfinish',count($dataschfinish1));
        $this->assign('dataschfinish',$dataschfinish);
        
		/*
		//$mapreels['grade']="优秀作品";
		$reels=M("Plmreels")->order('loadtime desc')->select();
		foreach ($reels as $key => $val)
		{
			$mapreelsplm[id]=$val[plmNumber];
			$ifperfect=M("Project")->where($mapreelsplm)->getField("grade");
			if($ifperfect=="优秀作品")
			{
				$reelsperfect=$val;
				break;
			}
		}
		$this->assign('reelsperfect',$reelsperfect);
		*/
	}
	
	
	
	
	public function index_v3() {
		$this->top();
		
		$myprojecttype=$_SESSION["projecttype"];
		$homepage=$_REQUEST["homepage"];
		

		if(empty($homepage)&&($myprojecttype=="集中式光伏发电"))$homepage=1;
		if(empty($homepage)&&($myprojecttype=="风力发电"))$homepage=2;
		if(empty($homepage)&&($myprojecttype=="集中式光伏发电,风力发电"))$homepage=1;
		
		$_REQUEST["homepage"]=$homepage;
		$this->assign('homepage',$homepage);
		$this->mainlist();
		
		
		
		
		$map['design_status'] = array("eq","施工中");
		$classify[0] = "主项";
		$classify[1] = "开发";
		$classify[2] = "设计";
		$classify[3] = "采购";
		$classify[4] = "施工";
		
		
		$projects=M("Project")->where($map)->order("id desc")->select();
		foreach($projects as $key => $val)
		{
			
			$mapforPlmoperatetype["id"]=$val["operatetypeid"];
			$operatetypecontent=M("Plmoperatetype")->where($mapforPlmoperatetype)->getField("content");
			if(false!==strstr($operatetypecontent,"工程"))
			{
				$projects[$key]["operatetype1"]=1;
			}
			if(false!==strstr($operatetypecontent,"开发"))
			{
				$projects[$key]["operatetype2"]=1;
			}
			if(false!==strstr($operatetypecontent,"设计"))
			{
				$projects[$key]["operatetype3"]=1;
			}
			if(false!==strstr($operatetypecontent,"采购"))
			{
				$projects[$key]["operatetype4"]=1;
			}
			if(false!==strstr($operatetypecontent,"工程"))
			{
				$projects[$key]["operatetype5"]=1;
			}
			
			
			for($i=1;$i<=5;$i++)
			{
				$mapforPlmschedule["plmid"] = array("eq",$val["id"]);
				$mapforPlmschedule["classify"] = array("like","%".$classify[$i-1]."%");
				$worktypes=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
				$projects[$key]["finish".$i]=0;
				$projects[$key]["unfinish".$i]=0;
				$projects[$key]["warning".$i]=0;
				
				
				foreach($worktypes as $key1 => $val1)
				{
					if($val1["percent"]=="100%")
					{
						$projects[$key]["finish".$i]++;
						
						if($val1["realtimeend"]>$val1["plantimeend"])
						{
							$projects[$key]["warning".$i]++;
						}
						
					}
					else
					{
						$projects[$key]["unfinish".$i]++;
						
						if((date("Y-m-d")>$val1["plantimeend"]))
						{
							$projects[$key]["warning".$i]++;
						}
					}
					
				}
				
				
				if(!empty($projects[$key]["warning".$i]))
				{
					$projects[$key]["warning"]=1;
				}
				
			}
			
		
		}
		$this->assign('list',$projects);
		$this->assign("worktypes", $worktypes);
		
		
		
		
		if($_SESSION[account]!="admin")
		{
			$map1['keeper'] = $_SESSION["nickname"];
		}
		$map1['file'] = array(array("neq",""),array("neq",","),array("neq",",,"),array("neq",",,,"),array("neq",",,,"),array("exp","is not null"),"and");
		$map1['filereceiveuser'] = array(array("eq",""),array("exp","is null"),"or");
		$name = "Plmschedule";
		$filereceivelist=M("Plmschedule")->where($map1)->order("update_time desc")->select();
		foreach($filereceivelist as $key => $val)
		{
			$filereceivelist[$key]['files']=explode(',',$val['file']);
			$filereceivelist[$key]['filesrealname']=explode(',',$val['filerealname']);
			
			$filereceivelist[$key]["plminfo"]=M("Project")->where("id=".$val[plmid])->field("title")->find();
		}
		$this->assign('filereceivelist',$filereceivelist);
		
		
		
		
		$this->display();
		return;
	}
	public function mainlist() {
		
	
		//$mapforprojectfortop[design_status]=array("in","合同审核完成,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回,暂停中");
		
		$mapforprojectfortop[design_status]=array("not in","暂存");
		$mapforprojectfortop1[design_status]=array("not in","暂存");
		$mapforprojectfortop2[design_status]=array("not in","暂存");
		$mapforProject[design_status]=array("not in","暂存");
		$mapforProject1[design_status]=array("not in","暂存");
		$mapforproject2[design_status]=array("not in","暂存");
		
		$mapforprojectfortop['_complex'] = $this->find5level($_SESSION[position],$mapforprojectfortop);
		if($_SESSION[account]!="admin")
		{
			$mapforprojectfortop['_complex'] = $this->find5level($_SESSION[position]);//,$mapforprojectfortop
			$mapforprojectfortop1['_complex'] = $this->find5level($_SESSION[position]);//,$mapforprojectfortop
			$mapforprojectfortop2['_complex'] = $this->find5level($_SESSION[position]);//,$mapforprojectfortop
			$mapforProject['_complex'] = $this->find5level($_SESSION[position]);//,$mapforProject//注意有大小写的区分
			$mapforProject1['_complex'] = $this->find5level($_SESSION[position]);//,$mapforProject1
			$mapforproject2['_complex'] = $this->find5level($_SESSION[position]);//,$mapforproject2
			$mapforplmwarnings['_complex'] = $this->find5level($_SESSION[position]);
		}
		
		$projects=M("Project")->where($mapforprojectfortop)->field("id")->select();
		$projectcount=count($projects);
		//项目总数
		$this->assign('projectcount',$projectcount);
		
		
		
		//施工中项目
		$mapforprojectfortop2[design_status]=array("in","施工中");
		$mapforprojectfortop2[activity]=array("exp","is null");
		$projects2=M("Project")->where($mapforprojectfortop2)->select();
		foreach($projects2 as $key => $val)
		{
			$mapforworktype[plmid]=$val[id];
			//$mapforworktype[percent]=array("neq","100%");
			$mapforworktype[percent]=array(array("neq",""),array("neq","0%"),"and");
			$mapforworktype[status]=1;
			$projects2[$key][subworktype]=M("Plmschedule")->where($mapforworktype)->order("sort desc")->find();
			$projects2[$key][group]=M("Secondgroup")->where("id=".$val[groupid])->getField("name");
			
			$projects2[$key][subtitle]=$this->g_substr($projects2[$key][title],65);
		}
		$this->assign('projects2',$projects2);
		$projectcount2=count($projects2);
		
		//待施工项目
		$mapforprojectfortop_1=$mapforprojectfortop;
		$mapforprojectfortop_1[design_status]=array("in","立项中");
		$projectcount1=M("Project")->where($mapforprojectfortop_1)->count();
		
		//施工完成
		$mapforprojectfortop3=$mapforprojectfortop;
		$mapforprojectfortop3['design_status'] = array('in',"施工完成");
		$projectcount3=M("Project")->where($mapforprojectfortop3)->count();
		
		//完成验收
		$mapforprojectfortop4=$mapforprojectfortop;
		$mapforprojectfortop4['design_status'] = array('in',"完成验收");
		$projectcount4=M("Project")->where($mapforprojectfortop4)->count();
		
		$mapforprojectfortop5=$mapforprojectfortop;
		$mapforprojectfortop5[design_status]=array("in","取消,暂停中");
		$projectcount5=M("Project")->where($mapforprojectfortop5)->count();
		
		
		
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
		$mapforplmwarnings['id'] = array('in',$plmwarningids);
		//if(empty($plmwarnings))$projectcount6=0;
		//else $projectcount6=count($plmwarnings);
		$projectcount6=M("Project")->where($mapforplmwarnings)->count();
		if(empty($projectcount6))$projectcount6=0;
		
		
		//$mapforprojectfortop[design_status]=array("in","储备");
		$mapforprojectfortop_2=$mapforprojectfortop;
		$mapforprojectfortop_2[design_status]=array("in","立项中");
		$mapforprojectfortop_2[step3]=array("neq","1");
		$projectcount7=M("Project")->where($mapforprojectfortop_2)->count();
		
		$this->assign('projectcount1',$projectcount1);
		$this->assign('projectcount2',$projectcount2);
		$this->assign('projectcount3',$projectcount3);
		$this->assign('projectcount4',$projectcount4);
		$this->assign('projectcount5',$projectcount5);
		$this->assign('projectcount6',$projectcount6);
		$this->assign('projectcount7',$projectcount7);
		
		$percent1=round(100*$projectcount1/$projectcount,0);
		$percent2=round(100*$projectcount2/$projectcount,0);
		$percent3=round(100*$projectcount3/$projectcount,0);
		$percent4=round(100*$projectcount4/$projectcount,0);
		$percent7=round(100*$projectcount7/$projectcount,0);
		$this->assign('percent1',$percent1);
		$this->assign('percent2',$percent2);
		$this->assign('percent3',$percent3);
		$this->assign('percent4',$percent4);
		$this->assign('percent7',$percent7);
		
		foreach($projects2 as $key => $val)
		{
			$ingplmidstr.=$val[id].",";
		}
		foreach($projects as $key => $val)
		{
			$allplmidstr.=$val[id].",";
		}
		
	
		
		
		
		//今日在岗数量
		$mapforPlmschedule1[plmid]=array("in",$ingplmidstr);
		$mapforPlmschedule1["date"]=array("eq",date("Y-m-d"));
		$prewarningcount=M("Plmdaily")->where($mapforPlmschedule1)->count();
		if($prewarningcount=="")$prewarningcount=0;
		$this->assign('prewarningcount',$prewarningcount);
		
		
		$mapforPlmschedule2[plmid]=array("in",$ingplmidstr);
		$mapforPlmschedule2[warning]=array("eq","1");
		$warningcount=M("Plmwarning")->where($mapforPlmschedule2)->count();
		if($warningcount=="")$warningcount=0;
		
		$mapforPlmschedule3[plmid]=array("in",$allplmidstr);
		$mapforPlmschedule3[status]=array("eq","1");
		$warningcount1=M("Plmwarningapprove")->where($mapforPlmschedule3)->count();
		if($warningcount1=="")$warningcount1=0;
		
		$warningcount+=$warningcount1;
		$this->assign('warningcount',$warningcount);
		
		//app项目数量
		//$mapforproject[design_status]=array("in","合同审核完成,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回,暂停中");
		$mapforProject[design_status]=array("not in","暂存");
		if($_SESSION["account"]!="admin")
		{
			$mapforProject['_complex'] = $this->find5level($_SESSION[position]);//,$mapforproject
		}
		
		$projects=M("Project")->where($mapforProject)->field("id,design_status")->select();
		$projectcount=count($projects);
		$this->assign('projectcount',$projectcount);
		
		//常用模块
		$mapforModulelog[account]=$_SESSION[account];
		$mapforModulelog[moduletitle]=array("neq","首页模块");
		$modules=M("Modulelog")->where($mapforModulelog)->order("number desc")->limit(6)->select();
		$this->assign('modules',$modules);
		
		//城市分类
		$mapforProject["city"]=array("neq","");
		$cities=M("Project")->where($mapforProject)->group("city")->field("city")->select();
		foreach($cities as $key => $val)
		{
			$mapforProject[city] = $val[city];
			$cities[$key][projects]=M("Project")->where($mapforProject)->field("city,design_status,construction_status,activity,step3")->select();
			$cities[$key][count0]=0;
			$cities[$key][count1]=0;
			$cities[$key][count2]=0;
			$cities[$key][count3]=0;
			$cities[$key][count4]=0;
			$cities[$key][count5]=0;
			$cities[$key][count6]=0;
			$cities[$key][countall]=0;
			foreach($cities[$key][projects] as $key1 => $val1)
			{
				if(($val1[design_status]=="立项中"))
				{
					if($val1["step3"]!="1")
					{
						$cities[$key][count1]++;
						$cities[$key][countall]++;
					}
				}
				if(($val1[design_status]=="待施工"))//待施工
				{
					if($val1["step3"]=="1")
					{
						$cities[$key][count2]++;
						$cities[$key][countall]++;
					}
				}
				if(($val1[design_status]=="施工中"))//施工中
				{
					$cities[$key][count3]++;
					$cities[$key][countall]++;
				}
				if(($val1[design_status]=="施工完成")||($val1[design_status]=="完成施工"))
				{
					$cities[$key][count4]++;
					$cities[$key][countall]++;
				}
				if(($val1[design_status]=="完成验收")||($val1[design_status]=="验收完成"))
				{
					$cities[$key][count5]++;
					$cities[$key][countall]++;
				}
				if($val1[design_status]=="暂停中")//暂停中
				{
					$cities[$key][count6]++;
					$cities[$key][countall]++;
				}
				if($val1[design_status]=="取消")//取消
				{
					$cities[$key][count7]++;
					$cities[$key][countall]++;
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
		$this->assign('cities',$cities);
		
		
		
		
		
		
		//场站分类
		$taketypes=M("Project")->where($mapforProject1)->group("taketype")->field("taketype")->select();
		foreach($taketypes as $key => $val)
		{
			$mapforProject1[taketype] = $val[taketype];
			$taketypes[$key][projects]=M("Project")->where($mapforProject1)->field("taketype,design_status,construction_status,activity,step3")->select();
			$taketypes[$key][count0]=0;
			$taketypes[$key][count1]=0;
			$taketypes[$key][count2]=0;
			$taketypes[$key][count3]=0;
			$taketypes[$key][count4]=0;
			$taketypes[$key][count5]=0;
			$taketypes[$key][countall]=0;
			foreach($taketypes[$key][projects] as $key1 => $val1)
			{
				if(($val1[design_status]=="初步立项待审批")||($val1[design_status]=="初步立项审批通过")||($val1[design_status]=="初步立项审批退回")||($val1[design_status]=="可研编制文件待审批")||($val1[design_status]=="可研编制文件审批通过")||($val1[design_status]=="可研编制文件审批退回")||($val1[design_status]=="可研评审报告待审批")||($val1[design_status]=="可研评审报告审批中")||($val1[design_status]=="可研评审报告审批退回")||($val1[design_status]=="可研评审报告审批通过"))
				{
					if($val1["step3"]!="1")
					{
						$taketypes[$key][count1]++;
						$taketypes[$key][countall]++;
					}
				}
				if(($val1[design_status]=="可研评审报告审批通过")||($val1[design_status]=="招标待审核")||($val1[design_status]=="招标审核通过")||($val1[design_status]=="招标审核退回")||($val1[design_status]=="合同待审核")||($val1[design_status]=="合同审核中")||($val1[design_status]=="合同审核完成")||($val1[design_status]=="合同审核退回")||($val1[design_status]=="设计待审核")||($val1[design_status]=="设计审核通过")||($val1[design_status]=="设计审核退回")||($val1[design_status]=="施工计划待审核")||($val1[design_status]=="施工计划审核通过")||($val1[design_status]=="施工计划审核退回")||($val1[design_status]=="待施工"))
				{
					//待施工
					if($val1["step3"]=="1")
					{
						$taketypes[$key][count2]++;
						$taketypes[$key][countall]++;
					}
				}
				if((($val1[design_status]=="施工中")||($val1[design_status]=="完成施工")||($val1[design_status]=="已完成")||($val1[design_status]=="完成验收")||($val1[design_status]=="竣工待验收")||($val1[design_status]=="项目待验收")||($val1[design_status]=="验收审核退回"))&&($val1[activity]!="投入使用"))//施工中
				{
					//施工中
					$taketypes[$key][count3]++;
					$taketypes[$key][countall]++;
				}
				if($val1[activity]=="投入使用")//投入使用
				{
					//完成施工
					$taketypes[$key][count4]++;
					$taketypes[$key][countall]++;
				}
				if($val1[design_status]=="暂停中")
				{
					//暂停中
					$taketypes[$key][count5]++;
					$taketypes[$key][countall]++;
				}
			}
		}
		
		
		$sort = array(  
			'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
			'field'     => 'count2',       //排序字段  
		);  
		$arrSort = array();  
		foreach($taketypes AS $uniqid => $row){  
			foreach($row AS $key=>$value){  
				$arrSort[$key][$uniqid] = $value;  
			}  
		}  
		if($sort['direction']){  
			array_multisort($arrSort[$sort['field']], constant($sort['direction']), $taketypes);  
		}
		$taketypes=array_slice($taketypes,0,100);
		$this->assign('taketypes',$taketypes);
		
		
		
		$data[projectcount]=$projectcount;
		$data[worktypecount]=$worktypecount;
		$data[prewarningcount]=$prewarningcount;
		$data[warningcount]=$warningcount;
		$data[projectcount1]=$projectcount1;
		$data[percent1]=$percent1."%";
		$data[projectcount2]=$projectcount2;
		$data[percent2]=$percent2."%";
		$data[projectcount3]=$projectcount3;
		$data[percent3]=$percent3."%";
		$data[projectcount4]=$projectcount4;
		$data[percent4]=$percent4."%";
		
		$data[projectcount5]=$projectcount5;
		$data[percent5]=$percent5."%";
		$data[projectcount6]=$projectcount6;
		$data[percent6]=$percent6."%";
		$data[projectcount7]=$projectcount7;
		$data[percent7]=$percent7."%";
		
		$data[cities]=$cities;
		$data[taketypes]=$taketypes;
		return $data;
		
    }
	
	
	public function classicindexdemo() {
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
	
		$name = "Group";
		$model = M($name);
		$map[name]='Talk';
		$status=$model->where($map)->getField('status');
		$this->assign('status', $status);
		$Userskin=M("User");
		$mapskin['number']=$_SESSION['number'];
		$skin=$Userskin->where($mapskin)->getField('skin');
		$skin=3;
		$_SESSION['skin']	=	$skin;
		$this->display('classicindexdemo');
	}
	
	public function main()
	{
		$company = "Cominfo";
		$commodel = D($company);
		$comname=$commodel->getField("name");
		$comsubname=$commodel->getField("subname");
		$this->assign('comname',$comname);
		$this->assign('comsubname',$comsubname);
		PublicAction::main();
		$this->display("list");
	}
	
	public function modify()
	{
		$model = D("Project");
		$map["charge"]="章江明";
		$model->where($map)->setField("charge","章江明一");
		$model->where($map)->setField("user","章江明一");
		
		$map["charge"]="章 江 明";
		$model->where($map)->setField("charge","章江明一");
		$model->where($map)->setField("user","章江明一");
		
		$map["charge"]="张敏";
		$model->where($map)->setField("charge","张敏一");
		$model->where($map)->setField("user","张敏一");
		
		$map["charge"]="张 敏";
		$model->where($map)->setField("charge","张敏一");
		$model->where($map)->setField("user","张敏一");
		
		$map["charge"]="马凯";
		$model->where($map)->setField("charge","马凯一");
		$model->where($map)->setField("user","马凯一");
		
		$map["charge"]="马 凯";
		$model->where($map)->setField("charge","马凯一");
		$model->where($map)->setField("user","马凯一");
		
		$map["charge"]="余智锋";
		$model->where($map)->setField("charge","余智锋一");
		$model->where($map)->setField("user","余智锋一");
		
		$map["charge"]="余 智 锋";
		$model->where($map)->setField("charge","余智锋一");
		$model->where($map)->setField("user","余智锋一");
		
		$map["charge"]="高志荣";
		$model->where($map)->setField("charge","高志荣一");
		$model->where($map)->setField("user","高志荣一");
		
		$map["charge"]="高 志 荣";
		$model->where($map)->setField("charge","高志荣一");
		$model->where($map)->setField("user","高志荣一");
		
		$mapfordesigner["designer"]="王文鑫一";
		$model->where($mapfordesigner)->setField("designbuild_user","王文鑫一");
		$model->where($mapfordesigner)->setField("budget_user","王文鑫一");
		
		$mapfordesigner["designer"]="邹海奇一";
		$model->where($mapfordesigner)->setField("designbuild_user","邹海奇一");
		$model->where($mapfordesigner)->setField("budget_user","邹海奇一");
		
		$mapfordesigner["designer"]="司云峰一";
		$model->where($mapfordesigner)->setField("designbuild_user","司云峰一");
		$model->where($mapfordesigner)->setField("budget_user","司云峰一");
		
		$mapfordesigner["designer"]="李游一";
		$model->where($mapfordesigner)->setField("designbuild_user","李游一");
		$model->where($mapfordesigner)->setField("budget_user","李游一");
		
		$mapfordesigner["designer"]="朱懿一";
		$model->where($mapfordesigner)->setField("designbuild_user","朱懿一");
		$model->where($mapfordesigner)->setField("budget_user","朱懿一");

	}
	public function modify1()
	{
		$model = D("Project");
		$map["charge"]="界定员";
		$model->where($map)->setField("charge","界定营销员");
		
		echo "success";
	}
	public function ajax1(){
		$map['password'] = pwdHash($_POST['oldpassword']);
		$map['account'] = $_SESSION[account];
		$user=M("user")->where($map)->find();
		if(!$user){
			echo json_encode('旧密码不符！');
		}
		elseif(md5($_POST['verify']) != $_SESSION['verify']) {
			echo json_encode('验证码错误！');
		}
		elseif($_POST['password'] !=$_POST['repassword']){
			echo json_encode('两次输入密码不一致！');
		}
		else{
			echo 1;
		}
		
	}
}
?>