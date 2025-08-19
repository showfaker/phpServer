<?php
class CqtxAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		$map[step4]=1;
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
		if(($_POST['para1'])||($_POST['para2'])||($_POST['para3'])||($_POST['para4'])||($_POST['para5'])||($_POST['para6']))
		{
			$plmbidarray=M("Plmbid")->where($map1)->select();
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
		//$map[design_status]=array("in","待签订合同");
		$map[user]=array("neq","");
		
		$name = "Project";
		$model = D($name);
		$voList = $model->where($map)->select();
		$time=time();
		foreach ($voList as $key => $val) {
			$timelength=round(($time-$val[intentionctime])/(24*3600),0);
			if($timelength>=$val[intentiontime])
			{
				$voList[$key][timelength]=$timelength;
			}
			else
			{
				unset($voList[$key]);
			}
			$mapforPlmbid[plmNumber]=$val["id"];
			$voList[$key][plminfo]=M("Plmbid")->where($mapforPlmbid)->find();
		}
		$this->assign('list', $voList);
		
		$allprojects=M("Project")->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
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
		
		$mapforPlmbid[plmNumber]=$vo["id"];
		$plminfo=M("Plmbid")->where($mapforPlmbid)->find();
		for($i=1;$i<=31;$i++)
		{
			$title="para".$i;
			$vo[$title]=$plminfo[$title];
		}
		
		$companies=M("Company")->select();
		$this->assign('companies', $companies);
		
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
		$mapforProject[id]=$_REQUEST["id"];
		$plminfo=M("Project")->where($mapforProject)->find();
		
		$name = "Plmbid";
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
		
		$model->para21=$_REQUEST["para21"][0].",".$_REQUEST["para21"][1].",".$_REQUEST["para21"][2].",".$_REQUEST["para21"][3].",".$_REQUEST["para21"][4];
		$model->para22=$_REQUEST["para22"][0].",".$_REQUEST["para22"][1].",".$_REQUEST["para22"][2].",".$_REQUEST["para22"][3].",".$_REQUEST["para22"][4];
		$model->para23=$_REQUEST["para23"][0].",".$_REQUEST["para23"][1].",".$_REQUEST["para23"][2].",".$_REQUEST["para23"][3].",".$_REQUEST["para23"][4];
		$model->para24=$_REQUEST["para24"][0].",".$_REQUEST["para24"][1].",".$_REQUEST["para24"][2].",".$_REQUEST["para24"][3].",".$_REQUEST["para24"][4];
		$model->para25=$_REQUEST["para25"][0].",".$_REQUEST["para25"][1].",".$_REQUEST["para25"][2].",".$_REQUEST["para25"][3].",".$_REQUEST["para25"][4];
		$model->para26=$_REQUEST["para26"][0].",".$_REQUEST["para26"][1].",".$_REQUEST["para26"][2].",".$_REQUEST["para26"][3].",".$_REQUEST["para26"][4];
		$model->para27=$_REQUEST["para27"][0].",".$_REQUEST["para27"][1].",".$_REQUEST["para27"][2].",".$_REQUEST["para27"][3].",".$_REQUEST["para27"][4];
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
			$model->filenewname=$newnameall;
			$model->file=$filenameall;
			
		}
		
		
		
		$mapforrepeat[plmNumber]=$plminfo["id"];
		$repeat=M("Plmbid")->where($mapforrepeat)->find();	
		if($repeat)
		{
			$model->id=$repeat[id];
			$model->save();
		}
		else
		{
			$model->add();
		}
		
		$mapforProject["id"]=$plminfo[id];
		$date=date('m-d H:i');
		M("Project")->where($mapforProject)->setField("step4","1");
		M("Project")->where($mapforProject)->setField("bid_number",$_REQUEST["para1"]);
		M("Project")->where($mapforProject)->setField("bid_time",time());
		M("Project")->where($mapforProject)->setField("toubiaouser",$_SESSION['loginUserName']);
		//M("Project")->where($mapforProject)->setField("design_status","招标待审核");
		M("Project")->where($mapforProject)->setField("design_status","招标审核通过");
		$plminfo[handlehistory]=$plminfo['handlehistory'].$_SESSION['loginUserName']."于".$date."进行招标立项</br>------------------</br>"; 
		M("Project")->where($mapforProject)->setField("handlehistory",$plminfo[handlehistory]);
		
		
		/*
		$taskid=$plminfo[id];
		
		$address=$plminfo['title'];
		$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》招标立项，请您进行招标立项评审。";
		$data['href'] ="index.php?s=Cqtxcheck/index";
		$data['taskid'] =$taskid;
		$data['type'] ="Cqtxcheck";
		$userschedule=$this->findleader($plminfo['projecttype'],$plminfo['city']);
		$data['user']=$userschedule['nickname'].$userschedule['number'];
		$this->Addschedule($data);
		*/
		$this->success('操作成功!');
	}
		
}
?>