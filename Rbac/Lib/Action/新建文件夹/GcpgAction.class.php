<?php
class GcpgAction extends CommonAction {			
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
		$map[design_status]=array("in","研究中心,工程评估退回,报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
		
		$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		
		$this->getAllcities();
		if($_SESSION[app]=="1")
		{
			//$this->display("../App/xmff");
			$this->display();
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
	
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
	
		$this->display();
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
		$schedulemap[type]="Gcpg";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="同意"))/*同意*/
		{
			$model->handlehistory=$info['handlehistory']."工程评估审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：同意。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：同意。";
			$this->Sendmail($data);
			
			/*
			$taskid=$info[id];
			$date=date('m-d H:i');
			$address=$info['title'];
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$address."》进行工程评估，请您进行工程评估。";
			$data['href'] ="index.php?s=Gcpg/index";
			$data['taskid'] =$taskid;
			$data['type'] ="Gcpg";
			$data['user']=$info["draw_user"].$this->findNumberByNameAndRole($info['draw_user'],"效果图师");
			$this->Addschedule($data);
			*/
		}
		else
		{	//拒绝流程
			$model->handlehistory=$info['handlehistory']."工程评估审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：拒绝。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：拒绝。";
			$this->Sendmail($data);
		}
		
		$list = $model->save();
		
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			//$this->success('新增成功!');
			$this->redirect('index');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	
	function add1() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
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
			$data[type]="1";
			$data[filename]=$filenameall;
			$data[plmNumber]=$info[id];
			$data[loadPerson]=$_SESSION[name];
			$data[loadtime]=$date;
			$data[money]=$_REQUEST[money];
			M("Plmfile")->add($data);
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了".$_REQUEST[filetitle]."资料</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/gcpg');
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