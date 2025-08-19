<?php
class LogformAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['nickname'] = array('like',"%".$_POST['name']."%");
		$this->assign('name',$_POST['name']);
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
		$map['type'] = array('in',"1");
		$name = "User";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'number',true);
		}
		$namesetting = "Setting";
		$modelsetting = D($namesetting);
		$data=$modelsetting->where('id=1')->select();
		$this->assign('amount',$data[0]);
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
            $p = new Page($count, $listRows);
            //分页查询数据

            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            foreach ($voList as $vokey => $voval) {
				$mapfornumber["number"]=$voval[number];
            	$voList[$vokey][groupip]=M("Log")->where($mapfornumber)->group("ip")->select();
				foreach ($voList[$vokey][groupip] as $vokey1 => $voval1) {
					$mapforLog[number]=$voval[number];
					$mapforLog[ip]=$voval1[ip];
					$voList[$vokey][groupip][$vokey1]["count"]=M("Log")->where($mapforLog)->count();
					if($voList[$vokey][groupip][$vokey1]["count"]==0)
					{
						dump($mapforLog);
					}
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
	function setamount() {
		$name = "Setting";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		$list = $model->where('id=1')->save();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('设置成功!');
		} else {
			//失败提示
			$this->error('设置失败!');
		}
	}
	
	function insert() {
		//B('FilterString');
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->user_id=$_SESSION['loginUserName'];
		//保存当前数据对象
		
		$list = $model->add();
		if ($list !== false) { //保存成功
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	function update() {
		//B('FilterString');
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		// 更新数据
		$model->create_time=time();
		$model->user_id=$_SESSION['loginUserName'];
		
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('编辑成功!');
		} else {
			//错误提示
			$this->error('编辑失败!');
		}
	}
	/*
	function news() {
		$name = $this->getActionName();
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		if (!empty($model)) {
			$this->_list($model, null,'create_time',false);
		}
		$this->assign('vo', $vo);
		if($_SESSION[skin]!=3)
		{
			$this->display(newsoa);
		}
		else
		{
			$this->display();
		}
	}*/
	
	function news() {
		$name = "Publicity";
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$map[status]=1;
		if (!empty($model)) {
			$this->_list($model, null,'ctime',false);
		}
		$this->assign('vo', $vo);
		if($_SESSION[skin]!=3)
		{
			$this->display(newsoa);
		}
		else
		{
			$this->display();
		}
	}
}
?>