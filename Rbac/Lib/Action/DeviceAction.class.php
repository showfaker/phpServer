<?php
class DeviceAction extends CommonAction {
	public function index() {
        if(!empty($_REQUEST['tab']))
		{
			$_SESSION[tab]=$_REQUEST['tab'];
		}
		$this->assign('tab',$_SESSION['tab']);	
		if(!empty($_REQUEST['name']))
		{
			$map['name'] = array("like","%".$_REQUEST['name']."%");
			$this->assign('name',$_REQUEST['name']);	
		}
		if(!empty($_REQUEST['number']))
		{
			$map['number'] = array("like","%".$_REQUEST['number']."%");
			$this->assign('number',$_REQUEST['number']);	
		}
		$name = "Device";
		$model = D($name);
		$voList = $model->where($map)->order("number asc")->select();
		$this->assign('list', $voList);
		$this->display();
	}
	
	
	function insert() {
		$name = "Device";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->user_id=$_SESSION['loginUserName'];
		$model->create_time=time();
		$list = $model->add();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->redirect('index');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function edit() 
	{
        $name = "Device";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3)
        {
        	$this->display(editoa);
        }
        else
        {
        $this->display();
        }
    }
	function detail() 
	{
        $name = "Device";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        $this->display();
    }
	function update() 
	{
        $name = "Device";
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
		$date=date('m-d H:i');
		$oldinfo=M("Device")->where("id=".$_REQUEST["id"])->find();
		if($oldinfo["status"]!=$_REQUEST["status"])
		{
			$model->handlehistory=$oldinfo["handlehistory"].$_SESSION['loginUserName']."于".$date."修改状态为【".$_REQUEST["status"]."】</br>------------------</br>"; 
		}
		
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->redirect('index');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
	
	function editpiliang() 
	{
        $name = "Device";
        $model = M($name);
        $ids = $_REQUEST ["ids"];
		$mapforDevice["id"]=array("in",$ids);
        $volist = $model->where($mapforDevice)->select();
        $this->assign('volist', $volist);
		$this->assign('ids', $ids);
		
		foreach($volist as $key => $val)
		{
			if($val[status]=="在用")
			{
				echo "<div style='line-height:50px'>选择的设备中有在用状态，无法批量修改状态</div>";
				return;
			}
		}
		
		
		
        if($_SESSION[skin]!=3)
        {
        	$this->display(editpiliang);
        }
        else
        {
        $this->display();
        }
    }
	
	function updatepiliang() 
	{
        $name = "Device";
        $model = D($name);
        
		
		$ids = $_REQUEST ["ids"];
		$mapforDevice["id"]=array("in",$ids);
        $volist = $model->where($mapforDevice)->select();
		foreach($volist as $key => $val)
		{
			// 更新数据
			$date=date('m-d H:i');
			$oldinfo=M("Device")->where("id=".$val["id"])->find();
			if($oldinfo["status"]!=$_REQUEST["status"])
			{
				$handlehistory=$oldinfo["handlehistory"].$_SESSION['loginUserName']."于".$date."修改状态为【".$_REQUEST["status"]."】</br>------------------</br>"; 
			}
			$model->where("id=".$val["id"])->setField("status",$_REQUEST["status"]);
			$model->where("id=".$val["id"])->setField("handlehistory",$handlehistory);
		}
		
       
       $this->redirect('index');
    }
	public function foreverdelete() {
        //删除指定记录
        $name = "Device";
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
					$mapforMaterials["number"]=$array[$k]['1'];
					$mapforMaterials["name"]=$array[$k]['2'];
					$mapforMaterials["model"]=$array[$k]['3'];
					$mapforMaterials["plate"]=$array[$k]['4'];
					$mapforMaterials["source"]=$array[$k]['5'];
					M("Device")->add($mapforMaterials);
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