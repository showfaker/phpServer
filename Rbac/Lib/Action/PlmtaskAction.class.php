<?php
class PlmtaskAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		
		$map['projecttype'] = array("neq","承揽项目");
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
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		$map[design_status]=array("in","待施工,施工中,完成施工,竣工待验收,项目待验收,验收审核退回,完成验收");//待施工,
		//$map[activity]=array("eq","");
		//$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities();
		if($_SESSION["app"])
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
				
				$voList[$key]['outplantime2file']=explode(',',$val['outplantime2file']);
				$voList[$key]['outplantime2filename']=explode(',',$val['outplantime2filename']);
				
				
				
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
		Cookie::set('_currentUrl_', __SELF__.$p->parameter);
		
        return;
    }
	
	
	
	function add() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$mapforPlmschedule["plmid"]=$vo["id"];
		
		$vo["plantimebegin"]=M("Plmschedule")->where($mapforPlmschedule)->min("plantimebegin");
		
		$mapforPlmschedule1["plmid"]=$vo["id"];
		$mapforPlmschedule1["subworktype"]="设备送电";
		$vo["sendelcplantimebegin"]=M("Plmschedule")->where($mapforPlmschedule1)->getField("plantimebegin");
		
		
		$vo["realtimebegin"]=M("Plmschedule")->where($mapforPlmschedule)->min("realtimebegin");
		$vo["realtimeend"]=M("Plmschedule")->where($mapforPlmschedule)->min("realtimeend");
		
		$vo['outplantime2file']=explode(',',$vo['outplantime2file']);
		$vo['outplantime2filename']=explode(',',$vo['outplantime2filename']);
		
		$this->assign('orgdata', $vo);
		
		$this->assign('app', $_REQUEST["app"]);
		$this->assign('step', $_REQUEST["step"]);
		$this->assign('check', $_REQUEST["check"]);
		$this->assign('finish', $_REQUEST["finish"]);
		$this->display("addoa");
	}	
	
	function insert() {
		
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		
		
		
		
		
		$mapforPlmschedule1["plmid"]=$info["id"];
		$mapforPlmschedule1["subworktype"]="设备送电";
		$sendelcplantimebegin=M("Plmschedule")->where($mapforPlmschedule1)->getField("plantimebegin");
		if($_REQUEST["outplantime4"]>$sendelcplantimebegin)
		{
			$this->error("送电计划时间不能超过".$sendelcplantimebegin);
		}
		
		
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$savePath = '../Public/Uploads/';     //设置附件上传目录		
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
			$model->outplantime2file=$newnameall;
			$model->outplantime2filename=$filenameall;
			
			/*
			if($_SESSION[app]=="1")
				$handlehistory.=$_SESSION['loginUserName']."于".$date."上传供电方案答复单</br>------------------</br>"; 
			else
				$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了供电方案答复单</br>------------------</br>";
			*/
		}
		$model->handlehistory=$handlehistory;
		
		if(($_REQUEST["step"]=="1")&&(empty($_REQUEST["finish"])))
		{
			$model->outplanset_time=time();
			$model->outplanset_user=$_SESSION["name"];
		}
		
		$list = $model->save();
		
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
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
			//预警报警去掉
			
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
				$this->success('操作成功!');
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
				$this->success('操作成功!');
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
			$model->budgetfinalcheck=$newnameall;
			$model->budgetfinalcheckfilename=$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了审计单</br>------------------</br>"; 
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
				$this->success('操作成功!');
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
	
	public function plancomplete() {
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
				$model->where($condition)->setField("outplanset_confirm",time());
                $this->success('操作成功！');
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
				$model->where($condition)->setField("outplanfinish_time",time());
                $this->success('操作成功！');
            }
        }
        $this->forward();
    }
	
}
?>