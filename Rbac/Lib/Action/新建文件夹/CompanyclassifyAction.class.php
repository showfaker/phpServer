<?php
class CompanyclassifyAction extends CommonAction {
	
	public function index() { 
		if(empty($_REQUEST['tab']))$_REQUEST['tab']=5;
        if(empty($_REQUEST['tab']) || $_REQUEST['tab']==1){
        	//添加供应商
			$department=$_SESSION['dept'];
			if($department=='材料部' || $department=='市场部' ){
				$cl=M('Companyclassify')->select();
			}elseif ($department=="专项部" || $department=='工程配合部' || $_SESSION['account']=='taojianhua' ||$_SESSION['account']=="chongfazhan") {
				$cl=M('classify')->select();
			}
			
			if($_SESSION[account]=='admin'){
				$cl=M('classify')->select();
			}
			
			// dump($classify);die;
			$this->assign('cl',$cl);
			$_SESSION['tab']=$_REQUEST['tab'];
			$this->assign('tab',$_REQUEST['tab']);
		}
        if(!empty($_REQUEST['tab']))
		{			
			$_SESSION[tab]=$_REQUEST['tab'];			
			$this->assign('tab',$_REQUEST['tab']);
			
			//供应商一览
            if($_REQUEST['tab']==2){
				$model = M("supplier");
				// dump($_SESSION);die;
				$map[status]=array("in","-1,0,1,3");
				if($_SESSION['account']!='admin'){
					$name=M('role')->where(array('id'=>$_SESSION['position']))->getfield('name');
					$department=M('dept')->where(array('id'=>$_SESSION['department']))->getfield('name');
					if($name=='材料经理'){
						$map['department']='材料部';
					}elseif($name=='市场部总监'){
						$map['department']='市场部';
					}elseif($_SESSION['account']=='chongfazhan'){
						$map['department']='工程配合部';
					}elseif($_SESSION['account']=='taojianhua'){
						$map['department']='专项部';
					}else{
						if($_SESSION[account]!="hukeke")
						{
							$map[creater]=$_SESSION['account'];
						}
						else
						{
							$map[creater]=array("in","hukeke,huangdian,liwen,chenxinqiao");
						}
					}
				}
				if(!empty($_REQUEST['city']))
				{
					$map[city]=array("like","%".$_REQUEST['city']."%");
					$this->assign('city',$_REQUEST['city']);	
				}
				if(!empty($_REQUEST['supplier1']))
				{
					$map[supplier]=array("like","%".$_REQUEST['supplier1']."%");
					$this->assign('supplier1',$_REQUEST['supplier1']);	
				}
				if(!empty($_REQUEST[brand])){
					$map[classify]=array("like","%".$_REQUEST['brand']."%");
					$this->assign('brands',$_REQUEST['brand']);
				}
				$type=M('supplier')->group('classify')->field('classify')->select();
				$this->assign('type',$type);
		
			}
			//待审核供应商
			if($_REQUEST['tab']==6){				
				$name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
				$account=$_SESSION['account'];
				$flag_supplier=0;
				if($name=="材料经理"){
					$map['department']='材料部';
					$flag_supplier=1;
				}
				if($name=="市场部总监"){
					$map['department']='市场部';
					$flag_supplier=1;
				}
				if($account=="taojianhua"){
					$map['department']='专项部';
					$flag_supplier=1;
				}
				if($account=="chongfazhan"){
					$map['department']='工程配合部';
					$flag_supplier=1;
				}
				if($_SESSION[account]=='admin' || $flag_supplier==1){
					$model = M("supplier");
					$map[status]=0;//新建待审核				
					if(!empty($_REQUEST['supplier1']))
					{
						$map[supplier]=$_REQUEST['supplier1'];
						$this->assign('supplier1',$_REQUEST['supplier1']);	
					}
					if(!empty($_REQUEST['city']))
					{
						$map[city]=array("like","%".$_REQUEST['city']."%");
						$this->assign('city',$_REQUEST['city']);	
					}
					if(!empty($_REQUEST[brands])){
						$supplierid=M("materials")->where("brand='".$_REQUEST[brands]."'")->field("supplierid")->select();
						foreach($supplierid as $su){
							$suid[]=$su[supplierid];
						}
						$map[id]=array("in",$suid);
						
						$this->assign('brands',$_REQUEST['brands']);
					}
				}
                
			}
			//材料bom表
            if($_REQUEST['tab']==3){
				$model=M('materials');
				$malist=$model->order("id asc")->group("number")->field("id,number")->select();
				$this->assign("malist",$malist);
				$name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
				if($_SESSION[account]=='admin' || $name=="材料经理"){
					$limit=1;
					$this->assign("limit",$limit);
				}
				if(!empty($_REQUEST['brands']))
				{
					$map[brand]=$_REQUEST['brands'];
					$this->assign('brands',$_REQUEST['brands']);	
				}
				if(!empty($_REQUEST['number']))
				{
					$map[number]=$_REQUEST['number'];
					$this->assign('number',$_REQUEST['number']);	
				}
				if(!empty($_REQUEST['supply']))
				{
					$map[supplier]=$_REQUEST['supply'];
					$this->assign('supply',$_REQUEST['supply']);
				}
				if(!empty($_REQUEST['name']))
				{
					$map[name]=$_REQUEST['name'];
					$this->assign('name',$_REQUEST['name']);
				}
				if(!empty($_REQUEST['city']))
				{
					$where[city]=array("like","%".$_REQUEST['city']."%");
					$supplierlsit=M("supplier")->where($where)->field("id")->select();
					$supplierid=array();
					foreach($supplierlsit as $i=>$va){
						$supplierid[$i]=$va[id];
					}
					$map[supplierid]=array("in",$supplierid);
					$this->assign('city',$_REQUEST['city']);					
				}
				$supplier=M("supplier")->where("status=1")->field("id,supplier")->select();
				$this->assign("supplier",$supplier);
			}
			//历史供应商
            if($_REQUEST['tab']==4){
				$model = M("supplier");
				$map[status]=2;//已删除
				if($_SESSION['account']!='admin'){
					$name=M('role')->where(array('id'=>$_SESSION['position']))->getfield('name');
					$department=M('dept')->where(array('id'=>$_SESSION['department']))->getfield('name');
					if($name=='材料经理'){
						$map['department']='材料部';
					}elseif($name=='市场部总监'){
						$map['department']='市场部';
					}elseif($_SESSION['account']=='chongfazhan'){
						$map['department']='工程配合部';
					}elseif($_SESSION['account']=='taojianhua'){
						$map['department']='专项部';
					}else{
						//$map[creater]=$_SESSION['account'];
						if($_SESSION[account]!="hukeke")
						{
							$map[creater]=$_SESSION['account'];
						}
						else
						{
							$map[creater]=array("in","hukeke,huangdian,liwen,chenxinqiao");
						}
					}
				}
				if(!empty($_REQUEST['supplier3']))
				{
					$map[supplier]=$_REQUEST['supplier3'];
					$this->assign('supplier3',$_REQUEST['supplier3']);	
				}
				if(!empty($_REQUEST['city']))
				{
					$map[city]=array("like","%".$_REQUEST['city']."%");
					$this->assign('city',$_REQUEST['city']);	
				}
				if(!empty($_REQUEST[brands])){
					$map[classify]=array("like","%".$_REQUEST['brands']."%");
					$this->assign('brands',$_REQUEST['brands']);
				}
			} 
            //材料类别表			
            if($_REQUEST['tab']==5){
				$model = M("Companyclassify");
				if(!empty($_REQUEST['brands']))
				{
					$map[name]=$_REQUEST['brands'];
					$this->assign('brands',$_REQUEST['brands']);	
				}
			}

            //待删除供应商审核
			if($_REQUEST['tab']==7){
				$name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
				if($_SESSION[account]=='admin' || $name=="材料经理" || $_SESSION[account]=='chongfazhan' || $_SESSION[account]=="taojianhua"){
					$name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
					$account=$_SESSION['account'];
					$flag_supplier=0;
					if($name=="材料经理"){
						$map['department']='材料部';
						$flag_supplier=1;
					}
					if($name=="市场部总监"){
						$map['department']='市场部';
						$flag_supplier=1;
					}
					if($account=="taojianhua"){
						$map['department']='专项部';
						$flag_supplier=1;
					}
					if($account=="chongfazhan"){
						$map['department']='工程配合部';
						$flag_supplier=1;
					}
					$model = M("supplier");
					$map[status]=3;//待删除
									
					if(!empty($_REQUEST['supplier1']))
					{
						$map[supplier]=$_REQUEST['supplier1'];
						$this->assign('supplier1',$_REQUEST['supplier1']);	
					}
					if(!empty($_REQUEST['city']))
					{
						$map[city]=array("like","%".$_REQUEST['city']."%");
						$this->assign('city',$_REQUEST['city']);	
					}
					if(!empty($_REQUEST[brands])){
						$map[classify]=array("like","%".$_REQUEST['brands']."%");
						$this->assign('brands',$_REQUEST['brands']);
					}
				}
				
			}			
		}
		if (!empty($model)) {
			$this->_list($model, $map,'ctime',false);
		}
		$type=M('supplier')->group('classify')->field('classify')->select();
		$this->assign('type',$type);		
		$cities=M("cities")->field("id,city")->select();
		$this->assign("cities",$cities);
		$provinces=M("provinces")->field("id,province")->select();
		$this->assign("provinces",$provinces);
		$brand=M("Companyclassify")->field("id,name")->select();
		$this->assign("brand",$brand);
		$this->display();
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
            
            //查找供应商创建人
            foreach ($voList as $key => $value) {
            	$voList[$key]['sqr']=M('user')->where(array('account'=>$value['creater']))->getfield('nickname');
            }

			foreach($voList as $i=>$va){
				if(!empty($va[filename])){
					$voList[$i][filename]=explode(',',$va[filename]);
				    $voList[$i][newname]=explode(',',$va[newname]);
					$voList[$i][creater]=$va[creater].",admin";
				}
				$province=explode(',',$va[province]);
				if(in_array('全国',$province)){
					$voList[$i][city]="全国";
				}
				if(empty($va[city]) && !empty($va[province])){
					$voList[$i][city]=$va[province];
				}

			}

            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            $data[city]=$_REQUEST[city];
            $data[supplier1]=$_REQUEST[supplier1];
            $data[brands]=$_REQUEST[brands];
            $data[number]=$_REQUEST[number];
            $data[name]=$_REQUEST[name];
            $data[supply]=$_REQUEST[supply];
            foreach ($data as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
	            }
	        }
            // dump($map);die;
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
	
