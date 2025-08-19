<?php
class CwtjAction extends CommonAction {			
	public function index() {
		$cities=M("cities")->field("city")->select();
		$this->assign("cities",$cities);
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
			if(!empty($_REQUEST[tjdate])){
				$map[enddate]=$_REQUEST[tjdate];
				$this->assign("tjdate",$_REQUEST[tjdate]);
			}
			if(!empty($_REQUEST[gys])){
				$map[supplier]=$_REQUEST[gys];
				$this->assign("gys",$_REQUEST[gys]);
			}
			if(!empty($_REQUEST[sqr])){
				$map[sqr]=array('like','%'.$_REQUEST[sqr]."%");
				$this->assign("sqr",$_REQUEST[sqr]);
			}
            $map[status]=array("not in","-1,1,3,20");//,0.5
            $map[type]=1;
            $map[approve]=1;
            if(!($_SESSION['account']=='admin' || $_SESSION['department']==7)){ //批量付款权限
            	$name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
				if($name=="材料经理"){
					$map[department]='材料部';
				}elseif($name=="市场部总监"){
					$map[department]='市场部';
				}else{
					if($_SESSION[account]=="hukeke")
					{
						$map['sqr']=array("in","胡可可,黄滇,李雯,陈心巧");
					}
					else if($_SESSION[account]=="songlingbo")
					{
						
					}
					else
					{
						$map['sqr']=$_SESSION[loginUserName];
					}
				}
            }
            if($_SESSION['account']=='admin' || $_SESSION['department']==7){ //批量付款权限
            	$quanxian=1;
            }else{
            	$quanxian=0;
            }	
            $this->assign('quanxian',$quanxian);
			//dump($map);
			if (!empty($model)) {
				$this->_list1($model, $map,'enddate',false,1,$_REQUEST[sqr]);
			}
			$supplier=M("plmmaterialtj")->where("type=1")->group("supplier")->field("supplier")->select();
		    $this->assign("supplier",$supplier);
		}
		
		if($_REQUEST[tab]=="2"){
			$this->assign("tab",$_REQUEST[tab]);
			if(!empty($_REQUEST['gys'])){
				$map['supplier']=$_REQUEST['gys'];
				$this->assign('gys',$_REQUEST['gys']);
			}
			if(!empty($_REQUEST['tjdate'])){
				$map['enddate']=$_REQUEST['tjdate'];
				$this->assign('tjdate',$_REQUEST['tjdate']);
			}
			$map['tuihuo']=2;
			$map['type']=2;
			$map['status']=0;
			$model=M('plmmaterialtj');
			$arr=array();
			$arr['gys']=$_REQUEST['gys'];
			$arr['tjdate']=$_REQUEST['tjdate'];
			if (!empty($model)) {
				$this->_list5($model, $map,'ctime',false,$arr);
			}
			$map_th['tuihuo']=2;
			$map_th['type']=2;
			$map_th['status']=0;
			$supplier=M('plmmaterialtj')->where($map_th)->field('supplier')->group('supplier')->select();
			if($_SESSION['account']=='admin' || $_SESSION['department']==7){ //权限
            	$quanxian=1;
            }else{
            	$quanxian=0;
            }	
            $this->assign('quanxian',$quanxian);
			$this->assign('supplier',$supplier);
		}
		
        if($_REQUEST[tab]=="3"){
			$this->assign("tab",$_REQUEST[tab]);		
			$model = M("plmmaterialtj");
			// $info=$model->select();
			// foreach ($info as $key => $value) {
			// 	$supplier=M('supplier')->where(array('supplier'=>$value['supplier']))->find();
			// 	if(!empty($supplier)){
			// 		$data[id]=$value[id];
			// 		$data[bankaccount]=$supplier[bankname];
			// 		$data[banknumber]=$supplier[bankaccount];
			// 		$data[bankaddress]=$supplier[bankhu]; 
			// 		$res=$model->save($data);
			// 	}
			// }
			if(!empty($_REQUEST[gys])){
				$map[supplier]=$_REQUEST[gys];
				$this->assign("gys",$_REQUEST[gys]);
			}
			if(!empty($_REQUEST[city])){
				$title=M("project")->where("city='".$_REQUEST[city]."'")->field("title")->select();
				foreach($title as $va){
					$plm[]=$va[title];
				}
				$map[plm]=array("in",$plm);
				$this->assign("city",$_REQUEST[city]);
			}
			if(!empty($_REQUEST[xm])){
				$map[plm]=$_REQUEST[xm];
				$this->assign("xm",$_REQUEST[xm]);
			}
			if(!empty($_REQUEST[sqr])){
				$map[sqr]=array('like','%'.$_REQUEST[sqr]."%");
				$this->assign("sqr",$_REQUEST[sqr]);
			}
			if(!empty($_REQUEST[spare])){
				if($_REQUEST[spare]==1){
					$map[spare]=0;
				}
				if($_REQUEST[spare]==2){
					$map[spare]=1;
				}
				$this->assign("spare",$_REQUEST[spare]);
			}
			if(!($_SESSION['account']=='admin' || $_SESSION['department']==7)){ //批量付款权限
            	$name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
            	// dump($name);
				if($name=="材料经理"){
					$map[department]='材料部';
				}elseif($name=="市场部总监"){
					$map[department]='市场部';
				}else{
					//$map['sqr']=$_SESSION[loginUserName];
					if($_SESSION[account]=="hukeke")
					{
						$map['sqr']=array("in","胡可可,黄滇,李雯,陈心巧");
					}
					else if($_SESSION[account]=="songlingbo")
					{
						
					}
					else
					{
						$map['sqr']=$_SESSION[loginUserName];
					}
				}
            }

            $map[status]=1;
            $map[type]=1; 
            $map[approve]=1;
            $arr=array();
            $arr[]=$_REQUEST[gys];
            $arr[]=$_REQUEST[city];
            $arr[]=$_REQUEST[xm];
            $arr[]=$_REQUEST[sqr];
            $arr[]=$_REQUEST[spare];

			if (!empty($model)) {
				$this->_list3($model, $map,'ctime',false,3,$arr);
			}
			if($_SESSION['account']=='admin' || $_SESSION['department']==7){ //baoxiao权限
            	$quanxian=1;
            }else{
            	$quanxian=0;
            }	
            $this->assign('quanxian',$quanxian);
			$plm=M("plmmaterialtj")->where("type=1")->group("plm")->field("plm")->select();
			$this->assign("plm",$plm);
			$supplier=M("plmmaterialtj")->where("type=1")->group("supplier")->field("supplier")->select();
		    $this->assign("supplier",$supplier);
        } 
        if($_REQUEST[tab]=="7"){
			$this->assign("tab",$_REQUEST[tab]);		
			$model = M("plmmaterialtj");
			if(!empty($_REQUEST[update]) && empty($_REQUEST[enddate])){
				$time=strtotime($_REQUEST[update]);
				$map[zf_time]=array('egt',$time);
				$this->assign("update",$_REQUEST[update]);
			}
            if(!empty($_REQUEST[enddate]) && empty($_REQUEST[update])){
            	$time=strtotime($_REQUEST[enddate]);
				$map[zf_time]=array('elt',$time);
				$this->assign("enddate",$_REQUEST[enddate]);
			}
			if(!empty($_REQUEST[enddate]) && !empty($_REQUEST[update])){
            	$time1=strtotime($_REQUEST[update]);
            	$time2=strtotime($_REQUEST[enddate])+3600*24;
				$map[zf_time]=array(array('egt',$time1),array('elt',$time2),'and');
				$this->assign("update",$_REQUEST[update]);
				$this->assign("enddate",$_REQUEST[enddate]);
			}
			if(!empty($_REQUEST[sqr])){
				$map[sqr]=array('like','%'.$_REQUEST[sqr]."%");
				$this->assign("sqr",$_REQUEST[sqr]);
			}
            $map[status]=20;
            $map[type]=1; 
            $map[approve]=1;           			
			if (!empty($model)) {
				$this->_list($model, $map,'zf_time',false,7,$_REQUEST[update],$_REQUEST[enddate],$_REQUEST[sqr]);
			}
			$plm=M("plmmaterialtj")->where($map)->group("plm")->field("plm")->select();
			$this->assign("plm",$plm);
			$supplier=M("plmmaterialtj")->where("type=1")->group("supplier")->field("supplier")->select();
		    $this->assign("supplier",$supplier);
        } 
        if($_REQUEST[tab]=="4"){
			$this->assign("tab",$_REQUEST[tab]);
			if(!empty($_REQUEST['gys'])){
				$map['supplier']=$_REQUEST['gys'];
				$this->assign('gys',$_REQUEST['gys']);
			}
			if(!empty($_REQUEST['tjdate'])){
				$map['enddate']=$_REQUEST['tjdate'];
				$this->assign('tjdate',$_REQUEST['tjdate']);
			}
			$map['tuihuo']=2;
			$map['type']=2;
			$map['status']=1;
			$model=M('plmmaterialtj');
			$arr=array();
			$arr['gys']=$_REQUEST['gys'];
			$arr['tjdate']=$_REQUEST['tjdate'];
			if (!empty($model)) {
				$this->_list5($model, $map,'ctime',false,$arr);
			}
			$map_th['tuihuo']=2;
			$map_th['type']=2;
			$map_th['status']=1;
			$supplier=M('plmmaterialtj')->where($map_th)->field('supplier')->group('supplier')->select();
			$this->assign('supplier',$supplier);		
			
        }

