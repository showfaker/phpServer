<?php
class QdbookAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		$map[step3]=1;
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
		
		if($_POST['owner'])
		{
			$map['owner'] = array('like',"%".$_POST['owner']."%");
			$this->assign("owner",$_POST['owner']);
		}
		if($_POST['offermoney'])
		{
			$map['offermoney'] = array('like',"%".$_POST['offermoney']."%");
			$this->assign("offermoney",$_POST['offermoney']);
		}
		if($_POST['offertime'])
		{
			$map['offertime'] = array('like',"%".$_POST['offertime']."%");
			$this->assign("offertime",$_POST['offertime']);
		}
		if($_POST['bidmoney'])
		{
			$map['bidmoney'] = array('like',"%".$_POST['bidmoney']."%");
			$this->assign("bidmoney",$_POST['bidmoney']);
		}
		if($_POST['bidtime'])
		{
			$map['bidtime'] = array('like',"%".$_POST['bidtime']."%");
			$this->assign("bidtime",$_POST['bidtime']);
		}
		if($_POST['status'])
		{
			$map['status'] = array('like',"%".$_POST['status']."%");
			$this->assign("status",$_POST['status']);
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
		if($_SESSION[role]=="拓展总监")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		//$map[design_status]=array("in","报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
		$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		
		$this->getAllcities();
		
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
			$time=time();
			foreach($voList as $key => $val)
			{
				$voList[$key]['intention']=explode(',',$val['intention']);
				$voList[$key]['intentionfilename']=explode(',',$val['intentionfilename']);
				$timelength=round(($time-$val[intentionctime])/(24*3600),0);
				if($timelength>=$val[intentiontime])
				{
					$voList[$key][timelength]=$timelength;
				}
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
		$this->assign('check',$_REQUEST[check]);
		$this->display();
	}
	
	function insert() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->step3=1;	
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
		
		$model->baojiauser=$_SESSION['loginUserName'];
		$model->yxsuser=$_SESSION['loginUserName'];
		$model->intentionctime=time();
		$date=date('m-d H:i');
		$address=$model->title;
		
		//保存当前数据对象
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."进行签订意向书</br>------------------</br>"; 
		$model->yxsapprove="0";
		$list = $model->save();
		

		
		
		$mapforProject[id]=$_REQUEST["id"];
		$plminfo=M("Project")->where($mapforProject)->find();
		M("Project")->where($mapforProject)->setField("step3","1");
		$name = "Plmoffer";
		$model = D($name);
		$model->plmNumber=$plminfo['id'];
		$model->title=$plminfo['title'];
		$model->number=$plminfo['number'];
		$model->addPerson=$_SESSION['loginUserName'];
		$model->create_time=time();
		
		$model->type=$_REQUEST['type'];
		$model->offertime=$_REQUEST['offertime'];
		$model->offermoney=$_REQUEST['offermoney'];
		
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
			$model->filenewname1=$newnameall;
			$model->file1=$filenameall;
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
			$model->filenewname2=$newnameall;
			$model->file2=$filenameall;
		}
		
		
		$mapforrepeat[plmNumber]=$plminfo["id"];
		$repeat=M("Plmoffer")->where($mapforrepeat)->find();	
		if(0)//$repeat
		{
			$model->id=$repeat[id];
			$model->save();
		}
		else
		{
			$model->add();
		}
		
		
		
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
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
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Qdbook";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if($_REQUEST[approvestatus]=="1")
		{
			$model->yxsapprove="1";
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核预算，审核结果：通过"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行意向书审核，结果：同意。";
			$data['receiver']=$info['yxsuser'].$this->findNumberByNameAndRole($info['yxsuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行意向书审核，结果：同意。";
			$this->Sendmail($data);
		}
		else
		{
			$model->yxsapprove="-1";
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核预算，审核结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行意向书审核，结果：退回。";
			$data['receiver']=$info['yxsuser'].$this->findNumberByNameAndRole($info['yxsuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行意向书审核，结果：退回。";
			$this->Sendmail($data);
			
		}
		$model->handlehistory=$handlehistory;
		$model->yxsapprover=$_SESSION['loginUserName'];
		$model->yxsapprove_time=time();
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/detail',array('id'=>$_REQUEST[id],'webid'=>"programlist4"));
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
		
}
?>