	public function insert(){
		if(empty($_POST[city])){
			
			if(!in_array('全国',$_POST[province])){
				$map[province]=array("in",$_POST[province]);
			}
			$province=M("provinces")->where($map)->field("provinceid")->select();
			$provinceid=array();
			foreach($province as $j=>$va){
				$provinceid[$j]=$va[provinceid];
			}
			$map1[provinceid]=array("in",$provinceid);
			
			$city=M("cities")->where($map1)->field("city")->select();
			
			$cityarr=array();
			foreach($city as $n=>$va){
				$cityarr[$n]=$va[city];
			}
			$area=implode(",",$cityarr);
		}
		else{
			$area=implode(",",$_POST[city]);
		}
		$data2[city]=$area;
		$data2[province]=implode(",",$_POST[province]);
		$data2[supplier]=$_POST[supplier];
		$data2[name]=$_POST[name];
		$data2[telephone]=$_POST[telephone];
		$data2[address]=$_POST[address];
		$data2[bankaccount]=$_POST[bankaccount];
		$data2[bankname]=$_POST[bankname];
		$data2[bankhu]=$_POST[bankhu];
		$data2[classify]=trim(implode(',',$_POST[classify]));
		$data2[creater]=$_SESSION[account];
		if($_SESSION['account']=='taojianhua'){
			$data2[department]='专项部';
		}elseif($_SESSION['account']=='chongfazhan'){
			$data2[department]='工程配合部';
		}else{
			$data2[department]=$_SESSION[dept];
		}
		
		$data2[status]=0;	
		// dump($_POST[classify]);die;	
		if(!empty($_FILES['file']['name']))
		{
			for($i=0;$i<count($_FILES['file']['name']);$i++){
				$savePath = '../Public/Uploads/';//设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$i])))); 
				$uuid=uniqid(rand(), false);
				$newname = $uuid.'.'.$ext;
				$upload_file = $savePath.$newname;
				$filename=$_FILES['file']['name'][$i];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				move_uploaded_file($_FILES['file']['tmp_name'][$i],$upload_file);
				$data2[newname] .= $newname.",";
				$data2[filename] .= $_FILES['file']['name'][$i].",";
			}			
		}
		
		$data2[ctime]=time();
	    $data2[history]=date("Y-m-d").'--'.$_SESSION[loginUserName].'添加供应商';
		$id=M("supplier")->add($data2);
		if(!empty($id)){
			for($k=0;$k<count($_POST[para2]);$k++){
				$data1[brand]=$_POST[para1][$k];
				$data1[number]=$_POST[para2][$k];
				$data1[name]=$_POST[para3][$k];
				$data1[standard]=$_POST[para4][$k];
				$data1[unit]=$_POST[para5][$k];
				$data1[price]=$_POST[para6][$k];
				$data1[supplierid]=$id;
				$data1[supplier]=$_POST[supplier];
				$data1["sort"]=$k;
				M("materials")->add($data1);
			}
			
			if(!empty($_FILES["load"]["name"]))
			{					
				Vendor ('Excel.PHPExcel');
				$filename=$_FILES['load']['name'];
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				if($filename!=null)
				{
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
					move_uploaded_file($_FILES['load']['tmp_name'],$upload_file);
					$file = $newname;
					$filerealname = $filename;
				}
				$filePath=$upload_file;
				$PHPExcel = new PHPExcel(); 

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
				//dump($allColumn);		
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
				$time=time();				
				for($j=0;$j<$count;$j++)
				{   
					if(!empty($data[$j]['C'])){
						$newdata['number']=$data[$j]['B'];
						$newdata['name']=$data[$j]['C'];
						$newdata['brand']=$data[$j]['A'];
						$newdata['standard']=$data[$j]['D'];					
						$newdata['unit']=$data[$j]['E'];
						$newdata['price']=$data[$j]['F'];
						$newdata[supplierid]=$id;
					    $newdata[supplier]=$_POST[supplier];
						$newdata['sort']=$j;
						M("materials")->add($newdata);
					}															
				}
			}
			
		}
				
		$this->redirect('index','tab=2');
	}
	public function edit1(){
		$brand=M("Companyclassify")->where("id='".$_REQUEST[id]."'")->find();
		$this->assign("brand",$brand);
		$this->display();
	}
	public function insert1(){		
		$data[name]=$_POST[name];		
		$data[ctime]=time();
		M("Companyclassify")->add($data);
		$this->redirect('index','tab=5');
	}
	public function update(){		
		$data[name]=$_POST[name];
		M("Companyclassify")->where("id='".$_POST[id]."'")->save($data);
		$this->redirect('index','tab=5');
	}
	
