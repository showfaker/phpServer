<?php
class PlmdiscussAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		//$map[step6]=1;
		$map['projecttype'] = array("neq","承揽项目");
		if($_REQUEST['address'])
		{
			$map['title'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		if($_REQUEST['city'])
		{
			$map['city'] = array('like',"%".$_REQUEST['city']."%");
			$this->assign("city",$_REQUEST['city']);
		}
		if($_REQUEST['owner'])
		{
			$map['owner'] = array('like',"%".$_REQUEST['owner']."%");
			$this->assign("owner",$_REQUEST['owner']);
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
			$map['status'] = array('like',"%".$_REQUEST['status']."%");
			$this->assign("status",$_REQUEST['status']);
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
		
		$map[design_status]=array("not in","取消,暂停中,暂存");
		
		if($_REQUEST['projecttype']){
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}

		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		$this->getAllcities(1);
		if($_REQUEST["plmid"]){
			$plminfo=M("Project")->where("id=".$_REQUEST["plmid"])->find();
			$plminfo["discusscount"]=M("Plmdiscuss")->where("plmid=".$_REQUEST["plmid"])->count();
			$this->assign("plminfo", $plminfo);
			$this->assign("plmid",$_REQUEST["plmid"]);
		}
		
		if($_SESSION[app]){
			$this->display(indexapp);
		}else{
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
		            // echo $model->getlastsql();
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            }else{
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            //分页查询数据
			if($_SESSION['curpage']!=null){
				$p->nowPage=$_SESSION['curpage'];		
				$p->firstRow=($_SESSION['curpage']-1)*($p->listRows);
				unset($_SESSION['curpage']);
			}
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			foreach ($voList as $key => $val){
				$voList[$key]["discusscount"]=M("Plmdiscuss")->where("plmid=".$val["id"])->count();
			}	
				
			if(!$_REQUEST["plmid"]){
				$plminfo=M("Project")->where("id=".$voList[0]["id"])->find();
				$plminfo["discusscount"]=M("Plmdiscuss")->where("plmid=".$_REQUEST["plmid"])->count();
				$this->assign("plminfo", $plminfo);
				$this->assign("plmid",$voList[0]["id"]);
				$_REQUEST["plmid"]=$voList[0]["id"];
			}
			
			
			$plmdiscusses=M("Plmdiscuss")->where("plmid=".$_REQUEST["plmid"])->select();
			foreach($plmdiscusses as $key => $val){
				if($val['atuser']){
					$plmdiscusses[$key]['at'] = explode(",",$val["atuser"]);
				}
				$plmdiscusses[$key]["replys"]=M("Plmdiscussreply")->where("discussid=".$val["id"])->select();
				foreach($plmdiscusses[$key]["replys"] as $k => $v){
					if($v["atuser"]){
						$plmdiscusses[$key]['replys'][$k]['at'] = explode(",",$v["atuser"]);
					}
				}
			}
			
			$this->assign("plmdiscusses", $plmdiscusses);
			
			
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
			if($_REQUEST['address']){
				$p->parameter .= "address=" . urlencode($_REQUEST['address']) . "&";
			}
			if($_REQUEST['plmid']){
				$p->parameter .= "plmid=" . urlencode($_REQUEST['plmid']) . "&";
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

	public function plmdiscuss() {
		$name = "plmdiscuss";
		$model = D($name);
		$plminfo["discusscount"] = M("plmdiscuss")->count();
		$this->assign("plminfo", $plminfo);
		if($plminfo["discusscount"] > 0){
			import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            }else{
                $listRows = '';
            }
            $p = new Page($plminfo["discusscount"], $listRows);
            //分页查询数据
			if($_SESSION['curpage']!=null){
				$p->nowPage=$_SESSION['curpage'];		
				$p->firstRow=($_SESSION['curpage']-1)*($p->listRows);
				unset($_SESSION['curpage']);
			}
			$plmdiscusses = M("Plmdiscuss")->order("create_time desc")->limit($p->firstRow . ',' . $p->listRows)->select();
			foreach ($plmdiscusses as $key => $val){
				if($val['atuser']){
					$plmdiscusses[$key]['at'] = explode(",",$val["atuser"]);
				}
				$plmdiscusses[$key]["replys"]=M("Plmdiscussreply")->where("discussid=".$val["id"])->order("create_time desc")->select();
				foreach($plmdiscusses[$key]["replys"] as $k => $v){
					if($v["atuser"]){
						$plmdiscusses[$key]['replys'][$k]['at'] = explode(",",$v["atuser"]);
					}
				}
			}
			$this->assign("plmdiscusses", $plmdiscusses);
			$page = $p->show();
			$this->assign('countnumber', $plminfo["discusscount"]);
			$this->assign("totalCount", $p->totalRows);
			$this->assign("numPerPage", $p->listRows);
			$this->assign("currentPage", $p->nowPage);
			$this->assign("page", $page);
		}
		
		
		$this->display();
	}
	
	function ajax1(){
		$titlerepeat["title"]=array("eq",$_REQUEST[title]);
		$ifrepeat=M("Project")->where($titlerepeat)->find();
		if(!empty($ifrepeat)){
			echo "0";
		}else{
			echo "1";
		}
	}


	public function add(){
		$this->assign('plmid', $_REQUEST["plmid"]);
        $this->display();
	}

	function insert() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		M("Project")->where("id=".$_REQUEST[plmid])->setField("last_time",time());
		$dataplmdiscuss["plmid"]=$_REQUEST[plmid];
		$dataplmdiscuss["create_time"]=time();
		$dataplmdiscuss["address"]=$plminfo[title];
		$dataplmdiscuss["title"]=$_REQUEST["title"];
		$dataplmdiscuss["content"]=$_REQUEST["content"];
		$dataplmdiscuss["user"]=$_SESSION["name"];
		$dataplmdiscuss["atuser"]=$_REQUEST["atuser"];
		// $dataplmdiscuss["at"]=M("user")->where("nickname='".$_REQUEST['atuser']."'")->getField("id");
		M("plmdiscuss")->add($dataplmdiscuss);
		
		if($_REQUEST["atuser"])
		{
			$datamail['content']=$_SESSION["name"]."于".$date."在《".$plminfo["title"]."》发布了一条信息并@了您";
			$datamail['receiver']=$_REQUEST["atuser"].$this->findNumberByNameAndRole($_REQUEST["atuser"]).",";
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."在《".$plminfo["title"]."》发布了一条信息并@了您";
			$this->Sendmail($datamail);
		}
		
		
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
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
	public function reply(){
		$this->assign('id', $_REQUEST["id"]);
		
		$name = "plmdiscuss";
		$model = M($name);
		$id = $_REQUEST[$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('vo', $vo);
		
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		
        $this->display();
	}
	
	function replysubmit() {
		$discussinfo=M("plmdiscuss")->where("id=".$_REQUEST[id])->find();
		$dataplmdiscuss["discussid"]=$discussinfo[id];
		$dataplmdiscuss["plmid"]=$discussinfo[plmid];
		$dataplmdiscuss["create_time"]=time();
		$dataplmdiscuss["address"]=$discussinfo[address];
		$dataplmdiscuss["title"]=$_REQUEST["title"];
		$dataplmdiscuss["content"]=$_REQUEST["content"];
		$dataplmdiscuss["user"]=$_SESSION["name"];
		$dataplmdiscuss["atuser"]=$_REQUEST["atuser"];
		// $dataplmdiscuss["at"]=M("user")->where("nickname='".$_REQUEST['atuser']."'")->getField("id");
		M("plmdiscussreply")->add($dataplmdiscuss);
		
		
		$plminfo=M("Project")->where("id=".$discussinfo[plmid])->find();
		
		if($_REQUEST["atuser"])
		{
			$number=M("user")->where("nickname='".$_REQUEST['atuser']."'")->getField("number");
			
			$date=date("Y-m-d H:i:s");
			$datamail['content']=$_SESSION["name"]."于".$date."在《".$plminfo["title"]."》协同中@了您。";
			$datamail['receiver']=$_REQUEST["atuser"].$number.",";
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."在《".$plminfo["title"]."》协同中@了您。";
			$this->Sendmail($datamail);
		}
		
		
		
		if ($list !== false) { //保存成功
			$this->success('操作成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
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
	
	public function changestatus(){
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
			
			
		}else{
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
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('type',$_REQUEST[type]);
		$this->assign('check',$_REQUEST[check]);
		$this->display();
	}
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Plmdiscussreply";
        $model = M($name);
        if (false !== $model->where("id=".$_REQUEST["id"])->delete())
		{
			$this->success('删除成功！');
		} else {
			$this->error('删除失败！');
		}
        $this->forward();
    }
	
	
	public function support() {
        //删除指定记录
        $name = "Plmdiscuss";
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
			
			
			$vo=M("Plmdiscuss")->where("id=".$_REQUEST [$pk])->find();
			if(false!==strstr($vo[supportpersons],$_SESSION["name"].","))
			{
				
			}
			else
			{
				M("Plmdiscuss")->where("id=".$_REQUEST [$pk])->setInc("support");
				M("Plmdiscuss")->where("id=".$_REQUEST [$pk])->setField("supportpersons",$vo["supportpersons"].$_SESSION["name"].",");
				$vo[support]++;
			}
			
			echo $vo[support];
        }
        
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

	function getuser(){
		if($_REQUEST['nickname']) {
			$where['nickname'] = array("like","%".$_REQUEST['nickname']."%");
		}
		$user = M("user")->where($where)->field("id,nickname")->select();
		return $this->send_success("获取成功",$user);
	}
}
?>