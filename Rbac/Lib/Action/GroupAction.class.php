<?php
class GroupAction extends CommonAction {
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
		//$map['id'] = array('egt',2);
		$map['title'] = array('like',"%".$_POST['name']."%");
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		//$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = $this->getActionName();
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,"sort",true);
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
	
    public function sort()
    {
		$node = M('Group');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->where('status=1')->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        if($_SESSION[skin]!=3)
        {
        	$this->display(sortoa);
        }
        else
        {
        $this->display();
        }
        return ;
    }

}
?>