<?php
class SecondgroupAction extends CommonAction {
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	function _filter(&$map){
		$map['name'] = array('like',"%".$_POST['name']."%");
	}
    
    public function index() {
    	//列表过滤器，生成查询Map对象
    	$map = $this->_search();
    	if (method_exists($this, '_filter')) {
    		$this->_filter($map);
    	}

    	//$map['status']  = 1;	 
    	$name = $this->getActionName();
    	$model = D($name);
    	
    	$Dept = D ( "Company" );
    	$list = $Dept->getField ('id,name');
    	foreach ($list as $key=>$val)
    	{
    		$list1[$key]=$val;
    	}
    	$this->assign('list1', $list1);
    	
    	
    	if (!empty($model)) {
    		$this->_list($model, $map);
    	}
    	/*zcy*/
    	if($_SESSION[app]!=1)
    	{
    		$this->display(indexoa);
    	}
    	else
    	{
    		$this->display(indexoaapp);
    	}
    	return;
    }

    protected function _list($model, $map, $sortBy = '', $asc = true) {
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

    		$this->assign("totalCount", $p->totalRows);
    		$this->assign("numPerPage", $p->listRows);
    		$this->assign("currentPage", $p->nowPage);
    		$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
    		$this->assign('volist', $voList);
    		//echo $model->getlastsql();
    		//分页跳转的时候保证查询条件
    		foreach ($map as $key => $val) {
    			if (!is_array($val)) {
    				$p->parameter .= "$key=" . urlencode($val) . "&";
    			}
    		}
    		foreach ($voList as $key1 => $val1) {
    			$vofortree.=$val1[id].')(*'.$val1[parentdept].'&^%'.$val1[name].'(职责：'.$val1[duty].'|主管：'.$val1[director].')'.'$#@';
    		}
    		$number=0;
    		foreach ($voList as $key2 => $val2) {
    			if($val2[parentdept]=="")
    			{
    				$tree[$number]=$val2;
    				$number++;
    				foreach ($voList as $key3 => $val3) {
    					if($val3[parentdept]==$val2[id])
    					{
    						$tree[$number]=$val3;
    						$number++;
    						foreach ($voList as $key4 => $val4) {
    							if($val4[parentdept]==$val3[id])
    							{
    								$tree[$number]=$val4;
    								$number++;
    								foreach ($voList as $key5 => $val5) {
    									if($val5[parentdept]==$val4[id])
    									{
    										$tree[$number]=$val5;
    										$number++;
    										foreach ($voList as $key6 => $val6) {
    											if($val6[parentdept]==$val5[id])
    											{
    												$tree[$number]=$val6;
    												$number++;
    											}
    										}
    									}
    								}
    							}
    						}	
    					}
    				}
    				
    				
    			}
    			
    		}
    		//dump($tree);
    		//分页显示
    		$page = $p->show();
    		//列表排序显示
    		$sortImg = $sort; //排序图标
    		$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
    		$sort = $sort == 'desc' ? 1 : 0; //排序方式
    		//模板赋值显示
    		$this->assign('listfortree', $vofortree);
    		$this->assign('list', $tree);
    		$this->assign('sort', $sort);
    		$this->assign('order', $order);
    		$this->assign('sortImg', $sortImg);
    		$this->assign('sortType', $sortAlt);
    		$this->assign("page", $page);
    	}
    	Cookie::set('_currentUrl_', __SELF__);
    
    	return;
    }
    
   
    public function add() {
    	$namedept='Secondgroup';
    	$modeldept = M($namedept);
    	$map[status]=1;
    	$vodept=$modeldept->where($map)->field('id,name')->select();
    	$this->assign('vodept', $vodept);
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(addoa);
    	}
    	else
    	{
    		$this->display();
    	}
    }
    function edit() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	 
    	$namedept='Company';
    	$modeldept = M($namedept);
    	$vodept=$modeldept->field('id,name')->select();
    	$this->assign('vodept', $vodept);
    	 
    	 
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
    	$name = $this->getActionName();
    	$model = D($name);
    	if (!empty($model)) {
    		$pk = $model->getPk();
    		$id = $_REQUEST [$pk];
    		if (isset($id)) {
    			$condition = array($pk => array('in', explode(',', $id)));
    			if (false !== $model->where($condition)->delete())
    			{
    				//echo $model->getlastsql();
    				
    				$map[parentdept]=$condition[id];
    				$model->where($map)->delete();
    				$this->redirect('index');
    				//$this->success('部门及下属部门删除成功！');
    			} else {
    				$this->error('删除失败！');
    			}
    		} else {
    			$this->error('非法操作');
    		}
    	}
    	$this->forward();
    }
	
	function ajax(){
		M("Secondgroup")->where("id='".htmlspecialchars($_REQUEST[id])."'")->delete();
		echo json_encode(htmlspecialchars($_REQUEST[id]));
	}
    


    
}
?>