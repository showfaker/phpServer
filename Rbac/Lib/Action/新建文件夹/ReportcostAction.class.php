<?php
class ReportcostAction extends CommonAction {		
	function _initialize() {
        import('@.ORG.Util.Cookie');
    }
	public function index() {
        if(empty($_REQUEST['tab']))
		{
			$_REQUEST['tab']=1;
			$this->assign('tab',$_REQUEST['tab']);	
		}
		if(($_REQUEST['tab']=="")||($_REQUEST['tab']=="1"))
		{
			//查找项目所有城市
			$projects=M("Project")->select();
			foreach($projects as $key => $val)
			{
				
				$mapforProject[plmid] = $val[id];
				$projects[$key][groups]=M("Plmgroup")->where($mapforProject)->select();
				//$mapforPlmcontract["plmNumber"] = $val[id];
				//$contractinfo=M("Plmcontract")->where($mapforPlmcontract)->find();
				foreach($projects[$key][groups] as $key1 => $val1)
				{
					$mapforPlmattendancedevice[groupid]=$val1[id];
					$projects[$key][groups][$key1]["para1"]=M("Plmattendancedevice")->where($mapforPlmattendancedevice)->min("date");
					$projects[$key][groups][$key1]["para2"]=$val["construction_start"];
					$projects[$key][groups][$key1]["para3"]=$val["finishtime"];
					$projects[$key][groups][$key1]["para4"]=M("Plmattendancedevice")->where($mapforPlmattendancedevice)->max("date");
					
					$projects[$key][groups][$key1]["para5"]=1+(strtotime($projects[$key][groups][$key1]["para4"])-strtotime($projects[$key][groups][$key1]["para1"]))/(24*60*60);//机组到达总时间
					if(($projects[$key][groups][$key1]["para4"]==$projects[$key][groups][$key1]["para1"])&&($projects[$key][groups][$key1]["para1"]!=""))
					{
						$projects[$key][groups][$key1]["para5"]=1;
					}
					
					$mapforPlmoutputdaily[groupid]=$val1[id];
					$mapforPlmoutputdaily[pworktype]=array("like","%热再生%");
					$mapforPlmoutputdaily["value"]=array("not in","0,");
					$tempdata=M("Plmoutputdaily")->where($mapforPlmoutputdaily)->group("date")->field("date")->select();
					if(empty($tempdata))$projects[$key][groups][$key1]["para6"]=0;else $projects[$key][groups][$key1]["para6"]=count($tempdata);//有效施工天数是根据每天填写的产值算的
					
					$mapforPlmoutputdaily1[groupid]=$val1[id];
					$mapforPlmoutputdaily1[pworktype]=array("like","%热再生%");
					$projects[$key][groups][$key1]["para7"]=M("Plmoutputdaily")->where($mapforPlmoutputdaily1)->sum("value");
					
					$projects[$key][groups][$key1]["para8"]=round($projects[$key][groups][$key1]["para7"]/$projects[$key][groups][$key1]["para6"],2);
					
					
					$mapforPlmmaterials["groupid"]=$val1[id];
					$mapforplmmaterials[name]=array("like","%沥青%");
					$projects[$key][groups][$key1]["para9"]=M("Plmmaterials")->where($mapforPlmmaterials)->sum("count");
					
					
					
					
					
					$mapforPlmmaterials1['groupid']=$val1[id];
					$mapforPlmmaterials1['density']=array("neq","0");
					$mapforPlmmaterials1['step']=array("like","%热再生%");
					$density=M("Plmmaterials")->where($mapforPlmmaterials1)->avg("density");
					$density=round($density,2);
					
					$mapforPlmmaterials3['groupid']=$val1[id];
					$mapforPlmmaterials3['step']=array("like","%热再生%");
					$mapforPlmmaterials3['name']=array("like","%沥青%");
					$zz=M("Plmmaterials")->where($mapforPlmmaterials3)->sum("count");
					
					$mapforPlmoutputdaily['groupid']=$val1[id];
					$mapforPlmoutputdaily['pworktype']=array("like","%热再生%");
					$outputdaily=M("Plmoutputdaily")->where($mapforPlmoutputdaily)->sum("value");
					$average=round($zz/$outputdaily,2);
					
				
					$thick=round(100*$zz/$density/$outputdaily,2);
					$projects[$key][groups][$key1]["para10"]=$thick;
					
					
					
					
					
					$mapforPlmattendancedevice1[groupid]=$val1[id];
					$mapforPlmattendancedevice1[status]="在岗";
					$tempdata=M("Plmattendancedevice")->where($mapforPlmattendancedevice1)->group("date")->field("date")->select();
					if(empty($tempdata))$tempdata1=0;else $tempdata1=count($tempdata);//机械在岗天数
					$projects[$key][groups][$key1]["para11"]=$projects[$key][groups][$key1]["para5"]-$tempdata1;//总天数-在岗天数
				}
				$projects[$key][count1]=0;
				$projects[$key][count2]=0;
				$projects[$key][count3]=0;
				$projects[$key][count4]=0;
			}
			$this->assign('projects',$projects);	
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
		if(($_REQUEST['tab']=="8"))
		{
			if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
			$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
			else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
			else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
			$this->assign('timebegin', $_REQUEST['timebegin']);
			$this->assign('timeend', $_REQUEST['timeend']);
			
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$map[design_status]=array("in","完成验收");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'last_time',false);
			}
		
			$this->display();
			return;
		}
		if(($_REQUEST['tab']=="9"))
		{
			if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
			$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
			else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
			else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
			$this->assign('timebegin', $_REQUEST['timebegin']);
			$this->assign('timeend', $_REQUEST['timeend']);
			
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$map[design_status]=array("in","完成验收");
			$map[activity]=array("in","投入使用");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'last_time',false);
			}
		
			$this->display();
			return;
		}
		if($_REQUEST['tab']=="10")
		{
			/*
			//查找项目专项
			$mapforWorktype[type]=1;
			$worktypes=M("Worktype")->where($mapforWorktype)->field("title")->select();
			foreach($worktypes as $key => $val)
			{
				$mapforPlmwarning[worktype] = $val[title];
				$mapforPlmwarning[percent] = array(array("neq","0%"),array("neq","100%"),array("neq",""),"and");
				$mapforPlmwarning[status]=1;
				$temp=M("Plmschedule")->where($mapforPlmwarning)->group("plmid")->select();
				if(empty($temp))$worktypes[$key][count1]=0;
				else $worktypes[$key][count1]=count($temp);
			}*/
			$mapforWorktype[type]=1;
			$worktypes=M("Worktype")->where($mapforWorktype)->field("title")->select();
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
			foreach($voList as $key => $val)
			{
				$voList[$key]['finishs']=explode(',',$val['finish']);
				$voList[$key]['finishsfilename']=explode(',',$val['finishfilename']);
				
				$voList[$key]['budgetsfinal']=explode(',',$val['budgetfinal']);
				$voList[$key]['budgetsfinalfilename']=explode(',',$val['budgetfinalfilename']);
				
				$voList[$key]['finishphotos']=explode(',',$val['finishphoto']);
				
				
				if(($_REQUEST['tab']=="9"))
				{
					$voList[$key]['drawings']=explode(',',$val['drawing']);
					$voList[$key]['drawingsfilename']=explode(',',$val['drawingfilename']);
					
					$voList[$key]['finishphotos']=explode(',',$val['finishphoto']);
					$voList[$key]['finishphotosfilename']=explode(',',$val['finishphotofilename']);
					
					$voList[$key]['finishs']=explode(',',$val['finish']);
					$voList[$key]['finishsfilename']=explode(',',$val['finishfilename']);
					
					$voList[$key]['budgetsfinal']=explode(',',$val['budgetfinal']);
					$voList[$key]['budgetsfinalfilename']=explode(',',$val['budgetfinalfilename']);
					
					$voList[$key]['contract']=explode(',',$val['contract']);
					$voList[$key]['contractfilename']=explode(',',$val['contractfilename']);
				}
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
}
?>