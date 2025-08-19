<?php
class Wgys1Action extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		if($_POST['keyword'])
		{
			$map['title'] = array('like',"%".$_POST['keyword']."%");
			$this->assign("keyword",$_POST['keyword']);
		}
		if($_POST['city'])
		{
			$map['city'] = array('like',"%".$_POST['city']."%");
			$this->assign("city",$_POST['city']);
		}
		if($_POST['charge'])
		{
			$map['charge'] = array('like',"%".$_POST['charge']."%");
			$this->assign("charge",$_POST['charge']);
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
		
		$map[design_status]=array("in","待施工,施工中,完成施工,联合验收中,联合验收通过,竣工待验收,项目待验收,验收审核退回,完成验收");
		$map['projecttype'] = array("eq","承揽项目");
		
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
			
		
		
		$this->getAllcities();
		if($_SESSION[app]=="1")
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
			foreach($voList as $key => $val)
			{
				
				$voList[$key]['drawings']=explode(',',$val['drawing']);
				$voList[$key]['drawingsfilename']=explode(',',$val['drawingfilename']);
				
				$voList[$key]['finishphotos']=explode(',',$val['finishphoto']);
				$voList[$key]['finishphotosfilename']=explode(',',$val['finishphotofilename']);
				
				$voList[$key]['finishs']=explode(',',$val['finish']);
				$voList[$key]['finishsfilename']=explode(',',$val['finishfilename']);
				
				$voList[$key]['budgetsfinal']=explode(',',$val['budgetfinal']);
				$voList[$key]['budgetsfinalfilename']=explode(',',$val['budgetfinalfilename']);
				
				
				$voList[$key]['budgetsfinalcheck']=explode(',',$val['budgetfinalcheck']);
				$voList[$key]['budgetsfinalcheckfilename']=explode(',',$val['budgetfinalcheckfilename']);
				
				$voList[$key]['evaluates']=explode(',',$val['evaluate']);
				$voList[$key]['evaluatesfilename']=explode(',',$val['evaluatefilename']);
				
				$voList[$key]['contract']=explode(',',$val['contract']);
				$voList[$key]['contractfilename']=explode(',',$val['contractfilename']);
				
				
				
				
				$voList[$key]['finish01']=explode(',',$val['finish01']);
				$voList[$key]['finishfilename01']=explode(',',$val['finishfilename01']);
				$voList[$key]['finish02']=explode(',',$val['finish02']);
				$voList[$key]['finishfilename02']=explode(',',$val['finishfilename02']);
				$voList[$key]['finish03']=explode(',',$val['finish03']);
				$voList[$key]['finishfilename03']=explode(',',$val['finishfilename03']);
				$voList[$key]['finish04']=explode(',',$val['finish04']);
				$voList[$key]['finishfilename04']=explode(',',$val['finishfilename041']);
				$voList[$key]['finish05']=explode(',',$val['finish05']);
				$voList[$key]['finishfilename05']=explode(',',$val['finishfilename05']);
				
				$voList[$key]['finishphoto1']=explode(',',$val['finishphoto1']);
				$voList[$key]['finishphotofilename1']=explode(',',$val['finishphotofilename1']);
				$voList[$key]['finishphoto2']=explode(',',$val['finishphoto2']);
				$voList[$key]['finishphotofilename2']=explode(',',$val['finishphotofilename2']);
				$voList[$key]['finishphoto3']=explode(',',$val['finishphoto3']);
				$voList[$key]['finishphotofilename3']=explode(',',$val['finishphotofilename3']);
				
				$voList[$key]['budgetsfinalcheck1']=explode(',',$val['budgetfinalcheck1']);
				$voList[$key]['budgetsfinalcheckfilename1']=explode(',',$val['budgetfinalcheckfilename1']);
				$voList[$key]['budgetsfinalcheck2']=explode(',',$val['budgetfinalcheck2']);
				$voList[$key]['budgetsfinalcheckfilename2']=explode(',',$val['budgetfinalcheckfilename2']);
				$voList[$key]['budgetsfinalcheck3']=explode(',',$val['budgetfinalcheck3']);
				$voList[$key]['budgetsfinalcheckfilename3']=explode(',',$val['budgetfinalcheckfilename3']);
				$voList[$key]['budgetsfinalcheck4']=explode(',',$val['budgetfinalcheck4']);
				$voList[$key]['budgetsfinalcheckfilename4']=explode(',',$val['budgetfinalcheckfilename4']);
				$voList[$key]['budgetsfinalcheck5']=explode(',',$val['budgetfinalcheck5']);
				$voList[$key]['budgetsfinalcheckfilename5']=explode(',',$val['budgetfinalcheckfilename5']);
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
	
	
	
	function add() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$mapforPlmschedule["plmid"]=$vo["id"];
		$vo["realtimebegin"]=M("Plmschedule")->where($mapforPlmschedule)->min("realtimebegin");
		$vo["realtimeend"]=M("Plmschedule")->where($mapforPlmschedule)->min("realtimeend");
		
		$this->assign('orgdata', $vo);
		
		
		$this->assign('step', $_REQUEST["step"]);
		
		$this->display("addoa");
	}	
	function invoice_print() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$mapforPlmschedule["plmid"]=$vo["id"];
		$vo["realtimebegin"]=M("Plmschedule")->where($mapforPlmschedule)->min("realtimebegin");
		$vo["realtimeend"]=M("Plmschedule")->where($mapforPlmschedule)->min("realtimeend");
		
		$this->assign('orgdata', $vo);
		
		
		$this->assign('step', $_REQUEST["step"]);
		$this->display();
	}	
	function insert() {
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
		$savePath = '../Public/Uploads/';     //设置附件上传目录
		
		$flag=0;
		for($i=1;$i<=5;$i++)
		{
			if(!empty($_FILES['file0'.$i]['name'][0]))
			{
				$newnameall="";
				$filenameall="";
				$file=$_FILES['file0'.$i]['name'];
				$file_tmp=$_FILES['file0'.$i]['tmp_name'];
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
				$x="finish0".$i;
				$xx="finishfilename0".$i;
				$model->$x=$newnameall;
				$model->$xx=$filenameall;
				
				if($i==1)$str="监理验收文件";
				if($i==2)$str="自查验收文件";
				if($i==3)$str="整改验收报告";
				if($i==4)$str="联合验收多方签字文件";
				if($i==5)$str="工程量三方确认单";
				
				if($_SESSION[app]=="1")
					$handlehistory.=$_SESSION['loginUserName']."于".$date."上传".$str."</br>------------------</br>"; 
				else
					$handlehistory.=$_SESSION['loginUserName']."于".$date."上传".$str."</br>------------------</br>";
				$flag=1;
			}
		}
		
		
		
		
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
			if($_SESSION[app]=="1")
				$handlehistory.=$_SESSION['loginUserName']."于".$date."上传竣工验收文件</br>------------------</br>"; 
			else
				$handlehistory.=$_SESSION['loginUserName']."于".$date."上传竣工验收文件</br>------------------</br>"; 
		}
		for($i=1;$i<=3;$i++)
		{
			if(!empty($_FILES['file2'.$i]['name'][0]))
			{
				$newnameall="";
				$filenameall="";
				$file=$_FILES['file2'.$i]['name'];
				$file_tmp=$_FILES['file2'.$i]['tmp_name'];
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
				$x="finishphoto".$i;
				$xx="finishphotofilename".$i;
				$model->$x=$newnameall;
				$model->$xx=$filenameall;
				
				if($i==1)$str="竣工报告";
				if($i==2)$str="资产移交清册";
				if($i==3)$str="竣工图";
				
				if($_SESSION[app]=="1")
					$handlehistory.=$_SESSION['loginUserName']."于".$date."上传".$str."</br>------------------</br>";
				else
					$handlehistory.=$_SESSION['loginUserName']."于".$date."上传".$str."</br>------------------</br>"; 
			}
		}
		$model->handlehistory=$handlehistory;
		
		if($_REQUEST[step]=="0.1")
		{
			$model->finish_time01=time();
		}
		if($_REQUEST[step]=="0.2")
		{
			$model->finish_time02=time();
			$model->design_status="联合验收通过";
		}
		if($_REQUEST["step"]=="1")
		{
			$model->finish_time=time();
			//$model->design_status="项目待验收";
			$model->design_status="竣工待验收";
		}
		if($_REQUEST["step"]=="1.5")
		{
			if($info["design_status"]!="完成验收")
			{
				$model->finish_time=time();
				//$model->design_status="项目待验收";
				$model->design_status="完成验收";
			}
		}
		
		if($_REQUEST[step]=="2")
		{
			if($info["design_status"]!="完成验收")
			{
				$model->finish_time1=time();
				$model->design_status="完成验收";
				$handlehistory.=$_SESSION['loginUserName']."于".$date."完成验收"."</br>------------------</br>";
			}
			$mapforPlmwarning[plmid]=$model->id;
			$warnings=M("Plmwarning")->where($mapforPlmwarning)->field("id")->select();
			foreach($warnings as $key => $val)
			{
				$ids.=$val[id].",";
			}
			$mapforPlmwarning[id]=array("in",$ids);
			M("Plmwarning")->where($mapforPlmwarning)->delete();
			
		}
		$model->handlehistory=$handlehistory;
		$list = $model->save();
		
		
		
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist8'));
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
	
	
	function approve3() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		
		$this->display();
	}
	
	function approvesubmit3() {
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		
		
		if($_REQUEST[approvestatus]=="1")
		{
			$model->design_status="完成验收";
			$handlehistory.=$_SESSION['loginUserName']."于".$date."验收审核，审核结果：通过"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			$mapforPlmwarning[plmid]=$model->id;
			$warnings=M("Plmwarning")->where($mapforPlmwarning)->field("id")->select();
			foreach($warnings as $key => $val)
			{
				$ids.=$val[id].",";
			}
			$mapforPlmwarning[id]=array("in",$ids);
			M("Plmwarning")->where($mapforPlmwarning)->delete();
			
		}
		else
		{
			$model->design_status="验收审核退回";
			$handlehistory.=$_SESSION['loginUserName']."于".$date."验收审核，审核结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
		}
		$model->handlehistory=$handlehistory;
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist8'));
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
	
	function add2() {
		//$_SESSION[app]=$_REQUEST[app];
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
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了项目投运单</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$model->budgetfinal_time=time();
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist8'));
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
	
	
	function add3() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->display("add3");
	}	
	
	function insert3() {
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
		for($i=1;$i<=5;$i++)
		{
			if(!empty($_FILES['file'.$i]['name'][0]))
			{
				$newnameall="";
				$filenameall="";
				$file=$_FILES['file'.$i]['name'];
				$file_tmp=$_FILES['file'.$i]['tmp_name'];
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
				$x="budgetfinalcheck".$i;
				$xx="budgetfinalcheckfilename".$i;
				$model->$x=$newnameall;
				$model->$xx=$filenameall;
				if($i==1)$str="送审结算书";
				if($i==2)$str="现场签字审批单";
				if($i==3)$str="变更工程量审计单";
				if($i==4)$str="工程审计申请表";
				if($i==5)$str="结算表";
				$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了".$str."</br>------------------</br>"; 
			}
		}
		$model->handlehistory=$handlehistory;
		$model->budgetfinalcheck_time=time();
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist8'));
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
	
	function add4() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->display("add4");
	}	
	
	function insert4() {
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
			$model->evaluate=$newnameall;
			$model->evaluatefilename=$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了项目后评单</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$model->evaluate_time=time();
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/plmdetail',array('id'=>$_REQUEST[id],'webid'=>'programlist8'));
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
				$info=$model->where($condition)->find();
				if($info[design_status]!="暂停中")
				{
					$model->where($condition)->setField("design_status_old",$info[design_status]);
				}
				
                if (false !== $model->where($condition)->setField("design_status","暂停中"))
				{
                    //echo $model->getlastsql();
                    $this->success('暂停项目成功！');
                } else {
                    $this->error('暂停项目失败！');
                }
            } else {
                $this->error('暂停项目操作');
            }
        }
        $this->forward();
    }
	public function start() {
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
				$info=$model->where($condition)->find();
                if (false !== $model->where($condition)->setField("design_status",$info[design_status_old]))
				{
                    //echo $model->getlastsql();
                    $this->success('启动项目成功！');
                } else {
                    $this->error('启动项目失败！');
                }
            } else {
                $this->error('启动项目操作');
            }
        }
        $this->forward();
    }	
	
	public function finish0() {
        //联合验收
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
				$info=$model->where($condition)->find();
                if (false !== $model->where($condition)->setField("design_status","联合验收中"))
				{
					$model->where($condition)->setField("finish_time0",time());
                    $this->success('提交联合验收成功！');
                } else {
                    $this->error('提交联合验收失败！');
                }
            } else {
                $this->error('提交联合验收操作失败');
            }
        }
        $this->forward();
    }
	
	public function activity() {
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
				$info=$model->where($condition)->find();
                if (false !== $model->where($condition)->setField("activity","投入使用"))
				{
                    //echo $model->getlastsql();
					$model->where($condition)->setField("activity_time",time());
                    $this->success('投入使用成功！');
                } else {
                    $this->error('投入使用失败！');
                }
            } else {
                $this->error('投入使用操作');
            }
        }
        $this->forward();
    }
	
	public function advancecomplete() {
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
				$info=$model->where($condition)->find();
				if($info["design_status"]=="待施工")
				{
					$model->where($condition)->setField("design_status","施工中");
				}
				$mapforPlmschedule["plmid"]=$_REQUEST["id"];
				$plmschedules=M("Plmschedule")->where($mapforPlmschedule)->select();
				foreach($plmschedules as $key => $val)
				{
					if(empty($val["realtimebegin"]))
					{
						M("Plmschedule")->where("id=".$val["id"])->setField("realtimebegin",date("Y-m-d",time()));
					}
					if(empty($val["realtimeend"]))
					{
						M("Plmschedule")->where("id=".$val["id"])->setField("realtimeend",date("Y-m-d",time()));
					}
					M("Plmschedule")->where("id=".$val["id"])->setField("percent","100%");
					M("Plmschedule")->where("id=".$val["id"])->setField("advance",date("Y-m-d",time()));
				}
                $this->success('超前完成项目成功！');
            }
        }
        $this->forward();
    }	
}
?>