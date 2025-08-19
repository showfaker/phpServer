<?php
class SecondpublishcheckAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		//$map[step6]=1;
		$map['projecttype'] = array("neq","承揽项目");
		$map['step3'] = array("eq","1");
		$map['step6'] = array("egt","0.4");
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
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
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
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('like',"%".$_REQUEST['plmid']."%");
			$this->assign("plmid",$_REQUEST['plmid']);
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
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		$map[design_status]=array("in","可研评审报告审批通过,待施工,施工中,完成施工,竣工待验收,项目待验收,验收审核退回");//完成验收
		
		
		if(($_SESSION["role"]=="监理单位")||($_SESSION["role"]=="施工单位")||($_SESSION["role"]=="设计单位")||($_SESSION["role"]=="投标单位"))
		{
			$map[sendtask_time]=array("exp","is not null");
		}
		//$map['three'] = 1;
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		$this->getAllcities(1);
		
		if($_SESSION["app"]=="1")
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
			foreach ($voList as $key => $val) {
				
				$voList[$key]["construction_start"]=M("Plmschedule")->where("plmid=".$val[id])->min("plantimebegin");
				$voList[$key]["finishtime"]=M("Plmschedule")->where("plmid=".$val[id])->max("plantimeend");
		
				$voList[$key]['taskfile']=explode(',',$val['taskfile']);
				$voList[$key]['taskfilename']=explode(',',$val['taskfilename']);
			}
			
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
				else
				{
					$p->parameter .= "$key=" . $_REQUEST[$key] . "&";
				}
            }
			if($_REQUEST['city'])
			{
				$p->parameter .= "city=" . urlencode($_REQUEST['city']) . "&";
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
		Cookie::set('_currentUrl_', __SELF__.$p->parameter);
		
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
		else
		{
			$titlerepeat["id"]=array("eq",$_REQUEST[id]);
			$olddata=M("Project")->where($titlerepeat)->find();
			if($olddata["title"]!=$_REQUEST["title"])
			{
				$mapforplmedit["plm"]=$olddata["title"];
				$plmeditid=M("Plmattendance")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmattendancedevice")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmdaily")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmfile")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmfilediaodu")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmmaterialorder")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmmaterials")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmorder2")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmorder2paytime")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmplan")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				
				
				$mapforplmedit1["title"]=$olddata["title"];
				$plmeditid=M("Plmbid")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
				$plmeditid=M("Plmcontract")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
				$plmeditid=M("Plmoffer")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
				
				
				
				$mapforplmedit2["address"]=$olddata["title"];
				$plmeditid=M("Plmdiscuss")->where($mapforplmedit2)->setField("address",$_REQUEST["title"]);
			}
		}
		
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file']['name'];
			$file_tmp=$_FILES['file']['tmp_name'];
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$allowedExts = array("pdf");
					if(!in_array($ext, $allowedExts))
					{
						$this->error('请上传pdf文件!');
					}
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						$this->error("非法文件类型！");
					}
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->taskfile=$newnameall;
			$model->taskfilename=$filenameall;
		}
		
		if($_REQUEST["type"]!="3")
		{
			$model->settask_time=time();
			$date=date('m-d H:i');
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			$address=$info[title];
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置任务单</br>------------------</br>"; 
			$list = $model->save();
			
			if($info["step6"]=="0.4")
			{
				M("Project")->where("id=".$info[id])->setField("step6","1");
				M("Project")->where("id=".$info[id])->setField("design_status","待施工");
			}
		}
		else
		{
			$list = $model->save();
			//派发任务单
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			M("Project")->where("id=".$info[id])->setField("sendtask_time",time());
			$date=date('m-d H:i');
			$address=$info[title];
			$handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."派发任务单</br>------------------</br>"; 
			M("Project")->where("id=".$info[id])->setField("handlehistory",$handlehistory);
				
		}
		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success('操作成功!');
		
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
			//金吉鸟这里不会走到
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
		$model1=M("Companysupervise");
		$companysupervises=$model1->order("id asc")->select();
		
		$model2=M("Companydesign");
		$companydesigns=$model2->order("id asc")->select();
		
		$this->assign('companysupervises',$companysupervises);
		$this->assign('companydesigns',$companydesigns);
		
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		
		$thisyear=date("Y");
		$mapfororder["time"]=array("like","%".$thisyear."%");
		$todaycount=M("Project")->where($mapfororder)->count();
		$todaycount=$todaycount+1;
		if($todaycount<10)$todaycount="000".$todaycount;
		else if($todaycount<100)$todaycount="00".$todaycount;
		else if($todaycount<1000)$todaycount="0".$todaycount;
		else if($todaycount<10000)$todaycount="".$todaycount;
		$thisorder="GC".date("Y").$todaycount;
		$this->assign('thisorder', $thisorder);
		
	
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$vo['chargedevice1array']=explode(';',$vo['chargedevice1']);
		$vo['chargedevice2array']=explode(';',$vo['chargedevice2']);
		$vo['chargedevice3array']=explode(';',$vo['chargedevice3']);
		$vo['chargedevice4array']=explode(';',$vo['chargedevice4']);
		$vo['chargedevice5array']=explode(';',$vo['chargedevice5']);
		$vo['chargedevice6array']=explode(';',$vo['chargedevice6']);
		$vo['chargedevice7array']=explode(';',$vo['chargedevice7']);
		$vo['chargedevice8array']=explode(';',$vo['chargedevice8']);
		$vo['chargedevice9array']=explode(';',$vo['chargedevice9']);
		$vo['devicescalearray']=explode(';',$vo['devicescale']);
		
		$vo['taskfile']=explode(',',$vo['taskfile']);
		$vo['taskfilename']=explode(',',$vo['taskfilename']);
				
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		
		$this->assign('type',$_REQUEST[type]);
		$this->assign('check',$_REQUEST[check]);
		$this->display();
	}
	function invoice_print() {
		$name = "Project";
		$model = M($name);
	
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		//$vo["companyid"]=M("Plmbid")->where("plmNumber=".$vo[id])->getField("para20");
		//$vo["company"]=M("Company")->where("id=".$vo["companyid"])->getField("para1");
		
		$companystr=M("Plmbid")->where("plmNumber=".$vo[id])->getField("para20");
		$companyarray=explode(",",$companystr);
		
		foreach($companyarray as $key => $val)
		{
			$valarray=explode("|",$val);
			if($valarray[0]=="中标单位")
			{
				
				$vo["companyid"]=$valarray[1];
				$vo["company"]=M("Company")->where("id=".$vo["companyid"])->getField("para1");
			}
		}
		
		$vo["construction_start"]=M("Plmschedule")->where("plmid=".$vo[id])->min("plantimebegin");
		$vo["finishtime"]=M("Plmschedule")->where("plmid=".$vo[id])->max("plantimeend");
		
		
		$type=str_replace("项目","",$vo["type"]);
		$chargedevice1array=explode(";",$vo["chargedevice1"]);
		$chargedevice2array=explode(";",$vo["chargedevice2"]);
		$chargedevice3array=explode(";",$vo["chargedevice3"]);
		$chargedevice4array=explode(";",$vo["chargedevice4"]);
		$chargedevice5array=explode(";",$vo["chargedevice5"]);
		$chargedevice6array=explode(";",$vo["chargedevice6"]);
		$devicescalearray=explode(";",$vo["devicescale"]);
		for($ii=0;$ii<10;$ii++)
		{
			$chargedevice1=$chargedevice1array[$ii];
			$chargedevice2=$chargedevice2array[$ii];
				
			if($chargedevice1!="")
			{
				$chargedevice_temp1 .= $chargedevice1."kVA箱变".$chargedevice2."台，";
			}
			
			$chargedevice5=$chargedevice5array[$ii];
			$chargedevice3=$chargedevice3array[$ii];
			$chargedevice4=$chargedevice4array[$ii];
			
			if($chargedevice5!="")
			{
				$chargedevice_temp2 .= $chargedevice5."kW".$chargedevice3.$chargedevice4."台，";
				$devicescale+=$devicescalearray[$ii];
			}
		}
		if($vo["projecttype"]!="低速车建设")
		{
			$content=$type.$chargedevice_temp1.$chargedevice_temp2."充电总功率".$devicescale."kW。";
		}
		else
		{
			$content=$type."充电设备".$chargedevice4array[0]."套，终端数（每套）".$chargedevice6array[0]."个。";
		}
		
		$vo["content"]=$content;
		
		$this->assign('vo', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('type',$_REQUEST[type]);
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
                if (false !== $model->where($condition)->setField("step6","0"))
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
	
	public function sendtask() {
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
				$info = M("Project")->where($condition)->find();
				M("Project")->where($condition)->setField("sendtask_time",time());
				$date=date('m-d H:i');
				$address=$info[title];
				$handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."派发任务单</br>------------------</br>"; 
				M("Project")->where($condition)->setField("handlehistory",$handlehistory);
				$this->success('操作成功！');
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
	
	public function sendtask_confirm() {
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
				$info = M("Project")->where($condition)->find();
				if($_REQUEST["flag"]=="sendtask_confirm1")
				{
					M("Project")->where($condition)->setField("sendtask_time_confirm1",time());
				}
				if($_REQUEST["flag"]=="sendtask_confirm2")
				{
					M("Project")->where($condition)->setField("sendtask_time_confirm2",time());
				}
				if($_REQUEST["flag"]=="sendtask_confirm3")
				{
					M("Project")->where($condition)->setField("sendtask_time_confirm3",time());
				}
				$info = M("Project")->where($condition)->find();
				if((!empty($info["sendtask_time_confirm1"]))&&(!empty($info["sendtask_time_confirm2"]))&&(!empty($info["sendtask_time_confirm3"])))
				{
					M("Project")->where($condition)->setField("design_status","施工中");
				}
				
				$date=date('m-d H:i');
				$address=$info[title];
				$handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."确认任务单</br>------------------</br>"; 
				M("Project")->where($condition)->setField("handlehistory",$handlehistory);
				$this->success('操作成功！');
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
		
		
		$vo['chargedevice1array']=explode(';',$vo['chargedevice1']);
		$vo['chargedevice2array']=explode(';',$vo['chargedevice2']);
		$vo['chargedevice3array']=explode(';',$vo['chargedevice3']);
		$vo['chargedevice4array']=explode(';',$vo['chargedevice4']);
		$vo['chargedevice5array']=explode(';',$vo['chargedevice5']);
		$vo['chargedevice6array']=explode(';',$vo['chargedevice6']);
		$vo['chargedevice7array']=explode(';',$vo['chargedevice7']);
		$vo['chargedevice8array']=explode(';',$vo['chargedevice8']);
		$vo['chargedevice9array']=explode(';',$vo['chargedevice9']);
		$vo['devicescalearray']=explode(';',$vo['devicescale']);
		
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
}
?>