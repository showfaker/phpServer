<?php
class WarningAction extends CommonAction {
	
	
	function _filter(&$map){
		if($_REQUEST['city'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['city']."%");
			$this->assign("city",$_REQUEST['city']);
		}
		if($_REQUEST['address'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		if($_REQUEST['keyword'])
		{
			$map['plm'] = array('like',"%".$_REQUEST['keyword']."%");
			$this->assign("keyword",$_REQUEST['keyword']);
		}
		if($_REQUEST['worktype'])
		{
			$map['worktype'] = array('like',"%".$_REQUEST['worktype']."%");
			$this->assign("worktypetitle",$_REQUEST['worktype']);
		}
	}
	
	
	public function index() {
		
		$this->getAllcities();
		$this->getAllworktypes();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		
		
        if(!empty($_REQUEST['tab']))
		{
			$this->assign('tab',$_REQUEST['tab']);	
		}		
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		if($_REQUEST['tab']==4)
		{
			/*
			$mapforplmschedule['warning'] = 1;
			$mapforPlmschedule[status]=1;
			$warningschedules=M("Plmschedule")->where($mapforplmschedule)->select();
			foreach($warningschedules as $key => $val)
			{
				$scheduleidstr.=$val[id].",";
			}
			$map[scheduleid]=array("in",$scheduleidstr);
			*/
			$map[warning]=array("neq","");
		}
		if($_REQUEST['tab']==7)
		{
			if($_REQUEST['city2'])
			{
				$mapforplmschedule['city'] = array('like',"%".$_REQUEST['city2']."%");
				$this->assign("city2",$_REQUEST['city2']);
			}
			if($_REQUEST['address2'])
			{
				$mapforplmschedule['address'] = array('like',"%".$_REQUEST['address2']."%");
				$this->assign("address2",$_REQUEST['address2']);
			}
			
			$date=date("Y-m-d");
			$mapforplmschedule['prewarning'] = 1;
			
			if($_REQUEST['worktype'])
			{
				$mapforplmschedule['worktype'] = array('like',"%".$_REQUEST['worktype']."%");
				$this->assign("worktypetitle",$_REQUEST['worktype']);
			}
			
			$warningschedules=M("Plmwarning")->where($mapforplmschedule)->select();
			foreach($warningschedules as $key => $val)
			{
				$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
				
				/*
				$day1 = $val[plantimebegin];
				$day2 = $val[plantimeend];
				$diff = $this->diffBetweenTwoDays($day1, $day2);
				$timeplanlenth=$diff;
				//每天所占的比例
				$percentperday=100/$timeplanlenth;
				//今天与计划日之间天数差
				$diffreal = $this->diffBetweenTwoDays($day1, $date);
				//今天应该完成的比例
				$todayplanpercent=$percentperday*$diffreal;
				if($todayplanpercent>100)$todayplanpercent=100;
				//实际的比例
				$realpercent=str_replace("%","",$val[percent]);
				if($realpercent=="")$realpercent=0;
				
				$warningschedules[$key][todayplanpercent]=$todayplanpercent;
				$warningschedules[$key][realpercent]=$realpercent;
				$warningschedules[$key][calc]=$realpercent-$todayplanpercent;
				*/
				
			}
			$this->assign("list", $warningschedules);
			$this->assign("warningschedulescount", count($warningschedules));
			$this->display();
			return;
		}
		if($_REQUEST['tab']==8)
		{
			
			if($_REQUEST['city2'])
			{
				$mapforplmschedule['city'] = array('like',"%".$_REQUEST['city2']."%");
				$this->assign("city2",$_REQUEST['city2']);
			}
			if($_REQUEST['address2'])
			{
				$mapforplmschedule['address'] = array('like',"%".$_REQUEST['address2']."%");
				$this->assign("address2",$_REQUEST['address2']);
			}
			
			$date=date("Y-m-d");
			$mapforplmschedule['warning'] = 1;
			
			if($_REQUEST['worktype'])
			{
				$mapforplmschedule['worktype'] = array('like',"%".$_REQUEST['worktype']."%");
				$this->assign("worktypetitle",$_REQUEST['worktype']);
			}
			
			$warningschedules=M("Plmwarning")->where($mapforplmschedule)->select();
			foreach($warningschedules as $key => $val)
			{
				$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
				
				/*
				$day1 = $val[plantimebegin];
				$day2 = $val[plantimeend];
				$diff = $this->diffBetweenTwoDays($day1, $day2);
				$timeplanlenth=$diff;
				//每天所占的比例
				$percentperday=100/$timeplanlenth;
				//今天与计划日之间天数差
				$diffreal = $this->diffBetweenTwoDays($day1, $date);
				//今天应该完成的比例
				$todayplanpercent=$percentperday*$diffreal;
				if($todayplanpercent>100)$todayplanpercent=100;
				//实际的比例
				$realpercent=str_replace("%","",$val[percent]);
				if($realpercent=="")$realpercent=0;
				
				$warningschedules[$key][todayplanpercent]=$todayplanpercent;
				$warningschedules[$key][realpercent]=$realpercent;
				$warningschedules[$key][calc]=$realpercent-$todayplanpercent;
				*/
				
			}
			$this->assign("list", $warningschedules);
			$this->assign("warningschedulescount", count($warningschedules));
			$this->display();
			return;
		}
		
		
		//$map['user'] = array("in",$this->find5levelusers($_SESSION[position]));
		
		$mapforproject["engineeringmanage|supervisor|drawing_user|budget_user|designer|projectmanager|way|waysub|areatype|areadetail|draw_user|waysubother"]=array("eq",$_SESSION[name]);
		$projects=M("Project")->where($mapforproject)->field("id")->select();
		$arrstr="";
		foreach($projects as $k=>$v){
            $arrstr.=$v['id'].",";
        }
		$where['user']  = array("in",$this->find5levelusers($_SESSION[position]));
		$where['plmid']  = array('in',$arrstr);
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$name = "Plmdaily";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		
		
		
		
		if($_REQUEST['tab']==5)
		{
			
			if($_REQUEST['city1'])
			{
				$map['city'] = array('like',"%".$_REQUEST['city1']."%");
				$this->assign("city1",$_REQUEST['city1']);
			}
			if($_REQUEST['address1'])
			{
				$map['title'] = array('like',"%".$_REQUEST['address1']."%");
				$this->assign("address1",$_REQUEST['address1']);
			}
		
			$map['design_status'] = array("in","施工中");
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			//$map[user]=array("neq","");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'create_time',false);
			}
		
		}
		if($_REQUEST['tab']==6)
		{
			
			if($_REQUEST['city1'])
			{
				$map['city'] = array('like',"%".$_REQUEST['city1']."%");
				$this->assign("city1",$_REQUEST['city1']);
			}
			if($_REQUEST['address1'])
			{
				$map['title'] = array('like',"%".$_REQUEST['address1']."%");
				$this->assign("address1",$_REQUEST['address1']);
			}
		
		
			$map['design_status'] = array("in","施工中");
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$map[user]=array("neq","");
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				$this->_list($model, $map,'create_time',false);
			}
		
		}
		$this->assign("date",date("Y-m-d"));
		$this->display();
		return;
	}
	function diffBetweenTwoDays ($day1, $day2)
	{
	  $second1 = strtotime($day1);
	  $second2 = strtotime($day2);
		
	  if ($second1 < $second2) {
		$tmp = $second2;
		$second2 = $second1;
		$second1 = $tmp;
	  }
	  return ($second1 - $second2) / 86400;
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
			if($_REQUEST[tab]=="6")
			{
				$p = new Page($count,1000);
			}
			else
			{
				$p = new Page($count, $listRows);
			}
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
				$voList[$key]['photos']=explode(',',$val['photo']);
			}
			if($_REQUEST[tab]=="5")
			{
				foreach($voList as $key => $val)
				{
					//$mapforPlmschedule[percent]=array("neq","100%");
					$mapforPlmschedule[plmid] = $val[id];
					$voList[$key][daily]=M("Plmdaily")->where($mapforPlmschedule)->order("create_time desc")->find();
					
					foreach($voList as $key => $val)
					{
						$voList[$key]['enters']=explode(',',$val['enter']);
						$voList[$key]['entersfilename']=explode(',',$val['enterfilename']);
					}
				}
			}
			if($_REQUEST[tab]=="6")
			{
				foreach($voList as $key => $val)
				{
					//$mapforPlmschedule[percent]=array("neq","100%");
					$mapforPlmschedule[plmid] = $val[id];
					$voList[$key][daily]=M("Plmdaily")->where($mapforPlmschedule)->order("create_time desc")->find();
					if(!empty($voList[$key][daily][warning]))
					{
						//$mapforPlmwarning[id] = $voList[$key][daily][warning];
						$mapforPlmwarning[plmid] = $voList[$key][daily][plmid];
						$mapforPlmwarning[worktype] = $voList[$key][daily][worktype];
						$voList[$key][warning]=M("Plmwarning")->where($mapforPlmwarning)->find();
					}
					else
					{
						unset($voList[$key]);
					}
				}
			}
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
			$p->parameter="&";
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
			if($_REQUEST['worktype'])
			{
				$p->parameter .= "worktype=" . urlencode($_REQUEST['worktype']) . "&";
			}
			if($_REQUEST['city'])
			{
				$p->parameter .= "city=" . urlencode($_REQUEST['city']) . "&";
			}
			if($_REQUEST['address'])
			{
				$p->parameter .= "address=" . urlencode($_REQUEST['address']) . "&";
			}
			if($_REQUEST['timebegin'])
			{
				$p->parameter .= "timebegin=" . urlencode($_REQUEST['timebegin']) . "&";
			}
			if($_REQUEST['timeend'])
			{
				$p->parameter .= "timeend=" . urlencode($_REQUEST['timeend']) . "&";
			}
			if($_REQUEST['tab'])
			{
				$p->parameter .= "tab=" . urlencode($_REQUEST['tab']) . "&";
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
	public function foreverdelete() {
        //删除指定记录
        $name = "Plmdaily";
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