        if($_REQUEST[tab]=="5"){
			$this->assign("tab",$_REQUEST[tab]);
			$model = M("plmmaterialtj");			
			if(!empty($_REQUEST[tjdate])){
				$map[enddate]=$_REQUEST[tjdate];
				$this->assign("tjdate",$_REQUEST[tjdate]);
			}
			if(!empty($_REQUEST[fkzt])){
				if($_REQUEST[fkzt]==1){
					$map['dj_type']=1;
					$this->assign("fkzt",1);
				}else{
					$map['dj_type'] =array('eq','0');
					if($_REQUEST[fkzt]==3){
						$map[file_status]=1;
						$this->assign("fkzt",3);
					}else{
						$map[file_status]=array('eq','0');
						$this->assign("fkzt",2);
					}
					
				}
				  
			}
			if(!empty($_REQUEST[gys])){
				$map[supplier]=$_REQUEST[gys];
				$this->assign("gys",$_REQUEST[gys]);
			}
			if(!empty($_REQUEST[sqr])){
				$map[sqr]=$_REQUEST[sqr];
				$this->assign("sqr",$_REQUEST[sqr]);
			}
            $map[status]=3;
            $map[type]=1;	
            $map[approve]=1;
            $arr2=array();
            $arr2[]=$_REQUEST[tjdate];
            $arr2[]=$_REQUEST[fkzt];
            $arr2[]=$_REQUEST[gys];
            $arr2[]=$_REQUEST[sqr];
            // var_dump($map);die;		
			if (!empty($model)) {
				$this->_list1($model, $map,'enddate',false,5,$arr2);
			}
			
			$supplier=M("plmmaterialtj")->where("type=1")->group("supplier")->field("supplier")->select();
		    $this->assign("supplier",$supplier);
		}
		if($_REQUEST[tab]==9){
			$model=M('plmorder2');
			$map[type]=2;//二级审核通过
			$map[finish]=0;//未完成
			$map[del]=0;//未作废
			if(!empty($_REQUEST[gys])){
				$map[supplier]=$_REQUEST[gys];
				$this->assign("gys",$_REQUEST[gys]);
			}
			if(!empty($_REQUEST[sqr])){
				$map[user]=array('like','%'.$_REQUEST[sqr]."%");
				$this->assign("sqr",$_REQUEST[sqr]);
			}
			if(!empty($_REQUEST[plm])){
				$map[plm]=array('like','%'.$_REQUEST[plm]."%");
				$this->assign("plm",$_REQUEST[plm]);
			}
			if(!empty($_REQUEST[tjdate])){
				$map[nexttime]=$_REQUEST[tjdate];
				$this->assign("tjdate",$_REQUEST[tjdate]);
			}
			$arr=array();
            $arr[]=$_REQUEST[gys];
            $arr[]=$_REQUEST[sqr];
            $arr[]=$_REQUEST[tjdate];
            $arr[]=$_REQUEST[plm];
			if(!empty($model)){
				$this->_list2($model,$map,'nexttime',true,9,$arr);
			}
			$supplier=M("plmorder2")->where(array('type'=>2))->group("supplier")->field("supplier")->select();
		    $this->assign("supplier",$supplier);
		}
		if($_REQUEST[tab]==10){
			$name = "Project";
			$model = D($name);
			if(!empty($_REQUEST['plm'])){
				$map['title']=array('like','%'.$_REQUEST['plm'].'%');
				$this->assign('plm',$_REQUEST['plm']);
			}
			$arr['plm']=$_REQUEST['plm'];
			if(!empty($model)){
				$this->_list10($model,$map,'create_time',true,$arr);
			}
		}

		if($_REQUEST[tab]==11){
			$model=M('plmorder2');
			$map[del]=1;//作废
			if(!empty($_REQUEST[update]) && empty($_REQUEST[enddate])){
				$time=strtotime($_REQUEST[update]);
				$map[finishtime]=array('egt',$time);
				$this->assign("update",$_REQUEST[update]);
			}
            if(!empty($_REQUEST[enddate]) && empty($_REQUEST[update])){
            	$time=strtotime($_REQUEST[enddate]);
				$map[finishtime]=array('elt',$time);
				$this->assign("enddate",$_REQUEST[enddate]);
			}
			if(!empty($_REQUEST[enddate]) && !empty($_REQUEST[update])){
            	$time1=strtotime($_REQUEST[update]);
            	$time2=strtotime($_REQUEST[enddate])+3600*24;
				$map[finishtime]=array(array('egt',$time1),array('elt',$time2),'and');
				$this->assign("update",$_REQUEST[update]);
				$this->assign("enddate",$_REQUEST[enddate]);
			}

			if(!empty($_REQUEST[sqr])){
				$map[user]=array('like','%'.$_REQUEST[sqr]."%");
				$this->assign("sqr",$_REQUEST[sqr]);
			}
			
			$arr=array();
            $arr[]=$_REQUEST[update];
            $arr[]=$_REQUEST[enddate];
            $arr[]=$_REQUEST[sqr];
			if(!empty($model)){
				$this->_list2($model,$map,'finishtime',false,11,$arr);
			}
		}