	public function draft2(){
		$suppliers=M("supplier")->where("status=1")->field("id,supplier")->select();
		$this->assign("suppliers",$suppliers);
		$brands=M("Companyclassify")->field("id,name")->select();
		$this->assign("brands",$brands);
		$this->display();
	}
	
	public function edit2(){
		$materials=M("materials")->where("id='".$_REQUEST[id]."'")->find();
		$this->assign("materials",$materials);
		$this->display();
	}
	
	public function insert2(){
		$data[name]=$_POST[name];
		$data[number]=$_POST[number];
        $data[standard]=$_POST[standard];
		$data[price]=$_POST[price];
		$data[unit]=$_POST[unit];
		$data[supplier]=$_POST[supplier];
		$data[brand]=$_POST[brand];
		$data[supplierid]=M('supplier')->where(array('supplier'=>$_POST['supplier']))->getfield('id');
		$data[brandid]=M('Companyclassify')->where(array('name'=>$_POST['brand']))->getfield('id');
		$data[ctime]=time();
		$data[status]=0;
		$res=M("materials")->add($data);
		if($res){
			$this->redirect('index','tab=3');
		}else{
			$this->error('添加失败,请重新输入');
		}
		
	}
	
	public function update2(){
		$data[name]=$_POST[name];
		$data[number]=$_POST[number];
        $data[standard]=$_POST[standard];
		$data[price]=$_POST[price];
		$data[unit]=$_POST[unit];
		M("materials")->where("id='".$_POST[id]."'")->save($data);
		$this->redirect('index','tab=3');
	}
	
	
	public function ajax(){
		$supplier=M("supplier")->where("supplier='".$_POST[supplier]."'")->find();
		if(empty($supplier)){
			echo 1;
		}else{
			echo json_encode("供应商已存在");
		}
	}
	
