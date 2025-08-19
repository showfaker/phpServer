<?php
class XmffAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		
		//$map['projecttype'] = array("neq","承揽项目");
		//$map['step3'] = array("eq","1");
		//$map['step6'] = array("egt","0.3");
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
		if($_REQUEST['plmgroup'])
		{
			$mapforSecondgroup["name"]=array('like',"%".$_REQUEST['plmgroup']."%");
			$plmgrouparray=M("Secondgroup")->where($mapforSecondgroup)->field("id")->select();
			foreach($plmgrouparray as $key => $val)
			{
				$plmgroupids.=$val["id"].",";
			}
			$plmgroupids= substr($plmgroupids,0,strlen($plmgroupids)-1);
			$map['groupid'] = array('in',$plmgroupids);
			$this->assign('plmgroup', $_REQUEST['plmgroup']);
		}
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
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
		
		if(($_SESSION[account]!="admin")&&($_SESSION[role]!="质量安全总监-工程项目经理")&&($_SESSION[role]!="质量安全总监-工程职员"))
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		
		
		$operatetypes=M("Plmoperatetype")->order("sort asc")->select();
		$this->assign('operatetypes',$operatetypes);
		/*
		if(empty($_REQUEST["tab"]))
		{
			$_REQUEST["tab"]=$operatetypes[0]["id"];
		}
		*/
		if(!empty($_REQUEST["operatetypeid"]))
		{
			$operatetypeid=$_REQUEST["operatetypeid"];
			$map["operatetypeid"]=array("eq",$_REQUEST["operatetypeid"]);
			$this->assign('operatetypeid',$_REQUEST["operatetypeid"]);
		}
		
		$operatetypeinfo=M("Plmoperatetype")->where("id=".$_REQUEST["operatetypeid"])->find();
		$this->assign('operatetypeinfo',$operatetypeinfo);
		
		if(($_REQUEST['design_status']=="立项中"))
		{
			$map[design_status]=array("in","立项中");
			$this->assign('design_status', $_REQUEST['design_status']);
		}
		else if(($_REQUEST['design_status']=="施工中"))
		{
			$map[design_status]=array("in","施工中");
			$this->assign('design_status', $_REQUEST['design_status']);
		}
		else if(($_REQUEST['design_status']=="施工完成"))
		{
			$map['design_status'] = array('in',"施工完成,完成施工");
			$this->assign('design_status', $_REQUEST['design_status']);
		}
		else if(($_REQUEST['design_status']=="完成验收"))
		{
			$map['design_status'] = array('in',"完成验收");
			$this->assign('design_status', $_REQUEST['design_status']);
		}
		else if(($_REQUEST['design_status']=="投入使用"))
		{
			$map['activity'] = array('eq',"投入使用");
			$this->assign('design_status', $_REQUEST['design_status']);
		}
		else
		{
			$map[design_status]=array("not in","取消,暂停中,暂存");
		}
			
		
		//$map[user]=array("neq","");
		//$map[step6]=array("eq","1");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		//$this->getAllcities(1);
		
		
		
		$mapforRole['name']=array("like","%市场部总监%");
		$positions=M("Role")->where($mapforRole)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuser["position"]=array("in",$pline);
		$mapuser[status]=1;
		$shichangbuzongjian=M("User")->where($mapuser)->order("nickname asc")->field("nickname,account,id")->select();
		foreach($shichangbuzongjian as $key=>$val)
		{
			$shichangbuzongjianaccount.=$val[account].",";
		}
		//dump($shichangbuzongjianaccount);
		$this->assign('shichangbuzongjianaccount', $shichangbuzongjianaccount);
		
		if($_SESSION[app]=="1")
		{
			//$this->display("../App/xmff");
			$this->display(indexapp);
		}
		else
		{
			$this->display();
		}
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
			
			foreach($voList as $key=>$val)
			{
				//$voList[$key]["groups"]=M("Plmgroup")->where("plmid=".$val["id"])->select();
				
				/*
				foreach($voList[$key]["groups"] as $key1=>$val1)
				{
					$workerarray=M("Plmworker")->where("groupid=".$val1["id"])->select();
					$workers="";
					foreach($workerarray as $key2=>$val2)
					{
						$workers.=$val2[worker]."($val2[number]),</br>";
					}
					$voList[$key]["groups"][$key1]["workers"]=$workers;
					
					
					$devicearray=M("Plmdevice")->where("groupid=".$val1["id"])->select();
					$devices="";
					foreach($devicearray as $key2=>$val2)
					{
						$devices.=$val2[device]."($val2[number]),</br>";
					}
					$voList[$key]["groups"][$key1]["devices"]=$devices;
				}
				*/
				
				$mapforPlmoperatetype["id"]=$val["operatetypeid"];
				$operatetypecontent=M("Plmoperatetype")->where($mapforPlmoperatetype)->getField("content");
				$voList[$key]["operatetypecontent"]=$operatetypecontent;
				
				/*
				$devicearray=M("Plmdevice")->where("plmid=".$val["id"])->select();
				$devices="";
				foreach($devicearray as $key2=>$val2)
				{
					$devices.=$val2[device]."($val2[number]),</br>";
				}
				$voList[$key]["devices"]=$devices;
				*/	
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
			if($_REQUEST["status"])
			{
				$p->parameter .= "status=" . $_REQUEST["status"] . "&";
			}
			if($p->parameter)
			{
				$p->parameter="&".$p->parameter;
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

	
	function draftfirst() {
		$name = "Project";
		$model = M($name);
		$model1=M("Companydevice");
		$companydevices=$model1->select();//->where("status=1")
		
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$this->findRelativePersons($vo["projecttype"],$vo["city"]);
		
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('companydevices',$companydevices);
		
		
		
		
		
		
	
		$this->display();
	}
	
	
	function setuser() {
		$name = "Project";
		$model = M($name);
		
		$model1=M("Companydevice");
		$companydevices=$model1->select();
		$this->assign('companydevices',$companydevices);
		
		
		
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		
		$operatetypeinfo=M("Plmoperatetype")->where("id=".$vo["operatetypeid"])->find();
		$this->assign('operatetypeinfo',$operatetypeinfo);
		
		
		$dealer=$this->findleaderbyrole("省公司专责",$vo['projecttype'],$vo['city']);
		$this->assign('dealer',$dealer);
		
		$this->findRelativePersons($vo["projecttype"],$vo["city"]);
		if(1)//!empty($vo[engineeringmanage])
		{
			$mapforuser[nickname]=array("eq",$vo[engineeringmanage]);
			$engineeringmanagerole=M("User")->where($mapforuser)->getField("position");
			
			
			//$mapforRole['name']=array(array("like","%开发%"),array("like","%经理%"),"and");
			$mapforRole["remark"]=array("in","开发负责人,商务负责人");
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$kaifas=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('kaifas', $kaifas);
			
			
			
			//$mapforRole['name']=array(array("like","%技术%"),array("like","%总监%"),"and");
			$mapforRole["remark"]="设计负责人";
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$shejis=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('shejis', $shejis);
			
			
			
			//$mapforRole['name']=array(array("like","%商务%"),array("like","%经理%"),"and");
			$mapforRole["remark"]="商务负责人";
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$shangwus=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('shangwus', $shangwus);
			
			
			//$mapforRole['name']=array(array("like","%供应链%"),array("like","%经理%"),"and");
			$mapforRole["remark"]="采购负责人";
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$caigous=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('caigous', $caigous);
			
			
			//$mapforRole['name']=array(array("like","%工程%"),array("like","%经理%"),"and");
			$mapforRole["remark"]="工程负责人";
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$gongchengs=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('gongchengs', $gongchengs);
			
			
			
			$mapforRole["remark"]="资料员";
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$fileusers=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('fileusers', $fileusers);
		}
		
	
		if(1)
		{
			if($_SESSION[account]!="admin")
			{
				$where["pid"]=array("eq",$_SESSION["position"]);
				$where["id"]=array("eq",$_SESSION["position"]);
				$where['_logic'] = 'or';
				$mapforRole['_complex'] = $where;
				//$mapforRole['pid']=array("eq",$_SESSION["position"]);
			}
			
			
			//$mapforRole['name']=array(array("like","%开发%"),array("like","%职员%"),"and");
			$mapforRole["remark"]=array("in","开发负责人,开发人员,商务负责人");
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$kaifausers=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('kaifausers', $kaifausers);
			
			
			
			//$mapforRole['name']=array(array("like","%设计%"),array("like","%职员%"),"and");
			$mapforRole["remark"]=array("in","设计负责人,设计人员");
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$shejiusers=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('shejiusers', $shejiusers);
			
			
			
			//$mapforRole['name']=array(array("like","%商务%"),array("like","%职员%"),"and");
			$mapforRole["remark"]=array("in","商务负责人,商务人员");
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$shangwuusers=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('shangwuusers', $shangwuusers);
			
			
			//$mapforRole['name']=array(array("like","%供应链%"),array("like","%职员%"),"and");
			$mapforRole["remark"]=array("in","采购负责人,采购人员");
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$caigouusers=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('caigouusers', $caigouusers);
			
			
			//$mapforRole['name']=array(array("like","%工程%"),array("like","%职员%"),"and");
			$mapforRole["remark"]=array("in","工程负责人,工程人员");
			$positions=M("Role")->where($mapforRole)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuser["position"]=array("in",$pline);
			$mapuser[status]=1;
			$gongchengusers=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('gongchengusers', $gongchengusers);
		}
				
		$mapforPlmworktype["plmid"]=$vo["id"];
		$worktypes=M("Plmworktype")->where($mapforPlmworktype)->order("sort asc")->select();
		$this->assign('worktypes', $worktypes);
		
		
		
		$mapforPlmworker1["groupid"]=array("neq",$_REQUEST[groupid]);
		$plmworkerarray1=M("Plmworker")->where($mapforPlmworker1)->field("workerid")->select();
		foreach($plmworkerarray1 as $key => $val)
		{
			$plmworkers1.=$val[workerid].",";
		}
		if($plmworkers1)
		{
			$mapforWorker["id"]=array("not in",$plmworkers1);
		}
		$workers=M("Worker")->where($mapforWorker)->order("number asc")->select();
		$this->assign('workers', $workers);
		
		
		
		//$where[status]="调拨中";
		//$where[groupid]=array("eq",$_REQUEST["groupid"]);
		//$where['_logic'] = 'or';
		//$mapforDevice["_complex"]=$where;
		$devices=M("Device")->where($mapforDevice)->order("number asc")->select();
		$this->assign('devices', $devices);
	
		
		
		$mapforPlmworker["groupid"]=$_REQUEST[groupid];
		$plmworkerarray=M("Plmworker")->where($mapforPlmworker)->select();
		foreach($plmworkerarray as $key => $val)
		{
			$plmworkers.=$val[workerid].",";
		}
		$vo["workerids"]=$plmworkers;
		
		//$mapforPlmdevice["groupid"]=$_REQUEST[groupid];
		$mapforPlmdevice["plmid"]=$_REQUEST[id];
		$plmdevicearray=M("Plmdevice")->where($mapforPlmdevice)->select();
		foreach($plmdevicearray as $key => $val)
		{
			$plmdevices.=$val[deviceid].",";
		}
		$vo["deviceids"]=$plmdevices;
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		$this->assign('orgdata', $vo);
		$this->assign('vo', $vo);
		$this->assign('type', $_REQUEST[type]);
		
		$this->assign('groupid', $_REQUEST[groupid]);
		$this->assign('moduletitle', $_REQUEST[moduletitle]);
		
		
		$companyclassifies=M("Companyclassify")->order("id asc")->select();
		foreach($companyclassifies as $key => $val)
		{
			$mapforCompany["duty"]=$val["name"];
			$companyclassifies[$key]["companies"]=M("Company")->order("id asc")->where($mapforCompany)->select();
		}
		$this->assign('companyclassifies', $companyclassifies);
		
		
		$model1=M("Companysupervise");
		$companysupervises=$model1->order("id asc")->select();
		
		$model2=M("Companydesign");
		$companydesigns=$model2->order("id asc")->select();
		
		$this->assign('companysupervises',$companysupervises);
		$this->assign('companydesigns',$companydesigns);
		
		$companies=M("Company")->order("id asc")->select();
		$this->assign('companies',$companies);
		
		$this->display();
	}
	function setusersubmit() {
		
		/*
		foreach($_REQUEST["workerids"] as $key => $val)
		{
			$workerids .= $val.",";
			$worker=M("Worker")->where("id=".$val)->getField("nickname");
			$workers.=$worker.",";
		}
		
		
		foreach($_REQUEST["deviceids"] as $key => $val)
		{
			$deviceids .= $val.",";
			$device=M("Device")->where("id=".$val)->getField("name");
			$devices.=$device.",";
		}
		
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		if($_REQUEST["workerids"])
		{
			$model->workerids=$workerids;
			$model->workers=$workers;
		}
		if($_REQUEST["deviceids"])
		{
			$model->deviceids=$deviceids;
			$model->devices=$devices;
		}
		$list = $model->save();
		*/
		
		$mapforPlmworktype["plmid"]=$_REQUEST["id"];
		$worktypes=M("Plmworktype")->where($mapforPlmworktype)->order("sort asc")->select();
		$this->assign('worktypes', $worktypes);
		
		
		$date=date('Y-m-d H:i:s');
		$plmid=$_REQUEST[id];
		$plminfo=M("Project")->where("id=".$plmid)->find();
		
		
		if($plminfo["step6"]=="0.3")
		{
			M("Project")->where("id=".$plminfo[id])->setField("step6","0.4");
		}
		
		$groupid=$_REQUEST[groupid];
		$group=M("plmgroup")->where("id=".$_REQUEST[groupid])->getField("group");
		
		$projectmanager3array=$_REQUEST["projectmanager3"];
		foreach($projectmanager3array as $key => $val)
		{
			$projectmanager3.=$val.",";
			M("Plmworktype")->where("id=".$worktypes[$key]["id"])->setField("worker",$val);
		}
		
		$devicecompanyarray=$_REQUEST["devicecompany"];
		foreach($devicecompanyarray as $key => $val)
		{
			$devicecompany.=$val.",";
		}
		

		if($_REQUEST["type"]=="4")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("gongcheng",$_REQUEST["gongcheng"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("kaifa",$_REQUEST["kaifa"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("sheji",$_REQUEST["sheji"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("caigou",$_REQUEST["caigou"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("shangwu",$_REQUEST["shangwu"]);
			
			
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			M("Schedule")->where($schedulemap)->setField("status",0);
		
			if((!empty($_REQUEST["gongcheng"])))
			{
				if(empty($plminfo["gongchenguser"]))
				{
					$data['content']=$_SESSION['loginUserName']."于".$date."将您设置为《".$plminfo["title"]."》施工负责人，请您设置执行人员。";
					$data['href'] ="index.php?s=Xmff/index/moduletitle/项目组织/";
					$data['taskid'] =$plminfo[id];
					$data['type'] ="Xmff";
					$data['classify']="施工";
					$userschedule=$this->findUserByName($_REQUEST["gongcheng"]);
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					
					M("Project")->where("id=".$_REQUEST["id"])->setField("set_leader_time1",date("Y-m-d"));
					M("Project")->where("id=".$_REQUEST["id"])->setField("set_leader_time5",date("Y-m-d"));
				}
				
				
				if(($plminfo["gongcheng"]!=$_REQUEST["gongcheng"])&&($plminfo["gongcheng"]!="")&&($_REQUEST["gongcheng"]!=""))
				{
					$schedulemap1[taskid]=$plminfo[id];
					$schedulemap1[status]=1;
					$userschedule=$this->findUserByName($_REQUEST["gongcheng"]);
					M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
				}
			}
			if((!empty($_REQUEST["kaifa"])))
			{
				if(empty($plminfo["kaifauser"]))
				{
					$data['content']=$_SESSION['loginUserName']."于".$date."将您设置为《".$plminfo["title"]."》开发负责人，请您设置执行人员。";
					$data['href'] ="index.php?s=Xmff/index/moduletitle/项目组织/";
					$data['taskid'] =$plminfo[id];
					$data['type'] ="Xmff";
					$data['classify']="开发";
					$userschedule=$this->findUserByName($_REQUEST["kaifa"]);
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					M("Project")->where("id=".$_REQUEST["id"])->setField("set_leader_time2",date("Y-m-d"));
				}
				
				if(($plminfo["kaifa"]!=$_REQUEST["kaifa"])&&($plminfo["kaifa"]!="")&&($_REQUEST["kaifa"]!=""))
				{
					$schedulemap1[taskid]=$plminfo[id];
					$schedulemap1[status]=1;
					$userschedule=$this->findUserByName($_REQUEST["kaifa"]);
					M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
				}
			}
			if((!empty($_REQUEST["sheji"])))
			{
				if(empty($plminfo["shejiuser"]))
				{
					$data['content']=$_SESSION['loginUserName']."于".$date."将您设置为《".$plminfo["title"]."》设计负责人，请您设置执行人员。";
					$data['href'] ="index.php?s=Xmff/index/moduletitle/项目组织/";
					$data['taskid'] =$plminfo[id];
					$data['type'] ="Xmff";
					$data['classify']="设计";
					$userschedule=$this->findUserByName($_REQUEST["sheji"]);
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					M("Project")->where("id=".$_REQUEST["id"])->setField("set_leader_time3",date("Y-m-d"));
				}
				
				if(($plminfo["sheji"]!=$_REQUEST["sheji"])&&($plminfo["sheji"]!="")&&($_REQUEST["sheji"]!=""))
				{
					$schedulemap1[taskid]=$plminfo[id];
					$schedulemap1[status]=1;
					$userschedule=$this->findUserByName($_REQUEST["sheji"]);
					M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
				}
			}
			if((!empty($_REQUEST["caigou"])))
			{
				if(empty($plminfo["caigouuser"]))
				{
					$data['content']=$_SESSION['loginUserName']."于".$date."将您设置为《".$plminfo["title"]."》采购负责人，请您设置执行人员。";
					$data['href'] ="index.php?s=Xmff/index/moduletitle/项目组织/";
					$data['taskid'] =$plminfo[id];
					$data['type'] ="Xmff";
					$data['classify']="采购";
					$userschedule=$this->findUserByName($_REQUEST["caigou"]);
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					M("Project")->where("id=".$_REQUEST["id"])->setField("set_leader_time4",date("Y-m-d"));
				}
				
				if(($plminfo["caigou"]!=$_REQUEST["caigou"])&&($plminfo["caigou"]!="")&&($_REQUEST["caigou"]!=""))
				{
					$schedulemap1[taskid]=$plminfo[id];
					$schedulemap1[status]=1;
					$userschedule=$this->findUserByName($_REQUEST["caigou"]);
					M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
				}
			}
			if((!empty($_REQUEST["shangwu"])))
			{
				if(empty($plminfo["shangwuuser"]))
				{
					$data['content']=$_SESSION['loginUserName']."于".$date."将您设置为《".$plminfo["title"]."》商务负责人，请您设置执行人员。";
					$data['href'] ="index.php?s=Xmff/index/moduletitle/项目组织/";
					$data['taskid'] =$plminfo[id];
					$data['type'] ="Xmff";
					$data['classify']="商务";
					$userschedule=$this->findUserByName($_REQUEST["shangwu"]);
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
					
					M("Project")->where("id=".$_REQUEST["id"])->setField("set_leader_time5",date("Y-m-d"));
				}
				
				if(($plminfo["shangwu"]!=$_REQUEST["shangwu"])&&($plminfo["shangwu"]!="")&&($_REQUEST["shangwu"]!=""))
				{
					$schedulemap1[taskid]=$plminfo[id];
					$schedulemap1[status]=1;
					$userschedule=$this->findUserByName($_REQUEST["shangwu"]);
					M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
				}
			}
		
		
		
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的专项负责人";
			$datamail['receiver']=$this->findProjectleaders($plmid);
		
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的专项负责人";
			$this->Sendmail($datamail);
			
			
		}
		
		
		
		
		if($_REQUEST["type"]=="5")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("kaifauser",$_REQUEST["kaifauser"]);
		}
		if(($_REQUEST["type"]=="5")&&(!empty($_REQUEST["kaifauser"])))
		{
			//清除负责人的设置执行人待办
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			$schedulemap['classify']="开发";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			if(empty($plminfo["worktype_status2"])&&($plminfo["design_status"]!="施工完成")&&($plminfo["design_status"]!="完成验收"))
			{
				$schedulemap[taskid]=$plminfo[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Ysgl";
				$schedulemap['classify']="开发";
				M("Schedule")->where($schedulemap)->setField("status",0);
			
				$data['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的开发执行人，请您设置开发节点";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/开发节点设置/";
				$data['taskid'] =$plminfo[id];
				$data['type'] ="Ysgl";
				$data['classify']="开发";
				$userschedule=$this->findUserByName($_REQUEST["kaifauser"]);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
				M("Project")->where("id=".$_REQUEST["id"])->setField("set_user_time2",date("Y-m-d"));
			}
			
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的开发执行人";
			$datamail['receiver']=$this->findProjectusers($plmid,"开发");
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的开发执行人";
			$this->Sendmail($datamail);
			
			
			if(($plminfo["kaifauser"]!=$_REQUEST["kaifauser"])&&($plminfo["kaifauser"]!="")&&($_REQUEST["kaifauser"]!=""))
			{
				$schedulemap1[taskid]=$plminfo[id];
				$schedulemap1[status]=1;
				$userschedule=$this->findUserByName($_REQUEST["kaifauser"]);
				M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
			}
		
			
		}
		
		if($_REQUEST["type"]=="6")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("shejiuser",$_REQUEST["shejiuser"]);
		}
		if(($_REQUEST["type"]=="6")&&(!empty($_REQUEST["shejiuser"])))
		{	
			//清除负责人的设置执行人待办
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			$schedulemap['classify']="设计";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			
			if(empty($plminfo["worktype_status3"])&&($plminfo["design_status"]!="施工完成")&&($plminfo["design_status"]!="完成验收"))
			{
				$schedulemap[taskid]=$plminfo[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Ysgl";
				$schedulemap['classify']="设计";
				M("Schedule")->where($schedulemap)->setField("status",0);
				
				$data['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的设计执行人，请您设置设计节点";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/设计节点设置/";
				$data['taskid'] =$plminfo[id];
				$data['type'] ="Ysgl";
				$data['classify']="设计";
				$userschedule=$this->findUserByName($_REQUEST["shejiuser"]);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
				M("Project")->where("id=".$_REQUEST["id"])->setField("set_user_time3",date("Y-m-d"));
			}
			
			
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的设计执行人";
			$datamail['receiver']=$this->findProjectusers($plmid,"设计");
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的设计执行人";
			$this->Sendmail($datamail);
			
			
			if(($plminfo["shejiuser"]!=$_REQUEST["shejiuser"])&&($plminfo["shejiuser"]!="")&&($_REQUEST["shejiuser"]!=""))
			{
				$schedulemap1[taskid]=$plminfo[id];
				$schedulemap1[status]=1;
				$userschedule=$this->findUserByName($_REQUEST["shejiuser"]);
				M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
			}
		}
		
		if($_REQUEST["type"]=="7")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("caigouuser",$_REQUEST["caigouuser"]);
		}
		if(($_REQUEST["type"]=="7")&&(!empty($_REQUEST["caigouuser"])))
		{
			//清除负责人的设置执行人待办
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			$schedulemap['classify']="采购";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			if(empty($plminfo["worktype_status4"])&&($plminfo["design_status"]!="施工完成")&&($plminfo["design_status"]!="完成验收"))
			{
				$schedulemap[taskid]=$plminfo[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Ysgl";
				$schedulemap['classify']="采购";
				M("Schedule")->where($schedulemap)->setField("status",0);
				
				$data['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的采购执行人，请您设置采购节点";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/采购节点设置/";
				$data['taskid'] =$plminfo[id];
				$data['type'] ="Ysgl";
				$data['classify']="采购";
				$userschedule=$this->findUserByName($_REQUEST["caigouuser"]);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
				M("Project")->where("id=".$_REQUEST["id"])->setField("set_user_time4",date("Y-m-d"));
			}
			
			
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的采购执行人";
			$datamail['receiver']=$this->findProjectusers($plmid,"采购");
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的采购执行人";
			$this->Sendmail($datamail);
			
			if(($plminfo["caigouuser"]!=$_REQUEST["caigouuser"])&&($plminfo["caigouuser"]!="")&&($_REQUEST["caigouuser"]!=""))
			{
				$schedulemap1[taskid]=$plminfo[id];
				$schedulemap1[status]=1;
				$userschedule=$this->findUserByName($_REQUEST["caigouuser"]);
				M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
			}
		}
		
		if($_REQUEST["type"]=="8")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("gongchenguser",$_REQUEST["gongchenguser"]);
		}
		if(($_REQUEST["type"]=="8")&&(!empty($_REQUEST["gongchenguser"])))
		{
			//清除负责人的设置执行人待办
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			$schedulemap['classify']="施工";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			if(empty($plminfo["worktype_status1"])&&($plminfo["design_status"]!="施工完成")&&($plminfo["design_status"]!="完成验收"))
			{
				$schedulemap[taskid]=$plminfo[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Ysgl";
				$schedulemap['classify']="主项";
				M("Schedule")->where($schedulemap)->setField("status",0);
				
				$data['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的主项执行人，请您设置主项节点";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/主项节点设置/";
				$data['taskid'] =$plminfo[id];
				$data['type'] ="Ysgl";
				$data['classify']="主项";
				$userschedule=$this->findUserByName($_REQUEST["gongchenguser"]);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
				M("Project")->where("id=".$_REQUEST["id"])->setField("set_user_time1",date("Y-m-d"));
			}
			
			
			if(empty($plminfo["worktype_status5"])&&($plminfo["design_status"]!="施工完成")&&($plminfo["design_status"]!="完成验收"))
			{
				
				$schedulemap[taskid]=$plminfo[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Ysgl";
				$schedulemap['classify']="施工";
				M("Schedule")->where($schedulemap)->setField("status",0);
				
				$data['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的施工执行人，请您设置施工节点";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/施工节点设置/";
				$data['taskid'] =$plminfo[id];
				$data['type'] ="Ysgl";
				$data['classify']="施工";
				$userschedule=$this->findUserByName($_REQUEST["gongchenguser"]);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			
			
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的施工执行人";
			$datamail['receiver']=$this->findProjectusers($plmid,"施工");
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的施工执行人";
			$this->Sendmail($datamail);
			
			
			if(($plminfo["gongchenguser"]!=$_REQUEST["gongchenguser"])&&($plminfo["gongchenguser"]!="")&&($_REQUEST["gongchenguser"]!=""))
			{
				$schedulemap1[taskid]=$plminfo[id];
				$schedulemap1[status]=1;
				$userschedule=$this->findUserByName($_REQUEST["gongchenguser"]);
				M("Schedule")->where($schedulemap1)->setField("user",$userschedule['nickname'].$userschedule['number']);
			}
		}
		
		if($_REQUEST["type"]=="9")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("shangwuuser",$_REQUEST["shangwuuser"]);
		}
		if(($_REQUEST["type"]=="9")&&(!empty($_REQUEST["shangwuuser"])))
		{
			//清除负责人的设置执行人待办
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			$schedulemap['classify']="商务";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的商务执行人";
			$datamail['receiver']=$this->findProjectusers($plmid,"商务");
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的商务执行人";
			$this->Sendmail($datamail);
			
			M("Project")->where("id=".$_REQUEST["id"])->setField("set_user_time5",date("Y-m-d"));
		}
		
		if($_REQUEST["type"]=="10")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("fileuser",$_REQUEST["fileuser"]);
		}
		if(($_REQUEST["type"]=="10")&&(!empty($_REQUEST["fileuser"])))
		{
			//清除负责人的设置执行人待办
			$schedulemap[taskid]=$plminfo[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Xmff";
			$schedulemap['classify']="资料";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			
			$datamail['content']=$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的资料员";
			$datamail['receiver']=$this->findProjectusers($plmid,"商务");
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."将您设置为项目《".$plminfo["title"]."》的资料员";
			$this->Sendmail($datamail);
			
			M("Project")->where("id=".$_REQUEST["id"])->setField("set_user_time6",date("Y-m-d"));
		}
		
		
		
		if($_REQUEST["company"])
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("company",$_REQUEST["company"]);
		}
		if($_REQUEST["companydesign"])
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("companydesign",$_REQUEST["companydesign"]);
		}
		if($_REQUEST["companysupervise"])
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("companysupervise",$_REQUEST["companysupervise"]);
		}
		
		
		
		if($plminfo["design_status"]=="立项中")
		{
			M("Project")->where("id=".$plminfo[id])->setField("design_status","施工中");
		}
			
		if($_REQUEST["workerids"])
		{
			M("Plmworker")->where("groupid=".$groupid)->delete();
			foreach($_REQUEST["workerids"] as $key => $val)
			{
				$dataforPlmworker[workerid]=$val;
				$worker=M("Worker")->where("id=".$val)->find();
				$dataforPlmworker[worker]=$worker["nickname"];
				$dataforPlmworker[number]=$worker["number"];
				$dataforPlmworker[job]=$worker["job"];
				$dataforPlmworker[groupid]=$groupid;
				$dataforPlmworker[group]=$group;
				$dataforPlmworker[plmid]=$_REQUEST["id"];
				M("Plmworker")->add($dataforPlmworker);
			}
		}
		
		if($_REQUEST["deviceids"])
		{
			$oldplmdeviceinfo=M("Plmdevice")->where("groupid=".$groupid)->select();
			foreach($oldplmdeviceinfo as $key => $val)
			{
				$flag=0;
				foreach($_REQUEST["deviceids"] as $key1 => $val1)
				{
					if($val["deviceid"]==$val1)
					{
						$flag=1;
						break;
					}
				}
				if($flag==0)
				{
					$oldinfo=M("Device")->where("id=".$val["deviceid"])->find();
					$handlehistory=$oldinfo["handlehistory"].$_SESSION['loginUserName']."于".$date."从项目【".$plminfo["title"]."】修改状态为【在库】</br>------------------</br>";
					M("Device")->where("id=".$val["deviceid"])->setField("handlehistory",$handlehistory);
					M("Device")->where("id=".$val["deviceid"])->setField("status","在库");
					
					M("Device")->where("id=".$val["deviceid"])->setField("plmid","");
					M("Device")->where("id=".$val["deviceid"])->setField("groupid","");
				}
			}
			
			//M("Plmdevice")->where("groupid=".$groupid)->delete();
			M("Plmdevice")->where("plmid=".$plmid)->delete();
			foreach($_REQUEST["deviceids"] as $key => $val)
			{
				
				$flag=0;
				foreach($oldplmdeviceinfo as $key1 => $val1)
				{
					//if($val1["deviceid"]==$val)
					//{
					//	$flag=1;
					//	break;
					//}
				}
				if($flag==0)
				{
					$oldinfo=M("Device")->where("id=".$val)->find();
					$handlehistory=$oldinfo["handlehistory"].$_SESSION['loginUserName']."于".$date."在项目【".$plminfo["title"]."】修改状态为【在用】</br>------------------</br>";
					M("Device")->where("id=".$val)->setField("handlehistory",$handlehistory);
					M("Device")->where("id=".$val)->setField("status","在用");
					
					
					M("Device")->where("id=".$val)->setField("plmid",$plminfo["id"]);
					M("Device")->where("id=".$val)->setField("groupid",$_REQUEST[groupid]);
				}
				
				
				$dataforPlmdevice[deviceid]=$val;
				$device=M("device")->where("id=".$val)->find();
				$dataforPlmdevice[device]=$device["name"];
				$dataforPlmdevice[model]=$device["model"];
				$dataforPlmdevice[plate]=$device["plate"];
				$dataforPlmdevice[number]=$device["number"];
				$dataforPlmdevice[groupid]=$groupid;
				$dataforPlmdevice[group]=$group;
				$dataforPlmdevice[plmid]=$_REQUEST["id"];
				M("Plmdevice")->add($dataforPlmdevice);
			}
		}
		
		
		if(1)
		{
			//$this->redirect('../App/xmff',array('moduletitle'=>$_REQUEST["moduletitle"]));
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
		}
		
	}
	
	
	function setgroup() {
		$name = "Project";
		$model = M($name);
		
		
		
		
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->findRelativePersons($vo["projecttype"],$vo["city"]);
		
		if(!empty($vo[engineeringmanage]))
		{
			$mapforuser[nickname]=array("eq",$vo[engineeringmanage]);
			$engineeringmanagerole=M("User")->where($mapforuser)->getField("position");
			
			$mapxiangmujingli['pid']=array("eq",$engineeringmanagerole);
			$mapxiangmujingli['name']=array("like","%项目经理%");
			$positions=M("Role")->where($mapxiangmujingli)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuserxiangmujingli["position"]=array("in",$pline);
			$mapuserxiangmujingli[status]=1;
			$xiangmujingli=M("User")->where($mapuserxiangmujingli)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('xiangmujingli', $xiangmujingli);
			
			$mapdaiban['pid']=array("eq",$engineeringmanagerole);
			$mapdaiban['name']=array("like","%带班%");
			$positions=M("Role")->where($mapdaiban)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuserdaiban["position"]=array("in",$pline);
			$mapuserdaiban[status]=1;
			$daiban=M("User")->where($mapuserdaiban)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('daiban', $daiban);
		}
		
		
		$workers=M("Worker")->order("number asc")->select();
		$this->assign('workers', $workers);
		
		$devices=M("Device")->order("number asc")->select();
		$this->assign('devices', $devices);
		
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		$this->assign('orgdata', $vo);
		$this->assign('vo', $vo);
		$this->assign('type', $_REQUEST[type]);
		$this->display();
	}
	function setgroupsubmit() {
		
		$group=$_REQUEST["group"];
		$plmid=$_REQUEST["id"];
		
		$data["group"]=$group;
		$data["plmid"]=$plmid;
		
		M("plmgroup")->add($data);
		
		if(1)
		{
			//$this->redirect('../App/xmff',array('moduletitle'=>$_REQUEST["moduletitle"]));
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
		}
		else
		{
			$this->redirect('../Xmff/index',array('moduletitle'=>$_REQUEST["moduletitle"]));
		}
		
	}
	
	

	
	public function foreverdelete() {
        //删除指定记录
        $name = "Plmgroup";
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
	
	
	
	function findRelativePersons($projecttype,$city)
	{
		/*查找所有设计师*/ 
		$map['name']=array("like","%设计%");
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuser["position"]=array("in",$pline);
		$mapuser[status]=1;
		
		if($projecttype)
		{
			$mapuser[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuser[city]=array("like","%".$city."%");
		}
		$shejishi=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
		foreach($shejishi as $key=>$val)
		{
			$shejishi[$key][pinyin]=$this->pinyin1($val[nickname]);
		}
		$this->assign('shejishi', $shejishi);
		$this->assign('shejishitel', $shejishi);
		
		$mapmanager['name']=array("like","%设计部经理%");
		$positions=M("Role")->where($mapmanager)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusermanager["position"]=array("in",$pline);
		$mapusermanager[status]=1;
		if($projecttype)
		{
			$mapusermanager[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapusermanager[city]=array("like","%".$city."%");
		}
		$shejibujingli=M("User")->where($mapusermanager)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('shejibujingli', $shejibujingli);
		$this->assign('shejibujinglitel', $shejibujingli);
		
		
		
		$mapxiangmujingli['name']=array("like","%项目经理%");
		$positions=M("Role")->where($mapxiangmujingli)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserxiangmujingli["position"]=array("in",$pline);
		$mapuserxiangmujingli[status]=1;
		if($projecttype)
		{
			$mapuserxiangmujingli[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserxiangmujingli[city]=array("like","%".$city."%");
		}
		$xiangmujingli=M("User")->where($mapuserxiangmujingli)->order("nickname asc")->field("nickname,tel")->select();
		foreach($xiangmujingli as $key=>$val)
		{
			$xiangmujingli[$key][pinyin]=$this->pinyin1($val[nickname]);
		}
		
		
		
		$this->assign('xiangmujingli', $xiangmujingli);
		$this->assign('manager', $xiangmujingli);
		$this->assign('xiangmujinglitel', $xiangmujingli);
		
		
		$mapjianli['name']=array("like","%监理%");
		$positions=M("Role")->where($mapjianli)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserjianli["position"]=array("in",$pline);
		$mapuserjianli[status]=1;
		if($projecttype)
		{
			$mapuserjianli[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserjianli[city]=array("like","%".$city."%");
		}
		$jianli=M("User")->where($mapuserjianli)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('jianli', $jianli);
		$this->assign('jianlitel', $jianli);
		
		
		$mapgongchengjingli['name']=array("like","%施工专责%");
		$positions=M("Role")->where($mapgongchengjingli)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusergongchengjingli["position"]=array("in",$pline);
		$mapusergongchengjingli[status]=1;
		if($projecttype)
		{
			$mapusergongchengjingli[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapusergongchengjingli[city]=array("like","%".$city."%");
		}
		$gongchengshi=M("User")->where($mapusergongchengjingli)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('gongchengshi', $gongchengshi);
		
		
		
		$mapcaiwu['name']=array("like","%班长%");
		$positions=M("Role")->where($mapcaiwu)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusercaiwu["position"]=array("in",$pline);
		$mapusercaiwu[status]=1;
		if($projecttype)
		{
			$mapusercaiwu[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapusercaiwu[city]=array("like","%".$city."%");
		}
		$banzhang=M("User")->where($mapusercaiwu)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('banzhang', $banzhang);
		
		
		$mapxiaoguotu['name']=array("like","%公司专责%");
		$positions=M("Role")->where($mapxiaoguotu)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserxiaoguotushi["position"]=array("in",$pline);
		$mapuserxiaoguotushi[status]=1;
		if($projecttype)
		{
			$mapuserxiaoguotushi[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserxiaoguotushi[city]=array("like","%".$city."%");
		}
		$xianchangfuzeren=M("User")->where($mapuserxiaoguotushi)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('xianchangfuzeren', $xianchangfuzeren);
		
		
		
		$mapyusuan['name']=array("like","%预算%");
		$positions=M("Role")->where($mapyusuan)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuseryusuan["position"]=array("in",$pline);
		$mapuseryusuan[status]=1;
		if($projecttype)
		{
			$mapuseryusuan[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuseryusuan[city]=array("like","%".$city."%");
		}
		$yusuan=M("User")->where($mapuseryusuan)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('yusuan', $yusuan);
		
		
		
		$mapranqi['name']=array("like","%燃气%");
		$positions=M("Role")->where($mapranqi)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserranqi["position"]=array("in",$pline);
		$mapuserranqi[status]=1;
		if($projecttype)
		{
			$mapuserranqi[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserranqi[city]=array("like","%".$city."%");
		}
		$ranqi=M("User")->where($mapuserranqi)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('ranqi', $ranqi);
		
		$mapxiaofang['name']=array("like","%消防%");
		$positions=M("Role")->where($mapxiaofang)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserxiaofang["position"]=array("in",$pline);
		$mapuserxiaofang[status]=1;
		if($projecttype)
		{
			$mapuserxiaofang[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserxiaofang[city]=array("like","%".$city."%");
		}
		$xiaofang=M("User")->where($mapuserxiaofang)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('xiaofang', $xiaofang);
		
		
		$mapruodian['name']=array("like","%弱电%");
		$positions=M("Role")->where($mapruodian)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserruodian["position"]=array("in",$pline);
		$mapuserruodian[status]=1;
		if($projecttype)
		{
			$mapuserruodian[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserruodian[city]=array("like","%".$city."%");
		}
		$ruodian=M("User")->where($mapuserruodian)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('ruodian', $ruodian);
		
		
		$mapdaiban['name']=array("like","%带班%");
		$positions=M("Role")->where($mapdaiban)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserdaiban["position"]=array("in",$pline);
		$mapuserdaiban[status]=1;
		if($projecttype)
		{
			$mapuserdaiban[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuserdaiban[city]=array("like","%".$city."%");
		}
		$daiban=M("User")->where($mapuserdaiban)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('daiban', $daiban);
		
		
		$mapcailiao['name']=array("like","%材料%");
		$positions=M("Role")->where($mapcailiao)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusercailiao["position"]=array("in",$pline);
		$mapusercailiao[status]=1;
		if($projecttype)
		{
			$mapusercailiao[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapusercailiao[city]=array("like","%".$city."%");
		}
		$cailiao=M("User")->where($mapusercailiao)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('cailiao', $cailiao);
		
		
		$mapshichang['name']=array("like","%市场%");
		$positions=M("Role")->where($mapshichang)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusershichang["position"]=array("in",$pline);
		$mapusershichang[status]=1;
		if($projecttype)
		{
			$mapusershichang[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapusershichang[city]=array("like","%".$city."%");
		}
		$shichang=M("User")->where($mapusershichang)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('shichang', $shichang);
		
		
		$mapyongchi['name']=array("like","%泳池%");
		$positions=M("Role")->where($mapyongchi)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuseryongchi["position"]=array("in",$pline);
		$mapuseryongchi[status]=1;
		if($projecttype)
		{
			$mapuseryongchi[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapuseryongchi[city]=array("like","%".$city."%");
		}
		$yongchi=M("User")->where($mapuseryongchi)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('yongchi', $yongchi);
		
		
		$mapgongcheng['name']=array("like","%工程负责人%");
		$positions=M("Role")->where($mapgongcheng)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusergongcheng["position"]=array("in",$pline);
		$mapusergongcheng[status]=1;
		if($projecttype)
		{
			$mapusergongcheng[projecttype]=array(array("eq",""),array("like","%".$projecttype."%"),"or");
		}
		if($city)
		{
			$mapusergongcheng[city]=array("like","%".$city."%");
		}
		$gongcheng=M("User")->where($mapusergongcheng)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('gongcheng', $gongcheng);
		
	}
}
?>