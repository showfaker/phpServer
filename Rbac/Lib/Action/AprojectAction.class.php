<?php
class AprojectAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		//$map[step2]=array("in","0.5,1");
		$map['projecttype'] = array("neq","承揽项目");
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
		
		$filedown=M("Filesetting")->find();
		$this->assign('filedown', $filedown);
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
		
		//$map[design_status]=array("in","初步立项审批通过,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,项目待验收,验收审核退回,暂停中");
		$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) 
		{
			$this->_list($model, $map,'create_time',false);
		}
		
		
		$mapforsetting[title]=array("eq",$_REQUEST["moduletitle"]);
		$mapforsetting[status]=1;
		$setting=M("Settingformproject")->where($mapforsetting)->find();
		$this->assign("setting",$setting);
		$this->assign("moduletitle",$_REQUEST["moduletitle"]);
		

		
		
		$this->getAllcities();
		if($_SESSION[app]=="1")
		{
			$this->display("indexapp");
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
				
				$voList[$key]['programme2']=explode(',',$val['programme2']);
				$voList[$key]['programmefilename2']=explode(',',$val['programmefilename2']);
				
				$voList[$key]['programme3']=explode(',',$val['programme3']);
				$voList[$key]['programmefilename3']=explode(',',$val['programmefilename3']);
				
				$voList[$key]['programme4']=explode(',',$val['programme4']);
				$voList[$key]['programmefilename4']=explode(',',$val['programmefilename4']);
				
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
		$this->assign('approve',$_REQUEST[approve]);
		$this->assign('type',$_REQUEST[type]);
		$this->assign('highpower',$_REQUEST[highpower]);
		
		$secondgroups=M("Secondgroup")->select();
		$this->assign('secondgroups',$secondgroups);
		
		
		
		$model1=M("Companysupervise");
		$companysupervises=$model1->order("id asc")->select();
		
		$model2=M("Companydesign");
		$companydesigns=$model2->order("id asc")->select();
		
		$this->assign('companysupervises',$companysupervises);
		$this->assign('companydesigns',$companydesigns);
		
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
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					
					$allowedExts = array("pdf");
					if(!in_array($ext, $allowedExts))
					{
						$this->error('请上传pdf文件!');
					}
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						$this->error("非法文件类型！");
					}
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
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
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					
					$allowedExts = array("pdf");
					if(!in_array($ext, $allowedExts))
					{
						$this->error('请上传pdf文件!');
					}
					
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
		else
		{
			//$this->error('请上传文件!');
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
		
		if(!empty($_FILES['file4']['name'][0]))
		{
			//$newnameall=$info["programme3"];
			//$filenameall=$info["programmefilename3"];
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
			$model->programme4=$newnameall;
			$model->programmefilename4=$filenameall;
		}
		
		$address=$info[title];
		
		if(!empty($_REQUEST[highpower]))
		{
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置可研编制文件（管）</br>------------------</br>"; 
		}
		else if($_REQUEST[operate_type]==2)
		{
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置项目群</br>------------------</br>"; 
		}
		else if(1)//empty($_REQUEST[approve])
		{
			if(empty($info[step2]))
			{
				$model->step2=0.5;
				$model->design_status="可研编制文件审批通过";
				$model->research_time=time();
				$model->yanjiuuser=$_SESSION['loginUserName'];
			}
			if(($info[design_status]=="可研评审报告审批退回"))
			{
				$model->step2=0.5;
				$model->design_status="可研编制文件审批通过";
				$model->research_time=time();
				$model->yanjiuuser=$_SESSION['loginUserName'];
			}
			$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置可研编制文件</br>------------------</br>";
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Jypg";
			M("Schedule")->where($schedulemap)->setField("status",0);
		}
		else
		{
			/*
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			//$schedulemap[type]="Secondcheck";
			M("Schedule")->where($schedulemap)->setField("status",0);
		
			if(($_REQUEST[result]=="同意"))
			{
				$model->handlehistory=$info['handlehistory']."可研立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
				$model->design_status="可研编制文件审批通过";
				$model->research_approve_time=time();
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行可研立项审核，结果：同意。";
				$data['receiver']=$info['yanjiuuser'].$this->findNumberByNameAndRole($info['yanjiuuser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行可研立项审核，结果：同意。";
				$this->Sendmail($data);
				
			}
			else
			{	//拒绝流程
				$model->handlehistory=$info['handlehistory']."可研立项审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";   //经办人记录
				$model->design_status="可研编制文件审批退回";
				$model->research_approve_time=time();
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行可研立项审核，结果：拒绝。";
				$data['receiver']=$info['yanjiuuser'].$this->findNumberByNameAndRole($info['yanjiuuser'],"设计师").",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行可研立项审核，结果：拒绝。";
				$this->Sendmail($data);
			}
			*/
		}
		$list = $model->save();
			
		if ($list !== false) {
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
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