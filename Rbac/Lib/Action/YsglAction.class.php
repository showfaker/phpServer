<?php
class YsglAction extends CommonAction {

	//过滤查询字段
	function _filter(&$map){
		//$map['projecttype'] = array("neq","承揽项目");
		//$map['step3'] = array("eq","1");
		if($_REQUEST['title'])
		{
			$map['title'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['number'])
		{
			$map['number'] = array('like',"%".$_REQUEST['number']."%");
			$this->assign("number",$_REQUEST['number']);
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
		if($_REQUEST['plmid'])
		{
			$map['id'] = array('eq',$_REQUEST['plmid']);
			$this->assign("plmid",$_REQUEST['plmid']);
		}
	}
	
	public function index() {
		
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		if(($_REQUEST["moduletitle"]=="项目设计管理")||($_SESSION["app"]==1))
		{
			if(!empty($_REQUEST['tab']))
			{
				$_SESSION[tab]=$_REQUEST['tab'];
			}
			else
			{
				$_SESSION[tab]=1;
			}
		}
        else
		{
			if(!empty($_REQUEST['tab']))
			{
				$_SESSION[tab]=$_REQUEST['tab'];
			}
			else
			{
				$_SESSION[tab]=3;
			}
		}
		$this->assign('tab',$_SESSION['tab']);		
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
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
		
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		//$map[design_status]=array("in","可研评审报告审批通过,合同审核完成,设计待审核,设计审核退回,设计审核通过,施工计划待审核,施工计划审核退回,施工计划审核通过,待施工,施工中,完成施工");//新加的  可研评审报告审批通过
		$map[design_status]=array("not in","取消,暂停中,暂存");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		$changepower=M("User")->where("id=".$_SESSION["id"])->getField("main");
		$this->assign('changepower', $changepower);
		$this->getAllcities();
		if($_SESSION["app"]=="1")
		{
			$this->display("indexapp");
			return;
		}
		if($_REQUEST["moduletitle"]=="项目设计管理")
		{
			$this->display("indexdesign");
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
			foreach($voList as $key => $val)
			{
				$voList[$key]['drawings']=explode(',',$val['drawing']);
				$voList[$key]['drawingsfilename']=explode(',',$val['drawingfilename']);
				
				
				$voList[$key]['drawings3']=explode(',',$val['drawing3']);
				$voList[$key]['drawingsfilename3']=explode(',',$val['drawingfilename3']);
				
				$voList[$key]['drawings4']=explode(',',$val['drawing4']);
				$voList[$key]['drawingsfilename4']=explode(',',$val['drawingfilename4']);
				
				$voList[$key]['drawings5']=explode(',',$val['drawing5']);
				$voList[$key]['drawingsfilename5']=explode(',',$val['drawingfilename5']);
				
				$voList[$key]['drawings6']=explode(',',$val['drawing6']);
				$voList[$key]['drawingsfilename6']=explode(',',$val['drawingfilename6']);
				
				$voList[$key]['drawings7']=explode(',',$val['drawing7']);
				$voList[$key]['drawingsfilename7']=explode(',',$val['drawingfilename7']);
				
				$voList[$key]['drawings8']=explode(',',$val['drawing8']);
				$voList[$key]['drawingsfilename8']=explode(',',$val['drawingfilename8']);
				
				$voList[$key]['drawings9']=explode(',',$val['drawing9']);
				$voList[$key]['drawingsfilename9']=explode(',',$val['drawingfilename9']);
				
				$voList[$key]['illustrations']=explode(',',$val['illustration']);
				$voList[$key]['clientillustrations']=explode(',',$val['clientillustration']);
				
				$voList[$key]['budgets']=explode(',',$val['budget']);
				$voList[$key]['budgetsfilename']=explode(',',$val['budgetfilename']);
				
				$voList[$key]['worktype']=M("Plmworktype")->where("plmid=".$val[id])->order("id asc")->select();
				
				
				if($_REQUEST["moduletitle"]=="主项节点设置")$classify="主项";
				if($_REQUEST["moduletitle"]=="开发节点设置")$classify="开发";
				if($_REQUEST["moduletitle"]=="设计节点设置")$classify="设计";
				if($_REQUEST["moduletitle"]=="采购节点设置")$classify="采购";
				if($_REQUEST["moduletitle"]=="设备采购节点")$classify="采购";
				if($_REQUEST["moduletitle"]=="施工节点设置")$classify="施工";
				
				if($_REQUEST["moduletitle"]=="主项节点设置")$voList[$key]['worktype_status']=$voList[$key]['worktype_status1'];
				if($_REQUEST["moduletitle"]=="开发节点设置")$voList[$key]['worktype_status']=$voList[$key]['worktype_status2'];
				if($_REQUEST["moduletitle"]=="设计节点设置")$voList[$key]['worktype_status']=$voList[$key]['worktype_status3'];
				if($_REQUEST["moduletitle"]=="采购节点设置")$voList[$key]['worktype_status']=$voList[$key]['worktype_status4'];
				if($_REQUEST["moduletitle"]=="设备采购节点")$voList[$key]['worktype_status']=$voList[$key]['worktype_status4'];
				if($_REQUEST["moduletitle"]=="施工节点设置")$voList[$key]['worktype_status']=$voList[$key]['worktype_status5'];
				
				
				
				if(false!==strstr($voList[$key]['worktype_status'],"待审核"))
				{
					$current=$this->findProjectleader($val["id"],$classify);
					$voList[$key]['current']=$current["nickname"];
				}
		
				
				//采购
				if(false!==strstr($voList[$key]['worktype_status'],"节点待工程部审核"))
				{
					$current=$this->findProjectleader($val["id"],"施工");
					$voList[$key]['current']=$current["nickname"];
				}
				if(false!==strstr($voList[$key]['worktype_status'],"节点待采购部审核"))
				{
					$current=$this->findProjectleader($val["id"],"采购");
					$voList[$key]['current']=$current["nickname"];
				}
				
				
				if(false!==strstr($voList[$key]['worktype_status'],"节点变更待工程部审核"))
				{
					$current=$this->findProjectleader($val["id"],"施工");
					$voList[$key]['current']=$current["nickname"];
				}
				if(false!==strstr($voList[$key]['worktype_status'],"节点变更待采购部审核"))
				{
					$current=$this->findProjectleader($val["id"],"采购");
					$voList[$key]['current']=$current["nickname"];
				}
			}
			$this->assign("classify", $classify);
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) 
			{
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
	public function draft()
	{
		$map[plmid]=$_REQUEST[id];
		$materials=M("gsmaterials")->where($map)->order("sort asc")->select();
		$materialcount=M("gsmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		$this->display();
	}
	
	public function draftsubmit(){
		
		$map[plmid]=$_REQUEST[plmid];
		M("gsmaterials")->where($map)->delete();
		
		$id=$_REQUEST[plmid];
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		$data1[plmid]=$_POST[plmid];
		$data1[ctime]=time();
		
		//dump($_POST[para2]);
		//return;
		if(!empty($id)){
			for($k=0;$k<count($_POST[para2]);$k++){
				$data1[brand]=$_POST[para1][$k];
				$mapforbrand[name]=$_POST[para1][$k];
				$data1[brandid]=M("brand")->where($mapforbrand)->getfield("id");
				$data1[number]=$_POST[para2][$k];
				$data1[name]=$_POST[para3][$k];
				$data1[standard]=$_POST[para4][$k];
				$data1[unit]=$_POST[para5][$k];
				$data1["count"]=$_POST[para6][$k];
				//$data1[price]=$_POST[para6][$k];
				$data1["sort"]=$k;
				$data1[plmid]=$id;
				$data1[plm]=$plminfo['title'];
				M("gsmaterials")->add($data1);
			}
		}
				
		$this->redirect('index','tab=5');
	}
		
		
	function add() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$vo['drawings']=explode(',',$vo['drawing']);
		$vo['drawingsfilename']=explode(',',$vo['drawingfilename']);
				
		$vo['illustrations']=explode(',',$vo['illustration']);
		$vo['clientillustrations']=explode(',',$vo['clientillustration']);
				
		$this->assign('orgdata', $vo);
		$this->display("addoa");
	}	
	
	function insert() {
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		
		$info['drawings']=explode(',',$info['drawing']);
		$info['drawingsfilename']=explode(',',$info['drawingfilename']);
				
		$info['illustrations']=explode(',',$info['illustration']);
		$info['clientillustrations']=explode(',',$info['clientillustration']);
		
		$info['drawing']="";
		$info['drawingfilename']="";
		$info['illustration']="";
		$info['clientillustration']="";
		
		
		for($i=0;$i<=20;$i++)
		{
			if((!empty($info['illustrations'][$i]))&&($_REQUEST['delx'.$i]!="on"))
			{
				$info['illustration'].=$info['illustrations'][$i].",";
				$info['clientillustration'].=$info['clientillustrations'][$i].",";
			}
			if((!empty($info['drawings'][$i]))&&($_REQUEST['dely'.$i]!="on"))
			{
				$info['drawing'].=$info['drawings'][$i].",";
				$info['drawingfilename'].=$info['drawingsfilename'][$i].",";
			}
			
			if((!empty($info['drawings3'][$i]))&&($_REQUEST['del3y'.$i]!="on"))
			{
				$info['drawing3'].=$info['drawings3'][$i].",";
				$info['drawingfilename3'].=$info['drawingsfilename3'][$i].",";
			}
			if((!empty($info['drawings4'][$i]))&&($_REQUEST['del4y'.$i]!="on"))
			{
				$info['drawing4'].=$info['drawings4'][$i].",";
				$info['drawingfilename4'].=$info['drawingsfilename4'][$i].",";
			}
			if((!empty($info['drawings5'][$i]))&&($_REQUEST['del5y'.$i]!="on"))
			{
				$info['drawing5'].=$info['drawings5'][$i].",";
				$info['drawingfilename5'].=$info['drawingsfilename5'][$i].",";
			}
			if((!empty($info['drawings6'][$i]))&&($_REQUEST['del6y'.$i]!="on"))
			{
				$info['drawing6'].=$info['drawings6'][$i].",";
				$info['drawingfilename6'].=$info['drawingsfilename6'][$i].",";
			}
			if((!empty($info['drawings7'][$i]))&&($_REQUEST['del7y'.$i]!="on"))
			{
				$info['drawing7'].=$info['drawings7'][$i].",";
				$info['drawingfilename7'].=$info['drawingsfilename7'][$i].",";
			}
			if((!empty($info['drawings8'][$i]))&&($_REQUEST['del8y'.$i]!="on"))
			{
				$info['drawing8'].=$info['drawings8'][$i].",";
				$info['drawingfilename8'].=$info['drawingsfilename8'][$i].",";
			}
			if((!empty($info['drawings9'][$i]))&&($_REQUEST['del9y'.$i]!="on"))
			{
				$info['drawing9'].=$info['drawings9'][$i].",";
				$info['drawingfilename9'].=$info['drawingsfilename9'][$i].",";
			}
			
		}
		
		$model->illustration=$info['illustration'];
		$model->clientillustration=$info['clientillustration'];
		$model->drawing=$info['drawing'];
		$model->drawingfilename=$info['drawingfilename'];
		$model->designmoney=$_REQUEST['designmoney'];
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$savePath = '../Public/Uploads/';     //设置附件上传目录		
		$x=0;
		if(!empty($_FILES['file1']['name'][0]))/*empty*/
		{
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
			$model->illustration=$info[illustration].$newnameall;
			$model->clientillustration=$info[clientillustration].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了设计要求</br>------------------</br>";
			$x++;
		}
		
		
		if($_FILES['file2']['name'][0])/*empty*/
		{
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
			$model->drawing=$info[drawing].$newnameall;
			$model->drawingfilename=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了设计方案</br>------------------</br>"; 
			$x++;
		}
		
		
		
		
		if($_FILES['file3']['name'][0])/*empty*/
		{
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
			$model->drawing3=$info[drawing].$newnameall;
			$model->drawingfilename3=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了施工图纸</br>------------------</br>"; 
			$x++;
		}
		
		
		if($_FILES['file4']['name'][0])/*empty*/
		{
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
			$model->drawing4=$info[drawing].$newnameall;
			$model->drawingfilename4=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了空调设计图纸</br>------------------</br>"; 
			$x++;
		}
		
		
		if($_FILES['file5']['name'][0])/*empty*/
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file5']['name'];
			$file_tmp=$_FILES['file5']['tmp_name'];
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
			$model->drawing5=$info[drawing].$newnameall;
			$model->drawingfilename5=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了水电设计图纸</br>------------------</br>"; 
			$x++;
		}
		
		
		if($_FILES['file6']['name'][0])/*empty*/
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file6']['name'];
			$file_tmp=$_FILES['file6']['tmp_name'];
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
			$model->drawing6=$info[drawing].$newnameall;
			$model->drawingfilename6=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了给排水设计图纸</br>------------------</br>"; 
			$x++;
		}
		
		
		if($_FILES['file7']['name'][0])/*empty*/
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file7']['name'];
			$file_tmp=$_FILES['file7']['tmp_name'];
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
			$model->drawing7=$info[drawing].$newnameall;
			$model->drawingfilename7=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了消防设计图纸</br>------------------</br>"; 
			$x++;
		}
		
		
		if($_FILES['file8']['name'][0])/*empty*/
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file8']['name'];
			$file_tmp=$_FILES['file8']['tmp_name'];
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
			$model->drawing8=$info[drawing].$newnameall;
			$model->drawingfilename8=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了监控点位设计图纸</br>------------------</br>"; 
			$x++;
		}
		
		
		if($_FILES['file9']['name'][0])/*empty*/
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file9']['name'];
			$file_tmp=$_FILES['file9']['tmp_name'];
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
			$model->drawing9=$info[drawing].$newnameall;
			$model->drawingfilename9=$info[drawingfilename].$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了效果图纸</br>------------------</br>"; 
			$x++;
		}
		
       
		$model->handlehistory=$handlehistory;
		$model->ysuser=$_SESSION['loginUserName'];
		
