<?php
class ReportmaterialAction extends CommonAction {			
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
		
		$this->getAllcities();
		
		
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
            $p = new Page($count, 5);//$listRows
            //分页查询数据
			if($_SESSION['curpage']!=null)
			{
				$p->nowPage=$_SESSION['curpage'];		
				$p->firstRow=($_SESSION['curpage']-1)*($p->listRows);
				unset($_SESSION['curpage']);
			}
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			/*
			$topname=$_REQUEST[topname];
			$mapforclassify[topname]=$_REQUEST[topname];
			$names=M('classify')->where($mapforclassify)->field("name")->select();
			foreach($names as $key => $val)
			{
				$namesstr.=$val[name].",";
			}
			*/
			
			$material=M('brand')->select();
			$material[]['name']='其他的';
			$names=array();
			foreach ($material as $k => $v) {
				$names[]=$v['name'];
			}
			$this->assign('material',$material);
				
			foreach($voList as $key => $val)
			{
				
				$mapforPlmoutputdaily['plmid']=$val[id];
				$voList[$key]['backmoney']=M('Plmoutputdaily')->where($mapforPlmoutputdaily)->sum("money");
				
				$plm=$val["title"];
				//材料下单
				$total=0;//总计
				$pay=0;//已付总计
				
				foreach ($material as $kk => $value) {
					$price=0;//总采购
					$payprice=0;//已付
					$mapforplmmaterialtj['department']=array('in',array('材料部','市场部'));
					$mapforplmmaterialtj['status']=array('neq',20);
					$mapforplmmaterialtj['plm']=$plm;
					$mapforplmmaterialtj['type']=1;
					$mapforplmmaterialtj['tuihuo']=1;
					
					$plmmaterialtj=M('plmmaterialtj')->where($mapforplmmaterialtj)->select();
					foreach ($plmmaterialtj as $key1 => $value1) {
						if($value1['status']==1){
							$clids=explode(',', trim($value1['clid'],','));
							if($value['name']=="其他的"){
								$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->field("price,count")->select();
								foreach ($materials as $key2 => $value2) {
									$payprice+=$value2['price']*$value2['count'];
									$price+=$value2['price']*$value2['count'];
								}
							}else{
								$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->field("price,count")->select();
								foreach ($materials as $key2 => $value2) {
									$payprice+=$value2['price']*$value2['count'];
									$price+=$value2['price']*$value2['count'];
								}
							}

						}else{
							$clids=explode(',', trim($value1['clid'],','));	
							if($value['name']=='其他的'){
								$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->field("price,count")->select();
								foreach ($materials as $key2 => $value2) {
									$price+=$value2['price']*$value2['count'];
								}
							}else{
								$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->field("price,count")->select();
								foreach ($materials as $key2 => $value2) {
									$price+=$value2['price']*$value2['count'];
								}
							}

						}
							
					}
					$material[$kk][total]=number_format($price,2);
					$total+=$price;
					$material[$kk][price]=number_format($payprice,2);
					$pay+=$payprice;
					
					
					$mapPlmattendance["plmid"]=$val["id"];
					$mapPlmattendance["status"]="在岗";
					$workermoney=M("Plmattendance")->where($mapPlmattendance)->sum("money");
					
					$mapPlmattendancedevice["plmid"]=$val["id"];
					$mapPlmattendancedevice["status"]="在岗";
					$devicemoney=M("Plmattendancedevice")->where($mapPlmattendancedevice)->sum("money");
					
					$mapPlmmaterialorder["plmid"]=$val["id"];
					$materialmoney=M("Plmmaterialorder")->where($mapPlmmaterialorder)->sum("price");
					
					$mapPlmorder2paytime["plmid"]=$val["id"];
					$othermoney=M("Plmorder2paytime")->where($mapPlmorder2paytime)->sum("money");
					
					$voList[$key]['chengben']=$workermoney+$devicemoney+$materialmoney+$othermoney;
					$voList[$key]['maoli']=$voList[$key]['backmoney']-$voList[$key]['chengben'];
					$voList[$key]['maolilv']=100*round($voList[$key]['maoli']/$voList[$key]['backmoney'],2)."%";
					
					$mapPlmoutputdaily["plmid"]=$val["id"];
					$mapPlmoutputdaily["pworktype"]=array("like","%热再生%");
					$voList[$key]['rezaisheng']=M("Plmoutputdaily")->where($mapPlmoutputdaily)->sum("value");
					
					
					$mapPlmmaterials["plmid"]=$val["id"];
					$mapPlmmaterials["name"]=array("like","%沥青%");
					$voList[$key]['liqingliao']=M("Plmmaterials")->where($mapPlmmaterials)->sum("money");
					
					$mapPlmmaterials["plmid"]=$val["id"];
					$mapPlmmaterials["name"]=array("like","%液化气%");
					$voList[$key]['yehuaqi']=M("Plmmaterials")->where($mapPlmmaterials)->sum("money");
					
					$voList[$key]['liqingliaounit']=round($voList[$key]['liqingliao']/$voList[$key]['rezaisheng'],2);
					$voList[$key]['yehuaqiunit']=round($voList[$key]['yehuaqi']/$voList[$key]['rezaisheng'],2);
					$voList[$key]['workerunit']=round($workermoney/$voList[$key]['rezaisheng'],2);
					//热再生面积 $voList[$key]['']
					
					
					$voList[$key]['shigongunit']=round($othermoney/$voList[$key]['rezaisheng'],2);
					
					
					$mapPlmorder2paytime1["plmid"]=$val["id"];
					$mapPlmorder2paytime1["classify"]=array("like","%应酬费%");
					$yingchou=M("Plmorder2paytime")->where($mapPlmorder2paytime1)->sum("money");
					
					$voList[$key]['yingchouunit']=round($yingchou/$voList[$key]['rezaisheng'],2);
				}
				$pay=number_format($pay,2);
				$total=number_format($total,2);
				
				
				//优惠总计
				$youhui1=M('plmmaterialtj')->where($map)->sum("yhje");
				$youhui2=M('plmmaterialtj')->where($map)->sum("yh2");
				$youhui=number_format($youhui1+$youhui2,2);
				$this->assign('youhui',$youhui);
				//保证金总计
				
				$mapforplmorder2[plm]=$plm;
				$mapforplmorder2[pay_baozheng_status]=1;
				$baozhengyifu=M('plmorder2')->where($mapforplmorder2)->sum("baozheng");
				$mapforplmorder2[pay_baozheng_status]=0;
				$baozhengweifu=M('plmorder2')->where($mapforplmorder2)->sum("baozheng");
				$youhui3 = M('plmorder2')->where($mapforplmorder2)->sum("yh2");
				$this->assign('youhui3',number_format($youhui3,2));
				$this->assign('baozhengyifu',number_format($baozhengyifu,2));
				$this->assign('baozhengweifu',number_format($baozhengweifu,2));
				
				/*
				//工程下单
				
				//$mapforplmorder2paytime[plm]=$plm;
				//$mapforplmorder2paytime[name]=array("in",$namesstr);
				//$classify=M('plmorder2paytime')->where($mapforplmorder2paytime)->group('name')->field('name')->select();
				//$allTotal=0;
				//$allPay=0;
				foreach ($names as $kk => $vv) {
					$gcprice=0;
					$gctotal=0;
					$mapforplmorder2paytime["approve"]=1;
					$mapforplmorder2paytime["plm"]=$plm;
					$mapforplmorder2paytime["name"]=$vv['name'];
					$mapforplmorder2paytime["del"]=0;
					$classifys=M('plmorder2paytime')->where($mapforplmorder2paytime)->select();
					foreach ($classifys as $key3 => $value3) {
						$gcprice+=$value3['oldpay'];//已付
						$gctotal+=$value3['pay'];//总款项
					}
					$voList[$key][price][$kk]['price']=$gcprice;
					$voList[$key][price][$kk]['total']=$gctotal;
					$allTotal+=$gctotal;
					$allPay+=$gcprice;
				}
				$allTotal=number_format($allTotal,2);//总款项
				$allPay=number_format($allPay,2);//已付
				
				$voList[$key][allTotal]=$allTotal;
				$voList[$key][allPay]=$allPay;
				
				$this->assign('total',$total);
				$this->assign('pay',$pay);
				$this->assign('allTotal',$allTotal);
				$this->assign('allPay',$allPay);
				$this->assign('material',$material);
				$this->assign('classify',$classify);
				$this->assign('plmid',$_REQUEST['plm']);
				*/
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
		
		
		$mapforplmmaterialtj['department']=array('in',array('材料部','市场部'));
		$mapforplmmaterialtj['status']=array('neq',20);
		$mapforplmmaterialtj['plm']=$plm;
		$mapforplmmaterialtj['type']=1;
		$mapforplmmaterialtj['tuihuo']=1;
		
		$plmmaterialtj=M('plmmaterialtj')->where($mapforplmmaterialtj)->select();
		
		
		foreach ($plmmaterialtj as $key1 => $value1) {
			$clids.=$value1[clid].",";
		}
		
		/*
		if($value1['status']==1){
			if($value['name']=="其他的"){
				$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->field("price,count")->select();
				foreach ($materials as $key2 => $value2) {
					$payprice+=$value2['price']*$value2['count'];
					$price+=$value2['price']*$value2['count'];
				}
			}else{
				$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->field("price,count")->select();
				foreach ($materials as $key2 => $value2) {
					$payprice+=$value2['price']*$value2['count'];
					$price+=$value2['price']*$value2['count'];
				}
			}

		}else{
			if($value['name']=='其他的'){
				$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->field("price,count")->select();
				foreach ($materials as $key2 => $value2) {
					$price+=$value2['price']*$value2['count'];
				}
			}else{
				$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->field("price,count")->select();
				foreach ($materials as $key2 => $value2) {
					$price+=$value2['price']*$value2['count'];
				}
			}

		}
		*/		
		
		$materials=M('plmmaterials')->where(array('id'=>array('in',$clids)))->select();
		foreach ($materials as $key2 => $value2) {
			$materials[$key2][total]=$value2['price']*$value2['count'];
			$allTotal+=$materials[$key2][total];
		}	
	
					
					
		$this->assign('total',$total);
		$this->assign('pay',$pay);
		$this->assign('allTotal',$allTotal);
		$this->assign('allPay',$allPay);
		$this->assign('material',$material);
		$this->assign('volist',$materials);
		$this->assign('plmid',$_REQUEST['plm']);
	
			
		$this->assign('orgdata', $vo);

	
		$this->display();
	}
		
}
?>