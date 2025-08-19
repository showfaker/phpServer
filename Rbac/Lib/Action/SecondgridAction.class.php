<?php
class SecondgridAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		
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
		if($_REQUEST['owner'])
		{
			$map['owner'] = array('like',"%".$_REQUEST['owner']."%");
			$this->assign("owner",$_REQUEST['owner']);
		}
		if($_REQUEST['owner2'])
		{
			$map['owner2'] = array('like',"%".$_REQUEST['owner2']."%");
			$this->assign("owner2",$_REQUEST['owner2']);
		}
		if($_REQUEST['invester'])
		{
			$map['invester'] = array('like',"%".$_REQUEST['invester']."%");
			$this->assign("invester",$_REQUEST['invester']);
		}
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}
		if($_REQUEST['type'])
		{
			$map['type'] = array('like',"%".$_REQUEST['type']."%");
			$this->assign("type",$_REQUEST['type']);
		}
		if($_REQUEST['taketype'])
		{
			$map['taketype'] = array('like',"%".$_REQUEST['taketype']."%");
			$this->assign("taketype",$_REQUEST['taketype']);
		}
		
		if($_REQUEST['address'])
		{
			$map['province|city|area|address'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		if($_REQUEST['charge'])
		{
			$map['charge'] = array('like',"%".$_REQUEST['charge']."%");
			$this->assign("charge",$_REQUEST['charge']);
		}
		if($_REQUEST['director'])
		{
			$map['director'] = array('like',"%".$_REQUEST['director']."%");
			$this->assign("director",$_REQUEST['director']);
		}
		
		if($_REQUEST['projecttype'])
		{
			$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
			$this->assign("projecttype",$_REQUEST['projecttype']);
		}
		
		
		
		if($_REQUEST['xiaoshouuser'])
		{
			$map['xiaoshouuser'] = array('like',"%".$_REQUEST['xiaoshouuser']."%");
			$this->assign("xiaoshouuser",$_REQUEST['xiaoshouuser']);
		}
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		/*
		$alldata=M("Project")->select();
		foreach($alldata as $key => $val)
		{
			M("Project")->where("id=". $val["id"])->setField("ctime",$val["time"]);
		}
		*/
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
	
		if($_SESSION[account]!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position]);
		}
		
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'id',false);
		}
		
		
		$map1['_complex'] = $this->find5level($_SESSION[position]);
		
		$allprojects=M("Project")->where($map1)->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
		
		$mapforRole["name"]=array("like","%组长%");
		$roles=M("Role")->where($mapforRole)->select();
		foreach ($roles as $key => $val) 
		{
			$roles[$key]["subname"]=str_replace("组长","",$val["name"]);
		}
		$this->assign('roles', $roles);
		
		
		if($_SESSION["app"])
		{
			$this->display("indexapp");
		}
		else
		{
			$this->display();
		}
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
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
				else
				{
					$p->parameter .= "$key=" . $_REQUEST[$key] . "&";
				}
            }
			if($_REQUEST["flag1"])
			{
				$p->parameter .= "flag1=" . $_REQUEST["flag1"] . "&";
			}
			if($_REQUEST["flag2"])
			{
				$p->parameter .= "flag2=" . $_REQUEST["flag2"] . "&";
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
	
	function ajax1()
	{
		$titlerepeat["title"]=array("eq",$_REQUEST[title]);
		$ifrepeat=M("Project")->where($titlerepeat)->find();
		if(!empty($ifrepeat))
		{
			echo "0";
		}
		else
		{
			echo "1";
		}
	}
	
	
	
	function setgriddate() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('orgdata', $vo);
		$this->assign('vo', $vo);
		$this->assign('approve', $_REQUEST["approve"]);
		$this->display();
	}
	
	public function setgriddatesubmit() {
        //删除指定记录
        $name = "Project";
        $model = D($name);
        
		$id = $_REQUEST ["id"];
	
		$condition = array("id" => array('in', explode(',', $id)));
		$info = $model->where($condition)->find();
		
		
		if(empty($_REQUEST["approve"]))
		{
			if(empty($info[gongcheng]))
			{
				$this->error('该项目没有配置项目经理，无法提交审批！');
			}
			
			$date=date('Y-m-d H:i');
			$model->where($condition)->setField("griddate_temp",$_REQUEST["griddate"]);
			$model->where($condition)->setField("griduser",$_SESSION["loginUserName"]);
			$model->where($condition)->setField("handlehistory",$info['handlehistory'].$_SESSION['loginUserName']."于".$date."设置并网日期</br>------------------</br>");
			
			$schedulemap[taskid]=$info[id];
			$schedulemap[status]=1;
			$schedulemap[type]="Secondgrid";
			$scheduleexist=M("schedule")->where($schedulemap)->find();
			
			if(empty($scheduleexist))
			{
				$taskid=$info[id];
				$address=$info['title'];
				$data['content']=$_SESSION['loginUserName']."于".$date."在《".$address."》项目设置并网日期，请您审核。";
				$data['href'] ="index.php?s=Secondgrid/index";
				$data['taskid'] =$taskid;
				$data['type'] ="Secondgrid";
				$mapforUser["nickname"]=$info[gongcheng];
				$userschedule=M("User")->where($mapforUser)->find();
				$data['user']=$userschedule['nickname'].$userschedule['number'];
				$this->Addschedule($data);
			}
			
		}
		else
		{
			
				$address=$info[title];
				$schedulemap[taskid]=$info[id];
				$schedulemap[status]=1;
				$schedulemap[type]="Secondgrid";
				M("Schedule")->where($schedulemap)->setField("status",0);
				M("Schedule")->where($schedulemap)->setField("result",$_REQUEST[result]);
		
				if(($_REQUEST[result]=="同意"))
				{
					$model->where($condition)->setField("griddate",$info["griddate_temp"]);
					$model->where($condition)->setField("griduser","");
					$model->where($condition)->setField("griddate_temp","");
				}
				else
				{
					$model->where($condition)->setField("griduser","");
					$model->where($condition)->setField("griddate_temp","");
				}
				$model->where($condition)->setField("handlehistory","handlehistory",$info['handlehistory'].$_SESSION['loginUserName']."于".$date."进行并网审核，结果：".$_REQUEST[result]."</br>------------------</br>");
				
				
				$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行并网审核，结果：".$_REQUEST[result]."。";
				$data['receiver']=$info['griduser'].$this->findNumberByNameAndRole($info['griduser']).",";
				$data['sender']="系统通知";
				$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行并网审核，结果：".$_REQUEST[result]."同意。";
				$this->Sendmail($data);
				
		}
		
						
		$this->success('提交成功！');
     
    }
	
	
}
?>