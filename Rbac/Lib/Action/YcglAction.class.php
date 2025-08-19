<?php
class YcglAction extends CommonAction {			
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
		
		if($_REQUEST['tab']==2){
			$gsmaterials=M("gsmaterials")->field("number,plmid,count")->select();
			$plmmatetials=M("plmmatetials")->field("number,plmid,count")->select();
			$plmid='';
			foreach($gsmaterials as $i=>$pro){
				$count=0;
                foreach($plmmatetials as $k=>$plm){
					if($plm[plmid]==$pro[plmid]&&$plm[number]==$pro[number]){
						$count+=$plm[count];
					}
				}
				if($count>$pro[count]){
					$plmid.=$pro[plmid].",";
				}
				
			}			
			$map[id]=array("in",$plmid);
			
			$name = "Project";
			$model = D($name);
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}
			if (!empty($model)) {
				$this->_list1($model, $map,'create_time',false);
			}
		}
		if($_REQUEST['tab']==3){
			$supplier=M("supplier")->field("id,supplier")->select();
			$this->assign("supplier",$supplier);			
			$model = M("resupplier");
			if(!empty($_REQUEST[gysid])){
				$map[supplierid]=$_REQUEST[gysid];
				$this->assign("gysid",$_REQUEST[gysid]);
			}
			if (!empty($model)) {
			    $this->_list($model, $map,'ctime',false);
		    } 
		}
		
        if($_REQUEST['tab']==4){
			$map[type]=array("in","3,5");
			$model = M("plmmaterialorder");
			if(!empty($_REQUEST[type])){
				$map[type]=$_REQUEST[type];
				$this->assign("type",$_REQUEST[type]);
			}	
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}
			if (!empty($model)) {
			    $this->_list($model, $map,'ctime',false);
		    } 
		}
		if($_REQUEST['tab']==6){
			$model = M("Plmmaterialorder");
			$map[status]=0;
			$map[type]=array("in","3,5");
			if($_SESSION[account]!='admin'){
					$map[creater]=array("neq",$_SESSION[account]);
				}
			if(!empty($_REQUEST[type])){
				$map[type]=$_REQUEST[type];
				$this->assign("type",$_REQUEST[type]);
			}	
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}
			if (!empty($model)) {
				$this->_list($model, $map,'ctime',false);
			}
		}
		
		if($_REQUEST['tab']==7){
			$supplier=M("supplier")->where("status=1")->field("id,supplier")->select();
			$this->assign("supplier",$supplier);
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
	
	protected function _list1($model, $map, $sortBy = '', $asc = false) {
    	
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
	
	public function tuihuo()
	{
		$map[plmid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("sort asc")->select();
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);		
		
		$project=M("project")->where("id=".$_REQUEST[id])->find();
		$this->assign("project",$project);
		
		$this->display();
	}
	
	public function insert(){
		$plminfo=M("project")->where("id=".$_POST[plmid])->find();//余料项目
		$plminfo1=M("project")->where("id=".$_POST[plmid1])->find();//转场项目			
		$data["type"]=3;//1 采购下单 2 退回  3 转场  4 待提交  5 项目退货			
		$data[fromid]=$plminfo[id];
		$data[form]=$plminfo[title];
		$time=time();
		$data[city]=$plminfo1[city];
		$data[plmid]=$plminfo1[id];
		$data[plm]=$plminfo1[title];
		$data[status]=0;//下单待审核
		$data[ctime]=$time;
		$data[user]=$_SESSION[name];
		$data[userid]=$_SESSION[number];				
		$data[price]=$_POST[price];
		dump($data);
		$id=M("plmmaterialorder")->add($data);
		if(!empty($id)){
			for($k=0;$k<count($_POST[para1]);$k++){
				if(($_POST[para8][$k]!="0")&&(!empty($_POST[para8][$k])))
				{
					//$data1[brandid]=M("brand")->where("name='".$_POST[para3][$k]."'")->getfield("id");
					$data1[brand]=$_POST[para3][$k];
					//$data1[supplierid]=M("brand")->where("name='".$_POST[para6][$k]."'")->getfield("id");
					$data1[supplier]=$_POST[para6][$k];
					$data1[number]=$_POST[para1][$k];
					$data1[name]=$_POST[para2][$k];
					$data1[standard]=$_POST[para4][$k];
					$data1[unit]=$_POST[para5][$k];
					$data1[price]=$_POST[para7][$k];
					$data1["count"]=$_POST[para8][$k];
					$data1[plmid]=$plminfo1[id];
					$data1[plm]=$plminfo1[title];
					$data1[orderid]=$id;
					$data1[ctime]=$time;
					$data1["sort"]=$k;
					
					M("plmmaterials")->add($data1);
				}
			}
		}
				
		$this->redirect('index','tab=4');
	}
	
	public function zhuanchang()
	{
		$map[plmid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("sort asc")->select();
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);		
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$map1[id]=array("neq",$_REQUEST[id]);
		$project=M("project")->where($map1)->select();
		$this->assign("projects",$project);
		
		$this->display();
	}
	
	public function update(){
		$plminfo=M("project")->where("id=".$_POST[plmid])->find();		
		$time=time();
		$data[city]=$plminfo[city];
		$data[plmid]=$plminfo[id];
		$data[orderid]=$_POST[orderid];
		$data[hkfs]=$_POST[hkfs];
		$data[status]=0;//下单待审核
		$data[ctime]=$time;
		$data[user]=$_SESSION[name];
		$data[userid]=$_SESSION[number];
		$data["type"]=5;//1 采购下单 2 退回  3 转场  5余料退货
		$data[plm]=$plminfo[title];
		$data[price]=$_POST[price];
		$id=M("plmmaterialorder")->add($data);
		if(!empty($id)){
			for($k=0;$k<count($_POST[para1]);$k++){
				if(($_POST[para8][$k]!="0")&&(!empty($_POST[para8][$k])))
				{
					//$data1[brandid]=M("brand")->where("name='".$_POST[para1][$k]."'")->getfield("id");
					$data1[brand]=$_POST[para3][$k];
					//$data1[supplierid]=M("supplier")->where("name='".$_POST[para6][$k]."'")->getfield("id");
					$data1[supplier]=$_POST[para6][$k];
					$data1[number]=$_POST[para1][$k];
					$data1[name]=$_POST[para2][$k];
					$data1[standard]=$_POST[para4][$k];
					$data1[unit]=$_POST[para5][$k];
					$data1[price]=$_POST[para7][$k];
					$data1["count"]=$_POST[para8][$k];
					$data1[plmid]=$data[plmid];
					$data1[plm]=$plminfo[title];
					$data1[orderid]=$id;
					$data1[ctime]=$time;
					$data1["sort"]=$k;
					
					M("plmmaterials")->add($data1);
				}
			}
		}
		
				
		$this->redirect('index','tab=4');
	}	
	
	public function ajaxcity(){
		// $map[design_status]=array('neq','完成验收');
		$map['city']=$_POST[city];
		$projects=M("Project")->where($map)->field("id,title")->select();
	    echo json_encode($projects);
	}
	
	public function ajaxplm(){
		$plmmaterialorder=M("plmmaterialorder")->where("plmid='".$_POST[plmid]."'")->field("id")->select();
	    echo json_encode($plmmaterialorder);
	}
	
	public function ajaxorder(){
		$materials=M("plmmaterials")->where("orderid='".$_POST[orderid]."'")->order("number asc,supplier asc")->field("id,number,name,brand,standard,unit,supplier,price")->select();
		echo json_encode($materials);
	}
	
	public function approve()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		$user=M("user")->where("status=1")->field("id,nickname")->select();
		$this->assign("user",$user);
		
		$this->display();
	}
	
	public function approve1()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		$user=M("user")->where("status=1")->field("id,nickname")->select();
		$this->assign("user",$user);
		
		$this->display();
	}
	
	public function approvesubmit(){
		$data[id]=$_POST[id];
		if($_REQUEST[result]=="同意")
		{
			$data[status]=1;
		}
		else
		{
			$data[status]=-1;
		}
		$data[approver]=$_SESSION[name];
		$data[approve_time]=time();
		M("Plmmaterialorder")->save($data);
		
		$this->redirect('index','tab=4');
	}
	
	public function ajaxdelete(){
		M("plmmaterials")->where("orderid='".$_POST[id]."'")->delete();
		M("plmmaterialorder")->where("id='".$_POST[id]."'")->delete();
		echo 1;
	}
	
	public function check()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		
		$this->display();
	}
	
	public function check1()
	{
		$map[orderid]=$_REQUEST[id];
		$materials=M("Plmmaterials")->where($map)->order("supplier asc")->select();
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$materialcount=M("Plmmaterials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);
		
		$orderinfo=M("Plmmaterialorder")->where("id=".$_REQUEST[id])->find();
		$this->assign("orderinfo",$orderinfo);
		
		
		$this->display();
	}
	
	public function suppliersub(){
		$data[remark]=$_POST[remark];
		$data[cause]=$_POST[cause];
		$data[supplierid]=$_POST[gysid];
		$data[ctime]=time();
		M("resupplier")->add($data);
		$this->redirect('index','tab=3');
	}
	
	public function editsupplier()
	{
		$map[supplierid]=$_REQUEST[id];
		$materials=M("materials")->where($map)->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$this->assign("supplierinfo",$supplierinfo);
		
		$this->display();
	}
	
	public function supplierapprove(){
		$orderinfo=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$this->assign("orderinfo",$orderinfo);
		$this->display();
	}
	
	public function supplierdelete(){
		
		if($_REQUEST[result]=="同意"){
			$data[status]=2;
			M("materails")->where("supplierid='".$_POST[id]."'")->setfield("status",1);
		}else{
			$data[status]=4;//删除退回
		}
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
	}
	
	public function supplieradd(){
		$orderinfo=M("supplier")->where("id='".$_REQUEST[id]."'")->find();
		$this->assign("orderinfo",$orderinfo);
		$this->display();
	}
	
	public function suppliersub1(){
		$data[remark]=$_POST[remark];
		$data[cause]=$_POST[cause];
		$data[status]=3; //供应商删除审核
		M("supplier")->where("id='".$_POST[id]."'")->save($data);
		$this->redirect('index','tab=3');
	}

	public function choice()
	{
		if(!empty($_REQUEST[number])){
			$map[number]=array("like","%".$_REQUEST[number]."%");
		}
		if(!empty($_REQUEST[name])){
			$map[name]=array("like","%".$_REQUEST[name]."%");
		}
		if(!empty($_REQUEST[city])){
			$where[city]=array("like","%".$_REQUEST[city]."%");
			if(!empty($_REQUEST[supplier])){
				$where[supplier]=array("like","%".$_REQUEST[supplier]."%");
			}
			$where[status]=1;
			$supplier=M("supplier")->where($where)->field("supplier")->select();
			
			foreach($supplier as $i=>$va){
				$name[$i]=$va[supplier];
			}			
			$suppliers=array("in",$name);
		}
		$map[supplier]=$suppliers;
		$model = M("materials");
		$materials=$model->where($map)->order("number asc,price asc")->select();
		foreach($materials as $k=>$val){			
			// $price[]=$plmtemp[$val[number]];
			$info=M('material')->where(array('number'=>$val['number']))->find();
			if (!empty($info)) {
				$materials[$k][lowprice]=$info[min];
				if(empty($info[price2])){
					$materials[$k][threeprice]=$info[price1];
				}
				if (!empty($info[price2]) && empty($info[price3])) {
					$materials[$k][threeprice]=$info[price1].','.$info[price2];
				}
				if(!empty($info[price3])){
					$materials[$k][threeprice]=$info[price1].','.$info[price2].','.$info[price3];
				}

				
			}else{
				$materials[$k][lowprice]='无';
				$materials[$k][threeprice]='无';

			}
		}
		$this->assign("materials",$materials);
		$this->assign("trkey",$_REQUEST[key]);
		$this->display();
	}
	
	
		
}
?>