<?php
class Workassignment2Action extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
		$this->assign('name', $_POST['name']);
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if($_REQUEST['plm']){
			$map["plm"]=array('like',"%".$_REQUEST['plm']."%");
			$this->assign('plm', $_REQUEST['plm']);
			$search['plm'] = $_REQUEST['plm'];
		}

		if($_REQUEST['title']){
			$map["title"]=array('like',"%".$_REQUEST['title']."%");
			$this->assign('title', $_REQUEST['title']);
			$search['title'] = $_REQUEST['title'];
		}
		
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$search['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
		}else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
			$search['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
			$search['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$search['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}
		$this->assign("timebeginassign",$_REQUEST['timebeginassign']);
		$this->assign("timeendassign",$_REQUEST['timeendassign']);
		//dump($map);
		$name = "Workassignment";
		$model = D($name);
		//$map['releaser']=$_SESSION['loginUserName'];
		//$search['releaser'] = $_SESSION['loginUserName'];
		$map['current'] = array("exp","like '%".$_SESSION['loginUserName']."%' or `releaser` = '".$_SESSION['loginUserName']."' or `current` like '%".$_SESSION['dept']."%' or `current` = '公司'");
		$search['current'] = array("exp","like '%".$_SESSION['loginUserName']."%' or `releaser` = '".$_SESSION['loginUserName']."' or `current` like '%".$_SESSION['dept']."%' or `current` = '公司'");
		$map['type'] = "2";
		$search['type'] = "2";
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',true,$search);
		}
		if($_SESSION["app"]=="1"){
			$this->display("indexapp");
		}else{
			$this->display("indexoa");
		}
		return;
	}
	
	protected function _list($model, $map, $sortBy = '', $asc = false,$search) {
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
			//$p = new Page($count, $listRows);
			$p = new Page($count, 20);
			$this->assign("totalCount", $p->totalRows);
			$this->assign("numPerPage", $p->listRows);
			$this->assign("currentPage", $p->nowPage);
			//分页查询数据
			$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ($search as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}
			//分页显示
			$page = $p->show();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
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
	 
	function add() {
		//查找所有部门
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);
		//查找所有项目
		$allprojects = M("project")->order("ctime desc")->select();
		$this->assign('allprojects', $allprojects);
		$section = M("dept")->select();
		$this->assign('section', $section);
		$this->display(addoa);
    }

	function insert() {
		$info['plmid'] = $_REQUEST['plmid'];
		$info['title'] = $_REQUEST['title'];
		$info['organize'] = $_REQUEST['organize'];
		$info['period'] = $_REQUEST['period'];
		$info['type'] = "2";
		$info['create_time'] = time();
		$info['timebegin'] = $_REQUEST['timebegin'];
		$info['timeend'] = $_REQUEST['timeend'];
		$info['content'] = $_REQUEST['content'];
		if($_REQUEST['organize'] == "公司"){
			$info['current'] = "";
		}elseif($_REQUEST['organize'] == "部门"){
			$info['current'] = $_REQUEST['section'];
		}else{
			$info['current'] = $_REQUEST['current'];
		}		
		$info['plm'] =M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		$info['releaser'] = $_SESSION['loginUserName'];

    	$list = M("workassignment")->add($info);
    	if($list !== false){
			//添加任务通知
			$date=date('Y-m-d H:i');
			$data['content']=$_SESSION['loginUserName']."于".$date."委派给您一项组织计划任务，任务名称：".$_REQUEST['title']."，请及时处理。";
			$data['href'] ="index.php?s=/Workassignment2/index/moduletitle/组织计划编制/";
			$data['taskid'] =time();
			$scheduleuser=explode(',',$_REQUEST['current']);
			foreach ($scheduleuser as $key=>$value){
    				if($value!=null){
	    				$data['user']=$value;
	    				$this->Addschedule($data);
    				}
    			}
			$this->success('组织计划上传成功!');
    	} else {
    		$this->error('组织计划上传失败!');
    	}
    }
	
    function edit() {
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);
		$allprojects = M("project")->order("ctime desc")->select();
		$this->assign('allprojects', $allprojects);
		$section = M("dept")->select();
		$this->assign('section', $section);
        $name = "Workassignment";
        $model = M($name);
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3){
        	$this->display(editoa);
        }else
        {
        	$this->display();
        }
    }

	public function pursue(){
		$name = "Workassignment";
        $model = M($name);
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        $this->display();
	}
	
	public function dopursue(){
		$info['status'] = $_REQUEST['status'];
		$info['complete'] = $_SESSION['loginUserName'];
		$info['feedback'] = $_REQUEST['feedback'];

		$list = M("workassignment")->where("id=".$_REQUEST['id'])->save($info);
		if (false !== $list) {
			$this->success('组织计划审核成功!');
		} else {
			//错误提示
			$this->error('组织计划审核失败!');
		}
	}

	public function draftfirst(){
		$name = "Workassignment";
        $model = M($name);
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->getById($id);        
        $this->assign('vo', $vo);
        $this->display();
	}
    
    function info() {
    	$name = "Workassignment";
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(infooa);
    	}
    	else
    	{
    		$this->display();
    	}
    }
    
   public function dodraftfirst(){
		$info['view'] = $_REQUEST['view'];
		$info['checker'] = $_SESSION['loginUserName'];
		$info['result'] = $_REQUEST['result'];

		$list = M("workassignment")->where("id=".$_REQUEST['id'])->save($info);
    	if (false !== $list) {
    		$this->success('组织计划审核成功!');
    	} else {
    		//错误提示
    		$this->error('组织计划审核失败!');
    	}
   }
    
    function update() {
    	$info['plmid'] = $_REQUEST['plmid'];
		$info['title'] = $_REQUEST['title'];
		$info['organize'] = $_REQUEST['organize'];
		$info['period'] = $_REQUEST['period'];
		$info['timebegin'] = $_REQUEST['timebegin'];
		$info['timeend'] = $_REQUEST['timeend'];
		$info['content'] = $_REQUEST['content'];
		if($_REQUEST['organize'] == "公司"){
			$info['current'] = "";
		}elseif($_REQUEST['organize'] == "部门"){
			$info['current'] = $_REQUEST['section'];
		}else{
			$info['current'] = $_REQUEST['current'];
		}		
		$info['plm'] =M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
    	$list = M("workassignment")->where("id=".$_REQUEST['id'])->save($info);
    	if (false !== $list) {
			$date=date('Y-m-d H:i');
			$emaildata['content'] =$_SESSION['loginUserName']."于".$date."更新了一项组织计划任务，任务名称：".$_REQUEST['title']."，您是查看人，请关注。";
			$emaildata['status']=1;
			$emaildata['sender']="系统通知";
			$emaildata['create_time']=time();
			$emaildata['title'] =$_SESSION['loginUserName']."于".$date."更新了一项组织计划任务，任务名称：".$_REQUEST['title']."，您是查看人，请关注。";
			$scheduleuser=explode(',',$_REQUEST['current']);
			foreach ($scheduleuser as $key=>$value){
				if($value!=null){
					$emaildata['receiver']=$value;
					$this->Sendmail($emaildata);
				}
			}
    		$this->success('组织计划更新成功!');
    	} else {
    		//错误提示
    		$this->error('组织计划更新失败!');
    	}
    }
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Workassignment";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            //$id = $_REQUEST [$pk];
            if(!empty($_REQUEST [$pk]))
            {
            	$id = $_REQUEST [$pk];
				
				$mapschedule[taskid]=$model->where("id='" . $_REQUEST['id'] . "'")->getField('taskid');
				M("Schedule")->where($mapschedule)->setField("status",0);
            }
            else
            {
            	$id = $_REQUEST ["ids"];
            }
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete()){
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }

	public function toexcel(){
		$model=M("Workassignment");
		if (method_exists($this, '_filter')){
			$this->_filter($map);
		}
		if($_REQUEST['plm']){
			$map['plm'] = array('like',"%".$_REQUEST['plm']."%");
		}
		if($_REQUEST['title']){
			$map["title"]=array('like',"%".$_REQUEST['title']."%");
			$this->assign('title', $_REQUEST['title']);
		}
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
		}else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}
		$map['type'] = "2";
		$volist=$model->where($map)->order('create_time asc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++){
			$data[$i]['plm']=$volist[$i]['plm'];
			$data[$i]['title']=$volist[$i]['title'];
			$data[$i]['organize']=$volist[$i]['organize'];
			$data[$i]['period']=$volist[$i]['period'];
			$data[$i]['timebegin']=$volist[$i]['timebegin'];
			$data[$i]['timeend']=$volist[$i]['timeend'];
			$data[$i]['releaser']=$volist[$i]['releaser'];
			$data[$i]['current']=$volist[$i]['current'];
			$data[$i]['content']=$volist[$i]['content'];
			if($volist[$i]['result'] == "同意"){
				$data[$i]['result'] = "同意";
			}elseif($volist[$i]['result'] == "退回"){
				$data[$i]['result'] = "退回";
			}else{
				$data[$i]['result'] = "待审批";
			}
			$data[$i]['checker']=$volist[$i]['checker'];
			$data[$i]['view']=$volist[$i]['view'];
			$data[$i]['result']=$volist[$i]['result'];
			$data[$i]['ctime']= date("Y-m-d H:i",$volist[$i]['create_time']);
		}
		$file="组织计划列表";
		$title="组织计划列表";
		$subtitle='组织计划列表';
		$excelname="组织计划列表";
		$th_array=array('所属项目','计划名称','组织','期间','计划开始时间','计划结束时间','添加人','当前责任','计划内容',"状态",'审批人','审批意见'," 添加时间");
		$this->createExel($file,$title,$subtitle,$th_array,$data,$excelname);
	}

	function createExel($file,$title,$subtitle,$array_th,$data,$excelname){
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template_second.xls" );
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
			$objActSheet->getCellByColumnAndRow($key,3)->setValue($value);		
		}

		$baseRow = 4; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
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
		$curdate=date('Ymd',time());
		$filename = $file.$curdate;
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );
	}

	public function getexcel(){
		$ext=end(explode('.', $_FILES['file']['name']));
		$exts=array('xls','xlsx');
		$check=in_array($ext,$exts);
		if(empty($_FILES['file']) || (!$check)){
			$this->error('没有找到文件或请上传EXCEL文件(xls,xlsx).');
		}
		$filename=$_FILES['file']['name'];
		$savePath = '../Public/Uploads/workassignment/'; 
		if($filename!=null){
			$ext = strtolower(end(explode(".",basename($filename))));
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;	
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$file = $newname;
			$filerealname = $filename;
		}
		$filePath=$upload_file;
		Vendor('Excel.PHPExcel');
		$PHPExcel = new PHPExcel();
		//ok
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)){ 
				echo 'no Excel'; 
				return ; 
			} 
		} 
		$PHPExcel = $PHPReader->load($filePath); 
		/**读取excel文件中的第一个工作表*/ 
		$currentSheet = $PHPExcel->getSheet(0); 
		/**取得最大的列号*/ 
		$allColumn = $currentSheet->getHighestColumn();
		/**取得一共有多少行*/ 
		$allRow = $currentSheet->getHighestRow(); 
		
		/**从第二行开始输出，因为excel表中第一行为列名*/ 
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){ 
			/**从第A列开始输出*/ 
			for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
				$val = (string)$currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/ 
				
				$data[$currentRow-2][$currentColumn]=$val;
			}
		}
		$count=count($data);
		for($k=0;$k<$count;$k++){
			$mapforProject["plm"] = $data[$k]['A'];
			$mapforProject["plmid"] = M("project")->where("title='".$data[$k]['A']."'")->getfield("id");
			$mapforProject["title"] = $data[$k]['B'];
			$mapforProject["organize"] = $data[$k]['C'];
			$mapforProject["period"] = $data[$k]['D'];
			$mapforProject["timebegin"] = $data[$k]['E'];
			$mapforProject["timeend"] = $data[$k]['F'];
			$mapforProject["releaser"] = $data[$k]['G'];
			$mapforProject["current"] = $data[$k]['H'];
			$mapforProject["content"] = $data[$k]['I'];
			$mapforProject["type"] = "2";
			$mapforProject["create_time"] = time();
			$x=M("workassignment")->add($mapforProject);
			//添加任务通知
			$date=date('Y-m-d H:i');
			$datainfo['content']=$_SESSION['loginUserName']."于".$date."委派给您一项组织计划任务，任务名称：".$data[$k]['B']."，请及时处理。";
			$datainfo['href'] ="index.php?s=/Workassignment2/index/moduletitle/组织计划编制/";
			$datainfo['taskid'] =time();
			$scheduleuser=explode(',',$data[$k]['H']);
			foreach ($scheduleuser as $key=>$value){
				if($value!=null){
					$datainfo['user']=$value;
					$this->Addschedule($datainfo);
				}
			}
		}
		$this->success('上传成功!');
	}
}
?>