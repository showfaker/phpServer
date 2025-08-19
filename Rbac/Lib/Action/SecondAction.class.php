<?php
class SecondAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map[step1]=1;
		if($_REQUEST['title'])
		{
			$map['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
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
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		$this->getAllprojects();
		$this->draftfirst();
		return;
	}
	public function index0() {
		$this->getAllprojects();
		$this->draftfirst(1);
		return;
	}
	public function index1() {
		$this->getAllprojects();
		$this->draftfirst(2);
		return;
	}
	public function index2() {
		$this->getAllprojects();
		$this->draftfirst(3);
		return;
	}
	public function index3() {
		$this->getAllprojects();
		$this->draftfirst();
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
		$model->step1=1;
		$model->xiaoshouuser=$_SESSION['loginUserName'];
		$model->department=$_SESSION['dept'];
		$model->user=$_SESSION['loginUserName'];
		$model->ctime=date("Y-m-d");
		$model->create_time=time();
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
		
		//保存当前数据对象
		if(empty($_REQUEST[id]))
		{
			$model->handlehistory=$_SESSION['loginUserName']."于".$date."创建了项目初申</br>------------------</br>";
			$list = $model->add();
			$info = M("Project")->where("id='" . $list . "'")->find();
		}
		else
		{
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			$address=$info[title];
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目初申</br>------------------</br>"; 
			$list = $model->save();
			$list = $_REQUEST["id"];
		}
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('App/detail&check=1&id='.$list);
				return;
			}
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
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
		$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目初申</br>------------------</br>"; 
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
			$data['content']=$_SESSION['loginUserName']."于".$date."修改了《".$address."》项目初申，请您审核。";
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
	
	function draftfirst($flag) {
		
		$this->assign('steponeflag', "1");
		
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		
		
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%GC".$thisyear."%");
		$mapfororder["projecttype"]=array("like","%%");
		//$todaycount=M("Project")->where($mapfororder)->count();
		$todaycount=M("Project")->where($mapfororder)->max("number");
		/*
		$todaycount=$todaycount+1;
		if($todaycount<10)$todaycount="000".$todaycount;
		else if($todaycount<100)$todaycount="00".$todaycount;
		else if($todaycount<1000)$todaycount="0".$todaycount;
		else if($todaycount<10000)$todaycount="".$todaycount;
		$thisorder="GC".date("Y").$todaycount;
		*/
		$thisorder="GC".(str_replace("GC","",$todaycount)+1);
		if(empty($todaycount))
		{
			$thisorder="GC".date("Y")."0001";
		}
		
		$this->assign('thisorder', $thisorder);
	
	
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$vo['keeponrecord1']=explode(',',$vo['keeponrecord1']);
		$vo['keeponrecord1filename']=explode(',',$vo['keeponrecord1filename']);
		
		$vo['keeponrecord2']=explode(',',$vo['keeponrecord2']);
		$vo['keeponrecord2filename']=explode(',',$vo['keeponrecord2filename']);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		
	
		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		
		
		if($flag==1)
		{
			$this->display("index");
			return;
		}
		if($flag==2)
		{
			$this->display("index1");
			return;
		}
		if($flag==3)
		{
			$this->display("index2");
			return;
		}
		
		
		if(($_SESSION["projecttype"]=="充电建设")||($_SESSION["projecttype"]=="充电建设,换电建设")||($_SESSION["projecttype"]=="充电建设,低速车建设"))
		{
			$this->display("index");
			return;
		}
		else if(($_SESSION["projecttype"]=="换电建设")||($_SESSION["projecttype"]=="换电建设,低速车建设"))
		{
			$this->display("index1");
			return;
		}
		else if($_SESSION["projecttype"]=="低速车建设")
		{
			$this->display("index2");
			return;
		}
		else
		{
			$this->display();
			return;
		}
		
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
		$model=M("Project");
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['number']=$volist[$i]['number'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['owner']=$volist[$i]['owner'];
			$data[$i]['address']=$volist[$i]['province'].$volist[$i]['city'].$volist[$i]['area'].$volist[$i]['address'];
			$data[$i]['charge']=$volist[$i]['charge'];
			$data[$i]['director']=$volist[$i]['director'];
			$data[$i]['type']=$volist[$i]['type'];
			$data[$i]['taketype']=$volist[$i]['taketype'];
			$data[$i]['technology']=$volist[$i]['technology'];
			$data[$i]['unit']=$volist[$i]['unit'];
			
		
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
			$data[$i]['entrancefee3']=$volist[$i]['entrancefee3'];
			$data[$i]['estimate_total3']=$volist[$i]['estimate_total3'];
			
	
			$data[$i]['quantities4']=$volist[$i]['quantities4'];
			$data[$i]['hotprice4']=$volist[$i]['hotprice4'];
			$data[$i]['entrancefee4']=$volist[$i]['entrancefee4'];
			$data[$i]['estimate_total4']=$volist[$i]['estimate_total4'];
			

			$data[$i]['quantities5']=$volist[$i]['quantities5'];
			$data[$i]['hotprice5']=$volist[$i]['hotprice5'];
			$data[$i]['entrancefee5']=$volist[$i]['entrancefee5'];
			$data[$i]['estimate_total5']=$volist[$i]['estimate_total5'];
			

			$data[$i]['quantities6']=$volist[$i]['quantities6'];
			$data[$i]['hotprice6']=$volist[$i]['hotprice6'];
			$data[$i]['material6']=$volist[$i]['material6'];
			$data[$i]['entrancefee6']=$volist[$i]['entrancefee6'];
			$data[$i]['estimate_total6']=$volist[$i]['estimate_total6'];
			
			
			$data[$i]['other_estimate_total']=$volist[$i]['other_estimate_total'];
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			$data[$i]['estimate_signtime']=$volist[$i]['estimate_signtime'];
			$data[$i]['estimate_intime']=$volist[$i]['estimate_intime'];
			$data[$i]['dealpercent']=$volist[$i]['dealpercent'];
			$data[$i]['deallevel']=$volist[$i]['deallevel'];
			$data[$i]['bid']=$volist[$i]['bid'];
			$data[$i]['progress']=$volist[$i]['progress'];
			$data[$i]['keyman']=$volist[$i]['keyman'];
			$data[$i]['qualifications']=$volist[$i]['qualifications'];
			$data[$i]['bidmeans']=$volist[$i]['bidmeans'];
			$data[$i]['design_institute']=$volist[$i]['design_institute'];
			$data[$i]['designer']=$volist[$i]['designer'];
			$data[$i]['fundsource']=$volist[$i]['fundsource'];
			$data[$i]['hardness']=$volist[$i]['hardness'];
			$data[$i]['target']=$volist[$i]['target'];
			$data[$i]['status']=$volist[$i]['status'];
			//$data[$i]['dept']=M("Dept")->where("id=".$volist[$i]['department'])->getField("name");
			//$data[$i]['role']=M("Role")->where("id=".$volist[$i]['position'])->getField("name");
		}
		
		$file="项目列表";
		$title="项目列表";
		$subtitle='项目列表';
		
		$th_array=array('项目编号','项目名称','业主名称','项目地址','项目负责人','直接主管','项目类型','承接方式','主要工艺','计量单位','预计工程量（m²）','预估单价（元/m²）','单价是否含料','进场费（万元）','预估额（万元）','预计工程量（m²）','预估单价（元/m²）','单价是否含料','进场费（万元）','预估额（万元）','预计工程量（m²）','预估单价（元','进场费（万元）','预估额（万元）','预计工程量（元/m²）','预估单价（元）','进场费（万元）','预估额（万元）','预计工程量（元/m²）','预估单价（元）','进场费（万元）','预估额（万元）','预计工程量（m²）','预估单价（元/m²）','单价是否含料','进场费（万元）','预估额（万元）','其他工程预估额（万元）','合计','预计签约时间','预计施工进场时间','成交把握度（%）','成交优先级','是否招投标','最新进展','关键人','企业资信','控标手段','设计院','设计师','资金来源','难点或威胁','主管指示及业务员反馈','状态');
		
		
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
		$map["status"]="进行中";
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['xuhao']=$i+1;
			$data[$i]['charge']=$volist[$i]['charge'];
			$data[$i]['director']=$volist[$i]['director'];
			$data[$i]['title']=$volist[$i]['title'];
			
			$data[$i]['quantities11']=$volist[$i]['quantities1']."/".$volist[$i]['quantities2'];
			$data[$i]['hotprice11']=$volist[$i]['hotprice1']."/".$volist[$i]['hotprice2'];
			$data[$i]['estimate_total11']=$volist[$i]['estimate_total1']."/".$volist[$i]['estimate_total2'];
			
			$data[$i]['estimate_total33']=$volist[$i]['estimate_total3']+$volist[$i]['estimate_total4']+$volist[$i]['estimate_total5']+$volist[$i]['estimate_total6']+$volist[$i]['other_estimate_total'];
			
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			$data[$i]['estimate_signtime']=$volist[$i]['estimate_signtime'];
			$data[$i]['estimate_intime']=$volist[$i]['estimate_intime'];
			
			$data[$i]['dealpercent']=$volist[$i]['dealpercent'];
			$data[$i]['deallevel']=$volist[$i]['deallevel'];
			$data[$i]['progress']=$volist[$i]['progress'];
			$data[$i]['technology']=$volist[$i]['technology'];
			$data[$i]['owner']=$volist[$i]['owner'];
			$data[$i]['keyman']=$volist[$i]['keyman'];
			$data[$i]['qualifications']=$volist[$i]['qualifications'];
			$data[$i]['bidmeans']=$volist[$i]['bidmeans'];
			$data[$i]['design_institute']=$volist[$i]['design_institute'];
			$data[$i]['designer']=$volist[$i]['designer'];
			
			
			
		}
		$file="进行中项目列表";
		$title="进行中项目列表";
		$subtitle='进行中项目列表';
		
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
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['xuhao']=$i+1;
			$data[$i]['charge']=$volist[$i]['charge'];
			$data[$i]['director']=$volist[$i]['director'];
			$data[$i]['title']=$volist[$i]['title'];
			
			$data[$i]['quantities11']=$volist[$i]['quantities1']."/".$volist[$i]['quantities2'];
			$data[$i]['hotprice11']=$volist[$i]['hotprice1']."/".$volist[$i]['hotprice2'];
			$data[$i]['estimate_total11']=$volist[$i]['estimate_total1']."/".$volist[$i]['estimate_total2'];
			
			$data[$i]['estimate_total33']=$volist[$i]['estimate_total3']+$volist[$i]['estimate_total4']+$volist[$i]['estimate_total5']+$volist[$i]['estimate_total6']+$volist[$i]['other_estimate_total'];
			
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			$data[$i]['estimate_signtime']=$volist[$i]['estimate_signtime'];
			$data[$i]['status']=$volist[$i]['status'];
			
			$data[$i]['qualifications']=$volist[$i]['qualifications'];
			
			$data[$i]['null']="";
			$data[$i]['technology']=$volist[$i]['technology'];
			$data[$i]['remark']=$volist[$i]['remark'];
			
			
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
		$volist=$model->where($map)->order('create_time desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$data[$i]['xuhao']=$i+1;
			$data[$i]['charge']=$volist[$i]['charge'];
			$data[$i]['director']=$volist[$i]['director'];
			$data[$i]['title']=$volist[$i]['title'];
			
			$data[$i]['quantities11']=$volist[$i]['quantities1']."/".$volist[$i]['quantities2'];
			$data[$i]['hotprice11']=$volist[$i]['hotprice1']."/".$volist[$i]['hotprice2'];
			$data[$i]['estimate_total11']=$volist[$i]['estimate_total1']."/".$volist[$i]['estimate_total2'];
			
			$data[$i]['estimate_total33']=$volist[$i]['estimate_total3']+$volist[$i]['estimate_total4']+$volist[$i]['estimate_total5']+$volist[$i]['estimate_total6']+$volist[$i]['other_estimate_total'];
			
			$data[$i]['estimate_total']=$volist[$i]['estimate_total'];
			$data[$i]['cancel_time']=$volist[$i]['cancel_time'];
			$data[$i]['cancel_reason']=$volist[$i]['cancel_reason'];
			
			
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
	public function findposition() 
	{	$lat=json_encode($_REQUEST[lat]);
	    $lng=json_encode($_REQUEST[lng]);
		$this->assign('lat', $lat);
		$this->assign('lng', $lng);
		$this->display();
	}
}
?>