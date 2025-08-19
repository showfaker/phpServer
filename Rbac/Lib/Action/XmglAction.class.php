<?php
class XmglAction extends CommonAction {			
	public function index() {		
        if(empty($_REQUEST['tab'])){
			$_SESSION['tab']=$_REQUEST['tab'];
			$this->assign('tab',$_REQUEST['tab']);
		}
        if(!empty($_REQUEST['tab']))
		{			
			$_SESSION[tab]=$_REQUEST['tab'];			
			$this->assign('tab',$_REQUEST['tab']);
		}
		
		if(empty($_REQUEST['tab'])||$_REQUEST['tab']==1){
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}else{
				
			}
			$map[design_status]=array("neq","完成验收");
		}
		if($_REQUEST['tab']==2){
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}else{
				
			}
			$map[design_status]=array("eq","完成验收");
		}
		
		if(!empty($_REQUEST['city']))
		{
			$map['city'] = $_REQUEST['city'];
			$this->assign('city',$_REQUEST['city']);	
		}
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
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
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
			if($_REQUEST['city'])
			{
				$p->parameter .= "city=" . urlencode($_REQUEST['city']) . "&";
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
	
	public function draft()
	{
		$map[plmid]=$_REQUEST[id];
		$materials=M("gsmaterials")->where($map)->order("number asc")->select();
		$materialcount=M("gsmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		$this->display();
	}
	
	public function check()
	{
		$map[plmid]=$_REQUEST[id];
		$materials=M("gsmaterials")->where($map)->order("number asc")->select();
		$materialcount=M("gsmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brands",$brand);
		$this->display();
	}
	
	public function draft1()
	{
		
		if(!empty($_REQUEST['number']))
		{			
			$map[number]=array("like","%".$_REQUEST['number']."%");
			$con5[number]=array("like","%".$_REQUEST['number']."%");
			
			$this->assign('number',$_REQUEST['number']);
		}
		if(!empty($_REQUEST['name']))
		{			
			$map[name]=array("like","%".$_REQUEST['name']."%");
			$con5[name]=array("like","%".$_REQUEST['name']."%");			
			$this->assign('name',$_REQUEST['name']);
		}
		//预算
		$map[plmid]=$_REQUEST[id];
		$materials=M("gsmaterials")->where($map)->order("number asc")->select();
		// dump($materials);die;
		$brand=M("brand")->select();
		$this->assign("brands",$brand);
		$this->assign('id',$_REQUEST[id]);
		
		//采购
		$map1[plmid]=$_REQUEST[id];
		$map1[status]=1;
		$map1[type]=1;
		$order1=M("plmmaterialorder")->where($map1)->field("id")->select();
		$orderid1=array();
		foreach($order1 as $i=>$va){
			$orderid1[]=$va[id];
		}
		$con1[orderid]=array("in",$orderid1);
		$plmmaterials1=M("plmmaterials")->where($con1)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		
		$newplm1=array();
		foreach($plmmaterials1 as $i=>$va){
			$newplm1[$va[number]]=$va['sum(count)'];
			$newplm1[$va[name]][$va[standard]]=$va['sum(count)'];
		}
		//被转场
		$map2[plmid]=$_REQUEST[id];
		$map2[status]=1;
		$map2[type]=3;
		$order2=M("plmmaterialorder")->where($map2)->field("id")->select();
		$orderid2=array();
		foreach($order2 as $i=>$va){
			$orderid2[]=$va[id];
		}
		$con2[orderid]=array("in",$orderid2);
		$plmmaterials2=M("plmmaterials")->where($con2)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		$newplm2=array();
		foreach($plmmaterials2 as $i=>$va){
			$newplm2[$va[number]]=$va['sum(count)'];
			$newplm2[$va[name]][$va[standard]]=$va['sum(count)'];
		}
		
		//转场
		$map3[fromid]=$_REQUEST[id];
		$map3[status]=1;
		$map3[type]=3;
		$order3=M("plmmaterialorder")->where($map3)->field("id")->select();
		$orderid3=array();
		foreach($order3 as $i=>$va){
			$orderid3[]=$va[id];
		}
		$con3[orderid]=array("in",$orderid3);
		$plmmaterials3=M("plmmaterials")->where($con3)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		$newplm3=array();
		foreach($plmmaterials3 as $i=>$va){
			$newplm3[$va[number]]=$va['sum(count)'];
			$newplm3[$va[name]][$va[standard]]=$va['sum(count)'];
		}
		
		//退货
		$map4[plmid]=$_REQUEST[id];
		$map4[status]=1;
		$map4[type]=array("in","2,5");
		$order4=M("plmmaterialorder")->where($map4)->field("id")->select();
		$orderid4=array();
		foreach($order4 as $i=>$va){
			$orderid4[]=$va[id];
		}
		$con4[orderid]=array("in",$orderid4);
		$plmmaterials4=M("plmmaterials")->where($con4)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		$newplm4=array();
		foreach($plmmaterials4 as $i=>$va){
			$newplm4[$va[number]]=$va['sum(count)'];
			$newplm4[$va[name]][$va[standard]]=$va['sum(count)'];
		}
				
		foreach($materials as $i=>$pro){
			$count=0;
			//$materials[$i][sjcount]=$newplm1[$pro[number]]+$newplm2[$pro[number]]-$newplm3[$pro[number]]-$newplm4[$pro[number]];
			$materials[$i][sjcount]=$newplm1[$pro[name]][$pro[standard]]+$newplm2[$pro[name]][$pro[standard]]-$newplm3[$pro[name]][$pro[standard]]-$newplm4[$pro[name]][$pro[standard]];
			
			if($materials[$i][sjcount]>$materials[$i]["count"])
			{
				$materials[$i][sjcount]="<font style='color:red'>".$materials[$i][sjcount]."</font>";
			}
			
			$arraytemp[$pro[name]][$pro[standard]]=1;
		}
		
		
		$map5[plmid]=$_REQUEST[id];
		$map5[status]=1;
		$order5=M("plmmaterialorder")->where($map5)->field("id")->select();
		$orderid5=array();
		foreach($order5 as $i=>$va){
			$orderid5[]=$va[id];
		}
		$con5[orderid]=array("in",$orderid5);
		$plmmaterials5=M("plmmaterials")->where($con5)->group("name,standard")->field("number,name,standard,brand,unit,sum(count)")->select();
		$key=0;
		foreach($plmmaterials5 as $i=>$pro){
			if($arraytemp[$pro[name]][$pro[standard]]!=1)
			{
				$sjcount=$newplm1[$pro[name]][$pro[standard]]+$newplm2[$pro[name]][$pro[standard]]-$newplm3[$pro[name]][$pro[standard]]-$newplm4[$pro[name]][$pro[standard]];
				if(!empty($sjcount))
				{
					$materialsunbudget[$key][sjcount]="<font style='color:red'>".$sjcount."</font>";
					$materialsunbudget[$key][number]=$pro[number];
					$materialsunbudget[$key][brand]=$pro[brand];
					$materialsunbudget[$key][name]=$pro[name];
					$materialsunbudget[$key][standard]=$pro[standard];
					$materialsunbudget[$key][unit]=$pro[unit];
					$materialsunbudget[$key]["count"]=0;
					$key++;
				}
				
			}
			
		}
		
		$this->assign("materials",$materials);
		$this->assign("materialsunbudget",$materialsunbudget);		
		$this->display();
	}
	
	public function insert(){
		
		$map[plmid]=$_REQUEST[plmid];
		M("gsmaterials")->where($map)->delete();
		
		$id=$_REQUEST[plmid];
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		$data1[plmid]=$_POST[plmid];
		$data1[ctime]=time();
		if(!empty($id)){
			for($k=0;$k<count($_POST[para2]);$k++){
				$data1[brand]=M("brand")->where("id='".$_POST[para1][$k]."'")->getfield("name");
				$data1[brandid]=$_POST[para1][$k];
				$data1[number]=$_POST[para2][$k];
				$data1[name]=$_POST[para3][$k];
				$data1[standard]=$_POST[para4][$k];
				$data1[unit]=$_POST[para5][$k];
				//$data1[price]=$_POST[para6][$k];
				$data1["sort"]=$k;
				$data1[plmid]=$id;
				$data1[plm]=$plminfo['title'];
				M("gsmaterials")->add($data1);
			}
		}
				
		$this->redirect('index','tab=1');
	}

	public function toexcel(){
		// dump($_REQUEST['id']);die;
		//处理数据，得到$data数组
		$map[plmid]=$_REQUEST[id];
		$materials=M("gsmaterials")->where($map)->order("number asc")->select();
		// dump($materials);die;
		$brand=M("brand")->select();
		$this->assign("brands",$brand);
		$this->assign('id',$_REQUEST[id]);
		
		//采购
		$map1[plmid]=$_REQUEST[id];
		$map1[status]=1;
		$map1[type]=1;
		$order1=M("plmmaterialorder")->where($map1)->field("id")->select();
		$orderid1=array();
		foreach($order1 as $i=>$va){
			$orderid1[]=$va[id];
		}
		$con1[orderid]=array("in",$orderid1);
		$plmmaterials1=M("plmmaterials")->where($con1)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		
		$newplm1=array();
		foreach($plmmaterials1 as $i=>$va){
			$newplm1[$va[number]]=$va['sum(count)'];
			$newplm1[$va[name]][$va[standard]]=$va['sum(count)'];
		}
		//被转场
		$map2[plmid]=$_REQUEST[id];
		$map2[status]=1;
		$map2[type]=3;
		$order2=M("plmmaterialorder")->where($map2)->field("id")->select();
		$orderid2=array();
		foreach($order2 as $i=>$va){
			$orderid2[]=$va[id];
		}
		$con2[orderid]=array("in",$orderid2);
		$plmmaterials2=M("plmmaterials")->where($con2)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		$newplm2=array();
		foreach($plmmaterials2 as $i=>$va){
			$newplm2[$va[number]]=$va['sum(count)'];
			$newplm2[$va[name]][$va[standard]]=$va['sum(count)'];
		}
		
		//转场
		$map3[fromid]=$_REQUEST[id];
		$map3[status]=1;
		$map3[type]=3;
		$order3=M("plmmaterialorder")->where($map3)->field("id")->select();
		$orderid3=array();
		foreach($order3 as $i=>$va){
			$orderid3[]=$va[id];
		}
		$con3[orderid]=array("in",$orderid3);
		$plmmaterials3=M("plmmaterials")->where($con3)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		$newplm3=array();
		foreach($plmmaterials3 as $i=>$va){
			$newplm3[$va[number]]=$va['sum(count)'];
			$newplm3[$va[name]][$va[standard]]=$va['sum(count)'];
		}
		
		//退货
		$map4[plmid]=$_REQUEST[id];
		$map4[status]=1;
		$map4[type]=array("in","2,5");
		$order4=M("plmmaterialorder")->where($map4)->field("id")->select();
		$orderid4=array();
		foreach($order4 as $i=>$va){
			$orderid4[]=$va[id];
		}
		$con4[orderid]=array("in",$orderid4);
		$plmmaterials4=M("plmmaterials")->where($con4)->group("name,standard")->field("number,name,standard,sum(count)")->select();
		$newplm4=array();
		foreach($plmmaterials4 as $i=>$va){
			$newplm4[$va[number]]=$va['sum(count)'];
			$newplm4[$va[name]][$va[standard]]=$va['sum(count)'];
		}
				
		foreach($materials as $i=>$pro){
			$count=0;
			//$materials[$i][sjcount]=$newplm1[$pro[number]]+$newplm2[$pro[number]]-$newplm3[$pro[number]]-$newplm4[$pro[number]];
			$materials[$i][sjcount]=$newplm1[$pro[name]][$pro[standard]]+$newplm2[$pro[name]][$pro[standard]]-$newplm3[$pro[name]][$pro[standard]]-$newplm4[$pro[name]][$pro[standard]];
			
			if($materials[$i][sjcount]>$materials[$i]["count"])
			{
				$materials[$i][sjcount]="<font style='color:red'>".$materials[$i][sjcount]."</font>";
			}
			
			$arraytemp[$pro[name]][$pro[standard]]=1;
		}
		
		
		$map5[plmid]=$_REQUEST[id];
		$map5[status]=1;
		$order5=M("plmmaterialorder")->where($map5)->field("id")->select();
		$orderid5=array();
		foreach($order5 as $i=>$va){
			$orderid5[]=$va[id];
		}
		$con5[orderid]=array("in",$orderid5);
		$plmmaterials5=M("plmmaterials")->where($con5)->group("name,standard")->field("number,name,standard,brand,unit,sum(count)")->select();
		$key=0;
		foreach($plmmaterials5 as $i=>$pro){
			if($arraytemp[$pro[name]][$pro[standard]]!=1)
			{
				$sjcount=$newplm1[$pro[name]][$pro[standard]]+$newplm2[$pro[name]][$pro[standard]]-$newplm3[$pro[name]][$pro[standard]]-$newplm4[$pro[name]][$pro[standard]];
				if(!empty($sjcount))
				{
					$materialsunbudget[$key][sjcount]=$sjcount;
					$materialsunbudget[$key][number]=$pro[number];
					$materialsunbudget[$key][brand]=$pro[brand];
					$materialsunbudget[$key][name]=$pro[name];
					$materialsunbudget[$key][standard]=$pro[standard];
					$materialsunbudget[$key][unit]=$pro[unit];
					$materialsunbudget[$key]["count"]=0;
					$key++;
				}
				
			}
			
		}
		// dump($materialsunbudget);die;

		// $list1 = M('store')->where($map)->order('id desc')->select();
		foreach($materialsunbudget as $k=>$v){
			// $data[$k]['type_id'] = $v['type_id'];
			$data[$k]['number'] = $v['number'];
			$data[$k]['brand'] = $v['brand'];
			
			$data[$k]['name'] = $v['name'];
			$data[$k]['standard'] = $v['standard'];
			$data[$k]['unit'] = $v['unit'];
			$data[$k]['count'] = $v['count'];
			$data[$k]['sjcount'] = $v['sjcount'];

		}
		$file="供给情况表";
		$title="供给情况表";
		$subtitle='';
		$excelname="供给情况表";
		// $th_array=array('物品类型ID','物品名称','规格型号','供应商','未使用','使用中','送洗中','总数量');
		$th_array=array('材料编号','材料类别','材料名称','规格型号','单位','预算','数量');
		$this->createExel($file,$title,$subtitle,$th_array,$data,$excelname);

	}
		
}
?>