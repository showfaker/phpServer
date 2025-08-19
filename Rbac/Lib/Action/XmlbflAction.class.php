<?php
class XmlbflAction extends CommonAction {			
	public function index() {
        if(!empty($_REQUEST['tab']))
		{
			$this->assign('tab',$_REQUEST['tab']);	
		}
		if(!empty($_REQUEST['projecttype']))
		{
			$map['projecttype'] = array("in",$_REQUEST['projecttype']);
			$this->assign('projecttype',$_REQUEST['projecttype']);	
		}
	
		if(($_REQUEST['tab']=="储备")||($_REQUEST['tab']=="立项中"))
		{
			$map[design_status]=array("in","立项中");
		}
		else if(($_REQUEST['tab']=="待施工"))
		{
			$map['design_status'] = array("in","待施工");
		}
		else if(($_REQUEST['tab']=="施工中"))
		{
			$map[design_status]=array("in","施工中");
		}
		else if(($_REQUEST['tab']=="滞后"))
		{
			$plmwarningids="";
			$mapforprojectfortopforwarning[warning]=array("eq","1");
			$mapforprojectfortopforwarning[status]=array("eq","1");
			$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$mapforprojectfortopforwarningapprove[status]=array("eq","1");
			$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$map['id'] = array('in',$plmwarningids);
		}
		else if(($_REQUEST['tab']=="延期"))
		{
			$plmwarningids="";
			$mapforprojectfortopforwarning[warning]=array("eq","1");
			$mapforprojectfortopforwarning[status]=array("eq","0");
			$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$mapforprojectfortopforwarningapprove[status]=array("eq","0");
			$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->group("plmid")->select();
			foreach($plmwarnings as $key => $val)
			{
				$plmwarningids.=$val["plmid"].",";
			}
			
			$map['id'] = array('in',$plmwarningids);
		}

		else if(($_REQUEST['tab']=="已完成"))
		{
			$map['design_status'] = array('in',"完成施工,施工完成,验收完成,完成验收");
		}
		else if(($_REQUEST['tab']=="施工完成")||($_REQUEST['tab']=="完成施工"))
		{
			$map['design_status'] = array('in',"完成施工,施工完成");
		}
		else if(($_REQUEST['tab']=="验收完成")||($_REQUEST['tab']=="完成验收"))
		{
			$map['design_status'] = array('in',"验收完成,完成验收");
		}
		else if(($_REQUEST['tab']=="暂停中"))
		{
			$map['design_status'] = array("in","暂停中");
		}
		else if(($_REQUEST['tab']=="暂停"))
		{
			$map['design_status'] = array("in","暂停中");
		}
		else if(($_REQUEST['tab']=="取消"))
		{
			$map['design_status'] = array("in","取消");
		}
		else if(!empty($_REQUEST['tab']))
		{
			$map['design_status'] = array("in",$_REQUEST['tab']);
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
		
		if($_POST['keyword'])
		{
			$map['title'] = array('like',"%".$_POST['keyword']."%");
			$this->assign("keyword",$_POST['keyword']);
		}
		
		$map[user]=array("neq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities();
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
				//$mapforPlmschedule[percent]=array("neq","100%");
				//$mapforPlmschedule[plmid] = $val[id];
				//$mapforPlmschedule[status]=1;
				//$voList[$key][schedule]=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
				$mapforPlmschedule[plmid] = $val[id];
				$voList[$key][daily]=M("Plmdaily")->where($mapforPlmschedule)->order("create_time desc")->find();
				
				//foreach($voList as $key => $val)
				//{
					$voList[$key]['enters']=explode(',',$val['enter']);
					$voList[$key]['entersfilename']=explode(',',$val['enterfilename']);
				//}
				
				
				$voList[$key]['finishphotos']=explode(',',$val['finishphoto']);
				$voList[$key]['finishphotosfilename']=explode(',',$val['finishphotofilename']);
					
				$voList[$key]['finishs']=explode(',',$val['finish']);
				$voList[$key]['finishsfilename']=explode(',',$val['finishfilename']);
				
				$voList[$key]['budgetsfinal']=explode(',',$val['budgetfinal']);
				$voList[$key]['budgetsfinalfilename']=explode(',',$val['budgetfinalfilename']);
				
				
				if(($_REQUEST['tab']=="滞后"))
				{
					$content1="";
					
					$mapforprojectfortopforwarning[warning]=array("eq","1");
					$mapforprojectfortopforwarning[status]=array("eq","1");
					$mapforprojectfortopforwarning[plmid]=array("eq",$val["id"]);
					$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->select();
					
					foreach($plmwarnings as $key1 => $val1)
					{
						$content1.=$val1["worktype"]."超时限;</br>";
					}
					
					$mapforprojectfortopforwarningapprove[status]=array("eq","1");
					$mapforprojectfortopforwarningapprove[plmid]=array("eq",$val["id"]);
					$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->select();
					foreach($plmwarnings as $key1 => $val1)
					{
						$content1.=$val1["title"].";</br>";
					}
					
					$voList[$key]['content1']=$content1;
				}
				if(($_REQUEST['tab']=="延期"))
				{
					$content1="";
					
					$mapforprojectfortopforwarning[warning]=array("eq","1");
					$mapforprojectfortopforwarning[status]=array("eq","0");
					$mapforprojectfortopforwarning[plmid]=array("eq",$val["id"]);
					$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->select();
					
					foreach($plmwarnings as $key1 => $val1)
					{
						$content1.=$val1["worktype"]."超时限;</br>";
					}
					
					$mapforprojectfortopforwarningapprove[status]=array("eq","0");
					$mapforprojectfortopforwarningapprove[plmid]=array("eq",$val["id"]);
					$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->select();
					foreach($plmwarnings as $key1 => $val1)
					{
						$content1.=$val1["title"].";</br>";
					}
					
					$voList[$key]['content1']=$content1;
				}
			}
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
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
		
}
?>