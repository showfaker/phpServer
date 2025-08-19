<?php
class WorkerAction extends CommonAction {
	public function index() {
        if(!empty($_REQUEST['tab']))
		{
			$_SESSION[tab]=$_REQUEST['tab'];
		}
		$this->assign('tab',$_SESSION['tab']);
		
		if(!empty($_REQUEST['nickname']))
		{
			$map['nickname'] = array("like","%".$_REQUEST['nickname']."%");
			$this->assign('nickname',$_REQUEST['nickname']);	
		}
		if(!empty($_REQUEST['number']))
		{
			$map['number'] = array("like","%".$_REQUEST['number']."%");
			$this->assign('number',$_REQUEST['number']);	
		}
		$name = "Worker";
		$model = D($name);
		$voList = $model->where($map)->order("number asc")->select();
		
		$this->assign('list', $voList);
		$this->display();
	}
	
	
	function insert() {
		
		
		$birthday=$_REQUEST[birthday];
		$idcard=$_REQUEST[idcard];
		$birthday1 = substr($idcard, 6, 4)."-".substr($idcard, 10, 2)."-".substr($idcard, 12, 2);
		if($birthday!=$birthday1)
		{
			//$this->error('身份证号与出生日期不符');
		}
		
		
				
				
		$name = "Worker";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->user_id=$_SESSION['loginUserName'];
		$model->create_time=time();
		$list = $model->add();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			//$this->redirect('index');
			$this->success('操作成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function edit() 
	{
        $name = "Worker";
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
	
	function update() 
	{
        $name = "Worker";
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            //$this->redirect('index');
			$this->success('操作成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
	
	
	public function foreverdelete() {
        //删除指定记录
        $name = "Worker";
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
			/*
			for($k=2;$k<=$number;$k++)
			{
				if($array[$k]['1']=="")
				{
					continue;
				}
				$birthday=$array[$k]['3'];
				$idcard=$array[$k]['7'];
				$birthday1 = substr($idcard, 6, 4)."-".substr($idcard, 10, 2)."-".substr($idcard, 12, 2);
				if($birthday!=$birthday1)
				{
					$this->error($array[$k]['2'].'的身份证号与出生日期不符');
				}
			}
			*/
			for($k=2;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
				
				
					$mapforMaterials["number"]=$array[$k]['1'];
					$mapforMaterials["nickname"]=$array[$k]['2'];
					$mapforMaterials["birthday"]=$array[$k]['3'];
					$mapforMaterials["job"]=$array[$k]['4'];
					$mapforMaterials["salary"]=$array[$k]['5'];
					$mapforMaterials["indate"]=$array[$k]['6'];
					$mapforMaterials["idcard"]=$array[$k]['7'];
					$mapforMaterials["source"]=$array[$k]['8'];
					M("Worker")->add($mapforMaterials);
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