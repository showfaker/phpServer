<?php
class ReasonsAction extends CommonAction {			
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
		if(($_REQUEST['tab']==1)||empty($_REQUEST['tab'])){
			$model=M('Settingreason');
			if(!empty($_REQUEST[name])){
				$map[name]=$_REQUEST[name];
				$this->assign('name',$_REQUEST[name]);
			}
			$list=$model->select();
            $this->assign('list',$list);
		}
	
		
		if($_REQUEST['tab']==2){
			$model=M('Settingcapacity');
			if(!empty($_REQUEST[name])){
				$map[name]=$_REQUEST[name];
				$this->assign('name',$_REQUEST[name]);
			}
			$list=$model->select();
            $this->assign('list',$list);
		}
		
		if($_REQUEST['tab']==3){
			$model=M('Plmoperatetype');
			if(!empty($_REQUEST[name])){
				$map[name]=$_REQUEST[name];
				$this->assign('name',$_REQUEST[name]);
			}
			$map['status']=0;
			$list=$model->where(array('status'=>0))->order("sort asc")->select();
            $this->assign('list',$list);

		}
		
		
		
		
		//$this->getAllcities(1);
		//$cities=M("cities")->select();
		//$this->assign("cities",$cities);
		//$brand=M("brand")->select();
		//$this->assign("brand",$brand);
		
		$classifies=M("Classifyfile")->where(array('status'=>0))->select();
        $this->assign('classifies',$classifies);
			
		$this->display();
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
	
	
	
	
	
	
	
