<?php
class ScheduleAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['content'] = array('like',"%".$_POST['content']."%");
		$this->assign('content', $_POST['content']);
		$map['user'] = array('like',"%".$_POST['user']."%");
		$this->assign('user', $_POST['user']);
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
		$name = "Schedule";
		$model = D($name);
		if($_SESSION["account"]!="admin")
		{
			$map[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
		}
		
		if (!empty($model)) {
			$this->_list($model, $map);
		}
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
			if (!empty($_REQUEST ['all'])) {
				$listRows=200;
			}
            $p = new Page($count, $listRows);
            //分页查询数据

            $voList = $model->where($map)->order("status desc,id desc")->limit($p->firstRow . ',' . $p->listRows)->select();
			
			
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            foreach ($voList as $vokey => $voval) {
            	$voList[$vokey][urltitle]=urlencode($voList[$vokey][title]);
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
	
	function insert() {
		//B('FilterString');
		$name = "Schedule";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		$model->href="index.php?s=Schedule/index";
		$model->user=$_SESSION['loginUserName'].$_SESSION['number'];
		$list = $model->add();
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function update() {
		//B('FilterString');
		$name = "Schedule";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		// 更新数据
		$model->create_time=time();
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
		$name = "Schedule";
		$model = D($name);
		if (!empty($model)) {
			$pk = $model->getPk();
			if(!empty($_REQUEST [$pk]))
			{
				$id = $_REQUEST [$pk];
			}
			else
			{
				$id = $_REQUEST ["ids"];
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
	}

}
?>