		$worktype=M("Plmworktype")->where("plmid=".$info[id])->order("id asc")->select();
		
		
		$list = $model->save();
		$info=M("Project")->where("id=".$info[id])->find();
		$x=0;
		if(!empty($info[illustration]))
		{
			$x++;
		}
		if(!empty($info[drawing]))
		{
			$x++;
		}
		
		if(($x==2))//(!empty($worktype))&&
		{
			
			//M("Project")->where("id=".$info[id])->setField("design_status","设计待审核");
			M("Project")->where("id=".$info[id])->setField("design_time",time());
			
			if($info["step6"]=="")
			{
				M("Project")->where("id=".$info[id])->setField("step6","0.1");
			}
			
			
			/*
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Ysgl";
			$scheduleexist=M("Schedule")->where($schedulemap)->getField("id");
			if(empty($scheduleexist))
			{	
				$data['content']=$_SESSION['loginUserName']."于".$date."创建了《".$address."》项目设计，请您审核。";
				$data['href'] ="index.php?s=Ysgl/index";
				$data['taskid'] =$info[id];
				$data['type'] ="Ysgl";
				$userschedule=$this->findleader($info['projecttype'],$info['city']);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			*/
		}
		
		
		
		
		
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功');
			//$this->redirect('index','moduletitle=项目设计管理');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function add1() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->display("add1");
	}	
	
	function insert1() {
		//B('FilterString');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
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
			$model->budget=$newnameall;
			$model->budgetfilename=$filenameall;
			$handlehistory.=$_SESSION['loginUserName']."于".$date."上传了设计方案报告</br>------------------</br>"; 
		}
		$model->handlehistory=$handlehistory;
		$model->ysuser=$_SESSION['loginUserName'];
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->success('操作成功');
			}
			else
			{
				$this->redirect('index');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function add2() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		if($_REQUEST["moduletitle"]=="主项节点设置")$mapforWorktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发节点设置")$mapforWorktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计节点设置")$mapforWorktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购节点设置")$mapforWorktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备采购节点")$mapforWorktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工节点设置")$mapforWorktype[classify]="施工专项节点库";
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		
		$mapforWorktype[type]=1;
		$mapforWorktype[projecttype]=$vo["projecttype"];
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		foreach ($worktypes as $key => $val) {
			$worktypes[$key][subworktypes]=M("Worktype")->where("pid=".$val[id])->order("sort asc")->select();
			foreach ($worktypes[$key][subworktypes] as $key1 => $val1) {
				$worktypes[$key][subworktypes][$key1]["title1"]=$val[title].$val1[title];
			}
		}
		$this->assign('worktypes', $worktypes);
		
		
		$mapforPlmworktype[plmid]=$vo[id];
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach ($vo['worktype'] as $key => $val) {
			$checkedworktype.=$val[pworktype].$val[title].",";
			$pcheckedworktype.=$val[pworktype].",";
		}
		$this->assign('checkedworktype', $checkedworktype);
		$this->assign('pcheckedworktype', $pcheckedworktype);
		$this->display("add2");
	}
	
	function insert2() {
		header('Content-Type:text/html;charset=UTF-8');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		
		if($_REQUEST["moduletitle"]=="主项节点设置"){$classify="主项";$worktype_status="worktype_status1";$set_node_time="set_node_time1";}
		if($_REQUEST["moduletitle"]=="开发节点设置"){$classify="开发";$worktype_status="worktype_status2";$set_node_time="set_node_time2";}
		if($_REQUEST["moduletitle"]=="设计节点设置"){$classify="设计";$worktype_status="worktype_status3";$set_node_time="set_node_time3";}
		if($_REQUEST["moduletitle"]=="采购节点设置"){$classify="采购";$worktype_status="worktype_status4";$set_node_time="set_node_time4";}
		if($_REQUEST["moduletitle"]=="设备采购节点"){$classify="采购";$worktype_status="worktype_status4";$set_node_time="set_node_time4";}
		if($_REQUEST["moduletitle"]=="施工节点设置"){$classify="施工";$worktype_status="worktype_status5";$set_node_time="set_node_time5";}
		
		
		$postdata=$_POST;
		$hasdata=0;
		foreach($postdata as $key => $val)
		{
			if(($key!="id")&&($key!="gz")&&($key!="moduletitle")&&($key!="ifsave"))
			{
				$hasdata=1;
			}
		}
		if($hasdata==0)
		{
			$this->error('请选择节点!');
		}
		
		if($_REQUEST[ifsave]!="1")
		{
			
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Ysgl";
			$schedulemap[classify]=$classify;
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			
			$handlehistory.=$_SESSION['loginUserName']."于".$date."设置".$classify."专项节点</br>------------------</br>"; 
			$model->handlehistory=$handlehistory;
			$model->ysuser=$_SESSION['loginUserName'];
			
			
			$model->$worktype_status="已设置节点";
			
			if($_REQUEST["moduletitle"]=="设备采购节点")
			{
				$model->$worktype_status=$classify."节点待工程部审核";
				$schedulemap[taskid]=$info[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Ysgl";
				$schedulemap[classify]=$classify;
				M("Schedule")->where($schedulemap)->setField("status",0);
				
				$data['content']=$_SESSION["name"]."于".$date."设置《".$info["title"]."》".$classify."节点，请您审核";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/6/";
				$data['taskid'] =$info[id];
				$data['type'] ="Ysgl";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectleader($info['id'],"施工");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			
			
			
			if($info["step6"]=="0.1")
			{
				M("Project")->where("id=".$info[id])->setField("step6","0.2");
			}
			if($info["design_status"]=="立项中")
			{
				M("Project")->where("id=".$info[id])->setField("design_status","施工中");
			}
			
			M("Project")->where("id=".$info[id])->setField($set_node_time,date("Y-m-d"));
			/*
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》设置".$classify."节点";
			$data['receiver']=$this->findProjectusers($info[id]);
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》设置".$classify."节点";
			$this->Sendmail($data);
			*/
			
			
			
			
			
		}
		
		$worktype=M("Plmworktype")->where("plmid=".$info[id])->order("id asc")->select();
		if(!empty($info[illustration])&&!empty($info[drawing]))
		{
			
			/*
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Ysgl";
			$scheduleexist=M("Schedule")->where($schedulemap)->getField("id");
			
			if(empty($scheduleexist))
			{	
				$data['content']=$_SESSION['loginUserName']."于".$date."创建了《".$address."》项目设计方案，请您审核。";
				$data['href'] ="index.php?s=Ysgl/index";
				$data['taskid'] =$info[id];
				$data['type'] ="Ysgl";
				$userschedule=$this->findleader($info['projecttype'],$info['city']);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			*/
		}
	
		$mapforPlmworktype["plmid"]=$_REQUEST["id"];
		if($_REQUEST["moduletitle"]=="主项节点设置")
		{
			$mapforPlmworktype[classify]="主项节点库";
		}
		else if($_REQUEST["moduletitle"]=="开发节点设置")
		{
			$mapforPlmworktype[classify]="开发专项节点库";
		}	
		else if($_REQUEST["moduletitle"]=="设计节点设置")
		{
			$mapforPlmworktype[classify]="设计专项节点库";
		}
		else if($_REQUEST["moduletitle"]=="采购节点设置")
		{
			$mapforPlmworktype[classify]="采购专项节点库";
		}
		else if($_REQUEST["moduletitle"]=="设备采购节点")
		{
			$mapforPlmworktype[classify]="采购专项节点库";
		}
		else if($_REQUEST["moduletitle"]=="施工节点设置")
		{
			$mapforPlmworktype[classify]="施工专项节点库";
		}
		else
		{
			$mapforPlmworktype[classify]="xxx";
		}
		
		M("Plmworktype")->where($mapforPlmworktype)->delete();
		
		$dataplmworktype["plmid"]=$_REQUEST["id"];
		
		foreach($postdata as $key => $val)
		{
			if(($key!="id")&&($key!="gz")&&($key!="moduletitle")&&($key!="ifsave"))
			{
				$dataplmworktype["title"]=urldecode($val);
				$worktypedetail=M("Worktype")->where("id=".$key)->find();
				$dataplmworktype["classify"]=$worktypedetail["classify"];
				$dataplmworktype["attribute"]=$worktypedetail["attribute"];
				$dataplmworktype["sort"]=$worktypedetail["sort"];
				$dataplmworktype["qualityunit"]=$worktypedetail["qualityunit"];
				$dataplmworktype["pid"]=$worktypedetail["pid"];
				$dataplmworktype["type"]=$worktypedetail["type"];
				$dataplmworktype["parallel"]=$worktypedetail["parallel"];
				$dataplmworktype["user_id"]=$_SESSION["number"];
				$dataplmworktype["create_time"]=time();
				$dataplmworktype["pworktype"]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
				M("Plmworktype")->add($dataplmworktype);
			}
		}
		
		$list = $model->save();
		
		
		if($_REQUEST[ifsave]!="1")
		{
			$this->success('设置成功!');
		}
		else
		{
			$this->success('保存成功!');
		}
	}
	
	function add3() {
		
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		
		if($_REQUEST["moduletitle"]=="主项节点设置")$mapforWorktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发节点设置")$mapforWorktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计节点设置")$mapforWorktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购节点设置")$mapforWorktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备采购节点")$mapforWorktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工节点设置")$mapforWorktype[classify]="施工专项节点库";
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		
		
		$mapforWorktype[projecttype]=$vo["projecttype"];
		$mapforWorktype[type]=1;
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		foreach ($worktypes as $key => $val) {
			$worktypes[$key][subworktypes]=M("Worktype")->where("pid=".$val[id])->order("sort asc")->select();
			foreach ($worktypes[$key][subworktypes] as $key1 => $val1) {
				$worktypes[$key][subworktypes][$key1]["title1"]=$val[title].$val1[title];
			}
		}
		$this->assign('worktypes', $worktypes);
		
		
		$mapforPlmworktype[plmid]=$vo[id];
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach ($vo['worktype'] as $key => $val) {
			$checkedworktype.=$val[pworktype].$val[title].",";
			$pcheckedworktype.=$val[pworktype].",";
		}
		$this->assign('checkedworktype', $checkedworktype);
		$this->assign('pcheckedworktype', $pcheckedworktype);
		
		$this->display("add3");
	}
	
	function insert3() {
		
		
		header('Content-Type:text/html;charset=UTF-8');
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		
		if($_REQUEST["moduletitle"]=="主项节点设置"){$classify="主项";$worktype_status="worktype_status1";}
		if($_REQUEST["moduletitle"]=="开发节点设置"){$classify="开发";$worktype_status="worktype_status2";}
		if($_REQUEST["moduletitle"]=="设计节点设置"){$classify="设计";$worktype_status="worktype_status3";}
		if($_REQUEST["moduletitle"]=="采购节点设置"){$classify="采购";$worktype_status="worktype_status4";}
		if($_REQUEST["moduletitle"]=="设备采购节点"){$classify="采购";$worktype_status="worktype_status4";}
		if($_REQUEST["moduletitle"]=="施工节点设置"){$classify="施工";$worktype_status="worktype_status5";}
		
		$postdata=$_POST;
		$hasdata=0;
		foreach($postdata as $key => $val)
		{
			if(($key!="id")&&($key!="gz")&&($key!="moduletitle")&&($key!="ifsave"))
			{
				$hasdata=1;
			}
		}
		if($hasdata==0)
		{
			$this->error('请选择节点!');
		}
		
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		$handlehistory.=$_SESSION['loginUserName']."于".$date."变更".$classify."专项节点</br>------------------</br>"; 
		$model->handlehistory=$handlehistory;
		$model->ysuser=$_SESSION['loginUserName'];
		
		$worktype=M("Plmworktype")->where("plmid=".$info[id])->order("id asc")->select();
		if(1)//!empty($info[illustration])&&!empty($info[drawing])
		{
			$model->$worktype_status=$classify."节点变更待审核";
			$model->worktype_status="节点变更待审核";
			
			
			if($_REQUEST["moduletitle"]=="设备采购节点")
			{
				$model->$worktype_status=$classify."节点变更待工程部审核";
			}
			
			if($_REQUEST["moduletitle"]=="主项节点设置")$classify="主项";
			if($_REQUEST["moduletitle"]=="开发节点设置")$classify="开发";
			if($_REQUEST["moduletitle"]=="设计节点设置")$classify="设计";
			if($_REQUEST["moduletitle"]=="采购节点设置")$classify="采购";
			if($_REQUEST["moduletitle"]=="设备采购节点")$classify="采购";
			if($_REQUEST["moduletitle"]=="施工节点设置")$classify="施工";
			
			/*
			$datamail['content']=$_SESSION["name"]."于".$date."变更《".$info["title"]."》".$classify."节点，请您审核";
			$datamail['receiver']=$this->findProjectleader();
			$datamail['sender']="系统通知";
			$datamail['title'] =$_SESSION["name"]."于".$date."变更《".$info["title"]."》".$classify."节点，请您审核";
			$this->Sendmail($datamail);
			*/
			
			
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Ysgl";
			$schedulemap[classify]=$classify;
			M("Schedule")->where($schedulemap)->setField("status",0);
			
			
			if($_REQUEST["moduletitle"]=="设备采购节点")
			{
				$data['content']=$_SESSION["name"]."于".$date."变更《".$info["title"]."》".$classify."节点，请您审核";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/6/";
				$data['taskid'] =$info[id];
				$data['type'] ="Ysgl";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectleader($info['id'],"施工");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			else
			{
				$data['content']=$_SESSION["name"]."于".$date."变更《".$info["title"]."》".$classify."节点，请您审核";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/6/";
				$data['taskid'] =$info[id];
				$data['type'] ="Ysgl";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectleader($info['id'],$classify);
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
		
			
		}
		$mapforPlmworktype["plmid"]=$_REQUEST["id"];
		if($_REQUEST["moduletitle"]=="主项节点设置")
			$mapforPlmworktype[classify]="主项节点库";
		else if($_REQUEST["moduletitle"]=="开发节点设置")
			$mapforPlmworktype[classify]="开发专项节点库";
		else if($_REQUEST["moduletitle"]=="设计节点设置")
			$mapforPlmworktype[classify]="设计专项节点库";
		else if($_REQUEST["moduletitle"]=="采购节点设置")
			$mapforPlmworktype[classify]="采购专项节点库";
		else if($_REQUEST["moduletitle"]=="设备采购节点")
			$mapforPlmworktype[classify]="采购专项节点库";
		else if($_REQUEST["moduletitle"]=="施工节点设置")
			$mapforPlmworktype[classify]="施工专项节点库";
		else
			$mapforPlmworktype[classify]="xxx";
		M("Plmworktypetemp")->where($mapforPlmworktype)->delete();
		
		
		$dataplmworktype["plmid"]=$_REQUEST["id"];
		$postdata=$_POST;
		foreach($postdata as $key => $val)
		{
			if(($key!="id")&&($key!="gz")&&($key!="moduletitle")&&($key!="ifsave"))
			{
				$dataplmworktype["title"]=urldecode($val);
				$worktypedetail=M("Worktype")->where("id=".$key)->find();
				$dataplmworktype["classify"]=$worktypedetail["classify"];
				$dataplmworktype["attribute"]=$worktypedetail["attribute"];
				$dataplmworktype["sort"]=$worktypedetail["sort"];
				$dataplmworktype["qualityunit"]=$worktypedetail["qualityunit"];
				$dataplmworktype["pid"]=$worktypedetail["pid"];
				$dataplmworktype["type"]=$worktypedetail["type"];
				$dataplmworktype["parallel"]=$worktypedetail["parallel"];
				$dataplmworktype["user_id"]=$_SESSION["number"];
				$dataplmworktype["create_time"]=time();
				$dataplmworktype["pworktype"]=M("Worktype")->where("id=".$worktypedetail[pid])->getField("title");
				M("Plmworktypetemp")->add($dataplmworktype);
			}
		}
		
		$list = $model->save();
		
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->success('操作成功');
			}
			else
			{
				$this->redirect('index','tab=3');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	function detail2() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		/*
		$mapforPlmschedule[plmid]=$vo[id];
		$mapforPlmschedule[status]=1;
		$vo['worktype']=M("Plmschedule")->where($mapforPlmschedule)->group("worktype")->order("sort asc")->select();
		foreach ($vo['worktype'] as $key => $val) {
			$mapforPlmschedule[worktype]=$val[worktype];
			$mapforPlmschedule[plmid]=$vo[id];
			$mapforPlmschedule[status]=1;
			$vo['worktype'][$key][subworktypes]=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			$mapforPlmschedule1[worktype]=$val[worktype];
			$mapforPlmschedule1[plmid]=$vo[id];
			$mapforPlmschedule1[status]=1;
			$mapforPlmschedule1[plantimebegin]=array("neq","");
			$vo['worktype'][$key][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule1)->min("plantimebegin");
			$vo['worktype'][$key][plantimeend]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
		}
		
		*/
		$mapforPlmworktype[plmid]=$vo[id];
		if($_REQUEST["moduletitle"]=="主项节点设置")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发节点设置")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计节点设置")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购节点设置")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备采购节点")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工节点设置")$mapforPlmworktype[classify]="施工专项节点库";
		
		$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
		}
		$this->assign('orgdata', $vo);
		
		$this->display("detail2");
	}	
	function detail3() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		if($_REQUEST["moduletitle"]=="主项节点设置")$mapforPlmworktype[classify]="主项节点库";
		if($_REQUEST["moduletitle"]=="开发节点设置")$mapforPlmworktype[classify]="开发专项节点库";
		if($_REQUEST["moduletitle"]=="设计节点设置")$mapforPlmworktype[classify]="设计专项节点库";
		if($_REQUEST["moduletitle"]=="采购节点设置")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="设备采购节点")$mapforPlmworktype[classify]="采购专项节点库";
		if($_REQUEST["moduletitle"]=="施工节点设置")$mapforPlmworktype[classify]="施工专项节点库";
		$vo['worktype']=M("Plmworktypetemp")->where($mapforPlmworktype)->order("id asc")->select();
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktypetemp")->where($mapforPlmworktype)->count();
			}
		}
		$this->assign('orgdata', $vo);
		
		$this->display("detail2");
	}	
	
	function approve3() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		
	
		$mapforWorktype[type]=1;
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		foreach ($worktypes as $key => $val) {
			$worktypes[$key][subworktypes]=M("Worktype")->where("pid=".$val[id])->order("sort asc")->select();
		}
		$this->assign('worktypes', $worktypes);
		$this->display();
	}
	
	function approvesubmit3() {
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Ysgl";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if($_REQUEST[approvestatus]=="1")
		{
			$model->design_status="设计审核通过";
			$model->design_approve_time=time();
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核设计方案，审核结果：通过"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行设计方案审核，结果：同意。";
			$data['receiver']=$info['ysuser'].$this->findNumberByNameAndRole($info['ysuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行设计方案审核，结果：同意。";
			$this->Sendmail($data);
		}
		else
		{
			$model->design_status="设计审核退回";
			$model->design_approve_time=time();
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核设计方案，审核结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行设计方案审核，结果：退回。";
			$data['receiver']=$info['ysuser'].$this->findNumberByNameAndRole($info['ysuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行设计方案审核，结果：退回。";
			$this->Sendmail($data);
			
		}
		$model->handlehistory=$handlehistory;
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->redirect('../App/detail',array('id'=>$_REQUEST[id],'webid'=>"programlist4"));
			}
			else
			{
				$this->redirect('index',array('moduletitle'=>'项目设计管理','tab'=>"4"));
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function approve2() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->display();
	}
	
	function approvesubmit2() {
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		
		
		if($_REQUEST["moduletitle"]=="主项节点设置")$classify="主项";
		if($_REQUEST["moduletitle"]=="开发节点设置")$classify="开发";
		if($_REQUEST["moduletitle"]=="设计节点设置")$classify="设计";
		if($_REQUEST["moduletitle"]=="采购节点设置")$classify="采购";
		if($_REQUEST["moduletitle"]=="设备采购节点")$classify="采购";
		if($_REQUEST["moduletitle"]=="施工节点设置")$classify="施工";
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Ysgl";
		$schedulemap[classify]=$classify;
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		
		if($_REQUEST["moduletitle"]=="主项节点设置"){$classify="主项";$worktype_status="worktype_status1";}
		if($_REQUEST["moduletitle"]=="开发节点设置"){$classify="开发";$worktype_status="worktype_status2";}
		if($_REQUEST["moduletitle"]=="设计节点设置"){$classify="设计";$worktype_status="worktype_status3";}
		if($_REQUEST["moduletitle"]=="采购节点设置"){$classify="采购";$worktype_status="worktype_status4";}
		if($_REQUEST["moduletitle"]=="设备采购节点"){$classify="采购";$worktype_status="worktype_status4";}
		if($_REQUEST["moduletitle"]=="施工节点设置"){$classify="施工";$worktype_status="worktype_status5";}
		
		if($_REQUEST[approvestatus]=="1")
		{
			if(false!==strstr($info[$worktype_status],"节点待工程部审核"))
			{
				$model->$worktype_status=$classify."节点待采购部审核";
				
				
				$data['content']=$_SESSION["name"]."于".$date."设置《".$info["title"]."》".$classify."节点，请您审核";
				$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/6/";
				$data['taskid'] =$info[id];
				$data['type'] ="Ysgl";
				$data['classify'] =$classify;
				$userschedule=$this->findProjectleader($info['id'],"采购");
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
			if(false!==strstr($info[$worktype_status],"节点待采购部审核"))
			{
				$model->$worktype_status=$classify."节点审核通过";
			}
			
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核".$classify."专项节点，审核结果：通过"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点审核，结果：同意。";
			$data['receiver']=$this->findProjectusers($info[id],"施工");
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点审核，结果：同意。";
			$this->Sendmail($data);
			
			
		}
		else
		{
			$model->$worktype_status=$classify."节点审核退回";
			$model->worktype_status="节点审核退回";
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核".$classify."专项节点，审核结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			/*
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点变更审核，结果：退回。";
			$data['receiver']=$this->findProjectusers($info[id]);
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点变更审核，结果：退回。";
			$this->Sendmail($data);
			*/
			
			$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."节点审核，请您修改后提交。";
			$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/";
			$data['taskid'] =$info[id];
			$data['type'] ="Ysgl";
			$data['classify'] =$classify;
			$userschedule=$this->findProjectuser($info['id'],"施工");
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
			
		}
		$model->handlehistory=$handlehistory;
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->success('操作成功');
			}
			else
			{
				$this->success('操作成功');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	function approve4() {
		//$_SESSION[app]=$_REQUEST[app];
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->display();
	}
	
	function approvesubmit4() {
		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$address=$info[title];
		$handlehistory=$info['handlehistory'];
		$date=date('Y-m-d H:i:s');
		
		
		if($_REQUEST["moduletitle"]=="主项节点设置")$classify="主项";
		if($_REQUEST["moduletitle"]=="开发节点设置")$classify="开发";
		if($_REQUEST["moduletitle"]=="设计节点设置")$classify="设计";
		if($_REQUEST["moduletitle"]=="采购节点设置")$classify="采购";
		if($_REQUEST["moduletitle"]=="设备采购节点")$classify="采购";
		if($_REQUEST["moduletitle"]=="施工节点设置")$classify="施工";
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Ysgl";
		$schedulemap[classify]=$classify;
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		
		if($_REQUEST["moduletitle"]=="主项节点设置"){$classify="主项";$worktype_status="worktype_status1";}
		if($_REQUEST["moduletitle"]=="开发节点设置"){$classify="开发";$worktype_status="worktype_status2";}
		if($_REQUEST["moduletitle"]=="设计节点设置"){$classify="设计";$worktype_status="worktype_status3";}
		if($_REQUEST["moduletitle"]=="采购节点设置"){$classify="采购";$worktype_status="worktype_status4";}
		if($_REQUEST["moduletitle"]=="设备采购节点"){$classify="采购";$worktype_status="worktype_status4";}
		if($_REQUEST["moduletitle"]=="施工节点设置"){$classify="施工";$worktype_status="worktype_status5";}
		
		if($_REQUEST[approvestatus]=="1")
		{

			$model->$worktype_status=$classify."节点变更审核通过";
			$model->worktype_status="节点变更审核通过";
			
			if($_REQUEST["moduletitle"]=="设备采购节点")
			{
				if(false!==strstr($info[$worktype_status],"节点变更待工程部审核"))
				{
					$model->$worktype_status=$classify."节点变更待采购部审核";
					
					
					$data['content']=$_SESSION["name"]."于".$date."变更《".$info["title"]."》".$classify."节点，请您审核";
					$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/tab/6/";
					$data['taskid'] =$info[id];
					$data['type'] ="Ysgl";
					$data['classify'] =$classify;
					$userschedule=$this->findProjectleader($info['id'],"采购");
					$data['user']=$userschedule['nickname'].$userschedule['number'];
					$this->Addschedule($data);
				}
				
				if(false!==strstr($info[$worktype_status],"节点变更待采购部审核"))
				{
					$model->$worktype_status=$classify."节点变更审核通过";
				}
			}
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核".$classify."专项节点变更，审核结果：通过"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点变更审核，结果：同意。";
			$data['receiver']=$this->findProjectusers($info[id],$classify);
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点变更审核，结果：同意。";
			$this->Sendmail($data);
			
			
			if(false===strstr($info[$worktype_status],"节点变更待工程部审核"))
			{
				//审核变更
				$mapforPlmworktype[plmid]=$info[id];
				if($_REQUEST["moduletitle"]=="主项节点设置")
				{
					$mapforPlmworktype[classify]="主项节点库";
				}
				else if($_REQUEST["moduletitle"]=="开发节点设置")
				{
					$mapforPlmworktype[classify]="开发专项节点库";
				}
				else if($_REQUEST["moduletitle"]=="设计节点设置")
				{
					$mapforPlmworktype[classify]="设计专项节点库";
				}
				else if($_REQUEST["moduletitle"]=="采购节点设置")
				{
					$mapforPlmworktype[classify]="采购专项节点库";
				}
				else if($_REQUEST["moduletitle"]=="设备采购节点")
				{
					$mapforPlmworktype[classify]="采购专项节点库";
				}
				else if($_REQUEST["moduletitle"]=="施工节点设置")
				{
					$mapforPlmworktype[classify]="施工专项节点库";
				}
				else
				{
					$mapforPlmworktype[classify]="xxx";
				}
				$worktypetemps=M("Plmworktypetemp")->where($mapforPlmworktype)->order("id asc")->select();
				M("Plmworktype")->where($mapforPlmworktype)->delete();
				$dataplmworktype["plmid"]=$info[id];
				foreach($worktypetemps as $key => $val)
				{
					$dataplmworktype["classify"]=$val["classify"];
					$dataplmworktype["attribute"]=$val["attribute"];
					$dataplmworktype["title"]=$val[title];
					$dataplmworktype["sort"]=$val["sort"];
					$dataplmworktype["qualityunit"]=$val["qualityunit"];
					$dataplmworktype["pid"]=$val["pid"];
					$dataplmworktype["type"]=$val["type"];
					$dataplmworktype["parallel"]=$val["parallel"];
					$dataplmworktype["user_id"]=$val["user_id"];
					$dataplmworktype["create_time"]=$val["create_time"];
					$dataplmworktype["pworktype"]=$val["pworktype"];
					M("Plmworktype")->add($dataplmworktype);
				}
				$worktypetemps=M("Plmworktypetemp")->where($mapforPlmworktype)->delete();
			
			}
			
			
		}
		else
		{
			$model->$worktype_status=$classify."节点变更审核退回";
			$model->worktype_status="节点变更审核退回";
			$handlehistory.=$_SESSION['loginUserName']."于".$date."审核".$classify."专项节点变更，审核结果：退回"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			
			/*
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点变更审核，结果：退回。";
			$data['receiver']=$this->findProjectusers($info[id]);
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行".$classify."节点变更审核，结果：退回。";
			$this->Sendmail($data);
			*/
			
			$data['content']=$_SESSION['loginUserName']."于".$date."退回《".$info['title']."》".$classify."节点变更审核，请您修改后提交。";
			$data['href'] ="index.php?s=Ysgl/index/moduletitle/".$_REQUEST["moduletitle"]."/";
			$data['taskid'] =$info[id];
			$data['type'] ="Ysgl";
			$data['classify'] =$classify;
			$userschedule=$this->findProjectuser($info['id'],$classify);
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
			
		}
		$model->handlehistory=$handlehistory;
		
		$list = $model->save();
		if ($list !== false) { //保存成功
			if($_SESSION[app])
			{
				$this->success('操作成功');
			}
			else
			{
				$this->success('操作成功');
			}
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Project";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            if(!empty($_REQUEST [$pk]))
            {
            	$id = $_REQUEST [$pk];
            }
            else
            {
            	$id = $_REQUEST ["ids"];
            }
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->setField("design_status","设计审核通过"))
				{
                    $this->success('撤销成功！');
                } else {
                    $this->error('撤销失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
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
			for($k=2;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
					$mapforBrand["name"]=$array[$k]['1'];
					$newdata[$k-1]['0']=M("Brand")->where($mapforBrand)->getField("id");;
					$newdata[$k-1]['1']=$array[$k]['1'];
					$newdata[$k-1]['2']=$array[$k]['2'];
					$newdata[$k-1]['3']=$array[$k]['3'];
					$newdata[$k-1]['4']=$array[$k]['4'];
					$newdata[$k-1]['5']=$array[$k]['5'];
					$newdata[$k-1]['6']=$array[$k]['6'];
				}					
			}
			
			$this->success(json_encode($newdata),"1","1");
		}
		else
		{
			$this->error('上传的文件类型非法!');
		}
	}
}
?>