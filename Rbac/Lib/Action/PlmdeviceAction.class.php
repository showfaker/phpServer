<?php
class PlmdeviceAction extends CommonAction {			
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
		if(($_REQUEST['tab']==1)||(empty($_REQUEST['tab']))){
			
			//$mapPlmdevice['plmid']=60209;
			//$workers=M('Plmdevice')->where($mapPlmdevice)->order("id asc")->select();
			//$this->assign('workers',$workers);
		}
		if(($_REQUEST['tab']==10)){
			if(($_SESSION['dept']=='专项部') || ($_SESSION['dept']=='工程配合部')  || ($_SESSION['account']=='admin') ||($_SESSION['account']=='taojianhua')||($_SESSION['account']=="chongfazhan")){
				$map_classify['status']=0;
				$classify=M('classify')->where($map_classify)->select();
				$map_supplier['status']=1;
				$supplier=M('supplier')->where($map_supplier)->select();
				$this->assign('classify',$classify);
				$this->assign('supplier',$supplier);
			}
			
			$citiesprojects=M("Project")->group("city")->field("city")->select();
			foreach($citiesprojects as $key => $val)
			{
				$mapforProjectxxx[city]=$val[city];
				$citiesprojects[$key]["projects"]=M("Project")->where($mapforProjectxxx)->field("id,title")->select();
			}
			$this->assign('citiesprojects',$citiesprojects);
		}
		//类别管理表
		if($_REQUEST['tab']==5){
			$model=M('classify');
			if(!empty($_REQUEST[name])){
				$map[name]=$_REQUEST[name];
				$this->assign('name',$_REQUEST[name]);
			}
			$map['status']=0;
			$info=$model->where(array('status'=>0))->select();
			if(!empty($model)){
				$this->_list($model,$map,'topname',false,5);
			}
            $this->assign('info',$info);

		}

        //记录表
        if($_REQUEST['tab']==2){
        	$model=M('plmorder2');
            $charge=1;
           if(($_SESSION[account]!='admin')&&($_SESSION[account]!='zhourong')){
				
				if($_SESSION[account]=='taojianhua'){
					$map['department']="专项部";
				}elseif($_SESSION[account]=='chongfazhan'){
					$map['department']="工程配合部";
				}else{
					//$map[user]=$_SESSION[loginUserName];
					if($_SESSION[account]!="hukeke")
					{
						$map['user']=$_SESSION[loginUserName];
					}
					else
					{
						$map['user']=array("in","胡可可,黄滇,李雯,陈心巧");
					}
					$charge=2;
				}
				
           }
           $this->assign('charge',$charge);
           if(!empty($_REQUEST['city'])){
           	$map[city]=$_REQUEST[city];
           	$this->assign('city', $_REQUEST['city']);
           }
		   if(!empty($_REQUEST['plm'])){
           	$map[plm]=array("like","%".$_REQUEST[plm]."%");
           	$this->assign('plm', $_REQUEST['plm']);
           }
           if((!empty($_REQUEST['start']))&&(empty($_REQUEST['end']))){
           	$str=strtotime($_REQUEST['start']);
           	$map[ctime]=array('egt',$str);
           }elseif ((empty($_REQUEST['start']))&&(!empty($_REQUEST['end']))) {
           	$str=strtotime($_REQUEST['end'])+3600*24;
           	$map[ctime]=array('elt',$str);
           }elseif ((!empty($_REQUEST['start']))&&(!empty($_REQUEST['end']))) {
           	$str1=strtotime($_REQUEST['start']);
           	$str2=strtotime($_REQUEST['end'])+3600*24;
           	$map[ctime]=array(array('egt',$str1),array('elt',$str2),'and');
           }
           $this->assign('start', $_REQUEST['start']);
		   $this->assign('end', $_REQUEST['end']);
		   
           if(!empty($_REQUEST['user'])){
            $map[user]=array('like','%'.$_REQUEST[user].'%');
            $this->assign('user', $_REQUEST['user']);
           }
		   $arr=array();
		   $arr[]=$_REQUEST[city];
		   $arr[]=$_REQUEST['start'];
		   $arr[]=$_REQUEST['end'];
		   $arr[]=$_REQUEST['zt'];
           $arr[]=$_REQUEST['user'];
           if(!empty($model)){
           	$this->_list($model,$map,'ctime',false,2,$arr);
           }

        }
    

