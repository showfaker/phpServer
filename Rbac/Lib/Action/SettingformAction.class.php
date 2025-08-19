<?php
class SettingformAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		
	}
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		$mapforSettingform["status"]=array("eq","1");
		$volist=M("Settingform")->where($mapforSettingform)->order("sort asc")->select();
		foreach ($volist as $key => $val) {
			$mapforsettingform[id]=array("eq",$val[id]);
			$volist[$key][detail]=M("Settingform")->where($mapforsettingform)->order("create_time desc")->find();
		}
		$this->assign('volist', $volist);
		$this->assign('totalCount', count($volist));
		if($_SESSION[skin]!=3)
		{
			$this->display(indexoa);
		}
		else
		{
			$this->display();
		}
		return;
	}
	
	public function add() {
		$name = "Node";
		$model = D($name);
		$map['status']=1;
		$map['group_id']=23;
		$map['level']=2;
		$type=$model->where($map)->order("sort asc")->select();
		$this->assign('type',$type);
		if($_SESSION[skin]!=3)
		{
			$this->display(addoa);
		}
		else
		{
			$this->display();
		}
		return;
	}
	
	function insert() {

		$name = "Settingform";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->status=1;
		$mapforexist[title]=$_REQUEST[title];
		$mapforexist[status]=1;
		$ifexist=M("Settingform")->where($mapforexist)->getField("id");
		if(!empty($ifexist))
		{
			$this->error('模块已经存在!');
		}

		$list = $model->add();
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('模块新增成功!');
		} else {
			//失败提示
			$this->error('模块新增失败!');
		}
	}
	
	
	function set()
	{
        $name = "Settingform";
		$vo=M($name)->where("id=".$_REQUEST[id])->find();
		$this->assign('vo', $vo);
	
        if($_SESSION[skin]!=3)
        {
        	$this->display(setoa);
        }
        else
        {
        $this->display();
        }
    }
	
	function setsubmit() {

		$name = "Settingform";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$list = $model->save();
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('模块修改成功!');
		} else {
			//失败提示
			$this->error('模块修改失败!');
		}
	}
	
	
	function update() {
		//B('FilterString');
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		if(!empty($_FILES['file']['name']))
		{
			$savePath = '../Public/Uploads/';
			$filename=$_FILES['file']['name'];
			$size = $_FILES['file']['size']; //文件大小
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
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$model->help=$newname;
		}
		
		//查找是否序列号重复
		$mapforrepeatserial[title]=array("neq",$_REQUEST[title]);
		$mapforrepeatserial[status]=1;
		$mapforrepeatserial[serial]=$_REQUEST[serial];
		$repeatserial=M("Settingform")->where($mapforrepeatserial)->getField("id");
		if($repeatserial)
		{
			$this->error('流水号索引重复!');
		}
		// 更新数据
		//$mapforrepeat[title]=array("like","%".$_REQUEST[title]."%");
		$mapforrepeat[title]=array("eq",$_REQUEST[title]);
		$mapforrepeat[status]=1;
		$oldinfo=M("Settingform")->where($mapforrepeat)->find();
		$repeat=$oldinfo["id"];
		$model->create_time=time();
		$model->status=1;
		
		if($model->pararow0!="")$model->pararow0--;if($model->pararow1!="")$model->pararow1--;if($model->pararow2!="")$model->pararow2--;
		if($model->pararow3!="")$model->pararow3--;if($model->pararow4!="")$model->pararow4--;if($model->pararow5!="")$model->pararow5--;
		if($model->pararow6!="")$model->pararow6--;if($model->pararow7!="")$model->pararow7--;if($model->pararow8!="")$model->pararow8--;
		if($model->pararow9!="")$model->pararow9--;if($model->pararow10!="")$model->pararow10--;if($model->pararow11!="")$model->pararow11--;
		if($model->pararow12!="")$model->pararow12--;if($model->pararow13!="")$model->pararow13--;if($model->pararow14!="")$model->pararow14--;
		if($model->pararow15!="")$model->pararow15--;if($model->pararow16!="")$model->pararow16--;if($model->pararow17!="")$model->pararow17--;
		if($model->pararow18!="")$model->pararow18--;if($model->pararow19!="")$model->pararow19--;if($model->pararow20!="")$model->pararow20--;
		if($model->pararow21!="")$model->pararow21--;if($model->pararow22!="")$model->pararow22--;if($model->pararow23!="")$model->pararow23--;
		if($model->pararow24!="")$model->pararow24--;if($model->pararow25!="")$model->pararow25--;if($model->pararow26!="")$model->pararow26--;
		if($model->pararow27!="")$model->pararow27--;if($model->pararow28!="")$model->pararow28--;if($model->pararow29!="")$model->pararow29--;
		if($model->pararow30!="")$model->pararow30--;
		
		$model->help="";
		if($_REQUEST[node0])$model->help.=$_REQUEST[node0].",";
		if($_REQUEST[node1])$model->help.=$_REQUEST[node1].",";
		if($_REQUEST[node2])$model->help.=$_REQUEST[node2].",";
		if($_REQUEST[node3])$model->help.=$_REQUEST[node3].",";
		if($_REQUEST[node4])$model->help.=$_REQUEST[node4].",";
		if($_REQUEST[node5])$model->help.=$_REQUEST[node5].",";
		if($_REQUEST[node6])$model->help.=$_REQUEST[node6].",";
		if($_REQUEST[node7])$model->help.=$_REQUEST[node7].",";
		if($_REQUEST[node8])$model->help.=$_REQUEST[node8].",";
		if($_REQUEST[node9])$model->help.=$_REQUEST[node9].",";
		
		if($repeat)
		{
			$settingformid=$repeat;
			if(empty($model->help))
			{
				$model->help=M("Settingform")->where($mapforrepeat)->getField("help");
			}
			$model->sort=M("Settingform")->where($mapforrepeat)->getField("sort");
			$mapforrepeat[id]=array("eq",$repeat);
			$model->where($mapforrepeat)->save();
			
			
			$newinfo=$model->where($mapforrepeat)->find();
			
			for($row=0;$row<=30;$row++)
			{
				if(($newinfo["para".$row])!=($oldinfo["para".$row]))
				{
					$mapforFlowform["templateid"]=$newinfo["id"];
					$flowformarray=M("Flowform")->where($mapforFlowform)->select();
					break;
				}
			}
			
			for($row=0;$row<=30;$row++)
			{
				if(($newinfo["para".$row])!=($oldinfo["para".$row]))
				{
					for($rowfind=0;$rowfind<=30;$rowfind++)
					{
						$new=1;
						if($newinfo["para".$row]==$oldinfo["para".$rowfind])
						{
							
							foreach($flowformarray as $key => $val)
							{
								M("Flowform")->where("id=".$val["id"])->setField("para".$row,$val["para".$rowfind]);
							}
							$new=0;
							break;
						}
					}
					
					if($new==1)
					{
						//新字段就把该字段的相应数据全部置空
						//所以字段名称不支持修改，一旦修改会被置空
						foreach($flowformarray as $key => $val)
						{
							M("Flowform")->where("id=".$val["id"])->setField("para".$row,"");
						}
					}
					
					
				}
			}
		}
		else
		{
			$settingformid=$model->add();
		}
		
		$settingforminfo=M("Settingform")->where("id=".$settingformid)->find();
		$Model = new Model();
		$sql = "CREATE TABLE think_flow_".$settingforminfo[tablename]." (
			id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
			user VARCHAR(30) DEFAULT '',
			create_time VARCHAR(30) DEFAULT '',
			update_time VARCHAR(30) DEFAULT '',
			user_id VARCHAR(30) DEFAULT '',
			type VARCHAR(30) DEFAULT '',
			templateid VARCHAR(30) DEFAULT '',
			current VARCHAR(30) DEFAULT '',
			serial VARCHAR(30) DEFAULT '',
			year VARCHAR(30) DEFAULT '',
			handlehistory VARCHAR(500) DEFAULT '',
			status VARCHAR(30) DEFAULT ''
			)";
		//handlehistory LONGTEXT(0) NOT NULL DEFAULT '',
			
		$Model->query($sql);
		for($row=0;$row<=30;$row++)
		{
			if($settingforminfo["tableparaname".$row])//($newinfo["tableparaname".$row])!=($oldinfo["tableparaname".$row])
			{
				$sql = "ALTER TABLE think_flow_".$settingforminfo[tablename]." add ".$settingforminfo["tableparaname".$row]." NVARCHAR(30) DEFAULT ''";// NOT NULL 
				$Model->query($sql);
			}
		}
		$this->success('编辑成功!');
	}
	
	function edit() 
	{
        $this->assign('title', $_REQUEST[title]);
		$mapforsetting[title]=array("eq",$_REQUEST[title]);
		$mapforsetting[status]=1;
		
		$vo=M("Settingform")->where($mapforsetting)->find();
		
		if($vo[pararow0]!="")$vo[pararow0]++;if($vo[pararow1]!="")$vo[pararow1]++;if($vo[pararow2]!="")$vo[pararow2]++;
		if($vo[pararow3]!="")$vo[pararow3]++;if($vo[pararow4]!="")$vo[pararow4]++;if($vo[pararow5]!="")$vo[pararow5]++;
		if($vo[pararow6]!="")$vo[pararow6]++;if($vo[pararow7]!="")$vo[pararow7]++;if($vo[pararow8]!="")$vo[pararow8]++;
		if($vo[pararow9]!="")$vo[pararow9]++;if($vo[pararow10]!="")$vo[pararow10]++;if($vo[pararow11]!="")$vo[pararow11]++;
		if($vo[pararow12]!="")$vo[pararow12]++;if($vo[pararow13]!="")$vo[pararow13]++;if($vo[pararow14]!="")$vo[pararow14]++;
		if($vo[pararow15]!="")$vo[pararow15]++;if($vo[pararow16]!="")$vo[pararow16]++;if($vo[pararow17]!="")$vo[pararow17]++;
		if($vo[pararow18]!="")$vo[pararow18]++;if($vo[pararow19]!="")$vo[pararow19]++;if($vo[pararow20]!="")$vo[pararow20]++;
		if($vo[pararow21]!="")$vo[pararow21]++;if($vo[pararow22]!="")$vo[pararow22]++;if($vo[pararow23]!="")$vo[pararow23]++;
		if($vo[pararow24]!="")$vo[pararow24]++;if($vo[pararow25]!="")$vo[pararow25]++;if($vo[pararow26]!="")$vo[pararow26]++;
		if($vo[pararow27]!="")$vo[pararow27]++;if($vo[pararow28]!="")$vo[pararow28]++;if($vo[pararow29]!="")$vo[pararow29]++;
		if($vo[pararow30]!="")$vo[pararow30]++;
		
		$this->assign('vo', $vo);
		
		$helpstr=explode(",",$vo[help]);
		$this->assign('helpstr', $helpstr);
		
        if($_SESSION[skin]!=3)
        {
        	$this->display(editoa);
        }
        else
        {
        $this->display();
        }
    }
	
	 public function foreverdelete() {
        //删除指定记录
        $name = "Settingform";
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
}
?>