	public function ajax1(){
		$name=M("Companyclassify")->where("name='".$_POST[name]."'")->find();
		if(empty($name)){
			echo 1;
		}else{ 
			echo "品牌已存在";
		}
	}
	public function foreverdelete(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		M("materials")->where("supplier='".$supplier[supplier]."'")->delete();
		M("supplier")->where("id='".$_POST[id]."'")->delete();
		echo 1;
	}
	
	public function chexiao(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		$data[status]=1;
		$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'撤销删除供应商';
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		echo 1;
	}
	public function foreverdelete3(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		$data[status]=1;
		$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'撤销删除供应商';
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		echo 1;
	}
	public function foreverdelete1(){
		M("Companyclassify")->where("id='".$_POST[id]."'")->delete();
		echo 1;
	}
	public function foreverdelete2(){
		M("materials")->where("id='".$_POST[id]."'")->delete();
		echo 1;
	}
	
	public function editsupplier()
	{	
		$department=$_SESSION['dept'];
			if($department=='材料部' || $department=='市场部'){
				$cl=M('Companyclassify')->select();
			}elseif ($department=="专项部" || $department=='工程配合部') {
				$cl=M('classify')->select();
			}
			// dump($classify);die;
			$this->assign('cl',$cl);
		$province=M("provinces")->select();
		$provinces[]=array('id'=>35,'provinceid'=>35,'province'=>'全国');
		foreach($province as $key=>$value){
			$provinces[]=$province[$key];
		}

		// dump($provinces);die;
		$this->assign("provinces",$provinces);
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$province=explode(',',$supplierinfo[province]);
		if(in_array('全国',$province)){
			$supplierinfo[city]="全国";
		}
		if(empty($supplierinfo[city]) && !empty($supplierinfo[province])){
			$supplierinfo[city]=$supplierinfo[province];
		}	
		$supplierinfo['classify']=explode(',',$supplierinfo['classify']);
		// dump($supplierinfo['classify']);
		$this->assign("supplierinfo",$supplierinfo);
		
		$this->display();
	}
	