        $name=M("role")->where("id='".$_SESSION[position]."'")->getfield("name");
        if($_SESSION[account]=='admin'||$name=="财务经理" || $_SESSION['dept']=="财务部"||$_SESSION[account]=='songlingbo'){
			$this->assign("limit","1");
		}	
		$dept=$_SESSION['dept'];
		$this->assign('dept',$dept);	
		$this->display();
		return;
	}

	protected function _list3($model, $map, $sortBy = '', $asc = false,$tab,$city) {
    	
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
            if($tab==3){
            	$data['gys']=$city[0];
            	$data['city']=$city[1];
            	$data['xm']=$city[2];
            	$data['sqr']=$city[3];
            	$data['spare']=$city[4];
            }
            
            $all_total=0;
            $all_yh2=0;
            foreach ($voList as $key => $val) {
                $voList[$key][total]=$val[total]+$val[yf]-$val[yhje]-$val[yh2];
                // $all_total+=$voList[$key][total];
                // $all_yh2+=$val[yh2];
                $approver=M('plmmaterialorder')->where(array('id'=>$val['orderid']))->getfield('approver');
				$voList[$key]['approver']=$approver;
				if($val[classify]==2){
					$orderid=substr($val[orderid],2);
					$old=M('plmorder2')->find($orderid);
					$voList[$key][baozheng]=$old[baozheng];
					$voList[$key][baozheng_status]=$old[baozheng_status];
					$voList[$key][oldid]=$old[id];
				}
            }
            $voList2=$model->where($map)->select();
            foreach ($voList2 as $key => $val) {
            	$voList2[$key][total]=$val[total]+$val[yf]-$val[yhje]-$val[yh2];
                $all_total+=$voList2[$key][total];
                $all_yh2+=$val[yh2];
            }

            //工程付款的供应商返利添加

			foreach ($data as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
	            }
	        }
            $all_total=number_format($all_total,2);
            $all_yh2=number_format($all_yh2,2);
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
            $this->assign('all_total', $all_total);
            $this->assign('all_yh2', $all_yh2);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
	
	protected function _list($model, $map, $sortBy = '', $asc = false,$tab,$city,$enddate,$user) {
    	
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
    //         foreach($voList as $key=>$value){
    //         	if($val[classify]==2){
				// 	$orderid=substr($val[orderid],2);
				// 	$old=M('plmorder2')->find($orderid);
				// 	$voList[$key][baozheng]=$old[baozheng];
				// 	$voList[$key][baozheng_status]=$old[baozheng_status];
				// 	$voList[$key][oldid]=$old[id];
				// }
    //         }
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            if($tab==3){
            	$data['gys']=$city[0];
            	$data['city']=$city[1];
            	$data['xm']=$city[2];
            	$data['sqr']=$city[3];
            	$data['spare']=$city[4];
            }
            if($tab==7){
            	$data[update]=$city;
            	$data[enddate]=$enddate;
            	$data[sqr]=$user;
            }
            foreach ($data as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
	            }
	        }
            $all_total=0;
            $all_yh2=0;
            foreach ($voList as $key => $val) {
                // $voList[$key][total]=$val[total]+$val[yf]-$val[yhje]-$val[yh2];
                // $all_total+=$voList[$key][total];
                // $all_yh2+=$val[yh2];
                $approver=M('plmmaterialorder')->where(array('id'=>$val['orderid']))->getfield('approver');
				$voList[$key]['approver']=$approver;
            }
            $voList2=$model->where($map)->select();
            foreach ($voList2 as $key => $val) {
            	$voList2[$key][total]=$val[total]+$val[yf]-$val[yhje]-$val[yh2];
                $all_total+=$voList2[$key][total];
                $all_yh2+=$val[yh2];
            }
            $all_total=number_format($all_total,2);
            $all_yh2=number_format($all_yh2,2);
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
            $this->assign('all_total', $all_total);
            $this->assign('all_yh2', $all_yh2);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
	protected function _list5($model, $map, $sortBy = '', $asc = false,$arr) {
    	
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'desc' : 'asc';
        } else {
            $sort = $asc ? 'desc' : 'asc';
        }
        //取得满足条件的记录数
        $count = count($model->where($map)->field("id")->select());
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
            foreach ($voList as $key => $value) {
            	
            	if(empty($value['yf'])){
            		$voList[$key]['yf']=0;
            	}
            	$voList[$key]['price']=$value['total']-$value['yf'];
            }
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($arr as $key => $val) {
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
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
	protected function _list1($model, $map, $sortBy = '', $asc = false,$tab,$sqr) {
    	
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'desc' : 'asc';
        } else {
            $sort = $asc ? 'desc' : 'asc';
        }
        //取得满足条件的记录数
        $count = count($model->where($map)->field("id")->select());
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
            foreach($voList as $key=>$value){
            	$voList[$key][total]=$value['total']+$value['yf']-$value['yhje']-$value['yh2'];
            	if($value[dj]>0){
            		$voList[$key][djstatus]=1;
            	}
            	if($_SESSION[loginUserName]==$value['sqr'] || $_SESSION['account']=='admin' || $_SESSION['dept']=='财务部'|| (($_SESSION['loginUserName']=='胡可可')&&($value['sqr']=="黄滇"))){
            		$voList[$key]['upload_quanxian']=1;
            	}
            	$voList[$key]['bankuser']=M('supplier')->where(array('supplier'=>$value['supplier']))->getfield('bankname');
            }
            if($tab==1){
            	$data[gys]=$map[supplier];
            	$data[tjdate]=$map[enddate];
            	$data[sqr]=$sqr;
            }
            if($tab==5){
            	$data[tjdate]=$sqr[0];
            	$data[fkzt]=$sqr[1];
            	$data[gys]=$sqr[2];
            	$data[sqr]=$sqr[3];
            }
            foreach ($data as $key => $val) {
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
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
    protected function _list2($model, $map, $sortBy = '', $asc = false,$tab,$arr) {
    	
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
            $all_total=0;
            foreach ($voList as $key => $value) {
            	$con['paytime']=array('neq','');
            	$con['orderid']=$value[id];
            	// $con1['paytime']=array('neq','');
            	$con1['orderid']=$value[id];
            	// $con1['status']=1;//已付款
            	$info=M('plmorder2paytime')->where($con)->select();
            	$info1=M('plmorder2paytime')->where($con1)->select();
            	$total=0;
            	$pay=0;
            	foreach ($info as $key2 => $value2) {
            		$total+=$value2['pay'];//确定付款日期
            	}
            	foreach ($info1 as $key3 => $value3) {
            		$pay+=$value3['oldpay'];//已付款
            	}
            	$voList[$key]['account']=M('supplier')->where(array('supplier'=>$value['supplier']))->getfield('bankname');
            	$voList[$key]['total']=$total;
            	$voList[$key]['total_changed']=$voList[$key]['price'] - $voList[$key]['yh2'];
            	$voList[$key]['pay']=$pay;


            	//加上保证金
            	$voList[$key]['price']=$value['price']+$value['baozheng'];
            	if($value['pay_baozheng_status']==1){
            		$voList[$key]['pay']=$pay+$value['baozheng'];
            	}
            	//判断是否要付保证金
            	if($value['pay_baozheng_status']==0 && floatval($value['baozheng'])>0){
            		$voList[$key]['baozhengjin']=0;
            	}else{
            		$voList[$key]['baozhengjin']=1;
            	}
            	// dump($value[$key]['pay_baozheng']);
            	if($_SESSION[account]=='admin'||$name=="财务经理" || $_SESSION['dept']=="财务部" || $_SESSION['loginUserName']==$voList[$key][user]|| (($_SESSION['loginUserName']=='胡可可')&&($voList[$key][user]=="黄滇"))){
					$voList[$key][limit_upload]=1;
				}
				$all_total+=$value[price];
            }
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            if($tab==9){
            	$data['gys']=$arr[0];
            	$data['sqr']=$arr[1];
            	$data['tjdate']=$arr[2];
            	$data['plm']=$arr[3];
            }
            if($tab==10){
            	$data['city']=$arr[0];
            	$data['xm']=$arr[1];
            	$data['gys']=$arr[2];
            	$data['sqr']=$arr[3];
            	$data['baozheng']=$arr[4];

            }
             if($tab==11){
            	$data['update']=$arr[0];
            	$data['enddate']=$arr[1];
            	$data['sqr']=$arr[2];
            }
            foreach ($data as $key => $val) {
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
	protected function _list10($model, $map, $sortBy = '', $asc = false,$arr) {
    	
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
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->field('id,title')->select();
            foreach ($voList as $key => $value) {
            	$plm=$value['title'];
            	$material=0;//材料部
            	$market=0;//市场部
            	$work=0;//专项部
            	$engineering=0;//工程配合部
            	$materialall=0;//材料下单总计
            	$engineeringall=0;//工程下单总计
            	$refund=0;//退货
            	$materials=M('plmmaterialtj')->where(array('status'=>1,'plm'=>$plm,'department'=>'材料部','type'=>1,'tuihuo'=>1))->select();
            	foreach ($materials as $key1 => $value1) {
            		$material+=$value1['total']+$value1['yf']-$value1['yhje']-$value1['yh2'];
            	}
            	$refunds=M('plmmaterialtj')->where(array('status'=>1,'tuihuo'=>2,'type'=>2,'plm'=>$plm))->select();
            	foreach ($refunds as $key5 => $value5) {
            		$refund+=$value5['total']-$value5['yf'];
            	}
            	$markets=M('plmmaterialtj')->where(array('status'=>1,'plm'=>$plm,'department'=>'市场部','type'=>1,'tuihuo'=>1))->select();
            	foreach ($markets as $key2 => $value2) {
            		$market+=$value2['total']+$value2['yf']-$value2['yhje']-$value2['yh2'];
            	}
            	$works=M('plmorder2paytime')->where(array('plm'=>$plm,'department'=>'专项部','del'=>0))->select();
            	foreach ($works as $key3 => $value3) {
            		$work+=$value3['oldpay'];
            	}
            	$engineerings=M('plmorder2paytime')->where(array('plm'=>$plm,'department'=>'工程配合部','del'=>0))->select();
            	foreach ($engineerings as $key4 => $value4) {
            		$engineering+=$value4['oldpay'];
            	}
            	//应付总计
            	$materialalls=M('plmmaterialtj')->where(array('status'=>array('neq',20),'plm'=>$plm,'type'=>1,'tuihuo'=>1,'department'=>array(array('eq','材料部'),array('eq','市场部'),'or')))->select();
            	foreach ($materialalls as $key6 => $value6) {
            		$materialall+=$value6['total']+$value6['yf']-$value6['yhje']-$value6['yh2'];
            	}
            	// $engineeringalls=M('plmorder2paytime')->where(array('plm'=>$plm,'del'=>0))->select();
            	// foreach ($engineeringalls as $key7 => $value7) {
            	// 	$engineeringall+=$value7['pay']-$value7['yh2'];
            	// }
            	//$engineeringalls=M('plmorder2')->where(array('plm'=>$plm,'del'=>0))->select();

            	$engineeringalls=M('plmorder2')->where(array('plm'=>$plm,'del'=>0))->select();
            	//dump($engineeringall11);die;
            	foreach ($engineeringalls as $key7 => $value7) {
            		$engineeringall+=$value7['price']-$value7['yh2'];
            	}
            	//dump($engineeringall);die;
            	$voList[$key]['material']=number_format($material,2);
            	$voList[$key]['market']=number_format($market,2);
            	$voList[$key]['work']=number_format($work,2);
            	$voList[$key]['refund']=number_format($refund,2);
            	$voList[$key]['engineering']=number_format($engineering,2);
            	$voList[$key][total]=number_format($market+$material+$work+$engineering,2);
            	$voList[$key][totalall]=number_format($materialall+$engineeringall,2);
            }
            //分页跳转的时候保证查询条件

            foreach ($arr as $key => $val) {
	            if (!is_array($val)) {
	                $p->parameter .= "$key/".$val."/";
	            }
	        }
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
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
	public function check(){	
		$map[id]=$_REQUEST[id];	
		$list=M("plmmaterialtj")->where($map)->find();
		$dept=M('plmmaterialorder')->where(array('id'=>$list[orderid]))->getfield('department');
		$this->assign('department',$dept);
		$total=0;
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		if(!empty($list[newname])){
			$newname=$list[newname];
		}
		$Total=$this->cny($total);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$model=M('plmmaterials');
		$arr=explode(',',$list['clid']);
		$count=1;
		$str="";
		foreach ($arr as $k => $v) {
			if(!empty($v)){
				$info=$model->where(array('id'=>$v))->find();
				$str.=$count.'.'."材料编号：".$info['number'].'，'."材料名称：".$info['name'].'，'."材料类别：".$info['brand'].'，'.'规格:'.$info['standard'].'，'.'单位：'.$info['unit'].'，'.'价格：'.$info['price'].'，'.'数量:'.$info['count'].';'.'<br/>';
			}
			$count++;
		}

		
		$con[id]=$list[orderid];
		$order=M("plmmaterialorder")->where($con)->field("ctime")->find();
		if($_SESSION['department']==7 || $_SESSION['loginUserName']==$_REQUEST[sqr] || $_SESSION['account']=='admin' || (($_SESSION['loginUserName']=='胡可可')&&($_REQUEST['sqr']=="黄滇"))){
			$limit2=1;
		}else{
			$limit2=0;
		}
		if($_SESSION['loginUserName']==$list[sqr] || $_SESSION['account']=='admin' || (($_SESSION['loginUserName']=='胡可可')&&($list['sqr']=="黄滇"))){
			$limit3=1;
		}else{
			$limit3=0;
		}
		$histroy=$list[handlehistroy];
		$array=explode(';',$histroy);	
        $this->assign("array",$array);
		$this->assign("list",$list);
        $this->assign("supplier",$supplier);
        $this->assign("total",$total);
		$this->assign("Total",$Total);
		$this->assign("limit2",$limit2);
		$this->assign("limit3",$limit3);
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if (!empty($value)) {
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}	
		}
		$this->assign("newname",$newname);
		$this->assign('str',$str);
		$this->assign('order',$order);
		$this->display();
	}
	public function check4(){	
		$info=M('plmmaterialtj')->where('id="'.$_REQUEST['id'].'"')->find();
		$order=M('plmmaterialorder')->where(array('id'=>$info['orderid']))->find();	
		$sqr=$info['sqr'];
		$ctime=$order['ctime'];
		$shiyou=$info[shiyou];
		$total=$info[total]+$total[yf]-$total[yhje]-$total[yh2];
		$remark=$info[remark];
		$Total=$this->cny($total);
        $supplier=M("supplier")->where("supplier='".$info[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$arr=explode(',',$info['clid']);
		$str="";
		$count=1;
		foreach ($arr as $k => $v) {
			if(!empty($v)){
				$clinfo=M('plmmaterials')->where(array('id'=>$v))->find();
				$str.=$count.'.'."材料编号：".$clinfo['number'].'，'."材料名称：".$clinfo['name'].'，'.'规格:'.$clinfo['standard'].'，'.'单位：'.$clinfo['unit'].'，'.'价格：'.$clinfo['price'].'，'.'数量:'.$clinfo['count'].';'.'<br/>';
			}
			$count++;
			
		}
		if($_SESSION['department']==7 || $_SESSION['loginUserName']==$info[sqr] || $_SESSION['account']=="admin"|| (($_SESSION['loginUserName']=='胡可可')&&($info['sqr']=="黄滇"))){
			$limit2=1;
		}else{
			$limit2=0;
		}
		$histroy=$info[handlehistroy];
		$array=explode(';',$histroy);
		// dump($array);die;
		$this->assign('sqr',$sqr);
		$this->assign("ctime",$ctime);
		$this->assign("shiyou",$shiyou);
		$this->assign("Total",$Total);
		$this->assign("total",$total);
		$this->assign("remark",$remark);
		$this->assign("supplier",$supplier);
        $this->assign("str",$str);
        $this->assign("limit2",$limit2);
        $this->assign("info",$info);
		$this->display();
	}
	public function check1(){			
		$plmmaterialtj=M("plmmaterialtj")->where("id='".$_REQUEST[id]."'")->field("supplier,enddate,clid,total,yf")->find();
		$plmmaterialtj['price']=$plmmaterialtj['total']-$plmmaterialtj['yf'];
		$map[id]=array("in",$plmmaterialtj[clid]);
		$list=M("plmmaterials")->where($map)->select();
		$total='';
		$clid='';
		foreach($list as $j=>$vk){
			$list[$j][found]=$vk[price]*$vk[count];
		}
		$this->assign("plmmaterialtj",$plmmaterialtj);
        $this->assign("list",$list);		
		$this->display();
	}	
	
	public function submit(){
		// dump($_FILES);die;
		foreach ($_FILES['file']['name'] as $key => $value) {
			$vo=M('plmmaterialtj')->where(array('id'=>$_REQUEST['id']))->find();
			if(!empty($_FILES['file']['name'][$key]))
			{
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$key])))); 
				$uuid=uniqid(rand(), false).$key;
				$newname = filter_var($uuid.'.'.$ext, FILTER_CALLBACK, array("options"=>"convertSpace"));
				$upload_file = $savePath.$newname;	
				
				$filename=$_FILES['file']['name'][$key];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				move_uploaded_file($_FILES['file']['tmp_name'][$key],$upload_file);
				if(!empty($vo[newname])){
					$data[newname] = $vo[newname].','.$newname;
					$data[filename] = $vo[filename].','.$_FILES['file']['name'][$key];
				}else{
					$data[newname] = $newname;
					$data[filename] = $_FILES['file']['name'][$key];
				}						
				M("plmmaterialtj")->where("id='".$vo[id]."'")->save($data);
			}					
		}
		$this->success('上传附件成功。');	
	}
	
	public function pay(){	        
		$map[id]=$_REQUEST[id];
		self::see($_REQUEST[id]);
		$list=M("plmmaterialtj")->where($map)->find();
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		$money=$total-$list[pay];
        $this->assign("list",$list);
        $this->assign("total",$total);
        $this->assign("money",$money);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,telephone")->find();
        $this->assign("supplier",$supplier);
		$this->display();
	}
	//[批量付款]
	public function allpay(){
		$arrstr=$_REQUEST['arr'];
		$arr=explode(',',$arrstr);
		$i=0;
		foreach ($arr as $key => $value) {
			if(!empty($value)){
				$map[id]=$value;	
				$list=M("plmmaterialtj")->where($map)->find();
				
				$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
				$data[user]=$_SESSION[loginUserName];
				$data[ctime]=time();
				$data[status]=1;
				$supplier=M('supplier')->where(array('supplier'=>$list['supplier']))->find();
				$data[bankaccount]=$supplier[bankname];
				$data[banknumber]=$supplier[bankaccount];
				$data[bankaddress]=$supplier[bankhu];
				$data[pay]=$total;
				$data[handlehistroy]="<td>".date('Y-m-d H:i')."</td><td>".$total."</td><td>".$_SESSION['loginUserName']."</td><td>一次性批量付款</td>;";
				$res=M('plmmaterialtj')->where($map)->save($data);
			}
		}
		if(empty($arrstr)){
			echo 2;//为空  未获取数据
		}else{
			echo 1;
		}
	}
	
	public function paysubmit(){
		$map[id]=$_REQUEST[id];	
		$djstatus=M("plmmaterialtj")->where($map)->find();
		$dj_type=$djstatus['dj_type'];
		if($_POST[pay]<$_POST[money]){
			$data[pay]=$djstatus['pay']+$_POST[pay];
			$data[status]=2;
			$data[ctime]=time();
			M("plmmaterialtj")->where($map)->save($data);
			$info=M("plmmaterialtj")->where($map)->find();
			$handlehistroy=$info['handlehistroy'];
				// $histroy['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$_POST[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>支付发起定金</td>;";
			$histroy['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$_POST[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>付款</td>;";
			
			$res=M("plmmaterialtj")->where($map)->save($histroy);
		    $this->redirect('index','tab=1');
		}else{
			$data[pay]=$djstatus['pay']+$_POST[pay];
			$data[status]=1;
			$data[bankaccount]=$_REQUEST[bankaccount];
			$data[banknumber]=$_REQUEST[banknumber];
			$data[bankaddress]=$_REQUEST[bankaddress];
			$data[ctime]=time();
			$data[user]=$_SESSION[loginUserName];

			$res=M("plmmaterialtj")->where($map)->save($data);
			$info=M("plmmaterialtj")->where($map)->find();
			$handlehistroy=$info['handlehistroy'];
			$histroy['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$_POST[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>付款</td>;";
			$res=M("plmmaterialtj")->where($map)->save($histroy);
		    $this->redirect('index','tab=1');
		}
	}
	public function yh2(){
		$map[id]=$_REQUEST[id];	
		self::see($_REQUEST[id]);
		$list=M("plmmaterialtj")->where($map)->find();
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		$total1=$list[total]+$list[yf]-$list[yhje];
		$model=M('plmmaterials');
		$arr=explode(',',$list['clid']);
		$count=1;
		$str="";
		foreach ($arr as $k => $v) {
			if(!empty($v)){
				$info=$model->where(array('id'=>$v))->find();
				$str.=$count.'.'."材料编号：".$info['number'].'，'."材料名称：".$info['name'].'，'.'规格:'.$info['standard'].'，'.'单位：'.$info['unit'].'，'.'价格：'.$info['price'].'，'.'数量:'.$info['count'].';'.'<br/>';
			}
			$count++;
		}
		$this->assign("list",$list);
        $this->assign("supplier",$supplier);
        $this->assign("str",$str);
        $this->assign("total",$total);
        $this->assign("total1",$total1);
		$this->display();
	}
	public function yh2submit(){
		$id=$_REQUEST[id];
		if ($_REQUEST[total1]<$_REQUEST[yh2]) {
			$this->error('返利金额不得大于总金额,请重新输入.');
		}
		$list=M("plmmaterialtj")->where(array('id'=>$_REQUEST[id]))->save(array('yh2'=>$_REQUEST[yh2]));


		$this->success('操作成功.');
	}
	public function remark(){
		$map[id]=$_REQUEST[id];	
		$data[remark]=$_POST['remark'];
		$data[shiyou]=$_POST['shiyou'];
		// var_dump($_POST[tjid]);die;
		$list=M("plmmaterialtj")->where($map)->save($data);				
		$this->success('操作成功.');
	}
	
	public function payx(){	        
		$map[id]=$_REQUEST[id];
		self::see($_REQUEST[id]);
		$list=M("plmmaterialtj")->where($map)->find();
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		$money=$total-$list[pay];
        $this->assign("list",$list);
        $this->assign("total",$total);
        $this->assign("money",$money);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,telephone")->find();
        $this->assign("supplier",$supplier);
		$this->display();
	}
	
	public function payxsubmit(){
		$map[id]=$_REQUEST['id'];
		$list=M('plmmaterialtj')->where($map)->find();
		if($_POST[pay]<$_POST[money]){
			$data[status]=12;//先付后补中
			$data[ctime]=time();
			$data[pay]=$list[pay]+$_POST[pay];
			M("plmmaterialtj")->where($map)->save($data);
			$info=M("plmmaterialtj")->where($map)->find();
			$handlehistroy=$info['handlehistroy'];
			$histroy['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$_POST[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>先付后补</td>;";
			$res=M("plmmaterialtj")->where($map)->save($histroy);	
			$this->redirect('index','tab=1');
		}else{
			$data[status]=3;//先付后补完成
			$data[ctime]=time();
			$data[pay]=$list[pay]+$_POST[pay];
			$data['user']=$_SESSION['loginUserName'];
			$supplier=M('supplier')->where(array('supplier'=>$list['supplier']))->find();
			$data[bankaccount]=$supplier[bankname];
			$data[banknumber]=$supplier[bankaccount];
			$data[bankaddress]=$supplier[bankhu];
			M("plmmaterialtj")->where($map)->save($data);
			$info=M("plmmaterialtj")->where($map)->find();
			$handlehistroy=$info['handlehistroy'];
			$histroy['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$_POST[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>先付后补</td>;";
			$res=M("plmmaterialtj")->where($map)->save($histroy);	
			$this->redirect('index','tab=5');
		}   
	}
	public function pay1(){	
		$id=$_REQUEST['id'];
		$info=M('plmmaterialtj')->find($id);
		$clid=explode(',',$info['clid']);
		$map['id']=array('in',$clid);
		$list=M('plmmaterials')->where($map)->select();
		$total=0;
		foreach ($list as $key => $value) {
			$list[$key]['found']=$value['price']*$value['count'];
			$total+=$value['price']*$value['count'];
		}
		$price=$total-$info['yf'];
        $this->assign("list",$list);		
        $this->assign('info',$info);
        $this->assign('total',$total);
        $this->assign('yf',$info['yf']);
        $this->assign('price',$price);
		$this->display();
	}
	
	public function pay1submit(){
		$map[id]=$_POST[id];
		$data[id]=$_POST[id];	
		$data[status]=1;
		$data[ctime]=time();
		$data[user]=$_SESSION[loginUserName];
		M("plmmaterialtj")->save($data);
		$this->redirect('index','tab=2');
	}
	
	public function edit(){	        
		$id=$_REQUEST[id];      
        $this->assign("id",$id);		
		$this->display();
	}

	public function payhistroy(){	        
		$map[enddate]=$_REQUEST[enddate];	
		$map[supplier]=$_REQUEST[supplier];
		$map[sqr]=$_REQUEST[sqr];
		$map[status]=$_REQUEST[status];
		$map[type]=1;
		$map[approve]=1;
		$list=M("plmmaterialtj")->where($map)->select();

		$tjid="";
		foreach($list as $j=>$vk){			
			$tjid.=$vk[id].",";
		}        
		$histroy=$list[0][handlehistroy];
		$array=explode(';',$histroy);
		// dump($array);die;
        $this->assign("id",$tjid);	
        $this->assign("array",$array);	
		$this->display();
	}
	
	public function edit1(){
        $map[id]=$_REQUEST[id];	
        self::see($_REQUEST[id]);
		$list=M("plmmaterialtj")->where($map)->find();
		$dept=M('plmmaterialorder')->where(array('id'=>$list[orderid]))->getfield('department');
		$this->assign('department',$dept);
		$total=0;
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		$total_pay=$list[total]+$list[yf]-$list[yhje]-$list[yh2]-$list[pay];
		$this->assign('total_pay',$total_pay);
		if(!empty($list[newname])){
			$newname=$list[newname];
		}
		$Total=$this->cny($total);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$model=M('plmmaterials');
		$arr=explode(',',$list['clid']);
		$count=1;
		$str="";
		foreach ($arr as $k => $v) {
			if(!empty($v)){
				$info=$model->where(array('id'=>$v))->find();
				$str.=$count.'.'."材料编号：".$info['number'].'，'."材料名称：".$info['name'].'，'.'规格:'.$info['standard'].'，'.'单位：'.$info['unit'].'，'.'价格：'.$info['price'].'，'.'数量:'.$info['count'].';'.'<br/>';
			}
			$count++;
		}

		
		$con[id]=$list[orderid];
		$order=M("plmmaterialorder")->where($con)->field("ctime")->find();
		if($_SESSION['department']==7 || $_SESSION['loginUserName']==$_REQUEST[sqr] || $_SESSION['account']=='admin'|| (($_SESSION['loginUserName']=='胡可可')&&($_REQUEST['sqr']=="黄滇"))){
			$limit2=1;
		}else{
			$limit2=0;
		}
		if($_SESSION['loginUserName']==$list[sqr] || $_SESSION['account']=='admin' || (($_SESSION['loginUserName']=='胡可可')&&($list['sqr']=="黄滇"))){
			$limit3=1;
		}else{
			$limit3=0;
		}
		$histroy=$list[handlehistroy];
		$array=explode(';',$histroy);	
        $this->assign("array",$array);
		$this->assign("list",$list);
        $this->assign("supplier",$supplier);
        $this->assign("total",$total);
		$this->assign("Total",$Total);
		$this->assign("limit2",$limit2);
		$this->assign("limit3",$limit3);
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if (!empty($value)) {
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}
		}

		$this->assign("newname",$newname);
		$this->assign('str',$str);
		$this->assign('order',$order);
		$this->display();
	}
	
	public function edit2(){	        
		$id=$_REQUEST[id];   
        $this->assign("id",$id);		
		$this->display();
	}
	
	public function submit2(){
		// dump($_FILES['file']);die;
		$id=$_REQUEST[id];
		$map[id]=$id;
		foreach ($_FILES['file']['name'] as $key => $value) {
			$tj=M("plmmaterialtj")->where($map)->field("id,newname,filename")->find();
			if(!empty($_FILES['file']['name'][$key]))
			{
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$key])))); 
				$uuid=uniqid(rand(), false).$key;
				$newname = filter_var($uuid.'.'.$ext, FILTER_CALLBACK, array("options"=>"convertSpace"));
				$upload_file = $savePath.$newname;	
				
				$filename=$_FILES['file']['name'][$key];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				$res1=move_uploaded_file($_FILES['file']['tmp_name'][$key],$upload_file);
				if($res1){
					if(!empty($tj[newname])){
						$data[newname] = $tj[newname].','.$newname;
						$data[filename] = $tj[filename].','.$_FILES['file']['name'][$key];
					}else{
						$data[newname] = $newname;
						$data[filename] = $_FILES['file']['name'][$key];
					}
				}
				$data['file_status']=1;						
				M("plmmaterialtj")->where($map)->save($data);		
			}
		}
		if ($res1) {
			$this->success('上传附件成功。');
		}else{
			$this->error('上传附件失败,请重试。');
		}
		
	}
	
	public function edit3(){
        $map[id]=$_REQUEST[id];
		$list=M("plmmaterialtj")->where($map)->find();
		$dept=M('plmmaterialorder')->where(array('id'=>$list[orderid]))->getfield('department');
		$this->assign('department',$dept);
		$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		if(!empty($list[newname])){
			$newname=$list[newname];
		}
		$Total=$this->cny($total);
        $supplier=M("supplier")->where("supplier='".$list[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$material=M("plmmaterialtj")->where($map)->group("plm")->field("plm,sum(total+yf-yhje-yh2) total")->select();
		$con[id]=$list[orderid];
		$order=M("plmmaterialorder")->where($con)->field("user,ctime")->find();
		$ctime=$order[ctime];
		$this->assign("list",$list);
        $this->assign("supplier",$supplier);
        $this->assign("total",$total);
		$this->assign("Total",$Total);
		$this->assign("ctime",$ctime);
		$name=explode(",",$newname);
		$newname=array();
		$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
		foreach ($name as $key => $value) {
			if (!empty($value)) {
				$newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$newname[$key][is_pic]=1;
				}else{
					$newname[$key][is_pic]=0;
				}
			}	
		}
		$this->assign("newname",$newname);
		$this->display();
	}
	
	public function payfor(){
		$map[id]=$_REQUEST[message];
		$list=M("plmmaterialtj")->where($map)->find();
		if(!$list[dj_type]){
			$handlehistroy=$list[handlehistroy];
			$data[pay]=$list[dj];
			$data[status]=4;
			$data[ctime]=time();
			$data['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$list[dj]."</td><td>".$_SESSION['loginUserName']."</td><td>付定金</td>;";
			M("plmmaterialtj")->where($map)->save($data);
			echo 1;
		}else{
			if ($list['addpay']==1) {
				$addpay=$list[dj]-$list[pay];
			}else{
				$addpay=$list[dj];
			}
			$data[status]=3;
			$data[pay]=$list[dj];
			$data[ctime]=time();
			$data[user]=$_SESSION[loginUserName];
			M("plmmaterialtj")->where($map)->save($data);
			$handlehistroy=$list['handlehistroy'];
			$histroy['handlehistroy']=$handlehistroy."<td>".date('Y-m-d H:i')."</td><td>".$addpay."</td><td>".$_SESSION['loginUserName']."</td><td>付定金</td>;";
			$res=M("plmmaterialtj")->where($map)->save($histroy);
		    echo 1;
		}
	}
	
	function paysub(){
		$data=explode("/",$_POST[message]);
		$map[enddate]=$data[1];	
		$map[supplier]=$data[0];
		$map[sqr]=$data[2];
		$map[status]=0;
		$map[type]=1;
		$map[approve]=1;
		$data1[status]=1;
		$data1[ctime]=time();
		$data1[user]=$_SESSION[loginUserName];
		$list=M("plmmaterialtj")->where($map)->save($data1);
		$dj=0;
		foreach($list as $j=>$vk){
			$dj+=$vk[dj];			
		}
		$data[pay]=$dj;
		$data[status]=4;
		$data[ctime]=time();
		M("plmmaterialtj")->where($map)->save($data);
		echo 1;
	}


	function fileconfirm(){
		$map[id]=$_REQUEST[message];
		$data1[status]=1;
		// // $data1[ctime]=time();
		// $data1[user]=$_SESSION[loginUserName];
		// $list=M("plmmaterialtj")->where($map)->select();
		$res=M("plmmaterialtj")->where($map)->save($data1);
		echo 1;
	}
	
	
	function cny($ns) { //人民币转大写
		static $cnums=array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"), 
			$cnyunits=array("圆","角","分"), 
			$grees=array("拾","佰","仟","万","拾","佰","仟","亿"); 
		list($ns1,$ns2)=explode(".",$ns,2); 
		$ns2=array_filter(array($ns2[1],$ns2[0])); 
		$ret=array_merge($ns2,array(implode("",$this->_cny_map_unit(str_split($ns1),$grees)),"")); 
		$ret=implode("",array_reverse($this->_cny_map_unit($ret,$cnyunits))); 
		return str_replace(array_keys($cnums),$cnums,$ret); 
	}
	
	function _cny_map_unit($list,$units) { 
		$ul=count($units); 
		$xs=array(); 
		foreach (array_reverse($list) as $x) { 
			$l=count($xs); 
			if ($x!="0" || !($l%4)) $n=($x=='0'?'':$x).($units[($l-1)%$ul]); 
			else $n=is_numeric($xs[0][0])?$x:''; 
			array_unshift($xs,$n); 
		} 
		return $xs; 
	}

	public function zuofei(){
		$map[id]=$_REQUEST[id];
		self::see($_REQUEST[id]);
		$data1[status]=20;//20  代表作废
		$data1[zf_time]=time();
		$list=M("plmmaterialtj")->where($map)->save($data1);
		$info=M('plmmaterialtj')->find($_REQUEST['id']);
		if($info['plm']=='仓库采购'){ //仓库采购清除剩余
			$clid=trim($info['clid'],',');
			$clid=explode(',',$clid);
			foreach ($clid as $key => $value) {
				if(!empty($value)){
					$res=M('plmmaterials')->where(array('id'=>$value))->save(array('odd'=>0));
				}
			}
		}
		echo 1;
	}
	public function baoxiao(){
		$id=$_REQUEST[id];
		$info=M('plmmaterialtj')->where(array('id'=>$id))->find();
		$handlehistroy=$info[handlehistroy];
		$supplier=M('supplier')->where(array('supplier'=>$info['supplier']))->find();
		$data[bankaccount]=$supplier[bankname];
		$data[banknumber]=$supplier[bankaccount];
		$data[bankaddress]=$supplier[bankhu];
		$data['spare']=0;
		$data[ctime]=time();
		$data[pay]=$info[total]-$info[yhje]+$info[yf];
		$data[handlehistroy]=$handlehistroy."<td>".date('Y-m-d H:i',time())."</td><td>".($info[total]-$info[yhje]+$info[yf]-$info[pay])."</td><td>".$_SESSION['loginUserName']."</td><td>进行报销</td>;";
		$data[user]=$_SESSION[loginUserName];
		if($info[dj_type]==1){
			$data[status]=3;
		}
		$res=M('plmmaterialtj')->where(array('id'=>$id))->save($data);
		if($res){
			echo 1;
		}else{
			echo 2;
		}


	}

	public function ajaxbaoxiao(){
		$ids=$_REQUEST[ids];
		$array=explode(',',$ids);
		foreach ($array as $key => $id) {
			if(!empty($id)){
				$info=M('plmmaterialtj')->where(array('id'=>$id))->find();
				$supplier=M('supplier')->where(array('supplier'=>$info['supplier']))->find();
				$data[bankaccount]=$supplier[bankname];
				$data[banknumber]=$supplier[bankaccount];
				$data[bankaddress]=$supplier[bankhu];
				$handlehistroy=$info[handlehistroy];
				$data['spare']=0;
				$data[ctime]=time();
				$data[pay]=$info[total]-$info[yhje]+$info[yf];
				$data[handlehistroy]=$handlehistroy."<td>".date('Y-m-d H:i',time())."</td><td>".($info[total]-$info[yhje]+$info[yf])."</td><td>".$_SESSION['loginUserName']."</td><td>批量报销</td>;";
				$data[user]=$_SESSION[loginUserName];
				if($info[dj_type]==1){
					$data[status]=3;
				}
				$res=M('plmmaterialtj')->where(array('id'=>$id))->save($data);
			}
		}
				
		if($res){
			echo 1;
		}else{
			echo 2;
		}
	}

	//工程下单模块开始
	
	//付款列表详情
	public function gcInfo(){
		$id=substr($_REQUEST[id],2);
		$order=M('plmorder2')->where(array('id'=>$id))->find();
		$histroy=$order[handle];
		$array=explode(';',$histroy);
		$this->assign('array',$array);
		$map1[orderid]=$id;
		$map1[paytime]=array('neq','');
		$order_paytime1=M('plmorder2paytime')->where($map1)->select();
		$total_time=0;
		foreach ($order_paytime1 as $key => $value) {
			$total_time+=$value[pay];
		}

		$map2[orderid]=$id;
		$map2[paytime]=array('neq','');
		// $map2[status]=1;
		$order_paytime2=M('plmorder2paytime')->where($map2)->select();

		$total_time2=0;
		foreach ($order_paytime2 as $key => $value) {
			$total_time2+=$value[oldpay];
		}
		//是否付了保证金
		if($order['pay_baozheng_status']==1){
			$total_time2+=$order[baozheng];
		}
		$order['allprice']=$order['price']+$order['baozheng'];
		$Total=$this->cny($order['allprice']);
		$supplier=M("supplier")->where("supplier='".$order[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();

		//yh2供应商返利
		$order['yh2'] = M('plmorder2')->where(array('id'=>$id))->getField('yh2');
		$this->assign('supplier',$supplier);
		$this->assign('total_time',$total_time);//确定付款
		$this->assign('total_time2',$total_time2);//已付款
		$this->assign('Total',$Total);
		$this->assign('info',$order);
		$newname=$order['newname'];
		if(!empty($newname)){
			$name=explode(",",$newname);
			$gc_newname=array();
			$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
			foreach ($name as $key => $value) {
				$gc_newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$gc_newname[$key][is_pic]=1;
				}else{
					$gc_newname[$key][is_pic]=0;
				}
			}
			$this->assign('newname',$gc_newname);
		}
		if(($_SESSION['account']=='admin') || ($_SESSION[loginUserName]==$order[user]) || $_SESSION['dept']=='财务部'|| (($_SESSION['loginUserName']=='胡可可')&&($order['user']=="黄滇"))){
			$gclimit=1;
		}
		$this->assign('gclimit',$gclimit);
		$this->display();
	}
	public function gcInfoa(){
		$id=substr($_REQUEST[id],2);
		$order=M('plmorder2')->where(array('id'=>$id))->find();
		$order2=M('plmmaterialtj')->where(array('orderid'=>$_REQUEST['id']))->find();
		$histroy=$order[handle];
		$array=explode(';',$histroy);
		$this->assign('array',$array);
		$this->assign('order2',$order2);
		$map1[orderid]=$id;
		$map1[paytime]=array('neq','');
		$order_paytime1=M('plmorder2paytime')->where($map1)->select();
		$total_time=0;
		foreach ($order_paytime1 as $key => $value) {
			$total_time+=$value[pay];
		}

		$map2[orderid]=$id;
		$map2[paytime]=array('neq','');
		$map2[status]=1;
		$order_paytime2=M('plmorder2paytime')->where($map2)->select();
		$total_time2=0;
		foreach ($order_paytime2 as $key => $value) {
			$total_time2+=$value[pay];
		}
		//是否付了保证金
		if($order['pay_baozheng_status']==1){
			$total_time2+=$order[baozheng];
		}
		$order['allprice']=$order['price']+$order['baozheng'];
		$Total=$this->cny($order['allprice']);
		// $Total=$this->cny($order['price']);
		$supplier=M("supplier")->where("supplier='".$order[supplier]."'")->field("supplier,bankaccount,bankname,bankhu,name")->find();
		$this->assign('supplier',$supplier);
		$this->assign('total_time',$total_time);//确定付款
		$this->assign('total_time2',$total_time2);//已付款
		$this->assign('Total',$Total);
		$this->assign('info',$order);
		$newname=$order['newname'];
		if(!empty($newname)){
			$name=explode(",",$newname);
			$gc_newname=array();
			$pic=array('bmp','jpg','png','tif','gif','pcx','tga','exif','fpx','svg','psd','cdr','eps','ai','webp');
			foreach ($name as $key => $value) {
				$gc_newname[$key]['name']=$value;
				$ext=end(explode('.',$value));
				if(in_array($ext,$pic)){
					$gc_newname[$key][is_pic]=1;
				}else{
					$gc_newname[$key][is_pic]=0;
				}

			}

			$this->assign('newname',$gc_newname);
		}
		if(($_SESSION['account']=='admin') || ($_SESSION[loginUserName]==$order[user]) || $_SESSION['dept']=='财务部'|| (($_SESSION['loginUserName']=='胡可可')&&($order['user']=="黄滇"))){
			$gclimit=1;
		}
		$this->assign('gclimit',$gclimit);
		$this->display('gcInfo1');
	}
	//修改备注,付款事由
	public function gcremark(){
		$id=$_REQUEST[id];
		$res=M('plmorder2')->where(array('id'=>$id))->save(array('because'=>$_REQUEST[because],'remark'=>$_REQUEST[remark]));
		$this->success('修改成功.');
	}
	//付款
	public function gcPay(){
		$id=$_REQUEST['id'];
		$list=M('plmorder2')->find($id);

		//应付
		$should_pay = $list['price'] - $list['yh2'];
		// dump($should_pay);die;
		$supplier=M("supplier")->where("supplier='".$list[supplier]."'")->find();
		$paytime=M('plmorder2paytime')->where(array('orderid'=>$id))->order('paytime')->select();
		//dump($paytime);die;
		foreach ($paytime as $key => $value) {
			$paytime[$key]['notpay']=$value['pay']-$value['oldpay'];
		}

		$this->assign('list',$list);
		$this->assign('supplier',$supplier);
		$this->assign('paytime',$paytime);
		$this->assign('should_pay',$should_pay);
		$this->display();
	}
	// //执行付款 
	// public function gcPaySubmit(){
	// 	$model=M('plmorder2paytime');
	// 	foreach ($_REQUEST['pay'] as $key => $value) {
	// 		$id=$key;
	// 		$info=$model->find($id);
	// 		$order=M('plmorder2')->find($info[orderid]);
	// 		// dump($info['pay']);die;
	// 		if($info['oldpay']+$value>$info['pay']){
	// 			$this->error('付款金额已大于总金额,请重新输入.');
	// 		}
	// 		if($info['oldpay']+$value<$info['pay']){
	// 			$oldpay=$info['oldpay']+$value;
	// 			$res=$model->where(array('id'=>$id))->save(array('oldpay'=>$oldpay));
	// 		}
	// 		if($info['oldpay']+$value==$info['pay']){
	// 			$oldpay=$info['oldpay']+$value;
	// 			$res=$model->where(array('id'=>$id))->save(array('oldpay'=>$oldpay,'status'=>1));
	// 			$list=$model->where(array('orderid'=>$info[orderid],'status'=>0,'paytime'=>array('neq','')))->select();
	// 			$list2=$model->where(array('orderid'=>$info[orderid]))->select();
	// 			$nexttime='9999-12-31';//修改下次付款时间
	// 			foreach ($list as $key => $value) {
	// 				if($nexttime>$value['paytime']){
	// 					$nexttime=$value[paytime];
	// 				}
	// 			}
	// 			if($nexttime=='9999-12-31'){
	// 				$nexttime="未设置";
	// 			}
	// 			$finish=1;//修改订单状态
	// 			foreach ($list2 as $key => $value) {
	// 				if($value[status]==0){
	// 					$finish=0;
	// 					break;
	// 				}
	// 			}
	// 			if($finish==1){
	// 				$finish_data[finishtime]=time();
	// 				$finish_data[payperson]=$_SESSION[loginUserName];
	// 				$finish_res=M('plmorder2')->where(array('id'=>$info[orderid]))->save($finish_data);
	// 			}
	// 			$handle=$order['handle'];//修改操作记录
	// 			$handle=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".$info[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>付款</td>;";

	// 			$res2=M('plmorder2')->where(array('id'=>$info['orderid']))->save(array('nexttime'=>$nexttime,'finish'=>$finish,'handle'=>$handle));
	// 			if($finish==1){
	// 				$addinfo=M('plmorder2')->find($info[orderid]);
	// 				$data['city']=$addinfo['city'];
	// 				$data['supplier']=$addinfo['supplier'];
	// 				$data['plm']=$addinfo['plm'];
	// 				$data['status']=1;
	// 				$data['type']=1;
	// 				$data['approve']=1;
	// 				$data['ctime']=time();
	// 				$data['sqr']=$addinfo['user'];
	// 				$data['total']=$addinfo['price'];
	// 				$data['user']=$addinfo['payperson'];
	// 				$data['filename']=$addinfo['filename'];
	// 				$data['newname']=$addinfo['newname'];
	// 				$data['handlehistroy']=$addinfo['handle'];
	// 				$data['orderid']='gc'.$addinfo['id'];
	// 				$data['department']=$addinfo['department'];
	// 				$supplier=M('supplier')->where(array('supplier'=>$addinfo['supplier']))->find();
	// 				$data[bankaccount]=$supplier[bankname];
	// 				$data[banknumber]=$supplier[bankaccount];
	// 				$data[bankaddress]=$supplier[bankhu];
	// 				$data['classify']=2;
	// 				$data['type']=1;
	// 				M('plmmaterialtj')->add($data);
	// 			}
	// 		}
	// 	}
	// 	$this->success('付款成功.');
	// }

	//执行付款 
	public function gcPaySubmit2(){
		//dump($_POST);die;//付款值$_REQUEST['pay'];
		$model=M('plmorder2paytime');
		$id=$_REQUEST['id'];
		$info=$model->find($id);

		// dump($_REQUEST['pay']);dump($_REQUEST['id']);die;

		//$orderid = $model->where("id=".$_REQUEST['id'])->getField('orderid');
		// dump($orderid);
		$infos = $model->where('orderid='.$info['orderid'])->select();
		// dump($infos);die;
		$already_pay = 0;
		foreach ($infos as $key => $value) {
			$already_pay +=(float)$value['oldpay'];
		}
		//dump($already_pay);die;

		//$order=M('plmorder2')->find($info[orderid]);
		$order=M('plmorder2')->where('id='.$info['orderid'])->select();

		//$yh2 = M('plmorder2')->where("id=".$info[orderid])->getField('yh2');
		//$info['pay'] - $yh3得到应付金额
		$shouldpay = $order[0]['price'] - $order[0]['yh2'];//应付总额
		// dump($shouldpay);
		// dump($already_pay);
		// dump($_REQUEST['pay']);
		// die;

		//修改$info['oldpay']改为$already_pay
		if($already_pay+$_REQUEST['pay']>$shouldpay){
			// $this->error('付款金额已大于总金额,请重新输入.');
			echo 2;die();
		}
		if($info['oldpay']+$_REQUEST['pay']>$info['pay']){
			// $this->error('付款金额已大于总金额,请重新输入.');
			echo 2;die();
		}
		if($info['oldpay']+$_REQUEST['pay']<$info['pay']){
			$oldpay=$info['oldpay']+$_REQUEST['pay'];
			$res=$model->where(array('id'=>$id))->save(array('oldpay'=>$oldpay));
		}
		if($info['oldpay']+$_REQUEST['pay']==$info['pay']){
			$oldpay=$info['oldpay']+$_REQUEST['pay'];
			$res=$model->where(array('id'=>$id))->save(array('oldpay'=>$oldpay,'status'=>1));
			$list=$model->where(array('orderid'=>$info[orderid],'status'=>0,'paytime'=>array('neq','')))->select();
			$list2=$model->where(array('orderid'=>$info[orderid]))->select();
			$nexttime='9999-12-31';//修改下次付款时间
			foreach ($list as $key => $value) {
				if($nexttime>$value['paytime']){
					$nexttime=$value[paytime];
				}
			}
			if($nexttime=='9999-12-31'){
				$nexttime="未设置";
			}
			$finish=1;//修改订单状态
			foreach ($list2 as $key => $value) {
				if($value[status]==0){
					$finish=0;
					break;
				}
			}
			if($finish==1){
				$finish_data[finishtime]=time();
				$finish_data[payperson]=$_SESSION[loginUserName];
				$finish_res=M('plmorder2')->where(array('id'=>$info[orderid]))->save($finish_data);
			}
			$handle=$order['handle'];//修改操作记录
			$handle=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".$info[pay]."</td><td>".$_SESSION['loginUserName']."</td><td>付款</td>;";

			$res2=M('plmorder2')->where(array('id'=>$info['orderid']))->save(array('nexttime'=>$nexttime,'finish'=>$finish,'handle'=>$handle));
			if($finish==1){
				$addinfo=M('plmorder2')->find($info[orderid]);
				$data['city']=$addinfo['city'];
				$data['supplier']=$addinfo['supplier'];
				$data['plm']=$addinfo['plm'];
				$data['status']=1;
				$data['type']=1;
				$data['approve']=1;
				$data['ctime']=time();
				$data['sqr']=$addinfo['user'];
				$data['total']=$addinfo['price'];
				$data['user']=$addinfo['payperson'];
				$data['filename']=$addinfo['filename'];
				$data['newname']=$addinfo['newname'];
				$data['handlehistroy']=$addinfo['handle'];
				$data['orderid']='gc'.$addinfo['id'];
				$data['department']=$addinfo['department'];
				$supplier=M('supplier')->where(array('supplier'=>$addinfo['supplier']))->find();
				$data[bankaccount]=$supplier[bankname];
				$data[banknumber]=$supplier[bankaccount];
				$data[bankaddress]=$supplier[bankhu];
				$data['classify']=2;
				$data['type']=1;
				M('plmmaterialtj')->add($data);
			}
		}
		echo 1;
	}
	//工程上传附件
	public function gcUpload(){
		$id=$_REQUEST['id'];
		$this->assign('id',$id);
		$this->display();
	}
	//上传附件
	public function gcUploadSubmit(){
		$id=$_REQUEST[id];
		$map[id]=$id;
		foreach ($_FILES['file']['name'] as $key => $value) {
			$tj=M("plmorder2")->where($map)->field("id,newname,filename")->find();
			if(!empty($_FILES['file']['name'][$key]))
			{
				$savePath = '../Public/Uploads/';     //设置附件上传目录
				$ext = strtolower(end(explode(".",basename($_FILES['file']['name'][$key])))); 
				$uuid=uniqid(rand(), false).$key;
				$newname = filter_var($uuid.'.'.$ext, FILTER_CALLBACK, array("options"=>"convertSpace"));
				$upload_file = $savePath.$newname;	
				
				$filename=$_FILES['file']['name'][$key];
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				$res1=move_uploaded_file($_FILES['file']['tmp_name'][$key],$upload_file);
				if($res1){
					if(!empty($tj[newname])){
						$data[newname] = $tj[newname].','.$newname;
						$data[filename] = $tj[filename].','.$_FILES['file']['name'][$key];
					}else{
						$data[newname] = $newname;
						$data[filename] = $_FILES['file']['name'][$key];
					}					
					M("plmorder2")->where($map)->save($data);
				}
							
			}
		}
		$this->success('上传附件成功。');
	}
	//作废
	public function gcZuoFei(){
		$id=$_REQUEST[id];
		$info=M('plmorder2')->find($id);
		$handle=$info[handle];
		$handle=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".'0'."</td><td>".$_SESSION['loginUserName']."</td><td>作废</td>;";
		$res=M('plmorder2')->where(array('id'=>$id))->save(array('del'=>1,'handle'=>$handle,'finishtime'=>time()));//1  代表作废
		$res2=M('plmorder2paytime')->where(array('orderid'=>$id))->save(array('del'=>1));
		// dump($res2);die;
		if($res){
			$addinfo=M('plmorder2')->find($id);
			$data['city']=$addinfo['city'];
			$data['supplier']=$addinfo['supplier'];
			$data['plm']=$addinfo['plm'];
			$data['status']=20;
			$data['type']=1;
			$data['approve']=1;
			// $data['ctime']=time();
			$data['zf_time']=time();
			$data['sqr']=$addinfo['user'];
			$data['total']=$addinfo['price'];
			$data['user']=$addinfo['payperson'];
			$data['filename']=$addinfo['filename'];
			$data['newname']=$addinfo['newname'];
			$data['handlehistroy']=$addinfo['handle'];
			$data['orderid']='gc'.$addinfo['id'];
			$data['department']=$addinfo['department'];
			$supplier=M('supplier')->where(array('supplier'=>$addinfo['supplier']))->find();
			$data[bankaccount]=$supplier[bankname];
			$data[banknumber]=$supplier[bankaccount];
			$data[bankaddress]=$supplier[bankhu];
			
			$data['classify']=2;
			// $data['type']=1;
			M('plmmaterialtj')->add($data);
		}
		if($res){
			echo 1;
		}else{
			echo 2;
		}
	}
	//付保证金
	public function paybaozheng(){
		$id=$_REQUEST['message'];
		$info=M('plmorder2')->find($id);
		$handle=$info[handle];
		$handle=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".$info[baozheng]."</td><td>".$_SESSION['loginUserName']."</td><td>付保证金</td>;";
		$res=M('plmorder2')->where(array('id'=>$id))->save(array('pay_baozheng_status'=>1,'handle'=>$handle));
		//如果只有保证金  直接完成
		if(!(floatval($info['price'])>0)){
			$finish_data[finishtime]=time();
			$finish_data[payperson]=$_SESSION[loginUserName];
			$finish_data[finish]=1;
			$finish_res=M('plmorder2')->where(array('id'=>$id))->save($finish_data);
			//添加完成统计记录
			$addinfo=M('plmorder2')->find($id);
			$data['city']=$addinfo['city'];
			$data['supplier']=$addinfo['supplier'];
			$data['plm']=$addinfo['plm'];
			$data['status']=1;
			$data['type']=1;
			$data['approve']=1;
			$data['ctime']=time();
			$data['sqr']=$addinfo['user'];
			$data['total']=$addinfo['price'];
			$data['user']=$addinfo['payperson'];
			$data['filename']=$addinfo['filename'];
			$data['newname']=$addinfo['newname'];
			$data['handlehistroy']=$addinfo['handle'];
			$data['orderid']='gc'.$addinfo['id'];
			$data['department']=$addinfo['department'];
			$supplier=M('supplier')->where(array('supplier'=>$addinfo['supplier']))->find();
			$data[bankaccount]=$supplier[bankname];
			$data[banknumber]=$supplier[bankaccount];
			$data[bankaddress]=$supplier[bankhu];
			$data['classify']=2;
			$data['type']=1;
			M('plmmaterialtj')->add($data);
		}
		if($res){
			echo 1;
		}else{
			echo 2;
		}
	}
	//保证金退还
	public function tuihuan(){
		$id=$_REQUEST[id];
		$info=M('plmorder2')->find($id);
		$handle=$info[handle];
		$handle=$handle."<td>".date('Y-m-d H:i',time())."</td><td>".$info[baozheng]."</td><td>".$_SESSION['loginUserName']."</td><td>退还保证金</td>;";
		$res=M('plmorder2')->where(array('id'=>$id))->save(array('baozheng_status'=>1,'handle'=>$handle));
		if($res){
			echo 1;
		}else{
			echo 2;
		}


	}
	//查看
	public function see($id){
		if($_SESSION['dept']=="财务部"){
			$data[id]=$id;
			$data[see]=1;
			$res=M('plmmaterialtj')->save($data);
		}
	}
	//项目成本
	public function plminfo(){
		
		$plm=M("Project")->where("id=".$_REQUEST['plm'])->getField("title");
		//材料下单
		$total=0;//总计
		$pay=0;//已付总计
		$material=M('brand')->select();
		$material[]['name']='其他的';
		$names=array();
		foreach ($material as $k => $v) {
			$names[]=$v['name'];
		}
		foreach ($material as $key => $value) {
			$price=0;//总采购
			$payprice=0;//已付
			$map['department']=array('in',array('材料部','市场部'));
			$map['status']=array('neq',20);
			$map['plm']=$plm;
			$map['type']=1;
			$map['tuihuo']=1;
			$plmmaterialtj=M('plmmaterialtj')->where($map)->select();
			foreach ($plmmaterialtj as $key1 => $value1) {
				if($value1['status']==1){
					$clids=explode(',', trim($value1['clid'],','));
					if($value['name']=="其他的"){
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->select();
						foreach ($materials as $key2 => $value2) {
							$payprice+=$value2['price']*$value2['count'];
							$price+=$value2['price']*$value2['count'];
						}
					}else{
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->select();
						foreach ($materials as $key2 => $value2) {
							$payprice+=$value2['price']*$value2['count'];
							$price+=$value2['price']*$value2['count'];
						}
					}

				}else{
					$clids=explode(',', trim($value1['clid'],','));	
					if($value['name']=='其他的'){
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->select();
						foreach ($materials as $key2 => $value2) {
							$price+=$value2['price']*$value2['count'];
						}
					}else{
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->select();
						foreach ($materials as $key2 => $value2) {
							$price+=$value2['price']*$value2['count'];
						}
					}

				}
					
			}
			$material[$key][total]=number_format($price,2);
			$total+=$price;
			$material[$key][price]=number_format($payprice,2);
			$pay+=$payprice;
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
		
		//工程下单
		$classify=M('plmorder2paytime')->where(array('plm'=>$plm))->group('name')->field('name')->select();
		$allTotal=0;
		$allPay=0;
		foreach ($classify as $kk => $vv) {
			$gcprice=0;
			$gctotal=0;
			$classifys=M('plmorder2paytime')->where(array('approve'=>1,'plm'=>$plm,'name'=>$vv['name'],'del'=>0))->select();
			foreach ($classifys as $key3 => $value3) {
				$gcprice+=$value3['oldpay'];
				$gctotal+=$value3['pay'];
			}
			$classify[$kk]['price']=$gcprice;
			$classify[$kk]['total']=$gctotal;
			$allTotal+=$gctotal;
			$allPay+=$gcprice;
		}
		$allTotal=number_format($allTotal,2);
		$allPay=number_format($allPay,2);
		$this->assign('total',$total);
		$this->assign('pay',$pay);
		$this->assign('allTotal',$allTotal);
		$this->assign('allPay',$allPay);
		$this->assign('material',$material);
		$this->assign('classify',$classify);
		$this->assign('plmid',$_REQUEST['plm']);
		$this->display();
	}

	/**工程付款-->供应商返利**/
	public function yh3(){
		$map[id]=$_REQUEST[id];	
		self::see($_REQUEST[id]);
		$list=M("plmorder2")->where($map)->find();
		//$total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		//$total1=$list[total]+$list[yf]-$list[yhje];
		
		$list[total]=$list[price]-$list[yh2];
		$total1=$list[price];

		// $total=$list[total]+$list[yf]-$list[yhje]-$list[yh2];
		// $total1=$list[total]+$list[yf]-$list[yhje];

		$map2[orderid]=$_REQUEST[id];
		$map2[paytime]=array('neq','');
		// $map2[status]=1;
		$order_paytime2=M('plmorder2paytime')->where($map2)->select();
		$total_time2=0;
		foreach ($order_paytime2 as $key => $value) {
			$total_time2+=$value[oldpay];
		}

		$this->assign("list",$list);
        // $this->assign("supplier",$supplier);
        $this->assign("total",$total);
        $this->assign("total1",$total1);
        $this->assign('total_time2',$total_time2);
        //dump($total_time2);
        // $this->assign("str",$str);
		$this->display();
	}
	public function yh3submit(){
		// dump($_REQUEST['yh2']);die;
		$id=$_REQUEST[id];

		if ($_REQUEST[total1]<$_REQUEST[yh2]) {
			$this->error('返利金额不得大于总金额,请重新输入.');
		}

		$data['yh2'] = $_REQUEST['yh2'];
		$list=M("plmorder2")->where(array('id'=>$_REQUEST[id]))->save($data);
		$this->success('操作成功.');
	}

	/**
	 * 导出该页excel
	 */
	public function print_info(){

		$plm=M("Project")->where("id=".$_REQUEST['plm'])->getField("title");
		//材料下单
		$total=0;//总计
		$pay=0;//已付总计
		$material=M('brand')->select();
		$material[]['name']='其他的';
		$names=array();
		foreach ($material as $k => $v) {
			$names[]=$v['name'];
		}
		foreach ($material as $key => $value) {
			$price=0;//总采购
			$payprice=0;//已付
			$map['department']=array('in',array('材料部','市场部'));
			$map['status']=array('neq',20);
			$map['plm']=$plm;
			$map['type']=1;
			$map['tuihuo']=1;
			$plmmaterialtj=M('plmmaterialtj')->where($map)->select();
			foreach ($plmmaterialtj as $key1 => $value1) {
				if($value1['status']==1){
					$clids=explode(',', trim($value1['clid'],','));
					if($value['name']=="其他的"){
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->select();
						foreach ($materials as $key2 => $value2) {
							$payprice+=$value2['price']*$value2['count'];
							$price+=$value2['price']*$value2['count'];
						}
					}else{
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->select();
						foreach ($materials as $key2 => $value2) {
							$payprice+=$value2['price']*$value2['count'];
							$price+=$value2['price']*$value2['count'];
						}
					}

				}else{
					$clids=explode(',', trim($value1['clid'],','));	
					if($value['name']=='其他的'){
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>array('not in',$names)))->select();
						foreach ($materials as $key2 => $value2) {
							$price+=$value2['price']*$value2['count'];
						}
					}else{
						$materials=M('plmmaterials')->where(array('id'=>array('in',$clids),'brand'=>$value['name']))->select();
						foreach ($materials as $key2 => $value2) {
							$price+=$value2['price']*$value2['count'];
						}
					}

				}
					
			}
			$material[$key][total]=number_format($price,2);
			$total_all+=$price;
			$material[$key][price]=number_format($payprice,2);
			$pay+=$payprice;
		}
		$pay=number_format($pay,2);
		$total_all=number_format($total_all,2);
		
		
		//优惠总计
		$youhui1=M('plmmaterialtj')->where($map)->sum("yhje");
		$youhui2=M('plmmaterialtj')->where($map)->sum("yh2");
		$youhui=number_format($youhui1+$youhui2,2);

		//保证金总计
		
		$mapforplmorder2[plm]=$plm;
		$mapforplmorder2[pay_baozheng_status]=1;
		$baozhengyifu=M('plmorder2')->where($mapforplmorder2)->sum("baozheng");
		$mapforplmorder2[pay_baozheng_status]=0;
		$baozhengweifu=M('plmorder2')->where($mapforplmorder2)->sum("baozheng");
		$youhui3 = M('plmorder2')->where($mapforplmorder2)->sum("yh2");

		//工程下单
		$classify=M('plmorder2paytime')->where(array('plm'=>$plm))->group('name')->field('name')->select();
		$allTotal=0;
		$allPay=0;
		foreach ($classify as $kk => $vv) {
			$gcprice=0;
			$gctotal=0;
			$classifys=M('plmorder2paytime')->where(array('approve'=>1,'plm'=>$plm,'name'=>$vv['name'],'del'=>0))->select();
			foreach ($classifys as $key3 => $value3) {
				$gcprice+=$value3['oldpay'];
				$gctotal+=$value3['pay'];
			}
			$classify[$kk]['price']=$gcprice;
			$classify[$kk]['total']=$gctotal;
			$allTotal+=$gctotal;
			$allPay+=$gcprice;
		}
		$allTotal=number_format($allTotal,2);
		$allPay=number_format($allPay,2);

		// dump($material);
		// die;

		$m_length = count($material);
		$e_length = count($classify);
		//dump($classify);

		$all_length = $m_length + $e_length;

		for($i=0;$i<=$m_length;$i++){
			$data[$i]['name'] = $material[$i]['name'];
			$data[$i]['total'] = $material[$i]['total'];
			$data[$i]['price'] = $material[$i]['price'];
		}

		$data[$m_length]['name'] = '';
		$data[$m_length]['total'] = '';
		$data[$m_length]['price'] = '';
		$data[$m_length]['temp'] = '';
		$data[$m_length]['allTotal'] = '总款项';
		$data[$m_length]['allPay'] = '已付款';
		$data[$m_length]['youhui3'] = '保证金已付';
		$data[$m_length]['v8'] = '保证金未付';
		$data[$m_length]['v9'] = '优惠金额';

		for($z=0;$z<$e_length;$z++){
			$data[$m_length+$z+1]['name'] = $classify[$z]['name'];
			$data[$m_length+$z+1]['total'] = $classify[$z]['total'];
			$data[$m_length+$z+1]['price'] = $classify[$z]['price'];
		}
		$data[$m_length+1][temp] = " ";

		//工程下单类别
		$data[$m_length+1]['allTotal'] = $allTotal;
		$data[$m_length+1]['allPay'] = $allPay;
		$data[$m_length+1]['baozhengyifu'] = $baozhengyifu;
		$data[$m_length+1]['baozhengweifu'] = $baozhengweifu;
		$data[$m_length+1]['youhui3'] = $youhui3;


		$data[0][temp] = " ";
		$data[0]['total_all'] = $total_all;
		$data[0]['pay'] = $pay;
		$data[0]['youhui'] = $youhui;


		$file=$plm."项目成本统计表";
		$title=$plm."项目成本统计表";
		$subtitle='';
		$excelname=$plm."项目成本统计表";
		$th_array=array('类别','总采购','已付','','总采购','已付款','优惠');
		$this->createExel($file,$title,$subtitle,$th_array,$data,$excelname);

	}
	
}
?>