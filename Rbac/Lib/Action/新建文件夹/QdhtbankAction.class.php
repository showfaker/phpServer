<?php
class QdhtbankAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		$map[step5]=1;
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
			$plmbidarray=M("Plmcontract")->where($map1)->select();
			foreach($plmbidarray as $key => $val)
			{
				$ids.=$val["plmNumber"].",";
			}
			$map['id'] = array('in',$ids);
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
		
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		
		//$map[design_status]=array("in","报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'contract_number',true);
		}
		
		$alldata=$model->where($map)->field("id")->select();
		foreach($alldata as $key => $val)
		{
			$plmids.=$val[id].",";
		}
		$mapforPlmcontract[plmNumber]=array("in",$plmids);
		$total1=M("Plmcontract")->where($mapforPlmcontract)->sum("quantities1")+M("Plmcontract")->where($mapforPlmcontract)->sum("quantities2");
		$total2=M("Plmcontract")->where($mapforPlmcontract)->sum("estimate_total1")+M("Plmcontract")->where($mapforPlmcontract)->sum("estimate_total2");
		$total3=M("Plmcontract")->where($mapforPlmcontract)->sum("para15");
		
		$this->assign('total1', $total1);
		$this->assign('total2', $total2);
		$this->assign('total3', $total3);
		$this->getAllcities();
		
		$this->display("../Qdhtbank/index");
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
				$voList[$key][plminfo]=M("Plmcontract")->where($mapforPlmcontract)->find();
				
				$voList[$key][plminfo][para10]=$voList[$key][plminfo][quantities1]+$voList[$key][plminfo][quantities2];
				$voList[$key][plminfo][para13]=$voList[$key][plminfo][estimate_total1]+$voList[$key][plminfo][estimate_total2];
				
				$voList[$key][plminfo]['contract']=explode(',',$voList[$key][plminfo]['contract']);
				$voList[$key][plminfo]['contractfilename']=explode(',',$voList[$key][plminfo]['contractfilename']);
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
		$plminfo=M("Plmcontract")->where($mapforPlmcontract)->find();
		$plminfo['contract']=explode(',',$plminfo['contract']);
		$plminfo['contractfilename']=explode(',',$plminfo['contractfilename']);
		$vo[plminfo]=$plminfo;
		
		for($i=1;$i<=31;$i++)
		{
			$title="para".$i;
			$vo[$title]=$plminfo[$title];
		}
		
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
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
		
		$name = "Plmcontract";
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
				}
			}
			$model->contract=$newnameall;
			$model->contractfilename=$filenameall;
		}
		
		
		$mapforrepeat[plmNumber]=$plminfo["id"];
		$repeat=M("Plmcontract")->where($mapforrepeat)->find();	
		if($repeat)
		{
			$model->id=$repeat[id];
			$model->save();
		}
		else
		{
			$model->add();
		}
		
		
		/*
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
					
					$hetong.='[<a href="__URL__/filedown/filename/'.$newname.'/filerealname/'.$filename.'" style="color:green">合同下载</a>]';
				}
			}
			$model->clientpicture=$newnameall;
			$model->clientpicturefilename=$filenameall;
		}
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
			$model->intention=$newnameall;
			$model->intentionfilename=$filenameall;
		}
		
		*/
		//保存当前数据对象
		/*
		$info = M("Project")->where("id='" . $_REQUEST[id] . "'")->find();
		$address=$info[title];
		M("Project")->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."进行上传合同</br>------------------</br>"; 
		M("Project")->contracthistory=$info['contracthistory'].$_SESSION['loginUserName']."于".$date.'进行上传合同'.$hetong.'</br>------------------</br>';
		M("Project")->approvestatus=1;
		$list = M("Project")->save();
		*/
		
		$this->success('操作成功!');
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
		$schedulemap[type]="Qdht";
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
				$this->redirect('../App/xmht');
			}
			else
			{
				$this->redirect('index');
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
	
	
	
	public function toexcel()
	{
		$model=M("Project");
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$volist=$model->where($map)->order('contract_number asc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			$mapforPlmcontract[plmNumber]=$volist[$i]["id"];
			$plminfo=M("Plmcontract")->where($mapforPlmcontract)->find();
			
			
			$data[$i]['para2']=$plminfo['para2'];
			$data[$i]['number']=$volist[$i]['number'];
			$data[$i]['title']=$volist[$i]['title'];
			
			$data[$i]['para5']=$plminfo['para5'];
			$data[$i]['para6']=$plminfo['para6'];
			$data[$i]['para7']=$plminfo['para7'];
			$data[$i]['para8']=$plminfo['para8'];
			$data[$i]['para9']=$plminfo['para9'];
			
			/*
			$data[$i][para10]=$plminfo[quantities1]+$plminfo[quantities2];
			if(!empty($plminfo[hotprice1])&&!empty($plminfo[hotprice2]))
			{
				$data[$i][para11]="整形".$plminfo[hotprice1]."/复拌".$plminfo[hotprice2];
			}
			else
			{
				$data[$i][para11]="整形".$plminfo[hotprice1]."/复拌".$plminfo[hotprice2];
			}
			
			$data[$i][para13]=$plminfo[estimate_total1]+$plminfo[estimate_total2];
			*/
			$data[$i]['quantities1']=$plminfo['quantities1'];
			$data[$i]['hotprice1']=$plminfo['hotprice1'];
			$data[$i]['material1']=$plminfo['material1'];
			$data[$i]['estimate_total1']=$plminfo['estimate_total1'];
		
			$data[$i]['quantities2']=$plminfo['quantities2'];
			$data[$i]['hotprice2']=$plminfo['hotprice2'];
			$data[$i]['material2']=$plminfo['material2'];
			$data[$i]['estimate_total2']=$plminfo['estimate_total2'];
			
			$data[$i]['quantities3']=$plminfo['quantities3'];
			$data[$i]['hotprice3']=$plminfo['hotprice3'];
			$data[$i]['estimate_total3']=$plminfo['estimate_total3'];
	
			$data[$i]['quantities4']=$plminfo['quantities4'];
			$data[$i]['hotprice4']=$plminfo['hotprice4'];
			$data[$i]['estimate_total4']=$plminfo['estimate_total4'];
			

			$data[$i]['quantities5']=$plminfo['quantities5'];
			$data[$i]['hotprice5']=$plminfo['hotprice5'];
			$data[$i]['estimate_total5']=$plminfo['estimate_total5'];

			$data[$i]['quantities6']=$plminfo['quantities6'];
			$data[$i]['hotprice6']=$plminfo['hotprice6'];
			$data[$i]['material6']=$plminfo['material6'];
			$data[$i]['estimate_total6']=$plminfo['estimate_total6'];
			
			
			
			$data[$i]['para23']=$plminfo['para23'];//价格组成说明
			$data[$i]['para14']=$plminfo['para14'];
			$data[$i]['para15']=$plminfo['para15'];
			$data[$i]['para16']=$plminfo['para16'];
			$data[$i]['para17']=$plminfo['para17'];
			$data[$i]['para18']=$plminfo['para18'];
			$data[$i]['para19']=$plminfo['para19'];
			$data[$i]['para20']=$plminfo['para20'];
			$data[$i]['para21']=$plminfo['para21'];
			$data[$i]['para22']=$plminfo['para22'];
			
				
			
			$data[$number]['1']="总计";
			$data[$number]['2']="";
			$data[$number]['3']="";
			$data[$number]['4']="";
			$data[$number]['5']="";
			$data[$number]['6']="";
			$data[$number]['7']="";
			$data[$number]['8']="";
			$data[$number]['quantities1']+=$data[$i]['quantities1'];
			$data[$number]['hotprice1']="";
			$data[$number]['material1']="";
			$data[$number]['estimate_total1']+=$data[$i]['estimate_total1'];
			
			$data[$number]['quantities2']+=$data[$i]['quantities2'];
			$data[$number]['hotprice2']="";
			$data[$number]['material2']="";
			$data[$number]['estimate_total2']+=$data[$i]['estimate_total2'];
			
			$data[$number]['quantities3']+=$data[$i]['quantities3'];
			$data[$number]['hotprice3']="";
			$data[$number]['estimate_total3']+=$data[$i]['estimate_total3'];
	
			$data[$number]['quantities4']+=$data[$i]['quantities4'];
			$data[$number]['hotprice4']="";
			$data[$number]['estimate_total4']+=$data[$i]['estimate_total4'];
			

			$data[$number]['quantities5']+=$data[$i]['quantities5'];
			$data[$number]['hotprice5']="";
			$data[$number]['estimate_total5']+=$data[$i]['estimate_total5'];

			$data[$number]['quantities6']+=$data[$i]['quantities6'];
			$data[$number]['hotprice6']="";
			$data[$number]['material6']="";
			$data[$number]['estimate_total6']+=$data[$i]['estimate_total6'];
			
			
			$data[$number]['para23']="";
			$data[$number]['para14']+=$data[$i]['para14'];
			$data[$number]['para15']+=$data[$i]['para15'];
		}
		
		$file="合同列表";
		$title="合同列表";
		$subtitle='合同列表';
		
		$th_array=array('省份','项目编号','项目名称','合同编号','合同名称','甲方信息','合同类型','付款条件','预计工程量（m²）','预估单价（元/m²）','单价是否含料','预估额（万元）','预计工程量（m²）','预估单价（元/m²）','单价是否含料','预估额（万元）','预计工程量（m²）','预估单价（元/㎡）','预估额（万元）','预计工程量（m²）','预估单价（元/㎡）','预估额（万元）','预计工程量（m²）','预估单价（元/m²）','预估额（万元）','预计工程量（m²）','预估单价（元/m²）','单价是否含料','预估额（万元）','价格组成说明','其他总价（万元）','合同总价（万元）','工期','状态','履约情况','存放位置（原件）','存放位置（数量）','存放位置（扫描件）','备注');
		
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_qdhtbank.xls" );
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
			//$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':T'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
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
}
?>