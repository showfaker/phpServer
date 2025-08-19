<?php
class SecondwarningAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		
		if($_REQUEST['title'])
		{
			$map['address'] = array('like',"%".$_REQUEST['title']."%");
			$this->assign("title",$_REQUEST['title']);
		}
		if($_REQUEST['number'])
		{
			$map['number'] = array('like',"%".$_REQUEST['number']."%");
			$this->assign("number",$_REQUEST['number']);
		}
		if(empty($_REQUEST['classify']))
		{
			$_REQUEST['classify']="开发";
		}
		if($_REQUEST['classify'])
		{
			$map['classify'] = array('like',"%".$_REQUEST['classify']."%");
			$this->assign("classify",$_REQUEST['classify']);
		}
	}

	
	
	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		
		
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
	
		if($_SESSION[account]!="admin")
		{
			$map1['_complex'] = $this->find5level($_SESSION[position],$map1);
			$projects=M("Project")->where($map1)->field("id")->select();
			foreach($projects as $key => $val)
			{
				$plmids.= $val["id"].",";
			}
			$map['plmid'] = array("in",$plmids);
		}
		
		$map['status'] = array("eq",1);
		$map['warning'] = array("eq",1);//prewarning|
		$name = "Plmwarning";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'id',false);
		}
		
		
		
		if($_SESSION["app"])
		{
			$this->display("indexapp");
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
				else
				{
					$p->parameter .= "$key=" . $_REQUEST[$key] . "&";
				}
            }
			if($_REQUEST["flag1"])
			{
				$p->parameter .= "flag1=" . $_REQUEST["flag1"] . "&";
			}
			if($_REQUEST["flag2"])
			{
				$p->parameter .= "flag2=" . $_REQUEST["flag2"] . "&";
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
		Cookie::set('_currentUrl_', __SELF__.$p->parameter);
        return;
    }
	public function foreverdelete() {
        //删除指定记录
        $name = "plmwarningapprove";
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