	//类别管理新增
    public function add(){
		
		$model=M('classifyfolder');
		$folders=$model->where(array('status'=>0))->select();
		$this->assign('folders',$folders);
			
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
		$data['classify']=$_REQUEST['classify'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['c_name']=$_SESSION['loginUserName'];
    	$res=M('Settingreason')->add($data);
    	if($res){
    		$this->success('添加成功.');
    	}else{
    		$this->error('添加失败,请重试!');
    	}
    }
    //编辑工程类
    public function edit(){
    	$id=$_REQUEST['id'];
    	$info=M('Settingreason')->where(array('id'=>$id))->find();
    	$this->assign('info',$info);
		
		$model=M('classifyfolder');
		$folders=$model->where(array('status'=>0))->select();
		$this->assign('folders',$folders);
		
    	$this->display();
    }
    public function update(){
     	$id=$_REQUEST[id];
    	$data['name']=$_REQUEST['name'];
		$data['classify']=$_REQUEST['classify'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['c_name']=$_SESSION['loginUserName'];
    	$res=M('Settingreason')->where(array('id'=>$id))->save($data);
    	if($res){
    		$this->success('修改成功.');
    	}else{
    		$this->error('修改失败,请重试!');
    	}
    }
    //删除工程类
    public function del(){
    	$id=$_REQUEST['id'];
    	$res=M('classifyfile')->where(array('id'=>$id))->delete();
    	if($res){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }
	
	
	//类别管理新增
    public function add2(){
		
    	$this->display();
    }
    //执行add
    public function insert2(){
    	$data['begin']=$_REQUEST['begin'];
		$data['end']=$_REQUEST['end'];
    	$data['ctime']=time();
    	$data['c_name']=$_SESSION['loginUserName'];
		
		
    	$res=M('Settingcapacity')->add($data);
    	if($res){
    		$this->success('添加成功.');
    	}else{
    		$this->error('添加失败,请重试!');
    	}
    }
    //编辑工程类
    public function edit2(){
    	$id=$_REQUEST['id'];
    	$vo=M('Settingcapacity')->where(array('id'=>$id))->find();
    	$this->assign('id',$id);
		$this->assign('vo',$vo);
		
    	$this->display();
    }
    public function edit_submit2(){
     	$id=$_REQUEST[id];
		
		$oldinfo=M("Settingcapacity")->where(array('id'=>$id))->find();
		
    	$data['begin']=$_REQUEST['begin'];
		$data['end']=$_REQUEST['end'];
    	$data['ctime']=time();
    	$data['c_name']=$_SESSION['loginUserName'];
		
	
    	$res=M('Settingcapacity')->where(array('id'=>$id))->save($data);
	
		
    	if($res){
    		$this->success('修改成功.');
    	}else{
    		$this->error('修改失败,请重试!');
    	}
    }
    //删除工程类
    public function delete2(){
    	$id=$_REQUEST['id'];
    	$res=M('Settingcapacity')->where(array('id'=>$id))->delete();
    	if($res){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }
	
	//类别管理新增
    public function add3(){
		
		$workclassifies=M("Workclassify")->select();
		$this->assign('workclassifies',$workclassifies);
    	$this->display();
    }
    //执行add
    public function insert3(){
    	$data['name']=$_REQUEST['name'];
		$data['sort']=$_REQUEST['sort'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['user']=$_SESSION['loginUserName'];
		
		$contentarray=$_REQUEST["content"];
		foreach($contentarray as $key => $val)
		{
			$content.=$val.",";
		}
		$data['content']=$content;
		
    	$res=M('Plmoperatetype')->add($data);
    	if($res){
    		$this->success('添加成功.');
    	}else{
    		$this->error('添加失败,请重试!');
    	}
    }
    //编辑工程类
    public function edit3(){
    	$id=$_REQUEST['id'];
    	$vo=M('Plmoperatetype')->where(array('id'=>$id))->find();
    	$this->assign('id',$id);
		$this->assign('vo',$vo);
		
		
		$workclassifies=M("Workclassify")->select();
		$this->assign('workclassifies',$workclassifies);
		
    	$this->display();
    }
    public function edit_submit3(){
     	$id=$_REQUEST[id];
		
		$oldinfo=M("Plmoperatetype")->where(array('id'=>$id))->find();
		
    	$data['name']=$_REQUEST['name'];
		$data['sort']=$_REQUEST['sort'];
    	$data['ctime']=time();
    	$data['status']=0;
    	$data['user']=$_SESSION['loginUserName'];
		
		$contentarray=$_REQUEST["content"];
		foreach($contentarray as $key => $val)
		{
			$content.=$val.",";
		}
		$data['content']=$content;
    	$res=M('Plmoperatetype')->where(array('id'=>$id))->save($data);
	
		
    	if($res){
    		$this->success('修改成功.');
    	}else{
    		$this->error('修改失败,请重试!');
    	}
    }
    //删除工程类
    public function delete3(){
    	$id=$_REQUEST['id'];
    	$res=M('Plmoperatetype')->where(array('id'=>$id))->delete();
    	if($res){
    		echo 1;
    	}else{
    		echo 2;
    	}
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function addoa() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->assign('type', $_REQUEST["type"]);
		$this->assign('classify', $_REQUEST["classify"]);
		$this->display("addoa");
	} 
	function insertoa() {
		$type=$_REQUEST["type"];
		$classify=$_REQUEST["classify"];
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$savePath = '../Public/Uploads/';     //设置附件上传目录		
		if(!empty($_FILES['file']['name'][0]))/*empty*/
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
			if($type=="可研编制文件")
			{
				$file="programme2";
				$filerealname="programmefilename2";
			}
			if($type=="项目估算书")
			{
				$file="programme";
				$filerealname="programmefilename";
			}
			if($type=="初步合作协议文件")
			{
				$file="programme4";
				$filerealname="programmefilename4";
			}
			if($type=="可研评审报告")
			{
				$file="programme3";
				$filerealname="programmefilename3";
			}
			
			if($type=="备案证")
			{
				$file="keeponrecord1";
				$filerealname="keeponrecord1filename";
			}
			if($type=="信息报送表")
			{
				$file="keeponrecord2";
				$filerealname="keeponrecord2filename";
			}
			
			if($type=="综合计划复批文件")
			{
				$file="enter";
				$filerealname="enterfilename";
			}
			if($type=="项目合同文件")
			{
				$file="contract";
				$filerealname="contractfilename";
			}
			if($type=="初步设计文件")
			{
				$file="illustration";
				$filerealname="clientillustration";
			}
			if($type=="初步设计评审报告")
			{
				$file="drawing";
				$filerealname="drawingfilename";
			}
			if($type=="任务单文件")
			{
				$file="taskfile";
				$filerealname="taskfilename";
			}
			
			if($type=="联合验收（监理验收文件）")
			{
				$file="finish01";
				$filerealname="finishfilename01";
			}
			if($type=="联合验收（自查验收文件）")
			{
				$file="finish02";
				$filerealname="finishfilename02";
			}
			if($type=="联合验收（整改验收报告文件）")
			{
				$file="finish03";
				$filerealname="finishfilename03";
			}
			if($type=="联合验收（联合验收多方签字文件）")
			{
				$file="finish04";
				$filerealname="finishfilename04";
			}
			if($type=="联合验收（工程量三方确认单）")
			{
				$file="finish05";
				$filerealname="finishfilename05";
			}
			
			
			if($type=="竣工验收（验收任务单）")
			{
				$file="finish";
				$filerealname="finishfilename";
			}
			
			if($type=="项目验收（竣工报告）")
			{
				$file="finishphoto1";
				$filerealname="finishphotofilename1";
			}
			if($type=="项目验收（资产移交清册）")
			{
				$file="finishphoto2";
				$filerealname="finishphotofilename2";
			}
			if($type=="项目验收（竣工图）")
			{
				$file="finishphoto3";
				$filerealname="finishphotofilename3";
			}
			
			if($type=="工程结决算（送审结算书）")
			{
				$file="budgetfinalcheck1";
				$filerealname="budgetfinalcheckfilename1";
			}
			if($type=="工程结决算（现场签字审批单）")
			{
				$file="budgetfinalcheck2";
				$filerealname="budgetfinalcheckfilename2";
			}
			if($type=="工程结决算（变更工程量审计单）")
			{
				$file="budgetfinalcheck3";
				$filerealname="budgetfinalcheckfilename3";
			}
			if($type=="工程结决算（工程审计申请表）")
			{
				$file="budgetfinalcheck4";
				$filerealname="budgetfinalcheckfilename4";
			}
			if($type=="工程结决算（结算表）")
			{
				$file="budgetfinalcheck5";
				$filerealname="budgetfinalcheckfilename5";
			}
			$model->$file=$newnameall;
			$model->$filerealname=$filenameall;
			
			$handlehistory.=$_SESSION['loginUserName']."于".$date."从项目文档上传了".$type."</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$list = $model->save();
		
		
		
		
		if(!empty($_FILES['file']['name'][0]))
		{
			$name = "Plmcooperate";
			$model = D($name);
			if($type=="合作协议文件")
			{
				$model->contract=$newnameall;
				$model->contractfilename=$filenameall;
				
				$mapforrepeat[plmNumber]=$info["id"];
				$repeat=M("Plmcooperate")->where($mapforrepeat)->find();	
				if($repeat)
				{
					$model->id=$repeat[id];
					$model->save();
				}
				else
				{
					
					$model->plmNumber=$info['id'];
					$model->title=$info['title'];
					$model->number=$info['number'];
					$model->addPerson=$_SESSION['loginUserName'];
					$model->create_time=time();
					$model->add();
				}
			}
			if($type=="备案文件")
			{
				$model->contractrecord=$newnameall;
				$model->contractrecordfilename=$filenameall;
				
				$mapforrepeat[plmNumber]=$info["id"];
				$repeat=M("Plmcooperate")->where($mapforrepeat)->find();	
				if($repeat)
				{
					$model->id=$repeat[id];
					$model->save();
				}
				else
				{
					$model->plmNumber=$info['id'];
					$model->title=$info['title'];
					$model->number=$info['number'];
					$model->addPerson=$_SESSION['loginUserName'];
					$model->create_time=time();
					$model->add();
				}
			}
			
		}
		
			
			
		if ($list !== false) { //保存成功
			//$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			//$this->success('新增成功!');
			$this->redirect('index','tab=5&plmid='.$_POST[id].'/'.'classify='.$_POST[classify].'/');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	
	
	
	
	
	
	
	public function download_zip()
	{
		//$img = [['url'=>'http://10.178.0.107/project222/Public/Uploads/131736311cbae8955c.pdf','name'=>'名字']];
		
		
			
		$map[plmNumber]=$_REQUEST['plmid'];
		$plminfo=M("Project")->where("id=".$_REQUEST['plmid'])->find();
		$i=0;
		$files[$i]['file']=explode(',',$plminfo['programme2']);
		$files[$i]['filename']=explode(',',$plminfo['programmefilename2']);
		$files[$i]['type']="可研编制文件";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['programme']);
		$files[$i]['filename']=explode(',',$plminfo['programmefilename']);
		$files[$i]['type']="项目估算书";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['programme4']);
		$files[$i]['filename']=explode(',',$plminfo['programmefilename4']);
		$files[$i]['type']="初步合作协议文件";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['programme3']);
		$files[$i]['filename']=explode(',',$plminfo['programmefilename3']);
		$files[$i]['type']="可研评审报告";
		$i++;
		
		
		$files[$i]['file']=explode(',',$plminfo['keeponrecord1']);
		$files[$i]['filename']=explode(',',$plminfo['keeponrecord1filename']);
		$files[$i]['type']="备案证";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['keeponrecord2']);
		$files[$i]['filename']=explode(',',$plminfo['keeponrecord2filename']);
		$files[$i]['type']="信息报送表";
		$i++;
		
		$mapforPlmcooperate[plmNumber]=$_REQUEST['plmid'];
		$plmcooperate=M("Plmcooperate")->where($mapforPlmcooperate)->find();
		$files[$i]['file']=explode(',',$plmcooperate['contract']);
		$files[$i]['filename']=explode(',',$plmcooperate['contractfilename']);
		$files[$i]['type']="合作协议文件";
		$i++;
		$files[$i]['file']=explode(',',$plmcooperate['contractrecord']);
		$files[$i]['filename']=explode(',',$plmcooperate['contractrecordfilename']);
		$files[$i]['type']="备案文件";
		$i++;
		
		
		$files[$i]['file']=explode(',',$plminfo['enter']);
		$files[$i]['filename']=explode(',',$plminfo['enterfilename']);
		$files[$i]['type']="综合计划复批文件";
		$i++;
		/*
		$files[$i]['file']=explode(',',$plminfo['contract']);
		$files[$i]['filename']=explode(',',$plminfo['contractfilename']);
		$files[$i]['type']="项目合同文件";
		$i++;
		*/
		$files[$i]['file']=explode(',',$plminfo['illustration']);
		$files[$i]['filename']=explode(',',$plminfo['clientillustration']);
		$files[$i]['type']="初步设计文件";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['drawing']);
		$files[$i]['filename']=explode(',',$plminfo['drawingfilename']);
		$files[$i]['type']="初步设计评审报告";
		$i++;
		
		$files[$i]['file']=explode(',',$plminfo['taskfile']);
		$files[$i]['filename']=explode(',',$plminfo['taskfilename']);
		$files[$i]['type']="任务单文件";
		$i++;
		
		$files[$i]['file']=explode(',',$plminfo['finish01']);
		$files[$i]['filename']=explode(',',$plminfo['finishfilename01']);
		$files[$i]['type']="联合验收（监理验收文件）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finish02']);
		$files[$i]['filename']=explode(',',$plminfo['finishfilename02']);
		$files[$i]['type']="联合验收（自查验收文件）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finish03']);
		$files[$i]['filename']=explode(',',$plminfo['finishfilename03']);
		$files[$i]['type']="联合验收（整改验收报告文件）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finish04']);
		$files[$i]['filename']=explode(',',$plminfo['finishfilename04']);
		$files[$i]['type']="联合验收（联合验收多方签字文件）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finish05']);
		$files[$i]['filename']=explode(',',$plminfo['finishfilename05']);
		$files[$i]['type']="联合验收（工程量三方确认单）";
		$i++;
		
		
		$files[$i]['file']=explode(',',$plminfo['finish']);
		$files[$i]['filename']=explode(',',$plminfo['finishfilename']);
		$files[$i]['type']="竣工验收（验收任务单）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finishphoto1']);
		$files[$i]['filename']=explode(',',$plminfo['finishphotofilename1']);
		$files[$i]['type']="项目验收（竣工报告）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finishphoto2']);
		$files[$i]['filename']=explode(',',$plminfo['finishphotofilename2']);
		$files[$i]['type']="项目验收（资产移交清册）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['finishphoto3']);
		$files[$i]['filename']=explode(',',$plminfo['finishphotofilename3']);
		$files[$i]['type']="项目验收（竣工图）";
		$i++;
		
		/*
		$files[$i]['file']=explode(',',$plminfo['budgetfinal']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalfilename']);
		$files[$i]['type']="投运文件";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['budgetfinalcheck']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalcheckfilename']);
		$files[$i]['type']="审计文件";
		$i++;
		$files[$i]['evaluate']=explode(',',$plminfo['evaluate']);
		$files[$i]['evaluatefilename']=explode(',',$plminfo['evaluatefilename']);
		$files[$i]['type']="后评文件";
		*/
		
		$files[$i]['file']=explode(',',$plminfo['budgetfinalcheck1']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalcheckfilename1']);
		$files[$i]['type']="工程结决算（送审结算书）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['budgetfinalcheck2']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalcheckfilename2']);
		$files[$i]['type']="工程结决算（现场签字审批单）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['budgetfinalcheck3']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalcheckfilename3']);
		$files[$i]['type']="工程结决算（变更工程量审计单）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['budgetfinalcheck4']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalcheckfilename4']);
		$files[$i]['type']="工程结决算（工程审计申请表）";
		$i++;
		$files[$i]['file']=explode(',',$plminfo['budgetfinalcheck5']);
		$files[$i]['filename']=explode(',',$plminfo['budgetfinalcheckfilename5']);
		$files[$i]['type']="工程结决算（结算表）";
		$i++;
		
		$iii=0;
		foreach ($files as $key => $value)
		{
			foreach ($value["file"] as $key1 => $value1)
			{
				if(!empty($value1))
				{
					$img[$iii]["url"]="../Public/Uploads/".$value1;
					$img[$iii]["name"]=$value["filename"][$key1];
					$img[$iii]["folder"]="施工前";
					$iii++;
				}
			}
		}
		
		$list=M("Plmfile")->where($map)->select();
		$this->assign('list',$list);
		
		$classifies=M("Classifyfile")->where(array('status'=>0))->select();
		foreach ($classifies as $key => $value)
		{
			$nofile="YES";
			$x=0;
			foreach ($list as $key1 => $value1) 
			{
				if($value["name"]==$value1["type"])
				{
					$nofile="NO";
					$classifies[$key]["filelist"][$x]=$value1;
					$x++;
					
					$img[$iii]["url"]="../Public/Uploads/".$value1["newname"];
					$img[$iii]["name"]=$value1["filename"];
					$img[$iii]["folder"]=$value["classify"];
					$iii++;
				}
			}
			$classifies[$key]["nofile"]=$nofile;
		}
		//$img = [['url'=>'http://10.178.0.107/project222/Public/Uploads/131736311cbae8955c.pdf','name'=>'名字']];
		$this->Download($img);
	}
	
	
	/**
	* 下载文件
	* @param $img
	* @return string
	*/
	public function Download($img)
	{
		if($img)
		{
		  //用于前端跳转zip链接拼接
		  $path_redirect = '/zip/'.date('Ymd');
		  //临时文件存储地址
		  $path      = '/tmp'.$path_redirect;
		  if(!is_dir($path))
		  {
			mkdir($path, 0777,true);
		  }
		  foreach ($img as $key => $value) {
			$fileContent = '';
			//$fileContent = $this->CurlDownload($value['url']);
			$fileContent = file_get_contents($value['url']);
			if( $fileContent )
			{
				$__tmp = $this->SaveFile( $value['url'] , $path , $fileContent );
				$items[] = $__tmp[0];
				$names[] = $value['name'].'_'.($key+1).'.'.$__tmp[1];
				$folders[] = $value['folder'];
			}
		  }
		  
		  if( $items )
		  {
			$zip = new ZipArchive();
			$filename = time().'download.zip';
			$zipname = $path.'/'.$filename;
			if (!file_exists($zipname)) {
			  $res = $zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			  if ($res) {
				foreach ($items as $k => $v) {
				  $value = explode("/", $v);
				  $end  = end($value);
				  $zip->addEmptyDir($folders[$k]);
				  $zip->addFile($v, $folders[$k]."/".$end);
				  $zip->renameName($folders[$k]."/".$end, $folders[$k]."/".$names[$k]);
				}
				$zip->close();
			  } else {
				return '';
			  }
			  //通过前端js跳转zip地址下载,让不使用php代码下载zip文件
			  //if (file_exists($zipname)) {
				//拼接附件地址
				//$redirect = 域名.$path_redirect.'/'.$filename;
				//return $redirect;
				//header("Location:".$redirect);
			  //}
			  //直接写文件的方式下载到客户端
			  if (file_exists($zipname)) {
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header('Content-disposition: attachment; filename=附件.zip'); //文件名
				header("Content-Type: application/zip"); //zip格式的
				header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
				header('Content-Length: ' . filesize($zipname)); //告诉浏览器，文件大小
				@readfile($zipname);
			  }
			  //删除临时文件
			  @unlink($zipname);
			}
		  }
		  return '';
		}
	}
	/**
	* curl获取链接内容
	* @param $url
	* @return mixed|string
	*/
	public function CurlDownload($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$errno   = curl_errno($ch);
		$error   = curl_error($ch);
		$res=curl_exec($ch);
		curl_close($ch);
		if($errno>0){
		  return '';
		}
		return $res;
	}
	/**
	* 保存临时文件
	* @param $url
	* @param $dir
	* @param $content
	* @return array
	*/
	public function SaveFile( $url ,$dir , $content)
	{
		$fname   = basename($url); //返回路径中的文件名部分
		$str_name  = pathinfo($fname); //以数组的形式返回文件路径的信息
		$extname  = strtolower($str_name['extension']); //把扩展名转换成小写
		$path    = $dir.'/'.md5($url).$extname;
		$fp     = fopen( $path ,'w+' );
		fwrite( $fp , $content );
		fclose($fp);
		return array( $path , $extname) ;
	}




}
?>