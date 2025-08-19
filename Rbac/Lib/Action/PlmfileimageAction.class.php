<?php
class PlmfileimageAction extends CommonAction {			
	public function index() {
        if(empty($_REQUEST['tab'])){
			$_SESSION['tab']=$_REQUEST['tab'];
			$this->assign('tab',$_REQUEST['tab']);
		}
        if(!empty($_REQUEST['tab']))
		{			
			$_SESSION[tab]=$_REQUEST['tab'];			
			$this->assign('tab',$_REQUEST['tab']);
		}
		
		//未完成订单统计
		$map9['status']=3;
		$map9['type']=1;
		$map9['approve']=1;
		$map9['plm']=array('neq','仓库采购');
		$map9['sqr']=$_SESSION['loginUserName'];
		$count9=count(M('plmmaterialtj')->where($map9)->select());
		$this->assign('count9',$count9);
		if(($_REQUEST['tab']==5)||(empty($_REQUEST['tab']))){						
            if($_SESSION[account]!="admin"){
				$pro=M("Project")->where("draw_user='".$_SESSION[loginUserName]."'")->field("id")->select();
				$plmid=array();
				foreach($pro as $i=>$va){
					$plmid[$i]=$va[id];
				}
				//$map[plmNumber]=array("in",$plmid);
				//$con[draw_user]=$_SESSION[loginUserName];
			}
			$con[step6]=array('eq','1');
			
			
			
            $projects=M("Project")->where($con)->select();
			foreach($projects as $key=>$value ){
				$info[status]=0;
				$info['plmNumber']=$value['id'];
				$res=M('plmsend')->where($info)->select();
				$count=count($res);
				$projects[$key]['count']=$count;
			}
			$this->assign("projects",$projects);			
			if(!empty($_REQUEST['plmid']))
			{
				$map[plmNumber]=$_REQUEST['plmid'];
			}
			
			
			if(!empty($_REQUEST['title']))
			{
				$map[plm]=array("like","%".$_REQUEST['title']."%");
				$this->assign('title',$_REQUEST['title']);
			}
			if(!empty($_REQUEST['type']))
			{
				$map[type]=array("like","%".$_REQUEST['type']."%");
				$this->assign('type',$_REQUEST['type']);
			}
			
			$map['loadPerson'] = array("in",$this->find5levelusers($_SESSION[position]));
			
			$model = M("Plmfilediaodu");
			if (!empty($model)) {
				$this->_list($model, $map,'loadtime',false);
			}
			
			$classifies=M("Classifyfile")->where(array('status'=>0))->select();
            $this->assign('classifies',$classifies);
		}
		if($_REQUEST['tab']==1)
		{
			//$mapforprojectforstep[step6]=1;
			$mapforprojectforstep['_complex'] = $this->find5level($_SESSION[position],$mapforprojectforstep);
			$projects=M("project")->where($mapforprojectforstep)->select();
			$this->assign("projects",$projects);
			
			$classifies=M("Classifyfile")->where(array('status'=>0))->select();
            $this->assign('classifies',$classifies);
		}
		if($_REQUEST['tab']==2){
			$model=M('classifyfile');
			if(!empty($_REQUEST[name])){
				$map[name]=$_REQUEST[name];
				$this->assign('name',$_REQUEST[name]);
			}
			$map['status']=0;
			$list=$model->where(array('status'=>0))->select();
            $this->assign('list',$list);

		}

		$this->getAllcities(1);
		//$cities=M("cities")->select();
		//$this->assign("cities",$cities);
		//$brand=M("brand")->select();
		//$this->assign("brand",$brand);
		if($_SESSION[app])
		{
			$this->display(indexapp);
		}
		else
		{
			$this->display();
		}
		return;
	}
	protected function _list($model, $map, $sortBy = '', $asc = false,$tab,$arr) {
    	
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
            $p = new Page($count, 20);
            //分页查询数据

            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            foreach ($voList as $key => $value) {
            	
				$voList[$key]["ext"]= strtolower(end(explode(".",basename($value['filename'])))); 
				
            }
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            if($tab==2){
            	$data['start']=$arr[0];
            	$data['end']=$arr[1];
            	$data['city']=$arr[2];
            	$data['zt']=$arr[3];
            	$data['user']=$arr[4];
				$data['plm']=$arr[5];
            	foreach ($data as $key => $val) {
		            if (!is_array($val)) {
		                $p->parameter .= "$key/".$val."/";
		            }
		        }
            }elseif($tab==3){
            	$data['start']=$arr[0];
            	$data['end']=$arr[1];
            	$data['city']=$arr[2];
            	foreach ($data as $key => $val) {
		            if (!is_array($val)) {
		                $p->parameter .= "$key/".$val."/";
		            }
		        }
            }else{
            	foreach ($map as $key => $val) {
		            if (!is_array($val)) {
		                $p->parameter .= "$key/".$val."/";
		            }
		        }
            }
	            
            foreach ($voList as $vokey => $voval) {
            	$voList[$vokey][urltitle]=urlencode($voList[$vokey][title]);
            }
            if ($tab==2) {
            	foreach ($voList as $key => $value) {
            		$orderid=$value['id'];
            		$tj=M('plmmaterialtj')->where(array('orderid'=>$orderid))->select();
            		$pay=0;
            		foreach ($tj as $key2 => $value2) {
            			$pay+=$value2[pay];
            		}
            		if($pay>0){
            			$voList[$key][forbid]=1;
            		}
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
    protected function _list1($model, $map, $sortBy = '', $asc = false,$tab,$sqr) {
    	
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'desc' : 'asc';
        } else {
            $sort = $asc ? 'desc' : 'asc';
        }
        //取得满足条件的记录数
        $count = count($model->where($map)->field("id")->select());
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
            foreach($voList as $key=>$value){
            	$voList[$key][total]=$value['total']+$value['yf']-$value['yhje']-$value['yh2'];
            	// if($value[dj]>0){
            	// 	$voList[$key][djstatus]=1;
            	// }
            	// if($_SESSION[loginUserName]==$value['sqr'] || $_SESSION['account']=='admin' || $_SESSION['dept']=='财务部'){
            	// 	$voList[$key]['upload_quanxian']=1;
            	// }
            }
            if($tab==5){
            	$data[tjdate]=$sqr[0];
            	$data[fkzt]=$sqr[1];
            	$data[gys]=$sqr[2];
            	// $data[sqr]=$sqr[3];
            }
            foreach ($data as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
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
	public function ajaxsupplier(){
		$city=$_REQUEST['city'];
		// $city="南京市";
		$model=M('supplier');
		$info=$model->where(array('status'=>1))->select();
		$res=array();
		foreach($info as $key=>$value){
			if(in_array($city,explode(',',$value['city']))){
				$res[]=$value;
			}
		}

		echo json_encode($res);
		// echo 11;
	}


	public function ajaxsearch(){
		$name=$_REQUEST['name'];
		// $city="南京市";
		$model=M('supplier');
		$map['supplier']=array('like','%'.$name.'%');
		$info=$model->where($map)->select();
		// foreach($info as $key=>$value){
		// 	if(in_array($city,explode(',',$value['city']))){
		// 		$res[]=$value;
		// 	}
		// }

		echo json_encode($info);
		// echo 11;
	}

	//source:draft
	public function insert(){
		
		
		$data[plmNumber]=$_POST[plmid];
		$data[title]=$_POST[title];
		$data[loadPerson]=$_SESSION["name"];
		$data[loadtime]=time();
		
		$plminfo=M("Project")->where("id=".$_POST[plmid])->find();
		$data[plm]=$plminfo[title];
		$data[type]=$_POST[type];

		$savePath = '../Public/Uploads/';//设置附件上传目录
		
		/*
		$ext = strtolower(end(explode(".",basename($_FILES['file']['name'])))); 
		$uuid=uniqid(rand(), false).$key;//防止生成相同文件名id
		$newname = $uuid.'.'.$ext;
		$upload_file = $savePath.$newname;
		
		$filename=$_FILES['file']['name'];
		if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
		{
			$this->error("文件名不能含有特殊字符！");
		}
		if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
		{
			$this->error("非法文件类型！");
		}
		$res1=move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
		if($res1){
			$data[newname] = $newname;
			$data[filename] = $_FILES['file']['name'];
		}
		$id=M("Plmfilediaodu")->add($data);
		*/
		
			
		$file=$_FILES['file']['name'];
		$file_tmp=$_FILES['file']['tmp_name'];
		foreach($file as $key=>$val)
		{
			if(!empty($val))
			{
				$filename=$val;
				$ext = strtolower(end(explode(".",basename($filename)))); 
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png')))
				{
					$this->error("非法文件类型！");
				}
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
				move_uploaded_file($file_tmp[$key],$upload_file);
				$data[newname] = $newname;
				$data[filename] = $val;
				M("Plmfilediaodu")->add($data);
			}
		}
			
		$this->redirect('index','tab=5/');
	}
	
	
	
	
	
	
	public function insertpiliang(){
		
		/*
		$time=time();
		$data[city]=$_POST[city];
		$data[plmid]=$_POST[plmid];
		$data[status]=0;//下单待审核
		$data[ctime]=$time;
		$data[user]=$_SESSION[name];
		$data[userid]=$_SESSION[number];
		$data["type"]=1;//采购下单 2 退回
		$data[department]=$_SESSION[dept];
		$plminfo=M("Project")->where("id=".$data[plmid])->find();
		$data[plm]=$plminfo[title];
		$data[price]=$_POST[price];
		$id=M("plmmaterialorder")->add($data);
		if(!empty($id)){
			
			for($k=0;$k<count($_POST[para1]);$k++){

				//$data1[brandid]=M("brand")->where("name='".$_POST[para3][$k]."'")->getfield("id");
				$data1[brand]=$_POST[para3][$k];
				//$data1[supplierid]=M("Supplier")->where("name='".$_POST[para6][$k]."'")->getfield("id");
				$data1[supplier]=$_POST[para6][$k];
				$data1[number]=$_POST[para1][$k];
				$data1[name]=$_POST[para2][$k];
				$data1[standard]=$_POST[para4][$k];
				$data1[unit]=$_POST[para5][$k];
				$data1[price]=$_POST[para7][$k];
				$data1["count"]=$_POST[para10][$k];
				$data1[plmid]=$_POST[plmid];
				$data1[plm]=$plminfo[title];
				$data1[orderid]=$id;
				$data1[ctime]=$time;
				$data1["sort"]=$k;
				
				M("plmmaterials")->add($data1);
			}
		}
				
		$this->redirect('index','tab=2/');
		*/
	}
	
	public function edit()
	{
		$map[orderid]=$_REQUEST[id];
		$info=M('plmmaterialtj')->where(array('orderid'=>$_REQUEST[id]))->find();
		if($info[pay]>0){
			$this->assign("pay",$info[pay]);
			$this->assign("tj_id",$info[id]);
			$this->assign("supplier",$info[supplier]);
		}
		$isedit=1;
		$materials=M("Plmmaterials")->where($map)->order("sort asc")->select();
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);
		$this->assign("isedit",$isedit);
		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		$project=M("Project")->field("id,title")->select();
		$this->assign("project",$project);
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		$leibie=M('brand')->field('name')->select();
			// dump($leibie);
		$this->assign('leibie',$leibie);
		$this->display();
	}
	
	public function update(){
		$map[id]=$_POST[id];
		$orderinfo=M("Plmmaterialorder")->where($map)->find();
		$time=time();
		$orderinfo[status]=0;//下单待审核
		$orderinfo[ctime]=$time;
		$orderinfo[user]=$_SESSION[name];
		$orderinfo[userid]=$_SESSION[number];
		$plminfo=M("Project")->where("id=".$orderinfo[plmid])->find();
		$data[price]=$_POST[price];
		M("plmmaterialorder")->save($data);
		
		$id=$orderinfo[id];
		
		$mapforPlmmaterials[orderid]=$id;
		M("Plmmaterials")->where($mapforPlmmaterials)->delete();
		if(!empty($id)){
			
			for($k=0;$k<count($_POST[para1]);$k++){
				//$data1[brandid]=M("brand")->where("name='".$_POST[para3][$k]."'")->getfield("id");
				$data1[brand]=$_POST[para3][$k];
				//$data1[supplierid]=M("Supplier")->where("name='".$_POST[para6][$k]."'")->getfield("id");
				$data1[supplier]=$_POST[para6][$k];
				$data1[number]=$_POST[para1][$k];
				$data1[name]=$_POST[para2][$k];
				$data1[standard]=$_POST[para4][$k];
				$data1[unit]=$_POST[para5][$k];
				$data1[price]=$_POST[para7][$k];
				$data1["count"]=$_POST[para10][$k];
				$data1[plmid]=$orderinfo[plmid];
				$data1[plm]=$plminfo[title];
				$data1[orderid]=$id;
				$data1[ctime]=$time;
				$data1["sort"]=$k;
				M("plmmaterials")->add($data1);
			}
		}
				
		$this->redirect('index','tab=2/');
	}
	public function edittuihuo()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("sort asc")->select();
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		$this->display();
	}
	
	public function approve()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		$price=0;
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
			$price+=$materials[$i][total];
			$m=M('materials')->where(array('id'=>$va['red']))->getfield('priceflag');
			
			if($m==1){
				$materials[$i][flag]=1;
			}else{
				$materials[$i][flag]=0;
			}
		}

		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);
		$tj=M('plmmaterialtj')->where(array('orderid'=>$_REQUEST[id]))->select();
		$total=0;
		$yf=0;
		$yhje=0;
		foreach ($tj as $key => $value) {
			$yf+=$value[yf];
			$yhje+=$value[yhje];
			$total+=$value[total]+$value[yf]-$value[yhje];
		}
		$newname=M('plmmaterialorder')->where(array('id'=>$_REQUEST[id]))->getfield('newname');
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if(!empty($value)){
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}
				
		}
		
		$this->assign("yf",$yf);
		$this->assign("yhje",$yhje);
		$this->assign("total",$total);
		$this->assign("price",$price);
		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();

		$this->assign("orderinfo",$orderinfo);
		$this->assign("newname",$newname);
		
		$user=M("user")->where("status=1")->field("id,nickname")->select();
		$this->assign("user",$user);
		
		$this->display();
	}
	
	public function approve1()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		$this->display();
	}
	
	public function approvesubmit()
	{
		$info=M('plmmaterialorder')->find($_POST['id']);
		$data[id]=$_POST[id];
		$date=date('Y-m-d');
		if($_REQUEST[result]=="同意")
		{
			$data[shr]=$_POST[shr];
			$data[enddate]=$_POST[enddate];
			if(empty($info[status]))
			{
				$data[status]=0.5;
			}
			else
			{
				$data[status]=1;
				$res=M('plmmaterialtj')->where(array('orderid'=>$_POST[id]))->save(array('approve'=>$data[status]));
			}
			$data['handle']=$info['handle'].$date.'--'.$_SESSION['loginUserName'].'审核通过,审核意见:'.$_POST['suggestion'].'.;';
			//$res=M('plmmaterialtj')->where(array('orderid'=>$_POST[id]))->save(array('approve'=>1));
			
		}
		else
		{
			$data[status]=-1;
			$data[submit]=0;
			$data['handle']=$info['handle'].$date.'--'.$_SESSION['loginUserName'].'审核退回,审核意见:'.$_POST['suggestion'].'.;';
		}
		$data[approver]=$_SESSION[name];
		$data[approve_time]=time();
		M("Plmmaterialorder")->save($data);
		$this->redirect('index','tab=6/');
	}
	
	
	public function approvesubmit_piliang()
	{
		$ids=$_REQUEST[arr];
		$idsarray=explode(",",$ids);
		foreach($idsarray as $key => $val)
		{
			if($val)
			{
				$_POST['id']=$val;
				$info=M('plmmaterialorder')->find($_POST['id']);
				$data[id]=$_POST[id];
				$date=date('Y-m-d');
				if(1)//$_REQUEST[result]=="同意"
				{
					//$data[shr]=$_POST[shr];
					//$data[enddate]=$_POST[enddate];
					if(empty($info[status]))
					{
						$data[status]=0.5;
					}
					else
					{
						$data[status]=1;
						$res=M('plmmaterialtj')->where(array('orderid'=>$_POST[id]))->save(array('approve'=>$data[status]));
					}
					$data['handle']=$info['handle'].$date.'--'.$_SESSION['loginUserName'].'审核通过,审核意见:'.$_POST['suggestion'].'.;';
					//$res=M('plmmaterialtj')->where(array('orderid'=>$_POST[id]))->save(array('approve'=>1));
					
				}
				else
				{
					$data[status]=-1;
					$data[submit]=0;
					$data['handle']=$info['handle'].$date.'--'.$_SESSION['loginUserName'].'审核退回,审核意见:'.$_POST['suggestion'].'.;';
				}
				$data[approver]=$_SESSION[name];
				$data[approve_time]=time();
				M("Plmmaterialorder")->save($data);
			}
		}
		
		if(empty($ids)){
			echo 2;//为空  未获取数据
		}else{
			echo 1;
		}
	}
	
	public function approvesubmit1()
	{
		$data[id]=$_POST[id];
		$id=$_POST[id];
		if($_REQUEST[result]=="同意")
		{
			$data[status]=1;
			$info=M('Plmmaterialorder')->find($id);
			$materials=M('plmmaterials')->where(array('orderid'=>$id))->group('supplier')->select();
			$yf=round($info['thyf']/count($materials),2);
			foreach ($materials as $key => $value) {
				$con['orderid']=$value['orderid'];
				$con['supplier']=$value['supplier'];
				$con2['orderid']=$info['orderid'];
				$con2['supplier']=$value['supplier'];
				$info2=M('plmmaterialtj')->where($con2)->find();
				$res=M('plmmaterials')->where($con)->select();
				$clid='';
				$total=0;
				foreach ($res as $key2 => $value2) {
					$clid.=$value2['id'].',';
					$total+=$value2['price']*$value2['count'];
				}
				$clid=rtrim($clid,',');
				$data1['supplier']=$value['supplier'];
				$data1['city']=$info['city'];
				// $data1['ctime']=time();
				$data1['status']=0;
				$data1['total']=$total;
				$data1['type']=2;
				$data1['orderid']=$value['orderid'];
				$data1['clid']=$clid;
				$data1['plm']=$value2['plm'];
				$data1['sqr']=$info['user'];
				$data1['bankaddress']=$info2['bankaddress'];
				$data1['bankaccount']=$info2['bankaccount'];
				$data1['banknumber']=$info2['banknumber'];
				$data1['approve']=1;
				$data1['department']=$_SESSION['dept'];
				$data1['classify']=1;
				$data1['tuihuo']=2;
				$data1['yf']=$yf;
				$data1['enddate']=date('Y-m-d',time());
				M('plmmaterialtj')->add($data1);
			}
			// $data1['supplier']=$info['supplier'];
		}
		else
		{
			$data[status]=-1;
		}
		$data[approver]=$_SESSION[name];
		$data[approve_time]=time();
		$data[suggestion]=$_REQUEST[suggestion];
		M("Plmmaterialorder")->save($data);
		
		$this->redirect('index','tab=7/');
	}
	
	public function tuihuo()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("number asc,supplier asc")->select();
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		$projects=M("Project")->where("city='".$orderinfo[city]."'")->field("id,title")->select();
		$this->assign("project",$project);
		
		$this->display();
	}
	
	public function tuihuosubmit()
	{
		$map[id]=$_POST[id];
		$orderinfo=M("Plmmaterialorder")->where($map)->find();
		$time=time();
		$data[city]=$orderinfo[city];
		$data[plmid]=$orderinfo[plmid];
		$data[status]=0;//下单待审核
		$data[ctime]=$time;
		$data[user]=$_SESSION[name];
		$data[userid]=$_SESSION[number];
		$data["type"]=2;//采购下单 2 退回
		$plminfo=M("Project")->where("id=".$data[plmid])->find();
		$data[plm]=$plminfo[title];
		$data[price]=$_POST[price];
		$data[orderid]=$_POST[id];
		$data[reamrk]=$_POST[remark];
		$data[department]=$_SESSION['dept'];
		$data[thyf]=$_POST[yf];
		$id=M("plmmaterialorder")->add($data);
		if(!empty($id)){
			for($k=0;$k<count($_POST[para2]);$k++){
				if(($_POST[para9][$k]!="0")&&(!empty($_POST[para9][$k])))
				{
					//$data1[brandid]=M("brand")->where("name='".$_POST[para3][$k]."'")->getfield("id");
					$data1[brand]=$_POST[para3][$k];
					//$data1[supplierid]=M("supplier")->where("name='".$_POST[para6][$k]."'")->getfield("id");
					$data1[supplier]=$_POST[para6][$k];
					$data1[number]=$_POST[para1][$k];
					$data1[name]=$_POST[para2][$k];
					$data1[standard]=$_POST[para4][$k];
					$data1[unit]=$_POST[para5][$k];
					$data1[price]=$_POST[para7][$k];
					$data1["count"]=$_POST[para9][$k];
					$data1["unit"]=$_POST[para10][$k];
					$data1[plmid]=$data[plmid];
					$data1[plm]=$plminfo[title];
					$data1[type]=2;
					$data1[orderid]=$id;
					$data1[ctime]=$time;
					$data1["sort"]=$k;
					// dump($data1);die;
					$res=M("plmmaterials")->add($data1);

				}
			}
		}
				
		$this->redirect('index','tab=2/');
	}
	
	public function tuihuocheck()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		$this->display();
	}
	
	public function check()
	{
		$map[orderid]=$_REQUEST[id];
           
        $map_tj[status]=array(array('gt',0),array('lt',20),'and');
        $map_tj['orderid']=$_REQUEST[id];
        $yh2=0;
        $tj=M('plmmaterialtj')->where($map_tj)->select();
        foreach ($tj as $key2 => $value2) {
            $yh2+=$value2['yh2'];
        }
        $this->assign('yh2',$yh2);
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		$price=0;
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
			$price+=$materials[$i][total];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);
		$tj=M('plmmaterialtj')->where(array('orderid'=>$_REQUEST[id]))->select();
		$total=0;
		$yf=0;
		$yhje=0;
		foreach ($tj as $key => $value) {
			$yf+=$value[yf];
			$yhje+=$value[yhje];
			$total+=$value[total]+$value[yf]-$value[yhje];
		}
		$total=$total-$yh2;
		$newname=M('plmmaterialorder')->where(array('id'=>$_REQUEST[id]))->getfield('newname');
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if(!empty($value)){
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}
		}
		$this->assign("yf",$yf);
		$this->assign("yhje",$yhje);
		$this->assign("total",$total);
		$this->assign("price",$price);
		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$orderinfo['handle']=explode(';',$orderinfo['handle']);
		$this->assign("orderinfo",$orderinfo);
		$this->assign("newname",$newname);
		$this->display();
	}
	
	public function choice()
	{
		if(!empty($_REQUEST[plmid])){
			$plmids=explode(",",$_REQUEST[plmid]);
			foreach($plmids as $key => $val)
			{
				if($val)
				{
					$_REQUEST['plmid']=$val;
					$cityold=$_REQUEST['city'];
					$_REQUEST['city']=M('project')->where(array('id'=>$_REQUEST['plmid']))->getfield('city');
					
					if($key!=0)
					{
						if($cityold!=$_REQUEST['city'])
						{
							echo "<div style='padding:20px;'>请选择相同城市的项目，按ESC键取消并重新选择。</div>";
							return;
						}
					}
				}
			}
		}
		
		
		if(!empty($_REQUEST[number])){
			$map[number]=array("like","%".$_REQUEST[number]."%");
		}
		if(!empty($_REQUEST[name])){
			$map[name]=array("like","%".$_REQUEST[name]."%");
		}
		
		if(!empty($_REQUEST[city])){
			$where[city]=array("like","%".$_REQUEST[city]."%");
			if(!empty($_REQUEST[supplier])){
				$where[supplier]=array("like","%".$_REQUEST[supplier]."%");
			}
			$where[status]=1;
			$supplier=M("supplier")->where($where)->field("supplier")->select();
			
			foreach($supplier as $i=>$va){
				$name[$i]=$va[supplier];
			}			
			$suppliers=array("in",$name);
		}
		
		
		
		$map[supplier]=$suppliers;
		$model = M("materials");
		// var_dump($supplier);die;

		$materials=$model->where($map)->order("number asc,price asc")->limit(100)->select();
		// var_dump($materials);die;
		// $plmmarerials=M("plmmarerials")->order("ctime desc")->field("price,number")->select();
		// foreach($plmmarerials as $i=>$vk){
		// $plmtemp[$vk[number]]=$vk[price];
		// }
		// $price='';
		foreach($materials as $k=>$val){
			//$price[]=$plmtemp[$val[number]];
			$info=M('material')->where(array('number'=>$val['number']))->find();
			if (!empty($info)) {
				$materials[$k][lowprice]=$info[min];
				if(empty($info[price2])){
					$materials[$k][threeprice]=$info[price1];
				}
				if (!empty($info[price2]) && empty($info[price3])) {
					$materials[$k][threeprice]=$info[price1].','.$info[price2];
				}
				if(!empty($info[price3])){
					$materials[$k][threeprice]=$info[price1].','.$info[price2].','.$info[price3];
				}

				
			}else{
				$materials[$k][lowprice]='无';
				$materials[$k][threeprice]='无';

			}
			// if(empty($price[0])){
			// 	$materials[$k][threeprice]='';
			// }
			// elseif(empty($price[1])){
			// 	$materials[$k][threeprice]=$price[0];
			// }
			// elseif(empty($price[2])){
			// 	$materials[$k][threeprice]=$price[0].','.$price[1];
			// }
			// else{
			// 	$materials[$k][threeprice]=$price[0].','.$price[1].','.$price[2];
			// };			
			// $min=$price[0];
			// foreach($price as $j=>$vo){
			// 	if($min>$vo){
			// 		$min=$vo;
			// 	}
			// }			
			// $materials[$k][lowprice]=$min;
		}
		$this->assign("materials",$materials);
		$this->assign("trkey",$_REQUEST[key]);
		$this->display();
	}
	public function history()
	{
		$model = M("materials");
		$materials=$model->where($map)->order("brand")->select();
		$this->assign("materials",$materials);
		
		$brand=M("brand")->field("id,name")->select();
		$this->assign("brand",$brand);
		
		$supplier=M("supplier")->where("status=1")->field("id,supplier")->select();
		$this->assign("supplier",$supplier);
		$this->assign("trkey",$_REQUEST[key]);
		
		$this->display();
	}
	
	public function pay()
	{
		$volist=M("plmmarerialorder")->where("id='".$_POST[id]."'")->find();
		$list=M("plmmarerials")->where("orderid='".$_POST[id]."'")->select();
	}
	
	public function ajaxcity()
	{
		// $map[design_status]=array('neq','完成验收');
		$map['city']=$_POST[city];
		$projects=M("Project")->where($map)->field("id,title")->select();
	    echo json_encode($projects);
	}
	public function ajaxgetsupplier()
	{
		$map['city']=array("like","%".$_POST[city]."%");
		$map[status]=array('eq','1');
		$projects=M("Supplier")->where($map)->field("id,supplier")->select();
	    echo json_encode($projects);
	}

	public function ajaxinsert()
	{	
		// dump('123');die;
		// dump($_POST['unit']);die;
		for($k=0;$k<count($_POST[number]);$k++){
				$supplier=$_POST[supplier][$k];
				if(!(M('supplier')->where(array('supplier'=>$supplier))->find())){
					echo 0;die;
				}
			}

		//判断单位是否为空
		for($i=0;$i<count($_POST['unit']);$i++){
			if(empty($_POST['unit'][$i])){
				echo 1;die;
			}
		}
		$time=time();
		$data[city]=$_POST[city];
		$data[plmid]=$_POST[plmid];
		$data[status]=0;
		$data[ctime]=$time;
		$data[submit]=0;
		$data[djtype]=0;
		$data[confirm]=0;
		$data[reamrk]=$_SESSION[loginUserName].'采购费';
		$data[user]=$_SESSION[name];
		$data[department]=$_SESSION[dept];
		$data[userid]=$_SESSION[number];
		$data["type"]=1;//采购下单 2 退回 3 余料转场 4 待提交 5余料退货
		$plminfo=M("Project")->where("id=".$_POST[plmid])->find();
		$data[plm]=$plminfo[title];
		$data[price]=$_POST[found];
		if($_REQUEST['orderid']){
			$res=M("plmmaterialorder")->where(array('id'=>htmlspecialchars($_REQUEST['orderid'])))->save($data);
			$id=htmlspecialchars($_REQUEST['orderid']);
			if(!empty($id)){
			$del=M('plmmaterials')->where(array('orderid'=>$id))->delete();
			for($k=0;$k<count($_POST[number]);$k++){
				//单位改为必填
				
				//$data1[brandid]=M("brand")->where("name='".$_POST[brand][$k]."'")->getfield("id");
				$data1[brand]=$_POST[brand][$k];
				//$data1[supplierid]=M("Supplier")->where("name='".$_POST[supplier][$k]."'")->getfield("id");
				$data1[supplier]=$_POST[supplier][$k];
				$data1[number]=$_POST[number][$k];
				$data1[name]=$_POST[name][$k];
				$data1[standard]=$_POST[standard][$k];
				$data1[unit]=$_POST[unit][$k];
				$data1[price]=$_POST[price][$k];
				$data1[threeprice]=$_POST[threeprice][$k];
				$data1[lowprice]=$_POST[lowprice][$k];
				$data1[count]=$_POST[count][$k];
				$data1[plmid]=$_POST[plmid];
				$data1[red]=$_POST[mid][$k];
				$data1[plm]=$plminfo[title];
				$data1[orderid]=$id;
				$data1[ctime]=$time;
				$data1["sort"]=$k;
				$material_id=M("plmmaterials")->add($data1);
				// echo json_encode($_POST[mid][$k]);die;				
				if(!empty($_POST[mid][$k])){
					$m=M('materials')->where(array('id'=>$_POST[mid][$k]))->find();
					$data2[number]=$_POST[number][$k];
					$data2[name]=$_POST[name][$k];
					$data2[standard]=$_POST[standard][$k];
					$data2[unit]=$_POST[unit][$k];
					$data2[price]=$_POST[price][$k];
					if($m['price']<$_POST[price][$k]){
						$data2[priceflag]=1;
					}else{
						$data2[priceflag]=0;
					}
					M("materials")->where("id='".$_POST[mid][$k]."'")->save($data2);
				}else{
					$data2[number]=$_POST[number][$k];
					$data2[name]=$_POST[name][$k];
					$data2[standard]=$_POST[standard][$k];
					$data2[unit]=$_POST[unit][$k];
					$data2[price]=$_POST[price][$k];
					$data2[supplier]=$_POST[supplier][$k];
					$data2[brand]=$_POST[brand][$k];
					$data2[ctime]=time();
					$supplier_id=M('supplier')->where(array('supplier'=>$data2[supplier]))->find();
					$supplier_id=$supplier_id['id'];
					$data2['supplierid']=$supplier_id;
					M("materials")->add($data2);
				}

				//插入历史价格表  编号
				if (!empty($_POST[number][$k])) {
					$model_material=M('material');
					$info_material=$model_material->where(array('number'=>$_POST[number][$k]))->find();
					if (!$info_material) {
						$data3[number]=$_POST[number][$k];
						$data3[price1]=$_POST[price][$k];
						$data3[plmmaterials_id1]=$material_id;
						$data3[min]=$_POST[price][$k];
						$data3[plmmaterials_min]=$material_id;
						$data3[status]=1;
						M('material')->add($data3);
					}else{
						$data3[plmmaterials_id3]=$info_material[plmmaterials_id2];
						$data3[plmmaterials_id2]=$info_material[plmmaterials_id1];
						$data3[plmmaterials_id1]=$material_id;
						$data3[price3]=$info_material[price2];
						$data3[price2]=$info_material[price1];
						$data3[price1]=$_POST[price][$k];
						if($info[min]>$_POST[price][$k]){
							$data3[min]=$_POST[price][$k];
							$data3[plmmaterials_min]=$material_id;
						}
						$res_material=M('material')->where(array('number'=>$_POST[number][$k]))->save($data3);
					}
				}
				
				
			}
		    echo json_encode($id);
			}
		}else{
			$date=date('Y-m-d');
			$data[handle]=$date.'--'.$_SESSION['loginUserName'].'新建订单;';
			$id=M("plmmaterialorder")->add($data);
			if(!empty($id)){
			for($k=0;$k<count($_POST[number]);$k++){
				//$data1[brandid]=M("brand")->where("name='".$_POST[brand][$k]."'")->getfield("id");
				$data1[brand]=$_POST[brand][$k];
				//$data1[supplierid]=M("Supplier")->where("name='".$_POST[supplier][$k]."'")->getfield("id");
				$data1[supplier]=$_POST[supplier][$k];
				$data1[number]=$_POST[number][$k];
				$data1[name]=$_POST[name][$k];
				$data1[standard]=$_POST[standard][$k];
				$data1[unit]=$_POST[unit][$k];
				$data1[price]=$_POST[price][$k];
				$data1[threeprice]=$_POST[threeprice][$k];
				$data1[lowprice]=$_POST[lowprice][$k];
				$data1[count]=$_POST[count][$k];
				$data1[red]=$_POST[mid][$k];
				$data1[plmid]=$_POST[plmid];
				$data1[plm]=$plminfo[title];
				$data1[orderid]=$id;
				$data1[ctime]=$time;
				$data1["sort"]=$k;
				$material_id=M("plmmaterials")->add($data1);
				// echo json_encode($_POST[mid][$k]);die;				
				if(!empty($_POST[mid][$k])){
					$m=M('materials')->where(array('id'=>$_POST[mid][$k]))->find();
					$data2[number]=$_POST[number][$k];
					$data2[name]=$_POST[name][$k];
					$data2[standard]=$_POST[standard][$k];
					$data2[unit]=$_POST[unit][$k];
					$data2[price]=$_POST[price][$k];
					if($m['price']<$_POST[price][$k]){
						$data2[priceflag]=1;
					}else{
						$data2[priceflag]=0;
					}
					M("materials")->where("id='".$_POST[mid][$k]."'")->save($data2);
				}else{
					$data2[number]=$_POST[number][$k];
					$data2[name]=$_POST[name][$k];
					$data2[standard]=$_POST[standard][$k];
					$data2[unit]=$_POST[unit][$k];
					$data2[price]=$_POST[price][$k];
					$data2[supplier]=$_POST[supplier][$k];
					$data2[brand]=$_POST[brand][$k];
					$data2[ctime]=time();
					$supplier_id=M('supplier')->where(array('supplier'=>$data2[supplier]))->find();
					$supplier_id=$supplier_id['id'];
					$data2['supplierid']=$supplier_id;
					M("materials")->add($data2);
				}
				//插入历史价格表  编号
				if (!empty($_POST[number][$k])) {
					$model_material=M('material');
					$info_material=$model_material->where(array('number'=>$_POST[number][$k]))->find();
					if (!$info_material) {
						$data3[number]=$_POST[number][$k];
						$data3[price1]=$_POST[price][$k];
						$data3[plmmaterials_id1]=$material_id;
						$data3[min]=$_POST[price][$k];
						$data3[plmmaterials_min]=$material_id;
						$data3[status]=1;
						M('material')->add($data3);
					}else{
						$data3[plmmaterials_id3]=$info_material[plmmaterials_id2];
						$data3[plmmaterials_id2]=$info_material[plmmaterials_id1];
						$data3[plmmaterials_id1]=$material_id;
						$data3[price3]=$info_material[price2];
						$data3[price2]=$info_material[price1];
						$data3[price1]=$_POST[price][$k];
						if($info[min]>$_POST[price][$k]){
							$data3[min]=$_POST[price][$k];
							$data3[plmmaterials_min]=$material_id;
						}
						$res_material=M('material')->where(array('number'=>$_POST[number][$k]))->save($data3);
					}
				}
				
			}
			
		    echo json_encode($id);
			}

		}
		
		
	}
	
	
	public function ajaxinsertpiliang()
	{	
		
		$supplierid=$_REQUEST[supplierid];
		$supplierinfo=M('supplier')->where(array('id'=>$supplierid))->find();
		if(!($supplierinfo)){
			echo 0;die;
		}

		//判断单位是否为空
		for($i=0;$i<count($_POST['unit']);$i++){
			if(empty($_POST['unit'][$i])){
				echo 1;die;
			}
		}
					
		$plmids=$_REQUEST[plmids];
		foreach($plmids as $key => $val)
		{
			if(($val)&&($plmids[$key]!=$plmids[$key-1]))
			{
				$_REQUEST['city']=M('project')->where(array('title'=>$val))->getfield('city');
				$_REQUEST['plmid']=M('project')->where(array('title'=>$val))->getfield('id');
					
		
				$time=time();
				$data[city]=$_REQUEST[city];
				$data[plmid]=$_REQUEST[plmid];
				$data[status]=0;
				$data[ctime]=$time;
				$data[submit]=0;
				$data[djtype]=0;
				$data[confirm]=0;
				$data[reamrk]=$_SESSION[loginUserName].'采购费';
				$data[user]=$_SESSION[name];
				$data[department]=$_SESSION[dept];
				$data[userid]=$_SESSION[number];
				$data["type"]=1;//采购下单 2 退回 3 余料转场 4 待提交 5余料退货
				$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
				$data[plm]=$plminfo[title];
				$data[price]=$_REQUEST[found];
				$data[suggestion]=json_encode($_REQUEST[lowprice]);
			
				$date=date('Y-m-d');
				$data[handle]=$date.'--'.$_SESSION['loginUserName'].'新建订单;';
				$pricetotal=0;
				$id=M("plmmaterialorder")->add($data);
				if(!empty($id)){
					
					for($k=0;$k<count($_REQUEST[number]);$k++){
						
						if($_REQUEST[plmids][$k]==$val)//$_REQUEST[plmids][$k]==$val
						{
							//$data1[brandid]=M("brand")->where("name='".$_REQUEST[brand][$k]."'")->getfield("id");
							$data1[brand]=$_REQUEST[brand][$k];
							//$data1[supplierid]=M("Supplier")->where("name='".$_REQUEST[supplier][$k]."'")->getfield("id");
							$data1[supplier]=$_REQUEST[supplier][$k];
							$data1[number]=$_REQUEST[number][$k];
							$data1[name]=$_REQUEST[name][$k];
							$data1[standard]=$_REQUEST[standard][$k];
							$data1[unit]=$_REQUEST[unit][$k];
							$data1[price]=$_REQUEST[price][$k];
							$data1[threeprice]=$_REQUEST[threeprice][$k];
							$data1[lowprice]=$_REQUEST[lowprice][$k];
							$data1[count]=$_REQUEST[count][$k];
							$data1[red]=$_REQUEST[mid][$k];
							$data1[plmid]=$_REQUEST[plmid];
							$data1[plm]=$plminfo[title];
							$data1[orderid]=$id;
							$data1[ctime]=$time;
							$data1["sort"]=$k;
							$material_id=M("plmmaterials")->add($data1);
							$pricetotal+=$data1[price]*$data1[count];
							// echo json_encode($_REQUEST[mid][$k]);die;				
							if(!empty($_REQUEST[mid][$k])){
								$m=M('materials')->where(array('id'=>$_REQUEST[mid][$k]))->find();
								$data2[number]=$_REQUEST[number][$k];
								$data2[name]=$_REQUEST[name][$k];
								$data2[standard]=$_REQUEST[standard][$k];
								$data2[unit]=$_REQUEST[unit][$k];
								$data2[price]=$_REQUEST[price][$k];
								if($m['price']<$_REQUEST[price][$k]){
									$data2[priceflag]=1;
								}else{
									$data2[priceflag]=0;
								}
								M("materials")->where("id='".$_REQUEST[mid][$k]."'")->save($data2);
							}else{
								$data2[number]=$_REQUEST[number][$k];
								$data2[name]=$_REQUEST[name][$k];
								$data2[standard]=$_REQUEST[standard][$k];
								$data2[unit]=$_REQUEST[unit][$k];
								$data2[price]=$_REQUEST[price][$k];
								$data2[supplier]=$_REQUEST[supplier][$k];
								$data2[brand]=$_REQUEST[brand][$k];
								$data2[ctime]=time();
								$supplier_id=M('supplier')->where(array('supplier'=>$data2[supplier]))->find();
								$supplier_id=$supplier_id['id'];
								$data2['supplierid']=$supplier_id;
								M("materials")->add($data2);
							}
							//插入历史价格表  编号
							if (!empty($_REQUEST[number][$k])) {
								$model_material=M('material');
								$info_material=$model_material->where(array('number'=>$_REQUEST[number][$k]))->find();
								if (!$info_material) {
									$data3[number]=$_REQUEST[number][$k];
									$data3[price1]=$_REQUEST[price][$k];
									$data3[plmmaterials_id1]=$material_id;
									$data3[min]=$_REQUEST[price][$k];
									$data3[plmmaterials_min]=$material_id;
									$data3[status]=1;
									M('material')->add($data3);
								}else{
									$data3[plmmaterials_id3]=$info_material[plmmaterials_id2];
									$data3[plmmaterials_id2]=$info_material[plmmaterials_id1];
									$data3[plmmaterials_id1]=$material_id;
									$data3[price3]=$info_material[price2];
									$data3[price2]=$info_material[price1];
									$data3[price1]=$_REQUEST[price][$k];
									if($info[min]>$_REQUEST[price][$k]){
										$data3[min]=$_REQUEST[price][$k];
										$data3[plmmaterials_min]=$material_id;
									}
									$res_material=M('material')->where(array('number'=>$_REQUEST[number][$k]))->save($data3);
								}
							}
							
							
							M("plmmaterialorder")->where("id=".$id)->setField("price",$pricetotal);
						}
						else
						{
							$pricetotal=0;
						}
					}
					//echo json_encode($id);
					
					
					
					
					
					
					
					
					
					
					
					
					$datax[status]=0;
					$datax[type]=1;
					$datax[confirm]=1;
					M("plmmaterialorder")->where("id='".$id."'")->save($datax);
					if($_REQUEST[isedit] && (!($_REQUEST['tj_id']))){
						M('plmmaterialtj')->where(array('orderid'=>$id))->delete();
					}
					$plm=M("plmmaterialorder")->where("id='".$id."'")->getfield("plm");
					$city=M("plmmaterialorder")->where("id='".$id."'")->getfield("city");
					$plmmaterials=M("plmmaterials")->where("orderid='".$id."'")->field("id,price,count,supplier")->select();
					
					$count=1;		
					for($i=0;$i<$count;$i++){
						$total=0;
						$clid="";
						$datas[status]=0;//设置了付款时间
						$datas[status]=-1;//未设置付款时间
						foreach($plmmaterials as $k=>$va){
							if($va[supplier]==$_POST[supplier][$i]){
								$total+=$va[price]*$va[count];
								$clid.=$va[id].",";
							}
						}
						$datas[total]=round($total,2);
						$datas[supplier]=$supplierinfo[supplier];
						$datas[orderid]=$id;
						$datas[clid]=$clid;
						$datas[dj]=0;
						$datas[yf]=0;
						$datas[spare]=0;//$_POST['spare']
						if($_POST['spare']==1){
							$datas[status]=1;
							$datas[ctime]=time();
							$datas['handlehistroy']="<td>".date('Y-m-d H:i',time())."</td><td>".($total-$_POST[yhje][$i]+$_POST[yf][$i])."</td><td>".$_SESSION['loginUserName']."</td><td>备用金支付</td>;";
						}
						$datas[yhje]=0;
						$datas[enddate]="";
						$datas[type]=1;
						$datas[city]=$city;
						$datas[plm]=$plm;
						$datas[department]=$_SESSION[dept];
						$datas[shiyou]=$_REQUEST[shiyou];
						if($_REQUEST[isedit] && (!($_REQUEST['tj_id']))){
							$datas[sqr]=$_SESSION['loginUserName'];
							$supplierInfo=M('supplier')->where(array('supplier'=>$_POST[supplier][$i]))->find();
							$datas[bankaccount]=$supplierInfo[bankname];
							$datas[banknumber]=$supplierInfo[bankaccount];
							$datas[bankaddress]=$supplierInfo[bankhu];
							M("plmmaterialtj")->add($datas);
						}else{
							if($_REQUEST['tj_id']){
								$datas[dj_type]=0;
								$tjinfo=M('plmmaterialtj')->where(array('id'=>$_REQUEST['tj_id']))->find();
								$handle=$tjinfo[handlehistroy];
								if($_POST['spare']==1){
									$datas[status]=1;
									$datas[handlehistroy]=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".($total-$_POST[yhje][$i]+$_POST[yf][$i]-$_POST[dj][$i])."</td><td>".$_SESSION['loginUserName']."</td><td>备用金支付</td>;";
								}else{
									$datas[status]=2;
								}
								$datas[sqr]=$_SESSION['loginUserName'];
								M("plmmaterialtj")->where(array('id'=>$_REQUEST['tj_id']))->save($datas);
							}else{
								$datas[sqr]=$_SESSION['loginUserName'];
								M("plmmaterialtj")->add($datas);
							}
						}
							
					}
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					
					$ids.=$id.",";
				}
				
			}
	
		}
		echo json_encode($ids);
	}
	
	public function ajaxinsert2()
	{
		
		if(empty($_POST['supplier']) || empty($_POST['price'])){
			 echo 0;
		}else{
			$time=time();
			$data[city]=$_POST[city];
			$data[reamrk]=$_POST[remark];
			$data[plmid]=$_POST[plmid];
			$data[status]=0;
			$data[djtype]=1;
			$data[confirm]=0;
			$data[ctime]=$time;
			$data[user]=$_SESSION[name];
			$data[department]=$_SESSION[dept];
			$data[userid]=$_SESSION[number];
			$data["type"]=1;//采购下单 2 退回 3 余料转场 4 待提交 5余料退货 6定金
			$plminfo=M("Project")->where("id=".$_POST[plmid])->find();
			$data[plm]=$plminfo[title];
			$data[price]=$_POST[price];
			$date=date('Y-m-d');
			$data[handle]=$date.'--'.$_SESSION['loginUserName'].'发起定金,付款内容:'.$_REQUEST['remark'].'.;';
			$id=M("plmmaterialorder")->add($data);
			if(!empty($id)){
				$supplier_id=$_POST[supplier];
				$name=M('supplier')->where(array('id'=>$supplier_id))->find();
				$data1[supplier]=$name['supplier'];
				$data1[price]=$_POST[price];
				$data1[plmid]=$_POST[plmid];
				$data1[plm]=$plminfo[title];
				$data1[orderid]=$id;
				$data1[ctime]=$time;
				// $data1["sort"]=$k;
				$id2=M("plmmaterials")->add($data1);
				// echo json_encode($id2);die;
			}
			echo json_encode($id);
		}
	}

	public function confirm2()
	{
		$materials=M("plmmaterials")->where("orderid='".$_REQUEST[id]."'")->order("supplier asc")->field("name,number,supplier,price,count")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$volist=M("plmmaterialorder")->where("id='".$_REQUEST[id]."'")->find();
		$this->assign("volist",$volist);
		$this->assign("materials",$materials);
		$this->display();
	}
	
	
	public function ajaxupdate()
	{	
		for($k=0;$k<count($_POST[number]);$k++){
				$supplier=$_POST[supplier][$k];
				if(!(M('supplier')->where(array('supplier'=>$supplier))->find())){
					echo 0;die;
				}
			}
		$time=time();
		$data[city]=$_POST[city];
		$data[plmid]=$_POST[plmid];
		$data[status]=0;
		$data[ctime]=$time;
		$data[confirm]=0;
		$data[user]=$_SESSION[name];
		$data[department]=$_SESSION[dept];
		$data[userid]=$_SESSION[number];
		$data["type"]=1;//采购下单 2 退回 3 余料转场 4 待提交 5余料退货
		$plminfo=M("Project")->where("id=".$_POST[plmid])->find();
		$data[plm]=$plminfo[title];
		$data[price]=$_POST[found];
		if($_REQUEST['orderid']){
			$res=M("plmmaterialorder")->where(array('id'=>htmlspecialchars($_REQUEST['orderid'])))->save($data);
			$id=htmlspecialchars($_REQUEST['orderid']);
			if(!empty($id)){
			$del=M('plmmaterials')->where(array('orderid'=>$id))->delete();
			for($k=0;$k<count($_POST[number]);$k++){
				//$data1[brandid]=M("brand")->where("name='".$_POST[brand][$k]."'")->getfield("id");
				$data1[brand]=$_POST[brand][$k];
				//$data1[supplierid]=M("Supplier")->where("name='".$_POST[supplier][$k]."'")->getfield("id");
				$data1[supplier]=$_POST[supplier][$k];
				$data1[number]=$_POST[number][$k];
				$data1[name]=$_POST[name][$k];
				$data1[standard]=$_POST[standard][$k];
				$data1[unit]=$_POST[unit][$k];
				$data1[price]=$_POST[price][$k];
				$data1[threeprice]=$_POST[threeprice][$k];
				$data1[lowprice]=$_POST[lowprice][$k];
				$data1[count]=$_POST[count][$k];
				$data1[plmid]=$_POST[plmid];
				$data1[red]=$_POST[mid][$k];
				$data1[plm]=$plminfo[title];
				$data1[orderid]=$id;
				$data1[ctime]=$time;
				$data1["sort"]=$k;
				$material_id=M("plmmaterials")->add($data1);
				// echo json_encode($_POST[mid][$k]);die;				
				if(!empty($_POST[mid][$k])){
					$m=M('materials')->where(array('id'=>$_POST[mid][$k]))->find();
					$data2[number]=$_POST[number][$k];
					$data2[name]=$_POST[name][$k];
					$data2[standard]=$_POST[standard][$k];
					$data2[unit]=$_POST[unit][$k];
					$data2[price]=$_POST[price][$k];
					if($m['price']<$_POST[price][$k]){
						$data2[priceflag]=1;
					}else{
						$data2[priceflag]=0;
					}
					M("materials")->where("id='".$_POST[mid][$k]."'")->save($data2);
				}else{
					$data2[number]=$_POST[number][$k];
					$data2[name]=$_POST[name][$k];
					$data2[standard]=$_POST[standard][$k];
					$data2[unit]=$_POST[unit][$k];
					$data2[price]=$_POST[price][$k];
					$data2[supplier]=$_POST[supplier][$k];
					$data2[brand]=$_POST[brand][$k];
					$data2[ctime]=time();
					$supplier_id=M('supplier')->where(array('supplier'=>$data2[supplier]))->find();
					$supplier_id=$supplier_id['id'];
					$data2['supplierid']=$supplier_id;
					M("materials")->add($data2);
				}

				//插入历史价格表  编号
				if (!empty($_POST[number][$k])) {
					$model_material=M('material');
					$info_material=$model_material->where(array('number'=>$_POST[number][$k]))->find();
					if (!$info_material) {
						$data3[number]=$_POST[number][$k];
						$data3[price1]=$_POST[price][$k];
						$data3[plmmaterials_id1]=$material_id;
						$data3[min]=$_POST[price][$k];
						$data3[plmmaterials_min]=$material_id;
						$data3[status]=1;
						M('material')->add($data3);
					}else{
						$data3[plmmaterials_id3]=$info_material[plmmaterials_id2];
						$data3[plmmaterials_id2]=$info_material[plmmaterials_id1];
						$data3[plmmaterials_id1]=$material_id;
						$data3[price3]=$info_material[price2];
						$data3[price2]=$info_material[price1];
						$data3[price1]=$_POST[price][$k];
						if($info[min]>$_POST[price][$k]){
							$data3[min]=$_POST[price][$k];
							$data3[plmmaterials_min]=$material_id;
						}
						$res_material=M('material')->where(array('number'=>$_POST[number][$k]))->save($data3);
					}
				}


				
			}
		    echo json_encode($id);
			}
		}
	}
	
	public function confirm(){
		$materials=M("plmmaterials")->where("orderid='".$_REQUEST[id]."'")->order("supplier asc")->field("name,number,supplier,price,count")->select();
		$isedit=$_REQUEST[isedit];
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$volist=M("plmmaterialorder")->where("id='".$_REQUEST[id]."'")->field("id,price")->find();
		// var_dump($_REQUEST['tj_id']);die;
		if(!empty($_REQUEST['tj_id'])){
			$info=M('plmmaterialtj')->where(array('id'=>$_REQUEST['tj_id']))->find();
			$info2=M('plmmaterialtj')->where(array('id'=>$_REQUEST['tj_id']))->save(array('approve'=>0));
			$dj=$info['dj'];
			$this->assign("dj",$dj);
			$this->assign("tj_id",$_REQUEST['tj_id']);

		}else{
			$this->assign("tj_id",'0');
		}
		$this->assign("volist",$volist);
		$this->assign("isedit",$isedit);
		$this->assign("materials",$materials);
		
		//判断近一个月是否有相同金额的订单
		$mapforrepeat["id"]=array("neq",$_REQUEST[id]);
		$mapforrepeat["ctime"]=array("egt",time()-30*24*60*60);
		$mapforrepeat["price"]=array(array("eq",$volist[price]),array("eq",str_replace(".00","",$volist[price])),array("eq",$volist[price].".00"),"or");
		$mapforrepeat["confirm"]=array("eq",1);
		$repeat=M("plmmaterialorder")->where($mapforrepeat)->find();
		$this->assign("repeat",$repeat);
		
		$this->display();
	}
	
	
	public function confirmpiliang(){
		$ids=$_REQUEST[ids];
		
		
		foreach($ids as $key => $val)
		{
			if($val)
			{
				$_REQUEST[id]=$val;
				
				
				$materials=M("plmmaterials")->where("orderid='".$_REQUEST[id]."'")->order("supplier asc")->field("name,number,supplier,price,count")->select();
				$isedit=$_REQUEST[isedit];
				foreach($materials as $i=>$va){
					$materials[$i][total]=$va[price]*$va[count];
				}
				$volist=M("plmmaterialorder")->where("id='".$_REQUEST[id]."'")->field("id,price")->find();
				// var_dump($_REQUEST['tj_id']);die;
				if(!empty($_REQUEST['tj_id'])){
					$info=M('plmmaterialtj')->where(array('id'=>$_REQUEST['tj_id']))->find();
					$info2=M('plmmaterialtj')->where(array('id'=>$_REQUEST['tj_id']))->save(array('approve'=>0));
					$dj=$info['dj'];
					$this->assign("dj",$dj);
					$this->assign("tj_id",$_REQUEST['tj_id']);

				}else{
					$this->assign("tj_id",'0');
				}
				$data[$key][volist]=$volist;
				$data[$key][isedit]=$isedit;
				$data[$key][materials]=$materials;
				
				/*
				$this->assign("volist",$volist);
				$this->assign("isedit",$isedit);
				$this->assign("materials",$materials);
				*/
				
				
				//判断近一个月是否有相同金额的订单
				$mapforrepeat["id"]=array("neq",$_REQUEST[id]);
				$mapforrepeat["ctime"]=array("egt",time()-30*24*60*60);
				$mapforrepeat["price"]=array(array("eq",$volist[price]),array("eq",str_replace(".00","",$volist[price])),array("eq",$volist[price].".00"),"or");
				$mapforrepeat["confirm"]=array("eq",1);
				$repeat=M("plmmaterialorder")->where($mapforrepeat)->find();
				//$this->assign("repeat",$repeat);
				$data[$key][repeat]=$repeat;
				
				$this->assign("data",$data);
				
				
			}
		}
		
		
		
		$this->display();
	}
	
	public function subinsert(){
		$data[status]=0;
		$data[type]=1;
		$data[confirm]=1;
		$data[price]=$_POST[total];
		M("plmmaterialorder")->where("id='".$_POST[id]."'")->save($data);
		if($_REQUEST[isedit] && (!($_REQUEST['tj_id']))){
			M('plmmaterialtj')->where(array('orderid'=>$_POST['id']))->delete();
		}
		$plm=M("plmmaterialorder")->where("id='".$_POST[id]."'")->getfield("plm");
		$city=M("plmmaterialorder")->where("id='".$_POST[id]."'")->getfield("city");
		$plmmaterials=M("plmmaterials")->where("orderid='".$_POST[id]."'")->field("id,price,count,supplier")->select();
		$count=count($_POST[supplier]);		
		for($i=0;$i<$count;$i++){
			$total=0;
			$clid="";
			if(!empty($_POST[enddate][$i])){
				$datas[status]=0;//设置了付款时间
			}else{
				$datas[status]=-1;//未设置付款时间
			}
			foreach($plmmaterials as $k=>$va){
				if($va[supplier]==$_POST[supplier][$i]){
					$total+=$va[price]*$va[count];
					$clid.=$va[id].",";
				}
			}
			$datas[total]=round($total,2);
			$datas[supplier]=$_POST[supplier][$i];
			$datas[orderid]=$_POST[id];
			$datas[clid]=$clid;
			$datas[dj]=$_POST[dj][$i];
			$datas[yf]=$_POST[yf][$i];
			$datas[spare]=$_POST['spare'];
			if($_POST['spare']==1){
				$datas[status]=1;
				$datas[ctime]=time();
				$datas['handlehistroy']="<td>".date('Y-m-d H:i',time())."</td><td>".($total-$_POST[yhje][$i]+$_POST[yf][$i])."</td><td>".$_SESSION['loginUserName']."</td><td>备用金支付</td>;";
			}
			$datas[yhje]=$_POST[yhje][$i];
			$datas[enddate]=$_POST[enddate][$i];
			$datas[type]=1;
			$datas[city]=$city;				
			$datas[plm]=$plm;
			$datas[department]=$_SESSION[dept];
			$datas[shiyou]=$_REQUEST[shiyou];
			if($_REQUEST[isedit] && (!($_REQUEST['tj_id']))){
				$datas[sqr]=$_SESSION['loginUserName'];
				$supplierInfo=M('supplier')->where(array('supplier'=>$_POST[supplier][$i]))->find();
				$datas[bankaccount]=$supplierInfo[bankname];
				$datas[banknumber]=$supplierInfo[bankaccount];
				$datas[bankaddress]=$supplierInfo[bankhu];
				M("plmmaterialtj")->add($datas);
			}else{
				if($_REQUEST['tj_id']){
					$datas[dj_type]=0;
					$tjinfo=M('plmmaterialtj')->where(array('id'=>$_REQUEST['tj_id']))->find();
					$handle=$tjinfo[handlehistroy];
					if($_POST['spare']==1){
						$datas[status]=1;
						$datas[handlehistroy]=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".($total-$_POST[yhje][$i]+$_POST[yf][$i]-$_POST[dj][$i])."</td><td>".$_SESSION['loginUserName']."</td><td>备用金支付</td>;";
					}else{
						$datas[status]=2;
					}
					$datas[sqr]=$_SESSION['loginUserName'];
					M("plmmaterialtj")->where(array('id'=>$_REQUEST['tj_id']))->save($datas);
				}else{
					$datas[sqr]=$_SESSION['loginUserName'];
					M("plmmaterialtj")->add($datas);
				}
			}
				
		}
		if($_REQUEST['tj_id']){
			$this->redirect('index',array('tab'=>2,'tj_id'=>$_REQUEST['tj_id']));
		}else{
			$this->redirect('index','tab=2/');
		}
		
	}
	public function subinsert2(){
		$data[status]=0;
		$data[type]=1;
		$data[confirm]=1;
		$data[price]=$_POST[total];
		$data['from']=$_POST['dj'];
		M("plmmaterialorder")->where("id='".$_POST[id]."'")->save($data);
		$plm=M("plmmaterialorder")->where("id='".$_POST[id]."'")->getfield("plm");
		$plmcity=M("plmmaterialorder")->where("id='".$_POST[id]."'")->getfield("city");
		$plmmaterials=M("plmmaterials")->where("orderid='".$_POST[id]."'")->field("id,price,count,supplier")->select();
		$count=count($_POST[supplier]);		
		for($i=0;$i<$count;$i++){
			$total=0;
			$clid="";
			if(!empty($_POST[enddate][$i])){
				$datas[status]=0;//设置了付款时间
			}else{
				$datas[status]=-1;//未设置付款时间
			}
			foreach($plmmaterials as $k=>$va){
				if($va[supplier]==$_POST[supplier][$i]){
					$total+=$va[price]*$va[count];
					$clid.=$va[id].",";
				}
			}
			// $datas[total]=$total;
			$datas[supplier]=$_POST[supplier][$i];
			$supplierInfo=M('supplier')->where(array('supplier'=>$_POST[supplier][$i]))->find();
			$datas[bankaccount]=$supplierInfo[bankname];
			$datas[banknumber]=$supplierInfo[bankaccount];
			$datas[bankaddress]=$supplierInfo[bankhu];
			$datas[orderid]=$_POST[id];
			$datas[clid]=$clid;
			$datas[total]=round($data['from'],2);
			// $datas[pay]=round($data['from'],2);
			$datas[dj]=$data['from'];
			$datas[yf]=$_POST[yf][$i];
			$datas[yhje]=$_POST[yhje][$i];
			$datas[enddate]=$_POST[enddate][$i];
			$datas[type]=1;	
			$datas[sqr]=$_SESSION['loginUserName'];	
			$datas[department]=$_SESSION[dept];
			$datas[dj_type]=1;			
			$datas[plm]=$plm;
			$datas[city]=$plmcity;
			$datas[shiyou]=$_REQUEST[shiyou];
			$datas[spare]=$_POST['spare'];
			if($_POST['spare']==1){
				$datas[status]=1;
				$datas[ctime]=time();
				$datas['handlehistroy']="<td>".date('Y-m-d H:i',time())."</td><td>".($data['from'])."</td><td>".$_SESSION['loginUserName']."</td><td>备用金支付</td>;";
			}
			
			M("plmmaterialtj")->add($datas);
		}
		$this->redirect('index','tab=2/');
	}
	
	public function ajaxdelete(){
		M("plmmaterials")->where("orderid='".$_POST[id]."'")->delete();
		M("plmmaterialorder")->where("id='".$_POST[id]."'")->delete();
		M("plmmaterialtj")->where("orderid='".$_POST[id]."'")->delete();
		echo 1;
	}
	
	public function ajaxcl(){
		/*
		$data[status]=1;
		$data[indate]=time();
		$data[approver]=$_SESSION[loginUserName];
		M("Plmsend")->where("id='".$_POST[id]."'")->save($data);
		*/
		M("Plmfilediaodu")->where("id='".$_POST[id]."'")->delete();
		echo 1;
	}
