<?php
class CominfoAction extends CommonAction {
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = $this->getActionName();
		$model = D($name);
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
	//过滤查询字段	
    function insert() {
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $model->id=1;
        //保存当前数据对象
        $list = $model->where("id=1")->delete();
        $list = $model->where("id=1")->add();
        if ($list !== false) { //保存成功
            /////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('设置成功!');
        } else {
            //失败提示
            $this->error('设置失败!');
        }
    }
    
    function setlog() {
    	$annex=$this->upload();
    	/*if ($annex == "") {
    		/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		$this->success('设置成功!');
    	} else {
    		$this->error('设置失败!');
    	}*/
    	 
    }
    public function upload() {
    	if (NULL!=$_FILES["image"]["name"]) {
    		//如果有文件上传 上传附件
    		return $this->_upload();
    	}
    	else
    	{
    		$this->error('请选取图片!');
    	}
    }
    Public function _upload(){
    	import("ORG.Net.UploadFile");
    	$upload = new UploadFile();// 实例化上传类
    	$upload->maxSize  = 20*1024*1024 ;// 设置附件上传大小
    	$upload->allowExts  = explode(',','gif,png');
    	$upload->savePath =  '../Public/Images/';// 设置附件上传目录
    	//$pathinfo = pathinfo("logo.png");
    	$upload->saveRule = "logo";
    	$upload->uploadReplace = true;
    	//$upload->thumbRemoveOrigin = true;
    	/*if(strlen($_FILES["image"]["name"])>30)
    	{
    		$this->error('上传的文件名称过长，请修改!');
    	}*/
    	if(!$upload->upload()) {// 上传错误提示错误信息
    		$this->error($upload->getErrorMsg());
    	}else{// 上传成功 获取上传文件信息
    		$info =  $upload->getUploadFileInfo();
    	}
    	//return $info[0][savename];
    	$this->success('设置成功!请刷新浏览器。');
    	
    }
    public function defaultlogo() {
    	copy('../Public/Images/logodefault.png','../Public/Images/logo.png');
    	$this->success('还原成功!请刷新浏览器。');
    }
   
}
?>