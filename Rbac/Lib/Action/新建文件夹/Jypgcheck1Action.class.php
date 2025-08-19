<?php
class Jypgcheck1Action extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		//$map[step2]=1;
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
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
		
		if($_REQUEST['plmgroup'])
		{
			$mapforSecondgroup["name"]=array('like',"%".$_REQUEST['plmgroup']."%");
			$plmgrouparray=M("Secondgroup")->where($mapforSecondgroup)->field("id")->select();
			foreach($plmgrouparray as $key => $val)
			{
				$plmgroupids.=$val["id"].",";
			}
			$plmgroupids= substr($plmgroupids,0,strlen($plmgroupids)-1);
			$map['groupid'] = array('in',$plmgroupids);
			$this->assign('plmgroup', $_REQUEST['plmgroup']);
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
		
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		//可研编制文件审批退回,
		//可研编制文件待审批,
		$map[design_status]=array("in","可研编制文件审批通过,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回");
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
			$this->display("indexapp");
		}
		else
		{
			$this->display();
		}
		return;
	}
	public function indexapp() {
		
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		
		if(false!==strstr($_SESSION['role'],"公司总经理"))
		{
			$map["invester"]=array("in","自投资");
			$map["design_status"]=array("in","可研评审报告待审批");
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$volist=M("Project")->where($map)->order("create_time desc")->select();
		}
		else if(false!==strstr($_SESSION['role'],"省公司专责"))
		{
			$map[design_status]=array("in","可研评审报告审批中");
			$map[invester]=array("in","自投资");
			$map["preresearchapproveflag"]=array("eq","0.1");
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$volist=M("Project")->where($map)->order("create_time desc")->select();
		}
		else if(false!==strstr($_SESSION['role'],"省公司负责人"))
		{
			$map[design_status]=array("in","可研评审报告待审批");
			$map[invester]=array("in","省投资,合作投资");
			$volist1=M("Project")->where($map)->order("create_time desc")->select();
			
			
			$map[design_status]=array("in","可研评审报告审批中");
			$map[invester]=array("in","自投资");//省投资,自投资
			$map["preresearchapproveflag"]=array("eq","0.5");
			$volist2=M("Project")->where($map)->order("create_time desc")->select();
			$volist=array_merge((array)$volist1,(array)$volist2);
		}
		else
		{
			$map[design_status]=array("eq","xx");
			$volist=M("Project")->where($map)->order("create_time desc")->select();
		}
		
		foreach($volist as $key => $val)
		{
			$volist[$key]['programme']=explode(',',$val['programme']);
			$volist[$key]['programmefilename']=explode(',',$val['programmefilename']);
			
			$volist[$key]['programme2']=explode(',',$val['programme2']);
			$volist[$key]['programmefilename2']=explode(',',$val['programmefilename2']);
			
			$volist[$key]['programme3']=explode(',',$val['programme3']);
			$volist[$key]['programmefilename3']=explode(',',$val['programmefilename3']);
			$condition["id"]=$val['groupid'];
			$volist[$key]['groupinfo']= M("Secondgroup")->where($condition)->find();
		}
		$this->assign('list', $volist);
		$this->display("indexapp");
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
				
				$voList[$key]['programme2']=explode(',',$val['programme2']);
				$voList[$key]['programmefilename2']=explode(',',$val['programmefilename2']);
				
				$voList[$key]['programme3']=explode(',',$val['programme3']);
				$voList[$key]['programmefilename3']=explode(',',$val['programmefilename3']);
				$condition["id"]=$val['groupid'];
				$voList[$key]['groupinfo']= M("Secondgroup")->where($condition)->find();
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
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		$this->assign('type',$_REQUEST[type]);
		$this->assign('highpower',$_REQUEST[highpower]);
		
		
		$secondgroups=M("Secondgroup")->select();
		$this->assign('secondgroups',$secondgroups);
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
		$model->last_time=time();
		
		$date=date('m-d H:i');
		$address=$model->title;
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
			//$newnameall=$info["programme"];
			//$filenameall=$info["programmefilename"];
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
			
			foreach($file as $key=>$val)
			{
				$size = $_FILES['file1']['size'][$key]; //文件大小
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
			//$newnameall=$info["programme2"];
			//$filenameall=$info["programmefilename2"];
			$newnameall="";
			$filenameall="";
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
			//$newnameall=$info["programme3"];
			//$filenameall=$info["programmefilename3"];
			$newnameall="";
			$filenameall="";
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
		
		
		if(!empty($_REQUEST[highpower]))
		{
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了可研评审报告（管）</br>------------------</br>"; 
		}
		else if($_REQUEST[operate_type]==2)
		{
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置项目群</br>------------------</br>"; 
		}
		else if(empty($_REQUEST[approve]))
		{
			$model->design_status="可研评审报告待审批";
			$model->research_time1=time();
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."上传了可研评审报告</br>------------------</br>";
			M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
			
			
			
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
		
			
			if(1)//$info["design_status"]=="可研评审报告审批退回"
			{
				$schedulemap[taskid]=$info[id];
				$schedulemap[status]=1;
				//$schedulemap[type]="Secondcheck";
				M("Schedule")->where($schedulemap)->setField("status",0);
				
				$taskid=$info[id];
				$date=date('m-d H:i');
				$address=$info['title'];
				$data['content']=$_SESSION['loginUserName']."于".$date."上传《".$address."》可研评审报告，请您进行可研评审报告审批。";
				$data['href'] ="index.php?s=Jypgcheck1/index";
				$data['taskid'] =$taskid;
				$data['type'] ="Jypgcheck1";
				$userschedule=$this->findleader($info['projecttype'],$info['city']);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
				
				M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
			}
			
		}
		else
		{
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			//$schedulemap[type]="Secondcheck";
			M("Schedule")->where($schedulemap)->setField("status",0);
			M("Schedule")->where($schedulemap)->setField("result",$_REQUEST[result]);
			
			if(($_REQUEST[result]=="同意"))/*同意*/
			{
				$model->handlehistory=$info['handlehistory']."可研评审报告审批：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				
				//if(($info["department"]=="省公司"))
				if(($info["invester"]=="省投资")||($info["invester"]=="合作投资"))
				{
					$model->design_status="可研评审报告审批通过";
					$model->research_approve_time=time();
					M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				}
				else if(1)//自投资&<=200万//($info["invester"]!="省投资")&&($info["invester"]!="合作投资")&&($info["invest6"]<=200)
				{
					if(1)//($info["design_status"]=="可研评审报告审批中")&&($info["preresearchapproveflag"]=="0.1")
					{
						$model->design_status="可研评审报告审批通过";
						$model->research_approve_time=time();
						$model->preresearchapproveflag=1;
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					else
					{
						$model->design_status="可研评审报告审批中";
						$model->research_approve_time=time();
						$model->preresearchapproveflag=0.1;
						$model->where("id=".$info["id"])->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
						
						
						
						$taskid=$info[id];
						$date=date('m-d H:i');
						$address=$info['title'];
						$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告第一次审批，请您进行可研评审报告审批。";
						$data['href'] ="index.php?s=Jypgcheck1/index";
						$data['taskid'] =$taskid;
						$data['type'] ="Jypgcheck1";
						$userschedule=$this->findleader($info['projecttype'],$info['city']);
						$data['user']=$userschedule['nickname'].$userschedule['number'];
						$this->Addschedule($data);
						
					}
				}
				else//自投资&>200万
				{
					
					if(($info["design_status"]=="可研评审报告审批中")&&($info["preresearchapproveflag"]=="0.5"))
					{
						$model->design_status="可研评审报告审批通过";
						$model->research_approve_time1=time();
						$model->preresearchapproveflag=1;
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					else
					{
						$model->design_status="可研评审报告审批中";
						$model->where("id=".$info["id"])->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
						$model->research_approve_time=time();
						
						
						$address=$info['title'];
						if(empty($info["preresearchapproveflag"]))
						{
							$model->preresearchapproveflag=0.1;
							$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告第一次审批，请您进行可研评审报告审批。";
						}
						if(($info["preresearchapproveflag"]==0.1))
						{
							$model->preresearchapproveflag=0.5;
							$data['content']=$_SESSION['loginUserName']."于".$date."提交《".$address."》可研评审报告第二次审批，请您进行可研评审报告审批。";
						}
						
						$taskid=$info[id];
						$date=date('m-d H:i');
						
						$data['href'] ="index.php?s=Jypgcheck1/index";
						$data['taskid'] =$taskid;
						$data['type'] ="Jypgcheck1";
						$userschedule=$this->findleader($info['projecttype'],$info['city']);
						$data['user']=$userschedule['nickname'].$userschedule['number'];
						$this->Addschedule($data);
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover",$this->findmyleader($info['projecttype'],$info['city']));
					}
				}
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行可研评审报告审批，结果：同意。";
				$data['receiver']=$info['yanjiuuser'].$this->findNumberByNameAndRole($info['yanjiuuser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行可研评审报告审批，结果：同意。";
				$this->Sendmail($data);
				
			}
			else
			{	//拒绝流程
				$model->handlehistory=$info['handlehistory']."可研评审报告审批：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				//$model->design_status="可研评审报告审批退回";
				//$model->design_status="可研编制文件审批通过";20220809
				$model->design_status="可研评审报告审批退回";
				$model->research_approve_time=time();
				$model->preresearchapproveflag="";
				
				M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行可研评审报告审批，结果：拒绝。";
				$data['receiver']=$info['yanjiuuser'].$this->findNumberByNameAndRole($info['yanjiuuser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行可研评审报告审批，结果：拒绝。";
				$this->Sendmail($data);
			}
		}
		$list = $model->save();
			
		if ($list !== false) {
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
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
			$data[$i]['communication']=$volist[$i]['communication'];
			$data[$i]['road']=$volist[$i]['road'];
			
			if($volist[$i]['test1'])$data[$i]['test'].="基本试验 ";
			if($volist[$i]['test2'])$data[$i]['test'].="再生试验 ";
			if($volist[$i]['test3'])$data[$i]['test'].="现场检测 ";
			
			$data[$i]['programmetime']=$volist[$i]['programmetime'];

		}
		
		$file="研究中心项目列表";
		$title="研究中心项目列表";
		$subtitle='研究中心项目列表';
		
		$th_array=array('项目编号','项目名称','技术交流','路巡','试验检测','方案交付客户日期');
		
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template.xls" );
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
}
?>