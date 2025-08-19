<?php
class ReportoutputAction extends CommonAction {			
	//过滤查询字段
	function _filter(&$map){
		if($_POST['address'])
		{
			$map['title'] = array('like',"%".$_POST['address']."%");
			$this->assign("address",$_POST['address']);
		}
		if($_POST['city'])
		{
			$map['city'] = array('like',"%".$_POST['city']."%");
			$this->assign("city",$_POST['city']);
		}
	}
	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		
		$topname=$_REQUEST[topname];
		$mapforclassify[topname]=$_REQUEST[topname];
		$names=M('classify')->where($mapforclassify)->field("name")->select();
		$this->assign('names',$names);
		$this->assign('topname', $topname);
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		//$map[design_status]=array("in","合同审核完成,合同审核完成,待施工,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,竣工待验收,项目待验收,验收审核退回,暂停中");
		$map[user]=array("neq","");
		$map[step6]=array("eq","1");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities(1);
		
		
		$mapforRole['name']=array("like","%市场部总监%");
		$positions=M("Role")->where($mapforRole)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuser["position"]=array("in",$pline);
		$mapuser[status]=1;
		$shichangbuzongjian=M("User")->where($mapuser)->order("nickname asc")->field("nickname,account,id")->select();
		foreach($shichangbuzongjian as $key=>$val)
		{
			$shichangbuzongjianaccount.=$val[account].",";
		}
		
		
		$this->assign('shichangbuzongjianaccount', $shichangbuzongjianaccount);
		
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
			
			$topname=$_REQUEST[topname];
			$mapforclassify[topname]=$_REQUEST[topname];
			$names=M('classify')->where($mapforclassify)->field("name")->select();
			foreach($names as $key => $val)
			{
				$namesstr.=$val[name].",";
			}
			
			foreach($voList as $key => $val)
			{
				$mapforPlmattendancedevice[plmid]=$val["id"];
				$voList[$key][mindate]=M("Plmattendancedevice")->where($mapforPlmattendancedevice)->min("date");
				$voList[$key][maxdate]=M("Plmattendancedevice")->where($mapforPlmattendancedevice)->max("date");

				$mapforPlmoutputdaily[plmid]=$val["id"];
				$mapforPlmoutputdaily[value]=array("not in","0,");
				$voList[$key][output]=M("Plmoutputdaily")->where($mapforPlmoutputdaily)->sum("value");
				
				
				$outputday=M("Plmoutputdaily")->where($mapforPlmoutputdaily)->group("date")->select();
				if($outputday)
					$voList[$key][outputday]=count($outputday);
				else
					$voList[$key][outputday]=0;
				
				$mapforPlmoutputdailyhot[plmid]=$val["id"];
				$mapforPlmoutputdailyhot[value]=array("neq","");
				$mapforPlmoutputdailyhot[pworktype]=array("like","%热再生%");
				$voList[$key][outputhot]=M("Plmoutputdaily")->where($mapforPlmoutputdailyhot)->sum("value");
				$outputhotday=M("Plmoutputdaily")->where($mapforPlmoutputdaily)->group("date")->select();
				
				if($outputhotday)
					$voList[$key][outputhotday]=count($outputhotday);
				else
					$voList[$key][outputhotday]=0;
				
				
				
				$mapforPlmplan[plmid]=$val["id"];
				$mapforPlmplan[plan7]=array("not in","无待工,");
				$plmplans=M("Plmplan")->where($mapforPlmplan)->field("plan7")->select();
				$voList[$key]["小计"]=0;
				foreach($plmplans as $key1 => $val1)
				{
					$voList[$key][$val1["plan7"]]++;
					$voList[$key]["小计"]++;
				}
				
				$voList[$key]["效率"]=round((100*$voList[$key][outputday])/($voList[$key]["小计"]+$voList[$key][outputday]),2)."%";
				
				$voList[$key][output]=M("Plmoutputdaily")->where($mapforPlmplan)->sum("value");
				$tempdata=M("Plmoutputdaily")->where($mapforPlmoutputdaily)->group("date")->select();
				
				if(empty($tempdata))
				{
					$voList[$key][outputday]=0;
				}
				else
				{
					$voList[$key][outputday]=count($tempdata);
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
		
        return;
    }

	
	function detail() {
		$name = "Project";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$plm=$vo[title];
		$topname=$_REQUEST[topname];
		$mapforclassify[topname]=$_REQUEST[topname];
		$names=M('classify')->where($mapforclassify)->field("name")->select();
		foreach($names as $key => $val)
		{
			$namesstr.=$val[name].",";
		}
		$mapforplmorder2paytime["approve"]=1;
		$mapforplmorder2paytime["plm"]=$plm;
		$mapforplmorder2paytime["name"]=array("in",$namesstr);
		$mapforplmorder2paytime["del"]=0;
		$voList=M('plmorder2paytime')->where($mapforplmorder2paytime)->select();
		foreach ($voList as $key3 => $value3) {
			$gcprice+=$value3['oldpay'];//已付
			$gctotal+=$value3['pay'];//总款项
			
			$voList[$key3]['price']=$gcprice;
			$voList[$key3]['total']=$gctotal;
		}
		
		$allTotal+=$gctotal;
		$allPay+=$gcprice;
			
		$allTotal=number_format($allTotal,2);//总款项
		$allPay=number_format($allPay,2);//已付
		$this->assign('total',$total);
		$this->assign('pay',$pay);
		$this->assign('allTotal',$allTotal);
		$this->assign('allPay',$allPay);
		$this->assign('material',$material);
		$this->assign('volist',$voList);
		$this->assign('plmid',$_REQUEST['plm']);
	
			
		$this->assign('orgdata', $vo);

	
		$this->display();
	}
		
}
?>