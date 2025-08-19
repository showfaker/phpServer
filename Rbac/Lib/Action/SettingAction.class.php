<?php
class SettingAction extends CommonAction {
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = "Filesetting";
		$model = D($name);
		$info=M("Filesetting")->find();
		
		$this->assign('info', $info);
		
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
        $name = "Filesetting";
        $model = D($name);
		$model->id=1;
		$savePath = '../Public/template/';
		if(!empty($_FILES['file1']['name']))
		{
			$file=$_FILES['file1']['name'];
			$file_tmp=$_FILES['file1']['tmp_name'];
			
			$filename=$_FILES['file1']['name'];
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
			move_uploaded_file($file_tmp,$upload_file);
		
			$model->file1=$newname;
			$model->file1name=$filename;
		}
		if(!empty($_FILES['file2']['name']))
		{
			$file=$_FILES['file2']['name'];
			$file_tmp=$_FILES['file2']['tmp_name'];
			
			$filename=$_FILES['file2']['name'];
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
			move_uploaded_file($file_tmp,$upload_file);
		
			$model->file2=$newname;
			$model->file2name=$filename;
		}
		if(!empty($_FILES['file3']['name']))
		{
			$file=$_FILES['file3']['name'];
			$file_tmp=$_FILES['file3']['tmp_name'];
			
			$filename=$_FILES['file3']['name'];
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
			move_uploaded_file($file_tmp,$upload_file);
		
			$model->file3=$newname;
			$model->file3name=$filename;
		}
		
		
		
        $list = $model->save();
        $this->success('设置成功!');
    }
   
}
?>