        //日志
        if($_REQUEST[tab]==4){
        	$model=M('Plmattendancedevice');
        	if(!empty($_REQUEST["date"])){
        		$map["date"]=$_REQUEST["date"];
        		$this->assign('date',$_REQUEST["date"]);
        	}
			if(!empty($_REQUEST["device"])){
        		$map["device"]=$_REQUEST["device"];
        		$this->assign('device',$_REQUEST["device"]);
        	}
			if(!empty($_REQUEST["number"])){
        		$map["number"]=$_REQUEST["number"];
        		$this->assign('number',$_REQUEST["number"]);
        	}
        	if(!empty($_REQUEST[plmid])){
        		$map[plmid]=$_REQUEST[plmid];
        		$this->assign('plmid',$_REQUEST[plmid]);
				
				$map1['plmid']=$_REQUEST[plmid];
				$groups=M("Plmgroup")->where($map1)->select();
				$this->assign('groups',$groups);
        	}
			if(!empty($_REQUEST[groupid])){
        		$map[groupid]=$_REQUEST[groupid];
        		$this->assign('groupid',$_REQUEST[groupid]);
				$groupname=M("Plmgroup")->where("id=".$_REQUEST[groupid])->getField("group");
				$this->assign('groupname',$groupname);
        	}
			$map['user'] = array("in",$this->find5levelusers($_SESSION[position]));
        	if($model){
        		$this->_list($model,$map,'date',false,4);
        	}
        }
        
		$this->assign('project',$plm_array);
		$projects=M("Project")->where("step6=1")->select();
		$this->assign("projects",$projects);
		
		
		$plm_array=M('project')->select();
		foreach($plm_array as $key => $val)
		{
			$plm_array[$key][plm]=$val[title];
		}
        $this->assign('plm_array',$plm_array);
		$this->assign('project',$plm_array);
		$suppliers=M('plmorder2')->where($map6)->group('supplier')->select();
		$this->assign("suppliers",$suppliers);
		
