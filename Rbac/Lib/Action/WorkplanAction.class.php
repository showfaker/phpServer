<?php
class WorkplanAction extends CommonAction {
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
			$map['plm'] = array('like',"%".$_REQUEST['plm']."%");
			$search['plm'] = array('like',"%".$_REQUEST['plm']."%");
			$this->assign("plm",$_REQUEST['plm']);
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
		$map['releaser'] = $_SESSION['loginUserName'];
		$model = D("workplan");
		if(!empty($model)){
			$this->_list($model, $map,"timebegin",true,$search);
		}
		$where['status']=1;
		$where['more']=1;
		$group=M("group")->where($where)->order("sort asc")->select();
		$this->assign('groups',$group);
		if($_SESSION[app]){
			$this->display(indexapp);
		}else{
			$this->display(indexoa);
		}
		return;
	}
	
	protected function _list($model, $map, $sortBy = '', $asc = false,$search) {
		//排序字段 默认为主键名
		if(isset($_REQUEST ['_order'])){
			$order = $_REQUEST ['_order'];
		}else{
			$order = !empty($sortBy) ? $sortBy : $model->getPk();
		}
	
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if(isset($_REQUEST ['_sort'])){
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}else{
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
			// foreach($volist as $k => $v){
			// 	$meetingassignment = M("meetingassignment")->where("plan_id=".$v['id'])->order("begintime asc")->select();
			// 	foreach($meetingassignment as $key => $val){
			// 		$volist[$k]['meetingassigment'] .= $val['begintime'];
			// 	}
			// }
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

	function adds() {
		//查找所有部门
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);


		//查找所有项目
		$allprojects = M("project")->order("ctime desc")->select();
        $this->assign('allprojects', $allprojects);
		$this->display(addsoa);
    }


    function edits() {
		$dept = M("dept")->select();
		foreach($dept as $k => $v){
			$dept[$k]['user'] = M("user")->where("department=".$v['id'])->select();
		}
        $this->assign('dept', $dept);

        $model = M("workplan");
        $id = $_REQUEST["id"];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);

		$list = M("workassignment")->where("plan_id=".$_REQUEST["id"])->select();
        $this->assign('list', $list);
        if($_SESSION[skin]!=3){
        	$this->display(editsoa);
        }else{
        	$this->display();
        }
    }

    function info() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
		//查找所有职能计划
		$worklist = M("workassignment")->where("plan_id=".$id)->select();
    	$this->assign('worklist', $worklist);
    	if($_SESSION[skin]!=3){
    		$this->display(infooa);
    	}else{
    		$this->display();
    	}
    }

	function inserts() {
		//先添加会议计划
		$datainfo['title'] = $_REQUEST['title'];
		// $datainfo['plmid'] = $_REQUEST['plmid'];
		// $datainfo['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		// $datainfo['organize'] = $_REQUEST['organize'];
		$datainfo['period'] = $_REQUEST['period'];
		$datainfo['timebegin'] = $_REQUEST['begintime'];
		$datainfo['timeend'] = $_REQUEST['endtime'];
		$datainfo['releaser'] = $_SESSION['loginUserName'];
		$res = M("workplan")->add($datainfo);
		$info['title'] = $_REQUEST['title'];
		// $info['plmid'] = $_REQUEST['plmid'];
		// $info['plm'] = M("Project")->where("id=".$_REQUEST["plmid"])->getField("title");
		// $info['organize'] = $_REQUEST['organize'];
		$info['period'] = $_REQUEST['period'];
		$info['plan_id'] = $res;
		$info['type'] = "1";
		$info['releaser'] = $_SESSION['loginUserName'];
		$info['create_time'] = time();
		foreach($_REQUEST['para1'] as $k => $v){
			$info['content'] = $v;
			$info['current'] = $_REQUEST['para2'][$k];
			$info['timebegin'] = $_REQUEST['timebegin'][$k];
			$info['timeend'] = $_REQUEST['timeend'][$k];
			M("workassignment")->add($info);
			//添加任务通知
			$date=date('Y-m-d H:i');
			$data['content']=$_SESSION['loginUserName']."于".$date."委派给您一项职能计划任务，任务名称：".$_REQUEST['title']."，请及时处理。";
			$data['href'] ="index.php?s=/Workassignment1/index/moduletitle/职能计划/";
			$data['taskid'] =time();
			$data['user']=$_REQUEST['para2'][$k];
			$this->Addschedule($data);
		}
    	if($res){
    		$this->success('职能计划添加成功!');
    	} else {
    		$this->error('职能计划添加失败!');
    	}
    }

    function update() {
		$datainfo['title'] = $_REQUEST['title'];
		$datainfo['period'] = $_REQUEST['period'];
		$datainfo['timebegin'] = $_REQUEST['begintime'];
		$datainfo['timeend'] = $_REQUEST['endtime'];
		//$datainfo['releaser'] = $_SESSION['loginUserName'];
		$datainfo['status'] = "0";
		$res = M("workplan")->where("id=".$_REQUEST['id'])->save($datainfo);

		$info['title'] = $_REQUEST['title'];
		$info['period'] = $_REQUEST['period'];
		$info['plan_id'] = $_REQUEST['id'];
		$info['type'] = "1";
		$info['releaser'] = $_SESSION['loginUserName'];
		foreach($_REQUEST['para1'] as $k => $v){
			$info['content'] = $v;
			$info['current'] = $_REQUEST['para2'][$k];
			$info['timebegin'] = $_REQUEST['timebegin'][$k];
			$info['timeend'] = $_REQUEST['timeend'][$k];
			if ($_REQUEST['ids'][$k] !== "0") {
				M("workassignment")->where("id=".$_REQUEST['ids'][$k])->save($info);
			}else{
				$info['create_time'] = time();
				M("workassignment")->add($info);
			}
			$date=date('Y-m-d H:i');
			$emaildata['content'] =$_SESSION['loginUserName']."于".$date."更新了一项职能计划任务，任务名称：".$_REQUEST['title']."，您是查看人，请关注。";
			$emaildata['status']=1;
			$emaildata['sender']="系统通知";
			$emaildata['create_time']=time();
			$emaildata['title'] =$_SESSION['loginUserName']."于".$date."更新了一项职能计划任务，任务名称：".$_REQUEST['title']."，您是查看人，请关注。";
			$emaildata['receiver']=$_REQUEST['para2'][$k];
			$this->Sendmail($emaildata);
		}
    	$this->success('职能计划编辑成功!');
    }



	
	public function foreverdelete() {
		$id=$_REQUEST['id'];
		$res=M('workplan')->where(array('id'=>$id))->delete();
		if($res){
			//删除所有会议决议
			$res=M('workassignment')->where(array('plan_id'=>$id))->delete();
			$this->success('职能专项计划删除成功!');
		}else{
			$this->error('职能专项计划删除失败!');
		}
	}

	public function delete(){
		$id=$_REQUEST['id'];
		$res=M('workassignment')->where(array('id'=>$id))->delete();
		if($res){
			$this->success('职能专项计划删除成功!');
		}else{
			$this->error('职能专项计划删除失败!');
		}
	}

	public function toexcel(){
		$model=M("workplan");
		if (method_exists($this, '_filter')){
			$this->_filter($map);
		}
		if((!empty($_REQUEST['timebeginassign']))&&(empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
		}else if((empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}else if((!empty($_REQUEST['timebeginassign']))&&(!empty($_REQUEST['timeendassign']))){
			$map['timebegin'] = array('egt',$_REQUEST['timebeginassign']);
			$map['timeend'] = array('elt',$_REQUEST['timeendassign']);
		}

		$volist=$model->where($map)->order('timebegin asc')->select();
		$ids = array();
		foreach($volist as $k => $v){
			$ids[] = $v['id'];
			$count[] = M("workassignment")->where("plan_id=".$v['id'])->count();
		}
		$where['plan_id'] = array("in",$ids);
		$workassignment = M("workassignment")->where($where)->order("plan_id asc")->select();
		$number=count($workassignment);
		for($i=0;$i<$number;$i++){
			$workplan = M("workplan")->where("id=".$workassignment[$i]["plan_id"])->find();
			$data[$i]['title']=$workassignment[$i]['title'];
			$data[$i]['period']=$workassignment[$i]['period'];
			$data[$i]['workplantimebegin']=$workplan['timebegin'];
			$data[$i]['workplantimeend']=$workplan['timeend'];
			$data[$i]['timebegin']=$workassignment[$i]['timebegin'];
			$data[$i]['timeend']=$workassignment[$i]['timeend'];
			$data[$i]['content']=$workassignment[$i]['content'];
			$data[$i]['current']=$workassignment[$i]['current'];
			$data[$i]['ctime']= date("Y-m-d H:i",$workassignment[$i]['create_time']);
		}
		$file="职能专项计划列表";
		$title="职能专项计划列表";
		$subtitle='职能专项计划列表';
		$th_array=array('计划名称','期间','计划期限开始','计划期限结束','事项期限开始','事项期限结束','事项内容',"责任人","发起时间");
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file,$count);
	}

	function createExel($file,$title,$subtitle,$array_th,$data,$excelname="",$count){
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
		if($array_th==null){
			$array_th=array_keys($data[0]);
		}
		foreach($array_th as $key=>$value){
			$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
		}
		$i = 0;
		foreach($count as $k => $v){
			$a = $i+5;
			$b = $i+$v+4;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$a.':A'.$b);
			$objPHPExcel->getActiveSheet()->mergeCells('B'.$a.':B'.$b);
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$a.':C'.$b);
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$a.':D'.$b);
			$i += $v;
		}
		// 水平居中
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 垂直居中
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$baseRow = 5; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
		foreach ( $data as $r => $dataRow ){
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value){		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
				// $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
			}		 
		}
		$filename = $excelname."_".time();
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
		$savePath = '../Public/Uploads/meetplan/'; 
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
			if($data[$k]["A"]){
				$info['title'] = $data[$k]['A'];
				$info['period'] = $data[$k]['B'];
				$info['timebegin'] = $data[$k]['C'];
				$info['timeend'] = $data[$k]['D'];
				$info['releaser'] = $_SESSION['loginUserName'];
				$res = M("workplan")->add($info);

				$addinfo['title'] = $data[$k]['A'];
				$addinfo['period'] = $data[$k]['B'];
				$addinfo['plan_id'] = $res;
				$addinfo['type'] = "1";
				$addinfo['releaser'] = $_SESSION['loginUserName'];
				$addinfo['create_time'] = time();
			}
			$addinfo['timebegin'] = $data[$k]['E'];
			$addinfo['timeend'] = $data[$k]['F'];
			$addinfo['content'] = $data[$k]['G'];
			$addinfo['current'] = $data[$k]['H'];
			M("workassignment")->add($addinfo);
			//添加任务通知
			$date=date('Y-m-d H:i');
			$datainfos['content']=$_SESSION['loginUserName']."于".$date."委派给您一项职能专项任务，任务名称：".$addinfo['title']."，请及时处理。";
			$datainfos['href'] ="index.php?s=/Workassignment1/index/moduletitle/职能计划/";
			$datainfos['taskid'] =time();
			$scheduleuser=explode(',',$data[$k]['H']);
			foreach ($scheduleuser as $key=>$value){
				if($value!=null){
					$datainfos['user']=$value;
					$this->Addschedule($datainfos);
				}
			}
		}

		$this->success('上传成功!');
	}
}
?>