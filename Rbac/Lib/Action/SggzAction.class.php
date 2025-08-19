<?php
class SggzAction extends CommonAction {
	public function index() {
		
		
		/*
		$mapforWorktype["type"]=2;
		$mapforWorktype["classify"]="采购专项节点库";
		$allworktypes=M("Worktype")->where($mapforWorktype)->select();
		foreach ($allworktypes as $key => $val) 
		{
			M("Worktype")->where("id=".$val["id"])->setField("parallel","是");
		}
		*/
		
		$map["classify"]=$_REQUEST["moduletitle"];
		$this->assign('moduletitle',$_REQUEST['moduletitle']);
		
		if(empty($_REQUEST['tab']))
		{
			$_REQUEST[tab]=1;
		}
        if(!empty($_REQUEST['tab']))
		{
			$_SESSION[tab]=$_REQUEST['tab'];
		}
		$this->assign('tab',$_SESSION['tab']);
		if(!empty($_REQUEST['title']))
		{
			$map['title'] = array("like","%".$_REQUEST['title']."%");
			$this->assign('title',$_REQUEST['title']);	
		}
		
		if($_REQUEST["projecttype"])
		{
			$map['projecttype'] = array("like","%".$_REQUEST['projecttype']."%");
			$this->assign('projecttype',$_REQUEST['projecttype']);
			$_SESSION["projecttype_temp"]=$_REQUEST['projecttype'];
		}
		else if($_SESSION["projecttype_temp"])
		{
			$map['projecttype'] = array("in",$_SESSION["projecttype_temp"]);
			$this->assign('projecttype',$_SESSION["projecttype_temp"]);	
		}
		else
		{
			$map['projecttype'] = array("like","%分布式光伏发电%");
			$this->assign('projecttype',"分布式光伏发电");	
			$_SESSION["projecttype_temp"]="分布式光伏发电";
		}
		
		$name = "Worktype";
		$model = D($name);
		
		if($_SESSION['tab']==1)
		{
			$map[type]="1";
			$voList = $model->where($map)->order("sort asc")->select();
			
			$settingcapacitys=M("Settingcapacity")->select();
			$this->assign('settingcapacitys', $settingcapacitys);
			
			foreach ($voList as $key => $val) 
			{
				$voList[$key]["settingcapacitys"]=$settingcapacitys;
				foreach ($voList[$key]["settingcapacitys"] as $key1 => $val1) 
				{
					$mapforWorktypeperiod["pid"]=$val["id"];
					$mapforWorktypeperiod["begin"]=$val1["begin"];
					$mapforWorktypeperiod["end"]=$val1["end"];
					$voList[$key]["settingcapacitys"][$key1]["period"]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
				}
				
			}
		}
		else if($_SESSION['tab']==2)
		{
			$settingcapacitys=M("Settingcapacity")->select();
			$this->assign('settingcapacitys', $settingcapacitys);
			
			$map[type]="1";
			$voList = $model->where($map)->order("sort asc")->select();
			foreach ($voList as $key => $val) {
				$voList[$key][subs]=M("Worktype")->where("pid=".$val[id])->order("sort asc")->select();
				/*
				if($voList[$key][parenttitle]!=$voList[$key-1][parenttitle])
				{
					$voList[$key][block]=1;
					$voList[$key][rowspan]=M("Worktype")->where("pid=".$val[pid])->count();
				}
				*/
				
				foreach ($voList[$key][subs] as $key1 => $val1) 
				{
					$voList[$key][subs][$key1]["settingcapacitys"]=$settingcapacitys;
					foreach ($voList[$key][subs][$key1]["settingcapacitys"] as $key2 => $val2) 
					{
						$mapforWorktypeperiod["pid"]=$val1["id"];
						$mapforWorktypeperiod["begin"]=$val2["begin"];
						$mapforWorktypeperiod["end"]=$val2["end"];
						$voList[$key][subs][$key1]["settingcapacitys"][$key2]["period"]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
					}
					$voList[$key][subs][$key1]["dependence"]=str_replace("</br>","\n",$voList[$key][subs][$key1]["dependence"]);
					$voList[$key][subs][$key1]["autocomplete"]=str_replace("</br>","\n",$voList[$key][subs][$key1]["autocomplete"]);
				}
				
			}
			
			
		}
		else
		{
			$map[type]="2";
			$voList = $model->where($map)->order("pid asc")->select();
			foreach ($voList as $key => $val) {
				$voList[$key][parenttitle]=M("Worktype")->where("id=".$val[pid])->getField("title");
				if($voList[$key][parenttitle]!=$voList[$key-1][parenttitle])
				{
					$voList[$key][block]=1;
					$voList[$key][rowspan]=M("Worktype")->where("pid=".$val[pid])->count();
					$voList[$key][percentall]=M("Worktype")->where("pid=".$val[pid])->sum("percent");
				}
			}
		}
		
		$map[type]="1";
		$sggzs = $model->where($map)->order("sort asc")->select();
		$this->assign('list', $voList);
		$this->display();
	}
	
	
	function insert() {
		$name = "Worktype";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->user_id=$_SESSION['loginUserName'];
		$model->create_time=time();
		
		
		$mapforWorktype["id"]=array("in",$_REQUEST["dependenceid"]);
		$dependencearray=M("Worktype")->where($mapforWorktype)->select();
		foreach($dependencearray as $key => $val)
		{
			$mapforWorktype1["id"]=$val["pid"];
			$parentinfo=M("Worktype")->where($mapforWorktype1)->find();
			$dependence.=$val["classify"]."-".$parentinfo["title"]."-".$val["title"]."</br>";
		}
		$model->dependence=$dependence;
		
		
		$mapforWorktype["id"]=array("in",$_REQUEST["autocompleteid"]);
		$autocompletearray=M("Worktype")->where($mapforWorktype)->select();
		foreach($autocompletearray as $key => $val)
		{
			$mapforWorktype1["id"]=$val["pid"];
			$parentinfo=M("Worktype")->where($mapforWorktype1)->find();
			$autocomplete.=$val["classify"]."-".$parentinfo["title"]."-".$val["title"]."</br>";
		}
		$model->autocomplete=$autocomplete;
		
		$list = $model->add();
		
		
		$info=M("Worktype")->where("id=".$list)->find();
		/*
		if($info["type"]=="1")
		{
			$mapforRepeat["id"]=array("neq",$info["id"]);
			$mapforRepeat["sort"]=array("eq",$info["sort"]);
			$mapforRepeat["type"]=array("eq","1");
			$repeatinfo=M("Worktype")->where($mapforRepeat)->find();
			if(!empty($repeatinfo))
			{
				$mapforRepeat["sort"]=array("egt",$info["sort"]);
				$wortypearray=M("Worktype")->where($mapforRepeat)->field("id")->select();
				foreach($wortypearray as $ky => $val)
				{
					$ids.=$val["id"].",";
				}
				$mapforRepeat["id"]=array("in",$ids);
				M("Worktype")->where($mapforRepeat)->setInc("sort");
			}
		}
		else
		{
			$mapforRepeat["id"]=array("neq",$info["id"]);
			$mapforRepeat["sort"]=array("eq",$info["sort"]);
			$mapforRepeat["type"]=array("eq","2");
			$mapforRepeat["pid"]=array("eq",$info["pid"]);
			$repeatinfo=M("Worktype")->where($mapforRepeat)->find();
			if(!empty($repeatinfo))
			{
				$mapforRepeat["sort"]=array("egt",$info["sort"]);
				$wortypearray=M("Worktype")->where($mapforRepeat)->field("id")->select();
				foreach($wortypearray as $ky => $val)
				{
					$ids.=$val["id"].",";
				}
				$mapforRepeat["id"]=array("in",$ids);
				M("Worktype")->where($mapforRepeat)->setInc("sort");
			}
		}
		*/
		
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->redirect('index',array('tab' => $_REQUEST["tab"]));
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	function insert1() {
		M("Worktype")->where("id=".$_REQUEST[id])->setField("percent",$_REQUEST[percent]);
		$this->redirect('index');
	}
	
	function edit() 
	{
        $name = "Worktype";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
		
		$mapforWorktype["projecttype"]=$vo["projecttype"];
		
		
		$mapforWorktype["type"]=1;
		$modules=M("Worktype")->where($mapforWorktype)->group("classify")->select();
		
		foreach($modules as $key => $val)
		{
			$mapforWorktype["classify"]=$val["classify"];
			$modules[$key]["nodes"]=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		}
			
		$this->assign('modules', $modules);
	
        if($_SESSION[skin]!=3)
        {
        	$this->display(editoa);
        }
        else
        {
        $this->display();
        }
    }
	function editperiod() 
	{
        $name = "Worktype";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
		
	
		$settingcapacitys=M("Settingcapacity")->select();
		foreach($settingcapacitys as $key => $val)
		{
			$mapforWorktypeperiod["pid"]=$vo["id"];
			$mapforWorktypeperiod["begin"]=$val["begin"];
			$mapforWorktypeperiod["end"]=$val["end"];
			$settingcapacitys[$key]["period"]=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("period");
		}
		$this->assign('settingcapacitys', $settingcapacitys);
	
       
        $this->display();
    }
	
	function periodupdate() 
	{
		$worktypeinfo=M("Worktype")->where("id=".$_REQUEST["id"])->find();
        $name = "Worktypeperiod";
        $model = D($name);
        $settingcapacitys=M("Settingcapacity")->select();
		$periodarray=$_REQUEST["period"];
		foreach($settingcapacitys as $key => $val)
		{
			$data["pid"]=$_REQUEST["id"];
			
			if($worktypeinfo["type"]=="1")
			{
				$data["worktype"]=$worktypeinfo["title"];
			}
			else
			{
				$data["worktype"]=M("Worktype")->where("id=".$worktypeinfo["pid"])->getField("title");
				$data["subworktype"]=$worktypeinfo["title"];
			}
			
			$data["begin"]=$val["begin"];
			$data["end"]=$val["end"];
			$data["period"]=$periodarray[$key];
			
			
			$mapforWorktypeperiod["pid"]=$_REQUEST["id"];
			$mapforWorktypeperiod["begin"]=$val["begin"];
			$mapforWorktypeperiod["end"]=$val["end"];
			$ifrepeat=M("Worktypeperiod")->where($mapforWorktypeperiod)->getField("id");
			if($ifrepeat)
			{
				M("Worktypeperiod")->where("id=".$ifrepeat)->save($data);
			}
			else
			{
				M("Worktypeperiod")->add($data);
			}
			
		}
		
		
        $this->success('操作成功');
    }
	
	function edit1() 
	{
		$mapforWorktype[type]=1;
		if($_SESSION["projecttype_temp"])
		{
			$mapforWorktype['projecttype'] = array("in",$_SESSION["projecttype_temp"]);
		}
		
		$mapforWorktype[projecttype]=$_REQUEST["projecttype"];
		$mapforWorktype[classify]=$_REQUEST["moduletitle"];
		$worktypes=M("Worktype")->where($mapforWorktype)->select();
		$this->assign('worktypes', $worktypes);
		
        $name = "Worktype";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
		
		
		$mapforWorktype1['projecttype'] = array("in",$_SESSION["projecttype_temp"]);
		$mapforWorktype1["type"]=1;
		$modules=M("Worktype")->where($mapforWorktype1)->order("classify")->select();
		foreach($modules as $key => $val)
		{
			$mapforWorktype2["type"]=2;
			$mapforWorktype2["pid"]=$val["id"];
			$modules[$key]["nodes"]=M("Worktype")->where($mapforWorktype2)->order("sort asc")->select();
		}
			
		$this->assign('modules', $modules);
		
        $this->display();
    }
	function update() 
	{
        $name = "Worktype";
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
		
		$mapforWorktype["id"]=array("in",$_REQUEST["dependenceid"]);
		$dependencearray=M("Worktype")->where($mapforWorktype)->select();
		foreach($dependencearray as $key => $val)
		{
			$mapforWorktype1["id"]=$val["pid"];
			$parentinfo=M("Worktype")->where($mapforWorktype1)->find();
			$dependence.=$val["classify"]."-".$parentinfo["title"]."-".$val["title"]."</br>";
		}
		$model->dependence=$dependence;
		
		$mapforWorktype["id"]=array("in",$_REQUEST["autocompleteid"]);
		$autocompletearray=M("Worktype")->where($mapforWorktype)->select();
		foreach($autocompletearray as $key => $val)
		{
			$mapforWorktype1["id"]=$val["pid"];
			$parentinfo=M("Worktype")->where($mapforWorktype1)->find();
			$autocomplete.=$val["classify"]."-".$parentinfo["title"]."-".$val["title"]."</br>";
		}
		$model->autocomplete=$autocomplete;
		
        $list = $model->save();
		
		
		
		$info=M("Worktype")->where("id=".$_REQUEST["id"])->find();
		/*
		if($info["type"]=="1")
		{
			$mapforRepeat["id"]=array("neq",$info["id"]);
			$mapforRepeat["sort"]=array("eq",$info["sort"]);
			$mapforRepeat["type"]=array("eq","1");
			$repeatinfo=M("Worktype")->where($mapforRepeat)->find();
			if(!empty($repeatinfo))
			{
				$mapforRepeat["sort"]=array("egt",$info["sort"]);
				$wortypearray=M("Worktype")->where($mapforRepeat)->field("id")->select();
				foreach($wortypearray as $key => $val)
				{
					$ids.=$val["id"].",";
				}
				$mapforRepeat["id"]=array("in",$ids);
				M("Worktype")->where($mapforRepeat)->setInc("sort");
			}
		}
		else
		{
			$mapforRepeat["id"]=array("neq",$info["id"]);
			$mapforRepeat["sort"]=array("eq",$info["sort"]);
			$mapforRepeat["type"]=array("eq","2");
			$mapforRepeat["pid"]=array("eq",$info["pid"]);
			$repeatinfo=M("Worktype")->where($mapforRepeat)->find();
			if(!empty($repeatinfo))
			{
				$mapforRepeat["sort"]=array("egt",$info["sort"]);
				$wortypearray=M("Worktype")->where($mapforRepeat)->field("id")->select();
				foreach($wortypearray as $key => $val)
				{
					$ids.=$val["id"].",";
				}
				$mapforRepeat["id"]=array("in",$ids);
				M("Worktype")->where($mapforRepeat)->setInc("sort");
			}
		}
		*/
		
        if (false !== $list) {
            //成功提示
            $this->redirect('index',array('tab' => $_REQUEST["tab"]));
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
	
	public function add() 
	{
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		
		
		$mapforWorktype["type"]=1;
		$modules=M("Worktype")->where($mapforWorktype)->group("classify")->select();
		
		foreach($modules as $key => $val)
		{
			$mapforWorktype["classify"]=$val["classify"];
			$modules[$key]["nodes"]=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		}
			
		$this->assign('modules', $modules);
		
    	$this->display(addoa);
    }
	
	public function add1() 
	{
		/*
		$mapforWorktype[id]=$_REQUEST[id];
		$worktype=M("Worktype")->where($mapforWorktype)->find();
		$this->assign('worktype', $worktype);
    	$this->display();
		*/
		$mapforWorktype[type]=1;
		
		if($_SESSION["projecttype_temp"])
		{
			$mapforWorktype['projecttype'] = array("in",$_SESSION["projecttype_temp"]);
		}
		$mapforWorktype[classify]=$_REQUEST["moduletitle"];
		$worktypes=M("Worktype")->where($mapforWorktype)->order("sort asc")->select();
		$this->assign('worktypes', $worktypes);
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
		
		$mapforWorktype1['projecttype'] = array("in",$_SESSION["projecttype_temp"]);
		$mapforWorktype1["type"]=1;
		$modules=M("Worktype")->where($mapforWorktype1)->order("classify")->select();
		foreach($modules as $key => $val)
		{
			$mapforWorktype2["type"]=2;
			$mapforWorktype2["pid"]=$val["id"];
			$modules[$key]["nodes"]=M("Worktype")->where($mapforWorktype2)->order("sort asc")->select();
		}
			
		$this->assign('modules', $modules);
		
		
    	$this->display();
    }
	
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Worktype";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            //$id = $_REQUEST [$pk];
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
                if (false !== $model->where($condition)->delete())
				{
                    //echo $model->getlastsql();
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
	public function foreverdeleteall() {
        //删除指定记录
        $name = "Worktype";
        $model = D($name);
		$mapforWorktype["classify"]=$_REQUEST["moduletitle"];
		$allworktype=M("Worktype")->where($mapforWorktype)->field("id")->select();
		foreach($allworktype as $key => $val)
		{
			$worktypeids.=$val["id"].",";
		}
 
 
		$condition = array("id" => array('in', explode(',', $worktypeids)));
		if (false !== $model->where($condition)->delete())
		{
			$this->success('删除成功！');
		} else {
			$this->error('删除失败！');
		}
	

        $this->forward();
    }
	
	
	public function toexcel()
	{
		$model=M("Worktype");
		$moduletitle=$_REQUEST["moduletitle"];
		$mapforWorktype["classify"]=$_REQUEST["moduletitle"];
		$mapforWorktype["type"]=1;
		$projecttypes=M("Worktype")->where($mapforWorktype)->group("projecttype")->select();
		

		$i=0;
		foreach($projecttypes as $key => $val)
		{
			
			$data[$i][]=$val['projecttype'];
			$i++;
			
			$mapforWorktype1["classify"]=$_REQUEST["moduletitle"];
			$mapforWorktype1["projecttype"]=$val["projecttype"];
			$mapforWorktype1["type"]=1;
			$parentnodes=M("Worktype")->where($mapforWorktype1)->order("sort asc")->select();
			
			foreach($parentnodes as $key1 => $val1)
			{
				$data[$i][]="";
				$data[$i][]=$val1["title"];
				$data[$i][]=$val1["sort"];
				$i++;
			
				$mapforWorktype2["classify"]=$_REQUEST["moduletitle"];
				$mapforWorktype2["pid"]=$val1["id"];
				$mapforWorktype2["type"]=2;
				$childnodes=M("Worktype")->where($mapforWorktype2)->order("sort asc")->select();
				
				foreach($childnodes as $key2 => $val2)
				{
					$data[$i][]="";
					$data[$i][]="";
					$data[$i][]="";
					$data[$i][]=$val2["title"];
					$data[$i][]=$val2["attribute"];
					$data[$i][]=$val2["sort"];
					$data[$i][]=str_replace("</br>",",",$val2["dependence"]);
					$data[$i][]=str_replace("</br>",",",$val2["autocomplete"]);
					$data[$i][]=$val2["qualityunit"];
					
					
					$mapforWorktypeperiod["pid"]=array("eq",$val2["id"]);
					$worktypeperiods=M("Worktypeperiod")->where($mapforWorktypeperiod)->order("begin asc")->select();
					$periods="";
					if(!empty($worktypeperiods))
					{
						foreach($worktypeperiods as $key3 => $val3)
						{
							$periods.=$val3["period"].",";
						}
					}
			
					$data[$i][]=$periods;//$val2["period1"];
					
					
					$data[$i][]=$val2["parallel"];
					$i++;
				}
				
			}
		}
		
		
		$file=$_REQUEST["moduletitle"];
		$title=$_REQUEST["moduletitle"];
		$subtitle=$_REQUEST["moduletitle"];
		
		$th_array=array('项目类型','一级节点名称','一级节点序号','二级节点名称','二级节点属性','二级节点序号','依赖','自动完成','二级节点工程量单位','二级节点周期','是否并行（填是）');
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
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
	
	
	
	
	
	
	public function upload() 
	{
		$this->assign('moduletitle', $_REQUEST["moduletitle"]);
    	$this->display();
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

			
			
			for($k=4;$k<=$number;$k++)
			{		
				if($array[$k]['1']!="")
				{
					$projecttype=$array[$k]['1'];
				}
				else if($array[$k]['2']!="")
				{
					
				}
				else if($array[$k]['4']!="")
				{
					if($array[$k]['7']!="")
					{
						$datanew2["dependence"]=$array[$k]['7'];
						$dependencearray=explode(",",$array[$k]['7']);
						$datanew2["dependenceid"]="";
						foreach($dependencearray as $key => $val)
						{
							if(!empty($val))
							{
								$dependencedetail=explode("-",$val);
								$mapforWorktypefordependencepid["classify"]=$dependencedetail[0];
								$mapforWorktypefordependencepid["projecttype"]=$projecttype;
								$mapforWorktypefordependencepid["title"]=$dependencedetail[1];
								$mapforWorktypefordependencepid["type"]=1;
								
								$mapforWorktypefordependence["pid"]=M("Worktype")->where($mapforWorktypefordependencepid)->getField("id");
								$mapforWorktypefordependence["classify"]=$dependencedetail[0];
								$mapforWorktypefordependence["title"]=$dependencedetail[2];
								$mapforWorktypefordependence["type"]=2;
								$dependenceid=M("Worktype")->where($mapforWorktypefordependence)->getField("id");
								if(empty($dependenceid))
								{
									$this->error($projecttype."-".$dependencedetail[0]."-".$dependencedetail[1]."-".$dependencedetail[2]."依赖不存在");
								}
							}
							
						}
					}
					if($array[$k]['8']!="")
					{
						$datanew2["autocomplete"]=$array[$k]['8'];
						$autocompletearray=explode(",",$array[$k]['8']);
						$datanew2["autocompleteid"]="";
						foreach($autocompletearray as $key => $val)
						{
							if(!empty($val))
							{
								$autocompletedetail=explode("-",$val);
								$mapforWorktypeforautocompletepid["classify"]=$autocompletedetail[0];
								$mapforWorktypeforautocompletepid["projecttype"]=$projecttype;
								$mapforWorktypeforautocompletepid["title"]=$autocompletedetail[1];
								$mapforWorktypeforautocompletepid["type"]=1;
								
								$mapforWorktypeforautocomplete["pid"]=M("Worktype")->where($mapforWorktypeforautocompletepid)->getField("id");
								$mapforWorktypeforautocomplete["classify"]=$autocompletedetail[0];
								$mapforWorktypeforautocomplete["title"]=$autocompletedetail[2];
								$mapforWorktypeforautocomplete["type"]=2;
								$autocompleteid=M("Worktype")->where($mapforWorktypeforautocomplete)->getField("id");
								if(empty($autocompleteid))
								{
									$this->error($projecttype."-".$autocompletedetail[0]."-".$autocompletedetail[1]."-".$autocompletedetail[2]."自动完成不存在");
								}
							}
							
						}
					}
				}
			}
			
			for($k=4;$k<=$number;$k++)
			{		
				if($array[$k]['1']!="")
				{
					$projecttype=$array[$k]['1'];
				}
				else if($array[$k]['2']!="")
				{
					$mapforWorktype1["classify"]=$_REQUEST["moduletitle"];
					$mapforWorktype1["projecttype"]=$projecttype;
					$mapforWorktype1["title"]=$array[$k]['2'];
					$mapforWorktype1["type"]=1;
					$nodeinfo=M("Worktype")->where($mapforWorktype1)->find();
					if(empty($nodeinfo))
					{
						$datanew1["classify"]=$_REQUEST["moduletitle"];
						$datanew1["projecttype"]=$projecttype;
						$datanew1["title"]=$array[$k]['2'];
						$datanew1["sort"]=$array[$k]['3'];
						$datanew1["type"]=1;
						$pid=M("Worktype")->add($datanew1);
					}
					else
					{
						$datasave1["id"]=$nodeinfo['id'];
						$datasave1["sort"]=$array[$k]['3'];
						M("Worktype")->save($datasave1);
						$pid=$nodeinfo['id'];
					}
					
				}
				else if($array[$k]['4']!="")
				{
					$mapforWorktype2["classify"]=$_REQUEST["moduletitle"];
					$mapforWorktype2["title"]=$array[$k]['4'];
					$mapforWorktype2["type"]=2;
					$mapforWorktype2["pid"]=$pid;
					$nodeinfo=M("Worktype")->where($mapforWorktype2)->find();
					if(empty($nodeinfo))
					{
						$datanew2["classify"]=$_REQUEST["moduletitle"];
						$datanew2["title"]=$array[$k]['4'];
						$datanew2["attribute"]=$array[$k]['5'];
						$datanew2["sort"]=$array[$k]['6'];
						$datanew2["qualityunit"]=$array[$k]['9'];
						$datanew2["period1"]=$array[$k]['10'];
						$datanew2["parallel"]=$array[$k]['11'];
						$datanew2["type"]=2;
						$datanew2["pid"]=$pid;
						
						
					
						$dependencearray=explode(",",$array[$k]['7']);
						$datanew2["dependenceid"]="";
						$datanew2["dependence"]="";
						foreach($dependencearray as $key => $val)
						{
							if(!empty($val))
							{
								$dependencedetail=explode("-",$val);
								$mapforWorktypefordependencepid["classify"]=$dependencedetail[0];
								$mapforWorktypefordependencepid["projecttype"]=$projecttype;
								$mapforWorktypefordependencepid["title"]=$dependencedetail[1];
								$mapforWorktypefordependencepid["type"]=1;
								$mapforWorktypefordependence["pid"]=M("Worktype")->where($mapforWorktypefordependencepid)->getField("id");
								$mapforWorktypefordependence["classify"]=$dependencedetail[0];
								$mapforWorktypefordependence["title"]=$dependencedetail[2];
								$mapforWorktypefordependence["type"]=2;
								
								$dependenceid=M("Worktype")->where($mapforWorktypefordependence)->getField("id");
								$datanew2["dependenceid"].=$dependenceid.",";
								$datanew2["dependence"].=$val."</br>";
							}
						}
						
						$autocompletearray=explode(",",$array[$k]['8']);
						$datanew2["autocompleteid"]="";
						$datanew2["autocomplete"]="";
						foreach($autocompletearray as $key => $val)
						{
							if(!empty($val))
							{
								$autocompletedetail=explode("-",$val);
								$mapforWorktypeforautocompletepid["classify"]=$autocompletedetail[0];
								$mapforWorktypeforautocompletepid["projecttype"]=$projecttype;
								$mapforWorktypeforautocompletepid["title"]=$autocompletedetail[1];
								$mapforWorktypeforautocompletepid["type"]=1;
								$mapforWorktypeforautocomplete["pid"]=M("Worktype")->where($mapforWorktypeforautocompletepid)->getField("id");
								$mapforWorktypeforautocomplete["classify"]=$autocompletedetail[0];
								$mapforWorktypeforautocomplete["title"]=$autocompletedetail[2];
								$mapforWorktypeforautocomplete["type"]=2;
								
								$autocompleteid=M("Worktype")->where($mapforWorktypeforautocomplete)->getField("id");
								$datanew2["autocompleteid"].=$autocompleteid.",";
								$datanew2["autocomplete"].=$val."</br>";
							}
						}
						
						M("Worktype")->add($datanew2);
					}
					else
					{
						$datasave2["id"]=$nodeinfo['id'];
						$datasave2["sort"]=$array[$k]['6'];
						$datasave2["qualityunit"]=$array[$k]['9'];
						$datasave2["period1"]=$array[$k]['10'];
						$datasave2["parallel"]=$array[$k]['11'];
						$dependencearray=explode(",",$array[$k]['7']);
						$datasave2["dependence"]="";
						$datasave2["dependenceid"]="";
						foreach($dependencearray as $key => $val)
						{
							if(!empty($val))
							{
								$dependencedetail=explode("-",$val);
								$mapforWorktypefordependencepid["classify"]=$dependencedetail[0];
								$mapforWorktypefordependencepid["projecttype"]=$projecttype;
								$mapforWorktypefordependencepid["title"]=$dependencedetail[1];
								$mapforWorktypefordependencepid["type"]=1;
								$mapforWorktypefordependence["pid"]=M("Worktype")->where($mapforWorktypefordependencepid)->getField("id");
								$mapforWorktypefordependence["classify"]=$dependencedetail[0];
								$mapforWorktypefordependence["title"]=$dependencedetail[2];
								$mapforWorktypefordependence["type"]=2;
								
								$dependenceid=M("Worktype")->where($mapforWorktypefordependence)->getField("id");
								$datasave2["dependenceid"].=$dependenceid.",";
								$datasave2["dependence"].=$val."</br>";
							}
						}
						
						$autocompletearray=explode(",",$array[$k]['8']);
						$datasave2["autocomplete"]="";
						$datasave2["autocompleteid"]="";
						foreach($autocompletearray as $key => $val)
						{
							if(!empty($val))
							{
								$autocompletedetail=explode("-",$val);
								$mapforWorktypeforautocompletepid["classify"]=$autocompletedetail[0];
								$mapforWorktypeforautocompletepid["projecttype"]=$projecttype;
								$mapforWorktypeforautocompletepid["title"]=$autocompletedetail[1];
								$mapforWorktypeforautocompletepid["type"]=1;
								$mapforWorktypeforautocomplete["pid"]=M("Worktype")->where($mapforWorktypeforautocompletepid)->getField("id");
								$mapforWorktypeforautocomplete["classify"]=$autocompletedetail[0];
								$mapforWorktypeforautocomplete["title"]=$autocompletedetail[2];
								$mapforWorktypeforautocomplete["type"]=2;
								
								$autocompleteid=M("Worktype")->where($mapforWorktypeforautocomplete)->getField("id");
								$datasave2["autocompleteid"].=$autocompleteid.",";
								$datasave2["autocomplete"].=$val."</br>";
							}
						}
						
						M("Worktype")->save($datasave2);
					}
				}
			}
			
			
			
			M("Worktypeperiod")->where("id>0")->delete();
			$periods=M('Settingcapacity')->order("begin asc")->select();
			$worktypes=M("Worktype")->select();
			foreach($worktypes as $key => $val)
			{
				if(!empty($val["period1"]))
				{
					$mapforWorktype["id"]=$val["pid"];
					$dataworktypeperiod["worktype"]=M("Worktype")->where($mapforWorktype)->getField("title");
					$dataworktypeperiod["subworktype"]=$val["title"];
					$dataworktypeperiod["pid"]=$val["id"];
					$periodarray=explode(",",$val["period1"]);
					foreach($periods as $key1 => $val1)
					{
						$dataworktypeperiod["begin"]=$val1["begin"];
						$dataworktypeperiod["end"]=$val1["end"];
						$dataworktypeperiod["period"]=$periodarray[$key1];
						M("Worktypeperiod")->add($dataworktypeperiod);
					}
				}
				
			}
			
			
			$this->success('上传成功!');
		}
		else
		{
			$this->error('上传的文件类型非法!');
		}
	}

}	
?>