<?php
class Secondcheck1Action extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map[step1]=1;
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
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
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
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
		if($_REQUEST['flag1']=="有")
		{
			$plmbidarray=M("Plmdiscuss")->field("plmid")->select();
			foreach($plmbidarray as $key => $val)
			{
				$ids.=$val["plmid"].",";
			}
			$map['id'] = array('in',$ids);
			$this->assign('flag1', $_REQUEST['flag1']);
		}
		if($_REQUEST['flag1']=="无")
		{
			$plmbidarray=M("Plmdiscuss")->field("plmid")->select();
			foreach($plmbidarray as $key => $val)
			{
				$ids.=$val["plmid"].",";
			}
			$map['id'] = array('not in',$ids);
			$this->assign('flag1', $_REQUEST['flag1']);
		}
		if($_REQUEST['flag2']=="主管指示未反馈")
		{
			$ids="";
			$mapforPlmdiscuss["content"]=array(array("eq",""),array("EXP","is null"),"or");
			$plmbidarray=M("Plmdiscuss")->where($mapforPlmdiscuss)->field("plmid")->select();
			foreach($plmbidarray as $key => $val)
			{
				$ids.=$val["plmid"].",";
			}
			$map['id'] = array('in',$ids);
			$this->assign('flag2', $_REQUEST['flag2']);
		}
		
		
		
		//$map[design_status]=array("in","储备,暂存,初步申报待审批,初步申报审批中,初步申报审批通过,初步申报审批退回,项目计划待审批,项目计划审批中,项目计划审批通过,项目计划审批退回,初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回");//完成验收
		
		
		
		
		if($_REQUEST['status'])
		{
			if(($_REQUEST['status']=="储备"))//储备不用了
			{
				$map[design_status]=array("in","储备,暂存,初步申报待审批,初步申报审批中,初步申报审批通过,初步申报审批退回,项目计划待审批,项目计划审批中,项目计划审批通过,项目计划审批退回,初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回");
			}
			else if(($_REQUEST['status']=="初申中"))
			{
				$map[design_status]=array("in","储备,暂存,初步申报待审批,初步申报审批中,初步申报审批通过,初步申报审批退回,项目计划待审批,项目计划审批中,项目计划审批退回");
			}
			else if(($_REQUEST['status']=="待施工"))
			{
				$map[design_status]=array("in","项目计划审批通过,初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工");
			}
			else if(($_REQUEST['status']=="施工中"))
			{
				$map[design_status]=array("in","施工中,已完成,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回");
				$map[activity]=array("exp","is null");
			}
			else if(($_REQUEST['status']=="已完成"))//施工已完成不用了
			{
				$map['design_status'] = array('in',"完成施工,已完成,竣工待验收,项目待验收,验收审核退回,完成验收");
			}
			else if(($_REQUEST['status']=="投入使用"))
			{
				$map['activity'] = array('eq',"投入使用");
			}
			else if(($_REQUEST['status']=="暂停中")||($_REQUEST['status']=="暂停"))
			{
				$map['design_status'] = array("in","暂停中");
			}
			else if(($_REQUEST['status']=="取消"))
			{
				$map['design_status'] = array("in","取消");
			}
			else if(($_REQUEST['status']=="滞后")||($_REQUEST['status']=="正在滞后"))
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
			else if(($_REQUEST['status']=="延期")||($_REQUEST['status']=="历史延期"))
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
			$this->assign("status",$_REQUEST['status']);
		}
		else
		{
			//$map['status'] = array('neq',"取消");
			if((false!==strstr($_SESSION["role"],"专责"))||($_SESSION[account]=="admin"))
			{
				$map[design_status]=array("not in","取消,暂停中");
			}
			else
			{
				$map[design_status]=array("not in","取消,暂停中,暂存");
			}
		}
		
		if($_REQUEST['approvestatus']=="待审核")
		{
			$map[design_status]=array("in","初步申报待审批,初步申报审批中,项目计划待审批,项目计划审批中,初步立项待审批");
			$this->assign('approvestatus', $_REQUEST['approvestatus']);
		}
		if($_REQUEST['approvestatus']=="待我审核")
		{
			
			$mapforSchedule["user"]=array("eq",$_SESSION["nickname"].$_SESSION["number"]);
			$mapforSchedule[status]=1;
			$schedulearray=M("Schedule")->where($mapforSchedule)->field("taskid")->order("taskid")->select();//->group("taskid")
			foreach($schedulearray as $key => $val)
			{
				$scheduleplmids.=$val['taskid'].",";
			}
		
			$map["id"]=array("in",$scheduleplmids);
			
			
			//dump($schedulearray);
			
			$map[design_status]=array("in","初步申报待审批,初步申报审批中,项目计划待审批,项目计划审批中,初步立项待审批,可研评审报告待审批,可研评审报告审批中");
			$this->assign('approvestatus', $_REQUEST['approvestatus']);
		}
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
			
			if($_REQUEST['approvestatus']=="待我审核")
			{
				$listRows = 1000;
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
			$titlerepeat["id"]=array("neq",$_REQUEST[id]);
			$titlerepeat["title"]=array("eq",$_REQUEST[title]);
			$ifrepeat=M("Project")->where($titlerepeat)->find();
			if(!empty($ifrepeat))
			{
				$this->error("项目名称已经存在！");	
			}
		}	
		if(empty($_REQUEST["id"]))
		{
			$model->step1=1;
			$model->xiaoshouuser=$_SESSION['loginUserName'];
			$model->department=$_SESSION['dept'];
			$model->user=$_SESSION['loginUserName'];
			$model->ctime=date("Y-m-d");
			$model->design_status="暂存";
			$model->create_time=time();
		}
		$model->last_time=time();
		$model->addressfull=$_REQUEST['province'].$_REQUEST['city'].$_REQUEST['area'].$_REQUEST['address'];
		
		foreach($_REQUEST["chargedevice1"] as $key => $val){if(!empty($val))$chargedevice1.=$val.";";}$model->chargedevice1=$chargedevice1;
		foreach($_REQUEST["chargedevice2"] as $key => $val){if(!empty($val))$chargedevice2.=$val.";";}$model->chargedevice2=$chargedevice2;
		foreach($_REQUEST["chargedevice3"] as $key => $val){if(!empty($val))$chargedevice3.=$val.";";}$model->chargedevice3=$chargedevice3;
		foreach($_REQUEST["chargedevice4"] as $key => $val){if(!empty($val))$chargedevice4.=$val.";";}$model->chargedevice4=$chargedevice4;
		foreach($_REQUEST["chargedevice5"] as $key => $val){if(!empty($val))$chargedevice5.=$val.";";}$model->chargedevice5=$chargedevice5;
		foreach($_REQUEST["chargedevice6"] as $key => $val){if(!empty($val))$chargedevice6.=$val.";";}$model->chargedevice6=$chargedevice6;
		foreach($_REQUEST["chargedevice7"] as $key => $val){if(!empty($val))$chargedevice7.=$val.";";}$model->chargedevice7=$chargedevice7;
		foreach($_REQUEST["chargedevice8"] as $key => $val){if(!empty($val))$chargedevice8.=$val.";";}$model->chargedevice8=$chargedevice8;
		foreach($_REQUEST["chargedevice9"] as $key => $val){if(!empty($val))$chargedevice9.=$val.";";}$model->chargedevice9=$chargedevice9;
		foreach($_REQUEST["devicescale"] as $key => $val){if(!empty($val))$devicescale.=$val.";";}$model->devicescale=$devicescale;
		
		
		if(empty($_REQUEST[city]))
		{
			//$model->city=$_REQUEST[province];
		}
		
		$date=date('Y-m-d H:i');
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
			$model->keeponrecord1=$newnameall;
			$model->keeponrecord1filename=$filenameall;
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
			$model->keeponrecord2=$newnameall;
			$model->keeponrecord2filename=$filenameall;
		}
		$oldinfo = M("Project")->where("id='" . $_REQUEST["id"] . "'")->find();
		//保存当前数据对象
		if(empty($_REQUEST[id]))
		{
			$model->handlehistory=$_SESSION['loginUserName']."于".$date."创建了项目立项</br>------------------</br>";
			$list = $model->add();
			$info = M("Project")->where("id='" . $list . "'")->find();
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
			
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			$address=$info[title];
			
			if(!empty($_REQUEST[highcheck]))
			{
				$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目信息（管）</br>------------------</br>";
			}
			else if(empty($_REQUEST[approve]))
			{
				//$model->design_status="初步立项待审批";
				//$model->create_time=time();
				$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目立项</br>------------------</br>"; 
			}
			else
			{
				$address=$info[title];
				$schedulemap[taskid]=$info[id];
				$schedulemap[status]=1;
				//$schedulemap[type]="Secondcheck";
				M("Schedule")->where($schedulemap)->setField("status",0);
				M("Schedule")->where($schedulemap)->setField("result",$_REQUEST[result]);
		
				if(($_REQUEST[result]=="同意"))/*同意*/
				{
					
					if(($info["invester"]=="省投资")||($info["invester"]=="合作投资"))
					{
						/*
						if($info["design_status"]=="初步申报待审批")
						{
							$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步申报审批中";
							$model->preplmapproveflag="0.1";
							$model->create_approve_time=time();
							
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步申报审核，请您进行初步申报二次审核。";
							$data['href'] ="index.php?s=Secondcheck/index";
							$data['taskid'] =$taskid;
							$data['type'] ="Secondcheck";
							$userschedule=$this->findleader($info['projecttype'],$info['city']);
							$data['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($data);
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
							
						}
						
						if($info["design_status"]=="初步申报审批中")
						{
							
							if($info["preplmapproveflag"]=="0.1")
							{
								$model->preplmapproveflag="0.5";
								$model->design_status="初步申报审批中";
								$model->handlehistory=$info['handlehistory']."初步申报二次审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
								
								$taskid=$info[id];
								$date=date('m-d H:i');
								$address=$info['title'];
								$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步申报审核，请您进行初步申报二次审核。";
								$data['href'] ="index.php?s=Secondcheck/index";
								$data['taskid'] =$taskid;
								$data['type'] ="Secondcheck";
								$userschedule=$this->findleader($info['projecttype'],$info['city']);
								$data['user']=$userschedule['nickname'].$userschedule['number'];
								$this->Addschedule($data);
								
								M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
							}
							if($info["preplmapproveflag"]=="0.5")
							{
								$model->preplmapproveflag="1";
								$model->design_status="初步申报审批通过";
								$model->handlehistory=$info['handlehistory']."初步申报三次审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
								
								M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
							}
							$model->create_approve_time1=time();
						}
						*/
						if($info["design_status"]=="初步立项待审批")
						{
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批中";
							$schedulesetting=M("Bjsz")->where("id=1")->find();
							if($schedulesetting["approver2"]=="0")
							{
								$userschedule=$this->findleader($info['projecttype'],$info['city']);
							}
							else
							{
								$userschedule=$this->findleaderbyroleid($schedulesetting["approver2"],$info['projecttype'],$info['city']);
							}
							$model->where("id=".$info["id"])->setField("currentapprover",$userschedule["nickname"]);
							
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步立项审核，请您进行初步立项二次审核。";
							$data['href'] ="index.php?s=Secondcheck/index";
							$data['taskid'] =$taskid;
							$data['type'] ="Secondcheck";
							$data['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($data);
						}
						if($info["design_status"]=="初步立项审批中")
						{
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批通过";
							$model->submit_approve_time=time();
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
					}
					else
					{
						/*
						if($info["design_status"]=="初步申报待审批")
						{
							$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步申报审批通过";
							$model->create_approve_time=time();
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
						*/
						if($info["design_status"]=="初步立项待审批")
						{
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批中";
							
							$schedulesetting=M("Bjsz")->where("id=1")->find();
							if($schedulesetting["approver2"]=="0")
							{
								$userschedule=$this->findleader($info['projecttype'],$info['city']);
							}
							else
							{
								$userschedule=$this->findleaderbyroleid($schedulesetting["approver2"],$info['projecttype'],$info['city']);
							}
							$model->where("id=".$info["id"])->setField("currentapprover",$userschedule["nickname"]);
							
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步立项审核，请您进行初步立项二次审核。";
							$data['href'] ="index.php?s=Secondcheck/index";
							$data['taskid'] =$taskid;
							$data['type'] ="Secondcheck";
							$data['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($data);
						}
						if($info["design_status"]=="初步立项审批中")
						{
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批通过";
							$model->submit_approve_time=time();
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
					}
				
					
					
					$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核，结果：同意。";
					$data['receiver']=$info['xiaoshouuser'].$this->findNumberByNameAndRole($info['xiaoshouuser']).",";
					$data['sender']="系统通知";
					$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核结果：同意。";
					$this->Sendmail($data);
				}
				else
				{	//拒绝流程
					if($info["design_status"]=="初步申报待审批")
					{
						if(($info["invester"]=="省投资")||($info["invester"]=="合作投资"))
						{
							$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步申报审批退回";
							$model->preplmapproveflag="0";
							$model->create_approve_time=time();
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
						else
						{
							$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="取消";
							$model->preplmapproveflag="0";
							$model->create_approve_time=time();
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
					}
					if($info["design_status"]=="初步申报审批中")
					{
						$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						$model->design_status="取消";
						$model->preplmapproveflag="0";
						$model->create_approve_time1=time();
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					if($info["design_status"]=="初步立项待审批")
					{
						$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						//$model->design_status="初步申报审批通过";
						//$model->design_status="项目计划审批通过";
						$model->design_status="初步立项审批退回";
						$model->submit_approve_time=time();
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					
					$model->approvestatus="";
					
					$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核，结果：拒绝。";
					$data['receiver']=$info['xiaoshouuser'].$this->findNumberByNameAndRole($info['xiaoshouuser']).",";
					$data['sender']="系统通知";
					$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核，结果：拒绝。";
					$this->Sendmail($data);
				}
			}
			
			
			$list = $model->save();
			$list = $_REQUEST["id"];
		}
		
		
		if($_REQUEST["id"])
		{
			$newinfo=M("Project")->where("id='" . $_REQUEST["id"] . "'")->find();
			foreach($newinfo as $key => $val)
			{
				if(($key!="last_time")&&($key!="handlehistory"))
				{
					if($val!=$oldinfo[$key])
					{
						$plmeditlog["plmid"]=$newinfo["id"];
						$plmeditlog["address"]=$newinfo["title"];
						$plmeditlog["title"]=$key;
						$plmeditlog["before"]=$oldinfo[$key];
						$plmeditlog["after"]=$newinfo[$key];
						$plmeditlog["user"]=$_SESSION["name"];
						$plmeditlog["create_time"]=time();
						$plmeditlog["ctime"]=date("Y-m-d H:i:s");
						M("Plmeditlog")->add($plmeditlog);
					}
				}
				
			}
		
		}
		
		
		
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('App/detail&check=1&id='.$list);
				return;
			}
			if($_REQUEST[jypg])
			{
				$this->redirect('Jypg1/index&moduletitle=可研编制');//&id=.$list
				return;
			}
			///////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
			//$this->redirect('index');
		} else {
			//失败提示
			$this->error('操作失败!');
		}
	}
	
	
	
	
	function piliangapproved() {
		
		$name = "Project";
		$model = D($name);
		$_REQUEST['suggestion']=$_REQUEST['inputValue'];
		
		
		$model->last_time=time();
		$date=date('Y-m-d H:i');
		
		$ids=$_REQUEST["id"];
		$mapforSchedule["taskid"]=array("in",$ids);
		$mapforSchedule["user"]=array("eq",$_SESSION["nickname"].$_SESSION["number"]);
		$mapforSchedule[status]=1;
		$schedulearray=M("Schedule")->where($mapforSchedule)->group("taskid")->field("taskid")->select();
	
		foreach($schedulearray as $key => $val)
		{
			$_REQUEST[id]=$val["taskid"];
			$info = M("Project")->where("id='" . $_REQUEST[id]. "'")->find();
			$address=$info[title];
			
			
			$address=$info[title];
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			M("Schedule")->where($schedulemap)->setField("status",0);
			M("Schedule")->where($schedulemap)->setField("result","同意");
		
			
			if(($info["invester"]=="省投资")||($info["invester"]=="合作投资"))
			{
				if($info["design_status"]=="初步申报待审批")
				{
					$datasave[handlehistory]=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$datasave[design_status]="初步申报审批中";
					$datasave[preplmapproveflag]="0.1";
					$datasave[create_approve_time]=time();
					
					$taskid=$info[id];
					$date=date('m-d H:i');
					$address=$info['title'];
					$dataschedule['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步申报审核（批量审批），请您进行初步申报二次审核。";
					$dataschedule['href'] ="index.php?s=Secondcheck/index";
					$dataschedule['taskid'] =$taskid;
					$dataschedule['type'] ="Secondcheck";
					$userschedule=$this->findleader($info['projecttype'],$info['city']);
					$dataschedule['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($dataschedule);
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
					
				}
				if($info["design_status"]=="初步申报审批中")
				{
					
					if($info["preplmapproveflag"]=="0.1")
					{
						$datasave[preplmapproveflag]="0.5";
						$datasave[design_status]="初步申报审批中";
						$datasave[handlehistory]=$info['handlehistory']."初步申报二次审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						
						$taskid=$info[id];
						$date=date('m-d H:i');
						$address=$info['title'];
						//$dataschedule['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步立项审核（批量审批），请您进行初步立项二次审核。";
						$dataschedule['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步申报二次审核（批量审批），请您进行初步申报三次审核。";
						$dataschedule['href'] ="index.php?s=Secondcheck/index";
						$dataschedule['taskid'] =$taskid;
						$dataschedule['type'] ="Secondcheck";
						$userschedule=$this->findleader($info['projecttype'],$info['city']);
						$dataschedule['user']=$userschedule['nickname'].$userschedule['number'];
						$this->Addschedule($dataschedule);
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
					}
					if($info["preplmapproveflag"]=="0.5")
					{
						$datasave[preplmapproveflag]="1";
						$datasave[design_status]="初步申报审批通过";
						$datasave[handlehistory]=$info['handlehistory']."初步申报三次审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					$model->create_approve_time1=time();
				}
				if($info["design_status"]=="初步立项待审批")
				{
					$datasave[handlehistory]=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$datasave[design_status]="初步立项审批通过";
					$datasave[submit_approve_time]=time();
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				
				
				
				
				
				if($info["design_status"]=="项目计划待审批")
				{
					$datasave[handlehistory]=$info['handlehistory']."关键节点时间审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$datasave[design_status]="项目计划审批中";
					$datasave[nodeset_approve_time]=time();
					$datasave[preplanapproveflag]="0.1";
					
					$taskid=$info[id];
					$date=date('m-d H:i');
					$address=$info['title'];
					$dataschedule['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成关键节点时间审核（批量审批），请您进行关键节点时间二次审核。";
					$dataschedule['href'] ="index.php?s=Secondcheck/index";
					$dataschedule['taskid'] =$taskid;
					$dataschedule['type'] ="Secondcheck";
					$userschedule=$this->findleader($info['projecttype'],$info['city']);
					$dataschedule['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($dataschedule);
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
					
				}
				if($info["design_status"]=="项目计划审批中")
				{
					if($info["preplanapproveflag"]=="0.1")
					{
						$datasave[preplanapproveflag]="0.5";
						$datasave[design_status]="项目计划审批中";
						$datasave[handlehistory]=$info['handlehistory']."关键节点时间二次审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						
						$taskid=$info[id];
						$date=date('m-d H:i');
						$address=$info['title'];
						$dataschedule['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成关键节点时间审核（批量审批），请您进行关键节点时间三次审核。";
						$dataschedule['href'] ="index.php?s=Secondcheck/index";
						$dataschedule['taskid'] =$taskid;
						$dataschedule['type'] ="Secondcheck";
						$userschedule=$this->findleader($info['projecttype'],$info['city']);
						$dataschedule['user']=$userschedule['nickname'].$userschedule['number'];
						$this->Addschedule($dataschedule);
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
					}
					if($info["preplanapproveflag"]=="0.5")
					{
						$datasave[preplanapproveflag]="1";
						$datasave[design_status]="项目计划审批通过";
						$datasave[handlehistory]=$info['handlehistory']."关键节点时间三次审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}

					
					$model->nodeset_approve_time1=time();
				}
				
				
				
				
				if($info["design_status"]=="可研评审报告待审批")
				{
					$datasave[design_status]="可研评审报告审批通过";
					$datasave[research_approve_time]=time();
					$datasave[handlehistory]=$info['handlehistory']."可研编制评审：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
			}
			else
			{
				if($info["design_status"]=="初步申报待审批")
				{
					$datasave[handlehistory]=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见（批量审批）：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$datasave[design_status]="初步申报审批通过";
					$datasave[create_approve_time]=time();
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				if($info["design_status"]=="初步立项待审批")
				{
					$datasave[handlehistory]=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见（批量审批）：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$datasave[design_status]="初步立项审批通过";
					$datasave[submit_approve_time]=time();
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				
				
				
				
				if($info["design_status"]=="项目计划待审批")
				{
					$datasave[handlehistory]=$info['handlehistory']."关键节点时间审核：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$datasave[design_status]="项目计划审批通过";
					$datasave[nodeset_approve_time]=time();
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				
				
				if(($info["design_status"]=="可研评审报告待审批")||($info["design_status"]=="可研评审报告审批中"))
				{
					$datasave[handlehistory]=$info['handlehistory']."可研评审报告审批：".$_SESSION['loginUserName']."</br>结果：同意（批量审批）"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					if(($info["invest6"]<=200))
					{
						if(($info["design_status"]=="可研评审报告审批中")&&($info["preresearchapproveflag"]=="0.1"))
						{
							$datasave[design_status]="可研评审报告审批通过";
							$datasave[research_approve_time]=time();
							$datasave[preresearchapproveflag]=1;
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
						else
						{
							$datasave[design_status]="可研评审报告审批中";
							$datasave[research_approve_time]=time();
							$datasave[preresearchapproveflag]=0.1;
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
							M("Project")->where("id=".$info["id"])->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
							
							
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$dataschedule['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告第一次审批（批量审批），请您进行可研评审报告审批。";
							$dataschedule['href'] ="index.php?s=Jypgcheck1/index";
							$dataschedule['taskid'] =$taskid;
							$dataschedule['type'] ="Jypgcheck1";
							$userschedule=$this->findleader($info['projecttype'],$info['city']);
							$dataschedule['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($dataschedule);
							
						}
					}
					else
					{
						if(($info["design_status"]=="可研评审报告审批中")&&($info["preresearchapproveflag"]=="0.5"))
						{
							$datasave[design_status]="可研评审报告审批通过";
							$datasave[research_approve_time1]=time();
							$datasave[preresearchapproveflag]=1;
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
						else
						{
							$datasave[design_status]="可研评审报告审批中";
							$datasave[research_approve_time]=time();
							
							$address=$info['title'];
							if(empty($info["preresearchapproveflag"]))
							{
								$datasave[preresearchapproveflag]=0.1;
								$dataschedule['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告审批（批量审批），请您进行可研评审报告审批。";
							}
							if(($info["preresearchapproveflag"]==0.1))
							{
								$datasave[preresearchapproveflag]=0.5;
								$dataschedule['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告第二次审批（批量审批），请您进行可研评审报告审批。";
							}
							
							$taskid=$info[id];
							$date=date('m-d H:i');
							$dataschedule['href'] ="index.php?s=Jypgcheck1/index";
							$dataschedule['taskid'] =$taskid;
							$dataschedule['type'] ="Jypgcheck1";
							$userschedule=$this->findleader($info['projecttype'],$info['city']);
							$dataschedule['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($dataschedule);
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->save($datasave);
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
						}
					}
				}
			}
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行批量审核，结果：同意。";
			$data['receiver']=$info['xiaoshouuser'].$this->findNumberByNameAndRole($info['xiaoshouuser']).",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行批量审核，结果：同意。";
			$this->Sendmail($data);
			
		}
		if (1) {
			if($_SESSION[app])
			{
				$this->success('提交成功！');
			}
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
		} else {
			//失败提示
			$this->error('操作失败!');
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
		
		
		if(empty($_REQUEST["id"]))
		{
			//$this->display(draftfirstnull);
			//return;
		}
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST["id"];
		$vo = $model->getById($id);
		
		
		if(empty($_REQUEST["id"]))
		{
			$this->assign('steponeflag', "1");
		}
		if(($vo["design_status"]=="暂存"))
		{
			$this->assign('steponeflag', "1");
		}
		if(($vo["design_status"]=="初步申报审批退回"))
		{
			$this->assign('steponeflag', "1");
		}
		if(($_REQUEST["highpower"]=="1"))
		{
			$this->assign('steponeflag', "0");
		}
		$this->assign('jypg', $_REQUEST["jypg"]);
		
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%GC".$thisyear."%");
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
		
		$vo['keeponrecord1']=explode(',',$vo['keeponrecord1']);
		$vo['keeponrecord1filename']=explode(',',$vo['keeponrecord1filename']);
		
		$vo['keeponrecord2']=explode(',',$vo['keeponrecord2']);
		$vo['keeponrecord2filename']=explode(',',$vo['keeponrecord2filename']);
		
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
		
		$plmdiscusses=M("Plmdiscuss")->where("plmid=".$vo['id'])->select();
		$this->assign('plmdiscusses', $plmdiscusses);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		$this->assign('highpower', $_REQUEST["highpower"]);
		
		$this->display(draftfirst);
	}
	
	function changeorder() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%GC".$thisyear."%");
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
		
		
		$plmdiscusses=M("Plmdiscuss")->where("plmid=".$vo['id'])->select();
		$this->assign('plmdiscusses', $plmdiscusses);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		
		$this->display();
		
	}
	
	
	
	function handle() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%GC".$thisyear."%");
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
		
		
		$plmdiscusses=M("Plmdiscuss")->where("plmid=".$vo['id'])->select();
		$this->assign('plmdiscusses', $plmdiscusses);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		
		
		
		
		$this->display();
		
	}
	/**
	* 求取从某日起经过一定天数后的日期,
	* 排除周六周日和节假日加上调休日
	* @param $start    int|string   开始日期
	* @param $offset   int   经过天数
	* @param $exception string|array 节假日
	* @param $allow   string|array    调休日
	* @return string
	*  examples:输入(2021-01-09,36,''),得到2021-03-09
	*/
	function testgetendday( $start='now', $offset=0, $exception='', $allow='' )
	{
		echo $this->getendday("2021-08-30",10);
	
	}
	function getendday( $start='now', $offset=0, $exception='', $allow='' )
	{
		date_default_timezone_set('prc');

		//先计算不排除周六周日及节假日的结果
		if (is_numeric($start)){
			$starttime = $start;
		}else{
			$starttime = strtotime($start);
		}

		$endtime = $starttime + $offset * 24 * 3600;
		$end = date('Y-m-d', $endtime);
		//然后计算周六周日引起的偏移

		$weekday = date('w', $starttime);//得到星期值：0-6
		if ($weekday == 0){
			//0是星期天
			$weekday = 7;
		}

		$remain = $offset % 7;
		$newoffset = 2 * ($offset - $remain) / 7;//每一周需重新计算两天

		if ($remain > 0) {//周余凑整
			$tmp = $weekday + $remain;
			if ($tmp >= 7) {
				$newoffset += 2;
			} else if ($tmp == 6) {
				$newoffset += 1;
			}

			//考虑当前为周六周日的情况
			if ($weekday == 6) {
				$newoffset -= 1;
			} else if ($weekday == 7) {
				$newoffset -= 2;
			}
		}

		//再计算节假日引起的偏移
		if (is_array($exception)) {//多个节假日
			foreach ($exception as $day) {
				$tmp_time = strtotime($day);
				if ($tmp_time > $starttime && $tmp_time <= $endtime) {//在范围(a,b]内
					$weekday_t = date('w', $tmp_time);
					if ($weekday_t <= 5 && $weekday_t != 0) {//防止节假日与周末重复
						$newoffset += 1;
					}
				}
			}
		}else {//单个节假日
			if (!empty($exception)) {
				$tmp_time = strtotime($exception);
				if ($tmp_time > $starttime && $tmp_time <= $endtime) {
					$weekday_t = date('w', $tmp_time);
					if ($weekday_t <= 5 && $weekday_t != 0) {
						$newoffset += 1;
					}
				}
			}

		}

		 //再计算调休日引起的偏移
		if (is_array($allow)) {//多个调休日

			foreach ($allow as $day) {
				$tmp_time = strtotime($day);

				if ($tmp_time > $starttime && $tmp_time <= $endtime) {//在范围(a,b]内
					$weekday_t =  date('w', $tmp_time);//得到星期值：0-6

					if ($weekday_t == 6 || $weekday_t == 0) {
						//若调休日是星期六、星期日
						$newoffset -= 1;
					}
				}
			}

		}else {//单个调休日
			if (!empty($allow)) {
				$tmp_time = strtotime($allow);
				if ($tmp_time > $starttime && $tmp_time <= $endtime) {
					$weekday_t = date('w', $tmp_time);

					if ($weekday_t == 6 || $weekday_t == 0) {
						//若调休日是星期六、星期日
						$newoffset -= 1;
					}
				}
			}

		}

		//根据偏移天数，递归做等价运算
		if($newoffset > 0){
			#echo "[{$start} -> {$offset}] = [{$end} -> {$newoffset}]"."<br />n";
			return $this->getendday($end,$newoffset,$exception,$allow);
		}else{
			return $end;
		}
	}

	function time_auto() {
		$plminfo=M("Project")->where("id=".htmlspecialchars($_REQUEST[id]))->find();
		$date=htmlspecialchars($_REQUEST["date"]);
		
		$bjsz=M("Bjsz")->find();
		if($plminfo["projecttype"]=="充电建设")
		{
			$time0=$bjsz["subtitle0"];
			$time1=$bjsz["subtitle1"];
			$time2=$bjsz["subtitle2"];
			$time3=$bjsz["subtitle3"];
			$time4=$bjsz["subtitle4"];
			$time5=$bjsz["subtitle5"];
			$time6=$bjsz["subtitle6"];
			$time6x=$bjsz["subtitle6x"];
			$time7=$bjsz["subtitle7"];
			$time8=$bjsz["subtitle8"];
			$time9=$bjsz["subtitle9"];
			$time9x=$bjsz["subtitle9x"];
		}
		if($plminfo["projecttype"]=="换电建设")
		{
			$time0=$bjsz["subtitle10"];
			$time1=$bjsz["subtitle11"];
			$time2=$bjsz["subtitle12"];
			$time3=$bjsz["subtitle13"];
			$time4=$bjsz["subtitle14"];
			$time5=$bjsz["subtitle15"];
			$time6=$bjsz["subtitle16"];
			$time6x=$bjsz["subtitle16x"];
			$time7=$bjsz["subtitle17"];
			$time8=$bjsz["subtitle18"];
			$time9=$bjsz["subtitle19"];
			$time9x=$bjsz["subtitle19x"];

		}
		if($plminfo["projecttype"]=="低速车建设")
		{
			$time0=$bjsz["subtitle20"];
			$time1=$bjsz["subtitle21"];
			$time2=$bjsz["subtitle22"];
			$time3=$bjsz["subtitle23"];
			$time4=$bjsz["subtitle24"];
			$time5=$bjsz["subtitle25"];
			$time6=$bjsz["subtitle26"];
			$time6x=$bjsz["subtitle26x"];
			$time7=$bjsz["subtitle27"];
			$time8=$bjsz["subtitle28"];
			$time9=$bjsz["subtitle29"];
			$time9x=$bjsz["subtitle29x"];
			
		}
		
		$date0=$date;
		//$date1=date("Y-m-d",strtotime($date0)+($time1)*24*60*60);
		$date1=$this->getendday($date0,$time1);
		//$date2=date("Y-m-d",strtotime($date1)+($time2)*24*60*60);
		$date2=$this->getendday($date1,$time2);
		//$date3=date("Y-m-d",strtotime($date2)+($time3)*24*60*60);
		$date3=$this->getendday($date2,$time3);
		//$date4=date("Y-m-d",strtotime($date3)+($time4)*24*60*60);
		$date4=$this->getendday($date3,$time4);
		
		/*
		if(($plminfo["invester"]!="省投资")&&($plminfo["invester"]!="合作投资")&&($plminfo["invest6"]>200))
		{
			//$date5=date("Y-m-d",strtotime($date4)+($time5)*24*60*60);
			$date5=$this->getendday($date4,$time5);
			//$date6=date("Y-m-d",strtotime($date5)+($time6)*24*60*60);
			$date6=$this->getendday($date5,$time6);
		}
		else
		{
			$date5="";
			//$date6=date("Y-m-d",strtotime($date4)+($time6)*24*60*60);
			$date6=$this->getendday($date4,$time6);
		}
		//$date6x=date("Y-m-d",strtotime($date6)+($time6x)*24*60*60);
		$date6x=$this->getendday($date6,$time6x);
		*/
		$date6x=$this->getendday($date4,$time6x);
		$date6=$this->getendday($date6x,$time6);
		
		
		//$date7=date("Y-m-d",strtotime($date6x)+($time7)*24*60*60);
		//$date7=$this->getendday($date6x,$time7);
		
		$date7=$this->getendday($date6,$time7);
		
		//$date8=date("Y-m-d",strtotime($date7)+($time8)*24*60*60);
		$date8=$this->getendday($date7,$time8);
		//$date9=date("Y-m-d",strtotime($date8)+($time9)*24*60*60);
		$date9=$this->getendday($date8,$time9);
		//$date9x=date("Y-m-d",strtotime($date9)+($time9x)*24*60*60);
		$date9x=$this->getendday($date9,$time9x);
		
		$datearry[0]=$date0;
		$datearry[1]=$date1;
		$datearry[2]=$date2;
		$datearry[3]=$date3;
		$datearry[4]=$date4;
		$datearry[5]=$date5;
		$datearry[6]=$date6;
		$datearry["6x"]=$date6x;
		$datearry[7]=$date7;
		$datearry[8]=$date8;
		$datearry[9]=$date9;
		$datearry["9x"]=$date9x;
		echo json_encode($datearry);
	}
	
	
	function handlesubmit() {
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		
		$date=date('Y-m-d H:i');
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		
		if(empty($_REQUEST[approve]))
		{
			
			$model->nodeset_user=$_SESSION['loginUserName'];
			$model->nodeset_time=time();
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置了项目关键节点时间</br>------------------</br>";
			//$model->design_status="项目计划待审批";
			/*
			if($info["design_status"]!="项目计划待审批")
			{
				$taskid=$info[id];
				$date=date('m-d H:i');
				$address=$info['title'];
				$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目设置了关键节点时间，请您进行关键节点时间审核。";
				$data['href'] ="index.php?s=Secondcheck/index";
				$data['taskid'] =$taskid;
				$data['type'] ="Secondcheck";
				$userschedule=$this->findleader($info['projecttype'],$info['city']);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
			*/
		}
		else
		{
			$address=$info[title];
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			//$schedulemap[type]="Secondcheck";
			M("Schedule")->where($schedulemap)->setField("status",0);
			M("Schedule")->where($schedulemap)->setField("result",$_REQUEST[result]);
	
			if(($_REQUEST[result]=="同意"))/*同意*/
			{
				if(($info["invester"]=="省投资")||($info["invester"]=="合作投资"))
				{
					if($info["design_status"]=="项目计划待审批")
					{
						$model->handlehistory=$info['handlehistory']."关键节点时间审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						$model->design_status="项目计划审批中";
						$model->nodeset_approve_time=time();
						$model->preplanapproveflag="0.1";
						
						$taskid=$info[id];
						$date=date('m-d H:i');
						$address=$info['title'];
						$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成关键节点时间审核，请您进行关键节点时间二次审核。";
						$data['href'] ="index.php?s=Secondcheck/index";
						$data['taskid'] =$taskid;
						$data['type'] ="Secondcheck";
						$userschedule=$this->findleader($info['projecttype'],$info['city']);
						$data['user']=$userschedule['nickname'].$userschedule['number'];
						$this->Addschedule($data);
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
						
					}
					if($info["design_status"]=="项目计划审批中")
					{
						if($info["preplanapproveflag"]=="0.1")
						{
							$model->preplanapproveflag="0.5";
							$model->design_status="项目计划审批中";
							$model->handlehistory=$info['handlehistory']."关键节点时间二次审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成关键节点时间审核，请您进行关键节点时间三次审核。";
							$data['href'] ="index.php?s=Secondcheck/index";
							$data['taskid'] =$taskid;
							$data['type'] ="Secondcheck";
							$userschedule=$this->findleader($info['projecttype'],$info['city']);
							$data['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($data);
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
						}
						if($info["preplanapproveflag"]=="0.5")
						{
							$model->preplanapproveflag="1";
							$model->design_status="项目计划审批通过";
							$model->handlehistory=$info['handlehistory']."关键节点时间三次审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
							
						
						$model->nodeset_approve_time1=time();
					}
					
				}
				else
				{
					if($info["design_status"]=="项目计划待审批")
					{
						$model->handlehistory=$info['handlehistory']."关键节点时间审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						$model->design_status="项目计划审批通过";
						$model->nodeset_approve_time=time();
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
				}
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行关键节点时间审核，结果：同意。";
				$data['receiver']=$info['nodeset_user'].$this->findNumberByNameAndRole($info['nodeset_user']).",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行关键节点时间审核，结果：同意。";
				$this->Sendmail($data);
			}
			else
			{	//拒绝流程
				if($info["design_status"]=="项目计划待审批")
				{
					$model->handlehistory=$info['handlehistory']."关键节点时间审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$model->design_status="项目计划审批退回";
					$model->preplmapproveflag="0";
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				if($info["design_status"]=="项目计划审批中")
				{
					$model->handlehistory=$info['handlehistory']."关键节点时间审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
					$model->design_status="项目计划审批退回";
					$model->preplmapproveflag="0";
					
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				
				$model->approvestatus="";
				$model->create_approve_time=time();
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行关键节点时间审核，结果：拒绝。";
				$data['receiver']=$info['nodeset_user'].$this->findNumberByNameAndRole($info['nodeset_user']).",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行关键节点时间审核，结果：拒绝。";
				$this->Sendmail($data);
			}
		}
	
		
		$model->save();
			
		
		if($_SESSION[app])
		{
			$this->redirect('App/detail&check=1&id='.$_REQUEST["id"]);
			return;
		}
		/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success('操作成功!');
	}
	
	
	function draftfirstdevice() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["time"]=array("like","%GC".$thisyear."%");
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
		
		
		
		$plmdiscusses=M("Plmdiscuss")->where("plmid=".$vo['id'])->select();
		$this->assign('plmdiscusses', $plmdiscusses);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		
		$this->assign('technology1', "修路王");
		$this->assign('technology2', "安全车");
		$this->assign('technology3', "清扫车");
		$this->assign('technology4', "除雪车");
		$this->assign('technology5', "TM系列");
		$this->assign('technology6', "EC系列");
		$this->assign('technology7', "其他设备");
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
                    
					
					$this->deleteunion("Plmattendance","plmid");
					$this->deleteunion("Plmcontract","plmNumber");
					$this->deleteunion("Plmbid","plmNumber");
					$this->deleteunion("Plmcooperate","plmNumber");
					$this->deleteunion("Plmcoordinate","plmNumber");
					$this->deleteunion("Plmdaily","plmid");
					$this->deleteunion("Plmdevice","plmid");
					$this->deleteunion("Plmdiscuss","plmid");
					$this->deleteunion("Plmdiscussreply","plmid");
					$this->deleteunion("Plmfilediaodu","plmNumber");
					$this->deleteunion("Plmschedule","plmid");
					$this->deleteunion("Plmscheduletemp","plmid");
					$this->deleteunion("Plmwarning","plmid");
					$this->deleteunion("Plmwarningapprove","plmid");

					
					$this->deleteunion("Plmworktype","plmid");
					$this->deleteunion("Plmworktypetemp","plmid");
					$this->deleteunion("Workassignment","plmid");
					$this->deleteunion("Schedule","taskid");
					
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
	
	public function cancel() {
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
                if (false !== $model->where($condition)->setField("design_status","取消"))
				{
					$info=$model->where($condition)->find();
					 $date=date('Y-m-d H:i');
					$datasave[handlehistory]=$info['handlehistory'].$_SESSION['loginUserName']."取消项目"."</br>取消原因：".$_REQUEST['inputValue']."</br>时间：".$date."</br>------------------</br>";
					$datasave[cancel_time]=time();
					$datasave[cancel_reason]=$_REQUEST["inputValue"];
					$datasave[id]=$info["id"];
					M("Project")->save($datasave);
					
                    $this->success('取消成功！');
                } else {
                    $this->error('取消失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
	public function suspend() {
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
                $date=date('Y-m-d H:i');
				
				$infoarray=$model->where($condition)->select();
				foreach($infoarray as $key => $info)
				{
					$datasave[design_status_old]=$info["design_status"];
					$datasave[design_status]="暂停中";
				
					$datasave[handlehistory]=$info['handlehistory'].$_SESSION['loginUserName']."暂停项目"."</br>暂停原因：".$_REQUEST['inputValue']."</br>时间：".$date."</br>------------------</br>";
					$datasave[suspend_time]=time();
					$datasave[suspend_reason]=$_REQUEST["inputValue"];
					$datasave[id]=$info["id"];
					M("Project")->save($datasave);
				}
				
				$this->success('取消成功！');
                
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
	public function approve() {
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
				$info = $model->where($condition)->find();
				/*
				if(($info["design_status"]=="暂存")||($info["design_status"]=="初步申报审批退回"))
				{
					$model->where($condition)->setField("design_status","初步申报待审批");
					$info=$model->where($condition)->find();
					$taskid=$info[id];
					$date=date('Y-m-d H:i');
					$address=$info['title'];
					
					$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》项目初步申报，请您进行初步申报审批。";
					$data['href'] ="index.php?s=Secondcheck/index";
					$data['taskid'] =$taskid;
					$data['type'] ="Secondcheck";
					$userschedule=$this->findleader($info['projecttype'],$info['city']);
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					$model->where($condition)->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
					$model->where($condition)->setField("handlehistory",$info['handlehistory'].$_SESSION['loginUserName']."于".$date."提交初步申报</br>------------------</br>");
					
                    $this->success('提交成功！');
                }
				*/
                if(1)//($info["design_status"]=="项目计划审批通过")||($info["design_status"]=="初步立项审批退回")
				{
					$model->where($condition)->setField("design_status","初步立项待审批");
					$info=$model->where($condition)->find();
					$taskid=$info[id];
					$date=date('Y-m-d H:i');
					$address=$info['title'];
					$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》项目立项，请您进行初步立项审批。";
					$data['href'] ="index.php?s=Secondcheck/index";
					$data['taskid'] =$taskid;
					$data['type'] ="Secondcheck";
					
					$schedulesetting=M("Bjsz")->where("id=1")->find();
					if($schedulesetting["approver"]=="0")
					{
						$userschedule=$this->findleader($info['projecttype'],$info['city']);
					}
					else
					{
						$userschedule=$this->findleaderbyroleid($schedulesetting["approver"],$info['projecttype'],$info['city']);
					}
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					
					$model->where($condition)->setField("currentapprover",$userschedule["nickname"]);
					$model->where($condition)->setField("handlehistory",$info['handlehistory'].$_SESSION['loginUserName']."于".$date."提交初步立项</br>------------------</br>");
					$model->where($condition)->setField("submit_time",time());
					
                    $this->success('提交成功！');
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
		
		
		
		/*
		if(!(($_SESSION[account]=="zhourong")||($_SESSION[account]=="chenxiaohua")||($_SESSION[account]=="taojianhua")||($_SESSION[account]=="chongfazhan")||($_SESSION[account]=="admin")))
		{
			if($vo[design_status]=="完成验收")
			{
				echo "</br>您无权查看此项目</br></br>";
				return;
			}
		}
		*/
		
		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		
		if($vo["projecttype"]=="充电建设")
		{
			$this->display(draftfirst);
		}
		if($vo["projecttype"]=="换电建设")
		{
			$this->display(draftfirst1);
		}
		if($vo["projecttype"]=="低速车建设")
		{
			$this->display(draftfirst2);
		}
	}
	
	public function toexcel()
	{
		$model=M("Project");
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
	
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['projecttype']=$volist[$i]['projecttype'];
			
			$data[$i]['quality']=$volist[$i]['quality'];
			$data[$i]['keeponrecord']=$volist[$i]['keeponrecord'];
			$data[$i]['invester']=$volist[$i]['invester'];
			$data[$i]['investpercent1']=$volist[$i]['investpercent1']."%";
			$data[$i]['investpercent2']=$volist[$i]['investpercent2']."%";
			
			
			$data[$i]['client']=$volist[$i]['client'];
			$data[$i]['clienttel']=$volist[$i]['clienttel'];
			
			$data[$i]['supplier']=$volist[$i]['supplier'];
			
			$data[$i]['owner']=$volist[$i]['owner'];
			$data[$i]['owner2']=$volist[$i]['owner2'];
			$data[$i]['area']=$volist[$i]['province'].$volist[$i]['city'].$volist[$i]['area'];
			$data[$i]['address']=$volist[$i]['address'];
			$data[$i]['number']=$volist[$i]['number'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['type']=$volist[$i]['type'];
			$data[$i]['taketype']=$volist[$i]['taketype'];
			
			$data[$i]['timebegin']=$volist[$i]['timebegin'];
			$data[$i]['timeend']=$volist[$i]['timeend'];
			
			$data[$i]['chargedevice1']=$volist[$i]['chargedevice1'];
			$data[$i]['chargedevice2']=$volist[$i]['chargedevice2'];
			$data[$i]['chargedevice3']=$volist[$i]['chargedevice3'];
			$data[$i]['chargedevice4']=$volist[$i]['chargedevice4'];
			$data[$i]['chargedevice5']=$volist[$i]['chargedevice5'];
			$data[$i]['chargedevice6']=$volist[$i]['chargedevice6'];
			$data[$i]['chargedevice7']=$volist[$i]['chargedevice7'];
			
			$data[$i]['chargedevice8']=$volist[$i]['chargedevice8'];
			$data[$i]['chargedevice9']=$volist[$i]['chargedevice9'];
			
		
			$data[$i]['energy1']=$volist[$i]['energy1'];
			$data[$i]['energy2']=$volist[$i]['energy2'];
			$data[$i]['energy3']=$volist[$i]['energy3'];
			
			$data[$i]['capital1']=$volist[$i]['capital1'];
			$data[$i]['capital2']=$volist[$i]['capital2'];
			$data[$i]['capital3']=$volist[$i]['capital3'];
			$data[$i]['capital4']=$volist[$i]['capital4'];
			$data[$i]['capital5']=$volist[$i]['capital5'];
			$data[$i]['capital6']=$volist[$i]['capital6'];
		
			$data[$i]['invest1']=$volist[$i]['invest1'];
			$data[$i]['invest2']=$volist[$i]['invest2'];
			$data[$i]['invest3']=$volist[$i]['invest3'];
			$data[$i]['invest41']=$volist[$i]['invest41'];
			$data[$i]['invest42']=$volist[$i]['invest42'];
			$data[$i]['invest43']=$volist[$i]['invest43'];
			$data[$i]['invest4']=$volist[$i]['invest4'];
			$data[$i]['invest51']=$volist[$i]['invest51'];
			$data[$i]['invest52']=$volist[$i]['invest52'];
			$data[$i]['invest53']=$volist[$i]['invest53'];
			$data[$i]['invest5']=$volist[$i]['invest5'];
			$data[$i]['invest6']=$volist[$i]['invest6'];

			$data[$i]['cost1']=$volist[$i]['cost1'];
			$data[$i]['cost2']=$volist[$i]['cost2'];
			$data[$i]['cost3']=$volist[$i]['cost3'];
			$data[$i]['cost4']=$volist[$i]['cost4'];
			$data[$i]['cost5']=$volist[$i]['cost5'];
			$data[$i]['cost6']=$volist[$i]['cost6'];
	
			$data[$i]['content']=$volist[$i]['content'];
			$data[$i]['design_status']=$volist[$i]['design_status'];
			
			$data[$i]['ctime']= $volist[$i]['ctime'];
			$data[$i]['create_time']= date('Y-m-d H:i',$volist[$i]['create_time']);
		}
		
		$file="项目列表";
		$title="项目列表";
		$subtitle='项目列表';
		
		$th_array=array('项目类型','项目属性','是否需要备案','投资方式','省投资比例','自投资比例','服务对象','负责人及联系方式','供应商','省公司','地市公司','区/县','地址','项目编号','项目名称','建设类型','场站分类','开始时间','结束时间','变压器容量（KVA）','变压器数量（台）','充电设施类型','数量（套）','单套功率（KW）','终端数（每套）（个）','单枪功率（KW）','电池数量（块）','电池单价（元）','车棚类型','储能功率（KW）','储能容量（kWh）','单车日均充电量（kWh）','服务车量数（辆）','年服务时间（天）','充电量合计（kWh）','服务费标准（元/kWh）','充电收入（万元）','基本预备费','安装工程费','建筑工程费','设备购置费-配电设备费','设备购置费-充电设备费','设备购置费-其他费用','设备购置费-费用总计','其他费用-监理费用','其他费用-设计费用','其他费用-其他费用','其他费用-费用总计','项目总投资（万元）','场地租金（购置）（万元）','外线费用（万元）','线损成本（万元）','整站运维成本（万元）','其他成本（万元）','总成本（万元）','建设内容','状态','立项时间','更新时间');
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_second.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		
		$objActSheet->setCellValue ( 'A1', $title );
		$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		$objActSheet->setCellValue ( 'F2', $subtitle);
		
		if($array_th==null)
		{
			$array_th=array_keys($data[0]);
		}
	
		foreach($array_th as $key=>$value)
		{
			$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
		}
		
		$baseRow = 5; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
		foreach ( $data as $r => $dataRow ) 
	    {
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value)
			{		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AQ'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
			}		 
		}
  
		//$filename = $file;
		$filename = $excelname."_".time();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );

	}
	
	
	public function toexcel1()
	{
		$model=M("Project");
		$map["design_status"]=$_REQUEST["design_status"];
	
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['oldxuhao']=$i+1;
			$data[$i]['xuhao']=$i+1;
			$data[$i]['city']=$volist[$i]['city'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['taketype']=$volist[$i]['taketype'];
			
		
			$data[$i]['content']=$volist[$i]['content'];
			$data[$i]['elecscale']=$volist[$i]['elecscale'];
			
			$data[$i]['devicescale']=$volist[$i]['devicescale'];
			$data[$i]['researchmoney']=$volist[$i]['researchmoney'];
			$data[$i]['invest']=$volist[$i]['invest'];
			
			$data[$i]['preinvest']=$volist[$i]['preinvest'];
			$data[$i]['intention']=$volist[$i]['intention'];
			$data[$i]['researchmoneyverify']=$volist[$i]['researchmoneyverify'];
			$data[$i]['contractverify']=$volist[$i]['contractverify'];
			$data[$i]['intime']=$volist[$i]['intime'];
			$data[$i]['mainfinishtime']=$volist[$i]['mainfinishtime'];
			$data[$i]['elecfinishtime']=$volist[$i]['elecfinishtime'];
			$data[$i]['progress']=$volist[$i]['progress'];
			$data[$i]['status']=$volist[$i]['status'];
			
			$data[$number]['0']="总计";
			$data[$number]['1']="总计";
			$data[$number]['2']="";
			$data[$number]['3']="";
			$data[$number]['4']="";
			$data[$number]['5']="";
			$data[$number]['6']+=$data[$i]['elecscale'];
			$data[$number]['7']+=$data[$i]['devicescale'];
			$data[$number]['8']+=$data[$i]['researchmoney'];
			$data[$number]['9']+=$data[$i]['invest'];
			$data[$number]['10']+=$data[$i]['preinvest'];
		}
	
		
		
		
		$file=filter_var(htmlspecialchars($_REQUEST["design_status"]), FILTER_CALLBACK, array("options"=>"convertSpace"))."项目列表";
		$title=$_REQUEST["design_status"]."项目列表";
		$subtitle=$_REQUEST["design_status"].'项目列表';
		
		$th_array=array();
		$this->createExel1($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel1($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_second1.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		
		//$objActSheet->setCellValue ( 'A1', $title );
		//$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		//$objActSheet->setCellValue ( 'F2', $subtitle);
		
		if($array_th==null)
		{
			//$array_th=array_keys($data[0]);
		}
	
		foreach($array_th as $key=>$value)
		{
			//$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
		}
		
		$baseRow = 3; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
		foreach ( $data as $r => $dataRow ) 
	    {
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value)
			{		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
				/*
				$styleArray = array(  
					'borders' => array(  
						'allborders' => array(  
							//'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的  
							'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
							'color' => array('argb' => $color),  
						),  
					),  
				);  
				$objPHPExcel->getActiveSheet()->getStyle('A2:AA2')->applyFromArray($styleArray);
				*/
				$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AB'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
		
			}		 
		}
  
		//$filename = $file;
		$filename = $excelname."_".time();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );

	}
	
	public function toexcel2()
	{
		$model=M("Project");
		$map["status"]="成交";
		
		if($_SESSION[account]!="戴合理")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['xuhao']=$i+1;
			$data[$i]['charge']=$volist[$i]['charge'];
			$data[$i]['director']=$volist[$i]['director'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['technology']=$volist[$i]['technology'];
			
			
			$data[$i]['quantities11']=$volist[$i]['quantities1']+$volist[$i]['quantities2'];
			if(substr_count($volist[$i]['technology'],',')>=2)
			{
				$data[$i]['hotprice11']="整形".$volist[$i]['hotprice1']."/复拌".$volist[$i]['hotprice2'];
			}
			else
			{
				$data[$i]['hotprice11']=$volist[$i]['hotprice1'].$volist[$i]['hotprice2'];
			}
			$data[$i]['material11']=$volist[$i]['material1'].$volist[$i]['material2'];
			$data[$i]['estimate_total11']=$volist[$i]['estimate_total1']+$volist[$i]['estimate_total2'];
			$data[$i]['estimate_total33']=$volist[$i]['estimate_total3']+$volist[$i]['estimate_total4']+$volist[$i]['estimate_total5']+$volist[$i]['estimate_total6']+$volist[$i]['estimate_total7']+$volist[$i]['other_estimate_total'];
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			
			$mapforPlmcontract[plmNumber]=$volist[$i]["id"];
			$plminfo=M("Plmcontract")->where($mapforPlmcontract)->find();
			
			$data[$i]['quantities11_1']=$plminfo['quantities1']+$plminfo['quantities2'];
			$data[$i]['hotprice11_1']="整形".$plminfo['hotprice1']."/复拌".$plminfo['hotprice2'];
			$data[$i]['material11_1']=$plminfo['material1'].$plminfo['material2'];
			$data[$i]['estimate_total11_1']=$plminfo['estimate_total1']+$plminfo['estimate_total2'];
			$data[$i]['estimate_total33_1']=$plminfo['estimate_total3']+$plminfo['estimate_total4']+$plminfo['estimate_total5']+$plminfo['estimate_total6']+$volist[$i]['estimate_total7']+$plminfo['other_estimate_total'];
			$data[$i]['estimate_total_1']=$plminfo['estimate_total'];
			
			
			
			$mapforPlmworktype[plmid]=$volist[$i]['id'];
			$worktypes=M("Plmworktype")->where($mapforPlmworktype)->group("pworktype")->order("id asc")->select();
			foreach($worktypes as $key=>$value ){
				$mapforPlmworktype[pworktype]=$value['pworktype'];
				$worktypes[$key]["subworktypes"]=M('Plmworktype')->where($mapforPlmworktype)->select();
			}
			
			
			
			$mapforplmoutputdaily[plmid]=$volist[$i]['id'];
			$mapforplmoutputdaily[pworktype]=array("like","%热再生%");
			$plmoutputdaily=M("plmoutputdaily")->where($mapforplmoutputdaily)->select();
			foreach($plmoutputdaily as $key=>$value ){
				$plmoutputdailydata[$value["date"]][$value["pworktype"]][$value["worktype"]]=$value["value"];
			}
			
			$plmoutputdailyvalue=0;
			$plmoutputdailytotal=0;
			foreach($worktypes as $key=>$value ){
				foreach($worktypes[$key]["subworktypes"] as $key1=>$value1)
				{
					foreach($plmoutputdaily as $key2=>$value2 ){
						if(($value2["pworktype"]==$value1["pworktype"])&&($value2["worktype"]==$value1["title"]))
						{
							$plmoutputdailyvalue+=$plmoutputdailydata[$value2["date"]][$value2["pworktype"]][$value2["worktype"]];
							$plmoutputdailytotal+=$value1["price"]*$plmoutputdailydata[$value2["date"]][$value2["pworktype"]][$value2["worktype"]];
						}
					}
				}
			}
		
			$data[$i]['quantities11_2']=$plmoutputdailyvalue;
			$data[$i]['hotprice11_2']="整形".$plminfo['hotprice1']."/复拌".$plminfo['hotprice2'];
			$data[$i]['material11_2']=$plminfo['material1'].$plminfo['material2'];
			//$data[$i]['estimate_total11_2']=round($plmoutputdailyvalue*$data[$i]['hotprice11_2']/10000,2);
			$data[$i]['estimate_total11_2']=round($plmoutputdailytotal/10000,2);
			
			$mapforplmoutputdaily[plmid]=$volist[$i]['id'];
			$mapforplmoutputdaily[worktype]=array("notlike","%热再生%");
			$plmoutputdaily=M("plmoutputdaily")->where($mapforplmoutputdaily)->select();
			foreach($plmoutputdaily as $key=>$value ){
				$plmoutputdailydata[$value["date"]][$value["pworktype"]][$value["worktype"]]=$value["value"];
			}
			
			$plmoutputdailyvalue1=0;
			$plmoutputdailytotal1=0;
			foreach($worktypes as $key=>$value ){
				foreach($worktypes[$key]["subworktypes"] as $key1=>$value1)
				{
					foreach($plmoutputdaily as $key2=>$value2 ){
						if(($value2["pworktype"]==$value1["pworktype"])&&($value2["worktype"]==$value1["title"]))
						{
							$plmoutputdailyvalue1+=$plmoutputdailydata[$value2["date"]][$value2["pworktype"]][$value2["worktype"]];
							$plmoutputdailytotal1+=$value1["price"]*$plmoutputdailydata[$value2["date"]][$value2["pworktype"]][$value2["worktype"]];
						}
					}
				}
			}
			
			$data[$i]['estimate_total33_2']=round($plmoutputdailytotal1/10000,2);
			$data[$i]['estimate_total_2']=$data[$i]['estimate_total11_2']+$data[$i]['estimate_total33_2'];
			/*
			$data[$i]['quantities1']=$plminfo['quantities1'];
			$data[$i]['hotprice1']=$plminfo['hotprice1'];
			$data[$i]['material1']=$plminfo['material1'];
			$data[$i]['entrancefee1']=$plminfo['entrancefee1'];
			$data[$i]['estimate_total1']=$plminfo['estimate_total1'];
			
			$data[$i]['quantities2']=$plminfo['quantities2'];
			$data[$i]['hotprice2']=$plminfo['hotprice2'];
			$data[$i]['material2']=$plminfo['material2'];
			$data[$i]['entrancefee2']=$plminfo['entrancefee2'];
			$data[$i]['estimate_total2']=$plminfo['estimate_total2'];
			
			$data[$i]['quantities3']=$plminfo['quantities3'];
			$data[$i]['hotprice3']=$plminfo['hotprice3'];
			$data[$i]['material3']=$plminfo['material3'];
			$data[$i]['entrancefee3']=$plminfo['entrancefee3'];
			$data[$i]['estimate_total3']=$plminfo['estimate_total3'];
			
			$data[$i]['quantities4']=$plminfo['quantities4'];
			$data[$i]['hotprice4']=$plminfo['hotprice4'];
			$data[$i]['material4']=$plminfo['material4'];
			$data[$i]['entrancefee4']=$plminfo['entrancefee4'];
			$data[$i]['estimate_total4']=$plminfo['estimate_total4'];
			
			$data[$i]['quantities5']=$plminfo['quantities5'];
			$data[$i]['hotprice5']=$plminfo['hotprice5'];
			$data[$i]['material5']=$plminfo['material5'];
			$data[$i]['entrancefee5']=$plminfo['entrancefee5'];
			$data[$i]['estimate_total5']=$plminfo['estimate_total5'];
			
			$data[$i]['quantities6']=$plminfo['quantities6'];
			$data[$i]['hotprice6']=$plminfo['hotprice6'];
			$data[$i]['material6']=$plminfo['material6'];
			$data[$i]['entrancefee6']=$plminfo['entrancefee6'];
			$data[$i]['estimate_total6']=$plminfo['estimate_total6'];
			
			
			$data[$i]['para14']=$plminfo['para14'];
			$data[$i]['para15']=$plminfo['para15'];
			
			*/
			
			
			
			$data[$i]['estimate_signtime']=$volist[$i]['estimate_signtime'];
			$data[$i]['status']=$volist[$i]['status'];
			$data[$i]['qualifications']=$volist[$i]['qualifications'];
			$data[$i]['type']=$volist[$i]['type'];
			$data[$i]['remark']=$volist[$i]['remark'];
			
			
			
			
			$data[$number]['1']="总计";
			$data[$number]['2']="";
			$data[$number]['3']="";
			$data[$number]['4']="";
			$data[$number]['5']="";
			$data[$number]['quantities11']+=$data[$i]['quantities11'];
			$data[$number]['6']="";
			$data[$number]['7']="";
			$data[$number]['estimate_total11']+=$data[$i]['estimate_total11'];
			$data[$number]['estimate_total33']+=$data[$i]['estimate_total33'];
			$data[$number]['estimate_total']+=$data[$i]['estimate_total'];
			
			$data[$number]['quantities11_1']+=$data[$i]['quantities11_1'];
			$data[$number]['8']="";
			$data[$number]['9']="";
			$data[$number]['estimate_total11_1']+=$data[$i]['estimate_total11_1'];
			$data[$number]['estimate_total33_1']+=$data[$i]['estimate_total33_1'];
			$data[$number]['estimate_total_1']+=$data[$i]['estimate_total_1'];
			
			$data[$number]['quantities11_2']+=$data[$i]['quantities11_2'];
			$data[$number]['10']="";
			$data[$number]['11']="";
			$data[$number]['estimate_total11_2']+=$data[$i]['estimate_total11_2'];
			$data[$number]['estimate_total33_2']+=$data[$i]['estimate_total33_2'];
			$data[$number]['estimate_total_2']+=$data[$i]['estimate_total_2'];
			
		}
		$file="成交项目列表";
		$title="成交项目列表";
		$subtitle='成交项目列表';
		
		$th_array=array();
		$this->createExel2($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel2($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_second2.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		
		//$objActSheet->setCellValue ( 'A1', $title );
		//$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		//$objActSheet->setCellValue ( 'F2', $subtitle);
		
		if($array_th==null)
		{
			//$array_th=array_keys($data[0]);
		}
	
		foreach($array_th as $key=>$value)
		{
			//$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
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
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
			}
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':AB'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
		}
  
		//$filename = $file;
		$filename = $excelname."_".time();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );

	}
	
	public function toexcel3()
	{
		$model=M("Project");
		$map["status"]="取消";
		
		if($_SESSION[account]!="戴合理")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['xuhao']=$i+1;
			$data[$i]['charge']=$volist[$i]['charge'];
			$data[$i]['director']=$volist[$i]['director'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['technology']=$volist[$i]['technology'];
			
			$data[$i]['quantities11']=$volist[$i]['quantities1']+$volist[$i]['quantities2'];
			if(substr_count($volist[$i]['technology'],',')>=2)
			{
				$data[$i]['hotprice11']="整形".$volist[$i]['hotprice1']."/复拌".$volist[$i]['hotprice2'];
			}
			else
			{
				$data[$i]['hotprice11']=$volist[$i]['hotprice1'].$volist[$i]['hotprice2'];
			}
			$data[$i]['material11']=$volist[$i]['material1'].$volist[$i]['material2'];
			$data[$i]['estimate_total11']=$volist[$i]['estimate_total1']+$volist[$i]['estimate_total2'];
			$data[$i]['estimate_total33']=$volist[$i]['estimate_total3']+$volist[$i]['estimate_total4']+$volist[$i]['estimate_total5']+$volist[$i]['estimate_total6']+$volist[$i]['estimate_total7']+$volist[$i]['other_estimate_total'];
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			
			/*
			$data[$i]['quantities1']=$volist[$i]['quantities1'];
			$data[$i]['hotprice1']=$volist[$i]['hotprice1'];
			$data[$i]['material1']=$volist[$i]['material1'];
			$data[$i]['entrancefee1']=$volist[$i]['entrancefee1'];
			$data[$i]['estimate_total1']=$volist[$i]['estimate_total1'];
			
			$data[$i]['quantities2']=$volist[$i]['quantities2'];
			$data[$i]['hotprice2']=$volist[$i]['hotprice2'];
			$data[$i]['material2']=$volist[$i]['material2'];
			$data[$i]['entrancefee2']=$volist[$i]['entrancefee2'];
			$data[$i]['estimate_total2']=$volist[$i]['estimate_total2'];
			
			$data[$i]['quantities3']=$volist[$i]['quantities3'];
			$data[$i]['hotprice3']=$volist[$i]['hotprice3'];
			$data[$i]['material3']=$volist[$i]['material3'];
			$data[$i]['entrancefee3']=$volist[$i]['entrancefee3'];
			$data[$i]['estimate_total3']=$volist[$i]['estimate_total3'];
			
			$data[$i]['quantities4']=$volist[$i]['quantities4'];
			$data[$i]['hotprice4']=$volist[$i]['hotprice4'];
			$data[$i]['material4']=$volist[$i]['material4'];
			$data[$i]['entrancefee4']=$volist[$i]['entrancefee4'];
			$data[$i]['estimate_total4']=$volist[$i]['estimate_total4'];
			
			$data[$i]['quantities5']=$volist[$i]['quantities5'];
			$data[$i]['hotprice5']=$volist[$i]['hotprice5'];
			$data[$i]['material5']=$volist[$i]['material5'];
			$data[$i]['entrancefee5']=$volist[$i]['entrancefee5'];
			$data[$i]['estimate_total5']=$volist[$i]['estimate_total5'];
			
			$data[$i]['quantities6']=$volist[$i]['quantities6'];
			$data[$i]['hotprice6']=$volist[$i]['hotprice6'];
			$data[$i]['material6']=$volist[$i]['material6'];
			$data[$i]['entrancefee6']=$volist[$i]['entrancefee6'];
			$data[$i]['estimate_total6']=$volist[$i]['estimate_total6'];
			
			$data[$i]['other_estimate_total']=$volist[$i]['other_estimate_total'];
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			*/
			$data[$i]['type']=$volist[$i]['type'];
			$data[$i]['cancel_time']=$volist[$i]['cancel_time'];
			$data[$i]['cancel_reason']=$volist[$i]['cancel_reason'];
			
			
			$data[$number]['1']="总计";
			$data[$number]['2']="";
			$data[$number]['3']="";
			$data[$number]['4']="";
			$data[$number]['5']="";
			$data[$number]['quantities11']+=$data[$i]['quantities11'];
			$data[$number]['6']="";
			$data[$number]['7']="";
			$data[$number]['estimate_total11']+=$data[$i]['estimate_total11'];
			$data[$number]['estimate_total33']+=$data[$i]['estimate_total33'];
			$data[$number]['estimate_total']+=$data[$i]['estimate_total'];
		}
		$file="取消项目列表";
		$title="取消项目列表";
		$subtitle='取消项目列表';
		
		$th_array=array();
		$this->createExel3($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel3($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_second3.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		
		//$objActSheet->setCellValue ( 'A1', $title );
		//$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		//$objActSheet->setCellValue ( 'F2', $subtitle);
		
		if($array_th==null)
		{
			//$array_th=array_keys($data[0]);
		}
	
		foreach($array_th as $key=>$value)
		{
			//$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
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
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
			}
			$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':N'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
		}
  
		//$filename = $file;
		$filename = $excelname."_".time();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );

	}
	public function revise()
	{
		$mapforplmedit["plm"]="黄山G330";
		M("Plmattendance")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmattendancedevice")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmdaily")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmfile")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmfilediaodu")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmmaterialorder")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmmaterials")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmorder2")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmorder2paytime")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		M("Plmplan")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
		
		//$mapforplmedit1["title"]="肥西G330热再生工程";
		//M("Plmbid")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
		//M("Plmcontract")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
		//M("Plmoffer")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
		//$mapforplmedit2["address"]="肥西G330热再生工程";
		//M("Plmdiscuss")->where($mapforplmedit2)->setField("address",$_REQUEST["title"]);
		
		
		
		
		
		M("Plmattendance")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmattendancedevice")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmdaily")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmfile")->where($mapforplmedit)->setField("plmNumber",$_REQUEST["plmid"]);
		M("Plmfilediaodu")->where($mapforplmedit)->setField("plmNumber",$_REQUEST["plmid"]);
		M("Plmmaterialorder")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmmaterials")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmorder2")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmorder2paytime")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		M("Plmplan")->where($mapforplmedit)->setField("plmid",$_REQUEST["plmid"]);
		
		//$mapforplmedit1["title"]="肥西G330热再生工程";
		//M("Plmbid")->where($mapforplmedit1)->setField("plmid",$_REQUEST["plmid"]);
		//M("Plmcontract")->where($mapforplmedit1)->setField("plmNumber",$_REQUEST["plmid"]);
		//M("Plmoffer")->where($mapforplmedit1)->setField("title",$_REQUEST["plmid"]);
		
		//$mapforplmedit2["address"]="肥西G330热再生工程";
		//M("Plmdiscuss")->where($mapforplmedit2)->setField("address",$_REQUEST["title"]);
	}
	public function findposition() 
	{	$lat=json_encode($_REQUEST[lat]);
	    $lng=json_encode($_REQUEST[lng]);
		$this->assign('lat', $lat);
		$this->assign('lng', $lng);
		$this->display();
	}
	
	
	
	
	public function getexcel()
	{
		if(empty($_FILES["file"]["name"]))
		{
			$this->error("请上传文件！");
		}
		$file_name = explode(".",$_FILES["file"]["name"]);
		if(($_FILES["file"]["type"] == "application/vnd.ms-excel")||($_FILES["file"]["type"] == "application/octet-stream")||($_FILES["file"]["type"] == "application/kset"))
		{												
			header("Content-type: text/html; charset=utf-8");
			error_reporting(E_ALL ^ E_NOTICE);
			$Import_TmpFile = $_FILES['file']['tmp_name'];
			Vendor('Excelload.reader');  //导入thinkphp 中第三方插件库
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('UTF-8');
			$data->read($Import_TmpFile);
			$array =array();
			for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
			{
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
				{
					$array[$i][$j] = $data->sheets[0]['cells'][$i][$j];
				}
			}
			$num=count($array);
			$number=$num;
			$time=time();
			
			/*
			for($k=2;$k<=$number;$k++)
			{
				if($array[$k]['1']=="")
				{
					continue;
				}
				$birthday=$array[$k]['3'];
				$idcard=$array[$k]['7'];
				$birthday1 = substr($idcard, 6, 4)."-".substr($idcard, 10, 2)."-".substr($idcard, 12, 2);
				if($birthday!=$birthday1)
				{
					$this->error($array[$k]['2'].'的身份证号与出生日期不符');
				}
			}
			*/
			
			$thisyear=date("Y");
			$mapfororder["number"]=array("like","%GC".$thisyear."%");
			$mapfororder["projecttype"]=array("like","%%");
			$todaycount=M("Project")->where($mapfororder)->max("number");
			$thisorder=(str_replace("GC","",$todaycount)+1);
			if(empty($todaycount))
			{
				$thisorder=date("Y")."0001";
			}
			
		
			for($k=4;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
					$kkflag=1;
					$mapforProject["projecttype"]=$array[$k][$kkflag++];//1
					$mapforProject["quality"]=$array[$k][$kkflag++];
					$mapforProject["keeponrecord"]=$array[$k][$kkflag++];
					$mapforProject["invester"]=$array[$k][$kkflag++];
					$mapforProject["investpercent1"]=$array[$k][$kkflag++];
					$mapforProject["investpercent2"]=$array[$k][$kkflag++];
					
					$mapforProject["name"]=$array[$k][$kkflag];
					$mapforProject["xiaoshouuser"]=$array[$k][$kkflag];
					$mapforProject["user"]=$array[$k][$kkflag];
					
					$mapforUser["nickname"]=$array[$k][$kkflag++];
					$deptid=M("User")->where($mapforUser)->getField("department");
					$mapforProject["department"]=M("Dept")->where("id=".$deptid)->getField("name");
					
					$mapforProject["client"]=$array[$k][$kkflag++];
					$mapforProject["clienttel"]=$array[$k][$kkflag++];
					
					//$mapforProject["supplier"]=$array[$k][$kkflag++];
					
					$mapforProject["owner"]=$array[$k][$kkflag++];
					$mapforProject["owner2"]=$array[$k][$kkflag++];
					$mapforProject["province"]=$array[$k][$kkflag++];
					$mapforProject["city"]=$array[$k][$kkflag++];
					$mapforProject["area"]=$array[$k][$kkflag++];
					$mapforProject["address"]=$array[$k][$kkflag++];
					//$mapforProject["number"]=$array[$k][$kkflag++];
					$kkflag++;
					$mapforProject["number"]="GC".$thisorder;
					$thisorder++;
					$mapforProject["title"]=$array[$k][$kkflag++];
					$mapforProject["type"]=$array[$k][$kkflag++];
					$mapforProject["taketype"]=$array[$k][$kkflag++];
					$mapforProject["taketype_other"]=$array[$k][$kkflag++];
					$mapforProject["timebegin"]=$array[$k][$kkflag++];
					$mapforProject["timeend"]=$array[$k][$kkflag++];
					$mapforProject["chargedevice1"]=$array[$k][$kkflag++];
					$mapforProject["chargedevice2"]=$array[$k][$kkflag++];
					$mapforProject["chargedevice3"]=$array[$k][$kkflag++];
					$mapforProject["chargedevice4"]=$array[$k][$kkflag++];
					$mapforProject["chargedevice5"]=$array[$k][$kkflag++];
					
					$mapforProject["devicescale"]=$array[$k][$kkflag++];//xxxxxx
					
					$mapforProject["chargedevice6"]=$array[$k][$kkflag++];
					$mapforProject["chargedevice7"]=$array[$k][$kkflag++];
					
					$mapforProject["chargedevice8"]=$array[$k][$kkflag++];//xxxxxx
					$mapforProject["chargedevice9"]=$array[$k][$kkflag++];//xxxxxx
					
					$mapforProject["energy1"]=$array[$k][$kkflag++];
					$mapforProject["energy2"]=$array[$k][$kkflag++];
					$mapforProject["energy3"]=$array[$k][$kkflag++];
					$mapforProject["capital1"]=$array[$k][$kkflag++];
					$mapforProject["capital2"]=$array[$k][$kkflag++];
					$mapforProject["capital3"]=$array[$k][$kkflag++];
					$mapforProject["capital4"]=$array[$k][$kkflag++];
					$mapforProject["capital5"]=$array[$k][$kkflag++];
					
					$mapforProject["capital7"]=$array[$k][$kkflag++];
					
					$mapforProject["capital6"]=$array[$k][$kkflag++];
					$mapforProject["invest1"]=$array[$k][$kkflag++];
					$mapforProject["invest2"]=$array[$k][$kkflag++];
					$mapforProject["invest3"]=$array[$k][$kkflag++];
					$mapforProject["invest41"]=$array[$k][$kkflag++];
					$mapforProject["invest42"]=$array[$k][$kkflag++];
					$mapforProject["invest43"]=$array[$k][$kkflag++];
					$mapforProject["invest4"]=$array[$k][$kkflag++];
					$mapforProject["invest51"]=$array[$k][$kkflag++];
					$mapforProject["invest52"]=$array[$k][$kkflag++];
					$mapforProject["invest53"]=$array[$k][$kkflag++];
					$mapforProject["invest5"]=$array[$k][$kkflag++];
					$mapforProject["invest6"]=$array[$k][$kkflag++];
					$mapforProject["cost1"]=$array[$k][$kkflag++];
					$mapforProject["cost2"]=$array[$k][$kkflag++];
					$mapforProject["cost3"]=$array[$k][$kkflag++];
					$mapforProject["cost4"]=$array[$k][$kkflag++];
					$mapforProject["cost5"]=$array[$k][$kkflag++];
					$mapforProject["cost6"]=$array[$k][$kkflag++];
					$mapforProject["cost7"]=$array[$k][$kkflag++];
					$mapforProject["content"]=$array[$k][$kkflag++];
					//$mapforProject["design_status"]=$array[$k][$kkflag++];
					//$mapforProject["ctime"]=$array[$k][$kkflag++];
					//$mapforProject["time"]=$array[$k][$kkflag++];
					
					$mapforProject["design_status"]="暂存";
					$mapforProject["ctime"]=date("Y-m-d");
					$mapforProject["time"]=date("Y-m-d");
					
					/*
					$mapforProject["elecscale"]=$array[$k]['48'];
					$mapforProject["devicescale"]=$array[$k]['49'];
					$mapforProject["researchmoney"]=$array[$k]['50'];
					$mapforProject["invest"]=$array[$k]['51'];
					$mapforProject["preinvest"]=$array[$k]['52'];
					$mapforProject["intention"]=$array[$k]['53'];
					$mapforProject["researchmoneyverify"]=$array[$k]['54'];
					$mapforProject["contractverify"]=$array[$k]['55'];
					$mapforProject["intime"]=$array[$k]['56'];
					$mapforProject["mainfinishtime"]=$array[$k]['57'];
					$mapforProject["elecfinishtime"]=$array[$k]['58'];
					$mapforProject["progress"]=$array[$k]['59'];
					$mapforProject["status"]=$array[$k]['60'];
					*/
					
					
					$mapforProject["create_time"]=$time;
					$mapforProject["step1"]=1;
					$x=M("Project")->add($mapforProject);
					
					
					
				}					
			}
			
			$this->success('上传成功!');
		}
		else
		{
			$this->error('上传的文件类型非法!');
		}
	}
	
	
	function plmeditlog() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
	
		$vo['logs']=M("Plmeditlog")->where("plmid=$id")->order("id desc")->select();
		$this->assign('orgdata', $vo);
		$this->assign('volist', $vo['logs']);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		
		$this->display();
	}
}
?>