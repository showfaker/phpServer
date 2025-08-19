<?php
class Qdhtcheck2Action extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		//$map[step4]=1;
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
		if($_POST['para1'])
		{
			$map1['para1'] = array('like',"%".$_POST['para1']."%");
			$this->assign("para1",$_POST['para1']);
		}
		if($_POST['para2'])
		{
			$map1['para2'] = array('like',"%".$_POST['para2']."%");
			$this->assign("para2",$_POST['para2']);
		}
		if($_POST['para3'])
		{
			$map1['para3'] = array('like',"%".$_POST['para3']."%");
			$this->assign("para3",$_POST['para3']);
		}
		if($_POST['para4'])
		{
			$map1['para4'] = array('like',"%".$_POST['para4']."%");
			$this->assign("para4",$_POST['para4']);
		}
		if($_POST['para5'])
		{
			$map1['para5'] = array('like',"%".$_POST['para5']."%");
			$this->assign("para5",$_POST['para5']);
		}
		if($_POST['para6'])
		{
			$map1['para6'] = array('like',"%".$_POST['para6']."%");
			$this->assign("para6",$_POST['para6']);
		}
		if($_POST['para7'])
		{
			$map1['para7'] = array('like',"%".$_POST['para7']."%");
			$this->assign("para7",$_POST['para7']);
		}
		if($_POST['para8'])
		{
			$map1['para8'] = array('like',"%".$_POST['para8']."%");
			$this->assign("para8",$_POST['para8']);
		}
		
		if((!empty($_REQUEST['para15_begin']))&&(empty($_REQUEST['para15_end'])))
			$map1['para15'] = array('egt',(int)($_REQUEST['para15_begin']));
		else if((empty($_REQUEST['para15_begin']))&&(!empty($_REQUEST['para15_end'])))
			$map1['para15'] = array('elt',(int)($_REQUEST['para15_end']));
		else if((!empty($_REQUEST['para15_begin']))&&(!empty($_REQUEST['para15_end'])))
			$map1['para15'] = array(array('egt',(int)($_REQUEST['para15_begin'])),array('elt',(int)($_REQUEST['para15_end'])),'and');
		$this->assign('para15_begin', $_REQUEST['para15_begin']);
		$this->assign('para15_end', $_REQUEST['para15_end']);
		
		if((!empty($_REQUEST['para11_begin']))&&(empty($_REQUEST['para11_end'])))
			$map1['para11'] = array('egt',(int)($_REQUEST['para11_begin']));
		else if((empty($_REQUEST['para11_begin']))&&(!empty($_REQUEST['para11_end'])))
			$map1['para11'] = array('elt',(int)($_REQUEST['para11_end']));
		else if((!empty($_REQUEST['para11_begin']))&&(!empty($_REQUEST['para11_end'])))
			$map1['para11'] = array(array('egt',(int)($_REQUEST['para11_begin'])),array('elt',(int)($_REQUEST['para11_end'])),'and');
		$this->assign('para11_begin', $_REQUEST['para11_begin']);
		$this->assign('para11_end', $_REQUEST['para11_end']);
		
		if((!empty($_REQUEST['para12_begin']))&&(empty($_REQUEST['para12_end'])))
			$map1['para12'] = array('egt',(int)($_REQUEST['para12_begin']));
		else if((empty($_REQUEST['para12_begin']))&&(!empty($_REQUEST['para12_end'])))
			$map1['para12'] = array('elt',(int)($_REQUEST['para12_end']));
		else if((!empty($_REQUEST['para12_begin']))&&(!empty($_REQUEST['para12_end'])))
			$map1['para12'] = array(array('egt',(int)($_REQUEST['para12_begin'])),array('elt',(int)($_REQUEST['para12_end'])),'and');
		$this->assign('para12_begin', $_REQUEST['para12_begin']);
		$this->assign('para12_end', $_REQUEST['para12_end']);
		
		if(($_POST['para1'])||($_POST['para2'])||($_POST['para3'])||($_POST['para4'])||($_POST['para5'])||($_POST['para6'])||($_POST['para7'])||($_POST['para8'])||($_POST['para15_begin'])||($_POST['para15_end'])||($_POST['para11_begin'])||($_POST['para11_end'])||($_POST['para12_begin'])||($_POST['para12_end']))
		{
			$plmbidarray=M("Plmcontract1")->where($map1)->select();
			foreach($plmbidarray as $key => $val)
			{
				$ids.=$val["plmNumber"].",";
			}
			$map['id'] = array('in',$ids);
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
		
		//$map[design_status]=array("in","报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'id',false);
		}
		
		$this->getAllcities();
		if($_SESSION[app]=="1")
		{
			$this->display("indexapp");
			return;
		}
		$this->display("../Qdhtcheck2/index");
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
			foreach($voList as $key => $val)
			{
				$voList[$key]['contract']=explode(',',$val['contract']);
				$voList[$key]['contractfilename']=explode(',',$val['contractfilename']);
				
				$mapforPlmcontract[plmNumber]=$val["id"];
				$voList[$key][plminfo]=M("Plmcontract1")->where($mapforPlmcontract)->find();
				
				$voList[$key][plminfo][para10]=$voList[$key][plminfo][quantities1]+$voList[$key][plminfo][quantities2];
				
				$voList[$key][plminfo]['contract']=explode(',',$voList[$key][plminfo]['contract']);
				$voList[$key][plminfo]['contractfilename']=explode(',',$voList[$key][plminfo]['contractfilename']);
				
				$voList[$key][plminfo]['note']=explode(',',$voList[$key][plminfo]['note']);
				$voList[$key][plminfo]['notefilename']=explode(',',$voList[$key][plminfo]['notefilename']);
				
			}
			foreach($voList as $key => $val)
			{
				$condition["id"]=$val['groupid'];
				$voList[$key]['groupinfo']= M("Secondgroup")->where($condition)->find();
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
        return;
    }
	
	
	function draftfirst() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$companies=M("Company")->select();
		$this->assign('companies', $companies);
		
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
		
		$vo['intention']=explode(',',$vo['intention']);
		$vo['intentionfilename']=explode(',',$vo['intentionfilename']);
		
		$vo['contract']=explode(',',$vo['contract']);
		$vo['contractfilename']=explode(',',$vo['contractfilename']);
		
		
		$mapforPlmcontract[plmNumber]=$vo["id"];
		$plminfo=M("Plmcontract1")->where($mapforPlmcontract)->find();
		$plminfo['contract']=explode(',',$plminfo['contract']);
		$plminfo['contractfilename']=explode(',',$plminfo['contractfilename']);
		
		$plminfo['note']=explode(',',$plminfo['note']);
		$plminfo['notefilename']=explode(',',$plminfo['notefilename']);
		
		$vo[plminfo]=$plminfo;
		
		for($i=1;$i<=31;$i++)
		{
			$title="para".$i;
			$vo[$title]=$plminfo[$title];
		}
		
		$vo["time"]=$plminfo["time"];
		$vo[technology]=$plminfo[technology];
		
		$vo[quantities1]=$plminfo[quantities1];
		$vo[hotprice1]=$plminfo[hotprice1];
		$vo[material1]=$plminfo[material1];
		$vo[entrancefee1]=$plminfo[entrancefee1];
		$vo[estimate_total1]=$plminfo[estimate_total1];
		
		$vo[quantities2]=$plminfo[quantities2];
		$vo[hotprice2]=$plminfo[hotprice2];
		$vo[material2]=$plminfo[material2];
		$vo[entrancefee2]=$plminfo[entrancefee2];
		$vo[estimate_total2]=$plminfo[estimate_total2];
		
		$vo[quantities3]=$plminfo[quantities3];
		$vo[hotprice3]=$plminfo[hotprice3];
		$vo[material3]=$plminfo[material3];
		$vo[entrancefee3]=$plminfo[entrancefee3];
		$vo[estimate_total3]=$plminfo[estimate_total3];
		
		$vo[quantities4]=$plminfo[quantities4];
		$vo[hotprice4]=$plminfo[hotprice4];
		$vo[material4]=$plminfo[material4];
		$vo[entrancefee4]=$plminfo[entrancefee4];
		$vo[estimate_total4]=$plminfo[estimate_total4];
		
		$vo[quantities5]=$plminfo[quantities5];
		$vo[hotprice5]=$plminfo[hotprice5];
		$vo[material5]=$plminfo[material5];
		$vo[entrancefee5]=$plminfo[entrancefee5];
		$vo[estimate_total5]=$plminfo[estimate_total5];
		
		$vo[quantities6]=$plminfo[quantities6];
		$vo[hotprice6]=$plminfo[hotprice6];
		$vo[material6]=$plminfo[material6];
		$vo[entrancefee6]=$plminfo[entrancefee6];
		$vo[estimate_total6]=$plminfo[estimate_total6];
		
		$vo[finishtime]=$plminfo[finishtime];
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		
		
		$this->assign('technology1', "整形热再生");
		$this->assign('technology2', "复拌热再生");
		$this->assign('technology3', "地聚物注浆");
		$this->assign('technology4', "高聚物注浆");
		$this->assign('technology5', "大空隙灌浆");
		$this->assign('technology6', "快速回填");
		

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		$this->display();
	}
	function approvedetail() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$this->assign('orgdata', $vo);
	
		$this->display("../Qdht/approvedetail");
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
	
	function insert() {
		$mapforProject[id]=$_REQUEST["id"];
		$plminfo=M("Project")->where($mapforProject)->find();
		
		$name = "Plmcontract1";
		$model = D($name);
		
		$model->plmNumber=$plminfo['id'];
		$model->title=$plminfo['title'];
		$model->number=$plminfo['number'];
		$model->addPerson=$_SESSION['loginUserName'];
		$model->create_time=time();
		
		for($i=1;$i<=31;$i++)
		{
			$title="para".$i;
			$model->$title=$_REQUEST[$title];
		}
		
		foreach($_REQUEST[technology] as $key => $val)
		{
			$technology.=$val.",";
		}
		$model->time=$_REQUEST["time"];
		$model->technology=$technology;
		
		$model->finishtime=$_REQUEST[finishtime];
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file3']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file3']['name'];
			$file_tmp=$_FILES['file3']['tmp_name'];
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
			$model->note=$newnameall;
			$model->notefilename=$filenameall;
		}
		if(!empty($_FILES['file4']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file4']['name'];
			$file_tmp=$_FILES['file4']['tmp_name'];
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
			$model->contract=$newnameall;
			$model->contractfilename=$filenameall;
		}
		
		
		$mapforrepeat[plmNumber]=$plminfo["id"];
		$repeat=M("Plmcontract1")->where($mapforrepeat)->find();	
		if($repeat)
		{
			$model->id=$repeat[id];
			$model->save();
			
			$date=date('m-d H:i');
			$plminfo[handlehistory]=$plminfo['handlehistory'].$_SESSION['loginUserName']."于".$date."上传合同</br>------------------</br>"; 
			M("Project")->where($mapforProject)->setField("handlehistory",$plminfo[handlehistory]);
		}
		else
		{
			$model->add();
			
			
			M("Project")->where("id='" . $plminfo[id] . "'")->setField("hetonguser",$_SESSION['loginUserName']);
			$date=date('m-d H:i');
			M("Project")->where($mapforProject)->setField("step5","1");
			M("Project")->where($mapforProject)->setField("contract_time",time());
			M("Project")->where($mapforProject)->setField("design_status","合同审核完成");
			$plminfo[handlehistory]=$plminfo['handlehistory'].$_SESSION['loginUserName']."于".$date."上传合同</br>------------------</br>"; 
			M("Project")->where($mapforProject)->setField("handlehistory",$plminfo[handlehistory]);
		
		}
		M("Project")->where("id='" . $plminfo[id] . "'")->setField("contract_number",$_REQUEST["para5"]);
	
	
		
		
		$model = D("Project");
		$info=M("Project")->where($mapforProject)->find();
		if(empty($_REQUEST[approve]))
		{
			//$model->design_status="合同待审核";
			/*
			$model->contract_time=time();
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了合同信息</br>------------------</br>"; 
			*/
		}
		else
		{
			/*
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			//$schedulemap[type]="Secondcheck";
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			if(($_REQUEST[result]=="同意"))
			{
				$model->handlehistory=$info['handlehistory']."合同审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->design_status="合同审核完成";
				$model->contract_approve_time=time();
				$model->step6=1;
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同审核，结果：同意。";
				$data['receiver']=$info['hetonguser'].$this->findNumberByNameAndRole($info['hetonguser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行经营评估，结果：同意。";
				$this->Sendmail($data);
				
			}
			else
			{	
				$model->handlehistory=$info['handlehistory']."合同审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->design_status="合同审核退回";
				$model->contract_approve_time=time();
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同审核，结果：拒绝。";
				$data['receiver']=$info['hetonguser'].$this->findNumberByNameAndRole($info['hetonguser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同审核，结果：拒绝。";
				$this->Sendmail($data);
			}
			*/
		}
		//$model->id=$_REQUEST["id"];
		//$model->save();
		
		if($_SESSION[app])
		{
			//$this->redirect('App/detail&check=1&id='.$_REQUEST["id"]);
			$this->redirect('index');
			return;
		}
		$this->success('操作成功!');
		//$this->redirect('index');
	}
	
	
	function approve() {
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
		
		$vo['intention']=explode(',',$vo['intention']);
		$vo['intentionfilename']=explode(',',$vo['intentionfilename']);
		
		$vo['contract']=explode(',',$vo['contract']);
		$vo['contractfilename']=explode(',',$vo['contractfilename']);
	
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
	
		$this->display();
	}
	
	function approvesubmit() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		
		
		
		//$model->user=$_SESSION['loginUserName'];
		//$model->charge=$_SESSION['loginUserName'];
		//$model->last_time=time();
		
		
		$date=date('m-d H:i');
		$address=$model->title;
		
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file4']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file4']['name'];
			$file_tmp=$_FILES['file4']['tmp_name'];
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
					$hetong.='[<a href="__URL__/filedown/filename/'.$newname.'/filerealname/'.$filename.'" style="color:green">合同下载</a>]';
				}
			}
			$model->contract=$newnameall;
			$model->contractfilename=$filenameall;
		}
		
		//保存当前数据对象
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		//$schedulemap[type]="Qdht";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="同意"))/*同意*/
		{
			$model->handlehistory=$info['handlehistory']."合同审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			if(!empty($_FILES['file4']['name'][0]))
			{
				$model->contracthistory=$info['contracthistory']."合同审核：".$_SESSION['loginUserName']."[".$_SESSION['dept']."]"."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date.'</br>'.$hetong.'</br>------------------</br>';
			}
			else
			{
				$model->contracthistory=$info['contracthistory']."合同审核：".$_SESSION['loginUserName']."[".$_SESSION['dept']."]"."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date.'</br>------------------</br>';
			}
			
			if($info[approvestatus]=="1")
			{
				/*
				$model->approvestatus=2;
				$userschedule=$this->findUserByAccount("jinjing");
				*/
				$model->approvestatus=4;
				$model->design_status="合同审核完成";
				$model->construction_time=time();
			}
			else if($info[approvestatus]=="2")
			{
				$model->approvestatus=3;
				$userschedule=$this->findUserByAccount("chenxiaohua");
			}
			else if($info[approvestatus]=="3")
			{
				$model->approvestatus=4;
				$model->design_status="合同审核完成";
				$model->construction_time=time();
			}
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同评估，结果：同意。";
			$data['receiver']=$info['charge'].$this->findNumberByNameAndRole($info['charge'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同评估，结果：同意。";
			$this->Sendmail($data);

			if(($info[approvestatus]=="1")||($info[approvestatus]=="2"))
			{
				$taskid=$info[id];
				$date=date('m-d H:i');
				$address=$info['title'];
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$address."》进行合同评估，请您进行合同评估。";
				$data['href'] ="index.php?s=Qdht/index";
				$data['taskid'] =$taskid;
				$data['type'] ="Qdht";
				
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
		}
		else
		{	//拒绝流程
			$model->handlehistory=$info['handlehistory']."合同审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
			if(!empty($_FILES['file4']['name'][0]))
			{
				$model->contracthistory=$info['contracthistory']."合同审核：".$_SESSION['loginUserName']."[".$_SESSION['dept']."]"."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date.'</br>'.$hetong.'</br>------------------</br>';
			}
			else
			{
				$model->contracthistory=$info['contracthistory']."合同审核：".$_SESSION['loginUserName']."[".$_SESSION['dept']."]"."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
			}
			$model->approvestatus="";
			$model->design_status="合同审核退回";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同评估，结果：拒绝。";
			$data['receiver']=$info['charge'].$this->findNumberByNameAndRole($info['charge'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行合同评估，结果：拒绝。";
			$this->Sendmail($data);
		}
		
		$list = $model->save();
		
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				//$this->redirect('../App/xmht');
				$this->success('操作成功!');
			}
			else
			{
				//$this->redirect('index');
				$this->success('操作成功!');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Project";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
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
                if (false !== $model->where($condition)->setField("design_status","待签订合同"))
				{
					$model->where($condition)->setField("approvestatus","0");
                    $this->success('撤销成功！');
                } else {
                    $this->error('撤销失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
}
?>