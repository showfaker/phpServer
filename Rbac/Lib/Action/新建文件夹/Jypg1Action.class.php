<?php
class Jypg1Action extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		$map[step2]=1;
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
		if($_POST['communication'])
		{
			$map['communication'] = array('like',"%".$_POST['communication']."%");
			$this->assign("communication",$_POST['communication']);
		}
		if($_POST['road'])
		{
			$map['road'] = array('like',"%".$_POST['road']."%");
			$this->assign("road",$_POST['road']);
		}
	}
	
	function getAllcities($flag)
	{
		//$map["design_status"]=array("eq","储备");
		$map[design_status]=array("in","储备,暂存,初步申报待审批,初步申报审批中,初步申报审批中,初步申报审批通过,初步申报审批退回,项目计划待审批,项目计划审批中,项目计划审批通过,项目计划审批退回,初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回");
		$allprojects=M("Project")->where($map)->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
	}	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		$filedown=M("Filesetting")->find();
		$this->assign('filedown', $filedown);
		
		$this->getAllprojects();
		$this->draftfirst();
		Cookie::set('_currentUrl_', __SELF__);
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
		
		$map[design_status]=array("in","销售中心,经营评估退回,研究中心,工程评估退回,报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
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
			foreach($voList as $key => $val)
			{
				$voList[$key]['programme']=explode(',',$val['programme']);
				$voList[$key]['programmefilename']=explode(',',$val['programmefilename']);
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
		
		$vo['programme']=explode(',',$vo['programme']);
		$vo['programmefilename']=explode(',',$vo['programmefilename']);
		
		$vo['programme2']=explode(',',$vo['programme2']);
		$vo['programmefilename2']=explode(',',$vo['programmefilename2']);
		
		$vo['programme3']=explode(',',$vo['programme3']);
		$vo['programmefilename3']=explode(',',$vo['programmefilename3']);
		
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
		$model->step2=1;	
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		
		$model->research_time1=time();
		$model->yanjiuuser1=$_SESSION['loginUserName'];
		$model->design_status="可研评审报告待审批";
		
		$date=date('m-d H:i');
		$address=$model->title;
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
			$newnameall=$info["programme"];
			$filenameall=$info["programmefilename"];
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
			
			
			foreach($file as $key=>$val)
			{
				$size = $_FILES['file1']['size'][$key]; //文件大小
				$MAXIMUM_FILESIZE = 1;	
				if($size>$MAXIMUM_FILESIZE)
				{
					$this->error('上传的文件大小超过50M限制');
				}
			}
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					
					$allowedExts = array("pdf");
					if(!in_array($ext, $allowedExts))
					{
						$this->error('请上传pdf文件!');
					}
					
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
			$model->programme=$newnameall;
			$model->programmefilename=$filenameall;
		}
		
		
		if(!empty($_FILES['file2']['name'][0]))
		{
			$newnameall=$info["programme2"];
			$filenameall=$info["programmefilename2"];
			$file=$_FILES['file2']['name'];
			$file_tmp=$_FILES['file2']['tmp_name'];
			
			foreach($file as $key=>$val)
			{
				$size = $_FILES['file2']['size'][$key]; //文件大小
				$MAXIMUM_FILESIZE = 50 * 1024 * 1024;
				if($size>$MAXIMUM_FILESIZE)
				{
					$this->error('上传的文件大小超过50M限制');
				}
			}
			
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					
					$allowedExts = array("pdf");
					if(!in_array($ext, $allowedExts))
					{
						$this->error('请上传pdf文件!');
					}
					
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
			$model->programme2=$newnameall;
			$model->programmefilename2=$filenameall;
		}
		
		if(!empty($_FILES['file3']['name'][0]))
		{
			$newnameall=$info["programme3"];
			$filenameall=$info["programmefilename3"];
			$file=$_FILES['file3']['name'];
			$file_tmp=$_FILES['file3']['tmp_name'];
			
			foreach($file as $key=>$val)
			{
				$size = $_FILES['file3']['size'][$key]; //文件大小
				$MAXIMUM_FILESIZE = 50 * 1024 * 1024;	
				if($size>$MAXIMUM_FILESIZE)
				{
					$this->error('上传的文件大小超过50M限制');
				}
			}
			
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					
					$allowedExts = array("pdf");
					if(!in_array($ext, $allowedExts))
					{
						$this->error('请上传pdf文件!');
					}
					
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
			$model->programme3=$newnameall;
			$model->programmefilename3=$filenameall;
		}
		
		
		$address=$info[title];
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Jypg";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."进行可研评审报告设置</br>------------------</br>";
		
		
		
		/*
		命名规则	JS+地市拼音首字母+投资类型（S,Z,H）-日期+0001（自增）
		*/
		$cityarray["南京"]="NJ";
		$cityarray["徐州"]="XZ";
		$cityarray["扬州"]="YZ";
		$cityarray["泰州"]="TZ";
		$cityarray["镇江"]="ZJ";
		$cityarray["常州"]="CZ";
		$cityarray["苏州"]="SZ";
		$cityarray["南通"]="NT";
		$cityarray["淮安"]="HA";
		$cityarray["连云港"]="LYG";
		$cityarray["宿迁"]="SQ";
		$cityarray["盐城"]="YC";
		$cityarray["无锡"]="WX";
		
		//省投资（S），自投资（Z），合作投资（H）按照比例进行投资
		$investtype["省投资"]="S";
		$investtype["自投资"]="Z";
		$investtype["合作投资"]="H";
		
		$mapfororder["number"]=array("like","%JS%");
		$todaycount=M("Project")->where($mapfororder)->max("numbernew");
		
		$todaycount=$todaycount+1;
		if($todaycount<10)$todaycount="000".$todaycount;
		else if($todaycount<100)$todaycount="00".$todaycount;
		else if($todaycount<1000)$todaycount="0".$todaycount;
		else if($todaycount<10000)$todaycount="".$todaycount;
		
		
		$city=str_replace("市","",$info["city"]);
		$invester=$info["invester"];
		$thisorder="JS".$cityarray[$city].$investtype[$invester]."-".date("Ymd").$todaycount;
		
		$model->number=$thisorder;
		$model->numbernew=$todaycount;
		$model->numberold=$$info["number"];
		
		
		
		$list = $model->save();
		
		
		$taskid=$info[id];
		$date=date('m-d H:i');
		$address=$info['title'];
		$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告，请您进行可研评审报告审批。";
		$data['href'] ="index.php?s=Jypgcheck1/index";
		$data['taskid'] =$taskid;
		$data['type'] ="Jypgcheck1";
		
		$schedulesetting=M("Bjsz")->where("id=3")->find();
		if($schedulesetting["approver"]=="0")
		{
			$userschedule=$this->findleader($info['projecttype'],$info['city']);
		}
		else
		{
			$userschedule=$this->findleaderbyroleid($schedulesetting["approver"],$info['projecttype'],$info['city']);
		}
		$data['user']=$userschedule['nickname'].$userschedule['number'];
		$this->Addschedule($data);
		
		M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$userschedule["nickname"]);
		
			
		if ($list !== false) {
			$this->success('操作成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
		
}
?>