		$this->display();
	}
    public function ajaxclassify(){
        $classify=$_REQUEST['classify'];
        $map[status]=1;
        $map['classify']=array('like',"%".$_REQUEST['classify']."%");
        $supplier=M('supplier')->where($map)->select();
        echo json_encode($supplier);
    }
	
	public function ajaxgetWorkersByGroupId(){
        $mapforWorkers['groupid']=$_REQUEST["groupid"];
		$workers=M("Plmdevice")->where($mapforWorkers)->order("id asc")->select();
		foreach($workers as $key => $val)
		{
			$mapforDevice[id]=$val["deviceid"];
			$workers[$key][source]=M("Device")->where($mapforDevice)->getField("source");;
		}
		echo json_encode($workers);
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
            $p = new Page($count,$listRows);
            //分页查询数据

            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            foreach ($voList as $key => $value) {
                $voList[$key]['price']=$value['price']+$value['baozheng'];
            }
            if($tab==4){
            	$total=0;
            	foreach ($voList as $key => $value) {
            		$total+=$value[price];
            	}
            	$this->assign('total',$total);
            }
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            if($tab==2){
            	$data[city]=$arr[0];
            	$data['start']=$arr[1];
            	$data['end']=$arr[2];
            	$data[zt]=$arr[3];
                $data[user]=$arr[4];
            	foreach ($data as $key => $val) {
		            if (!is_array($val)) {
		                $p->parameter .= "$key/".$val."/";
		            }
		        }
            }elseif($tab==7){
                $data[city]=$arr[0];
                $data['start']=$arr[1];
                $data['end']=$arr[2];
                foreach ($data as $key => $val) {
                    if (!is_array($val)) {
                        $p->parameter .= "$key/".$val."/";
                    }
                }
            }elseif($tab==6){
                $data[city]=$arr[0];
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
					else
					{
						$p->parameter .= "$key=" . $_REQUEST[$key] . "&";
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
    //根据城市获取项目
    public function ajaxcity()
	{
		// $map[design_status]=array('neq','完成验收');
		$map['city']=$_POST[city];
		$projects=M("Project")->where($map)->field("id,title")->select();
	    echo json_encode($projects);
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
    		$res=M('classify')->where(array('name'=>$name))->find();
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
		$data['topname']=$_REQUEST['topname'];
    	$data['name']=$_REQUEST['name'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['c_name']=$_SESSION['loginUserName'];
    	$res=M('classify')->add($data);
    	if($res){
    		$this->success('添加成功.');
    	}else{
    		$this->error('添加失败,请重试!');
    	}
    }
    //编辑工程类
    public function classify_edit(){
    	$id=$_REQUEST['id'];
    	$name=M('classify')->where(array('id'=>$id))->getfield('name');
    	$this->assign('name',$name);
		$topname=M('classify')->where(array('id'=>$id))->getfield('topname');
		$this->assign('topname',$topname);
		
		
    	$this->assign('id',$id);
    	$this->display();
    }
     public function name_edit(){
     	$id=$_REQUEST[id];
		$data['topname']=$_REQUEST['topname'];
    	$data['name']=$_REQUEST['name'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['c_name']=$_SESSION['loginUserName'];
    	$res=M('classify')->where(array('id'=>$id))->save($data);
    	if($res){
    		$this->success('修改成功.');
    	}else{
    		$this->error('修改失败,请重试!');
    	}
    }
    //删除工程类
    public function classify_del(){
    	$id=$_REQUEST['id'];
    	$res=M('classify')->where(array('id'=>$id))->delete();
    	if($res){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }
    //确认提交订单
    public function insert(){
    	// dump($_REQUEST);die;
    	
    	$data['plmid']=$_REQUEST['plmid'];
    	$plm=M('project')->where(array('id'=>$_REQUEST['plmid']))->getfield('title');
		$data['plmid']=$_REQUEST['plmid'];
    	$data['plm']=$plm;
    	$data['ctime']=time();
    	$data[user]=$_SESSION['loginUserName'];
    	$data['date']=$_REQUEST["date"];
        $data[nexttime]=$nexttime;
        $date=date('Y-m-d');
	   
	    $data['step']=$_REQUEST['step'];
	   
		$mapPlmdevice['groupid']=$_REQUEST["groupid"];
	   	$mapPlmdevice['plmid']=$_REQUEST["plmid"];
		$workers=M('Plmdevice')->where($mapPlmdevice)->select();
		
		$mapforPlmattendancedevice["groupid"]=$_REQUEST['groupid'];
		$mapforPlmattendancedevice["plmid"]=$_REQUEST['plmid'];
		$mapforPlmattendancedevice["date"]=$_REQUEST['date'];
		M('Plmattendancedevice')->where($mapforPlmattendancedevice)->delete();
		
        foreach($workers as $key => $val)
		{
			$data[device]=$val["device"];
			$data[deviceid]=$val["deviceid"];
			$data[number]=$val["number"];
			$data[plate]=$val["plate"];
			$data[model]=$val["model"];
			$data[money]=$_REQUEST["money_".$val[deviceid]];
			$data[status]=$_REQUEST["status_".$val[deviceid]];
			
			$data[group]=$_REQUEST["group_".$val[deviceid]];
			$data[groupid]=$_REQUEST["groupid_".$val[deviceid]];
			$data['step']=$_REQUEST['step'];
			
			$mapforDevice[id]=$val["deviceid"];
			$data['source']=M('Device')->where($mapforDevice)->getField("source");

			M('Plmattendancedevice')->add($data);
        }
        $this->success('提交出勤表成功.');
    }
	
	
	
	//确认提交订单
    public function insertpiliang(){
    	//dump($_REQUEST);die;
		$plmids=$_REQUEST[plmid];
		
		foreach($plmids as $key => $val)
		{
			
				$_REQUEST['plmid']=$val;
				$_REQUEST['city']=M('project')->where(array('id'=>$_REQUEST['plmid']))->getfield('city');
				
				$data['city']=$_REQUEST['city'];
				$data['plmid']=$_REQUEST['plmid'];
				$plm=M('project')->where(array('id'=>$_REQUEST['plmid']))->getfield('title');
				$data['plm']=$plm;
				$data['ctime']=time();
				$data[status]=0;
				$data[user]=$_SESSION['loginUserName'];
				if($_SESSION['account']=="taojianhua"){
				   $data[department]='专项部'; 
				}elseif($_SESSION['account']=='chongfazhan'){
					$data[department]='工程配合部';
				}else{
				   $data[department]=$_SESSION['dept'];
				}
				$data[type]=0;
				$data['price']=$_REQUEST[total];
				$data['remark']=$_REQUEST[remark];
				$data['because']=$_REQUEST[because];
				$data[submit]=0;
				$data[cw_status]=0;
				$data[finish]=0;
				$data[baozheng]=$_REQUEST['baozheng'];
				$data[supplier]=$_REQUEST[supplier];
				$data[name]=$_REQUEST[classify];
				$data[count]=count($_REQUEST['price']);
				$nexttime='9999-12-31';
				for ($i=0; $i <count($_REQUEST['price']) ; $i++) { 
					if((!empty($_REQUEST[time][$i]))&&($nexttime>$_REQUEST[time][$i])){
						$nexttime=$_REQUEST[time][$i];
					}
				}
				if($nexttime=='9999-12-31'){
					$nexttime='未设置';
				}
				$data[nexttime]=$nexttime;
				$date=date('Y-m-d');
				$data['record']=$date.'--'.$_SESSION['loginUserName'].'申请了订单;';
				$orderid=M('plmorder2')->add($data);
				for ($i=0; $i <count($_REQUEST['price']) ; $i++) { 
					if(floatval($_REQUEST[price][$i])!=0) {
						$data2[orderid]=$orderid;
						$data2[supplier]=$_REQUEST[supplier];
						$data2[status]=0;
						$data2[type]=0;
						$data2[plm]=$plm;
						$data2[city]=$_REQUEST['city'];
						$data2[name]=$_REQUEST[classify];
						$data2[count]=$i+1;
						$data2[user]=$_SESSION['loginUserName'];
						if($_SESSION['account']=="taojianhua"){
							$data2[department]='专项部'; 
						}elseif($_SESSION['account']=='chongfazhan'){
							$data2[department]='工程配合部';
						}else{
							$data2[department]=$_SESSION['dept'];
						}
						$data2[pay]=$_REQUEST[price][$i];
						$data2[paytime]=$_REQUEST[time][$i];
						$data2[remark]=$_REQUEST[remarks][$i];
						$data2['ctime']=time();
						$data2['approve']=0;
						$res2=M('plmorder2paytime')->add($data2);
					}
						
				}
				
		}
        if($orderid){
            $this->success('提交订单成功.');
        }else{
            $this->error('抱歉,发生错误,请重试.');
        }
    }
	
	
    //订单删除
    public function order_del(){
    	$id=$_REQUEST[id];
    	$res1=M('plmorder2')->delete($id);
    	$res2=M('plmorder2paytime')->where(array('orderid'=>$id))->delete();
    	if($res1){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }

    //加载订单编辑
    public function edit(){
    	$id=$_REQUEST[id];
    	$order_info=M('plmorder2')->where(array('id'=>$id))->find();
    	$order_paytime=M('plmorder2paytime')->where(array('orderid'=>$id))->select();

    	$this->assign('info',$order_info);
    	$this->assign('order_paytime',$order_paytime);
    	$this->display();
    }
    //修改
    public function order_update(){
    	$id=$_REQUEST[id];
        $info=M('plmorder2')->find($id);
    	$data[ctime]=time();
    	$data[price]=$_REQUEST[total];
    	$data[remark]=$_REQUEST[remark];
    	$data[because]=$_REQUEST[because];
    	$data[baozheng]=$_REQUEST['baozheng'];
    	$data[count]=count($_REQUEST['price']);
        $date=date('Y-m-d');
        $data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'修改了订单;';
    	$res1=M('plmorder2')->where(array('id'=>$id))->save($data);
    	$res=M('plmorder2paytime')->where(array('orderid'=>$id))->delete();
    	for ($i=0; $i <count($_REQUEST['price']) ; $i++) { 
            $data2[orderid]=$id;
            $data2[supplier]=$_REQUEST[supplier];
            $data2[status]=0;
            $data2[type]=0;
            $data2[plm]=$_REQUEST[plm];
            $data2[city]=$_REQUEST['city'];
            $data2[name]=$_REQUEST[name];
            $data2[count]=$i+1;
            $data2[user]=$_SESSION['loginUserName'];
            $data2[pay]=$_REQUEST[price][$i];
            $data2[paytime]=$_REQUEST[time][$i];
            $data2[remark]=$_REQUEST[remarks][$i];
            $data2['ctime']=time();
            $data2['approve']=0;
            $res2=M('plmorder2paytime')->add($data2);
        }
        if($res1 && $res2){
            $this->redirect('index','tab=2');
        }else{
            $this->error('抱歉,发生错误,请重试.');
        }
    }
    //上传
    public function upload(){
    	$this->assign('id',$_REQUEST['id']);
    	$this->display();
    }
    //执行上传
    public function file_upload(){
    	$id=$_REQUEST['id'];
		foreach ($_FILES['file'][name] as $key => $value) {
			if(!empty($_FILES['file']['name'][$key]))
			{
				$map[id]=$id;			
				$orderinfo=M("plmorder2")->where($map)->field("id,newname,filename")->find();
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
                    M("plmorder2")->where("id='".$_POST[id]."'")->save($data);
                }
    								
			}
		}
		$this->success('上传成功.');
	}
	//查看详情
	public function check(){
		$id=$_REQUEST[id];
		$order=M('plmorder2')->where(array('id'=>$id))->find();
		$order_paytime=M('plmorder2paytime')->where(array('orderid'=>$id))->select();
        $order['record']=explode(';',$order['record']);
		$this->assign('info',$order);
		$this->assign('order_paytime',$order_paytime);
		$name=$order['newname'];
        $pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		if(!empty($name)){
			$name=explode(',',$name);
            $newname=array();
            foreach ($name as $key => $value) {
                $newname[$key][name]=$value;
                $ext=end(explode('.',$value));
                if(in_array($ext,$pic)){
                    $newname[$key][is_pic]=1;
                }else{
                    $newname[$key][is_pic]=0;
                }
            }
            // dump($newname);die;
			$this->assign('newname',$newname);
		}
		$this->display();
	}
	//提交审核
	public function ajaxsubmit(){
		$id=htmlspecialchars($_REQUEST[id]);
        $info=M('plmorder2')->where(array('id'=>$id))->find();
        $date=date('Y-m-d');
        $data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'提交了订单;';
		$data[type]=0;
		$data[status]=0;
		$data[submit]=1;
		$res=M('plmorder2')->where(array('id'=>$id))->save($data);
		echo $id;
	}
	//经理审核界面
	public function approve(){
		$id=$_REQUEST[id];
		$order=M('plmorder2')->where(array('id'=>$id))->find();
        $order['total']=$order['price']+$order['baozheng'];
		$order_paytime=M('plmorder2paytime')->where(array('orderid'=>$id))->select();
		$this->assign('info',$order);
		$this->assign('order_paytime',$order_paytime);
		$newname=$order['newname'];
		if(!empty($newname)){
			$newname=explode(',',$newname);
			$this->assign('newname',$newname);
		}
		
		$this->assign('city',$_REQUEST[city]);
		$this->assign('plm',$order[plm]);
		$this->assign('supplier',$_REQUEST[supplier]);
		
		$this->display();
	}
	//审核结果
	public function approve_submit(){
		$id=$_REQUEST[id];
		$model=M('plmorder2');
		$info=M('plmorder2')->find($id);
        $date=date('Y-m-d');
		if($info[type]==0){
			if($_REQUEST[result]==1){
				$data['approve1']=$_SESSION[loginUserName];
				$data['approve1_time']=time();
				$data[type]=1;//一级审核
                $data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核通过,审核意见:'.$_REQUEST['sug'].'.;';
				$res1=$model->where(array('id'=>$id))->save($data);
			}else{
				$data[type]=0;
				$data[status]=-1;
				$data[submit]=0;
                $data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核不通过,审核意见:'.$_REQUEST['sug'].'.;';
				$res1=$model->where(array('id'=>$id))->save($data);
			}
		}else{
			if($_REQUEST[result]==1){
				$data['approve2']=$_SESSION[loginUserName];
				$data['approve2_time']=time();
				$data[status]=1;
				$data[type]=2;//二级审核
                $data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核通过,审核意见:'.$_REQUEST['sug'].'.;';
				$res1=$model->where(array('id'=>$id))->save($data);
				$res2=M('plmorder2paytime')->where(array('orderid'=>$id))->save(array('approve'=>1));
			}else{
				// $data[type]=0;
				$data[status]=-1;
				$data[submit]=0;
                $data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核不通过,审核意见:'.$_REQUEST['sug'].'.;';
				$res1=$model->where(array('id'=>$id))->save($data);
			}

		}
		$this->redirect('index',array('tab'=>6,'city'=>$_REQUEST[city],'plm'=>$_REQUEST[plm],'supplier'=>$_REQUEST[supplier]));

	}
	
	
	public function approvesubmit_piliang(){
		
		$ids=$_REQUEST[arr];
		$idsarray=explode(",",$ids);
		foreach($idsarray as $key => $val)
		{
			if($val)
			{
				$_REQUEST[id]=$val;
				$id=$_REQUEST[id];
				$model=M('plmorder2');
				$info=M('plmorder2')->find($id);
				$date=date('Y-m-d');
				if($info[type]==0){
					if(1){//$_REQUEST[result]==1
						$data['approve1']=$_SESSION[loginUserName];
						$data['approve1_time']=time();
						$data[type]=1;//一级审核
						$data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核通过,审核意见:'.$_REQUEST['sug'].'.;';
						$res1=$model->where(array('id'=>$id))->save($data);
					}else{
						$data[type]=0;
						$data[status]=-1;
						$data[submit]=0;
						$data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核不通过,审核意见:'.$_REQUEST['sug'].'.;';
						$res1=$model->where(array('id'=>$id))->save($data);
					}
				}else{
					if(1){//$_REQUEST[result]==1
						$data['approve2']=$_SESSION[loginUserName];
						$data['approve2_time']=time();
						$data[status]=1;
						$data[type]=2;//二级审核
						$data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核通过,审核意见:'.$_REQUEST['sug'].'.;';
						$res1=$model->where(array('id'=>$id))->save($data);
						$res2=M('plmorder2paytime')->where(array('orderid'=>$id))->save(array('approve'=>1));
					}else{
						// $data[type]=0;
						$data[status]=-1;
						$data[submit]=0;
						$data['record']=$info['record'].$date.'--'.$_SESSION['loginUserName'].'审核不通过,审核意见:'.$_REQUEST['sug'].'.;';
						$res1=$model->where(array('id'=>$id))->save($data);
					}

				}
		
		
			}
		}
		//$this->redirect('index',array('tab'=>6,'city'=>$_REQUEST[city],'plm'=>$_REQUEST[plm],'supplier'=>$_REQUEST[supplier]));
		if(empty($ids)){
			echo 2;//为空  未获取数据
		}else{
			echo 1;
		}
	}
	
	
	public function delFile(){
        $str=$_REQUEST[arr];
        $id=$_REQUEST[id];
        $arr=explode(';',$str);
        $info=M('plmorder2')->find($id);
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
        $res=M('plmorder2')->save($data);
        if($res){
            echo 1;
        }else{
            echo 2;
        }
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

    //付款列表详情
    public function info(){
        $id=$_REQUEST[id];
        $order=M('plmorder2')->where(array('id'=>$id))->find();
        $histroy=$order[handle];
        $array=explode(';',$histroy);
        $this->assign('array',$array);
        $map1[orderid]=$id;
        $map1[paytime]=array('neq','');
        $order_paytime1=M('plmorder2paytime')->where($map1)->select();
        $total_time=0;
        foreach ($order_paytime1 as $key => $value) {
            $total_time+=$value[pay];
        }

        $map2[orderid]=$id;
        $map2[paytime]=array('neq','');
        $map2[status]=1;
        $order_paytime2=M('plmorder2paytime')->where($map2)->select();
        $total_time2=0;
        foreach ($order_paytime2 as $key => $value) {
            $total_time2+=$value[pay];
        }
        $Total=$this->cny($order['price']);
        $supplier=M("supplier")->where("supplier='".$order[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
        $this->assign('supplier',$supplier);
        $this->assign('total_time',$total_time);//确定付款
        $this->assign('total_time2',$total_time2);//已付款
        $this->assign('Total',$Total);
        $this->assign('info',$order);
        $newname=$order['newname'];
        if(!empty($newname)){
            $name=explode(",",$newname);
            $gc_newname=array();
            $pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
            foreach ($name as $key => $value) {
                $gc_newname[$key]['name']=$value;
                $ext=end(explode('.',$value));
                if(in_array($ext,$pic)){
                    $gc_newname[$key][is_pic]=1;
                }else{
                    $gc_newname[$key][is_pic]=0;
                }

            }

            $this->assign('newname',$gc_newname);
        }
        if(($_SESSION['account']=='admin') || ($_SESSION[loginUserName]==$order[user]) || $_SESSION['dept']=='财务部'){
            $gclimit=1;
        }
        $this->assign('gclimit',$gclimit);
        $this->display();
    }
    
    
	
	
	public function toexcel()
	{
		$model=M('Plmattendancedevice');
		if(!empty($_REQUEST["date"])){
			$map["date"]=$_REQUEST["date"];
			$this->assign('date',$_REQUEST["date"]);
		}
		if(!empty($_REQUEST["device"])){
			$map["device"]=$_REQUEST["device"];
			$this->assign('device',$_REQUEST["device"]);
		}
		if(!empty($_REQUEST["number"])){
			$map["number"]=$_REQUEST["number"];
			$this->assign('number',$_REQUEST["number"]);
		}
		if(!empty($_REQUEST[plmid])){
			$map[plmid]=$_REQUEST[plmid];
			$this->assign('plmid',$_REQUEST[plmid]);
			
			$map1['plmid']=$_REQUEST[plmid];
			$groups=M("Plmgroup")->where($map1)->select();
			$this->assign('groups',$groups);
		}
		if(!empty($_REQUEST[groupid])){
			$map[groupid]=$_REQUEST[groupid];
			$this->assign('groupid',$_REQUEST[groupid]);
			$groupname=M("Plmgroup")->where("id=".$_REQUEST[groupid])->getField("group");
			$this->assign('groupname',$groupname);
		}
		if($model){
			$this->_list($model,$map,'date',false,4);
		}
		
		$volist=$model->where($map)->order('date desc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{

			$data[$i]['date']=$volist[$i]['date'];
			$data[$i]['plm']=$volist[$i]['plm'];
			$data[$i]['step']=$volist[$i]['step'];
			
			$data[$i]['group']=$volist[$i]['group'];
			$data[$i]['device']=$volist[$i]['device'];
			$data[$i]['number']=$volist[$i]['number'];
			$data[$i]['model']=$volist[$i]['model'];
			$data[$i]['plate']=$volist[$i]['plate'];
			$data[$i]['status']=$volist[$i]['status'];
			$data[$i]['money']=$volist[$i]['money'];
		}
		
		$file="设备每日考勤表";
		$title="设备每日考勤表";
		$subtitle='设备每日考勤表';
		
		$th_array=array('出勤日期','项目名称','项目阶段','分组','设备名称','设备编号','设备型号','车牌','状态','日租金');
		
		
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