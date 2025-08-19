<?php
class CesuanAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		
		if($_REQUEST['title'])
		{
			$map['projectNm'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['projecttype'])
		{
			$map['projectTyp'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}
		if($_REQUEST['address'])
		{
			$map['addr'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		/*
		$alldata=M("Project")->select();
		foreach($alldata as $key => $val)
		{
			M("Project")->where("id=". $val["id"])->setField("ctime",$val["time"]);
		}
		*/
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
		
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
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
		$buildSql = M("electricity1")
			->field('id,projectNm,projectTyp,area,addr,create_time,"1" as type')
			->union(array('field'=>'id,projectNm,projectTyp,area,addr,create_time,"3" as type','table'=>'think_electricity'))
			->union(array('field'=>'id,projectNm,projectTyp,area,addr,create_time,"2" as type','table'=>'think_electricity2'))
			->buildSql(); 
		$count = M()->table($buildSql.' a')->where($map)->count();
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
			
			$voList = M()->table($buildSql.' a')->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            //echo M()->getlastsql();
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
			
		
		$model->user=$_SESSION['loginUserName'];
		//$model->charge=$_SESSION['loginUserName'];
		//$model->create_time=time();
		$model->last_time=time();
		$model->addressfull=$_REQUEST['province'].$_REQUEST['city'].$_REQUEST['area'].$_REQUEST['address'];
		
		foreach($_REQUEST[technology] as $key => $val)
		{
			$technology.=$val.",";
		}
		$model->technology=$technology;
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
			
			
			if($_REQUEST["target1"])
			{
				$model->target=$_SESSION['loginUserName']."于".$date."添加主管指示：".$_REQUEST["target1"]."</br>------------------</br>";
			}
			if($_REQUEST["target2"])
			{
				$model->target=$_SESSION['loginUserName']."于".$date."添加业务员反馈：".$_REQUEST["target2"]."</br>------------------</br>"; 
			}
			
			$list = $model->add();
			$info = M("Project")->where("id='" . $list . "'")->find();
			
			
			
			if($_REQUEST["target1"])
			{
				$data['content']=$_SESSION['loginUserName']."于".$date."在项目"."【".$info["title"]."】"."添加主管指示：".$_REQUEST["target1"];
				$data['href'] ="index.php?s=Schedule/index";
				$data['taskid'] =$list;
				$data['type'] ="Schedule";
				$userschedule=$this->findUserByName($info["name"]);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			if($_REQUEST["target2"])
			{
				$data['content']=$_SESSION['loginUserName']."于".$date."在项目"."【".$info["title"]."】"."添加业务员反馈：".$_REQUEST["target2"];
				$data['href'] ="index.php?s=Schedule/index";
				$data['taskid'] =$list;
				$data['type'] ="Schedule";
				$userschedule=$this->findUserByAccount("admin");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
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
			
			
			
			
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			$address=$info[title];
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目立项</br>------------------</br>"; 
			
			if($_REQUEST["target1"])
			{
				$model->target=$info['target'].$_SESSION['loginUserName']."于".$date."添加主管指示：".$_REQUEST["target1"]."</br>------------------</br>"; 
				
				$dataplmdiscuss["plmid"]=$_REQUEST[id];
				$dataplmdiscuss["create_time"]=time();
				$dataplmdiscuss["address"]=$_REQUEST[title];
				$dataplmdiscuss["title"]=$_REQUEST["target1"];
				$dataplmdiscuss["user"]=$_SESSION["name"];
				M("plmdiscuss")->add($dataplmdiscuss);
				
			}
			
			
			$plmdiscusses=M("Plmdiscuss")->where("plmid=".$info['id'])->select();
			foreach($plmdiscusses as $key => $val)
			{
				if($_REQUEST["target2_".$val[id]])
				{
					
					$dataplmdiscuss["id"]=$val[id];
					$dataplmdiscuss["update_time"]=time();
					$dataplmdiscuss["content"]=$_REQUEST["target2_".$val[id]];
					$dataplmdiscuss["replyuser"]=$_SESSION["name"];
					M("plmdiscuss")->save($dataplmdiscuss);
				
				
					$model->target=$info['target'].$_SESSION['loginUserName']."于".$date."添加业务员反馈：".$_REQUEST["target2_".$val[id]]."</br>"; 
				}
				if($_REQUEST["target3_".$val[id]])
				{
					$dataplmdiscuss["id"]=$val[id];
					$dataplmdiscuss["remark"]=$val['remark'].$_SESSION['loginUserName']."于".$date."添加业务员反馈：".$_REQUEST["target3_".$val[id]]."</br>";
					M("plmdiscuss")->save($dataplmdiscuss);
				}
			}
			
			
			
			$list = $model->save();
			
			/*********增加待办事项***********/
			if($_REQUEST["target1"])
			{
				$scheduledata[taskid]=$info[id];
				$scheduledata[type]="Schedule";		
				$scheduledata['content']=$_SESSION['loginUserName']."于".$date."在项目"."【".$info["title"]."】"."添加主管指示：".$_REQUEST["target1"];
				$scheduledata['href'] ="index.php?s=Schedule/index";
				$userschedule=$this->findUserByName($info["name"]);
				$scheduledata['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($scheduledata);
			}
			if($_REQUEST["target2"])
			{
				$scheduledata[taskid]=$info[id];
				$scheduledata[type]="Schedule";		
				$scheduledata['content']=$_SESSION['loginUserName']."于".$date."在项目"."【".$info["title"]."】"."添加业务员反馈：".$_REQUEST["target2"];
				$scheduledata['href'] ="index.php?s=Schedule/index";
				$userschedule=$this->findUserByAccount("admin");
				$scheduledata['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($scheduledata);
			}
			
			
			
			
		}
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
			//$this->redirect('index');
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
			$data['content']=$_SESSION['loginUserName']."于".$date."修改了《".$address."》项目立项，请您审批。";
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
		if($_REQUEST['type'] == 1){
			$name = "electricity1";
			$view = "draftfirst1";
		}elseif($_REQUEST['type'] == 2){
			$name = "electricity2";
			$view = "draftfirst2";
		}else{
			$name = "electricity";
			$view = "draftfirst";
		}
		
		$model = M($name);
		$id = $_REQUEST ['id'];
		$vo = $model->getById($id);
		
		if($_REQUEST['type'] == 1){
			
		}elseif($_REQUEST['type'] == 2){
			
		}else{
			$vo['powerList'] = json_decode($vo['powerList'],true);
			$vo['fcltList'] = json_decode($vo['fcltList'],true);
		}
		$vo['create_time'] = date("Y-m-d",$vo['create_time']);
		
		$_SESSION[curpage]=$_REQUEST[curpage];
		$this->assign('orgdata', $vo);
		$this->assign('check',$_REQUEST[check]);
	
		$this->display($view);
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
	
	
	public function resume() {
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
                if (false !== $model->where($condition)->setField("design_status","暂存"))
				{
                    //echo $model->getlastsql();
                    $this->success('恢复成功！');
                } else {
                    $this->error('恢复失败！');
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
		$model=M("Project");
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
	
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position]);
		}
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['projecttype']=$volist[$i]['projecttype'];
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
			$data[$i]['invest4']=$volist[$i]['invest4'];
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
		
		$th_array=array('项目类型','省公司','地市公司','区/县','地址','项目编号','项目名称','建设类型','场站分类','开始时间','结束时间','变压器容量（KVA）','变压器数量（台）','充电设施类型','数量（套）','单套功率（KW）','终端数（每套）（个）','单枪功率（KW）','车棚类型','储能功率（KW）','储能容量（kWh）','单车日均充电量（kWh）','服务车量数（辆）','年服务时间（天）','充电量合计（kWh）','服务费标准（元/kWh）','充电收入（万元）','充电设施（万元）','配电设施金额（万元）','土建施工综合费用（万元）','其他投资（光伏、储能等）（万元）','安装工程费（万元）','项目总投资（万元）','场地租金（购置）（万元）','外线费用（万元）','线损成本（万元）','整站运维成本（万元）','其他成本（万元）','总成本（万元）','建设内容','状态','立项时间','更新时间');
		
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
			$map['_complex'] = $this->find5level($_SESSION[position]);
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
			$map['_complex'] = $this->find5level($_SESSION[position]);
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
			$map['_complex'] = $this->find5level($_SESSION[position]);
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
			for($k=2;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
					$mapforProject["projecttype"]=$array[$k]['1'];
					$mapforProject["owner"]=$array[$k]['2'];
					$mapforProject["owner2"]=$array[$k]['3'];
					$mapforProject["province"]=$array[$k]['4'];
					$mapforProject["city"]=$array[$k]['5'];
					$mapforProject["area"]=$array[$k]['6'];
					$mapforProject["address"]=$array[$k]['7'];
					$mapforProject["number"]=$array[$k]['8'];
					$mapforProject["title"]=$array[$k]['9'];
					$mapforProject["type"]=$array[$k]['10'];
					$mapforProject["taketype"]=$array[$k]['11'];
					$mapforProject["taketype_other"]=$array[$k]['12'];
					$mapforProject["timebegin"]=$array[$k]['13'];
					$mapforProject["timeend"]=$array[$k]['14'];
					$mapforProject["chargedevice1"]=$array[$k]['15'];
					$mapforProject["chargedevice2"]=$array[$k]['16'];
					$mapforProject["chargedevice3"]=$array[$k]['17'];
					$mapforProject["chargedevice4"]=$array[$k]['18'];
					$mapforProject["chargedevice5"]=$array[$k]['19'];
					$mapforProject["chargedevice6"]=$array[$k]['20'];
					$mapforProject["chargedevice7"]=$array[$k]['21'];
					$mapforProject["energy1"]=$array[$k]['22'];
					$mapforProject["energy2"]=$array[$k]['23'];
					$mapforProject["energy3"]=$array[$k]['24'];
					$mapforProject["capital1"]=$array[$k]['25'];
					$mapforProject["capital2"]=$array[$k]['26'];
					$mapforProject["capital3"]=$array[$k]['27'];
					$mapforProject["capital4"]=$array[$k]['28'];
					$mapforProject["capital5"]=$array[$k]['29'];
					$mapforProject["capital6"]=$array[$k]['30'];
					$mapforProject["invest1"]=$array[$k]['31'];
					$mapforProject["invest2"]=$array[$k]['32'];
					$mapforProject["invest3"]=$array[$k]['33'];
					$mapforProject["invest4"]=$array[$k]['34'];
					$mapforProject["invest5"]=$array[$k]['35'];
					$mapforProject["invest6"]=$array[$k]['36'];
					$mapforProject["cost1"]=$array[$k]['37'];
					$mapforProject["cost2"]=$array[$k]['38'];
					$mapforProject["cost3"]=$array[$k]['39'];
					$mapforProject["cost4"]=$array[$k]['40'];
					$mapforProject["cost5"]=$array[$k]['41'];
					$mapforProject["cost6"]=$array[$k]['42'];
					$mapforProject["cost7"]=$array[$k]['43'];
					$mapforProject["content"]=$array[$k]['44'];
					$mapforProject["design_status"]=$array[$k]['45'];
					$mapforProject["ctime"]=$array[$k]['46'];
					$mapforProject["time"]=$array[$k]['47'];
					
					
					
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
					
					
					
					$mapforProject["name"]=$_SESSION["name"];
					$mapforProject["xiaoshouuser"]=$_SESSION["name"];
					$mapforProject["user"]=$_SESSION["name"];
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
}
?>