//提交审核
	public function ajaxsubmit(){
		$info=M('plmmaterialorder')->find($_POST['id']);
		$date=date("Y-m-d");
		$data['submit']=1;
		$data['status']=0;
		$data['handle']=$info['handle'].$date.'--'.$_SESSION['loginUserName'].'提交了申请;';
		M("plmmaterialorder")->where("id='".$_POST[id]."'")->save($data);
		echo 1;
	}
	//上传附件
	public function upload(){
		$id=$_REQUEST[id];        
        $this->assign("id",$id);		
		$this->display();
	}
	public function uploadupdate(){
		// dump($_FILES);die;
		foreach ($_FILES['file'][name] as $key => $value) {
			if(!empty($_FILES['file']['name'][$key]))
			{
				$map[id]=array("in",$_POST[id]);			
				$orderinfo=M("plmmaterialorder")->where($map)->field("id,newname,filename")->find();
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$key])))); 
				$uuid=uniqid(rand(), false).$key;//防止生成相同文件名id
				$newname = $uuid.'.'.$ext;
				$upload_file = $savePath.$newname;	
				$filename=$_FILES['file']['name'][$key];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				$res1=move_uploaded_file($_FILES['file']['tmp_name'][$key],$upload_file);
				if($res1){
					if(!empty($orderinfo[newname])){
						$data[newname] = $orderinfo[newname].','.$newname;
						$data[filename] = $orderinfo[filename].','.$_FILES['file']['name'][$key];
					}else{
						$data[newname] = $newname;
						$data[filename] = $_FILES['file']['name'][$key];
					}
				}
				M("plmmaterialorder")->where("id='".$_POST[id]."'")->save($data);
				$tj=M('plmmaterialtj')->where(array('orderid'=>$_POST[id]))->select();
				foreach ($tj as $key => $value) {
					if(!empty($value[newname])){
						$data1[newname] = $value[newname].','.$newname;
						$data1[filename] = $value[filename].','.$_FILES['file']['name'][$key];
					}else{
						$data1[newname] = $newname;
						$data1[filename] = $_FILES['file']['name'][$key];
					}
					M("plmmaterialtj")->where("id='".$value[id]."'")->save($data1);
				}

								
			}
		}
		if($res1){
			$this->success('上传成功.');
		}else{
			$this->error('上传失败,请重试');
		}
		
	}

	//上传附件
	public function tj_upload(){
		$id=$_REQUEST[id];        
        $this->assign("id",$id);		
		$this->display();
	}
	public function tj_update(){
		// dump($_FILES);die;
		$id=$_REQUEST[id];
		$map[id]=$id;
		foreach ($_FILES['file']['name'] as $key => $value) {
			$tj=M("plmmaterialtj")->where($map)->field("id,newname,filename")->find();
			if(!empty($_FILES['file']['name'][$key]))
			{
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$key])))); 
				$uuid=uniqid(rand(), false).$key;
				$newname = filter_var($uuid.'.'.$ext, FILTER_CALLBACK, array("options"=>"convertSpace"));
				$upload_file = $savePath.$newname;
				
				$filename=$_FILES['file']['name'][$key];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				$res1=move_uploaded_file($_FILES['file']['tmp_name'][$key],$upload_file);
				if($res1){
					if(!empty($tj[newname])){
						$data[newname] = $tj[newname].','.$newname;
						$data[filename] = $tj[filename].','.$_FILES['file']['name'][$key];
					}else{
						$data[newname] = $newname;
						$data[filename] = $_FILES['file']['name'][$key];
					}
				}
				$data['file_status']=1;						
				M("plmmaterialtj")->where($map)->save($data);		
			}
		}
		if ($res1) {
			$this->success('上传附件成功。');
		}else{
			$this->error('上传附件失败,请重试。');
		}
	}
	public function tjinfo(){
        $map[id]=$_REQUEST[id];
		$list=M("plmmaterialtj")->where($map)->find();
		$dept=M('plmmaterialorder')->where(array('id'=>$list[orderid]))->getfield('department');
		$this->assign('department',$dept);
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		if(!empty($list[newname])){
			$newname=$list[newname];
		}
		$Total=$this->cny($total);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$material=M("plmmaterialtj")->where($map)->group("plm")->field("plm,sum(total+yf-yhje-yh2) total")->select();
		$con[id]=$list[orderid];
		$order=M("plmmaterialorder")->where($con)->field("user,ctime")->find();
		$ctime=$order[ctime];
		$histroy=$list[handlehistroy];
		$array=explode(';',$histroy);	
        $this->assign("array",$array);
		$this->assign("list",$list);
        $this->assign("supplier",$supplier);
        $this->assign("total",$total);
		$this->assign("Total",$Total);
		$this->assign("ctime",$ctime);
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if (!empty($value)) {
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}	
		}
		$this->assign("newname",$newname);
		$this->display();
	}
	function cny($ns) { //人民币转大写
		static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"), 
			$cnyunits=array("圆","角","分"), 
			$grees=array("拾","佰","仟","万","拾","佰","仟","亿"); 
		list($ns1,$ns2)=explode(".",$ns,2); 
		$ns2=array_filter(array($ns2[1],$ns2[0])); 
		$ret=array_merge($ns2,array(implode("",$this->_cny_map_unit(str_split($ns1),$grees)),"")); 
		$ret=implode("",array_reverse($this->_cny_map_unit($ret,$cnyunits))); 
		return str_replace(array_keys($cnums),$cnums,$ret); 
	}
	
	function _cny_map_unit($list,$units) { 
		$ul=count($units); 
		$xs=array(); 
		foreach (array_reverse($list) as $x) { 
			$l=count($xs); 
			if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]); 
			else $n=is_numeric($xs[0][0])?$x:''; 
			array_unshift($xs,$n); 
		} 
		return $xs; 
	}
	public function addpay(){
		$map[id]=$_REQUEST[id];
		$list=M("plmmaterialtj")->where($map)->find();
		$dept=M('plmmaterialorder')->where(array('id'=>$list[orderid]))->getfield('department');
		$this->assign('department',$dept);
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		if(!empty($list[newname])){
			$newname=$list[newname];
		}
		$Total=$this->cny($total);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$material=M("plmmaterialtj")->where($map)->group("plm")->field("plm,sum(total+yf-yhje-yh2) total")->select();
		$con[id]=$list[orderid];
		$order=M("plmmaterialorder")->where($con)->field("user,ctime")->find();
		$ctime=$order[ctime];
		$histroy=$list[handlehistroy];
		$array=explode(';',$histroy);	
        $this->assign("array",$array);
		$this->assign("list",$list);
        $this->assign("supplier",$supplier);
        $this->assign("total",$total);
		$this->assign("Total",$Total);
		$this->assign("ctime",$ctime);
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if (!empty($value)) {
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}	
		}
		$this->assign("newname",$newname);
		$this->display();
	}
	public function addpaySubmit(){
		$id=$_REQUEST[id];
		if(!empty($_REQUEST[addpay])){
			$info=M('plmmaterialtj')->find($id);
			$data[addpay]=1;
			$data[status]=2;
			$data[total]=$info[total]+$_REQUEST[addpay];
			$data[dj]=$info['dj']+$_REQUEST[addpay];
			$data[approve]=0;
			$data[handlehistroy]=$info[handlehistroy]."<td>".date('Y-m-d H:i',time())."</td><td>".$_REQUEST[addpay]."</td><td>".$_SESSION['loginUserName']."</td><td>申请增加定金</td>;";
			$res1=M('plmmaterialtj')->where(array('id'=>$id))->save($data);
			$orderid=$info[orderid];
			$info2=M('plmmaterialorder')->find($orderid);
			$data2[status]=0;
			$data2[from]=$info2[from]+$_REQUEST[addpay];
			$data2[price]=$info2[price]+$_REQUEST[addpay];
			$data2[reamrk]=$_SESSION[loginUserName]."增加定金:".$_REQUEST[addpay];
			$date=date('Y-m-d');
			$data2[handle]=$info2['handle'].$date.'--'.$_SESSION[loginUserName]."增加定金:".$_REQUEST[addpay].';';
			$res2=M('plmmaterialorder')->where(array('id'=>$orderid))->save($data2);
			$info3=M('plmmaterials')->where(array('orderid'=>$orderid))->find();
			$data3[price]=$info3[price]+$_REQUEST[addpay];
			$res3=M('plmmaterials')->where(array('orderid'=>$orderid))->save($data3);
		}
		$this->success('增加定金成功.');
	}
	public function delFile(){
		$str=$_REQUEST[arr];
		$id=$_REQUEST[id];
		$arr=explode(';',$str);
		$info=M('plmmaterialorder')->find($id);
		$newname=explode(',',$info[newname]);
		foreach ($newname as $key => $value) {
			foreach ($arr as $key2 => $value2) {
				if($value2==$value){
					unset($newname[$key]);
				}
			}
		}
		$newnameStr=implode(',',$newname);
		$data[newname]=$newnameStr;
		$data[id]=$id;
		$res=M('plmmaterialorder')->save($data);
		if($res){
			echo 1;
		}else{
			echo 2;
		}
	}
	
	
	
	public function getexcel()
	{
		if(empty($_FILES["file"]["name"]))
		{
			$this->error("请上传文件！");
		}
		$file_name = explode(".",$_FILES["file"]["name"]);
		if(($_FILES["file"]["type"] == "application/vnd.ms-excel")||($_FILES["file"]["type"] == "application/octet-stream")||($_FILES["file"]["type"] == "application/kset"))
		{												
			header("Content-type: text/html; charset=utf-8");
			error_reporting(E_ALL ^ E_NOTICE);
			$Import_TmpFile = $_FILES['file']['tmp_name'];
			Vendor('Excelload.reader');  //导入thinkphp 中第三方插件库
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('UTF-8');
			$data->read($Import_TmpFile);
			$array =array();
			for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
			{
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
				{
					$array[$i][$j] = $data->sheets[0]['cells'][$i][$j];
				}
			}
			$num=count($array);
			$number=$num;
			$time=time();
			$supplierid=$_REQUEST[supplierid];
			$supplierinfo=M('Supplier')->where(array('id'=>$supplierid))->find();
			for($k=2;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
					/*
					门店						材料编号	材料名称	材料类别	材料型号	材料单位	价格	数量
					北京易事达购物中心			01013		铝方通		吊顶类		亚光白		支			1		100
					北京易事达购物中心			01013		铝方通		吊顶类		亚光白		支			2		100
					北京通州万达店（成品店）	01013		铝方通		吊顶类		亚光白		支			3		50
					*/
					/*
					材料编号	材料名称	材料类别	规格型号	单位	供应商	价格	近三次价格	历史价格	数量
					*/
					$mapforProject["title"]=$array[$k]['1'];
					$projectinfo=M("Project")->where($mapforProject)->find();
					if(empty($projectinfo))
					{
						$newdata[$k-1]['0']="门店名称不匹配";
					}
					else
					{
						$newdata[$k-1]['0']=$array[$k]['1'];
					}
					
					$mapforMaterials["number"]=$array[$k]['2'];
					$mapforMaterials["name"]=$array[$k]['3'];
					$mapforMaterials["brand"]=$array[$k]['4'];
					$mapforMaterials["standard"]=$array[$k]['5'];
					$mapforMaterials["unit"]=$array[$k]['6'];
					$mapforMaterials["supplierid"]=$supplierid;
					$materialinfo=M("Materials")->where($mapforMaterials)->find();
					if(empty($materialinfo))
					{
						$dataforMaterials["number"]=$array[$k]['2'];
						$dataforMaterials["name"]=$array[$k]['3'];
						$dataforMaterials["brand"]=$array[$k]['4'];
						$mapforBrand["name"]=$array[$k]['4'];
						$dataforMaterials["brandid"]=M("Brand")->where($mapforBrand)->getField("name");
						if(empty($dataforMaterials["brandid"]))
						{
							$branddata[name]=$dataforMaterials["brand"];
							$branddata[ctime]=time();
							$branddata[type]="piliang";
							$brandid=M("Brand")->add($branddata);
							$dataforMaterials["brandid"]=$brandid;
						}
						$dataforMaterials["standard"]=$array[$k]['5'];
						$dataforMaterials["unit"]=$array[$k]['6'];
						$dataforMaterials["price"]=$array[$k]['7'];
						$dataforMaterials["supplierid"]=$supplierid;
						$dataforMaterials["supplier"]=M("Supplier")->where("id=".$supplierid)->getField("supplier");
						$dataforMaterials["ctime"]=time();
						$dataforMaterials["type"]="piliang";
						$materialid=M("Materials")->add($dataforMaterials);
						
						$materialinfo=M("Materials")->where("id=".$materialid)->find();
						$newdata[$k-1]['1_0']=$materialid;
					}
					else
					{
						
						$newdata[$k-1]['1_0']=$materialinfo[id];
					}
					$newdata[$k-1]['1']=$array[$k]['2'];
					if(empty($newdata[$k-1]['1']))$newdata[$k-1]['1']="";
					$newdata[$k-1]['2']=$array[$k]['3'];
					$newdata[$k-1]['3']=$array[$k]['4'];
					
					
					
					$newdata[$k-1]['4']=$array[$k]['5'];
					$newdata[$k-1]['5']=$array[$k]['6'];
					$newdata[$k-1]['6']=$supplierinfo["supplier"];
					$newdata[$k-1]['7']=$array[$k]['7'];
					
					
					$info=M('material')->where(array('number'=>$array[$k]['2']))->find();
					if (!empty($info)) {
						$materialinfo[lowprice]=$info[min];
						if(empty($info[price2])){
							$materialinfo[threeprice]=$info[price1];
						}
						if (!empty($info[price2]) && empty($info[price3])) {
							$materialinfo[threeprice]=$info[price1].','.$info[price2];
						}
						if(!empty($info[price3])){
							$materialinfo[threeprice]=$info[price1].','.$info[price2].','.$info[price3];
						}

						
					}else{
						$materialinfo[lowprice]='无';
						$materialinfo[threeprice]='无';
					}
			
			
					$newdata[$k-1]['8']=$materialinfo[lowprice];
					$newdata[$k-1]['9']=$materialinfo[threeprice];
					$newdata[$k-1]['10']=$array[$k]['8'];
				}					
			}
			
			$this->success(json_encode($newdata),"1","1");
		}
		else
		{
			$this->error('上传的文件类型非法!');
		}
	}
	
	
	
	
	
	
	
	
	
	//类别管理新增
    public function add(){
    	$this->display();
    }
    //新增名字监测
    public function ajax_name_check(){
    	$name=$_REQUEST['name'];
    	$id=$_REQUEST['id'];
    	if(empty($_REQUEST[id])){
    		$res=M('classifyfile')->where(array('name'=>$name))->find();
	    	if($res || empty($_REQUEST[name])){
	    		echo 1;
	    	}else{
	    		echo 2;
	    	}
    	}else{
    		$res=M('classify')->where(array('name'=>$name))->find();
	    	if(($res && ($res[id]!=$id)) || empty($_REQUEST[name])){
	    		echo 1;
	    	}else{
	    		echo 2;
	    	}
    	}	
    }
    //执行add
    public function name_add(){
    	$data['name']=$_REQUEST['name'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['c_name']=$_SESSION['loginUserName'];
    	$res=M('classifyfile')->add($data);
    	if($res){
    		$this->success('添加成功.');
    	}else{
    		$this->error('添加失败,请重试!');
    	}
    }
    //编辑工程类
    public function classify_edit(){
    	$id=$_REQUEST['id'];
    	$name=M('classifyfile')->where(array('id'=>$id))->getfield('name');
    	$this->assign('name',$name);
		
    	$this->assign('id',$id);
    	$this->display();
    }
    public function name_edit(){
     	$id=$_REQUEST[id];
    	$data['name']=$_REQUEST['name'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['c_name']=$_SESSION['loginUserName'];
    	$res=M('classifyfile')->where(array('id'=>$id))->save($data);
    	if($res){
    		$this->success('修改成功.');
    	}else{
    		$this->error('修改失败,请重试!');
    	}
    }
    //删除工程类
    public function classify_del(){
    	$id=$_REQUEST['id'];
    	$res=M('classifyfile')->where(array('id'=>$id))->delete();
    	if($res){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }
}
?>