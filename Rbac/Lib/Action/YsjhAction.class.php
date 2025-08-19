<?php
class YsjhAction extends CommonAction {			
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
        if($_REQUEST[tab]==""||$_REQUEST[tab]=="1"){
        	$model = M("plmmaterialtj");
        	if(!empty($_REQUEST[plm])){
				$map[plm]=$_REQUEST[plm];
				$this->assign("plm",$_REQUEST[plm]);
			}
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}
			if(!empty($_REQUEST[supplier])){
				$map[supplier]=$_REQUEST[supplier];
				$this->assign("supplier",$_REQUEST[supplier]);
			}
			if($_SESSION['position']=='274' || $_SESSION['position']=='226'){
				$map['approve']=1;
			}else{
				//$map[sqr]=$_SESSION[loginUserName];
				if($_SESSION[account]!="hukeke")
				{
					$map['sqr']=$_SESSION[loginUserName];
				}
				else
				{
					$map['sqr']=array("in","胡可可,黄滇,李雯,陈心巧");
				}
			}
			$map[status]=-1;
			$map[type]=1;
			if (!empty($model)) {
				$this->_list($model, $map,'plm',false,$_REQUEST[supplier],$city);
			}
			
			$data[status]=-1;
			$data[type]=1;
			$array=M('plmmaterialtj')->where($data)->group('supplier')->select();
			$cities=M("cities")->select();
			$this->assign("cities",$cities);
			// $new_map['design_status']=array('neq','完成验收');
			$new_map[status]=-1;
			$project=M("plmmaterialtj")->where($new_map)->group('plm')->field("plm")->select();
			$this->assign("project",$project);		
			$this->assign("suppliers",$array);
        }
        if($_REQUEST[tab]=='2'){
        	$model = M("plmorder2paytime");
        	if(!empty($_REQUEST[plm])){
				$map[plm]=$_REQUEST[plm];
				$this->assign("plm",$_REQUEST[plm]);
			}
			if(!empty($_REQUEST[city])){
				$map[city]=$_REQUEST[city];
				$this->assign("city",$_REQUEST[city]);
			}
			if(!empty($_REQUEST[supplier])){
				$map[supplier]=$_REQUEST[supplier];
				$this->assign("supplier",$_REQUEST[supplier]);
			}
			if($_SESSION['position']=='274' || $_SESSION['position']=='226'){
			}else{
				//$map[user]=$_SESSION[loginUserName];
				if($_SESSION[account]!="hukeke")
				{
					$map['user']=$_SESSION[loginUserName];
				}
				else
				{
					$map['user']=array("in","胡可可,黄滇,李雯,陈心巧");
				}
			}
			$map[paytime]=array('eq','');
			$map[approve]=1;

			if (!empty($model)) {
				$this->_list2($model, $map,'orderid',false);
			}
			$map1[paytime]=array('eq','');
			$map1[approve]=1;
			$array=M('plmorder2paytime')->where($map1)->group('supplier')->select();
			$cities=M('plmorder2paytime')->where($map1)->group('city')->select();
			$this->assign("cities",$cities);
			$project=M('plmorder2paytime')->where($map1)->group('plm')->field("plm")->select();
			$this->assign("project",$project);		
			$this->assign("suppliers",$array);
        }	
		$this->display();
	}
	
	public function _list($model, $map, $sortBy = '', $asc = false) {
    	
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
        $count = $model->where($map)->group('orderid')->select();
        $count=count($count);
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
            $voList = $model->where($map)->group('orderid')->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            $totalall=0;
            foreach ($voList as $key => $value) {
            	$con[status]=-1;
            	$con[type]=1;
            	$con[orderid]=$value[orderid];
            	if(!empty($map[supplier])){
            		$con[supplier]=$value[supplier];
            	}
            	$info=M('plmmaterialtj')->where($con)->select();
            	$info2=M('plmmaterialorder')->where(array('id'=>$value[orderid]))->find();
            	$total=0;
            	$tj_id='';
            	$supplier='';
            	foreach ($info as $key2 => $value2) {
            		$total+=$value2[total]+$value2[yf]-$value2[yhje];
            		$tj_id.=$value2['id'].',';
            		$supplier.=$value2['supplier'].',';
            	}
            	$voList[$key][total]=$total;
            	$voList[$key][plm]=$info2[plm];
            	$voList[$key][tj_id]=$tj_id;
            	$voList[$key][supplier]=trim($supplier,',');
            	$voList[$key][ctime]=date('Ymd',$info2[ctime]).$value[orderid];
            }
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
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
            $this->assign('totalall', $totalall);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        return;
    }
	public function _list2($model, $map, $sortBy = '', $asc = false,$tab,$arr) {
    	
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
        $count = count($model->where($map)->group('orderid')->select());
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
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->group('orderid')->limit($p->firstRow . ',' . $p->listRows)->select();
            foreach ($voList as $key => $value) {
            	$total=0;
            	$con['paytime']=array('eq','');
            	$con['orderid']=$value[orderid];
            	$info=M('plmorder2paytime')->where($con)->select();
            	foreach ($info as $key2 => $value2) {
            		$total+=$value2['pay'];//没有付款日期
            	}
            	$voList[$key]['total']=$total;
            	if($_SESSION[account]=='admin'||$name=="财务经理" || $_SESSION['dept']=="财务部" || $_SESSION['loginUserName']==$voList[$key][user]){
					$voList[$key][limit_upload]=1;
				}
				$order=M('plmorder2')->find($value[orderid]);
				$voList[$key][price]=$order[price];
				$voList[$key][order]=date('Ymd',$order[ctime]).$value[orderid];
            }
            //分页跳转的时候保证查询条件
        
            foreach ($map as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
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
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
            $this->assign('all_total',$all_total);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
	public function insert(){
		$id=$_REQUEST['tj_id'];
		$id2=explode(',',$id);
		$ids=array();
		foreach ($id2 as $key => $value) {
			if(!empty($value)){
				$ids[]=$value;
			}
		}
		$map[id]=array("in",$ids);
		$data[enddate]=$_POST[enddate];
		$data[status]=0;
		$res1=M("plmmaterialtj")->where($map)->save($data);
		$this->redirect("index");
	}
	
	public function check2()
	{
		$plmmaterialtj=M('plmmaterialtj')->where(array('id'=>$_REQUEST['id']))->find();
		$map[status]=-1;
		$map[type]=1;
		$map[orderid]=$plmmaterialtj[orderid];
		if(!empty($_REQUEST[supplier])){
			$map[supplier]=$_REQUEST[supplier];
		}
		$tj=M('plmmaterialtj')->where($map)->select();
		$materials=array();
		foreach ($tj as $key => $value) {
			$arr=explode(',',$value[clid]);
			foreach($arr as $key2=>$value2){
				if(!empty($value2)){
					$materials[]=M("Plmmaterials")->where(array('id'=>$value2))->find();
				}
			}
		}
		foreach($materials as $i=>$va){
			$materials[$i][total]=$va[price]*$va[count];
		}
		$this->assign("materials",$materials);
		$this->display();
	}
	public function check3(){
		// $firstid=$_REQUEST[firstid];
		// $info=M('plmorder2paytime')->find($firstid);
		// $id=$info[orderid];
		$id=$_REQUEST['orderid'];
		$order=M('plmorder2')->where(array('id'=>$id))->find();
		$order_paytime=M('plmorder2paytime')->where(array('orderid'=>$id))->select();
		$this->assign('info',$order);
		$this->assign('order_paytime',$order_paytime);
		$newname=$order['newname'];
		if(!empty($newname)){
			$newname=explode(',',$newname);
			$this->assign('newname',$newname);
		}
		$this->display();
	}
	// public function delete()
	// {
	// 	$plmmaterialtj=M('plmmaterialtj')->where(array('id'=>$_REQUEST['id']))->find();
	// 	$map[status]=-1;
	// 	$map[type]=1;
	// 	$map[orderid]=$plmmaterialtj[orderid];
	// 	if(!empty($_REQUEST[supplier])){
	// 		$map[supplier]=$_REQUEST[supplier];
	// 	}
	// 	$tj=M('plmmaterialtj')->where($map)->delete();
	// 	if($tj){
	// 		echo 1;
	// 	}else{
	// 		echo 2;
	// 	}
	// }

	public  function ajaxcheck(){
		$id=$_REQUEST['id'];
		$id2=explode(',',$id);
		$ids=array();
		foreach ($id2 as $key => $value) {
			if(!empty($value)){
				$ids[]=$value;
			}
		}
		
		echo 1;
	}
	//设置时间
	public function settime(){
		// dump(111111);die;
		$id=$_REQUEST[id];
		$order=M('plmorder2')->find($id);
		$order_paytime=M('plmorder2paytime')->where(array('orderid'=>$id))->order('ctime')->select();
		// dump($order_paytime);die;
		$this->assign('order',$order);
		$this->assign('order_paytime',$order_paytime);
		$this->display();

	}
	public function settimeSubmit(){
		$id=$_REQUEST[id];
		$info=M('plmorder2paytime')->find($id);
		$orderid=$info[orderid];
		$count=$info[count];
		$total=0;
		foreach ($_REQUEST['time'] as $key => $value) {
			if(!empty($value)){
				$data[orderid]=$info[orderid];
				$data[city]=$info[city];
				$data[plm]=$info[plm];
				$data[name]=$info[name];
				$data[supplier]=$info[supplier];
				$data[status]=$info[status];
				$data[type]=$info[type];
				$data[user]=$info[user];
				$data[ctime]=time();
				$data[approve]=$info[approve];
				$data[department]=$info[department];
				$data[paytime]=$value;
				$data[count]=$count;
				$data[pay]=$_REQUEST['price'][$key];
				$res=M('plmorder2paytime')->add($data);
				$count++;
				$total+=$_REQUEST['price'][$key];
			}
		}
		//剩余的
		$data[orderid]=$info[orderid];
		$data[city]=$info[city];
		$data[plm]=$info[plm];
		$data[name]=$info[name];
		$data[supplier]=$info[supplier];
		$data[status]=$info[status];
		$data[type]=$info[type];
		$data[user]=$info[user];
		$data[ctime]=time();
		$data[approve]=$info[approve];
		$data[department]=$info[department];
		$data[paytime]='';
		$data[count]=$count;
		$data[pay]=$info[pay]-$total;
		if ($data[pay]!='0') {
			$res=M('plmorder2paytime')->add($data);
		}
		// $res=M('plmorder2paytime')->add($data);
		$res1=M('plmorder2paytime')->where(array('id'=>$id))->delete();
		//修改订单下次付款时间
		$list=M('plmorder2paytime')->where(array('orderid'=>$orderid,'status'=>0,'paytime'=>array('neq','')))->select();
		$nexttime='9999-12-31';//修改下次付款时间
		foreach ($list as $key => $value) {
			if($nexttime>$value['paytime']){
				$nexttime=$value[paytime];
			}
		}
		if($nexttime=='9999-12-31'){
			$nexttime="未设置";
		}
		$res2=M('plmorder2')->where(array('id'=>$orderid))->save(array('nexttime'=>$nexttime));
		$this->success('设置时间成功.');
	}
	
}
?>
