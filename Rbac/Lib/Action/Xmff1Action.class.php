<?php
class Xmff1Action extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		if($_POST['address'])
		{
			$map['title'] = array('like',"%".$_POST['address']."%");
			$this->assign("address",$_POST['address']);
		}
		if($_POST['city'])
		{
			$map['city'] = array('like',"%".$_POST['city']."%");
			$this->assign("city",$_POST['city']);
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
		
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$map['projecttype'] = array("eq","承揽项目");
		//$map[design_status]=array("in","设计审核通过,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,竣工待验收,项目待验收,验收审核退回,暂停中");
		//$map[user]=array("neq","");
		//$map[step6]=array("eq","1");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities(1);
		
		
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
			//$this->display("../App/xmff1");
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
				
				$mapforPlmworktype["plmid"]=$val["id"];
				$worktypes=M("Plmworktype")->where($mapforPlmworktype)->order("sort asc")->select();
				$voList[$key]["worktypes"]=$worktypes;
		
		
				$devicearray=M("Plmdevice")->where("plmid=".$val["id"])->select();
				$devices="";
				foreach($devicearray as $key2=>$val2)
				{
					$devices.=$val2[device]."($val2[number]),</br>";
				}
				$voList[$key]["devices"]=$devices;
					
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
		
		$this->findRelativePersons($vo["projecttype"]);
		
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
		
		$this->findRelativePersons($vo["projecttype"]);
		
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
			
			
			
			$mapxianchangfuzeren['pid']=array("eq",$engineeringmanagerole);
			$mapxianchangfuzeren['name']=array("like","%公司专责%");
			$positions=M("Role")->where($mapxianchangfuzeren)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuserxianchangfuzeren["position"]=array("in",$pline);
			$mapuserxianchangfuzeren[status]=1;
			$xianchangfuzeren=M("User")->where($mapuserxianchangfuzeren)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('xianchangfuzeren', $xianchangfuzeren);
			
			
			
			$mapdaiban['pid']=array("eq",$engineeringmanagerole);
			$mapdaiban['name']=array("like","%施工专责%");
			$positions=M("Role")->where($mapdaiban)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuserdaiban["position"]=array("in",$pline);
			$mapuserdaiban[status]=1;
			$daiban=M("User")->where($mapuserdaiban)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('gongchengshi', $daiban);
			
			
			
			
			
			$mapdaiban['pid']=array("eq",$engineeringmanagerole);
			$mapdaiban['name']=array("like","%施工单位%");
			$positions=M("Role")->where($mapdaiban)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuserdaiban["position"]=array("in",$pline);
			$mapuserdaiban[status]=1;
			$shigong=M("User")->where($mapuserdaiban)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('shigong', $shigong);
			
			
			$mapdaiban['pid']=array("eq",$engineeringmanagerole);
			$mapdaiban['name']=array("like","%监理单位%");
			$positions=M("Role")->where($mapdaiban)->field("id")->select();
			$pline="";
			foreach($positions as $pkey=>$pval)
			{
				$pline.=$pval[id].",";
			}
			$mapuserdaiban["position"]=array("in",$pline);
			$mapuserdaiban[status]=1;
			$jianli=M("User")->where($mapuserdaiban)->order("nickname asc")->field("nickname,tel")->select();
			$this->assign('jianli', $jianli);
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
		
		
		$date=date('m-d H:i');
		$plmid=$_REQUEST[id];
		$plminfo=M("Project")->where("id=".$plmid)->find();
		
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
			M("Project")->where("id=".$_REQUEST["id"])->setField("projectmanager",$_REQUEST["projectmanager"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("projectmanager2",$_REQUEST["projectmanager2"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("projectmanager3",$projectmanager3);
			M("Project")->where("id=".$_REQUEST["id"])->setField("projectmanager4",$_REQUEST["projectmanager4"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("projectmanager5",$_REQUEST["projectmanager5"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("projectmanager6",$_REQUEST["projectmanager6"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("devicecompany",$devicecompany);
		}
		
		if($_REQUEST["moduletitle"]=="项目分发设置")
		{
			M("Project")->where("id=".$_REQUEST["id"])->setField("company",$_REQUEST["company"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("companydesign",$_REQUEST["companydesign"]);
			M("Project")->where("id=".$_REQUEST["id"])->setField("companysupervise",$_REQUEST["companysupervise"]);
		}
		
		if($_REQUEST["moduletitle"]=="项目人员库")//$_REQUEST["workerids"]
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
		
		if($_REQUEST["moduletitle"]=="施工资产管理")//$_REQUEST["deviceids"]
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
		
		
		if(!empty($_SESSION[app]))
		{
			$this->redirect('../App/xmff',array('moduletitle'=>$_REQUEST["moduletitle"]));
		}
		else
		{
			$this->redirect('../Xmff1/index',array('moduletitle'=>$_REQUEST["moduletitle"]));
		}
		
	}
	
	
	function setgroup() {
		$name = "Project";
		$model = M($name);
		
		
		
		
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->findRelativePersons($vo["projecttype"]);
		
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
		
		if(!empty($_SESSION[app]))
		{
			$this->redirect('../App/xmff',array('moduletitle'=>$_REQUEST["moduletitle"]));
		}
		else
		{
			$this->redirect('../Xmff1/index',array('moduletitle'=>$_REQUEST["moduletitle"]));
		}
		
	}
	
	
	function insert() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		
		
		//$model->user=$_SESSION['loginUserName'];
		//$model->charge=$_SESSION['loginUserName'];
		$model->last_time=time();
		
		
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
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Jypg";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="同意"))/*同意*/
		{
			$model->handlehistory=$info['handlehistory']."经营评估审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行经营评估，结果：同意。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行经营评估，结果：同意。";
			$this->Sendmail($data);
			
			
			$taskid=$info[id];
			$date=date('m-d H:i');
			$address=$info['title'];
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$address."》进行经营评估，请您进行工程评估。";
			$data['href'] ="index.php?s=Gcpg/index";
			$data['taskid'] =$taskid;
			$data['type'] ="Gcpg";
			//$userschedule=$this->findUserByRole("设计部经理");
			//$data['user']=$userschedule['nickname'].$userschedule['number'];
			$userschedule=$this->findleader($info['projecttype'],$info['city']);
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
			
		}
		else
		{	//拒绝流程
			$model->handlehistory=$info['handlehistory']."经营评估审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
			
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行经营评估，结果：拒绝。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行经营评估，结果：拒绝。";
			$this->Sendmail($data);
		}
		
		$list = $model->save();
			
		if ($list !== false) {
			//$this->redirect('index');
			$this->success('操作成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
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
	
	
	
	function findRelativePersons($projecttype)
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
		$gongcheng=M("User")->where($mapusergongcheng)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('gongcheng', $gongcheng);
		
	}
}
?>