	public function suppliercheck()
	{
		$supplier=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$materials=M("materials")->where("supplier='".$supplier[supplier]."'")->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("Companyclassify")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$history=explode(",",$supplierinfo[history]);
		$this->assign("supplierinfo",$supplierinfo);
		$this->assign("history",$history);
		
		$this->display();
	}
	
	public function approvesupplier()
	{
		$supplier=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$materials=M("materials")->where("supplier='".$supplier[supplier]."'")->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("Companyclassify")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$province=explode(',',$supplierinfo[province]);
		if(in_array('全国',$province)){
			$supplierinfo[city]="全国";
		}
		if(empty($supplierinfo[city]) && !empty($supplierinfo[province])){
			$supplierinfo[city]=$supplierinfo[province];
		}	
		$this->assign("supplierinfo",$supplierinfo);
		
		$this->display();
	}
	public function approvesubmit(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		if($_REQUEST[result]=="同意")
		{
			$data[status]=1;
			$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'审核同意添加';
		}
		else
		{
			$data[status]=-1;
			$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'审核驳回添加';
		}
		$data[approver]=$_SESSION[loginUserName];
		$data[approve_time]=time();
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		
		$this->redirect('index','tab=6');
	}
	
	public function approvesupplier1()
	{
		$supplier=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$materials=M("materials")->where("supplier='".$supplier[supplier]."'")->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("Companyclassify")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$this->assign("supplierinfo",$supplierinfo);
		
		$this->display();
	}
	public function approvesubmit1(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		if($_REQUEST[result]=="同意")
		{
			$data[status]=2;
			$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'审核同意删除';
			M("materials")->where("supplier='".$supplier[supplier]."'")->setfield("status",1);
		}
		else
		{
			$data[status]=1;
			$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'审核驳回删除';
		}
		$data[approver]=$_SESSION[loginUserName];
		$data[approve_time]=time();
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		
		$this->redirect('index','tab=4');
	}
	public function updatesupplier(){		
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		if(empty($_POST[city])){
			if(!in_array('全国',$_POST[province])){
				$map[province]=array("in",$_POST[province]);
			}
			$province=M("provinces")->where($map)->field("provinceid")->select();
			$provinceid=array();
			foreach($province as $j=>$va){
				$provinceid[$j]=$va[provinceid];
			}
			$map1[provinceid]=array("in",$provinceid);
			$city=M("cities")->where($map1)->field("city")->select();
			$cityarr=array();
			foreach($city as $n=>$va){
				$cityarr[$n]=$va[city];
			}
			$area=implode(",",$cityarr);
		}
		else{
			$area=implode(",",$_POST[city]);
		}
		$data[city]=$area;
		$data[province]=implode(",",$_POST[province]);
		$data[supplier]=$_POST[supplier];
		$data[classify]=trim(implode(',',$_POST[classify]));
		$data[name]=$_POST[name];
		$data[telephone]=$_POST[telephone];
		$data[address]=$_POST[address];
		$data[bankname]=$_POST[bankname];
		$data[bankhu]=$_POST[bankhu];
		$data[bankaccount]=$_POST[bankaccount];
		$data[status]=0;		
		if(!empty($_FILES['file']['name']))
		{
			for($i=0;$i<count($_FILES['file']['name']);$i++){
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$i])))); 
				$uuid=uniqid(rand(), false);
				$newname = $uuid.'.'.$ext;
				$upload_file = $savePath.$newname;	
				$filename=$_FILES['file']['name'][$i];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				move_uploaded_file($_FILES['file']['tmp_name'][$i],$upload_file);
				$data[newname] .= $newname.",";
				$data[filename] .= $_FILES['file']['name'][$i].",";
			}			
		}
		$data[ctime]=time();
		$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'修改供应商信息';
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		if(!empty($_FILES["load"]["name"]))
		{	
            M("materials")->where("supplier='".$supplier[supplier]."'")->delete();	
			Vendor ('Excel.PHPExcel');
			$filename=$_FILES['load']['name'];
			$savePath = '../Public/Uploads/';     //设置附件上传目录
			if($filename!=null)
			{
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
				move_uploaded_file($_FILES['load']['tmp_name'],$upload_file);
				$file = $newname;
				$filerealname = $filename;
			}
			$filePath=$upload_file;
			$PHPExcel = new PHPExcel(); 

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
			//dump($allColumn);		
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
			
			$time=time();				
			for($j=0;$j<$count;$j++)
			{    
				if(!empty($data[$j]['C'])){
					$newdata['number']=$data[$j]['B'];
					$newdata['name']=$data[$j]['C'];
					$newdata['brand']=$data[$j]['A'];
					$newdata['standard']=$data[$j]['D'];					
					$newdata['unit']=$data[$j]['E'];
					$newdata['price']=$data[$j]['F'];
					$newdata[supplierid]=$_POST[id];
					$newdata[supplier]=$_POST[supplier];
					$newdata['sort']=$j;
					M("materials")->add($newdata);	
				}                					
																				
			}
		}						
		
		$this->redirect('index','tab=2');
	}
	public function check()
	{
		$supplier=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$materials=M("materials")->where("supplier='".$supplier[supplier]."'")->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("Companyclassify")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$this->assign("supplierinfo",$supplierinfo);
		
		$this->display();
	}
	
    public function delete(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		$data[status]=3;
		$data[reason]=$_REQUEST[reason];
		$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'删除供应商';
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		//M("materials")->where("supplierid='".$_POST[id]."'")->setfield("status",1);
		$this->redirect('index','tab=7');
	}

    public function check1()
	{
		$supplier=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$materials=M("materials")->where("supplier='".$supplier[supplier]."'")->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("Companyclassify")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$history=explode(",",$supplierinfo[history]);
		$this->assign("supplierinfo",$supplierinfo);
		$this->assign("history",$history);
		
		$this->display();
	}	

    public function ajaxqy(){
		$supplier=M("supplier")->where("id='".$_POST[id]."'")->find();
		$data[status]=1;
		$data[history]=$supplier[history].','.date("Y-m-d").'--'.$_SESSION[loginUserName].'启用供应商';
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		M("materials")->where("supplier='".$supplier[supplier]."'")->setfield("status",0);
		echo 1; 
	}	
	public function ajaxsc(){
		M("supplier")->where("id='".$_POST[id]."'")->delete();
		M("materials")->where("supplier='".$supplier[supplier]."'")->delete();
		echo 1; 
	}

	public function ajaxcity(){
		$city=$_REQUEST[city];
		$info=M('supplier')->where($map)->select();
		
		foreach($info as $key=>$value){
			if(!empty($value)){
				$array_city=explode(',',$value['city']);
				if(in_array($city,$array_city)){
					$supplier[]=$info[$key];
				}
			}
		}
		echo json_encode($supplier);
	}
} 
?>