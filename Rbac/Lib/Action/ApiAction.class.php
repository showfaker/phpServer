<?php
class ApiAction extends Action {
	// 电量计算1
	function calculation1(){
		$request = filter_var(file_get_contents('php://input'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $request = json_decode($request,true);
       
        $name = "electricity1";
		$model = D($name);
		if (false === $model->create($request)) {
			$this->error($model->getError());
		}
		$model->create_time=time();
		$model->projectNm=$request['projectNm'];
		$model->projectTyp=$request['projectTyp'];
		$model->area=$request['area'];
		$model->addr=$request['addr'];
		$list = $model->add();
		if ($list !== false) { //保存成功
			$arr['status']="ok";				
			$arr['msg']='添加成功';
			echo json_encode($arr);
		} else {
			//失败提示
			$arr['status']="error";				
			$arr['msg']='添加失败';
			echo json_encode($arr);
		}

        
	}

	// 电量计算2
	function calculation2(){
		$request = filter_var(file_get_contents('php://input'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $request = json_decode($request,true);

        $name = "electricity2";
		$model = D($name);
		if (false === $model->create($request)) {
			$this->error($model->getError());
		}
		$model->create_time=time();
		$model->projectNm=$request['projectNm'];
		$model->projectTyp=$request['projectTyp'];
		$model->area=$request['area'];
		$model->addr=$request['addr'];
		$list = $model->add();
		if ($list !== false) { //保存成功
			$arr['status']="ok";				
			$arr['msg']='添加成功';
			echo json_encode($arr);
		} else {
			//失败提示
			$arr['status']="error";				
			$arr['msg']='添加失败';
			echo json_encode($arr);
		}

        
	}

	// 电量计算3
	function calculation3(){
		$request = filter_var(file_get_contents('php://input'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $request = json_decode($request,true);

        $name = "electricity";
		$model = D($name);
		if (false === $model->create($request)) {
			$this->error($model->getError());
		}
		$model->create_time=time();
		$model->projectNm=$request['projectNm'];
		$model->projectTyp=$request['projectTyp'];
		$model->area=$request['area'];
		$model->addr=$request['addr'];
		$list = $model->add();
		if ($list !== false) { //保存成功
			$arr['status']="ok";				
			$arr['msg']='添加成功';
			echo json_encode($arr);
		} else {
			//失败提示
			$arr['status']="error";				
			$arr['msg']='添加失败';
			echo json_encode($arr);
		}

        
	}
	
	function sanitize($dat) {
	   return $dat;
	}

		//项目更新
	public function plmdata_update(){
		$request = file_get_contents('php://input');
		//$request = json_decode($request,true);
		//保存接受记录
		$info['ctime'] = date("Y-m-d H:i:s",time());
		$info['data'] = $request;
		$info['accesskey'] = $_REQUEST['accessKey'];
		M("plmuploaddata")->add($info);
		
		//$info=M("plmuploaddata")->find();
		//{"applyDate":"2022-12-12","meetingCode":"胜多负少","projectNumber":"230F12289","projectCode":"东风三到四","projectName":"士大夫","projectType":"F1","projectTypeLabel":"风电-陆风-陆风","projectRegisterNumber":"Y230F122135","companyName":"南京电气电力工程有限公司","deptName":"市场开发一部","followMan":"隋峰","approvalStatus":"ESTABLISHMENT","approvalStatusLabel":"正式立项","projectAddress":"撒旦反对","installedCapacity":"士大夫","financeAbbreviation":"士大夫","establishmentType":"ESTABLISHMENT","establishmentTypeLabel":"正式立项"}
		$datararay=json_decode($info["data"],true);
		if(false!=strpos($datararay["projectAddress"], "/")){
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目地址不能含有特殊字符！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			echo json_encode($dataresult);
			return;
		}
		if(false!=strpos($datararay["projectAddress"], " ")){
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目地址不能含有特殊字符！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		if(false!=strpos($datararay["projectAddress"], "\\")){
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目地址不能含有特殊字符！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		if(empty($datararay["projectName"]))
		{
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目名称必填！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		if(empty($datararay["projectAddress"]))
		{
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目地址必填！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		if(empty($datararay["projectNumber"]))
		{
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目编号必填！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		if(empty($datararay["projectTypeLabel"]))
		{
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="项目类型必填！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		if(empty($datararay["applyDate"]))
		{
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="日期必填！";
			$result["result"]="error";
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			//echo json_encode($dataresult);
			//return;
		}
		
		if(!empty($datararay["projectNumber"]))
		{
			$dataforProject["number"]=$datararay["projectNumber"];
		}
		if(!empty($datararay["projectName"]))
		{
			$dataforProject["title"]=$datararay["projectName"];
		}
		if(!empty($datararay["projectNumber"]))
		{
			$dataforProject["taketype"]=$datararay["projectNumber"];
		}
		if(!empty($datararay["companyName"]))
		{
			$dataforProject["company"]=$datararay["companyName"];
		}
		if(!empty($datararay["deptName"]))
		{
			$dataforProject["department"]=$datararay["deptName"];
		}
		if(!empty($datararay["installedCapacity"]))
		{
			$dataforProject["capacity"]=$datararay["installedCapacity"];
		}
		
		if(!empty($datararay["followMan"]))
		{
			$dataforProject["xiaoshouuser"]=$datararay["followMan"];
		}
		if(!empty($datararay["followMan"]))
		{
			$dataforProject["name"]=$datararay["followMan"];
		}
		if(!empty($datararay["followMan"]))
		{
			//$dataforProject["kaifauser"]=$datararay["followMan"];
		}
		if(!empty($datararay["followMan"]))
		{
			$dataforProject["user"]=$datararay["followMan"];
			$mapforUser["nickname"]=$datararay["followMan"];
			$deptid=M("User")->where($mapforUser)->getField("department");
			$dataforProject["department"]=M("Dept")->where("id=".$deptid)->getField("name");
			$role=M("User")->where($mapforUser)->getField("position");
			$mapforparentrole[id]=$role;
			$parentrole=M("Role")->where($mapforparentrole)->getField("pid");
			$mapuser['position']=array("in",$parentrole);
			$mapuser['department']=$deptid;
			$user=M("User")->where($mapuser)->find();
			//$dataforProject["kaifa"]=$user["nickname"];
		}
		
		
		
		
		
		$dataforProject["headtitle"]=$datararay["financeAbbreviation"];
		if(!empty($datararay["projectAddress"]))
		{
			$dataforProject["address"]=$datararay["projectAddress"];
		}
		if(!empty($datararay["applyDate"]))
		{
			$dataforProject["ctime"]=$datararay["applyDate"];
		}
		if(!empty($datararay["applyDate"]))
		{
			$dataforProject["time"]=$datararay["applyDate"];
		}
		if(!empty($datararay["applyDate"]))
		{
			$dataforProject["create_time"]=strtotime($datararay["applyDate"]);
		}
		if(!empty($datararay["projectTypeLabel"]))
		{
			$projecttype=$datararay["projectTypeLabel"];
			if(false!==strstr($projecttype,"分布式"))
			{
				$dataforProject["projecttype"]="分布式光伏发电";
			}
			if(false!==strstr($projecttype,"集中式"))
			{
				$dataforProject["projecttype"]="集中式光伏发电";
			}
			if(false!==strstr($projecttype,"风电"))
			{
				$dataforProject["projecttype"]="风力发电";
			}
		}
		
		$dataforProject["design_status"]="立项中";
		$dataforProject["bidding"]="1";
		
		$mapforProject["number"]=$datararay["projectNumber"];
		$ifexist=M("Project")->where($mapforProject)->getField("id");
		if(empty($ifexist))
		{
			M("Project")->add($dataforProject);
			
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="新增项目成功！";
			$result["result"]="success";
			
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			echo json_encode($dataresult);
			return;
		}
		else
		{
			M("Project")->where("id=".$ifexist)->save($dataforProject);
			
			header('Content-Type:application/json; charset=utf-8');
			$result["message"]="更新项目成功！";
			$result["result"]="success";
			
			$dataresult = array('result'=>$result["result"],'message'=>$result["message"]);
			echo json_encode($dataresult);
			return;
		}
					
		
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		if(false!=strpos($_REQUEST[address], "/")){
			$this->error("项目地址不能含有特殊字符！");
		}
		if(false!=strpos($_REQUEST[address], " ")){
			$this->error("项目地址不能含有空格！");
		}
		if(false!=strpos($_REQUEST[address], "\\")){
			$this->error("项目地址不能含有特殊字符！");
		}
		if(empty($_REQUEST[id])){
			$titlerepeat["title"]=array("eq",$_REQUEST[title]);
			$ifrepeat=M("Project")->where($titlerepeat)->find();
			if(!empty($ifrepeat)){
				$this->error("项目名称已经存在！");	
			}
		}else{
			$titlerepeat["id"]=array("neq",$_REQUEST[id]);
			$titlerepeat["title"]=array("eq",$_REQUEST[title]);
			$ifrepeat=M("Project")->where($titlerepeat)->find();
			if(!empty($ifrepeat))
			{
				$this->error("项目名称已经存在！");	
			}
		}	
		if(empty($_REQUEST["id"])){
			$model->step1=1;
			$model->xiaoshouuser=$_SESSION['loginUserName'];
			$model->department=$_SESSION['dept'];
			$model->user=$_SESSION['loginUserName'];
			$model->ctime=date("Y-m-d");
			$model->create_time=time();
		}
		$model->last_time=time();
		$model->addressfull=$_REQUEST['province'].$_REQUEST['city'].$_REQUEST['area'].$_REQUEST['address'];
		foreach($_REQUEST["chargedevice1"] as $key => $val){if(!empty($val))$chargedevice1.=$val.";";}$model->chargedevice1=$chargedevice1;
		foreach($_REQUEST["chargedevice2"] as $key => $val){if(!empty($val))$chargedevice2.=$val.";";}$model->chargedevice2=$chargedevice2;
		foreach($_REQUEST["chargedevice3"] as $key => $val){if(!empty($val))$chargedevice3.=$val.";";}$model->chargedevice3=$chargedevice3;
		foreach($_REQUEST["chargedevice4"] as $key => $val){if(!empty($val))$chargedevice4.=$val.";";}$model->chargedevice4=$chargedevice4;
		foreach($_REQUEST["chargedevice5"] as $key => $val){if(!empty($val))$chargedevice5.=$val.";";}$model->chargedevice5=$chargedevice5;
		foreach($_REQUEST["chargedevice6"] as $key => $val){if(!empty($val))$chargedevice6.=$val.";";}$model->chargedevice6=$chargedevice6;
		foreach($_REQUEST["chargedevice7"] as $key => $val){if(!empty($val))$chargedevice7.=$val.";";}$model->chargedevice7=$chargedevice7;
		foreach($_REQUEST["chargedevice8"] as $key => $val){if(!empty($val))$chargedevice8.=$val.";";}$model->chargedevice8=$chargedevice8;
		foreach($_REQUEST["chargedevice9"] as $key => $val){if(!empty($val))$chargedevice9.=$val.";";}$model->chargedevice9=$chargedevice9;
		foreach($_REQUEST["devicescale"] as $key => $val){if(!empty($val))$devicescale.=$val.";";}$model->devicescale=$devicescale;
		$date=date('Y-m-d H:i');
		$address=$model->title;
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0])){
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
			foreach($file as $key=>$val){
				if(!empty($val)){
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\"))){
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx'))){
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->keeponrecord1=$newnameall;
			$model->keeponrecord1filename=$filenameall;
		}
		if(!empty($_FILES['file2']['name'][0])){
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file2']['name'];
			$file_tmp=$_FILES['file2']['tmp_name'];
			foreach($file as $key=>$val){
				if(!empty($val)){
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
			$model->keeponrecord2=$newnameall;
			$model->keeponrecord2filename=$filenameall;
		}
		$oldinfo = M("Project")->where("id='" . $_REQUEST["id"] . "'")->find();
		//保存当前数据对象
		if(empty($_REQUEST[id])){
			$model->handlehistory=$_SESSION['loginUserName']."于".$date."创建了项目立项</br>------------------</br>";
			$list = $model->add();
			$info = M("Project")->where("id='" . $list . "'")->find();
		}else{
			$titlerepeat["id"]=array("eq",$_REQUEST[id]);
			$olddata=M("Project")->where($titlerepeat)->find();
			if($olddata["title"]!=$_REQUEST["title"]){
				$mapforplmedit["plm"]=$olddata["title"];
				$plmeditid=M("Plmattendance")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmattendancedevice")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmdaily")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmfile")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmfilediaodu")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmmaterialorder")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmmaterials")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmorder2")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmorder2paytime")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$plmeditid=M("Plmplan")->where($mapforplmedit)->setField("plm",$_REQUEST["title"]);
				$mapforplmedit1["title"]=$olddata["title"];
				$plmeditid=M("Plmbid")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
				$plmeditid=M("Plmcontract")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
				$plmeditid=M("Plmoffer")->where($mapforplmedit1)->setField("title",$_REQUEST["title"]);
				$mapforplmedit2["address"]=$olddata["title"];
				$plmeditid=M("Plmdiscuss")->where($mapforplmedit2)->setField("address",$_REQUEST["title"]);
			}
			
			$info = M("Project")->where("id='" . $model->id . "'")->find();
			$address=$info[title];
			
			if(!empty($_REQUEST[highcheck])){
				$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目信息（管）</br>------------------</br>";
			}else if(empty($_REQUEST[approve])){
				//$model->design_status="初步立项待审批";
				//$model->create_time=time();
				$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目立项</br>------------------</br>"; 
			}else{
				$address=$info[title];
				$schedulemap[taskid]=$info[id];
				$schedulemap[status]=1;
				//$schedulemap[type]="Secondcheck";
				M("Schedule")->where($schedulemap)->setField("status",0);
				M("Schedule")->where($schedulemap)->setField("result",$_REQUEST[result]);
				if(($_REQUEST[result]=="同意")){
					if(($info["invester"]=="省投资")||($info["invester"]=="合作投资")){
						if($info["design_status"]=="初步立项待审批"){
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批中";
							$schedulesetting=M("Bjsz")->where("id=1")->find();
							if($schedulesetting["approver2"]=="0"){
								$userschedule=$this->findleader($info['projecttype'],$info['city']);
							}else{
								$userschedule=$this->findleaderbyroleid($schedulesetting["approver2"],$info['projecttype'],$info['city']);
							}
							$model->where("id=".$info["id"])->setField("currentapprover",$userschedule["nickname"]);
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步立项审核，请您进行初步立项二次审核。";
							$data['href'] ="index.php?s=Secondcheck/index";
							$data['taskid'] =$taskid;
							$data['type'] ="Secondcheck";
							$data['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($data);
						}
						if($info["design_status"]=="初步立项审批中"){
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批通过";
							$model->submit_approve_time=time();
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
					}else{
						if($info["design_status"]=="初步立项待审批"){
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批中";
							$schedulesetting=M("Bjsz")->where("id=1")->find();
							if($schedulesetting["approver2"]=="0"){
								$userschedule=$this->findleader($info['projecttype'],$info['city']);
							}else{
								$userschedule=$this->findleaderbyroleid($schedulesetting["approver2"],$info['projecttype'],$info['city']);
							}
							$model->where("id=".$info["id"])->setField("currentapprover",$userschedule["nickname"]);
							$taskid=$info[id];
							$date=date('m-d H:i');
							$address=$info['title'];
							$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目完成初步立项审核，请您进行初步立项二次审核。";
							$data['href'] ="index.php?s=Secondcheck/index";
							$data['taskid'] =$taskid;
							$data['type'] ="Secondcheck";
							$data['user']=$userschedule['nickname'].$userschedule['number'];
							$this->Addschedule($data);
						}
						if($info["design_status"]=="初步立项审批中"){
							$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步立项审批通过";
							$model->submit_approve_time=time();
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
					}
					$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核，结果：同意。";
					$data['receiver']=$info['xiaoshouuser'].$this->findNumberByNameAndRole($info['xiaoshouuser']).",";
					$data['sender']="系统通知";
					$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核结果：同意。";
					$this->Sendmail($data);
				}else{	//拒绝流程
					if($info["design_status"]=="初步申报待审批"){
						if(($info["invester"]=="省投资")||($info["invester"]=="合作投资")){
							$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="初步申报审批退回";
							$model->preplmapproveflag="0";
							$model->create_approve_time=time();
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}else{
							$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
							$model->design_status="取消";
							$model->preplmapproveflag="0";
							$model->create_approve_time=time();
							
							M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
						}
					}
					if($info["design_status"]=="初步申报审批中"){
						$model->handlehistory=$info['handlehistory']."初步申报审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						$model->design_status="取消";
						$model->preplmapproveflag="0";
						$model->create_approve_time1=time();
						
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					if($info["design_status"]=="初步立项待审批"){
						$model->handlehistory=$info['handlehistory']."初步立项审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
						$model->design_status="初步立项审批退回";
						$model->submit_approve_time=time();
						M("Project")->where("id='" .$_REQUEST["id"]. "'")->setField("currentapprover","");
					}
					$model->approvestatus="";
					$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核，结果：拒绝。";
					$data['receiver']=$info['xiaoshouuser'].$this->findNumberByNameAndRole($info['xiaoshouuser']).",";
					$data['sender']="系统通知";
					$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行立项审核，结果：拒绝。";
					$this->Sendmail($data);
				}
			}
			$list = $model->save();
			$list = $_REQUEST["id"];
		}
		if($_REQUEST["id"]){
			$newinfo=M("Project")->where("id='" . $_REQUEST["id"] . "'")->find();
			foreach($newinfo as $key => $val){
				if(($key!="last_time")&&($key!="handlehistory")){
					if($val!=$oldinfo[$key]){
						$plmeditlog["plmid"]=$newinfo["id"];
						$plmeditlog["address"]=$newinfo["title"];
						$plmeditlog["title"]=$key;
						$plmeditlog["before"]=$oldinfo[$key];
						$plmeditlog["after"]=$newinfo[$key];
						$plmeditlog["user"]=$_SESSION["name"];
						$plmeditlog["create_time"]=time();
						$plmeditlog["ctime"]=date("Y-m-d H:i:s");
						M("Plmeditlog")->add($plmeditlog);
					}
				}
			}
		}
		if($list !== false){
			if($_REQUEST[jypg]){
				$this->redirect('Jypg1/index&moduletitle=可研编制');//&id=.$list
				return;
			}
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('操作成功!');
		}else{
			//失败提示
			$this->error('操作失败!');
		}
	}
}

?>