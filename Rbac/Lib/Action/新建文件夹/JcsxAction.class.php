<?php
class JcsxAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		
		
		if($_REQUEST['title'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['number'])
		{
			$map['number'] = array('like',"%".$_REQUEST['number']."%");
			$this->assign("number",$_REQUEST['number']);
		}
		if($_REQUEST['filename'])
		{
			$map['filename'] = array('like',"%".$_REQUEST['filename']."%");
			$this->assign("filename",$_REQUEST['filename']);
		}
		if($_REQUEST['owner2'])
		{
			$map['owner2'] = array('like',"%".$_REQUEST['owner2']."%");
			$this->assign("owner2",$_REQUEST['owner2']);
		}
		/*
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}
		*/
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
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$this->assign("tab",$_REQUEST['tab']);
		//$map = $this->_search();
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
		
		if($_SESSION["dept"]!="省公司")
		{
			$mapforProject=array();
			$mapforProject['_complex'] = $this->find5level($_SESSION[position],$mapforProject);
			$projectarray=M("Project")->where($mapforProject)->field("planfile_time")->select();
			foreach($projectarray as $key => $val)
			{
				$planfile_times.=$val["planfile_time"].",";
			}
			$map["loadtime"]=array("in",$planfile_times);
		}
		
		//$map[design_status]=array("in","可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回,暂停中");
		//$map[user]=array("neq","");
		//$map[cooperate_time]=array("exp","is not null");
		$name = "Plmfilereply";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'loadtime',false);
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
				$voList[$key]['filenames']=explode(',',$val['filename']);
				$voList[$key]['newnames']=explode(',',$val['newname']);
				//$voList[$key]['plm']=str_replace(",","</br>",$voList[$key]['plm']);
				$mapforProject["id"]=array("in",$val["plmNumber"]);
				
				
				if($_SESSION[account]!="admin")
				{
					$mapforProject['_complex'] = $this->find5level($_SESSION[position],$mapforProject);
				}
				$voList[$key]['plmarray']=M("Project")->where($mapforProject)->field("title,invester")->select();
				$voList[$key]['plm']="";
				foreach($voList[$key]['plmarray'] as $key1 => $val1)
				{
					if($val1)
					{
						$voList[$key]['plm'].=$val1["title"]."(".$val1["invester"]."),";
					}
				}
				$voList[$key]['plm']=str_replace(",","</br>",$voList[$key]['plm']);
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
		$name = "Project";
		$model = M($name);
		$map['projecttype'] = array("neq","承揽项目");
		
		$plmfilereplyarray=M("Plmfilereply")->field("plmNumber")->select();
		foreach($plmfilereplyarray as $key => $val)
		{
			$plmids.=$val[plmNumber].",";
		}
		
		$map['id'] = array("not in",$plmids);
		
		$map[design_status]=array("in","可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回,暂停中");
		
		$list = $model->where($map)->select();
		$this->assign('list', $list);
		$this->display("addoa");
	} 
	function insert() {
		
		if(empty($_FILES['file']['name']))
		{
			$this->error("请上传文件");
		}
		$plmidarray=$_POST[plmid];
		foreach($plmidarray as $key => $val)
		{
			$plmids.=$val.",";
		}
		
		$data[plmNumber]=$plmids;
		$data[loadPerson]=$_SESSION["name"];
		$time=time();
		$data[loadtime]=$time;
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file']['name']))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file']['name'];
			$file_tmp=$_FILES['file']['tmp_name'];
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
			$data[newname] = $newnameall;
			$data[filename] = $filenameall;
		}
		
		
		foreach($plmidarray as $key => $val)
		{
			$name = "Project";
			$model = D($name);
			$info = M("Project")->where("id='" . $val . "'")->find();
			$plm.=$info["title"].",";
			$address=$info[title];
			$handlehistory=$info['handlehistory'];
			$date=date('Y-m-d H:i:s');
			$savePath = '../Public/Uploads/';     //设置附件上传目录		
			if(!empty($_FILES['file']['name'][0]))/*empty*/
			{
				$model->enter=$newnameall;
				$model->enterfilename=$filenameall;
				$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了综合计划复批文件</br>------------------</br>"; 
			}
			$model->handlehistory=$handlehistory;
			$model->planfile_time=$time;
			$model->step3=0.5;
			$model->id=$val;
			$model->save();
		}
		$data[plm]=$plm;
		M("Plmfilereply")->add($data);
		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		$this->success('操作成功!');
	}
	function edit() {
		
		$name = "Plmfilereply";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
		
		
		$name = "Project";
		$model = M($name);
		$map['projecttype'] = array("neq","承揽项目");
		//$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		$mapforplmfilereply["id"]=array("neq",$_REQUEST["id"]);
		$plmfilereplyarray=M("Plmfilereply")->where($mapforplmfilereply)->field("plmNumber")->select();
		foreach($plmfilereplyarray as $key => $val)
		{
			$plmids.=$val[plmNumber].",";
		}
		$map['id'] = array("not in",$plmids);
		
		$map[design_status]=array("in","可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回,暂停中");
		
		$list = $model->where($map)->select();
		$this->assign('list', $list);
		
		$this->display();
	} 
	function update() {
		
		$name = "Plmfilereply";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
		
		
		$plmidarray=$_POST[plmid];
		foreach($plmidarray as $key => $val)
		{
			$plmids.=$val.",";
		}
		$data[id]=$vo["id"];
		$data[plmNumber]=$plmids;
		$data[updateuser]=$_SESSION["name"];
		$time=time();
		$data[updatetime]=$time;
		$data[loadtime]=$time;
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file']['name'];
			$file_tmp=$_FILES['file']['tmp_name'];
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
			$data[newname] = $newnameall;
			$data[filename] = $filenameall;
		}
		else
		{
			$newnameall=$vo["newname"];
			$filenameall=$vo["filename"];
		}
		
		foreach($plmidarray as $key => $val)
		{
			$name = "Project";
			$model = D($name);
			$info = M("Project")->where("id='" . $val . "'")->find();
			$plm.=$info["title"].",";
			$address=$info[title];
			$handlehistory=$info['handlehistory'];
			$date=date('Y-m-d H:i:s');
			$savePath = '../Public/Uploads/';     //设置附件上传目录		
			if(1)/*empty*///!empty($_FILES['file']['name'][0])
			{
				$model->enter=$newnameall;
				$model->enterfilename=$filenameall;
				$handlehistory.=$_SESSION['loginUserName']."于".$date."设置了综合计划复批文件</br>------------------</br>";
			}
			$model->handlehistory=$handlehistory;
			$model->planfile_time=$time;
			$model->step3=0.5;
			$model->id=$val;
			$model->save();
		}
		$data[plm]=$plm;
		M("Plmfilereply")->save($data);
		
		$this->success('操作成功!');
	}
	public function foreverdelete() {
        //删除指定记录
        $name = "Plmfilereply";
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
				$plmidarray=explode(",",$info[plmNumber]);
				foreach($plmidarray as $key => $val)
				{
					$name = "Project";
					$modelproject = D($name);
					$info = M("Project")->where("id='" . $val . "'")->find();
					$address=$info[title];
					$handlehistory=$info['handlehistory'];
					$date=date('Y-m-d H:i:s');
					
					$modelproject->enter="";
					$modelproject->enterfilename="";
					$handlehistory.=$_SESSION['loginUserName']."于".$date."删除了综合计划复批文件</br>------------------</br>"; 
					
					$modelproject->handlehistory=$handlehistory;
					//$modelproject->planfile_time=time();
					//$modelproject->step3=1;
					$modelproject->id=$val;
					$modelproject->save();
				}
                if (false !== $model->where($condition)->delete())
				{
                    //echo $model->getlastsql();
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('发生错误');
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



	function add1() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->assign('tab', $_REQUEST["tab"]);
		$this->display("add1");
	}	
	
	function insert1() {
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
			$data[title]=$_REQUEST[filetitle];
			$data[newname]=$newnameall;
			$data[type]="2";
			$data[filename]=$filenameall;
			$data[plmNumber]=$info[id];
			$data[loadPerson]=$_SESSION[name];
			$data[loadtime]=$date;
			$data[money]=$_REQUEST[money];
			$data[remark]=$_REQUEST[remark];
			M("Plmfile")->add($data);
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了".$_REQUEST[filetitle]."资料</br>------------------</br>";
		}
		$model->handlehistory=$handlehistory;
		$list = $model->save();
		if ($list !== false) { //保存成功
			if(0)
			{
				$this->redirect('../App/gcpg');
			}
			else
			{
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('操作成功');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	function approvedetail() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		
		$mapforplmfile[plmNumber]=$vo[id];
		$files=M("Plmfile")->where($mapforplmfile)->select();
		foreach($files as $key=>$val)
		{
			$files[$key][filenamearray]=explode(",",$files[$key][filename]);
			$files[$key][newnamearray]=explode(",",$files[$key][newname]);
		}
		$this->assign('files', $files);
		
		$this->display("approvedetail");
	}		
}
?>