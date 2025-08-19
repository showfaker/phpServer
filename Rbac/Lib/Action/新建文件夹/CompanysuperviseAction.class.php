<?php
class CompanysuperviseAction extends CommonAction {
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
    	
    	$Dept = D ($name);
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
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(indexoa);
    	}
    	else
    	{
    		$this->display();
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
			
			foreach ($voList as $key => $val) {
    			$voList[$key]['file1array']=explode(',',$voList[$key]['file1']);
				$voList[$key]['file1namearray']=explode(',',$voList[$key]['file1name']);
				
				$voList[$key]['file2array']=explode(',',$voList[$key]['file2']);
				$voList[$key]['file2namearray']=explode(',',$voList[$key]['file2name']);
    		}
			
    		$this->assign('volist', $voList);
    		//echo $model->getlastsql();
    		//分页跳转的时候保证查询条件
    		foreach ($map as $key => $val) {
    			if (!is_array($val)) {
    				$p->parameter .= "$key=" . urlencode($val) . "&";
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
    	$namedept = $this->getActionName();
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
	
	 function insert() 
	{
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
		
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->file1=$newnameall;
			$model->file1name=$filenameall;
		}
		if(!empty($_FILES['file2']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file2']['name'];
			$file_tmp=$_FILES['file2']['tmp_name'];
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->file2=$newnameall;
			$model->file2name=$filenameall;
		}

        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }

	
    function edit() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	$this->assign('vo', $vo);
    	 
    	$namedept = $this->getActionName();
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
	
	
	
    function detail() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
		
		$vo['file1array']=explode(',',$vo['file1']);
		$vo['file1namearray']=explode(',',$vo['file1name']);
		
		$vo['file2array']=explode(',',$vo['file2']);
		$vo['file2namearray']=explode(',',$vo['file2name']);
				
    	$this->assign('vo', $vo);
    	 
    	$namedept = $this->getActionName();
    	$modeldept = M($namedept);
    	$vodept=$modeldept->field('id,name')->select();
    	$this->assign('vodept', $vodept);
    	 
		 
		 
    	 
    	$this->display();
    }
	
	function update() 
	{
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
		
		$savePath = '../Public/Uploads/';
		if(!empty($_FILES['file1']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->file1=$newnameall;
			$model->file1name=$filenameall;
		}
		if(!empty($_FILES['file2']['name'][0]))
		{
			$newnameall="";
			$filenameall="";
			$file=$_FILES['file2']['name'];
			$file_tmp=$_FILES['file2']['tmp_name'];
			foreach($file as $key=>$val)
			{
				if(!empty($val))
				{
					$filename=$val;
					$ext = strtolower(end(explode(".",basename($filename)))); 
					$uuid=uniqid(rand(), false);
					$newname = $uuid.'.'.$ext;
					$upload_file = $savePath.$newname;
					
					if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
					{
						$this->error("文件名不能含有特殊字符！");
					}
					if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
					{
						$this->error("非法文件类型！");
					}
					move_uploaded_file($file_tmp[$key],$upload_file);
					$newnameall.=$newname.',';
					$filenameall.=$filename.',';
				}
			}
			$model->file2=$newnameall;
			$model->file2name=$filenameall;
		}
		
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->redirect('index');
        } else {
            //错误提示
            $this->error('编辑失败!');
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
		$namedept = $this->getActionName();
		M($namedept)->where("id='".htmlspecialchars($_REQUEST[id])."'")->delete();
		echo json_encode(htmlspecialchars($_REQUEST[id]));
	}
    


    
}
?>