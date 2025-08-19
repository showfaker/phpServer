<?php
class ModulesettingAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['title'] = array('like',"%".$_POST['name']."%");
	}

	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = "Group";
		$model = D($name);
		$map['status']=1;
		$map['more']=1;
		$list=$model->where($map)->order("sort asc")->select();
		$this->assign('list',$list);
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

	
	function insert() {
		//B('FilterString');
		$name = "Group";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->status=1;
		$model->more=1;
		$model->fa="fa-cube";
		$list = $model->add();
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function edit() 
	{
        $name = "Group";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3)
        {
        	$this->display(editoa);
        }
        else
        {
			$this->display();
        }
    }
	
	function update() {
		$name = "Group";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		// 更新数据
		/*
		$filename=$_FILES['file']['name'];
		if($filename!=null)
		{
			$savePath = '../Public/news/';     //设置附件上传目录
			$ext = strtolower(end(explode(".",basename($filename)))); 
			$uuid=uniqid(rand(), false);
			$newname = $uuid.'.'.$ext;
			$upload_file = $savePath.$newname;	
			move_uploaded_file($_FILES['file']['tmp_name'],$upload_file);
			$model->file = $newname;
			$model->filerealname = $filename;
		}
		*/
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('编辑成功!');
		} else {
			//错误提示
			$this->error('编辑失败!');
		}
	}
	
	 public function foreverdelete() {
        //删除指定记录
        $name = "Group";
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
			
			
			
			$mapforsnode[pid]=$id;
			$nodecount=M("Node")->where($mapforsnode)->count();
			if($nodecount)
			{
				$this->error('请至模块参数配置下删除该类型下的流程模块，再进行类型删除！');
			}
			
			
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete())
				{
                    //echo $model->getlastsql();
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