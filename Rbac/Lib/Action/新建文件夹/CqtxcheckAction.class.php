<?php
class CqtxcheckAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		//$map[step4]=1;
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
		if($_POST['para9'])
		{
			$map1['para9'] = array('like',"%".$_POST['para9']."%");
			$this->assign("para9",$_POST['para9']);
		}
		if($_POST['para21'])
		{
			$map1['para21'] = array('like',"%".$_POST['para21']."%");
			$this->assign("para21",$_POST['para21']);
		}
		if(($_POST['para1'])||($_POST['para2'])||($_POST['para5'])||($_POST['para6'])||($_POST['para7'])||($_POST['para9'])||($_POST['para21']))
		{
			$plmbidarray=M("Plmbid")->where($map1)->select();
			foreach($plmbidarray as $key => $val)
			{
				$ids.=$val["plmNumber"].",";
			}
			$map['id'] = array('in',$ids);
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

		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		//$map[design_status]=array("in","待签订合同");
		//$map[user]=array("neq","");
		
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$map[planfile_time]=array("exp","is not null");
		$name = "Project";
		$model = D($name);
		$voList = $model->where($map)->order("id desc")->select();
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
			
			
				
			$voList[$key][plminfo]['file']=explode(',',$voList[$key][plminfo]['file']);
			$voList[$key][plminfo]['filenewname']=explode(',',$voList[$key][plminfo]['filenewname']);
			
			$mapforCompany[id]=	$voList[$key][plminfo]["para20"];
			$voList[$key]["company"]=M("Company")->where($mapforCompany)->find();
			
			$voList[$key][plminfo]["para21"]=explode(",",$voList[$key][plminfo]["para21"]);
			$voList[$key][plminfo]["para22"]=explode(",",$voList[$key][plminfo]["para22"]);
			$voList[$key][plminfo]["para23"]=explode(",",$voList[$key][plminfo]["para23"]);
			$voList[$key][plminfo]["para24"]=explode(",",$voList[$key][plminfo]["para24"]);
			$voList[$key][plminfo]["para25"]=explode(",",$voList[$key][plminfo]["para25"]);
			$voList[$key][plminfo]["para26"]=explode(",",$voList[$key][plminfo]["para26"]);
			$voList[$key][plminfo]["para27"]=explode(",",$voList[$key][plminfo]["para27"]);
			
			
			
			$para20array=explode(",",$voList[$key][plminfo]["para20"]);
			$datapara20="";
			foreach($para20array as $key1 => $val1)
			{
				$temparray=explode("|",$val1);
				//$datapara20[$temparray[0]]=$temparray[1];
				$mapforCompany[id]=	$temparray[1];
				$company=M("Company")->where($mapforCompany)->getField("para1");
				$datapara20[$temparray[0]]=$company;
			}
			$voList[$key]['datapara20']=$datapara20;
			
		}
		foreach($voList as $key => $val)
		{
			$condition["id"]=$val['groupid'];
			$voList[$key]['groupinfo']= M("Secondgroup")->where($condition)->find();
		}
		$this->assign('list', $voList);
		
		$allprojects=M("Project")->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
		
		
			
		$companyclassifies=M("Companyclassify")->order("id asc")->select();
		$this->assign('companyclassifies',$companyclassifies);
			
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
		$vo["para21"]=explode(",",$vo["para21"]);
		$vo["para22"]=explode(",",$vo["para22"]);
		$vo["para23"]=explode(",",$vo["para23"]);
		$vo["para24"]=explode(",",$vo["para24"]);
		$vo["para25"]=explode(",",$vo["para25"]);
		$vo["para26"]=explode(",",$vo["para26"]);
		$vo["para27"]=explode(",",$vo["para27"]);
		
		
		$para20array=explode(",",$vo["para20"]);
		foreach($para20array as $key => $val)
		{
			$temparray=explode("|",$val);
			$datapara20[$temparray[0]]=$temparray[1];
		}
		$this->assign('datapara20', $datapara20);
		
		
		
		$companies=M("Company")->select();
		$this->assign('companies', $companies);
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		
		$companyclassifies=M("Companyclassify")->order("id asc")->select();
		foreach($companyclassifies as $key => $val)
		{
			$mapforCompany["duty"]=$val["name"];
			$companyclassifies[$key]["companies"]=M("Company")->order("id asc")->where($mapforCompany)->select();
		}
		
		$this->assign('companyclassifies', $companyclassifies);
		
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
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
		$para20=$_REQUEST["para20"];
		$companyclassifies=M("Companyclassify")->order("id asc")->select();
		foreach($companyclassifies as $key => $val)
		{
			$x.=$val["name"]."|".$para20[$key].",";
		}
		$model->para20=$x;
		$mapforrepeat[plmNumber]=$plminfo["id"];
		$repeat=M("Plmbid")->where($mapforrepeat)->find();	
		$date=date('m-d H:i');
		if($repeat)
		{
			$model->id=$repeat[id];
			$model->save();
			
			$plminfo[handlehistory]=$plminfo['handlehistory'].$_SESSION['loginUserName']."于".$date."修改招标信息</br>------------------</br>";
			M("Project")->where($mapforProject)->setField("handlehistory",$plminfo[handlehistory]);
		}
		else
		{
			$model->add();
			
			M("Project")->where($mapforProject)->setField("step4","1");
			M("Project")->where($mapforProject)->setField("bid_time",time());
			M("Project")->where($mapforProject)->setField("toubiaouser",$_SESSION['loginUserName']);
			M("Project")->where($mapforProject)->setField("design_status","招标审核通过");
			$plminfo[handlehistory]=$plminfo['handlehistory'].$_SESSION['loginUserName']."于".$date."设置招标信息</br>------------------</br>";
			M("Project")->where($mapforProject)->setField("handlehistory",$plminfo[handlehistory]);
		}
		
		
		M("Project")->where($mapforProject)->setField("bid_number",$_REQUEST["para1"]);
		$model = D("Project");
		$info=M("Project")->where($mapforProject)->find();
		if(empty($_REQUEST[approve]))
		{
			//$model->design_status="招标待审核";
			//$model->bid_time=time();
			//$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了招标信息</br>------------------</br>"; 
		}
		else
		{
			
			//$schedulemap[taskid]=$info[id];
			//$schedulemap[status]=1;
			//$schedulemap[type]="Secondcheck";
			//M("Schedule")->where($schedulemap)->setField("status",0);
			/*
			if(($_REQUEST[result]=="同意"))
			{
				$model->handlehistory=$info['handlehistory']."招标审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->design_status="招标审核通过";
				$model->bid_approve_time=time();
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行招标审核，结果：同意。";
				$data['receiver']=$info['toubbiaouser'].$this->findNumberByNameAndRole($info['toubbiaouser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行经营评估，结果：同意。";
				$this->Sendmail($data);
				
			}
			else
			{	//拒绝流程
				$model->handlehistory=$info['handlehistory']."招标审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->design_status="招标审核退回";
				$model->bid_approve_time=time();
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行招标审核，结果：拒绝。";
				$data['receiver']=$info['toubbiaouser'].$this->findNumberByNameAndRole($info['toubbiaouser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行招标审核，结果：拒绝。";
				$this->Sendmail($data);
			}
			*/
		}
		//$model->id=$_REQUEST["id"];
		//$model->save();
		
		if($_SESSION[app])
		{
			//$this->redirect('App/detail&check=1&id='.$_REQUEST["id"]);
			$this->redirect('index');
			return;
		}
		$this->redirect('index');
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
			$mapforPlmbid[plmNumber]=$volist[$i]["id"];
			$plminfo=M("Plmbid")->where($mapforPlmbid)->find();
			
			$data[$i]['para1']=$plminfo['para1'];
			$data[$i]['para2']=$plminfo['para2'];
			$data[$i]['number']=$volist[$i]['number'];
			$data[$i]['title']=$volist[$i]['title'];
			
			$data[$i]['para5']=$plminfo['para5'];
			$data[$i]['para6']=$plminfo['para6'];
			$data[$i]['para7']=$plminfo['para7'];
			$data[$i]['para8']=$plminfo['para8'];
			$data[$i]['para9']=$plminfo['para9'];
			$data[$i]['para10']=$plminfo['para10'];
			$data[$i]['para11']=$plminfo['para11'];
			$data[$i]['para12']=$plminfo['para12'];
			$data[$i]['para13']=$plminfo['para13'];
			$data[$i]['para14']=$plminfo['para14'];
			$data[$i]['para15']=$plminfo['para15'];
			$data[$i]['para16']=$plminfo['para16'];
			$data[$i]['para17']=$plminfo['para17'];
			$data[$i]['para18']=$plminfo['para18'];
			$data[$i]['para19']=$plminfo['para19'];
			$data[$i]['para20']=$plminfo['para20'];
			
			$plminfo["para21"]=explode(",",$plminfo["para21"]);
			$plminfo["para22"]=explode(",",$plminfo["para22"]);
			$plminfo["para23"]=explode(",",$plminfo["para23"]);
			$plminfo["para24"]=explode(",",$plminfo["para24"]);
			$plminfo["para25"]=explode(",",$plminfo["para25"]);
			$plminfo["para26"]=explode(",",$plminfo["para26"]);
			$plminfo["para27"]=explode(",",$plminfo["para27"]);
			//'陪标单位','保证金接收账号','对方联系人及联系方式','我司申请打款时间','是否退回我司','往来金额（万元）','投标价（元）',
			$content="";
			foreach($plminfo["para21"] as $key=>$val)
			{
				if(!empty($val))
				{
					$content.="【陪标单位：".$val.",保证金接收账号：".$plminfo['para22'][$key].",对方联系人及联系方式：".$plminfo['para23'][$key].",我司申请打款时间：".$plminfo['para24'][$key].",是否退回我司：".$plminfo['para25'][$key].",往来金额（万元）：".$plminfo['para26'][$key].",投标价（元）：".$plminfo['para27'][$key]."】\r\n";
				}
			}
			$data[$i]['content']=$content;
			
			$data[$i]['para28']=$plminfo['para28'];
			$data[$i]['para29']=$plminfo['para29'];
			$data[$i]['para30']=$plminfo['para30'];
			$data[$i]['para31']=$plminfo['para31'];
			
		}
		
		$file="招投标列表";
		$title="招投标列表";
		$subtitle='招投标列表';
		
		$th_array=array('存档编码','招标人','项目编号','项目名称','工程性质','投标日期','开标日期','投标保证金（万元）','投标单位','经理','总工','安C','我方保证金缴纳至','是否退回我方','控制价（元）','现场下浮率（%）','标基准价（元）','投标价（元）','中标价（元）','中标单位','陪标单位','招标文件存放位置','投标文件存放位置','电子版招投标文件存放位置','备注');
		
		
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